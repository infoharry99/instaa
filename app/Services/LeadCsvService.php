<?php

namespace App\Services;

use App\Models\BusinessLead;
use App\Models\Company;
use App\Models\ImportJob;
use App\Models\LeadContact;
use App\Models\LeadSource;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LeadCsvService
{
    /**
     * Process an uploaded CSV file and import leads.
     */
    public function importCsv(Company $company, int $userId, UploadedFile $file, array $columnMapping = []): ImportJob
    {
        $path = $file->store('imports', 'local');
        $fileName = $file->getClientOriginalName();

        $job = ImportJob::create([
            'company_id' => $company->id,
            'user_id' => $userId,
            'file_name' => $fileName,
            'file_path' => $path,
            'status' => 'processing',
            'mapping_config' => $columnMapping,
        ]);

        $filePath = Storage::disk('local')->path($path);
        $fileHandle = fopen($filePath, 'r');

        if (!$fileHandle) {
            $job->update([
                'status' => 'failed',
                'error_log' => ['Could not read uploaded CSV file.'],
            ]);
            return $job;
        }

        $header = fgetcsv($fileHandle);
        if (!$header) {
            fclose($fileHandle);
            $job->update([
                'status' => 'failed',
                'error_log' => ['CSV header row is empty.'],
            ]);
            return $job;
        }

        // Clean header BOM and spaces
        $header = array_map(function ($col) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $col));
        }, $header);

        $defaultSource = LeadSource::where('slug', 'manual-import')->first() ?? LeadSource::first();

        $totalRows = 0;
        $successRows = 0;
        $failedRows = 0;
        $errors = [];

        while (($row = fgetcsv($fileHandle)) !== false) {
            $totalRows++;
            $data = [];

            foreach ($header as $index => $colName) {
                $val = trim($row[$index] ?? '');
                // Check mapped column or direct name
                $targetKey = $columnMapping[$colName] ?? strtolower(str_replace(' ', '_', $colName));
                $data[$targetKey] = $val;
            }

            $businessName = $data['business_name'] ?? $data['name'] ?? null;
            if (empty($businessName)) {
                $failedRows++;
                $errors[] = "Row {$totalRows}: Business Name is missing.";
                continue;
            }

            $email = !empty($data['public_email']) ? $data['public_email'] : ($data['email'] ?? null);
            $phone = !empty($data['public_phone']) ? $data['public_phone'] : ($data['phone'] ?? null);
            $igUser = !empty($data['instagram_username']) ? ltrim($data['instagram_username'], '@') : null;

            // Check duplicate
            $duplicate = BusinessLead::where('company_id', $company->id)
                ->where(function ($q) use ($igUser, $email, $phone) {
                    if ($igUser) $q->orWhere('instagram_username', $igUser);
                    if ($email) $q->orWhere('public_email', $email);
                    if ($phone) $q->orWhere('public_phone', $phone);
                })
                ->exists();

            if ($duplicate) {
                $failedRows++;
                $errors[] = "Row {$totalRows}: Duplicate detected for '{$businessName}' ({$igUser} / {$email}). Skipped.";
                continue;
            }

            // Quota check
            if ($company->leads_used_count >= $company->max_leads_quota) {
                $failedRows++;
                $errors[] = "Row {$totalRows}: Quota limit reached ({$company->max_leads_quota} max leads).";
                break;
            }

            try {
                $lead = BusinessLead::create([
                    'company_id' => $company->id,
                    'lead_source_id' => $defaultSource?->id,
                    'business_name' => $businessName,
                    'instagram_username' => $igUser,
                    'instagram_profile_url' => $data['instagram_profile_url'] ?? ($igUser ? "https://instagram.com/{$igUser}" : null),
                    'profile_picture_url' => $data['profile_picture_url'] ?? null,
                    'business_description' => $data['business_description'] ?? ($data['description'] ?? null),
                    'category' => $data['category'] ?? 'Other business categories',
                    'city' => $data['city'] ?? null,
                    'state' => $data['state'] ?? null,
                    'country' => $data['country'] ?? 'India',
                    'location_address' => $data['location_address'] ?? ($data['address'] ?? null),
                    'official_website' => $data['official_website'] ?? ($data['website'] ?? null),
                    'public_email' => $email,
                    'public_phone' => $phone,
                    'contact_person' => $data['contact_person'] ?? null,
                    'status' => 'new',
                    'priority' => in_array(strtolower($data['priority'] ?? ''), ['low', 'medium', 'high']) ? strtolower($data['priority']) : 'medium',
                    'source_type' => 'csv_import',
                    'collected_at' => Carbon::now(),
                    'last_verified_at' => Carbon::now(),
                ]);

                if (!empty($lead->contact_person)) {
                    LeadContact::create([
                        'business_lead_id' => $lead->id,
                        'name' => $lead->contact_person,
                        'email' => $email,
                        'phone' => $phone,
                        'is_primary' => true,
                    ]);
                }

                $company->increment('leads_used_count');
                $successRows++;
            } catch (Exception $e) {
                $failedRows++;
                $errors[] = "Row {$totalRows}: " . $e->getMessage();
            }
        }

        fclose($fileHandle);

        $job->update([
            'total_rows' => $totalRows,
            'processed_rows' => $totalRows,
            'successful_rows' => $successRows,
            'failed_rows' => $failedRows,
            'status' => $failedRows > 0 && $successRows === 0 ? 'failed' : 'completed',
            'error_log' => $errors,
            'completed_at' => Carbon::now(),
        ]);

        AuditLogService::log(
            action: 'lead.csv_imported',
            entityType: 'ImportJob',
            entityId: $job->id,
            description: "Imported {$successRows} leads from {$fileName} ({$failedRows} failed/duplicate)."
        );

        return $job;
    }

    /**
     * Generate CSV export string for filtered leads.
     */
    public function exportCsv(Company $company, array $filters = []): string
    {
        $discoveryService = new LeadDiscoveryService();
        $leads = $discoveryService->searchCompanyLeads($company, $filters, 5000);

        $output = fopen('php://temp', 'r+');

        // CSV Header
        fputcsv($output, [
            'ID',
            'Business Name',
            'Instagram Username',
            'Instagram Profile URL',
            'Category',
            'City',
            'State',
            'Country',
            'Address',
            'Official Website',
            'Public Email',
            'Public Phone',
            'Contact Person',
            'Status',
            'Priority',
            'Source',
            'Created Date',
        ]);

        foreach ($leads as $lead) {
            fputcsv($output, [
                $lead->id,
                $lead->business_name,
                $lead->instagram_username ? "@{$lead->instagram_username}" : 'Not available',
                $lead->instagram_profile_url ?? 'Not available',
                $lead->category,
                $lead->city ?? 'Not available',
                $lead->state ?? 'Not available',
                $lead->country ?? 'Not available',
                $lead->location_address ?? 'Not available',
                $lead->official_website ?? 'Not available',
                $lead->public_email ?? 'Not available',
                $lead->public_phone ?? 'Not available',
                $lead->contact_person ?? 'Not available',
                ucfirst($lead->status),
                ucfirst($lead->priority),
                $lead->leadSource?->name ?? $lead->source_type,
                $lead->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        AuditLogService::log(
            action: 'lead.csv_exported',
            description: "Exported " . count($leads) . " leads to CSV."
        );

        return $csvContent;
    }
}

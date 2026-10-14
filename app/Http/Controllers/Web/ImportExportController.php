<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ImportJob;
use App\Services\LeadCsvService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportExportController extends Controller
{
    protected LeadCsvService $csvService;

    public function __construct(LeadCsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        $jobs = ImportJob::where('company_id', $company->id)->latest()->paginate(10);

        return view('import_export.index', compact('jobs', 'company'));
    }

    public function import(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // 10MB
        ]);

        try {
            $job = $this->csvService->importCsv($company, $user->id, $request->file('csv_file'));

            return redirect()->route('import-export.index')
                ->with('success', "Import processed: {$job->successful_rows} leads imported successfully ({$job->failed_rows} skipped/failed).");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        $filters = $request->only(['category', 'city', 'status', 'priority', 'has_email', 'has_phone']);
        $csvContent = $this->csvService->exportCsv($company, $filters);

        $fileName = 'leads_export_' . date('Y-m-d_His') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
    }

    public function downloadSample()
    {
        $sampleData = [
            ['business_name', 'instagram_username', 'category', 'city', 'state', 'official_website', 'public_email', 'public_phone', 'contact_person', 'priority'],
            ['Artisan Roast Cafe', 'artisanroastcafe', 'Restaurants and cafes', 'Mumbai', 'Maharashtra', 'https://artisanroast.com', 'contact@artisanroast.com', '+91 98200 99999', 'Vikram Patel', 'high'],
            ['Glow Aesthetics Studio', 'glowaesthetics', 'Beauty salons and makeup artists', 'Delhi', 'Delhi', 'https://glowaesthetics.in', 'info@glowaesthetics.in', '+91 11 4500 8888', 'Neha Sen', 'medium'],
            ['Iron Core Crossfit', 'ironcoregym', 'Gyms and fitness centers', 'Bengaluru', 'Karnataka', 'https://ironcore.fit', 'team@ironcore.fit', '+91 80 4123 4567', 'Rajesh Nair', 'high'],
        ];

        $output = fopen('php://temp', 'r+');
        foreach ($sampleData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="instalead_sample_import.csv"');
    }
}

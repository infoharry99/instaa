# InstaLead CRM - Instagram Business Lead Finder & CRM Suite

A modern SaaS web application built with **Laravel 12, PHP 8.2+, Tailwind CSS 4, Alpine.js, and MySQL**. It enables sales teams and agencies to discover business leads from permitted and authorized sources, manage leads in a high-velocity CRM pipeline, conduct multi-channel outreach (Instagram, WhatsApp, Email, Phone), and track conversion analytics.

---

## 🛠️ Tech Stack & Architecture

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Pure Laravel Blade + Tailwind CSS 4 + Alpine.js (zero React overhead)
- **Database:** MySQL (`instagram_crm`)
- **Authentication:** Laravel Session Auth (Web) + Laravel Sanctum (REST API)
- **APIs:** Official Meta Graph API (v21.0) & authorized business data directories
- **Compliance:** Enforces GDPR/DPDP public data boundaries, rate-limit safety, and duplicate protection.

---

## 🚀 Quick Start Guide

### 1. Database Configuration
The local MySQL database server is running and configured with:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=instagram_crm
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Run Database Migrations & Seeds
To reset or re-seed the database:
```bash
php artisan migrate --seed
```

### 3. Build Frontend Assets
```bash
npm run build
# Or start Vite dev server:
npm run dev
```

### 4. Start the Application Server
```bash
php artisan serve
```
Open your browser at: **[http://localhost:8000](http://localhost:8000)**

---

## 👥 Seeded Demo Accounts

All seeded accounts use password: `password`

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@instalead.com` | `password` |
| **Manager** | `manager@instalead.com` | `password` |
| **Sales Agent** | `agent@instalead.com` | `password` |

*(The login page includes a 1-click Quick Demo Switcher to test any role instantly)*

---

## 📦 Key Modules & Features

### 1. Business Lead Discovery (`/discovery`)
- Discovery by categories:
  - Restaurants and cafes
  - Beauty salons and makeup artists
  - Gyms and fitness centers
  - Real estate agents
  - Hotels and travel agencies
  - Doctors and clinics
  - Ecommerce businesses
  - Digital marketing agencies
  - Local shops
- Filters by keyword, city, category, email availability, phone availability, and website.
- 1-click save to CRM with duplicate detection and monthly lead quota checking.

### 2. Lead Management CRM (`/leads`)
- Dedicated Lead Profile page showing verified public data:
  - Business name, official Instagram handle, profile picture, description
  - Category, location address, city, official website
  - Public business email, public phone, contact person
  - Provenance data: data source, collected date, verification date
  - "Not available" fallback for missing fields (zero fabricated data)
- Status pipeline: `New`, `Contacted`, `Interested`, `Follow-up`, `Converted`, `Not Interested`
- Priority tagging: `Low`, `Medium`, `High`
- Color-coded tags (e.g. *Restaurant*, *Premium*, *Mumbai*, *Hot Lead*)
- Assignment to team members (Admin, Manager, Agent)
- Sticky bulk action toolbar (bulk delete, bulk status change)

### 3. Communication Center (`/communication`)
- Multi-channel outreach logging (Email, Phone, WhatsApp, Instagram DM, Meta API)
- WhatsApp 1-click click-to-chat with pre-filled message templates
- Click-to-call and mailto quick links
- Open official Instagram profile and website in 1 click
- Outreach template manager with dynamic variables (`{{business_name}}`, `{{contact_person}}`, `{{city}}`)
- Automatic lead status progression upon outreach

### 4. Custom Lists & Campaigns (`/lists`)
- Segment leads into campaigns (e.g., *Mumbai Cafes Q4*, *Luxury Salons*)
- Add/remove leads from lists with dedicated campaign views.

### 5. Follow-ups & Reminders (`/followups`)
- Schedule follow-up tasks with due dates, priority, and notes
- Filter by `Pending`, `Completed`, and `Overdue` tasks.

### 6. Official Meta / Instagram OAuth (`/social-accounts`)
- Connect official Instagram Business / Creator accounts via Meta Graph API OAuth
- Token expiration tracker and disconnect functionality
- Sandbox / Developer account connector for local testing without live Meta app credentials.

### 7. Import & Export (`/import-export`)
- Upload CSV with automatic column mapping and duplicate prevention
- Download sample CSV import template
- Export filtered leads to CSV.

### 8. Enterprise Admin Panel (`/admin`)
- Team member creation and role switching (`admin`, `manager`, `sales_agent`)
- Subscription tier management (Free, Starter, Pro, Enterprise)
- Monthly quota management
- Data source toggling
- Immutable Audit Trail (`/admin/audit-logs`).

### 9. REST API with Laravel Sanctum (`/api`)
- `POST /api/auth/login` - Obtain Sanctum bearer token
- `GET /api/leads` - Filter and fetch CRM leads
- `GET /api/leads/{id}` - Lead details profile
- `GET /api/discovery` - Search permitted leads

---

## 🧪 Automated Testing
Run the complete PHPUnit test suite:
```bash
php artisan test
```
All feature and unit tests pass with assertions across authentication, dashboard KPIs, lead discovery, CRM management, CSV export, and Sanctum REST APIs.

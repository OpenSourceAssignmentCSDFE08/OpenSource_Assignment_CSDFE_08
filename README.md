# Security Incident Email Monitoring System (SIEMS)

Local PHP + MySQL app for XAMPP. No internet required at runtime — Bootstrap & Chart.js are loaded from CDN by default; for fully-offline use see "Offline assets" below.

## Install (XAMPP)

1. Copy this `security-incident-system` folder into `xampp/htdocs/`.
2. Start **Apache** and **MySQL** from the XAMPP control panel.
3. Open `http://localhost/phpmyadmin`, click **Import**, choose
   `database/security_incident_db.sql`, and run it.
4. Visit `http://localhost/security-incident-system/`.

## Features

- Secure login (PHP `password_hash`, CSRF token, session timeout 30 min, show/hide password)
- Dashboard: 6 animated stat cards + 4 Chart.js charts (severity, daily trend, keyword frequency, threat sources)
- Incident list: AJAX, search, severity/status filters, sortable columns, pagination
- Incident details: highlights dangerous keywords in red, recommendations, threat score
- Keyword library CRUD (used by the detection engine)
- Reports: PDF (browser print-to-PDF), Excel (.xls), Printable
- Dark / Light theme toggle, sidebar layout, responsive mobile design
- Cyber theme: dark blue, black, neon green, white

## Security

- PDO prepared statements throughout
- `password_hash` / `password_verify`
- CSRF token validated on all POSTs
- Session regenerated on login, HttpOnly cookies, 30-min inactivity timeout
- HTML output escaped via `htmlspecialchars`
- Whitelisted sort columns in AJAX endpoint

## Folder structure

```
security-incident-system/
├── index.php
├── login.php  logout.php
├── dashboard.php  incidents.php  incident_details.php
├── reports.php  keywords.php
├── ajax_incidents.php
├── export_pdf.php  export_excel.php
├── assets/{css,js,images}/
├── includes/{db,auth,functions,header,sidebar,footer}.php
└── database/security_incident_db.sql
```
## github link
https://github.com/OpenSourceAssignmentCSDFE08/OpenSource_Assignment_CSDFE_08.git

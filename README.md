# Security Incident Email Monitoring System (SIEMS)

Local PHP + MySQL app for XAMPP. No internet required at runtime — Bootstrap & Chart.js are loaded from CDN by default; for fully-offline use see "Offline assets" below.

## Install (XAMPP)

1. Copy this `security-incident-system` folder into `xampp/htdocs/`.
2. Start **Apache** and **MySQL** from the XAMPP control panel.
3. Open `http://localhost/phpmyadmin`, click **Import**, choose
   `database/security_incident_db.sql`, and run it.
4. Visit `http://localhost/security-incident-system/`.
5. 5. Login:
   - **Username:** `admin`
   - **Password:** `admin123`

## Features

- Secure login (PHP `password_hash`, CSRF token, session timeout 30 min, show/hide password)
- Dashboard: 6 animated stat cards + 4 Chart.js charts (severity, daily trend, keyword frequency, threat sources)
- Incident list: AJAX, search, severity/status filters, sortable columns, pagination
- Incident details: highlights dangerous keywords in red, recommendations, threat score
- Keyword library CRUD (used by the detection engine)
- Reports: PDF (browser print-to-PDF), Excel (.xls), Printable
- Dark / Light theme toggle, sidebar layout, responsive mobile design
- Cyber theme: dark blue, black, neon green, white

## LIST OF GIT COMMAD USED
Create / Initialize Repository
git init
Connect to GitHub Repository
git remote add origin https://github.com/CYBER/OpenSource_Assignment_Program_Group08.git
git remote -v
Create and Commit README.md
touch README.md
git add README.md
git commit -m "Added README file with project details"
First Push to GitHub
git branch -M main
git push -u origin main
Project Development Commits (minimum 5 commits)
Example workflow:
After creating a feature (e.g. student registration form)
git add .
git commit -m "Added student registration module"
After adding display feature
git add .
git commit -m "Implemented student record display feature"
After adding search feature
git add .
git commit -m "Added student search functionality"
After database connection
git add .
git commit -m "Connected project to MySQL database"
After UI improvement
git add .
git commit -m "Improved UI using Bootstrap"
Create and Work on Development Branch
Create branch:
git branch development
git switch -c development
Add Feature in Development Branch
git add .
git commit -m "Added new feature in development branch"
Switch Back to Main Branch
git switch main
Merge Development Branch into Main
git merge development
Push All Changes to GitHub
git push origin main
git push origin development

## Folder structure
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

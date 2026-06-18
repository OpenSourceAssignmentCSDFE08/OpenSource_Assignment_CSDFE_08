-- Security Incident Email Monitoring System
-- Database schema + seed data
-- Import via phpMyAdmin or: mysql -u root -p < security_incident_db.sql

DROP DATABASE IF EXISTS security_incident_db;
CREATE DATABASE security_incident_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE security_incident_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(150),
    role VARCHAR(50) DEFAULT 'analyst',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE security_incidents (
    incident_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_email VARCHAR(255) NOT NULL,
    receiver_email VARCHAR(255) NOT NULL,
    email_subject VARCHAR(255),
    email_content LONGTEXT,
    dangerous_keywords TEXT,
    threat_score INT DEFAULT 0,
    severity_level ENUM('Low','Medium','High') DEFAULT 'Low',
    detection_reason TEXT,
    status ENUM('Open','Investigating','Resolved') DEFAULT 'Open',
    detected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    report_name VARCHAR(255),
    report_type VARCHAR(50),
    generated_by INT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE keyword_library (
    keyword_id INT AUTO_INCREMENT PRIMARY KEY,
    keyword_name VARCHAR(100) UNIQUE,
    severity_weight INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin user: admin / admin123
INSERT INTO users (username, password, full_name, role) VALUES
('admin', '$2y$10$e0NRiP7m6kS8Yw3l6oQ8Hu5sM1k7g7L9wYzG7r2nFqB7eC6P5d2cS', 'System Administrator', 'admin');
-- Note: replace hash by running php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
-- The system will auto-create the admin on first run if missing (see includes/db.php).

INSERT INTO keyword_library (keyword_name, severity_weight) VALUES
('password',3),('login',2),('verify',2),('urgent',2),('bank',3),
('account',2),('payment',3),('click here',2),('transfer',3),
('confidential',3),('security alert',3),('wire',3),('ssn',3),
('credit card',3),('otp',3);

INSERT INTO security_incidents
(sender_email, receiver_email, email_subject, email_content, dangerous_keywords, threat_score, severity_level, detection_reason, status) VALUES
('attacker@phish.ru','user1@company.com','Urgent: Verify your bank account',
 'Dear customer, please verify your bank account password immediately. Click here to confirm payment.',
 'urgent,verify,bank,account,password,click here,payment',16,'High','Multiple high-risk keywords detected','Open'),
('promo@news.io','user2@company.com','Monthly newsletter',
 'Read the latest updates from our team.','',0,'Low','No dangerous keywords','Resolved'),
('it-support@fake.com','user3@company.com','Security alert: reset password',
 'A security alert was triggered, login and reset your password now.',
 'security alert,login,password',8,'High','Sensitive credential keywords','Investigating'),
('billing@unknown.biz','user4@company.com','Payment confirmation',
 'Your payment of $499 is pending, click here to confirm.',
 'payment,click here',5,'Medium','Payment + action link','Open'),
('hr@company.com','user5@company.com','Team lunch Friday',
 'Lunch at 1pm in cafeteria.','',0,'Low','Benign internal message','Resolved'),
('noreply@bank-secure.co','user6@company.com','Confidential wire transfer',
 'Please process this confidential wire transfer urgently.',
 'confidential,transfer,urgent',9,'High','Wire fraud pattern','Investigating'),
('alerts@service.com','user7@company.com','OTP request',
 'Your OTP for account verification is 123456.',
 'otp,account,verify',7,'Medium','OTP phishing pattern','Open');

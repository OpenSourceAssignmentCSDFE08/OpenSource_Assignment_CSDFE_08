<?php require_once __DIR__ . '/auth.php'; require_login(); ?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= isset($pageTitle) ? e($pageTitle).' · ' : '' ?>SIEMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-shell">
<?php include __DIR__ . '/sidebar.php'; ?>
<main class="app-main">
<nav class="topbar">
  <button class="btn btn-sm btn-outline-light d-md-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
  <div class="ms-auto d-flex align-items-center gap-3">
    <button class="btn btn-sm btn-outline-light" id="themeToggle" title="Toggle theme"><i class="bi bi-moon-stars"></i></button>
    <span class="text-light small"><i class="bi bi-person-circle"></i> <?= e($_SESSION['full_name'] ?? $_SESSION['username']) ?></span>
    <a href="logout.php" class="btn btn-sm btn-neon"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</nav>
<div class="content-wrap">

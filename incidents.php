<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_login();
$pageTitle='Incidents';
include __DIR__.'/includes/header.php';
?>
<h3 class="mb-3"><i class="bi bi-exclamation-triangle text-neon"></i> Security Incidents</h3>

<div class="card p-3 mb-3">
  <form id="filterForm" class="row g-2">
    <div class="col-md-3"><input class="form-control" name="q" placeholder="Search ID, sender, receiver, keyword..."></div>
    <div class="col-md-2">
      <select class="form-select" name="severity">
        <option value="">All severities</option>
        <option>High</option><option>Medium</option><option>Low</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" name="status">
        <option value="">All statuses</option>
        <option>Open</option><option>Investigating</option><option>Resolved</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" name="per_page">
        <option value="10">10 / page</option>
        <option value="25">25 / page</option>
        <option value="50">50 / page</option>
      </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
      <button class="btn btn-neon w-100"><i class="bi bi-search"></i> Apply</button>
      <button type="reset" class="btn btn-outline-light"><i class="bi bi-x"></i></button>
    </div>
  </form>
</div>

<div class="card p-3">
  <div id="incidentsTable">Loading...</div>
</div>

<script>window.INCIDENTS_PAGE = true;</script>
<?php include __DIR__.'/includes/footer.php'; ?>

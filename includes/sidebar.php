<aside class="sidebar" id="sidebar">
  <div class="brand"><i class="bi bi-shield-lock-fill text-neon"></i> <span>SIEMS</span></div>
  <nav class="nav flex-column">
    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='dashboard.php'?'active':'' ?>" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='incidents.php'?'active':'' ?>" href="incidents.php"><i class="bi bi-exclamation-triangle"></i> Incidents</a>
    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='reports.php'?'active':'' ?>" href="reports.php"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])==='keywords.php'?'active':'' ?>" href="keywords.php"><i class="bi bi-key"></i> Keywords</a>
  </nav>
  <div class="sidebar-footer small text-muted px-3">v1.0 · Cyber Defense</div>
</aside>

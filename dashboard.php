<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/functions.php';
require_login();
$pageTitle = 'Dashboard';

$stats = [
  'total'  => (int)$pdo->query("SELECT COUNT(*) FROM security_incidents")->fetchColumn(),
  'high'   => (int)$pdo->query("SELECT COUNT(*) FROM security_incidents WHERE severity_level='High'")->fetchColumn(),
  'medium' => (int)$pdo->query("SELECT COUNT(*) FROM security_incidents WHERE severity_level='Medium'")->fetchColumn(),
  'low'    => (int)$pdo->query("SELECT COUNT(*) FROM security_incidents WHERE severity_level='Low'")->fetchColumn(),
  'today'  => (int)$pdo->query("SELECT COUNT(*) FROM security_incidents WHERE DATE(detected_at)=CURDATE()")->fetchColumn(),
  'reports'=> (int)$pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn(),
];

$byDay = $pdo->query("SELECT DATE(detected_at) d, COUNT(*) c FROM security_incidents
                     WHERE detected_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
                     GROUP BY DATE(detected_at) ORDER BY d")->fetchAll();

$senders = $pdo->query("SELECT SUBSTRING_INDEX(sender_email,'@',-1) src, COUNT(*) c
                        FROM security_incidents GROUP BY src ORDER BY c DESC LIMIT 6")->fetchAll();

// Keyword frequency
$rows = $pdo->query("SELECT dangerous_keywords FROM security_incidents WHERE dangerous_keywords<>''")->fetchAll();
$kwCount = [];
foreach ($rows as $r) {
    foreach (array_filter(array_map('trim', explode(',', $r['dangerous_keywords']))) as $k) {
        $kwCount[$k] = ($kwCount[$k] ?? 0) + 1;
    }
}
arsort($kwCount); $kwCount = array_slice($kwCount, 0, 8, true);

include __DIR__.'/includes/header.php';
?>
<h3 class="mb-3"><i class="bi bi-speedometer2 text-neon"></i> Dashboard</h3>

<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['Total Incidents',   $stats['total'],   'bi-shield-exclamation','primary'],
    ['High Severity',     $stats['high'],    'bi-exclamation-octagon','danger'],
    ['Medium Severity',   $stats['medium'],  'bi-exclamation-triangle','warning'],
    ['Low Severity',      $stats['low'],     'bi-info-circle','success'],
    ['Today\'s Incidents',$stats['today'],   'bi-calendar-event','info'],
    ['Reports Generated', $stats['reports'], 'bi-file-earmark-text','neon'],
  ];
  foreach ($cards as $c): ?>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card stat-<?= $c[3] ?>">
      <div class="stat-icon"><i class="bi <?= $c[2] ?>"></i></div>
      <div class="stat-meta">
        <div class="stat-label"><?= e($c[0]) ?></div>
        <div class="stat-value" data-count="<?= (int)$c[1] ?>">0</div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="row g-3">
  <div class="col-lg-6"><div class="card p-3"><h6>Severity Distribution</h6><canvas id="chartSeverity" height="180"></canvas></div></div>
  <div class="col-lg-6"><div class="card p-3"><h6>Incidents by Day (last 14)</h6><canvas id="chartDay" height="180"></canvas></div></div>
  <div class="col-lg-6"><div class="card p-3"><h6>Dangerous Keywords Trend</h6><canvas id="chartKw" height="180"></canvas></div></div>
  <div class="col-lg-6"><div class="card p-3"><h6>Email Threat Sources</h6><canvas id="chartSrc" height="180"></canvas></div></div>
</div>

<div class="card mt-4 p-3">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0">Recent Incidents</h6>
    <a class="btn btn-sm btn-neon" href="incidents.php">View all <i class="bi bi-arrow-right"></i></a>
  </div>
  <div id="incidentsMini">Loading...</div>
</div>

<script>
window.SIEMS_DATA = {
  severity: {High: <?= $stats['high'] ?>, Medium: <?= $stats['medium'] ?>, Low: <?= $stats['low'] ?>},
  byDay: <?= json_encode($byDay) ?>,
  keywords: <?= json_encode($kwCount) ?>,
  sources: <?= json_encode($senders) ?>
};
</script>
<?php include __DIR__.'/includes/footer.php'; ?>

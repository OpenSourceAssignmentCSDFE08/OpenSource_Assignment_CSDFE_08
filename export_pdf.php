<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_login();
$print = !empty($_GET['print']);
$rows = $pdo->query("SELECT * FROM security_incidents ORDER BY detected_at DESC")->fetchAll();
$counts = ['High'=>0,'Medium'=>0,'Low'=>0]; $kw=[];
foreach ($rows as $r) {
    $counts[$r['severity_level']] = ($counts[$r['severity_level']] ?? 0) + 1;
    foreach (array_filter(array_map('trim', explode(',', (string)$r['dangerous_keywords']))) as $k) $kw[$k] = ($kw[$k] ?? 0)+1;
}
arsort($kw);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Security Incident Report</title>
<style>
body{font-family:Arial, sans-serif;color:#111;padding:24px;}
h1{margin:0;color:#0a3d62}
.meta{color:#555;margin-bottom:18px}
table{width:100%;border-collapse:collapse;margin-top:8px;font-size:12px}
th,td{border:1px solid #999;padding:6px;text-align:left}
th{background:#0a3d62;color:#fff}
.sev-High{color:#b00020;font-weight:bold}
.sev-Medium{color:#b07d00;font-weight:bold}
.sev-Low{color:#0a7d36;font-weight:bold}
.section{margin-top:18px}
.no-print{margin-bottom:12px}
@media print { .no-print{display:none} }
</style></head><body>
<div class="no-print"><button onclick="window.print()">Save as PDF / Print</button></div>
<h1>Security Incident Report</h1>
<div class="meta">Organization: ACME Corp · Generated: <?= date('Y-m-d H:i') ?> · By: <?= htmlspecialchars($_SESSION['username']) ?></div>

<div class="section"><h3>Incident Statistics</h3>
<table style="width:auto"><tr><th>Total</th><th>High</th><th>Medium</th><th>Low</th></tr>
<tr><td><?= count($rows) ?></td><td class="sev-High"><?= $counts['High'] ?></td>
<td class="sev-Medium"><?= $counts['Medium'] ?></td><td class="sev-Low"><?= $counts['Low'] ?></td></tr></table></div>

<div class="section"><h3>Keyword Analysis</h3>
<table><tr><th>Keyword</th><th>Occurrences</th></tr>
<?php foreach (array_slice($kw,0,10,true) as $k=>$c): ?>
<tr><td><?= htmlspecialchars($k) ?></td><td><?= $c ?></td></tr>
<?php endforeach; ?>
</table></div>

<div class="section"><h3>Incident List</h3>
<table>
<thead><tr><th>ID</th><th>Sender</th><th>Receiver</th><th>Subject</th><th>Severity</th><th>Score</th><th>Status</th><th>Detected</th></tr></thead>
<tbody>
<?php foreach ($rows as $r): ?>
<tr>
 <td><?= (int)$r['incident_id'] ?></td>
 <td><?= htmlspecialchars($r['sender_email']) ?></td>
 <td><?= htmlspecialchars($r['receiver_email']) ?></td>
 <td><?= htmlspecialchars($r['email_subject']) ?></td>
 <td class="sev-<?= $r['severity_level'] ?>"><?= $r['severity_level'] ?></td>
 <td><?= (int)$r['threat_score'] ?></td>
 <td><?= htmlspecialchars($r['status']) ?></td>
 <td><?= htmlspecialchars($r['detected_at']) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>

<script><?php if (!$print): ?>window.addEventListener('load',()=>setTimeout(()=>window.print(),300));<?php endif; ?></script>
</body></html>

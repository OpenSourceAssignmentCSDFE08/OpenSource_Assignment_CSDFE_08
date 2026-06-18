<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_login();
$rows = $pdo->query("SELECT * FROM security_incidents ORDER BY detected_at DESC")->fetchAll();
$fn = 'incidents_'.date('Ymd_His').'.xls';
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$fn.'"');
echo "\xEF\xBB\xBF"; // BOM
echo "<table border='1'><thead><tr>";
foreach (['ID','Sender','Receiver','Subject','Keywords','Score','Severity','Status','Detected At'] as $h) echo "<th>".htmlspecialchars($h)."</th>";
echo "</tr></thead><tbody>";
foreach ($rows as $r) {
    echo "<tr>";
    foreach ([$r['incident_id'],$r['sender_email'],$r['receiver_email'],$r['email_subject'],$r['dangerous_keywords'],$r['threat_score'],$r['severity_level'],$r['status'],$r['detected_at']] as $c)
        echo "<td>".htmlspecialchars((string)$c)."</td>";
    echo "</tr>";
}
echo "</tbody></table>";

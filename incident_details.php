<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/functions.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM security_incidents WHERE incident_id=?");
$stmt->execute([$id]);
$inc = $stmt->fetch();
$pageTitle = 'Incident #'.$id;
include __DIR__.'/includes/header.php';

if (!$inc) { echo '<div class="alert alert-warning">Incident not found.</div>'; include __DIR__.'/includes/footer.php'; exit; }

$keywords = array_filter(array_map('trim', explode(',', (string)$inc['dangerous_keywords'])));
$recommendations = match($inc['severity_level']) {
    'High'   => ['Quarantine the email immediately','Block sender domain','Notify recipient and SOC','Reset affected credentials'],
    'Medium' => ['Investigate sender reputation','Warn recipient','Monitor related traffic'],
    default  => ['Mark as reviewed','Optional user awareness reminder'],
};
?>
<a href="incidents.php" class="btn btn-sm btn-outline-light mb-3"><i class="bi bi-arrow-left"></i> Back</a>
<h3 class="mb-3"><i class="bi bi-file-earmark-medical text-neon"></i> Incident #<?= e($inc['incident_id']) ?> <?= severity_badge($inc['severity_level']) ?></h3>

<div class="row g-3">
 <div class="col-lg-6">
   <div class="card p-3">
     <h6>Email Information</h6>
     <table class="table table-sm table-borderless mb-0">
       <tr><th style="width:140px">Incident ID</th><td><?= e($inc['incident_id']) ?></td></tr>
       <tr><th>Sender</th><td><?= e($inc['sender_email']) ?></td></tr>
       <tr><th>Receiver</th><td><?= e($inc['receiver_email']) ?></td></tr>
       <tr><th>Subject</th><td><?= e($inc['email_subject']) ?></td></tr>
       <tr><th>Date Received</th><td><?= e($inc['detected_at']) ?></td></tr>
       <tr><th>Status</th><td><span class="badge bg-info"><?= e($inc['status']) ?></span></td></tr>
     </table>
   </div>
 </div>
 <div class="col-lg-6">
   <div class="card p-3">
     <h6>Threat Analysis</h6>
     <p class="mb-1"><strong>Risk Score:</strong> <span class="text-neon"><?= (int)$inc['threat_score'] ?></span></p>
     <p class="mb-1"><strong>Severity:</strong> <?= severity_badge($inc['severity_level']) ?></p>
     <p class="mb-2"><strong>Detection Reason:</strong> <?= e($inc['detection_reason']) ?></p>
     <p class="mb-1"><strong>Dangerous Keywords:</strong></p>
     <div>
       <?php foreach ($keywords as $k): ?>
         <span class="badge bg-danger me-1 mb-1"><?= e($k) ?></span>
       <?php endforeach; ?>
       <?php if (!$keywords): ?><em class="text-muted">None</em><?php endif; ?>
     </div>
     <hr>
     <strong>Recommended Actions:</strong>
     <ul class="mb-0"><?php foreach ($recommendations as $r): ?><li><?= e($r) ?></li><?php endforeach; ?></ul>
   </div>
 </div>
 <div class="col-12">
   <div class="card p-3">
     <h6>Email Content (dangerous keywords highlighted)</h6>
     <div class="email-content border rounded p-3"><?= highlight_keywords((string)$inc['email_content'], $keywords) ?></div>
   </div>
 </div>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>

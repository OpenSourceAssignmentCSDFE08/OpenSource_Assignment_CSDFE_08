<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_login();
$pageTitle='Reports';

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf'] ?? '')) {
    $type = $_POST['type'] ?? 'pdf';
    $name = 'Security Report '.date('Y-m-d H:i');
    $pdo->prepare("INSERT INTO reports (report_name, report_type, generated_by) VALUES (?,?,?)")
        ->execute([$name, $type, $_SESSION['user_id']]);
    if ($type==='excel') { header('Location: export_excel.php'); exit; }
    if ($type==='pdf')   { header('Location: export_pdf.php');   exit; }
    if ($type==='print') { header('Location: export_pdf.php?print=1'); exit; }
}

$recent = $pdo->query("SELECT r.*, u.username FROM reports r LEFT JOIN users u ON u.id=r.generated_by ORDER BY generated_at DESC LIMIT 25")->fetchAll();
include __DIR__.'/includes/header.php';
?>
<h3 class="mb-3"><i class="bi bi-file-earmark-bar-graph text-neon"></i> Reports</h3>
<div class="row g-3">
  <?php foreach (['pdf'=>'PDF Report','excel'=>'Excel Report','print'=>'Printable Report'] as $t=>$label): ?>
  <div class="col-md-4">
    <form method="post" class="card p-3 text-center">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="type" value="<?= $t ?>">
      <div class="display-5 text-neon"><i class="bi bi-file-earmark-<?= $t==='excel'?'spreadsheet':($t==='print'?'text':'pdf') ?>"></i></div>
      <h5><?= $label ?></h5>
      <p class="text-muted small">Generate a <?= $label ?> covering all incidents.</p>
      <button class="btn btn-neon">Generate</button>
    </form>
  </div>
  <?php endforeach; ?>
</div>

<div class="card p-3 mt-4">
  <h6>Recent Reports</h6>
  <div class="table-responsive">
  <table class="table table-sm align-middle mb-0">
    <thead><tr><th>#</th><th>Name</th><th>Type</th><th>By</th><th>Generated</th></tr></thead>
    <tbody>
    <?php foreach ($recent as $r): ?>
      <tr><td><?= (int)$r['report_id'] ?></td><td><?= e($r['report_name']) ?></td>
          <td><span class="badge bg-secondary text-uppercase"><?= e($r['report_type']) ?></span></td>
          <td><?= e($r['username']) ?></td><td><?= e($r['generated_at']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$recent): ?><tr><td colspan="5" class="text-center text-muted">No reports yet</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>

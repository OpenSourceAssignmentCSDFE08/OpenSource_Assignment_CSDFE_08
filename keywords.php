<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_login();
$pageTitle='Keywords';

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';
  if ($action==='add') {
    $k = trim($_POST['keyword'] ?? '');
    $w = max(1, min(10, (int)($_POST['weight'] ?? 1)));
    if ($k !== '') {
      try { $pdo->prepare("INSERT INTO keyword_library (keyword_name, severity_weight) VALUES (?,?)")->execute([$k,$w]); } catch(Exception $e){}
    }
  } elseif ($action==='delete') {
    $pdo->prepare("DELETE FROM keyword_library WHERE keyword_id=?")->execute([(int)$_POST['id']]);
  }
  header('Location: keywords.php'); exit;
}
$rows = $pdo->query("SELECT * FROM keyword_library ORDER BY severity_weight DESC, keyword_name")->fetchAll();
include __DIR__.'/includes/header.php';
?>
<h3 class="mb-3"><i class="bi bi-key text-neon"></i> Dangerous Keyword Library</h3>
<div class="row g-3">
  <div class="col-md-4">
    <form method="post" class="card p-3">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="add">
      <h6>Add keyword</h6>
      <div class="mb-2"><label class="form-label">Keyword</label><input class="form-control" name="keyword" required></div>
      <div class="mb-3"><label class="form-label">Severity weight (1-10)</label><input type="number" min="1" max="10" value="2" class="form-control" name="weight"></div>
      <button class="btn btn-neon">Add</button>
    </form>
  </div>
  <div class="col-md-8">
    <div class="card p-3">
      <table class="table table-sm align-middle">
        <thead><tr><th>#</th><th>Keyword</th><th>Weight</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['keyword_id'] ?></td>
            <td><?= e($r['keyword_name']) ?></td>
            <td><span class="badge bg-warning text-dark"><?= (int)$r['severity_weight'] ?></span></td>
            <td class="text-end">
              <form method="post" onsubmit="return confirm('Delete?')">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$r['keyword_id'] ?>">
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>

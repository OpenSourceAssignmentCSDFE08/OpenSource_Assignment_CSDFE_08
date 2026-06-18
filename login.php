<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $error = 'Invalid session token. Refresh and try again.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';
        if ($u==='' || $p==='') {
            $error = 'Please fill in both fields.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
            $stmt->execute([$u]);
            $user = $stmt->fetch();
            if ($user && password_verify($p, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name']= $user['full_name'];
                $_SESSION['role']     = $user['role'];
                header('Location: dashboard.php'); exit;
            } else {
                $error = 'Invalid username or password.';
            }
        }
    }
}
?>
<!doctype html><html lang="en" data-bs-theme="dark"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login · SIEMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head><body class="login-body">
<div class="login-bg"></div>
<div class="login-card card shadow-lg">
  <div class="card-body p-4">
    <div class="text-center mb-3">
      <div class="login-logo"><i class="bi bi-shield-lock-fill"></i></div>
      <h4 class="mt-2 mb-0">SIEMS</h4>
      <small class="text-muted">Security Incident Email Monitoring</small>
    </div>
    <?php if (!empty($_GET['timeout'])): ?>
      <div class="alert alert-warning py-2">Session expired. Please sign in again.</div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input class="form-control" name="username" required autofocus>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-key"></i></span>
          <input class="form-control" name="password" id="pw" type="password" required>
          <button type="button" class="btn btn-outline-secondary" id="togglePw"><i class="bi bi-eye"></i></button>
        </div>
      </div>
      <button class="btn btn-neon w-100"><i class="bi bi-box-arrow-in-right"></i> Sign In</button>
      
    </form>
  </div>
</div>
<script>
document.getElementById('togglePw').onclick = () => {
  const pw = document.getElementById('pw');
  pw.type = pw.type === 'password' ? 'text' : 'password';
};
</script>
</body></html>

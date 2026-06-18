<?php
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/db.php';
require_login();
header('Content-Type: application/json');

$q        = trim($_GET['q'] ?? '');
$severity = $_GET['severity'] ?? '';
$status   = $_GET['status'] ?? '';
$sort     = $_GET['sort'] ?? 'detected_at';
$dir      = strtolower($_GET['dir'] ?? 'desc')==='asc'?'ASC':'DESC';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = in_array((int)($_GET['per_page'] ?? 10), [5,10,25,50]) ? (int)$_GET['per_page'] : 10;

$allowedSort = ['incident_id','sender_email','receiver_email','severity_level','threat_score','detected_at','status'];
if (!in_array($sort, $allowedSort, true)) $sort = 'detected_at';

$where = []; $args = [];
if ($q !== '') {
    $where[] = "(incident_id = :idq OR sender_email LIKE :q OR receiver_email LIKE :q OR email_subject LIKE :q OR dangerous_keywords LIKE :q)";
    $args[':q']  = "%$q%";
    $args[':idq']= ctype_digit($q) ? (int)$q : 0;
}
if (in_array($severity, ['High','Medium','Low'], true)) { $where[]='severity_level=:sev'; $args[':sev']=$severity; }
if (in_array($status, ['Open','Investigating','Resolved'], true)) { $where[]='status=:st'; $args[':st']=$status; }
$whereSql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$total = (int)(function() use ($pdo,$whereSql,$args){
    $s=$pdo->prepare("SELECT COUNT(*) FROM security_incidents $whereSql");
    foreach($args as $k=>$v) $s->bindValue($k,$v);
    $s->execute(); return $s->fetchColumn();
})();

$offset = ($page-1)*$perPage;
$sql = "SELECT incident_id,sender_email,receiver_email,email_subject,severity_level,threat_score,status,detected_at
        FROM security_incidents $whereSql ORDER BY $sort $dir LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
foreach($args as $k=>$v) $stmt->bindValue($k,$v);
$stmt->execute();

echo json_encode([
  'total' => $total,
  'page'  => $page,
  'per_page' => $perPage,
  'pages' => max(1, (int)ceil($total/$perPage)),
  'rows'  => $stmt->fetchAll(),
]);

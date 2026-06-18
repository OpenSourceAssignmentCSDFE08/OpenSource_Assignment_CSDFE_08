<?php
// Threat detection + helpers
function get_keywords(PDO $pdo): array {
    $rows = $pdo->query("SELECT keyword_name, severity_weight FROM keyword_library")->fetchAll();
    return $rows ?: [];
}

function analyze_email(string $subject, string $content, array $keywords): array {
    $haystack = strtolower($subject . ' ' . $content);
    $found = []; $score = 0;
    foreach ($keywords as $k) {
        if (strpos($haystack, strtolower($k['keyword_name'])) !== false) {
            $found[] = $k['keyword_name'];
            $score += (int)$k['severity_weight'];
        }
    }
    $count = count($found);
    if ($count >= 5)      $sev = 'High';
    elseif ($count >= 3)  $sev = 'Medium';
    elseif ($count >= 1)  $sev = 'Low';
    else                  $sev = 'Low';
    return [
        'found' => $found,
        'score' => $score,
        'severity' => $sev,
        'reason' => $count
            ? "$count dangerous keyword(s) detected: " . implode(', ', $found)
            : 'No dangerous keywords detected'
    ];
}

function highlight_keywords(string $text, array $keywords): string {
    $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    foreach ($keywords as $k) {
        if ($k === '') continue;
        $safeK = preg_quote(htmlspecialchars($k, ENT_QUOTES, 'UTF-8'), '/');
        $safe = preg_replace('/('.$safeK.')/i',
            '<span class="kw-highlight">$1</span>', $safe);
    }
    return nl2br($safe);
}

function severity_badge(string $sev): string {
    $map = ['High'=>'danger','Medium'=>'warning','Low'=>'success'];
    $c = $map[$sev] ?? 'secondary';
    return '<span class="badge bg-'.$c.'">'.htmlspecialchars($sev).'</span>';
}

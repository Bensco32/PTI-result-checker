<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
$student = require_student();

$id = ctype_digit((string) ($_GET['id'] ?? '')) ? (int) $_GET['id'] : 0;
$stmt = db()->prepare('SELECT * FROM results WHERE id = ? AND student_id = ?');
$stmt->execute([$id, $student['id']]);
$result = $stmt->fetch();
if (!$result) {
    http_response_code(403);
    exit('You are not allowed to view this result.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PTI | Result Slip</title>
  <link rel="stylesheet" href="../css/style.css">
  <script src="../js/script.js" defer></script>
</head>
<body>
<header class="site-header no-print">
  <a class="brand" href="dashboard.php"><img src="../assets/pti-logo.jpeg" alt="PTI logo"><span><strong>PETROLEUM TRAINING INSTITUTE</strong><small>Student Result Slip</small></span></a>
</header>
<main class="slip-page">
  <section class="print-slip">
    <div class="slip-head">
      <img src="../assets/pti-logo.jpeg" alt="PTI logo">
      <div><h1>PETROLEUM TRAINING INSTITUTE</h1><h2>EFFURUN, DELTA STATE</h2><strong>STUDENT RESULT SLIP</strong></div>
      <div class="slip-meta">Session: <?= e($result['academic_session']) ?><br>Semester: <?= e($result['semester']) ?></div>
    </div>
    <div class="student-details slip-details">
      <div>
        <h2>STUDENT INFORMATION</h2>
        <p><b>Matriculation Number:</b><span><?= e($student['matric_number']) ?></span></p>
        <p><b>Full Name:</b><span><?= e($student['full_name']) ?></span></p>
        <p><b>Department:</b><span><?= e($student['department']) ?></span></p>
        <p><b>Programme:</b><span><?= e($student['programme']) ?></span></p>
        <p><b>Level:</b><span><?= e($student['level']) ?></span></p>
      </div>
      <img class="student-photo" src="<?= e(student_photo_url($student['student_photo'] ?? null)) ?>" alt="Student photograph">
    </div>
    <h2 class="table-title">Official Result Document</h2>
    <iframe class="pdf-frame" title="Result PDF" src="../serve-result.php?id=<?= (int) $result['id'] ?>"></iframe>
    <div class="signature-row">
      <div><span class="signature-line"></span><b>Head of Department</b><small>Academic Affairs</small></div>
      <div class="stamp">PTI<br><small>EFFURUN</small></div>
    </div>
    <p class="slip-note">The uploaded PDF remains the authoritative result document.</p>
    <div class="result-actions no-print">
      <button class="button button-primary" onclick="window.print()">Print This Slip</button>
      <a class="button button-outline" href="../download-result.php?id=<?= (int) $result['id'] ?>">Download PDF</a>
      <a class="button button-outline" href="dashboard.php">Back to Dashboard</a>
    </div>
  </section>
</main>
</body>
</html>

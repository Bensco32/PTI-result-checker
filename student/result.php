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
    $context = 'student'; $active = 'results'; $title = 'PTI | Access Denied';
    require __DIR__ . '/../includes/header.php';
    echo '<main class="content-page"><div class="alert alert-error">You are not allowed to view this result.</div><a class="button button-primary" href="dashboard.php">Back to Dashboard</a></main>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$context = 'student';
$active = 'results';
$title = 'PTI | My Result';
require __DIR__ . '/../includes/header.php';
?>
<main class="result-page">
  <section class="result-panel">
    <div class="result-heading"><img src="<?= e(base('assets/pti-logo.jpeg')) ?>" alt="PTI logo"><div><h1>PETROLEUM TRAINING INSTITUTE</h1><p>STUDENT ACADEMIC RESULT</p></div></div>
    <div class="student-details">
      <div>
        <h2>Student Information</h2>
        <p><b>Matriculation Number:</b><span><?= e($student['matric_number']) ?></span></p>
        <p><b>Full Name:</b><span><?= e($student['full_name']) ?></span></p>
        <p><b>Department:</b><span><?= e($student['department']) ?></span></p>
        <p><b>Programme:</b><span><?= e($student['programme']) ?></span></p>
        <p><b>Level:</b><span><?= e($student['level']) ?></span></p>
        <p><b>Academic Session:</b><span><?= e($result['academic_session']) ?></span></p>
        <p><b>Semester:</b><span><?= e($result['semester']) ?></span></p>
      </div>
      <img class="student-photo" src="<?= e(student_photo_url($student['student_photo'] ?? null)) ?>" alt="Student photograph">
    </div>
    <h2 class="table-title"><?= e($result['semester']) ?> Result (<?= e($result['academic_session']) ?>)</h2>
    <iframe class="pdf-frame" title="Result PDF preview" src="../serve-result.php?id=<?= (int) $result['id'] ?>"></iframe>
    <div class="result-actions">
      <a class="button button-primary" href="../download-result.php?id=<?= (int) $result['id'] ?>">⬇ &nbsp; Download Result PDF</a>
      <a class="button button-outline" href="result-slip.php?id=<?= (int) $result['id'] ?>">▣ &nbsp; Print Result</a>
      <a class="button button-outline" href="dashboard.php">⌂ &nbsp; Back to Dashboard</a>
    </div>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>

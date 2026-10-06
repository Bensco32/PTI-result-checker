<?php
declare(strict_types=1);
$base = '';
require_once __DIR__ . '/includes/functions.php';
$context = 'public';
$active = 'check';
$title = 'PTI Result Checker | Academic Result';

$matric   = trim((string) ($_GET['matric'] ?? ''));
$session  = trim((string) ($_GET['session'] ?? ''));
$semester = trim((string) ($_GET['semester'] ?? ''));

$student = null;
$result = null;
$searched = ($matric !== '' && $session !== '' && $semester !== '');

if ($searched) {
    $stmt = db()->prepare('SELECT * FROM students WHERE matric_number = ?');
    $stmt->execute([$matric]);
    $student = $stmt->fetch() ?: null;

    if ($student) {
        $stmt = db()->prepare(
            'SELECT * FROM results WHERE student_id = ? AND academic_session = ? AND semester = ?'
        );
        $stmt->execute([$student['id'], $session, $semester]);
        $result = $stmt->fetch() ?: null;
    }
}

require __DIR__ . '/includes/header.php';
?>
<main class="result-page">
  <section class="result-panel">
    <?php if (!$searched): ?>
      <div class="alert alert-error">Please provide your matriculation number, academic session and semester.</div>
      <div class="result-actions"><a class="button button-primary" href="check-result.php">Back to Result Checker</a></div>
    <?php elseif ($student === null || $result === null): ?>
      <div class="alert alert-error">No result was found for the matriculation number, session and semester provided.</div>
      <div class="result-actions"><a class="button button-primary" href="check-result.php">Try Again</a></div>
    <?php else: ?>
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
      <iframe class="pdf-frame" title="Result PDF preview"
        src="serve-result.php?matric=<?= urlencode($matric) ?>&session=<?= urlencode($session) ?>&semester=<?= urlencode($semester) ?>"></iframe>
      <div class="result-actions">
        <a class="button button-primary" href="download-result.php?matric=<?= urlencode($matric) ?>&session=<?= urlencode($session) ?>&semester=<?= urlencode($semester) ?>">⬇ &nbsp; Download Result PDF</a>
        <a class="button button-outline" href="check-result.php">Back to Result Checker</a>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
$student = require_student();

$results = db()->prepare('SELECT * FROM results WHERE student_id = ? ORDER BY uploaded_at DESC');
$results->execute([$student['id']]);
$results = $results->fetchAll();

$context = 'student';
$active = 'dashboard';
$title = 'PTI | Student Dashboard';
require __DIR__ . '/../includes/header.php';
$flash = get_flash();
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">Welcome, <?= e($student['full_name']) ?></h1>
  <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
  <section class="table-panel">
    <h2 class="table-title">Student Information</h2>
    <p><b>Matric Number:</b> <?= e($student['matric_number']) ?></p>
    <p><b>Department:</b> <?= e($student['department']) ?></p>
    <p><b>Programme:</b> <?= e($student['programme']) ?></p>
    <p><b>Level:</b> <?= e($student['level']) ?></p>
  </section>

  <h2 class="table-title">My Results</h2>
  <section class="table-panel">
    <?php if (!$results): ?>
      <p class="muted" style="text-align:left">No results have been uploaded for you yet.</p>
    <?php else: ?>
      <table>
        <thead><tr><th>Session</th><th>Semester</th><th>Uploaded</th><th>Result</th></tr></thead>
        <tbody>
        <?php foreach ($results as $r): ?>
          <tr>
            <td><?= e($r['academic_session']) ?></td>
            <td><?= e($r['semester']) ?></td>
            <td><?= e(substr((string) $r['uploaded_at'], 0, 10)) ?></td>
            <td>
              <a class="button button-primary button-small" href="result.php?id=<?= (int) $r['id'] ?>">View Result</a>
              <a class="button button-outline button-small" href="../download-result.php?id=<?= (int) $r['id'] ?>">Download</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>

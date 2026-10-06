<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
$student = require_student();

$results = db()->prepare('SELECT * FROM results WHERE student_id = ? ORDER BY academic_session DESC, semester');
$results->execute([$student['id']]);
$results = $results->fetchAll();

$context = 'student';
$active = 'results';
$title = 'PTI | My Results';
require __DIR__ . '/../includes/header.php';
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">My Results</h1>
  <section class="table-panel">
    <?php if (!$results): ?>
      <p class="muted" style="text-align:left">No results available yet. Please check back later.</p>
    <?php else: ?>
      <table>
        <thead><tr><th>#</th><th>Session</th><th>Semester</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($results as $i => $r): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= e($r['academic_session']) ?></td>
            <td><?= e($r['semester']) ?></td>
            <td><a class="button button-primary button-small" href="result.php?id=<?= (int) $r['id'] ?>">View Result</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>

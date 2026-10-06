<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
$admin = require_admin();

$totalStudents = (int) db()->query('SELECT COUNT(*) FROM students')->fetchColumn();
$totalResults  = (int) db()->query('SELECT COUNT(*) FROM results')->fetchColumn();
$currentSessions = (int) db()->query('SELECT COUNT(DISTINCT academic_session) FROM results')->fetchColumn();
$recent = db()->query('SELECT matric_number, full_name, department, created_at FROM students ORDER BY created_at DESC LIMIT 5')->fetchAll();

$context = 'admin';
$active = 'dashboard';
$title = 'PTI | Admin Dashboard';
require __DIR__ . '/../includes/header.php';
$flash = get_flash();
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">Admin Dashboard</h1>
  <p class="muted" style="text-align:left">Welcome back, <?= e($admin['full_name']) ?>.</p>
  <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>

  <section class="stats-grid">
    <div class="stat-card"><b><?= $totalStudents ?></b><span>Total Students</span></div>
    <div class="stat-card"><b><?= $totalResults ?></b><span>Results Uploaded</span></div>
    <div class="stat-card"><b><?= $currentSessions ?></b><span>Academic Sessions</span></div>
  </section>

  <h2 class="table-title">Recent Student Registrations</h2>
  <section class="table-panel">
    <?php if (!$recent): ?>
      <p class="muted" style="text-align:left">No students registered yet.</p>
    <?php else: ?>
      <table>
        <thead><tr><th>Matric Number</th><th>Full Name</th><th>Department</th><th>Registered</th></tr></thead>
        <tbody>
        <?php foreach ($recent as $r): ?>
          <tr><td><?= e($r['matric_number']) ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['department']) ?></td><td><?= e(substr((string) $r['created_at'], 0, 10)) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <div class="result-actions" style="margin-top:22px">
    <a class="button button-primary" href="students.php">Manage Students</a>
    <a class="button button-outline" href="add-student.php">Add Student</a>
    <a class="button button-outline" href="results.php">Manage Results</a>
    <a class="button button-outline" href="upload-result.php">Upload Result</a>
  </div>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>

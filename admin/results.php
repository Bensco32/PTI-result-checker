<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin();

$matric   = trim((string) ($_GET['matric'] ?? ''));
$session  = trim((string) ($_GET['session'] ?? ''));
$semester = trim((string) ($_GET['semester'] ?? ''));

$sql = 'SELECT r.*, s.matric_number, s.full_name FROM results r JOIN students s ON s.id = r.student_id WHERE 1=1';
$params = [];
if ($matric !== '') {
    $sql .= ' AND s.matric_number ILIKE ?';
    $params[] = '%' . $matric . '%';
}
if ($session !== '') {
    $sql .= ' AND r.academic_session = ?';
    $params[] = $session;
}
if ($semester !== '') {
    $sql .= ' AND r.semester = ?';
    $params[] = $semester;
}
$sql .= ' ORDER BY r.uploaded_at DESC LIMIT 100';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

$sessions = db()->query('SELECT DISTINCT academic_session FROM results ORDER BY academic_session DESC')->fetchAll(PDO::FETCH_COLUMN);

$context = 'admin';
$active = 'results';
$title = 'PTI | Manage Results';
require __DIR__ . '/../includes/header.php';
$flash = get_flash();
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">Results</h1>
  <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>

  <form class="toolbar" method="get" action="results.php">
    <input type="text" name="matric" value="<?= e($matric) ?>" placeholder="Matric number">
    <select name="session">
      <option value="">All sessions</option>
      <?php foreach ($sessions as $s): ?>
        <option value="<?= e($s) ?>" <?= $s === $session ? 'selected' : '' ?>><?= e($s) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="semester">
      <option value="">All semesters</option>
      <option <?= $semester === 'First Semester' ? 'selected' : '' ?>>First Semester</option>
      <option <?= $semester === 'Second Semester' ? 'selected' : '' ?>>Second Semester</option>
    </select>
    <button class="button button-primary button-small" type="submit">Filter</button>
    <a class="button button-outline button-small" href="upload-result.php">+ Upload Result</a>
  </form>

  <section class="table-panel">
    <table>
      <thead><tr><th>Matric Number</th><th>Student</th><th>Session</th><th>Semester</th><th>Uploaded</th><th>Actions</th></tr></thead>
      <tbody>
      <?php if (!$results): ?>
        <tr><td colspan="6">No results found.</td></tr>
      <?php endif; ?>
      <?php foreach ($results as $r): ?>
        <tr>
          <td><?= e($r['matric_number']) ?></td>
          <td><?= e($r['full_name']) ?></td>
          <td><?= e($r['academic_session']) ?></td>
          <td><?= e($r['semester']) ?></td>
          <td><?= e(substr((string) $r['uploaded_at'], 0, 10)) ?></td>
          <td>
            <div class="actions-row">
              <a class="button button-outline button-small" target="_blank" href="../serve-result.php?id=<?= (int) $r['id'] ?>">View</a>
              <a class="button button-outline button-small" href="../download-result.php?id=<?= (int) $r['id'] ?>">Download</a>
              <a class="button button-outline button-small" href="replace-result.php?id=<?= (int) $r['id'] ?>">Replace</a>
              <form method="post" action="delete-result.php">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <button class="button button-danger button-small" type="submit" data-confirm="Delete this result?">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>

<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin();

$search = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

if ($search !== '') {
    $like = '%' . $search . '%';
    $countStmt = db()->prepare('SELECT COUNT(*) FROM students WHERE matric_number ILIKE ? OR full_name ILIKE ? OR email ILIKE ?');
    $countStmt->execute([$like, $like, $like]);
    $total = (int) $countStmt->fetchColumn();
    $stmt = db()->prepare('SELECT * FROM students WHERE matric_number ILIKE ? OR full_name ILIKE ? OR email ILIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?');
    $stmt->execute([$like, $like, $like, $perPage, $offset]);
} else {
    $total = (int) db()->query('SELECT COUNT(*) FROM students')->fetchColumn();
    $stmt = db()->prepare('SELECT * FROM students ORDER BY created_at DESC LIMIT ? OFFSET ?');
    $stmt->execute([$perPage, $offset]);
}
$students = $stmt->fetchAll();

$context = 'admin';
$active = 'students';
$title = 'PTI | Manage Students';
require __DIR__ . '/../includes/header.php';
$flash = get_flash();
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">Students</h1>
  <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>

  <form class="toolbar" method="get" action="students.php">
    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search by matric number, name or email">
    <button class="button button-primary button-small" type="submit">Search</button>
    <a class="button button-outline button-small" href="add-student.php">+ Add Student</a>
  </form>

  <section class="table-panel">
    <table>
      <thead><tr><th>Matric Number</th><th>Name</th><th>Email</th><th>Department</th><th>Level</th><th>Actions</th></tr></thead>
      <tbody>
      <?php if (!$students): ?>
        <tr><td colspan="6">No students found.</td></tr>
      <?php endif; ?>
      <?php foreach ($students as $s): ?>
        <tr>
          <td><?= e($s['matric_number']) ?></td>
          <td><?= e($s['full_name']) ?></td>
          <td><?= e($s['email']) ?></td>
          <td><?= e($s['department']) ?></td>
          <td><?= e($s['level']) ?></td>
          <td>
            <div class="actions-row">
              <a class="button button-outline button-small" href="edit-student.php?id=<?= (int) $s['id'] ?>">Edit</a>
              <a class="button button-outline button-small" href="reset-password.php?id=<?= (int) $s['id'] ?>">Reset Password</a>
              <form method="post" action="delete-student.php">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                <button class="button button-danger button-small" type="submit" data-confirm="Delete this student and all of their results?">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php $pages = max(1, (int) ceil($total / $perPage)); ?>
    <p class="muted" style="text-align:left">Page <?= $page ?> of <?= $pages ?></p>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>

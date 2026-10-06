<?php
declare(strict_types=1);
$base = '';
require_once __DIR__ . '/includes/functions.php';
$context = 'public';
$active = 'about';
$title = 'About | PTI Result Checker';
require __DIR__ . '/includes/header.php';
?>
<main class="content-page">
  <h1>About This Project</h1>
  <p>The PTI Act: 1972 No.37: An Act to establish the Petroleum Training Institute to provide courses of instruction, training, and research in petroleum technology and to produce technicians and other skilled personnel required to run the petroleum industry.</p>
  <br>
  <a class="button button-primary" href="check-result.php">Go to Result Checker</a>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

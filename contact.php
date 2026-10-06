<?php
declare(strict_types=1);
$base = '';
require_once __DIR__ . '/includes/functions.php';
$context = 'public';
$active = 'contact';
$title = 'Contact | PTI Result Checker';
require __DIR__ . '/includes/header.php';
?>
<main class="content-page">
  <h1>Contact / Project Information</h1>
  <p>Welcome to our result checker website.</p>
  <div class="info-card">
    <h2>Contact Us:</h2>
    <p><b>Phone:</b> 09022152105, 07061401721 </p>
    <p><b>Support:</b> itsupport@pti.edu.ng </p>
    <p><b>Registrar's office:</b> info@pti.edu.ng </p>
  </div>
  <a class="button button-primary" href="index.php">Return Home</a>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

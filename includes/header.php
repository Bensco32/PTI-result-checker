<?php
declare(strict_types=1);
/** @var string $context 'public' | 'student' | 'admin' @var string $active @var string $title */
require_once __DIR__ . '/functions.php';
$context = $context ?? 'public';
$active = $active ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="PTI Student Result Management and Result Checking System">
  <title><?= e($title ?? 'PTI Result Checker') ?></title>
  <link rel="stylesheet" href="<?= e(base('css/style.css')) ?>">
  <script src="<?= e(base('js/script.js')) ?>" defer></script>
</head>
<body>
<header class="site-header">
  <a class="brand" href="<?= e(base($context === 'student' ? 'student/dashboard.php' : ($context === 'admin' ? 'admin/dashboard.php' : 'index.php'))) ?>">
    <img src="<?= e(base('assets/pti-logo.jpeg')) ?>" alt="PTI logo">
    <span><strong>PETROLEUM TRAINING INSTITUTE</strong><small>Skill • Service • Progress</small></span>
  </a>
  <button class="menu-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
  <nav class="main-nav">
    <?php if ($context === 'admin'): ?>
      <a class="<?= $active === 'dashboard' ? 'active' : '' ?>" href="<?= e(base('admin/dashboard.php')) ?>">Dashboard</a>
      <a class="<?= $active === 'students' ? 'active' : '' ?>" href="<?= e(base('admin/students.php')) ?>">Students</a>
      <a class="<?= $active === 'add' ? 'active' : '' ?>" href="<?= e(base('admin/add-student.php')) ?>">Add Student</a>
      <a class="<?= $active === 'results' ? 'active' : '' ?>" href="<?= e(base('admin/results.php')) ?>">Results</a>
      <a class="<?= $active === 'upload' ? 'active' : '' ?>" href="<?= e(base('admin/upload-result.php')) ?>">Upload Result</a>
      <a class="<?= $active === 'profile' ? 'active' : '' ?>" href="<?= e(base('admin/profile.php')) ?>">Profile</a>
      <a href="<?= e(base('admin/logout.php')) ?>">Logout</a>
    <?php elseif ($context === 'student'): ?>
      <a class="<?= $active === 'dashboard' ? 'active' : '' ?>" href="<?= e(base('student/dashboard.php')) ?>">Dashboard</a>
      <a class="<?= $active === 'profile' ? 'active' : '' ?>" href="<?= e(base('student/profile.php')) ?>">My Profile</a>
      <a class="<?= $active === 'results' ? 'active' : '' ?>" href="<?= e(base('student/results.php')) ?>">My Results</a>
      <a href="<?= e(base('logout.php')) ?>">Logout</a>
    <?php else: ?>
      <a class="<?= $active === 'home' ? 'active' : '' ?>" href="<?= e(base('index.php')) ?>">Home</a>
      <a class="<?= $active === 'check' ? 'active' : '' ?>" href="<?= e(base('check-result.php')) ?>">Check Result</a>
      <a class="<?= $active === 'about' ? 'active' : '' ?>" href="<?= e(base('about.php')) ?>">About PTI</a>
      <a class="<?= $active === 'contact' ? 'active' : '' ?>" href="<?= e(base('contact.php')) ?>">Contact</a>
      <a class="<?= $active === 'login' ? 'active' : '' ?>" href="<?= e(base('login.php')) ?>">Student Login</a>
    <?php endif; ?>
  </nav>
</header>

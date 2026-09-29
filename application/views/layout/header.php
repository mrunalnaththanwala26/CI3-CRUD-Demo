<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= html_escape($title) ?> | CI3 CRUD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <style>
    body { padding-top: 70px; background:#f7f8fa; }
    .avatar { width:44px; height:44px; object-fit:cover; border-radius:50%; background:#ddd; }
    .avatar-lg { width:140px; height:140px; }
    .card { background:#fff; padding:20px; border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,.08); }
    .hobby-box label { margin-right:15px; font-weight:normal; }
  </style>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <a class="navbar-brand" href="<?= site_url('users') ?>">CI3 CRUD</a>
    <ul class="nav navbar-nav">
      <li><a href="<?= site_url('users') ?>">Users</a></li>
      <li><a href="<?= site_url('users/create') ?>">Add User</a></li>
    </ul>
  </div>
</nav>
<div class="container">
  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= html_escape($this->session->flashdata('success')) ?></div>
  <?php endif; ?>
  <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= html_escape($this->session->flashdata('error')) ?></div>
  <?php endif; ?>

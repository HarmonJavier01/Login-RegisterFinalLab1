<?php
require_once 'auth.php';
require_role(ROLE_ADMIN);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Harmon - Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
        <li class="nav-item"><a class="nav-link" href="contacts.php">Contacts</a></li>
        <li class="nav-item"><a class="nav-link" href="logs.php">Logs</a></li>
        <li class="nav-item"><a class="nav-link" href="backup.php">Backup</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

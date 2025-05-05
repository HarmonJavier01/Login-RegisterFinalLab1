<?php
require_once 'auth.php';
require_role(ROLE_TEACHER);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Harmon - Teacher</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#teacherNavbar" aria-controls="teacherNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="teacherNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="contacts.php">My Contacts</a></li>
        <li class="nav-item"><a class="nav-link" href="assign.php">Assign Contacts</a></li>
        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

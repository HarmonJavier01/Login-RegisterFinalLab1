<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - RealEase</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar bg-dark text-white">
  <div class="sidebar-header text-center py-4">
    <img src="assets/img/logo.png" alt="RealEase Logo" class="img-fluid" width="120">
  </div>
  <ul class="nav flex-column px-3">
    <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-house-door me-2"></i> Home</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-people me-2"></i> User Management</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-calculator me-2"></i> Calculator</a></li>
    <li class="nav-item dropdown">
      <a class="nav-link text-white dropdown-toggle" href="#" id="productDropdown" data-bs-toggle="dropdown">
        <i class="bi bi-shop me-2"></i> Product of House
      </a>
      <ul class="dropdown-menu dropdown-menu-dark">
        <li><a class="dropdown-item" href="#">Buy</a></li>
        <li><a class="dropdown-item" href="#">Sell</a></li>
      </ul>
    </li>
  </ul>
</div>

<!-- Main Content -->
<div class="main">
  <!-- Topbar -->
  <nav class="navbar navbar-expand bg-light shadow-sm px-4">
    <div class="ms-auto dropdown">
      <a class="nav-link dropdown-toggle text-dark" href="#" data-bs-toggle="dropdown">
        <i class="bi bi-person-circle me-1"></i> Admin
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">Logout</a></li>
      </ul>
    </div>
  </nav>

  <!-- Dashboard Cards -->
  <div class="container-fluid p-4">
    <h3>Welcome Admin 👋</h3>
    <p class="text-muted">Manage all RealEase activities here.</p>

    <div class="row g-3">
      <div class="col-md-6">
        <div class="card shadow-sm custom-card-size">
          <div class="card-body">
            <h5>Total Users</h5>
            <p class="fs-4">1,254</p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm custom-card-size">
          <div class="card-body">
            <h5>Properties Listed</h5>
            <p class="fs-4">367</p>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="card shadow-sm custom-card-size">
          <div class="card-body">
            <h5>Pending Requests</h5>
            <p class="fs-4">24</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>

</html>



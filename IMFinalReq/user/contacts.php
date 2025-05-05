<?php
require_once '../../includes/auth.php';
redirectIfNotLoggedIn();
if (!isUser()) {
    header("Location: ../".$_SESSION['user_role']."/dashboard.php");
    exit();
}

// Handle contact actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_contact'])) {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        
        $stmt = $pdo->prepare("INSERT INTO contacts (user_id, name, email, phone, address) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $name, $email, $phone, $address])) {
            logActivity($_SESSION['user_id'], 'add_contact', "Added contact: $name", $_SERVER['REMOTE_ADDR']);
            $_SESSION['success'] = "Contact added successfully";
        }
    } elseif (isset($_POST['edit_contact'])) {
        $id = $_POST['contact_id'];
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        
        $stmt = $pdo->prepare("UPDATE contacts SET name = ?, email = ?, phone = ?, address = ? WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$name, $email, $phone, $address, $id, $_SESSION['user_id']])) {
            logActivity($_SESSION['user_id'], 'edit_contact', "Updated contact: $name", $_SERVER['REMOTE_ADDR']);
            $_SESSION['success'] = "Contact updated successfully";
        }
    } elseif (isset($_POST['bulk_action'])) {
        $action = $_POST['bulk_action'];
        $contact_ids = $_POST['contact_ids'] ?? [];
        
        if (!empty($contact_ids)) {
            $placeholders = implode(',', array_fill(0, count($contact_ids), '?'));
            
            if ($action === 'delete') {
                // Get contact names for logging before deletion
                $stmt = $pdo->prepare("SELECT name FROM contacts WHERE id IN ($placeholders) AND user_id = ?");
                $stmt->execute(array_merge($contact_ids, [$_SESSION['user_id']]));
                $contacts = $stmt->fetchAll();
                
                // Perform deletion
                $stmt = $pdo->prepare("DELETE FROM contacts WHERE id IN ($placeholders) AND user_id = ?");
                if ($stmt->execute(array_merge($contact_ids, [$_SESSION['user_id']]))) {
                    foreach ($contacts as $contact) {
                        logActivity($_SESSION['user_id'], 'delete_contact', "Deleted contact: ".$contact['name'], $_SERVER['REMOTE_ADDR']);
                    }
                    $_SESSION['success'] = count($contact_ids) . " contact(s) deleted successfully";
                }
            } elseif ($action === 'export') {
                $stmt = $pdo->prepare("SELECT name, email, phone, address FROM contacts WHERE id IN ($placeholders) AND user_id = ?");
                $stmt->execute(array_merge($contact_ids, [$_SESSION['user_id']]));
                $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($contacts)) {
                    $filename = "contacts_export_".date('Y-m-d').".csv";
                    exportToCSV($contacts, $filename);
                }
            }
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get contact name for logging before deletion
    $stmt = $pdo->prepare("SELECT name FROM contacts WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $contact = $stmt->fetch();
    
    if ($contact) {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$id, $_SESSION['user_id']])) {
            logActivity($_SESSION['user_id'], 'delete_contact', "Deleted contact: ".$contact['name'], $_SERVER['REMOTE_ADDR']);
            $_SESSION['success'] = "Contact deleted successfully";
        }
    }
    header("Location: contacts.php");
    exit();
} elseif (isset($_GET['export_all'])) {
    $stmt = $pdo->prepare("SELECT name, email, phone, address FROM contacts WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($contacts)) {
        $filename = "all_contacts_export_".date('Y-m-d').".csv";
        exportToCSV($contacts, $filename);
    }
}

// Pagination setup
$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// Get user's contacts with search and pagination
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$query = "SELECT SQL_CALC_FOUND_ROWS * FROM contacts WHERE user_id = ?";
$params = [$_SESSION['user_id']];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR address LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
}

$query .= " ORDER BY name LIMIT ? OFFSET ?";
$params = array_merge($params, [$per_page, $offset]);

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$contacts = $stmt->fetchAll();

// Get total count for pagination
$total_stmt = $pdo->query("SELECT FOUND_ROWS()");
$total_contacts = $total_stmt->fetchColumn();
$total_pages = ceil($total_contacts / $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Contacts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .contact-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-weight: bold;
        }
        .bulk-actions {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Contacts</h2>
            <div>
                <a href="contacts.php?export_all=1" class="btn btn-outline-success me-2">
                    <i class="bi bi-download"></i> Export All
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                    <i class="bi bi-plus"></i> Add Contact
                </button>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <form class="d-flex">
                            <input class="form-control me-2" type="search" name="search" placeholder="Search contacts..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-success" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (empty($contacts)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-person-lines-fill" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-3">No contacts found</h5>
                        <p>Add your first contact to get started</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                            <i class="bi bi-plus"></i> Add Contact
                        </button>
                    </div>
                <?php else: ?>
                    <form method="POST" id="bulkForm">
                        <div class="bulk-actions mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <select name="bulk_action" class="form-select" required>
                                        <option value="">Bulk Actions</option>
                                        <option value="delete">Delete Selected</option>
                                        <option value="export">Export Selected</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-check2"></i> Apply
                                    </button>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <small class="text-muted"><?= $total_contacts ?> contact(s) found</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th width="60">Photo</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contacts as $contact): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="contact_ids[]" value="<?= $contact['id'] ?>" class="form-check-input contact-checkbox">
                                            </td>
                                            <td>
                                                <div class="contact-photo">
                                                    <?= strtoupper(substr($contact['name'], 0, 1)) ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($contact['name']) ?></td>
                                            <td>
                                                <?php if (!empty($contact['email'])): ?>
                                                    <a href="mailto:<?= htmlspecialchars($contact['email']) ?>">
                                                        <?= htmlspecialchars($contact['email']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Not set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($contact['phone'])): ?>
                                                    <a href="tel:<?= htmlspecialchars($contact['phone']) ?>">
                                                        <?= htmlspecialchars($contact['phone']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Not set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewContactModal<?= $contact['id'] ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editContactModal<?= $contact['id'] ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="contacts.php?delete=<?= $contact['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this contact?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- View Contact Modal -->
                                        <div class="modal fade" id="viewContactModal<?= $contact['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Contact Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="text-center mb-4">
                                                            <div class="contact-photo mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                                                                <?= strtoupper(substr($contact['name'], 0, 1)) ?>
                                                            </div>
                                                            <h4 class="mt-3"><?= htmlspecialchars($contact['name']) ?></h4>
                                                        </div>
                                                        
                                                        <div class="row mb-3">
                                                            <div class="col-md-4 fw-bold">Email:</div>
                                                            <div class="col-md-8">
                                                                <?php if (!empty($contact['email'])): ?>
                                                                    <a href="mailto:<?= htmlspecialchars($contact['email']) ?>">
                                                                        <?= htmlspecialchars($contact['email']) ?>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="text-muted">Not set</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row mb-3">
                                                            <div class="col-md-4 fw-bold">Phone:</div>
                                                            <div class="col-md-8">
                                                                <?php if (!empty($contact['phone'])): ?>
                                                                    <a href="tel:<?= htmlspecialchars($contact['phone']) ?>">
                                                                        <?= htmlspecialchars($contact['phone']) ?>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="text-muted">Not set</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-4 fw-bold">Address:</div>
                                                            <div class="col-md-8">
                                                                <?php if (!empty($contact['address'])): ?>
                                                                    <?= nl2br(htmlspecialchars($contact['address'])) ?>
                                                                <?php else: ?>
                                                                    <span class="text-muted">Not set</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Edit Contact Modal -->
                                        <div class="modal fade" id="editContactModal<?= $contact['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Contact</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="contact_id" value="<?= $contact['id'] ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Name</label>
                                                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($contact['name']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($contact['email']) ?>">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Phone</label>
                                                                <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($contact['phone']) ?>">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Address</label>
                                                                <textarea class="form-control" name="address" rows="3"><?= htmlspecialchars($contact['address']) ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" name="edit_contact" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Contacts pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1&search=<?= urlencode($search) ?>" aria-label="First">
                                            <span aria-hidden="true">&laquo;&laquo;</span>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                $start = max(1, $page - 2);
                                $end = min($total_pages, $page + 2);
                                
                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page=1&search='.urlencode($search).'">1</a></li>';
                                    if ($start > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start; $i <= $end; $i++) {
                                    $active = $i == $page ? 'active' : '';
                                    echo '<li class="page-item '.$active.'"><a class="page-link" href="?page='.$i.'&search='.urlencode($search).'">'.$i.'</a></li>';
                                }
                                
                                if ($end < $total_pages) {
                                    if ($end < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?page='.$total_pages.'&search='.urlencode($search).'">'.$total_pages.'</a></li>';
                                }
                                ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>" aria-label="Last">
                                            <span aria-hidden="true">&raquo;&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Add Contact Modal -->
    <div class="modal fade" id="addContactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_contact" class="btn btn-primary">Add Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select all checkboxes functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.contact-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Bulk form validation
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const action = this.elements['bulk_action'].value;
            const checked = document.querySelectorAll('.contact-checkbox:checked').length > 0;
            
            if (!action) {
                e.preventDefault();
                alert('Please select a bulk action');
                return false;
            }
            
            if (!checked) {
                e.preventDefault();
                alert('Please select at least one contact');
                return false;
            }
            
            if (action === 'delete' && !confirm('Are you sure you want to delete the selected contacts?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_role(ROLE_ADMIN);

// Fetch all contacts with user and teacher info
$stmt = $pdo->query("
    SELECT c.name AS contact_name, c.email, c.phone, c.address,
           u.name AS user_name, t.name AS teacher_name, c.created_at
    FROM contacts c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN users t ON c.teacher_id = t.id
    ORDER BY c.created_at DESC
");
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=contacts_backup_' . date('Y-m-d') . '.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output column headers
fputcsv($output, ['Contact Name', 'Email', 'Phone', 'Address', 'User', 'Teacher', 'Created At']);

// Output data rows
foreach ($contacts as $contact) {
    fputcsv($output, [
        $contact['contact_name'],
        $contact['email'],
        $contact['phone'],
        $contact['address'],
        $contact['user_name'],
        $contact['teacher_name'],
        $contact['created_at']
    ]);
}

fclose($output);
exit;
?>

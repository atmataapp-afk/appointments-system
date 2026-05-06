<?php
include 'db.php';
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}
header("Location: view_appointments.php");
exit;
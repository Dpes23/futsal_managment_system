<?php
require_once 'config/database.php';
try {
    $pdo = getDatabaseConnection();
    echo "Database connection successful";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
}
?>

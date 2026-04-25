<?php
session_start();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

// Get latitude and longitude from POST data
$lat = $_POST['lat'] ?? null;
$lon = $_POST['lon'] ?? null;

// Validate coordinates
if ($lat !== null && $lon !== null) {
    $_SESSION['user_lat'] = (float)$lat;
    $_SESSION['user_lon'] = (float)$lon;
    $_SESSION['location_updated'] = true; // Flag to prevent reload loop
    
    echo json_encode(['success' => true, 'lat' => $lat, 'lon' => $lon]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid coordinates']);
}
?>

<?php
session_start();
require '../include/db.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Not logged in'
    ]);
    exit;
}

if (!isset($_FILES['image'])) {
    echo json_encode([
        'success' => false,
        'error' => 'No file uploaded'
    ]);
    exit;
}

$file = $_FILES['image'];

$allowed = ['image/jpeg', 'image/png', 'image/webp'];

if (!in_array($file['type'], $allowed)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid file type'
    ]);
    exit;
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'story_' . time() . '.' . $ext;

$path = '../uploads/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $path)) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to save file'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'path' => 'uploads/' . $filename
]);

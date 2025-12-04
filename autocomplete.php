<?php
require_once 'config.php';
$conn = getDBConnection();

header('Content-Type: application/json');

// Get search term from GET parameter
$term = isset($_GET['term']) ? trim($_GET['term']) : '';

// Return empty array if search term is too short
if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

// Search for recipes matching the term (SQL Injection protection)
$stmt = $conn->prepare("SELECT id, name, category FROM recipes WHERE name LIKE ? LIMIT 10");
$search_term = "%" . $term . "%";
$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'category' => $row['category']
    ];
}

echo json_encode($suggestions);

$stmt->close();
$conn->close();
?>
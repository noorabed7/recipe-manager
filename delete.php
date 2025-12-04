<?php
require_once 'config.php';
$conn = getDBConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$recipe_id = intval($_GET['id']);

// Verify recipe exists
$stmt = $conn->prepare("SELECT name FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$recipe = $result->fetch_assoc();
$stmt->close();

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("Invalid security token.");
    }
    
    $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->bind_param("i", $recipe_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: index.php?deleted=1");
        exit;
    } else {
        $error = "Error deleting recipe.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Recipe - Recipe Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🗑️ Delete Recipe</h1>
            <nav>
                <a href="view.php?id=<?= $recipe_id ?>" class="btn">Cancel</a>
            </nav>
        </header>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <div class="delete-confirmation">
            <h2>Are you sure you want to delete this recipe?</h2>
            <p class="recipe-name"><?= h($recipe['name']) ?></p>
            <p class="warning">⚠️ This action cannot be undone.</p>
            
            <form method="POST" class="delete-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <button type="submit" class="btn btn-danger">Yes, Delete Recipe</button>
                <a href="view.php?id=<?= $recipe_id ?>" class="btn">No, Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
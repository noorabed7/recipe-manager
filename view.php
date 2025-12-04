<?php
require_once 'config.php';
$conn = getDBConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$recipe_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$recipe = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($recipe['name']) ?> - Recipe Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🍳 Recipe Details</h1>
            <nav>
                <a href="index.php" class="btn">Back to Home</a>
                <a href="edit.php?id=<?= $recipe_id ?>" class="btn btn-primary">Edit</a>
                <a href="delete.php?id=<?= $recipe_id ?>" class="btn btn-danger" 
                   onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</a>
            </nav>
        </header>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Recipe updated successfully!</div>
        <?php endif; ?>

        <div class="recipe-detail">
            <div class="recipe-detail-header">
                <h2><?= h($recipe['name']) ?></h2>
                <span class="category-badge"><?= h($recipe['category']) ?></span>
            </div>

            <div class="recipe-meta">
                <div class="meta-item">
                    <strong>⏱️ Prep Time:</strong> <?= h($recipe['prep_time']) ?> minutes
                </div>
                <div class="meta-item">
                    <strong>🔥 Cook Time:</strong> <?= h($recipe['cook_time']) ?> minutes
                </div>
                <div class="meta-item">
                    <strong>⏰ Total Time:</strong> <?= h($recipe['prep_time'] + $recipe['cook_time']) ?> minutes
                </div>
                <div class="meta-item">
                    <strong>👥 Servings:</strong> <?= h($recipe['servings']) ?>
                </div>
                <div class="meta-item">
                    <strong>📊 Difficulty:</strong> 
                    <span class="difficulty-<?= strtolower($recipe['difficulty']) ?>">
                        <?= h($recipe['difficulty']) ?>
                    </span>
                </div>
            </div>

            <div class="recipe-section">
                <h3>🥘 Ingredients</h3>
                <div class="ingredients-list">
                    <?php
                    $ingredients = explode("\n", $recipe['ingredients']);
                    foreach ($ingredients as $ingredient) {
                        $ingredient = trim($ingredient);
                        if (!empty($ingredient)) {
                            echo "<div class='ingredient-item'>• " . h($ingredient) . "</div>";
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="recipe-section">
                <h3>📝 Instructions</h3>
                <div class="instructions-list">
                    <?php
                    $instructions = explode("\n", $recipe['instructions']);
                    $step = 1;
                    foreach ($instructions as $instruction) {
                        $instruction = trim($instruction);
                        if (!empty($instruction)) {
                            // Remove numbering if already present
                            $instruction = preg_replace('/^\d+\.\s*/', '', $instruction);
                            echo "<div class='instruction-step'>";
                            echo "<span class='step-number'>" . $step . "</span>";
                            echo "<span class='step-text'>" . h($instruction) . "</span>";
                            echo "</div>";
                            $step++;
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="recipe-footer">
                <small>Created: <?= date('F j, Y, g:i a', strtotime($recipe['created_at'])) ?></small>
                <?php if ($recipe['updated_at'] !== $recipe['created_at']): ?>
                    <small>Last updated: <?= date('F j, Y, g:i a', strtotime($recipe['updated_at'])) ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
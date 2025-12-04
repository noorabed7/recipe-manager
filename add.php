<?php
require_once 'config.php';
$conn = getDBConnection();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = "Invalid security token.";
    } else {
        // Validate and sanitize input
        $name = trim($_POST['name']);
        $category = trim($_POST['category']);
        $prep_time = intval($_POST['prep_time']);
        $cook_time = intval($_POST['cook_time']);
        $servings = intval($_POST['servings']);
        $difficulty = $_POST['difficulty'];
        $ingredients = trim($_POST['ingredients']);
        $instructions = trim($_POST['instructions']);
        
        // Validation
        if (empty($name)) $errors[] = "Recipe name is required.";
        if (empty($category)) $errors[] = "Category is required.";
        if ($prep_time < 0) $errors[] = "Prep time must be positive.";
        if ($cook_time < 0) $errors[] = "Cook time must be positive.";
        if ($servings < 1) $errors[] = "Servings must be at least 1.";
        if (!in_array($difficulty, ['Easy', 'Medium', 'Hard'])) $errors[] = "Invalid difficulty.";
        if (empty($ingredients)) $errors[] = "Ingredients are required.";
        if (empty($instructions)) $errors[] = "Instructions are required.";
        
        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO recipes (name, category, prep_time, cook_time, servings, difficulty, ingredients, instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiissss", $name, $category, $prep_time, $cook_time, $servings, $difficulty, $ingredients, $instructions);
            
            if ($stmt->execute()) {
                $success = true;
                header("Location: index.php?added=1");
                exit;
            } else {
                $errors[] = "Error adding recipe: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe - Recipe Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🍳 Add New Recipe</h1>
            <nav>
                <a href="index.php" class="btn">Back to Home</a>
            </nav>
        </header>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= h($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="add.php" class="recipe-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="name">Recipe Name *</label>
                <input type="text" id="name" name="name" required 
                       value="<?= isset($_POST['name']) ? h($_POST['name']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Category *</label>
                <input type="text" id="category" name="category" required 
                       placeholder="e.g., Italian, Asian, Dessert"
                       value="<?= isset($_POST['category']) ? h($_POST['category']) : '' ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="prep_time">Prep Time (minutes) *</label>
                    <input type="number" id="prep_time" name="prep_time" required min="0" 
                           value="<?= isset($_POST['prep_time']) ? h($_POST['prep_time']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="cook_time">Cook Time (minutes) *</label>
                    <input type="number" id="cook_time" name="cook_time" required min="0" 
                           value="<?= isset($_POST['cook_time']) ? h($_POST['cook_time']) : '' ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="servings">Servings *</label>
                    <input type="number" id="servings" name="servings" required min="1" 
                           value="<?= isset($_POST['servings']) ? h($_POST['servings']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="difficulty">Difficulty *</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="Easy" <?= isset($_POST['difficulty']) && $_POST['difficulty'] === 'Easy' ? 'selected' : '' ?>>Easy</option>
                        <option value="Medium" <?= isset($_POST['difficulty']) && $_POST['difficulty'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="Hard" <?= isset($_POST['difficulty']) && $_POST['difficulty'] === 'Hard' ? 'selected' : '' ?>>Hard</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="ingredients">Ingredients * (one per line)</label>
                <textarea id="ingredients" name="ingredients" required rows="8" 
                          placeholder="e.g.,&#10;200g flour&#10;2 eggs&#10;100ml milk"><?= isset($_POST['ingredients']) ? h($_POST['ingredients']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="instructions">Instructions * (one step per line)</label>
                <textarea id="instructions" name="instructions" required rows="10" 
                          placeholder="e.g.,&#10;1. Preheat oven to 180°C&#10;2. Mix all ingredients&#10;3. Bake for 30 minutes"><?= isset($_POST['instructions']) ? h($_POST['instructions']) : '' ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Recipe</button>
                <a href="index.php" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
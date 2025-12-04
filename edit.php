<?php
require_once 'config.php';
$conn = getDBConnection();

$errors = [];
$recipe = null;

// Get recipe ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$recipe_id = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = "Invalid security token.";
    } else {
        $name = trim($_POST['name']);
        $category = trim($_POST['category']);
        $prep_time = intval($_POST['prep_time']);
        $cook_time = intval($_POST['cook_time']);
        $servings = intval($_POST['servings']);
        $difficulty = $_POST['difficulty'];
        $ingredients = trim($_POST['ingredients']);
        $instructions = trim($_POST['instructions']);
        
        if (empty($name)) $errors[] = "Recipe name is required.";
        if (empty($category)) $errors[] = "Category is required.";
        if ($prep_time < 0) $errors[] = "Prep time must be positive.";
        if ($cook_time < 0) $errors[] = "Cook time must be positive.";
        if ($servings < 1) $errors[] = "Servings must be at least 1.";
        if (!in_array($difficulty, ['Easy', 'Medium', 'Hard'])) $errors[] = "Invalid difficulty.";
        if (empty($ingredients)) $errors[] = "Ingredients are required.";
        if (empty($instructions)) $errors[] = "Instructions are required.";
        
        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE recipes SET name=?, category=?, prep_time=?, cook_time=?, servings=?, difficulty=?, ingredients=?, instructions=? WHERE id=?");
            $stmt->bind_param("ssiissssi", $name, $category, $prep_time, $cook_time, $servings, $difficulty, $ingredients, $instructions, $recipe_id);
            
            if ($stmt->execute()) {
                header("Location: view.php?id=" . $recipe_id . "&updated=1");
                exit;
            } else {
                $errors[] = "Error updating recipe.";
            }
            $stmt->close();
        }
    }
}

// Fetch recipe data
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
    <title>Edit Recipe - Recipe Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>✏️ Edit Recipe</h1>
            <nav>
                <a href="view.php?id=<?= $recipe_id ?>" class="btn">Cancel</a>
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

        <form method="POST" class="recipe-form">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="name">Recipe Name *</label>
                <input type="text" id="name" name="name" required 
                       value="<?= h(isset($_POST['name']) ? $_POST['name'] : $recipe['name']) ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Category *</label>
                <input type="text" id="category" name="category" required 
                       value="<?= h(isset($_POST['category']) ? $_POST['category'] : $recipe['category']) ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="prep_time">Prep Time (minutes) *</label>
                    <input type="number" id="prep_time" name="prep_time" required min="0" 
                           value="<?= h(isset($_POST['prep_time']) ? $_POST['prep_time'] : $recipe['prep_time']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="cook_time">Cook Time (minutes) *</label>
                    <input type="number" id="cook_time" name="cook_time" required min="0" 
                           value="<?= h(isset($_POST['cook_time']) ? $_POST['cook_time'] : $recipe['cook_time']) ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="servings">Servings *</label>
                    <input type="number" id="servings" name="servings" required min="1" 
                           value="<?= h(isset($_POST['servings']) ? $_POST['servings'] : $recipe['servings']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="difficulty">Difficulty *</label>
                    <select id="difficulty" name="difficulty" required>
                        <?php 
                        $current_difficulty = isset($_POST['difficulty']) ? $_POST['difficulty'] : $recipe['difficulty'];
                        ?>
                        <option value="Easy" <?= $current_difficulty === 'Easy' ? 'selected' : '' ?>>Easy</option>
                        <option value="Medium" <?= $current_difficulty === 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="Hard" <?= $current_difficulty === 'Hard' ? 'selected' : '' ?>>Hard</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="ingredients">Ingredients *</label>
                <textarea id="ingredients" name="ingredients" required rows="8"><?= h(isset($_POST['ingredients']) ? $_POST['ingredients'] : $recipe['ingredients']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="instructions">Instructions *</label>
                <textarea id="instructions" name="instructions" required rows="10"><?= h(isset($_POST['instructions']) ? $_POST['instructions'] : $recipe['instructions']) ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Recipe</button>
                <a href="view.php?id=<?= $recipe_id ?>" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
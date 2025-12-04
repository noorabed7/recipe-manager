<?php
require_once 'config.php';
$conn = getDBConnection();

// Get search parameters
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$search_category = isset($_GET['search_category']) ? trim($_GET['search_category']) : '';
$search_time = isset($_GET['search_time']) ? intval($_GET['search_time']) : 0;

// Build query with prepared statements (SQL Injection protection)
$sql = "SELECT * FROM recipes WHERE 1=1";
$params = [];
$types = "";

if (!empty($search_name)) {
    $sql .= " AND name LIKE ?";
    $params[] = "%" . $search_name . "%";
    $types .= "s";
}

if (!empty($search_category)) {
    $sql .= " AND category = ?";
    $params[] = $search_category;
    $types .= "s";
}

if ($search_time > 0) {
    $sql .= " AND (prep_time + cook_time) <= ?";
    $params[] = $search_time;
    $types .= "i";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get unique categories for filter
$categories_result = $conn->query("SELECT DISTINCT category FROM recipes ORDER BY category");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Manager</title>
    <link rel="stylesheet" href="style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="https://www.fifteenspatulas.com/wp-content/themes/fifteenspatulas2020/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://www.fifteenspatulas.com/wp-content/themes/fifteenspatulas2020/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://www.fifteenspatulas.com/wp-content/themes/fifteenspatulas2020/favicon/favicon-16x16.png">
</head>
<body>
    <div class="container">
        <header>
            <h1>🍳 Recipe Manager</h1>
            <nav>
                <a href="index.php" class="btn">Home</a>
                <a href="add.php" class="btn btn-primary">Add Recipe</a>
            </nav>
        </header>

        <!-- Search Form -->
        <div class="search-box">
            <h2>Search Recipes</h2>
            <form method="GET" action="index.php" class="search-form">
                <div class="form-group">
                    <label>Recipe Name:</label>
                    <input type="text" name="search_name" id="search_name" 
                           value="<?= h($search_name) ?>" 
                           placeholder="Start typing..." autocomplete="off">
                    <div id="autocomplete-results"></div>
                </div>
                
                <div class="form-group">
                    <label>Category:</label>
                    <select name="search_category">
                        <option value="">All Categories</option>
                        <?php while ($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?= h($cat['category']) ?>" 
                                <?= $search_category === $cat['category'] ? 'selected' : '' ?>>
                                <?= h($cat['category']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Max Total Time (minutes):</label>
                    <input type="number" name="search_time" value="<?= $search_time > 0 ? $search_time : '' ?>" 
                           placeholder="e.g., 30">
                </div>
                
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="index.php" class="btn">Clear</a>
            </form>
        </div>

        <!-- Recipe Grid -->
        <div class="recipes-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($recipe = $result->fetch_assoc()): ?>
                    <div class="recipe-card">
                        <div class="recipe-header">
                            <h3><?= h($recipe['name']) ?></h3>
                            <span class="category-badge"><?= h($recipe['category']) ?></span>
                        </div>
                        <div class="recipe-info">
                            <span>⏱️ Prep: <?= h($recipe['prep_time']) ?>m</span>
                            <span>🔥 Cook: <?= h($recipe['cook_time']) ?>m</span>
                            <span>👥 Serves: <?= h($recipe['servings']) ?></span>
                            <span class="difficulty-<?= strtolower($recipe['difficulty']) ?>">
                                <?= h($recipe['difficulty']) ?>
                            </span>
                        </div>
                        <div class="recipe-actions">
                            <a href="view.php?id=<?= $recipe['id'] ?>" class="btn btn-small">View</a>
                            <a href="edit.php?id=<?= $recipe['id'] ?>" class="btn btn-small">Edit</a>
                            <a href="delete.php?id=<?= $recipe['id'] ?>" 
                               class="btn btn-small btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-results">No recipes found. <a href="add.php">Add your first recipe!</a></p>
            <?php endif; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
-- Database: recipe_manager
-- Create this database first, then run these commands

CREATE DATABASE IF NOT EXISTS recipe_manager;
USE recipe_manager;

-- Recipes table
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    prep_time INT NOT NULL COMMENT 'in minutes',
    cook_time INT NOT NULL COMMENT 'in minutes',
    servings INT NOT NULL,
    difficulty ENUM('Easy', 'Medium', 'Hard') DEFAULT 'Medium',
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO recipes (name, category, prep_time, cook_time, servings, difficulty, ingredients, instructions) VALUES
('Spaghetti Carbonara', 'Italian', 10, 15, 4, 'Easy', 
'400g spaghetti\n200g pancetta\n4 eggs\n100g parmesan cheese\nSalt and pepper',
'1. Cook spaghetti according to package\n2. Fry pancetta until crispy\n3. Mix eggs and cheese\n4. Combine all ingredients\n5. Serve hot'),

('Chicken Stir Fry', 'Asian', 15, 10, 4, 'Easy',
'500g chicken breast\n2 bell peppers\n1 onion\nSoy sauce\nGinger and garlic',
'1. Cut chicken into strips\n2. Chop vegetables\n3. Stir fry chicken\n4. Add vegetables\n5. Season with soy sauce'),

('Chocolate Cake', 'Dessert', 20, 45, 8, 'Medium',
'200g flour\n200g sugar\n100g cocoa powder\n3 eggs\n200ml milk\n100g butter',
'1. Preheat oven to 180°C\n2. Mix dry ingredients\n3. Add wet ingredients\n4. Bake for 45 minutes\n5. Let cool before serving'),

('Greek Salad', 'Salad', 15, 0, 4, 'Easy',
'2 tomatoes\n1 cucumber\n1 red onion\n200g feta cheese\nOlives\nOlive oil',
'1. Chop all vegetables\n2. Crumble feta cheese\n3. Add olives\n4. Drizzle with olive oil\n5. Season and serve'),

('Beef Tacos', 'Mexican', 15, 20, 4, 'Easy',
'500g ground beef\nTaco shells\nLettuce\nTomatoes\nCheese\nSour cream',
'1. Cook ground beef\n2. Warm taco shells\n3. Prepare toppings\n4. Assemble tacos\n5. Serve immediately');
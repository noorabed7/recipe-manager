# Recipe Manager - University Assignment

A full-stack web application for managing recipes with CRUD operations, advanced search, and Ajax autocomplete.

## 🎯 Assignment Requirements Met

### Core Functionality (All Completed)
- ✅ **Create**: Add new recipes with validation
- ✅ **Read**: View all recipes and individual recipe details
- ✅ **Update**: Edit existing recipes
- ✅ **Delete**: Remove recipes with confirmation
- ✅ **Multi-criteria Search**: Search by name, category, and total cooking time
- ✅ **Ajax Autocomplete**: Real-time search suggestions as you type

### Security Features Implemented
- ✅ **SQL Injection Protection**: All queries use prepared statements with parameter binding
- ✅ **XSS Protection**: All output is escaped using `htmlspecialchars()`
- ✅ **CSRF Protection**: Token-based protection for all forms
- ✅ **Session Security**: HTTP-only cookies, secure session configuration
- ✅ **Input Validation**: Server-side validation for all user inputs

### Technology Stack
- PHP (server-side logic)
- MySQL (database)
- JavaScript (Ajax functionality)
- HTML5 & CSS3 (responsive design)

## 📁 Project Structure

```
recipe-manager/
├── config.php              # Database config & security functions
├── index.php               # Home page with recipe grid & search
├── add.php                 # Add new recipe
├── edit.php                # Edit existing recipe
├── view.php                # View recipe details
├── delete.php              # Delete recipe confirmation
├── autocomplete.php        # Ajax autocomplete endpoint
├── script.js               # JavaScript for autocomplete
├── style.css               # Stylesheet
└── database.sql            # Database schema and sample data
```

## 🚀 Installation Steps

### 1. Upload Files to Student Server

Upload all files to your student server directory (e.g., `public_html/recipe-manager/`)

### 2. Create Database

1. Log into phpMyAdmin on your student server
2. Click "New" to create a database named `recipe_manager`
3. Select the database
4. Click "Import" and upload the `database.sql` file
5. OR click "SQL" tab and paste the SQL commands from the file

### 3. Configure Database Connection

Edit `config.php` and update these lines:

```php
define('DB_HOST', 'localhost');          // Usually 'localhost'
define('DB_USER', 'your_username');      // Your database username
define('DB_PASS', 'your_password');      // Your database password
define('DB_NAME', 'recipe_manager');     // Database name
```

### 4. Test the Application

Visit the website: `https://mi-linux.wlv.ac.uk/~2528160/recipe-manager/index.php`

## 🧪 Testing Checklist

### Basic CRUD Operations
- [ ] Add a new recipe
- [ ] View recipe details
- [ ] Edit a recipe
- [ ] Delete a recipe

### Search Functionality
- [ ] Search by recipe name
- [ ] Filter by category
- [ ] Filter by maximum cooking time
- [ ] Combine multiple search criteria (e.g., "Italian" + "30 minutes max")

### Ajax Autocomplete
- [ ] Type in the search box (at least 2 characters)
- [ ] Verify suggestions appear in real-time
- [ ] Click a suggestion to populate the search field

### Security Testing
- [ ] Try SQL injection (e.g., `' OR '1'='1`)
- [ ] Try XSS (e.g., `<script>alert('XSS')</script>`)
- [ ] Submit forms without CSRF token (should fail)

## 🔧 Common Issues & Solutions

### Database Connection Failed
- Check `config.php` credentials
- Verify database exists in phpMyAdmin
- Ensure MySQL service is running

### Autocomplete Not Working
- Check browser console for JavaScript errors
- Verify `autocomplete.php` is accessible
- Check file permissions (644 for PHP files)

### Forms Not Submitting
- Check CSRF token is being generated
- Verify session is started
- Check PHP error logs

## 🎓 Features Summary

| Feature | Implementation | Security |
|---------|---------------|----------|
| Create Recipe | `add.php` with validation | CSRF token, input sanitization |
| Read Recipes | `index.php`, `view.php` | XSS protection on output |
| Update Recipe | `edit.php` | CSRF token, prepared statements |
| Delete Recipe | `delete.php` with confirmation | CSRF token, prepared statements |
| Search (1 criteria) | Name search | SQL injection protection |
| Search (multiple) | Name + Category + Time | Parameterized queries |
| Ajax Autocomplete | Real-time suggestions | JSON encoding, XSS protection |

## 📞 Support

If you encounter issues during setup:
1. Check PHP error logs on your server
2. Use browser DevTools Console for JavaScript errors
3. Verify database connection in phpMyAdmin
4. Ensure all file permissions are correct (644 for files, 755 for directories)

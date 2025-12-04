// Ajax autocomplete functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search_name');
    const resultsDiv = document.getElementById('autocomplete-results');
    
    if (!searchInput || !resultsDiv) return;
    
    let timeout = null;
    
    searchInput.addEventListener('input', function() {
        const term = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(timeout);
        
        // Hide results if search term is too short
        if (term.length < 2) {
            resultsDiv.innerHTML = '';
            resultsDiv.style.display = 'none';
            return;
        }
        
        // Debounce - wait 300ms after user stops typing
        timeout = setTimeout(function() {
            fetchSuggestions(term);
        }, 300);
    });
    
    function fetchSuggestions(term) {
        // Make Ajax request
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'autocomplete.php?term=' + encodeURIComponent(term), true);
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const suggestions = JSON.parse(xhr.responseText);
                    displaySuggestions(suggestions);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                }
            }
        };
        
        xhr.onerror = function() {
            console.error('Request failed');
        };
        
        xhr.send();
    }
    
    function displaySuggestions(suggestions) {
        if (suggestions.length === 0) {
            resultsDiv.innerHTML = '<div class="autocomplete-item no-results">No recipes found</div>';
            resultsDiv.style.display = 'block';
            return;
        }
        
        let html = '';
        suggestions.forEach(function(suggestion) {
            html += '<div class="autocomplete-item" data-name="' + escapeHtml(suggestion.name) + '">';
            html += '<strong>' + escapeHtml(suggestion.name) + '</strong>';
            html += '<span class="autocomplete-category">' + escapeHtml(suggestion.category) + '</span>';
            html += '</div>';
        });
        
        resultsDiv.innerHTML = html;
        resultsDiv.style.display = 'block';
        
        // Add click handlers
        const items = resultsDiv.querySelectorAll('.autocomplete-item');
        items.forEach(function(item) {
            item.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                searchInput.value = name;
                resultsDiv.style.display = 'none';
            });
        });
    }
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target !== searchInput && e.target !== resultsDiv) {
            resultsDiv.style.display = 'none';
        }
    });
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
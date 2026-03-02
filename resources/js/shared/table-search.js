document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector("#search");
    const table = document.querySelector(".data-table");
    if (!table || !searchInput) return;

    const rows = table.querySelectorAll(".data-row");

    searchInput.addEventListener("keyup", () => {
        const query = searchInput.value.toLowerCase();
        
        rows.forEach(row => {
            // Try to get searchable text from data attributes first
            let searchableText = Object.values(row.dataset).join(" ");
            
            // If no data attributes, use cell text content
            if (!searchableText.trim()) {
                searchableText = row.textContent;
            }
            
            searchableText = searchableText.toLowerCase();
            
            // Show/hide row based on whether query matches
            row.style.display = searchableText.includes(query) ? "" : "none";
        });
    });
});

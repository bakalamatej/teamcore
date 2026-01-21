document.addEventListener("DOMContentLoaded", () => {
    const search = document.querySelector("#search");
    const table = document.querySelector(".data-table");
    if (!table || !search) return;

    const rows = table.querySelectorAll(".data-row");

    search.addEventListener("keyup", () => {
        const q = search.value.toLowerCase();
        
        rows.forEach(row => {
            const title = row.dataset.title || '';
            const location = row.dataset.location || '';            
            row.style.display = title.includes(q) || location.includes(q) ? "" : "none";
        });
    });
});
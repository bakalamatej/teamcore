document.addEventListener("DOMContentLoaded", () => {
    const search = document.querySelector("#search");
    const table = document.querySelector(".data-table");
    if (!table || !search) return;

    const rows = table.querySelectorAll(".data-row");

    search.addEventListener("keyup", () => {
        const q = search.value.toLowerCase();
        
        rows.forEach(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';            
            row.style.display = name.includes(q) || email.includes(q) ? "" : "none";
        });
    });
});
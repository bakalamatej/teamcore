document.addEventListener("DOMContentLoaded", () => {
    const search = document.querySelector("#search");
    const table = document.querySelector(".clubs-table");
    if (!table) return;

    const rows = table.querySelectorAll(".club-row");

    if (search) {
        search.addEventListener("keyup", () => {
            const q = search.value.toLowerCase();
            rows.forEach(row => {
                const name = row.dataset.name;
                const location = row.dataset.city;
                row.style.display = (name.includes(q) || location.includes(q)) ? "" : "none";
            });
        });
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const search = document.querySelector("#search");
    const table = document.querySelector(".event-table");
    if (!table) return;

    const rows = table.querySelectorAll(".event-row");

    if (search) {
        search.addEventListener("keyup", () => {
            const q = search.value.toLowerCase();
            rows.forEach(row => {
                const title = row.dataset.title;
                const location = row.dataset.location;
                row.style.display = (title.includes(q) || location.includes(q)) ? "" : "none";
            });
        });
    }
});
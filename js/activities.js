export class ActivityFilter {
    constructor() {
        this.form = document.querySelector(".controls-form");
        this.distanceSelect = document.getElementById("distanceSelect");
        this.searchInput = document.getElementById("searchInput");
        this.sortSelect = document.getElementById("sortSelect");
        
        this.init();
    }

    init() {
        if (this.distanceSelect) {
            this.distanceSelect.addEventListener("change", () => this.handleDistanceChange());
        }
        
        if (this.searchInput) {
            this.searchInput.addEventListener("input", () => this.handleSearch());
        }
        
        if (this.sortSelect) {
            this.sortSelect.addEventListener("change", () => this.handleSort());
        }
    }

    handleDistanceChange() {
        if (this.form) {
            this.form.submit();
        }
    }

    handleSearch() {
        // Optional: Add real-time search without page reload
        console.log("Search input changed:", this.searchInput.value);
    }

    handleSort() {
        if (this.form) {
            this.form.submit();
        }
    }
}

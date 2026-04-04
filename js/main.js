import Forms from "./forms.js";
import { ActivityFilter } from "./activities.js";

// laad pas als de pagina volledig is geladen
document.addEventListener("DOMContentLoaded", () => {
    new Forms();
    new ActivityFilter();
});
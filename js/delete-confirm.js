/**
 * Delete Confirmation - Verzoekt bevestiging voordat verwijdering
 */
class DeleteConfirmation {
    constructor() {
        this.setupDeleteButtons();
    }

    setupDeleteButtons() {
        const deleteButtons = document.querySelectorAll('.admin-delete-btn, .mobile-delete-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('Weet je zeker dat je deze vacature wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.')) {
                    e.preventDefault();
                }
            });
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new DeleteConfirmation();
});

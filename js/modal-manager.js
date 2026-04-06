/**
 * Modal Manager - Beheert alle modals in de activiteiten pagina
 */
class ModalManager {
    constructor() {
        this.setupModals();
    }

    setupModals() {
        // Modal elements
        this.modal = document.getElementById('add-activity-modal');
        this.addBtn = document.getElementById('add-activity-btn');
        this.closeBtn = document.querySelector('.close');
        this.cancelBtn = document.querySelector('.btn-cancel');
        this.form = document.getElementById('add-activity-form');

        // Edit modal
        this.editModal = document.getElementById('edit-activity-modal');
        this.editCloseBtn = document.querySelector('.edit-close');
        this.editCancelBtn = document.querySelector('.btn-edit-cancel');
        this.editForm = document.getElementById('edit-activity-form');

        // Detail modal
        this.detailModal = document.getElementById('detail-activity-modal');
        this.detailCloseBtn = document.querySelector('.detail-close');
        this.detailContent = document.getElementById('detail-content');

        this.attachEventListeners();
    }

    attachEventListeners() {
        // Add modal
        if (this.addBtn) {
            this.addBtn.onclick = () => this.openAddModal();
        }

        if (this.closeBtn) this.closeBtn.onclick = () => this.closeAddModal();
        if (this.cancelBtn) this.cancelBtn.onclick = () => this.closeAddModal();

        if (this.editCloseBtn) this.editCloseBtn.onclick = () => this.closeEditModal();
        if (this.editCancelBtn) this.editCancelBtn.onclick = () => this.closeEditModal();

        if (this.detailCloseBtn) this.detailCloseBtn.onclick = () => this.closeDetailModal();

        // Form submissions
        if (this.form) this.form.onsubmit = (e) => this.handleAddSubmit(e);
        if (this.editForm) this.editForm.onsubmit = (e) => this.handleEditSubmit(e);

        // Click outside modal
        window.onclick = (event) => this.handleOutsideClick(event);
    }

    openAddModal() {
        this.modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    closeAddModal() {
        this.modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        if (this.form) this.form.reset();
    }

    closeEditModal() {
        this.editModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        if (this.editForm) this.editForm.reset();
    }

    closeDetailModal() {
        this.detailModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        this.detailContent.innerHTML = '';
    }

    handleOutsideClick(event) {
        if (event.target === this.modal) this.closeAddModal();
        if (event.target === this.editModal) this.closeEditModal();
        if (event.target === this.detailModal) this.closeDetailModal();
    }

    handleAddSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);
        formData.append('user_id', 1); // TODO: Get from session

        fetch('classes/add_activiteit.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Activiteit succesvol toegevoegd!');
                    this.closeAddModal();
                    location.reload();
                } else {
                    alert('Fout bij toevoegen: ' + (data.error || 'Onbekende fout'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Er is een fout opgetreden.');
            });
    }

    handleEditSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.editForm);

        fetch('classes/update_activiteit.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Activiteit succesvol bijgewerkt!');
                    this.closeEditModal();
                    location.reload();
                } else {
                    alert('Fout bij bijwerken: ' + (data.error || 'Onbekende fout'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Er is een fout opgetreden.');
            });
    }
}

/**
 * Global functions for editing/deleting activities
 */
window.editActiviteit = function(id) {
    fetch('classes/get_activiteit.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const activiteit = data.data;

                document.getElementById('edit-id').value = activiteit.activiteit_id;
                document.getElementById('edit-titel').value = activiteit.activiteit_titel;
                document.getElementById('edit-beschrijving').value = activiteit.activiteit_beschrijving;
                document.getElementById('edit-datum').value = activiteit.activiteit_datum;
                document.getElementById('edit-tijd').value = activiteit.activiteit_tijd;
                document.getElementById('edit-locatie').value = activiteit.activiteit_locatie;
                document.getElementById('edit-soort').value = activiteit.soort_activiteit;
                document.getElementById('edit-status').value = activiteit.activiteit_status;
                document.getElementById('edit-image_url').value = activiteit.activiteit_afbeelding_url || '';
                document.getElementById('edit-opmerkingen').value = activiteit.activiteit_opmerkingen || '';

                const editModal = document.getElementById('edit-activity-modal');
                editModal.style.display = 'block';
                document.body.style.overflow = 'hidden';

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else {
                alert('Fout bij ophalen activiteit: ' + (data.error || 'Onbekende fout'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Er is een fout opgetreden bij het ophalen van de activiteit.');
        });
};

window.deleteActiviteit = function(id, titel) {
    if (confirm('Weet je zeker dat je "' + titel + '" wilt verwijderen?\n\nDeze actie kan niet ongedaan worden.')) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('classes/delete_activiteit.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Activiteit succesvol verwijderd!');
                    location.reload();
                } else {
                    alert('Fout bij verwijderen: ' + (data.error || 'Onbekende fout'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Er is een fout opgetreden.');
            });
    }
    return false;
};

window.openDetailModal = function(id) {
    const detailModal = document.getElementById('detail-activity-modal');
    const detailContent = document.getElementById('detail-content');

    fetch('classes/get_activiteit_detail.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const activiteit = data.data;
                const participants = data.participants || [];

                document.getElementById('detail-titel').textContent = activiteit.activiteit_titel;

                let html = `
                    <div class="detail-body">
                        <div class="detail-section">
                            <h3>Beschrijving</h3>
                            <p>${escapeHtml(activiteit.activiteit_beschrijving)}</p>
                        </div>
                        
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <span class="detail-label"><i data-lucide="calendar-days"></i> Datum:</span>
                                <span class="detail-value">${escapeHtml(activiteit.activiteit_datum)}</span>
                            </div>
                            <div class="detail-info-item">
                                <span class="detail-label"><i data-lucide="clock-10"></i> Tijd:</span>
                                <span class="detail-value">${escapeHtml(activiteit.activiteit_tijd)}</span>
                            </div>
                            <div class="detail-info-item">
                                <span class="detail-label"><i data-lucide="map-pin"></i> Locatie:</span>
                                <span class="detail-value">${escapeHtml(activiteit.activiteit_locatie)}</span>
                            </div>
                            <div class="detail-info-item">
                                <span class="detail-label"><i data-lucide="flame-kindling"></i> Soort:</span>
                                <span class="detail-value">${escapeHtml(activiteit.soort_activiteit)}</span>
                            </div>
                            <div class="detail-info-item">
                                <span class="detail-label"><i data-lucide="chart-column"></i> Status:</span>
                                <span class="detail-value status ${escapeHtml(activiteit.activiteit_status)}">${escapeHtml(activiteit.activiteit_status)}</span>
                            </div>
                `;

                if (activiteit.lat && activiteit.lng) {
                    html += `
                            <div class="detail-info-item">
                                <span class="detail-label"><i data-lucide="axis-3d"></i> Coördinaten:</span>
                                <span class="detail-value">${(Math.round(activiteit.lat * 10000) / 10000)}, ${(Math.round(activiteit.lng * 10000) / 10000)}</span>
                            </div>
                    `;
                }

                html += `
                        </div>
                `;

                if (activiteit.activiteit_opmerkingen) {
                    html += `
                        <div class="detail-section">
                            <h3>Opmerkingen</h3>
                            <p>${escapeHtml(activiteit.activiteit_opmerkingen)}</p>
                        </div>
                    `;
                }

                html += `
                        <div class="detail-section">
                            <div class="participants-header">
                                <h3>Deelnemers</h3>
                                <span class="participant-count">${participants.length}</span>
                            </div>
                `;

                if (participants.length > 0) {
                    html += '<ul class="participant-list">';
                    participants.forEach(p => {
                        html += `<li class="participant-item">👤 ${escapeHtml(p.naam || p.username || 'Onbekend')}</li>`;
                    });
                    html += '</ul>';
                } else {
                    html += '<p class="no-participants">Nog geen deelnemers.</p>';
                }

                html += `
                        </div>
                        
                        <div class="detail-guest-section">
                            <h3>Gast uitnodigen</h3>
                            <form class="guest-form" onsubmit="inviteGuest(event, ${id})">
                                <div class="form-group">
                                    <input type="email" placeholder="E-mailadres gast" required class="guest-email">
                                </div>
                                <button type="submit" class="btn-invite">Gast uitnodigen</button>
                            </form>
                        </div>
                        
                        <div class="detail-actions">
                            <button class="btn-edit" onclick="editActiviteit(${id}); document.querySelector('.detail-close').onclick();">Bewerken</button>
                            <button class="btn-close" onclick="document.querySelector('.detail-close').onclick();">Sluiten</button>
                        </div>
                    </div>
                `;

                detailContent.innerHTML = html;
                detailModal.style.display = 'block';
                document.body.style.overflow = 'hidden';

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else {
                alert('Fout bij ophalen activiteit: ' + (data.error || 'Onbekende fout'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Er is een fout opgetreden bij het ophalen van de activiteit.');
        });
};

window.inviteGuest = function(event, activityId) {
    event.preventDefault();
    const emailInput = document.querySelector('.guest-email');
    const email = emailInput.value.trim();

    if (!email) {
        alert('Voer een e-mailadres in');
        return;
    }

    // TODO: Implement server-side guest invitation
    alert(`Uitnodiging verzonden naar ${email}`);
    emailInput.value = '';
};

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ModalManager();
});

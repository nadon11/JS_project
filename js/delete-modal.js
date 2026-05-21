


let currentClaimToDelete = null;

function initializeDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete-claim-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const claimId = this.getAttribute('data-claim-id');
            openDeleteModal(claimId);
        });
    });
}

function openDeleteModal(claimId) {
    currentClaimToDelete = claimId;
    

    let modal = document.getElementById('deleteClaimModal');
    if (!modal) {
        createDeleteModal();
        modal = document.getElementById('deleteClaimModal');
    }
    

    const deleteModal = new bootstrap.Modal(modal);
    deleteModal.show();
}

function createDeleteModal() {
    const modalHTML = `
        <div class="modal fade" id="deleteClaimModal" tabindex="-1" role="dialog" aria-labelledby="deleteClaimLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteClaimLabel">
                            <i class="mdi mdi-alert-octagon"></i> Confirmation de suppression
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                            <i class="mdi mdi-information"></i>
                            <strong>Attention !</strong> Cette action est irréversible.
                        </div>
                        <p>
                            Êtes-vous certain de vouloir <strong>supprimer définitivement</strong> cette réclamation ?
                        </p>
                        <p class="text-muted">
                            Cette opération ne peut pas être annulée.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="mdi mdi-close"></i> Annuler
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                            <i class="mdi mdi-trash-can"></i> Supprimer définitivement
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    

    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);
}

function confirmDelete() {
    if (!currentClaimToDelete) {
        showNotification('Erreur: ID de la réclamation non trouvé', 'danger');
        return;
    }
    

    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteClaimModal'));
    modal.hide();
    
    showLoadingSpinner('Suppression en cours...');
    
    fetch('delete_claim.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `claim_id=${encodeURIComponent(currentClaimToDelete)}`
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        
        if (data.success) {
            showNotification('✅ ' + data.message, 'success');
            

            setTimeout(() => {
                const row = document.querySelector(`button[data-claim-id="${currentClaimToDelete}"]`)?.closest('tr');
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-100%)';
                    
                    setTimeout(() => {
                        row.remove();

                        const tableBody = document.getElementById('claimsTableBody');
                        if (tableBody && tableBody.children.length === 0) {
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="100%" style="text-align: center; padding: 20px;">
                                        <i class="mdi mdi-inbox-multiple"></i> Aucune réclamation trouvée
                                    </td>
                                </tr>
                            `;
                        }
                    }, 300);
                }
            }, 1000);
        } else {
            showNotification('❌ Erreur: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        console.error('Error:', error);
        showNotification('❌ Erreur de connexion au serveur', 'danger');
    });
    
    currentClaimToDelete = null;
}

function showLoadingSpinner(text = 'Chargement...') {
    let spinner = document.getElementById('loadingSpinner');
    
    if (!spinner) {
        spinner = document.createElement('div');
        spinner.id = 'loadingSpinner';
        document.body.appendChild(spinner);
    }
    
    spinner.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: rgba(0, 0, 0, 0.8);
        padding: 30px;
        border-radius: 8px;
        color: white;
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    `;
    
    spinner.innerHTML = `
        <div class="spinner-border spinner-border-sm" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span>${text}</span>
    `;
}

function hideLoadingSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.style.opacity = '0';
        spinner.style.transition = 'opacity 0.3s ease';
        
        setTimeout(() => {
            if (spinner.parentNode) {
                spinner.remove();
            }
        }, 300);
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    const typeClasses = {
        'success': 'alert-success',
        'danger': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    notification.innerHTML = `
        <div class="alert ${typeClasses[type] || 'alert-info'} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease';
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 4000);
}


if (!document.getElementById('notificationStyles')) {
    const style = document.createElement('style');
    style.id = 'notificationStyles';
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
}


document.addEventListener('DOMContentLoaded', function() {
    initializeDeleteButtons();
});

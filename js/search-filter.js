


function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('filterStatus');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', debounce(performSearch, 300));
    }
    
    if (filterSelect) {
        filterSelect.addEventListener('change', performSearch);
    }
}


function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function performSearch() {
    const searchTerm = document.getElementById('searchInput')?.value || '';
    const filterStatus = document.getElementById('filterStatus')?.value || '';
    const tableBody = document.getElementById('claimsTableBody');
    
    if (!tableBody) {

        clientSideFilter(searchTerm, filterStatus);
        return;
    }
    

    showLoadingSpinner();
    
    fetch('search_claims.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `search=${encodeURIComponent(searchTerm)}&status=${encodeURIComponent(filterStatus)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateTableBody(data.rows);
            showNotification('Recherche effectuée avec succès', 'success');
        } else {
            showNotification('Erreur lors de la recherche: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur de connexion', 'danger');
        clientSideFilter(searchTerm, filterStatus);
    })
    .finally(() => {
        hideLoadingSpinner();
    });
}

function clientSideFilter(searchTerm, filterStatus) {
    const rows = document.querySelectorAll('#claimsTableBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const statusCell = row.querySelector('td:nth-child(3)');
        const status = statusCell ? statusCell.textContent.toLowerCase() : '';
        
        const matchSearch = text.includes(searchTerm.toLowerCase());
        const matchStatus = filterStatus === '' || status.includes(filterStatus.toLowerCase());
        
        if (matchSearch && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    

    if (visibleCount === 0) {
        const noResultsRow = document.getElementById('noResultsRow');
        if (noResultsRow) {
            noResultsRow.style.display = '';
        }
    } else {
        const noResultsRow = document.getElementById('noResultsRow');
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
    }
}

function updateTableBody(rows) {
    const tableBody = document.getElementById('claimsTableBody');
    
    if (rows.length === 0) {
        tableBody.innerHTML = `
            <tr id="noResultsRow">
                <td colspan="100%" style="text-align: center; padding: 20px;">
                    <i class="mdi mdi-magnify"></i> Aucun résultat trouvé
                </td>
            </tr>
        `;
        return;
    }
    
    tableBody.innerHTML = rows.map(row => createTableRow(row)).join('');
    

    initializeDeleteButtons();
}

function createTableRow(row) {
    const statusBadge = getStatusBadge(row.status);
    const damageBadge = getDamageBadge(row.damage);
    
    let deleteBtn = '';
    if (row.showDeleteBtn) {
        deleteBtn = `<button class="btn btn-danger btn-sm delete-claim-btn" data-claim-id="${row.id}" title="Supprimer">
                        <i class="mdi mdi-trash-can"></i> Supprimer
                    </button>`;
    }
    
    let modifyBtn = '';
    if (row.showModifyBtn) {
        modifyBtn = `<a class="btn btn-inverse-success btn-sm" href="affectation_rec.php?id_rec=${row.id}">
                        <i class="mdi mdi-pencil"></i> Modifier
                    </a>`;
    } else if (row.showAddBtn) {
        modifyBtn = `<a class="btn btn-inverse-primary btn-sm" href="affectation_rec.php?id_rec=${row.id}">
                        <i class="mdi mdi-plus"></i> Ajouter
                    </a>`;
    }
    
    return `
        <tr>
            <td>${row.full_name}</td>
            <td>${damageBadge}</td>
            <td>${statusBadge}</td>
            <td>${row.date_add}</td>
            <td>${row.type}</td>
            ${row.showTeamColumn ? `<td>${row.team_info}${modifyBtn}</td>` : ''}
            ${deleteBtn ? `<td>${deleteBtn}</td>` : ''}
        </tr>
    `;
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<label class="badge badge-danger">En attente</label>',
        'In progress': '<label class="badge badge-warning">En cours</label>',
        'Completed': '<label class="badge badge-success">Terminée</label>'
    };
    return badges[status] || `<label class="badge badge-secondary">${status}</label>`;
}

function getDamageBadge(damage) {
    const badges = {
        'little': '<button type="button" class="btn btn-warning btn-rounded btn-fw btn-sm">Faible</button>',
        'very little': '<button type="button" class="btn btn-success btn-rounded btn-fw btn-sm">Très faible</button>',
        'عالية': '<button type="button" class="btn btn-danger btn-rounded btn-fw btn-sm">Élevée</button>'
    };
    return badges[damage] || `<button type="button" class="btn btn-secondary btn-rounded btn-fw btn-sm">${damage}</button>`;
}

function showLoadingSpinner() {
    const spinner = document.createElement('div');
    spinner.id = 'loadingSpinner';
    spinner.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: rgba(0, 0, 0, 0.7);
        padding: 30px;
        border-radius: 8px;
        color: white;
        display: flex;
        align-items: center;
        gap: 15px;
    `;
    spinner.innerHTML = `
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span>Recherche en cours...</span>
    `;
    document.body.appendChild(spinner);
}

function hideLoadingSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.remove();
    }
}


document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeDeleteButtons();
});

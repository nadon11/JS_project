


function validateClaimForm() {
    const place = document.getElementById('place');
    const type = document.getElementById('typeSelect');
    const damage = document.querySelector('input[name="damage"]:checked');
    const description = document.getElementById('description');
    
    let isValid = true;
    const errors = [];
    

    if (!place || place.value.trim() === '') {
        errors.push('❌ Veuillez saisir l\'emplacement du dégât');
        isValid = false;
    } else if (place.value.trim().length < 3) {
        errors.push('❌ L\'emplacement doit contenir au moins 3 caractères');
        isValid = false;
    }
    

    if (!type || type.value === '' || type.value === '0') {
        errors.push('❌ Veuillez sélectionner un type de dégât');
        isValid = false;
    }
    

    if (!damage || damage.value === '') {
        errors.push('❌ Veuillez sélectionner le degré du dégât');
        isValid = false;
    }
    

    if (!description || description.value.trim() === '') {
        errors.push('❌ Veuillez saisir une description du dégât');
        isValid = false;
    } else if (description.value.trim().length < 10) {
        errors.push('❌ La description doit contenir au moins 10 caractères');
        isValid = false;
    } else if (description.value.trim().length > 500) {
        errors.push('❌ La description ne doit pas dépasser 500 caractères');
        isValid = false;
    }
    

    if (!isValid) {
        showValidationErrors(errors);
    } else {
        clearValidationErrors();
    }
    
    return isValid;
}

function showValidationErrors(errors) {

    let errorContainer = document.getElementById('validationErrors');
    
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.id = 'validationErrors';
        const form = document.querySelector('.forms-sample');
        form.insertBefore(errorContainer, form.firstChild);
    }
    
    errorContainer.innerHTML = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 20px;">
            <strong>Erreurs de validation :</strong><br>
            ${errors.map(error => `<div>${error}</div>`).join('')}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    errorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function clearValidationErrors() {
    const errorContainer = document.getElementById('validationErrors');
    if (errorContainer) {
        errorContainer.remove();
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.forms-sample');
    
    if (form) {

        const inputs = form.querySelectorAll('input[type="text"], select, textarea, input[type="radio"]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateClaimForm();
            });
            
            input.addEventListener('change', function() {
                validateClaimForm();
            });
        });
        

        form.addEventListener('submit', function(e) {
            if (!validateClaimForm()) {
                e.preventDefault();
                return false;
            }
        });
    }
});

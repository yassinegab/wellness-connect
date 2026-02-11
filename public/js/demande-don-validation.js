document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('#demandeDonForm');
    if (!form) return;

    const typeDemande   = form.querySelector('[name="type_demande"]');
    const typeOrgane    = form.querySelector('[name="type_organe"]');
    const typeSanguin   = form.querySelector('[name="type_sanguin"]');
    const region        = form.querySelector('[name="region"]');
    const urgence       = form.querySelector('[name="urgence"]');

    /* ===================== HELPERS ===================== */

    function showError(input, message) {
        clearError(input);

        input.classList.add('border-red-500', 'ring-2', 'ring-red-400');

        const error = document.createElement('p');
        error.className = 'text-red-600 text-sm mt-1 error-msg';
        error.innerText = message;

        input.parentNode.appendChild(error);
    }

    function clearError(input) {
        input.classList.remove('border-red-500', 'ring-2', 'ring-red-400');
        const error = input.parentNode.querySelector('.error-msg');
        if (error) error.remove();
    }

    function clearAllErrors() {
        form.querySelectorAll('.error-msg').forEach(e => e.remove());
        form.querySelectorAll('.border-red-500').forEach(e =>
            e.classList.remove('border-red-500', 'ring-2', 'ring-red-400')
        );
    }

    function showField(el) {
        el.closest('.field').classList.remove('hidden');
    }

    function hideField(el) {
        el.closest('.field').classList.add('hidden');
        el.value = '';
        clearError(el);
    }

    /* ===================== DYNAMIC LOGIC ===================== */

    function toggleFields() {
        if (typeDemande.value === 'sang') {
            showField(typeSanguin);
            hideField(typeOrgane);
        }

        if (typeDemande.value === 'organe') {
            showField(typeOrgane);
            hideField(typeSanguin);
        }
    }

    typeDemande.addEventListener('change', toggleFields);

    /* ===================== LIVE VALIDATION ===================== */

    region.addEventListener('input', () => {
        if (region.value.trim().length >= 3) {
            clearError(region);
        }
    });

    typeOrgane?.addEventListener('input', () => clearError(typeOrgane));
    typeSanguin?.addEventListener('change', () => clearError(typeSanguin));

    /* ===================== FORM SUBMIT ===================== */

    form.addEventListener('submit', e => {
        clearAllErrors();

        let hasError = false;

        // TYPE DEMANDE
        if (!['sang', 'organe'].includes(typeDemande.value)) {
            showError(typeDemande, 'Choisissez un type de demande');
            hasError = true;
        }

        // REGION
        if (!region.value || region.value.trim().length < 3) {
            showError(region, 'Région invalide (min 3 caractères)');
            hasError = true;
        }

        // LOGIC SANG / ORGANE
        if (typeDemande.value === 'sang') {
            if (!typeSanguin.value) {
                showError(typeSanguin, 'Type sanguin obligatoire');
                hasError = true;
            }
        }

        if (typeDemande.value === 'organe') {
            if (!typeOrgane.value || typeOrgane.value.trim().length < 2) {
                showError(typeOrgane, 'Type d’organe obligatoire');
                hasError = true;
            }
        }

        // FINAL
        if (hasError) {
            e.preventDefault();
            form.classList.add('animate-shake');
            setTimeout(() => form.classList.remove('animate-shake'), 400);
        }
    });

    /* ===================== INIT ===================== */
    toggleFields();
});

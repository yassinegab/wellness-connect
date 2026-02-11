document.addEventListener('DOMContentLoaded', () => {

    const type = document.getElementById('type_don');
    const sang = document.getElementById('sangBox');
    const organe = document.getElementById('organeBox');
    const form = document.getElementById('donForm');

    type.addEventListener('change', () => {
        sang.classList.add('hidden');
        organe.classList.add('hidden');

        if (type.value === 'sang') sang.classList.remove('hidden');
        if (type.value === 'organe') organe.classList.remove('hidden');
    });

    form.addEventListener('submit', e => {
        if (type.value === '') {
            alert('Veuillez choisir un type de don');
            e.preventDefault();
        }
    });
});

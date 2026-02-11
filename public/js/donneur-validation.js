document.getElementById('donneurForm').addEventListener('submit', function (e) {
    let valid = true;

    const rules = {
        nom: /^[A-Za-zÀ-ÿ\s]{2,}$/,
        prenom: /^[A-Za-zÀ-ÿ\s]{2,}$/,
        telephone: /^\d{8}$/
    };

    document.querySelectorAll('.error').forEach(e => e.textContent = '');
    document.querySelectorAll('.input').forEach(i => i.classList.remove('error-input'));

    function error(input, message) {
        input.classList.add('error-input');
        input.nextElementSibling.textContent = message;
        valid = false;
    }

    const nom = this.nom;
    if (!rules.nom.test(nom.value)) {
        error(nom, 'Nom invalide');
    }

    const prenom = this.prenom;
    if (!rules.prenom.test(prenom.value)) {
        error(prenom, 'Prénom invalide');
    }

    const age = this.age;
    if (age.value < 18 || age.value > 65) {
        error(age, 'Âge autorisé : 18–65');
    }

    const tel = this.telephone;
    if (!rules.telephone.test(tel.value)) {
        error(tel, 'Téléphone invalide (8 chiffres)');
    }

    const gs = this.groupe_sanguin;
    if (gs.value === '') {
        error(gs, 'Choisissez un groupe sanguin');
    }

    if (!valid) e.preventDefault();
});

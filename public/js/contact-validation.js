document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('contactForm');
  const errorBox = document.getElementById('formError');

  if (!form || !errorBox) return;

  form.addEventListener('submit', e => {
    errorBox.classList.add('hidden');
    errorBox.textContent = '';

    const nom = form.querySelector('[name$="[nom]"]')?.value.trim();
    const lien = form.querySelector('[name$="[lien]"]')?.value.trim();
    const tel = form.querySelector('[name$="[telephone]"]')?.value.trim();

    // Nom obligatoire
    // Regex : lettres + espaces + accents
const textRegex = /^[A-Za-zÀ-ÿ\s]+$/;

// Nom obligatoire + texte seulement
if (!nom) {
  showError('❌ Le nom est obligatoire');
  e.preventDefault();
  return;
}
if (!textRegex.test(nom)) {
  showError('❌ Le nom doit contenir uniquement des lettres');
  e.preventDefault();
  return;
}

// Lien obligatoire + texte seulement
if (!lien) {
  showError('❌ Le lien est obligatoire (famille, voisin…)');
  e.preventDefault();
  return;
}
if (!textRegex.test(lien)) {
  showError('❌ Le lien doit contenir uniquement des lettres');
  e.preventDefault();
  return;
}


    // Téléphone (8 à 15 chiffres, + autorisé)
    const phoneRegex = /^[0-9+\s]{8,15}$/;
    if (!phoneRegex.test(tel)) {
      showError('❌ Numéro de téléphone invalide');
      e.preventDefault();
      return;
    }
  });

  function showError(message) {
    errorBox.textContent = message;
    errorBox.classList.remove('hidden');
  }
});

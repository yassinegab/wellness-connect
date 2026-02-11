document.addEventListener('DOMContentLoaded', () => {
  const fp = document.getElementById('donFingerprint');
  const form = document.getElementById('donForm');

  if (!fp || !form) return;

  fp.addEventListener('click', () => {

    // ❌ Bloque si formulaire invalide
    if (!form.checkValidity()) {
      form.reportValidity();
      fp.classList.document.addEventListener('DOMContentLoaded', () => {
    const fp = document.getElementById('donFingerprint');
    const submit = document.getElementById('realSubmit');

    if (!fp || !submit) return;

    fp.addEventListener('click', () => {
        // déclenche animation
        fp.classList.add('active');

        // soumission réelle après animation
        setTimeout(() => {
            submit.click();
        }, 1800); // timing adapté à ton animation
    });
});
add('shake');

      setTimeout(() => fp.classList.remove('shake'), 400);
      return;
    }

    // ❌ Empêche double clic
    if (fp.classList.contains('active')) return;

    fp.classList.add('active');

    // ⏳ Soumission après animation
    setTimeout(() => {
      form.submit();
    }, 4500);
  });
});

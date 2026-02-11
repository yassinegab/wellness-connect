document.addEventListener('DOMContentLoaded', () => {
  const latInput = document.getElementById('latitude');
  const lngInput = document.getElementById('longitude');
  const status = document.getElementById('geoStatus');

  if (!latInput || !lngInput) return;

  if (!navigator.geolocation) {
    status.textContent = '❌ Géolocalisation non supportée';
    return;
  }

  status.textContent = '📍 Récupération de votre position...';

  navigator.geolocation.getCurrentPosition(
    position => {
      latInput.value = position.coords.latitude;
      lngInput.value = position.coords.longitude;
      status.textContent = '✅ Position détectée';
    },
    error => {
      status.textContent = '❌ Impossible de récupérer la position';
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
});

document.addEventListener('DOMContentLoaded', () => {

  const root = document.getElementById('domeRoot');
  const sphere = document.getElementById('domeSphere');
  const focus = document.getElementById('domeFocus');
  const focusImg = focus?.querySelector('img');

  if (!root || !sphere || !window.DOME_IMAGES) return;

  const radius = 450;
  let rotY = 0;
  let rotX = 0;
  let vx = 0;
  let vy = 0;
  let dragging = false;
  let lastX = 0;
  let lastY = 0;

  const images = window.DOME_IMAGES;
  const count = images.length;

  // 🟢 CREATE IMAGES ONCE
  images.forEach((src, i) => {
    const item = document.createElement('div');
    item.className = 'dome-item';

    const img = document.createElement('img');
    img.src = src;
    img.alt = 'Aide image ' + (i + 1);

    item.appendChild(img);
    sphere.appendChild(item);

    const angleY = (360 / count) * i;
    const angleX = i % 2 === 0 ? -20 : 20;

    item.style.transform =
      `rotateY(${angleY}deg) rotateX(${angleX}deg) translateZ(${radius}px)`;

    // 🔍 Focus on click
    img.addEventListener('click', () => {
      if (!focus || !focusImg) return;
      focusImg.src = src;
      focus.style.display = 'flex';
    });
  });

  // Close focus
  if (focus) {
    focus.addEventListener('click', () => {
      focus.style.display = 'none';
    });
  }

  // 🖱️ DRAG ROTATION
  root.addEventListener('pointerdown', e => {
    dragging = true;
    lastX = e.clientX;
    lastY = e.clientY;
    vx = vy = 0;
  });

  window.addEventListener('pointerup', () => dragging = false);

  window.addEventListener('pointermove', e => {
    if (!dragging) return;

    const dx = e.clientX - lastX;
    const dy = e.clientY - lastY;

    rotY += dx * 0.3;
    rotX -= dy * 0.3;

    vx = dx * 0.05;
    vy = dy * 0.05;

    lastX = e.clientX;
    lastY = e.clientY;
  });

  // 🔄 ANIMATION LOOP
  function animate() {
    if (!dragging) {
      rotY += vx;
      rotX -= vy;
      vx *= 0.95;
      vy *= 0.95;
    }

    sphere.style.transform =
      `rotateX(${rotX}deg) rotateY(${rotY}deg)`;

    requestAnimationFrame(animate);
  }

  animate();
});

document.querySelectorAll('.aide-box').forEach(box => {
  box.addEventListener('mouseenter', () => {
    gsap.to(box, {
      y: -10,
      scale: 1.02,
      boxShadow: '0 30px 60px rgba(0,0,0,0.35)',
      duration: 0.35,
      ease: 'power3.out'
    });
  });

  box.addEventListener('mouseleave', () => {
    gsap.to(box, {
      y: 0,
      scale: 1,
      boxShadow: '0 10px 20px rgba(0,0,0,0.2)',
      duration: 0.35,
      ease: 'power3.out'
    });
  });
});

'use strict';
document.addEventListener('submit', (event) => {
  const form = event.target.closest('[data-confirm]');
  if (form && !window.confirm(form.dataset.confirm || '¿Confirmas esta acción?')) {
    event.preventDefault();
  }
});

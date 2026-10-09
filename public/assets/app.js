document.querySelector('.nav-toggle')?.addEventListener('click', (event) => {
  const button = event.currentTarget;
  const expanded = button.getAttribute('aria-expanded') === 'true';
  button.setAttribute('aria-expanded', String(!expanded));
  document.getElementById('site-nav')?.classList.toggle('is-open', !expanded);
});

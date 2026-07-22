// Auto-dismiss flash errors after 3s
document.querySelectorAll('.error').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .5s'; }, 3000);
});

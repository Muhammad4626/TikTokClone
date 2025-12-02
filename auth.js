document.addEventListener('DOMContentLoaded', () => {
    const toggleSignup = document.getElementById('toggleSignup');
    const toggleLogin = document.getElementById('showLogin');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const loginTitle = document.getElementById('loginTitle');
    const signupTitle = document.getElementById('signupTitle');
    const toggleLoginText = document.getElementById('toggleLogin');

    toggleSignup.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display = 'none';
        loginTitle.style.display = 'none';
        toggleSignup.parentElement.style.display = 'none';
        signupForm.style.display = 'block';
        signupTitle.style.display = 'block';
        toggleLoginText.style.display = 'block';
    });

    toggleLogin.addEventListener('click', (e) => {
        e.preventDefault();
        signupForm.style.display = 'none';
        signupTitle.style.display = 'none';
        toggleLoginText.style.display = 'none';
        loginForm.style.display = 'block';
        loginTitle.style.display = 'block';
        toggleSignup.parentElement.style.display = 'block';
    });
});
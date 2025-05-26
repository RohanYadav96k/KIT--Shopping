
document.addEventListener('DOMContentLoaded', function() {
    const signupForm = document.querySelector('form[action="signup.php"]'); 
    if (signupForm) {
        signupForm.addEventListener('submit', function(event) {
            const password = signupForm.querySelector('#password');
            const confirmPassword = signupForm.querySelector('#confirm_password');
            const email = signupForm.querySelector('#email');

            if (password && confirmPassword && password.value !== confirmPassword.value) {
                alert("Passwords do not match!");
                event.preventDefault(); // Prevent form submission
                return;
            }

            if (password && password.value.length < 6) {
                alert("Password must be at least 6 characters long.");
                event.preventDefault();
                return;
            }

            if (email && !validateEmail(email.value)) {
                alert("Please enter a valid email address.");
                event.preventDefault();
                return;
            }
        });
    }

    
});

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

// above is validation for signup form
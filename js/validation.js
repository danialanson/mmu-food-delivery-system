function validateForm() {
    var username = document.forms["registrationForm"]["username"].value.trim();
    var email = document.forms["registrationForm"]["email"].value.trim();
    var password = document.forms["registrationForm"]["password"].value.trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
    var isValid = true;

    // Clear previous error messages
    document.getElementById("usernameError").innerText = "";
    document.getElementById("emailError").innerText = "";
    document.getElementById("passwordError").innerText = "";

    // Username validation
    if (username === "") {
        document.getElementById("usernameError").innerText = "Username must be filled out";
        isValid = false;
    }

    // Email validation
    if (!emailPattern.test(email)) {
        document.getElementById("emailError").innerText = "Invalid email address";
        isValid = false;
    }

    // Password validation
    if (password === "") {
        document.getElementById("passwordError").innerText = "Password must be filled out";
        isValid = false;
    } else if (password.length < 8) {
        document.getElementById("passwordError").innerText = "Password must be at least 8 characters long";
        isValid = false;
    }

    return isValid;
}

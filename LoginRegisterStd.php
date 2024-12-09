<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register Here</title>
    <!-- Include FingerprintJS CDN -->
    <script async src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3.3.0/dist/fp.min.js"></script>
</head>
<body>
    
<h2>Student Registration</h2>
    <form action="ConnecteRegistretionFile.php" method="POST" id="registrationForm" enctype="multipart/form-data" onsubmit="return validateForm()">
    
        <!-- First Name -->
        <label for="fName">First Name:</label>
        <input type="text" id="fName" name="fName" maxlength="20"  placeholder="First Name" required>
        <br><br>

        <!-- Last Name -->
        <label for="lName">Last Name:</label>
        <input type="text" id="lName" name="lName" maxlength="50" pattern="[A-Za-z]+" placeholder="Last Name" required>
        <br><br>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" maxlength="60" placeholder="YourName@email.com" required>
        <br><br>

        <!-- Password -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" minlength="8" maxlength="15" placeholder="Password (ensure the password has a minimum of 8 characters)" required>
        <br><br>

        <!-- Re-enter password -->
        <label for="rePassword">Reenter password:</label>
        <input type="password" id="rePassword" name="rePassword" minlength="8" maxlength="15" placeholder="Re-enter your password" required>
        <br><br>
        
        <small id="passwordAlert" style="color: red; visibility: hidden;">Passwords do not match!</small>
        <br>

        <!-- Telephone Number -->
        <label for="telNo">Telephone Number:</label>
        <input type="tel" id="telNo" name="telNo" pattern="[0-9]{10}" maxlength="15" placeholder="Your Telephone Number" required>
        <br><br>
        
        <!-- Address -->
        <label for="address">Address:</label>
        <textarea id="address" name="address" maxlength="200" rows="3" placeholder="Home Address"></textarea>
        <br><br>

        <!-- School -->
        <label for="school">School:</label>
        <input type="text" id="school" name="school" maxlength="100" placeholder="Your School Name">
        <br><br>

        <!-- Profile Picture -->
        <label for="profilePicture">Profile Picture:</label>
        <input type="file" id="profilePicture" name="profilePicture" accept="image/png, image/jpeg, image/jpg">
        <br><br>

        <!-- Submit -->
        <button id="submitBtn" type="submit" disabled>Register</button>
    </form>

    <h2>Student Login</h2>
        <form action="LoginFile.php" method="POST" id="loginForm">
           <!-- Email -->
            <label for="lemail">Email:</label>
            <input type="email" id="lemail" name="lemail" maxlength="60" placeholder="YourName@email.com" required>
            <br><br>
            
           <!-- Password -->
            <label for="lpassword">Password:</label>
            <input type="password" id="lpassword" name="lpassword" minlength="8" maxlength="15" placeholder="Your Password " required>
            <br><br>

            <button type="submit">Login</button>
        </form>


<script>
    const passwordInput = document.getElementById("password");
    const rePasswordInput = document.getElementById("rePassword");
    const submitButton = document.getElementById("submitBtn");
    const alertMessage = document.getElementById("passwordAlert");

    function validatePasswords() {
        const password = passwordInput.value;
        const rePassword = rePasswordInput.value;

        if (password && rePassword) {
            if (password === rePassword) {
                alertMessage.style.color = "green";
                alertMessage.textContent = "Passwords match!";
                alertMessage.style.visibility = "visible";
                submitButton.disabled = false; // Enable the button
            } else {
                alertMessage.style.color = "red";
                alertMessage.textContent = "Passwords do not match!";
                alertMessage.style.visibility = "visible";
                submitButton.disabled = true; // Disable the button
            }
        } else {
            alertMessage.style.visibility = "hidden"; // Hide alert if fields are empty
            submitButton.disabled = true;
        }
    }

    // Add event listeners for real-time validation
    passwordInput.addEventListener("input", validatePasswords);
    rePasswordInput.addEventListener("input", validatePasswords);


    // When the form is submitted, generate the fingerprint and send it with the form data
        document.getElementById('registrationForm').addEventListener('submit', async function (event) {
        event.preventDefault(); // Prevent form submission until fingerprint is added

        try {
            const fp = await FingerprintJS.load();
            const result = await fp.get();
            const fingerprint = result.visitorId;

            // Append the fingerprint as a hidden field to the form
            const fingerprintInput = document.createElement('input');
            fingerprintInput.type = 'hidden';
            fingerprintInput.name = 'deviceFingerprint';
            fingerprintInput.value = fingerprint;

            document.getElementById('registrationForm').appendChild(fingerprintInput);
            document.getElementById('loginForm').appendChild(fingerprintInput);
            // Now submit the form with the fingerprint included
            this.submit(); 
        } catch (error) {
            console.error("Error generating fingerprint:", error);
        }
    });

        document.getElementById('loginForm').addEventListener('submit', async function (event) {
        event.preventDefault();

            try {
                const fp = await FingerprintJS.load();
                const result = await fp.get();
                const fingerprint = result.visitorId;

                // Append the fingerprint as a hidden field to the form
                const fingerprintInput = document.createElement('input');
                fingerprintInput.type = 'hidden';
                fingerprintInput.name = 'deviceFingerprintlog';
                fingerprintInput.value = fingerprint;

                document.getElementById('loginForm').appendChild(fingerprintInput);
                // Now submit the form with the fingerprint included
                this.submit(); 
            } catch (error) {
                console.error("Error generating fingerprint:", error);
            }
        });

</script>

</body>
</html>
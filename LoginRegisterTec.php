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
    
<h2>Teacher Registration</h2>
<form action="TeacherRegistration.php" method="POST" id="teacherRegistrationForm" enctype="multipart/form-data" onsubmit="return validateForm()">

    <!-- First Name -->
    <label for="fName">First Name:</label>
    <input type="text" id="fName" name="fName" maxlength="20" placeholder="First Name" required>
    <br><br>

    <!-- Last Name -->
    <label for="lName">Last Name:</label>
    <input type="text" id="lName" name="lName" maxlength="20" pattern="[A-Za-z]+" placeholder="Last Name" required>
    <br><br>

    <!-- Subject -->
    <label for="subject">Subject:</label>
    <select id="subject" name="subject" required>
        <option value="" disabled selected>Select a subject</option>
        <!-- Science Stream -->
        <optgroup label="Science Stream">
            <option value="Biology">Biology</option>
            <option value="Chemistry">Chemistry</option>
            <option value="Physics">Physics</option>
            <option value="Combined Mathematics">Combined Mathematics</option>
            <option value="Agricultural Science">Agricultural Science</option>
            <option value="ICT">Information and Communication Technology (ICT)</option>
        </optgroup>
        <!-- Commerce Stream -->
        <optgroup label="Commerce Stream">
            <option value="Accounting">Accounting</option>
            <option value="Business Studies">Business Studies</option>
            <option value="Economics">Economics</option>
            <option value="ICT">Information and Communication Technology (ICT)</option>
            <option value="Business Statistics">Business Statistics</option>
        </optgroup>
        <!-- Arts Stream -->
        <optgroup label="Arts Stream">
            <option value="Sinhala Language and Literature">Sinhala Language and Literature</option>
            <option value="Tamil Language and Literature">Tamil Language and Literature</option>
            <option value="English Literature">English Literature</option>
            <option value="History">History</option>
            <option value="Geography">Geography</option>
            <option value="Political Science">Political Science</option>
            <option value="Logic and Scientific Method">Logic and Scientific Method</option>
            <option value="Buddhist Civilization">Buddhist Civilization</option>
            <option value="Hindu Civilization">Hindu Civilization</option>
            <option value="Christian Civilization">Christian Civilization</option>
            <option value="Islam Civilization">Islam Civilization</option>
            <option value="Drama">Drama</option>
            <option value="Dance">Dance</option>
            <option value="Music">Music</option>
            <option value="Arts">Art</option>
            <option value="Media Studies">Media Studies</option>
            <option value="Communication and Media Studies">Communication and Media Studies</option>
            <option value="Sociology">Sociology</option>
            <option value="Psychology">Psychology</option>
            <option value="ICT">Information and Communication Technology (ICT)</option>
        </optgroup>
        <!-- Technology Stream -->
        <optgroup label="Technology Stream">
            <option value="Science for Technology">Science for Technology</option>
            <option value="Engineering Technology">Engineering Technology</option>
            <option value="Bio-Systems Technology">Bio-Systems Technology</option>
            <option value="ICT">Information and Communication Technology (ICT)</option>
        </optgroup>
    </select>
    <br><br>


    <!-- Email -->
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" maxlength="100" placeholder="YourName@email.com" required>
    <br><br>

    <!-- Password -->
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" minlength="8" maxlength="15" placeholder="Password (ensure the password has a minimum of 8 characters)" required>
    <br><br>

    <!-- Re-enter password -->
    <label for="rePassword">Re-enter Password:</label>
    <input type="password" id="rePassword" name="rePassword" minlength="8" maxlength="15" placeholder="Re-enter your password" required>
    <br><br>

    <small id="passwordAlert" style="color: red; visibility: hidden;">Passwords do not match!</small>
    <br>

    <!-- Telephone Number -->
    <label for="telNo">Telephone Number:</label>
    <input type="tel" id="telNo" name="telNo" pattern="[0-9]{10}" maxlength="15" minlength="9" placeholder="Your Telephone Number" required>
    <br><br>

    <!-- Level -->
    <!-- <label for="level">Teaching Level:</label>
    <div id="level">
        <input type="radio" id="level" name="level" value="O/L">
        <label for="ol">Ordenari Level</label>
        <br>
        <input type="radio" id="level" name="level" value="A/L">
        <label for="al">Advance Level</label>
    </div>
    <br><br> -->

    <!-- Profile Picture -->
    <label for="profilePicture">Profile Picture:</label>
    <input type="file" id="profilePicture" name="profilePicture" accept="image/png, image/jpeg, image/jpg" required>
    <br><br>

    <!-- Description -->
    <label for="description">Description:</label>
    <textarea id="description" name="description" maxlength="900" rows="3" placeholder="description about yourself"></textarea>
    <br><br>

    <!-- School -->
    <label for="school">School:</label>
    <input type="text" id="school" name="school" maxlength="100" placeholder="Your School Name" required>
    <br><br>

    <!-- Submit -->
    <button id="submitBtn" type="submit" disabled>Register</button>
</form>

<h2>Teacher Login</h2>
<form action="LoginFileTec.php" method="POST" id="teacherLoginForm">

    <!-- Email -->
    <label for="lemail">Email:</label>
    <input type="email" id="lemail" name="lemail" maxlength="30" placeholder="YourName@email.com" required>
    <br><br>

    <!-- Password -->
    <label for="lpassword">Password:</label>
    <input type="password" id="lpassword" name="lpassword" minlength="8" maxlength="15" placeholder="Your Password" required>
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
                submitButton.disabled = false;
            } else {
                alertMessage.style.color = "red";
                alertMessage.textContent = "Passwords do not match!";
                alertMessage.style.visibility = "visible";
                submitButton.disabled = true;
            }
        } else {
            alertMessage.style.visibility = "hidden";
            submitButton.disabled = true;
        }
    }

    passwordInput.addEventListener("input", validatePasswords);
    rePasswordInput.addEventListener("input", validatePasswords);

    async function generateFingerprint(form) {
        try {
            const fp = await FingerprintJS.load();
            const result = await fp.get();
            const fingerprint = result.visitorId;

            const fingerprintInput = document.createElement("input");
            fingerprintInput.type = "hidden";
            fingerprintInput.name = "deviceFingerprint";
            fingerprintInput.value = fingerprint;

            form.appendChild(fingerprintInput);
            form.submit();
        } catch (error) {
            console.error("Error generating fingerprint:", error);
        }
    }

    document.getElementById("teacherRegistrationForm").addEventListener("submit", function (event) {
        event.preventDefault();
        generateFingerprint(this);
    });

    document.getElementById("teacherLoginForm").addEventListener("submit", function (event) {
        event.preventDefault();
        generateFingerprint(this);
    });
</script>

</body>
</html>

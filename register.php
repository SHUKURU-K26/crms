<?php
session_start();

if (isset($_SESSION['valid_registration_code'])) {    
    include "./web_db/connection.php";

    $errors = [];
    $success = "";

    if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["register"])) {
        // Sanitize input
        $fullnames = trim(htmlspecialchars($_POST['fullnames']));
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $username = trim(htmlspecialchars($_POST['username']));
        $mobile = trim(htmlspecialchars($_POST['mobile']));
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "<p style='font-weight:bold;font-style:italic;'>Invalid email format.</p>";
        }
        if ($password !== $confirmPassword) {
            $errors[] = "<p style='font-weight:bold;font-style:italic;'>Passwords do not match.</p>";
        }

        // If no errors, hash and insert
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (full_name,username,email,phone, password) 
            VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $fullnames,$username,$email, $mobile, $hashedPassword);

            if ($stmt->execute()) {
                $success = "<p style='background-color:lightgreen;padding:10px;font-size:1.2em;color:white;font-weight:bold;border-radius:8px;'>
                <i class='fas fa-check'></i> Account created successfully!
                </p>";
                header("refresh:2;url=index.php");
            } else {
                $errors[] = "Error creating account: " . $conn->error;
            }
            $stmt->close();
        }
    }            
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>New User Registration</title>
  <link href="./vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="./css/index.css" rel="stylesheet" type="text/css">
  <link rel="icon" href="./img/GuestProLogoReal.JPG" type="image/png">
  <style>
    .password-rules {
      display: none;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
      padding: 15px;
      font-size: 14px;
      margin-top: 8px;
      animation: fadeIn 0.3s ease-in-out;
    }
    .password-rules p {
      margin: 6px 0;
      font-weight: 500;
    }
    .rule-valid { color: green; }
    .rule-invalid { color: red; }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-5px);}
      to {opacity: 1; transform: translateY(0);}
    }
    .success-msg {color: green; font-weight: bold; margin-bottom: 10px;}
    .error-msg {color: red; margin-bottom: 10px;}
  </style>
</head>
<body>

<div class="login-container">
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">    
    <div style="max-width: 400px; margin: auto; text-align: center; padding: 20px;">
      <img src="./img/GuestProLogoReal.JPG" alt="Logo" style="width: 100px; height: auto; margin-bottom: 15px;">

      <h2 style="font-size: 20px; margin: 0;">
        <span style="font-weight: bold;">GuestPro Tours</span> -
        <span style="color: #007bff;">CRMS</span>
      </h2>
      <p style="margin: 5px 0 20px 0; color: #666;">Sign Up as a new User.</p>

      <!-- Show messages -->
      <?php if(!empty($errors)): ?>
        <div class="error-msg"><?php echo implode("<br>", $errors); ?></div>
      <?php endif; ?>
      <?php if($success): ?>
        <div class="success-msg"><?php echo $success; ?></div>
      <?php endif; ?>

      <!-- Fields -->
      <div class="input-group">
        <i class="fas fa-user icon-left"></i>
        <input type="text" placeholder="Full Names" name="fullnames" required />
      </div>

      <div class="input-group">
        <i class="fas fa-envelope icon-left"></i>
        <input type="email" placeholder="Enter your Email" name="email" id="email" required />          
      </div>

      <div class="input-group">
        <i class="fas fa-user-circle icon-left"></i>
        <input type="text" placeholder="Username" name="username" id="username" required />          
      </div>

      <div class="input-group">
        <i class="fas fa-phone icon-left"></i>
        <input type="tel" placeholder="Mobile Number" name="mobile" id="phone" required />          
      </div>

      <div class="input-group">
        <i class="fas fa-lock icon-left"></i>
        <input type="password" placeholder="Password" name="password" id="passwordInput" required />
        <i class="fas fa-eye icon-right" id="togglePassword"></i>
      </div>

      <!-- Password Rules Box -->
      <div id="passwordRules" class="password-rules">
        <p id="ruleLength" class="rule-invalid">At least 8 characters</p>
        <p id="ruleUpper" class="rule-invalid">At least one uppercase letter</p>
        <p id="ruleLower" class="rule-invalid">At least one lowercase letter</p>
        <p id="ruleNumber" class="rule-invalid">At least one number</p>
        <p id="ruleSpecial" class="rule-invalid">At least one special character (!@#$%^&*)</p>
      </div>

      <div class="input-group">
        <i class="fas fa-lock icon-left"></i>
        <input type="password" placeholder="Confirm Password" name="confirmPassword" id="confirmPassword" required />          
      </div>
      
      <input type="submit" class="login-btn" name="register" value="SIGN UP" />
      <button type="button" class="login-btn" style="background: #970000; margin-top: 10px;" onclick="window.location.href='index.php'">
          Cancel Registration
      </button>
    </div>
  </form>

  <p style="text-align: center; color: rgb(184, 184, 184); font-size: 15px;">&copy; 2025 Car Rental Management System.</p>
</div>

<script>
  const passwordInput = document.getElementById("passwordInput");
  const confirmPassword = document.getElementById("confirmPassword");
  const togglePassword = document.getElementById("togglePassword");
  const passwordRules = document.getElementById("passwordRules");

  // Toggle Password Visibility
  togglePassword.addEventListener("click", () => {
    const type = passwordInput.type === "password" ? "text" : "password";
    passwordInput.type = type;
    togglePassword.classList.toggle("fa-eye-slash");
  });

  // Show rules when password field focused
  passwordInput.addEventListener("focus", () => {
    passwordRules.style.display = "block";
  });
  passwordInput.addEventListener("blur", () => {
    if(passwordInput.value === "") {
      passwordRules.style.display = "none";
    }
  });

  // Live validation
  passwordInput.addEventListener("input", () => {
    const value = passwordInput.value;

    document.getElementById("ruleLength").className = value.length >= 8 ? "rule-valid" : "rule-invalid";
    document.getElementById("ruleUpper").className = /[A-Z]/.test(value) ? "rule-valid" : "rule-invalid";
    document.getElementById("ruleLower").className = /[a-z]/.test(value) ? "rule-valid" : "rule-invalid";
    document.getElementById("ruleNumber").className = /\d/.test(value) ? "rule-valid" : "rule-invalid";
    document.getElementById("ruleSpecial").className = /[!@#$%^&*]/.test(value) ? "rule-valid" : "rule-invalid";
  });
</script>

</body>
</html>
<?php
} else {
    header("Location: index.php");
    exit();
}
?>

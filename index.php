<?php
session_start();
include "./web_db/connection.php";

$usernameErr = $passwordErr = "";
$username = $password = "";
$isSubmitted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isSubmitted = true;

    // Detect if it's login or register mode
    $formMode = isset($_POST["form_mode"]) ? $_POST["form_mode"] : "login";

    if ($formMode === "login") {
        // Sanitize input
        $username = isset($_POST["username"]) ? htmlspecialchars(trim($_POST["username"])) : '';
        $password = isset($_POST["password"]) ? htmlspecialchars(trim($_POST["password"])) : '';

        // Check for Admin credentials securely
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role='admin'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $adminPassword = $result->fetch_assoc();
            if (password_verify($password, $adminPassword['password'])) {
              $_SESSION["password"] = $adminPassword["password"];
              $_SESSION["adminEmail"] = $adminPassword["email"];
              header("Location: frontend/admin/home.php");
              exit();
            }
            else {
                $passwordErr = "Invalid Crdentials";
            }
        } else {
            $usernameErr = "Invalid Credentials";
        }

        $stmt->close();

      // Check staff credentials securely
      $stmt2=$conn->prepare("SELECT * FROM users WHERE username = ?  AND role='staff'");
        $stmt2->bind_param("s", $username);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows === 1) {
            $staffCredentials = $result2->fetch_assoc();
            if (password_verify($password, $staffCredentials['password'])){
                $_SESSION["Userpassword"] = $staffCredentials["password"];
                $_SESSION["username"] = $staffCredentials["username"];
                header("Location: frontend/staff/staff_home.php");
                exit();
            }else{
                $passwordErr = "Invalid Credentials";
            }
        }  else{
            $usernameErr = "Invalid Credentials";
        }

        $stmt2->close();
    } elseif ($formMode === "register") {
        // Handle registration code verification here
        if (isset($_POST["verify"])){
        
            $code = isset($_POST["register_code"]) ? htmlspecialchars(trim($_POST["register_code"])) : '';
            $_SESSION['valid_registration_code'] = $code;
     
             // Example logic: check if code exists and unused
             $stmt = $conn->prepare("SELECT * FROM registration_codes WHERE code = ? AND status = 'Unused'");
             $stmt->bind_param("s", $code);
             $stmt->execute();
             $result = $stmt->get_result();
     
             if ($result->num_rows === 1) {
                // Mark code as used
                $updateCode = $conn->prepare("UPDATE registration_codes SET status='used' WHERE code=?");
                $deleteCode=$conn->prepare("DELETE FROM registration_codes WHERE code=?");
                $deleteCode->bind_param("s", $code);
                $updateCode->bind_param("s", $code);
                $deleteCode->execute();
                $updateCode->execute();
                header("Location: register.php");
                exit();
             } else {
                 include "./system_messages/invalidCodeMessage.php";
             $stmt->close();
          }
          # code...
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Car Rental System</title>
  <link href="./vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="./css/index.css" rel="stylesheet" type="text/css">
  <link rel="icon" href="./img/GuestProLogoReal.JPG" type="image/png">

  <link rel="stylesheet" href="./css/custom.css">
</head>
<body>

<div class="login-container">
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST" novalidate id="authForm">
    <input type="hidden" name="form_mode" id="form_mode" value="login">
    <div style="max-width: 400px; margin: auto; text-align: center; padding: 20px;">
      <img src="./img/GuestProLogoReal.JPG" alt="Logo" style="width: 100px; height: auto; margin-bottom: 15px;">

      <h2 style="font-size: 20px; margin: 0;">
        <span style="font-weight: bold;">GuestPro Tours</span> -
        <span style="color: #007bff;">CRMS</span>
      </h2>
      <p id="formDescription" style="margin: 5px 0 20px 0; color: #666;">Sign in to continue.</p>

      <!-- Login Fields -->
      <div id="loginFields">
        <div class="input-group <?php echo $usernameErr ? 'shake' : ''; ?>">
          <i class="fas fa-user icon-left"></i>
          <input type="text" placeholder="Username" name="username" value="<?php echo $username; ?>" required />
        </div>

        <?php if ($usernameErr): ?>
          <p class="error-message"><?php echo $usernameErr; ?></p>
        <?php endif; ?>

        <div class="input-group <?php echo $passwordErr ? 'shake' : ''; ?>">
          <i class="fas fa-lock icon-left"></i>
          <input type="password" placeholder="Password" name="password" id="passwordInput" required />
          <i class="fas fa-eye icon-right" id="togglePassword"></i>
        </div>
        
        <?php if ($passwordErr): ?>
          <p class="error-message"><?php echo $passwordErr; ?></p>
        <?php endif; ?>

        <input type="submit" class="login-btn" value="SIGN IN" />
        <button type="button" class="login-btn" style="background: #970000; margin-top: 10px;" id="showRegisterBtn">CREATE ACCOUNT</button>
      </div>

      <!-- Register Fields -->
      <div id="registerFields" style="display: none;">
        <div class="input-group">
          <i class="fas fa-key icon-left"></i>
          <input type="text" placeholder="Enter Registration Code" name="register_code" required />
        </div>
        <p id="codeError" class="error-message" style="display:none; color:red; margin-top:5px;"></p>
        <input type="submit" class="login-btn" value="VERIFY CODE" name="verify"/>
        <button type="button" class="login-btn" style="background: #6c757d; margin-top: 10px;" id="showLoginBtn">BACK TO LOGIN</button>
      </div>
    </div>
  </form>

  <p style="text-align: center; color: rgb(184, 184, 184); font-size: 15px;">&copy; 2025 Car Rental Management System.</p>
</div>

<script>
  // Toggle Password Visibility
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("passwordInput");
  togglePassword.addEventListener("click", () => {
    const type = passwordInput.type === "password" ? "text" : "password";
    passwordInput.type = type;
    togglePassword.classList.toggle("fa-eye-slash");
  });

  // Remove shake class after animation
  document.querySelectorAll(".input-group.shake").forEach(input => {
    input.addEventListener("animationend", () => {
      input.classList.remove("shake");
    });
  });

  // Switch between login and register modes
  const loginFields = document.getElementById("loginFields");
  const registerFields = document.getElementById("registerFields");
  const formModeInput = document.getElementById("form_mode");
  const formDescription = document.getElementById("formDescription");

  document.getElementById("showRegisterBtn").addEventListener("click", () => {
    loginFields.style.display = "none";
    registerFields.style.display = "block";
    formModeInput.value = "register";
    formDescription.textContent = "Enter your code to register.";
  });

  document.getElementById("showLoginBtn").addEventListener("click", () => {
    loginFields.style.display = "block";
    registerFields.style.display = "none";
    formModeInput.value = "login";
    formDescription.textContent = "Sign in to continue.";
  });

  const authForm = document.getElementById("authForm");
  authForm.addEventListener("submit", function(e){
  const formMode = document.getElementById("form_mode").value;
  if (formMode === "register") {
    const codeInput = document.querySelector('input[name="register_code"]');
    const codeError = document.getElementById("codeError");
    if (codeInput.value.trim() === "") {
      e.preventDefault();
      codeError.textContent = "Please Enter the registration code.";
      codeError.style.display = "block";
      codeInput.focus();
    } else {
      codeError.style.display = "none";
    }
  }
});
</script>

</body>
</html>

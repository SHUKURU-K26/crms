
const form =document.getElementById("myform")
const ruleLength=document.getElementById("rule-length")
const passwordField = document.getElementById("NewPassword");
const ruleUppercase = document.getElementById("rule-uppercase");
const ruleLowercase = document.getElementById("rule-lowercase");
const ruleNumber = document.getElementById("rule-number");
const ruleSpecial = document.getElementById("rule-special");

      function validatePasswordRules(password) {
         let valid = true;

            if (/[A-Z]/.test(password)) {
            ruleUppercase.classList.add("valid");
            ruleUppercase.classList.remove("invalid");
            } else {
            ruleUppercase.classList.remove("valid");
            ruleUppercase.classList.add("invalid");
            valid = false;
            }

            if (/[a-z]/.test(password)) {
            ruleLowercase.classList.add("valid");
            ruleLowercase.classList.remove("invalid");
            } else {
            ruleLowercase.classList.remove("valid");
            ruleLowercase.classList.add("invalid");
            valid = false;
            }

            if (/[0-9]/.test(password)) {
            ruleNumber.classList.add("valid");
            ruleNumber.classList.remove("invalid");
            } 
            else {
            ruleNumber.classList.remove("valid");
            ruleNumber.classList.add("invalid");
            valid = false;
            }
            if (/[^A-Za-z0-9]/.test(password)) {
            ruleSpecial.classList.add("valid");
            ruleSpecial.classList.remove("invalid");
            } 
            else {
            ruleSpecial.classList.remove("valid");
            ruleSpecial.classList.add("invalid");
            valid = false;
            } 

            if (password.length >= 8) {
            ruleLength.classList.add("valid");
            ruleLength.classList.remove("invalid");
            } else {
            ruleLength.classList.remove("valid");
            ruleLength.classList.add("invalid");
            valid = false;
            }

         return valid;
      }
      passwordField.addEventListener("input", function() {
      validatePasswordRules(passwordField.value);
    });
    form.addEventListener("submit", function(e) {
      const password = passwordField.value;
      let checkPasswordMatch=document.getElementById("checkPasswordMatch");
      
      if (!validatePasswordRules(password)) {
        e.preventDefault();
        checkPasswordMatch.textContent = "Your password must meet all the strength rules below.";
        checkPasswordMatch.style.color="red"
        return;
      }
      checkPasswordMatch.textContent = "";
    });

    [ruleLength, ruleUppercase, ruleLowercase, ruleNumber, ruleSpecial].forEach(p => p.classList.add("invalid"));
   
    //Check Password Matching Section
    document.getElementById("NewPassword").addEventListener("input", ()=>{      
      let checkPasswordMatch=document.getElementById("checkPasswordMatch");
      let NewPasswordField=document.getElementById("NewPassword");
      let ConfirmPasswordField=document.getElementById("ConfirmPassword");      
      
      if(NewPasswordField.value!=""){
         ConfirmPasswordField.disabled=false
         checkPasswordMatch.textContent=""
      }
    })

    document.getElementById("ConfirmPassword").addEventListener("input", ()=>{
      let checkPasswordMatch=document.getElementById("checkPasswordMatch");
      let NewPasswordField=document.getElementById("NewPassword");
      let ConfirmPasswordField=document.getElementById("ConfirmPassword");
      let submitBtn=document.getElementById("submitBtn");

       if(NewPasswordField.value==""){
         ConfirmPasswordField.disabled=true;
         ConfirmPasswordField.value="";
         checkPasswordMatch.textContent="Enter the Password First before to Re-Type";
         checkPasswordMatch.style.color="red";
         submitBtn.disabled=true;
      }
      else if(NewPasswordField.value!=""){

         if(ConfirmPasswordField.value===NewPasswordField.value){
            checkPasswordMatch.textContent="Password Match";
            checkPasswordMatch.style.color="green";
            checkPasswordMatch.style.fontWeight="bold";
            submitBtn.disabled=false;
         }
         else{
            submitBtn.disabled=true;
            checkPasswordMatch.textContent="Password Mismatch";
            checkPasswordMatch.style.color="red";
            checkPasswordMatch.style.fontWeight="bold";
         }
      }
      
    })
    function ToShowPassword(){
        let pswd=document.getElementById("password");
        let NewPswd=document.getElementById("NewPassword")
        let ConfirmPassword=document.getElementById("ConfirmPassword")
        if (pswd.type==="password" && NewPswd.type==="password" && ConfirmPassword.type==="password"){
            pswd.type="text"
            NewPswd.type="text"
            ConfirmPassword.type="text"
        }else{
            pswd.type="password"
            NewPswd.type="password"
            ConfirmPassword.type="password"
        }
    }
   
      
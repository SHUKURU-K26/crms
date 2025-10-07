//Get the insurance issued and expiry date elements
let insurance_issued_date=document.getElementById("insurance_issued_date");
let insurance_expiry_date=document.getElementById("insurance_expiry_date");

// Get the control issued and expiry date elements
let control_issued_date=document.getElementById("control_issued_date");
let control_expiry_date=document.getElementById("control_expiry_date");

insurance_expiry_date.addEventListener('input', function(){
    
  if(insurance_expiry_date.value <= insurance_issued_date.value){
    document.getElementById("insurance_expiry_date").value="";
    document.getElementById("insurance_expiry_date_message").textContent="Must be Greater than Renew Date!";
    document.getElementById("insurance_expiry_date_message").style.color="red";
    document.getElementById("insurance_expiry_date_message").style.fontSize="12px";
    setTimeout(function(){
      document.getElementById("insurance_expiry_date_message").textContent="";
  }, 3000);
  }
  else if(insurance_expiry_date.value > insurance_issued_date.value){
      document.getElementById("insurance_expiry_date_message").textContent="Still in Use!";
      document.getElementById("insurance_expiry_date_message").style.color="green";

     setTimeout(function(){
      document.getElementById("insurance_expiry_date_message").textContent="";
  }, 2000); 
}
})

control_expiry_date.addEventListener('input', function(){
  
  if(control_expiry_date.value <= control_issued_date.value){
    document.getElementById("control_expiry_date").value="";
    document.getElementById("control_expiry_date_message").textContent="Must be Greater than Renew Date!";
    document.getElementById("control_expiry_date_message").style.color="red";
    document.getElementById("control_expiry_date_message").style.fontSize="12px";
    setTimeout(function(){
      document.getElementById("control_expiry_date_message").textContent="";
  }, 3000);
  }
  else if(control_expiry_date.value > control_issued_date.value){
      document.getElementById("control_expiry_date_message").textContent="Still in Use!";
      document.getElementById("control_expiry_date_message").style.color="green";

     setTimeout(function(){
      document.getElementById("control_expiry_date_message").textContent="";
  }, 2000); 
}
});
            //Validation of Form Inputs

            //Textual Data Form Validation
         function ToValidateChars(InputField){
            InputField.addEventListener("input", function (event) {
            let inputValue = event.target.value;
            event.target.value = inputValue.replace(/[^a-zA-Z]/g, " ");
                    
        });
      } 

      
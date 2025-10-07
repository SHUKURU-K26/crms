<?php
echo "
<div id='successAlertBox'>
  ✅ $category_name is Registered a New Category.
</div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const alertBox = document.getElementById('successAlertBox');
      if (!alertBox) return;

      setTimeout(() => {
      alertBox.style.opacity = 0;
      setTimeout(() => alertBox.remove(), 500);  
      window.location.href = '';    
      }, 300);

  });
  </script>
";
?>
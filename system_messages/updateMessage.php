<?php
echo "
<div id='successAlertBox'>
  ✅ $Car_name Updated <strong>Successfully.</strong>
</div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const alertBox = document.getElementById('successAlertBox');
      if (!alertBox) return;

      setTimeout(() => {
      alertBox.style.opacity = 0;
      setTimeout(() => alertBox.remove(), 500);
      }, 3000);
  });
  </script>
";
?>
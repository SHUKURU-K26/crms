<?php
echo "
<div id='successAlertBox'>
  ✅ $car_name Insurance renewed <strong>Successfully. Valid till $expiry_date</strong>
</div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const alertBox = document.getElementById('successAlertBox');
      if (!alertBox) return;
      setTimeout(() => {
      alertBox.style.opacity = 0;
      setTimeout(() => alertBox.remove(), 500);
        window.location.href = '../frontend/admin/car_overview.php';
      }, 3000);
  });
  </script>
";
?>
<?php
echo "
<div id='alertBox'>
  ⚠️ Error: The Plate Number '$PlateNumber' already exists in the database.
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const alertBox = document.getElementById('alertBox');
      if (!alertBox) return;

      setTimeout(() => {
      alertBox.style.opacity = 0;
      setTimeout(() => alertBox.remove(), 500);
      }, 3000);
  });
  </script>
";
?>
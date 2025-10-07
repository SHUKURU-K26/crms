<?php
echo "
<div id='alertBox'>
  ⚠️ Incorrent Current Password!
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
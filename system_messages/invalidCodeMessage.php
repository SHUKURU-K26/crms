<?php
echo "
<div id='alertBox'>
  ⚠️ Invalid Code. Please try again.
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const alertBox = document.getElementById('alertBox');
      if (!alertBox) return;

      setTimeout(() => {
      alertBox.style.opacity = 0;
      setTimeout(() => alertBox.remove(), 500);
      window.location.href = '';
      }, 2000);
  });
  </script>
";
?>
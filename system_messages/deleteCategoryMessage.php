<?php
echo "
<div id='deleteSuccessBox'>
  🗑️ Deletion Went Successful!
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('deleteSuccessBox');
    if (!alertBox) return;

    setTimeout(() => {
      alertBox.style.opacity = 0;
      setTimeout(() => alertBox.remove(), 500);
      window.location.href = '';
    }, 2000);

  });
</script>

<style>
  #deleteSuccessBox {
    max-width: 90%;
    margin: 10px auto;
    padding: 12px 20px;
    background-color: #f8d7da;
    color: #721c24;
    font-family: 'Segoe UI', sans-serif;
    font-size: 16px;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: opacity 0.5s ease-in-out;
    z-index: 9999;
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
  }

  @media (max-width: 600px) {
    #deleteSuccessBox {
      font-size: 14px;
      padding: 10px 15px;
    }
  }
</style>
";
?>

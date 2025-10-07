<?php

            echo "
                <div id='successAlertBox' style='position: fixed; top: 20px; right: 20px; z-index: 9999; background: linear-gradient(135deg, #1cc88a, #13855c); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); animation: slideIn 0.5s ease;'>
                        <i class='fas fa-check-circle'></i> $car_name rental Fee has been fully Cleared!
                    </div>
                    <style>
                        @keyframes slideIn {
                            from { transform: translateX(100%); opacity: 0; }
                            to { transform: translateX(0); opacity: 1; }
                        }
                    </style>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const alertBox = document.getElementById('successAlertBox');
                        setTimeout(() => {
                            alertBox.style.transform = 'translateX(100%)';
                            alertBox.style.opacity = '0';
                            setTimeout(() => {
                                alertBox.remove();
                                window.location.href=''
                            }, 500);
                        }, 3000);
                    });
                    </script>";
?>

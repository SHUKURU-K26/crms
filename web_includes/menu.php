<!-- Original Menu with Modal Integration -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="home.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-car"></i>
        </div>
        <div class="sidebar-brand-text mx-3">CRMS<sup>&copy;</sup></div>                
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="home.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Car & Renting
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-car"></i>
            <span>Cars</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Cars & Categories:</h6>
                <a class="collapse-item" href="register_new_car.php">Register New Car</a>                        
                <a class="collapse-item" href="new_category.php">Register New Category</a>
                <a class="collapse-item" href="car_overview.php">Overview</a>
            </div>
        </div>
    </li>



    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-key"></i>
            <span>Renting</span>
        </a>

        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Renting Menus:</h6>
                <a class="collapse-item" href="rent_car.php">Rent</a>
                <a class="collapse-item" href="return_car.php">Rentals</a>
            </div>
        </div>
    </li>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#extendRetention"
            aria-expanded="true" aria-controls="extendRetention">
            <i class="fas fa-fw fa-key"></i>
            <span>Extend Retention</span>
        </a>

        <div id="extendRetention" class="collapse" aria-labelledby="extendRetention" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">External Facility:</h6>
                <a class="collapse-item" href="extend_retention.php">Extend</a>                
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Finance
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#finance"
            aria-expanded="true" aria-controls="finance">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Income</span>
        </a>
        <div id="finance" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Debts & Payments:</h6>
                <a class="collapse-item" href="debts_view.php">Debts View</a>                        
                <a class="collapse-item" href="payments.php">Payments</a>                               
            </div>
        </div>


        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#expenses"
            aria-expanded="true" aria-controls="expenses">
            <i class="fas fa-fw fa-wallet"></i>
            <span> Expenses</span>
        </a>
        <div id="expenses" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Debts & Payments:</h6>
                <a class="collapse-item" href="expenses.php">General View</a>                
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        History
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#rentalHistory"
            aria-expanded="true" aria-controls="rentalHistory">
            <i class="fas fa-fw fa-folder"></i>
            <span>Rental History</span>
        </a>
        <div id="rentalHistory" class="collapse" aria-labelledby="rentalHistory" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Internal & External</h6>
                <a class="collapse-item" href="rental_history.php">Internal History</a>
                <a class="collapse-item" href="external_rental_history.php">External History</a>
            </div>
        </div>


        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#ExpenseHistory"
            aria-expanded="true" aria-controls="ExpenseHistory">
            <i class="fas fa-fw fa-folder"></i>
            <span>Expense History</span>
        </a>
        <div id="ExpenseHistory" class="collapse" aria-labelledby="ExpenseHistory" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Expense</h6>
                <a class="collapse-item" href="expense_history.php">General View</a>                
            </div>
        </div>
    </li>

    <!-- Divider-->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Account & Registration
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-toolbox"></i>
            <span>Settings</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Settings</h6>
                <a class="collapse-item" href="profile.php">Profile</a>
                <a class="collapse-item" href="change_password.php">Change Password</a>
                <a class="collapse-item" href="#">Logout</a>                        
            </div>
        </div>

        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#codeMenu"
        aria-expanded="true" aria-controls="collapsePages">
        <i class="fas fa-user-circle"></i>
        <span>New User Registration</span>
        </a>
        <div id="codeMenu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Generate Account Code</h6>                        
                <a class="collapse-item" href="#" id="singleUseCodeLink" data-toggle="modal" data-target="#codeGeneratorModal">
                    <i class="fas fa-key mr-2"></i>
                    Single Use Code
                </a>                        
            </div>
        </div>
    </li>
    
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>           

</ul>

<!-- Code Generator Modal -->
<div class="modal fade" id="codeGeneratorModal" tabindex="-1" role="dialog" aria-labelledby="codeGeneratorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="codeGeneratorModalLabel">
                    <i class="fas fa-key mr-2"></i>
                    Single Use Code Generator
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="codeForm" method="POST" action="">
                    <div class="form-group">
                        <label for="codeInput" class="font-weight-bold text-primary">
                            <i class="fas fa-hashtag mr-2"></i>
                            Generated Code
                        </label>
                        <input type="text" 
                            class="form-control form-control-lg text-center" 
                            id="codeInput" 
                            name="code"
                            placeholder="######"
                            readonly
                            maxlength="6"
                            style="font-family: 'Courier New', monospace; letter-spacing: 3px; font-weight: 600; font-size: 1.5rem;"
                        >
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-primary btn-lg mr-2" id="generateBtn">
                            <i class="fas fa-magic mr-2"></i>
                            Generate Code
                        </button>
                        
                        <button type="submit" class="btn btn-success btn-lg" id="useCodeBtn" name="use_code" style="display: none;">
                            <i class="fas fa-check-circle mr-2"></i>
                            Use Code
                        </button>
                    </div>
                    
                    <div class="alert alert-success mt-3" id="successMessage" style="display: none;">
                        <i class="fas fa-check-circle mr-2"></i>
                        Code generated successfully!
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Notification Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Code Saved Successfully!</h5>
                <p class="mb-3 text-muted">The code has been generated and saved to the database. Share it with the new user for registration.</p>
                <button type="button" class="btn btn-success" data-dismiss="modal">
                    <i class="fas fa-thumbs-up mr-2"></i>
                    Got it!
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for the modal */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header.bg-gradient-primary {
    border-radius: 15px 15px 0 0;
}

#codeInput {
    border: 2px solid #e3e6f0;
    border-radius: 10px;
    transition: all 0.3s ease;
}

#codeInput:focus {
    border-color: #5a5c69;
    box-shadow: 0 0 0 0.2rem rgba(90, 92, 105, 0.25);
    transform: scale(1.02);
}

.btn-lg {
    padding: 10px 25px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.pulse-animation {
    animation: pulse 0.6s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.slide-in {
    animation: slideInRight 0.5s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Highlight active menu item */
.collapse-item.active-code-gen {
    background: linear-gradient(135deg, #4e73df, #224abe);
    color: white !important;
    border-radius: 6px;
    font-weight: 600;
}
</style>

<script>
    const BASE_URL = '<?php echo "/cars_rental_management_system/"; ?>';
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Code Generator Class - Works with existing jQuery
class CodeGenerator {
    constructor() {
        this.generateBtn = document.getElementById('generateBtn');
        this.useCodeBtn = document.getElementById('useCodeBtn');
        this.codeInput = document.getElementById('codeInput');
        this.successMessage = document.getElementById('successMessage');
        
        this.init();
    }
    
    init() {
        // Use your existing jQuery setup
        $(document).ready(() => {
            $('#generateBtn').click(() => this.generateCode());
            
            $('#singleUseCodeLink').click(() => {
                $('.collapse-item').removeClass('active-code-gen');
                $('#singleUseCodeLink').addClass('active-code-gen');
            });
            
            $('#codeGeneratorModal').on('hidden.bs.modal', () => {
                this.resetForm();
            });
        });
    }
    
    generateRandomCode() {
        let code = '';
        for (let i = 0; i < 6; i++) {
            code += Math.floor(Math.random() * 10);
        }
        return code;
    }
    
    async generateCode() {
        // Disable buttons during generation
        this.generateBtn.disabled = true;
        $(this.useCodeBtn).hide();
        
        // Add loading animation
        this.generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
        
        // Simulate generation delay for better UX
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Generate the code
        const newCode = this.generateRandomCode();
        
        // Animate the input field
        $(this.codeInput).addClass('pulse-animation');
        
        // Set the code with typing animation
        await this.typeCode(newCode);
        
        // Show success message
        this.showSuccessMessage();
        
        // Reset generate button and show use code button
        this.generateBtn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Generate New Code';
        this.generateBtn.disabled = false;
        
        // Show Use Code button with animation
        $(this.useCodeBtn).addClass('slide-in').show();
        
        // Remove animation class
        setTimeout(() => {
            $(this.codeInput).removeClass('pulse-animation');
        }, 600);
    }
    
    async typeCode(code) {
        this.codeInput.value = '';
        
        for (let i = 0; i < code.length; i++) {
            await new Promise(resolve => setTimeout(resolve, 100));
            this.codeInput.value += code[i];
        }
    }
    
    showSuccessMessage() {
        $(this.successMessage).fadeIn();
        
        setTimeout(() => {
            $(this.successMessage).fadeOut();
        }, 3000);
    }
    
    resetForm() {
        this.codeInput.value = '';
        $(this.useCodeBtn).hide().removeClass('slide-in');
        this.useCodeBtn.disabled = false;
        this.useCodeBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Use Code';
        this.generateBtn.innerHTML = '<i class="fas fa-magic mr-2"></i>Generate Code';
        this.generateBtn.disabled = false;
        $(this.successMessage).hide();
        $('.collapse-item').removeClass('active-code-gen');
    }
}

// Initialize when DOM is ready - using your existing jQuery
$(document).ready(function() {
    new CodeGenerator();

    // Handle saving the generated code via AJAX
    $('#codeForm').on('submit', function(e) {
        e.preventDefault(); // prevent default form submit

        const code = $('#codeInput').val();
        if (!code) return;

        $.ajax({
            url: BASE_URL + 'web_includes/save_code.php',
            type: 'POST',
            data: { code: code },
            dataType: 'json',
            beforeSend: function() {
                $('#useCodeBtn')
                    .html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving Code...')
                    .prop('disabled', true);
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#codeGeneratorModal').modal('hide');
                    $('#successModal').modal('show');
                } else {
                    alert(response.message || 'Error saving code');
                }
            },
            error: function(xhr, status, error) {
                alert("AJAX Error: " + error);
            },
            complete: function() {
                $('#useCodeBtn')
                    .html('<i class="fas fa-check-circle mr-2"></i>Use Code')
                    .prop('disabled', false);
            }
        });
    });
});
</script>
    
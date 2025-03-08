<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-warning elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
        <img src="../assets/img/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SASO Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="../assets/img/admin.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Administrator</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- MANAGEMENT -->
                <li class="nav-header">MANAGEMENT</li>
                
                <!-- Student -->
                <li class="nav-item">
                    <a href="student_masterlist.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'student_masterlist.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Student</p>
                    </a>
                </li>

               
                <!-- Programs & Sections -->
                <li class="nav-item">
                    <a href="manage_programs.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_programs.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-graduation-cap"></i>
                        <p>Programs & Sections</p>
                    </a>
                </li>

                <!-- SASO -->
                <li class="nav-header">SASO</li>
                
                <!-- Report Student Violation -->
                <li class="nav-item">
                    <a href="report_list.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'report_list.php' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-exclamation-triangle"></i>
                        <p>Report Student Violation</p>
                    </a>
                </li>
               
                <!-- Offenses & Sanction -->
                <li class="nav-item has-treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['manage_offenses.php', 'sec2.php']) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['manage_offenses.php', 'sec2.php']) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>
                            Offenses & Sanction
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="manage_offenses.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_offenses.php' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sec 1 - Academic</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="sec2.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sec2.php' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sec 2 - Non-Academic</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<script>
$(document).ready(function() {
    // Function to get the current page name from URL
    function getCurrentPage() {
        var path = window.location.pathname;
        var page = path.split("/").pop();
        return page || 'dashboard.php'; // Default to dashboard if no page is found
    }

    // Add click event listeners to all menu items with submenus
    $('.nav-item.has-treeview > .nav-link').on('click', function(e) {
        e.preventDefault();
        const menuItem = $(this).parent();
        
        // Toggle the menu open/closed state
        menuItem.toggleClass('menu-open');
        
        // Toggle the arrow rotation
        const arrow = $(this).find('.fa-angle-left');
        arrow.css('transform', menuItem.hasClass('menu-open') ? 'rotate(-90deg)' : 'rotate(0deg)');
    });

    // Set active menu item based on current page
    var currentPage = getCurrentPage();
    
    // First, remove any existing active classes
    $('.nav-link').removeClass('active');
    $('.nav-item').removeClass('menu-open');
    
    // Find the link that matches the current page and add active class
    $('.nav-link').each(function() {
        var href = $(this).attr('href');
        if (href === currentPage) {
            $(this).addClass('active');
            
            // If it's in a submenu, handle parent menu
            var parentTreeview = $(this).closest('.nav-treeview');
            if (parentTreeview.length) {
                var parentItem = parentTreeview.closest('.nav-item.has-treeview');
                parentItem.addClass('menu-open');
                parentItem.children('.nav-link').addClass('active');
                parentTreeview.show();
                parentItem.find('.fa-angle-left').css('transform', 'rotate(-90deg)');
            }
            
            // Handle special cases for parent menu items
            if ($(this).parent('.nav-item').hasClass('has-treeview')) {
                $(this).parent('.nav-item').addClass('menu-open');
                $(this).addClass('active');
            }
        }
    });
});
</script>

<style>
.nav-treeview {
    display: none;
    /* margin-left: 15px; */
}

.nav-item.has-treeview.menu-open > .nav-treeview {
    display: block;
}

.fa-angle-left {
    transition: transform 0.3s ease;
}

.nav-link.active {
    background-color: #007bff !important;
    color: white !important;
    /* border: 1px solid rgba(255, 255, 255, 0.4) !important; */
    border-radius: 4px;
}

.nav-treeview > .nav-item > .nav-link.active {
    background-color: rgba(255,255,255,0.2) !important;
    /* border: 1px solid rgba(255, 255, 255, 0.2) !important; */
}

.nav-item.has-treeview > .nav-link {
    cursor: pointer;
}

.nav-link {
    border: 1px solid transparent;
    transition: all 0.3s ease;
    margin: 4px 0;
}

.nav-link:hover {
    border-color: rgba(255, 255, 255, 0.1);
}

/* Additional styles for better active state visibility */
.nav-item.has-treeview.menu-open > .nav-link {
    background-color: rgba(255, 255, 255, 0.1) !important;
}

.nav-treeview .nav-link.active {
    background-color: #007bff !important;
    color: white !important;
}

.nav-sidebar .nav-item > .nav-link.active {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.nav-sidebar .nav-link.active {
    background-color:rgb(113, 117, 121) !important;
    color: #fff !important;
}

.nav-sidebar .nav-link:hover {
    background-color: rgba(255,255,255,.1);
    color: #fff;
}

.nav-treeview > .nav-item > .nav-link.active {
    background-color: rgba(255,255,255,.2) !important;
}

.nav-sidebar .menu-open > .nav-link {
    background-color: rgba(255,255,255,.1) !important;
}

.nav-sidebar .menu-open > .nav-treeview {
    display: block;
}

.nav-treeview {
    display: none;
}
</style> 
<!-- Header -->
<nav class="navbar navbar-expand-lg main-navbar navbar-border">
    <a href="#" data-toggle="sidebar" class="nav-bar nav-link nav-link-lg"><i class="fas fa-bars"></i></a>
    
    <div class="dropdown dropdown-company">
        <button class="btn btn-add btn-dropdown-company dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-building fa-sm mr-2" aria-hidden="true"></i>KIM 1
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
            <li><a class="dropdown-item active" href="#"><i class="fa fa-building fa-sm mr-2" aria-hidden="true"></i>KIM 1</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa fa-building fa-sm mr-2" aria-hidden="true"></i>KIM 2</a></li>
            <li><a class="dropdown-item" href="#"><i class="fa fa-building fa-sm mr-2" aria-hidden="true"></i>KIM 3</a></li>
        </ul>
    </div>

    <ul class="navbar-nav profile-button">
        <li class="dropdown">
            <a class="nav-link nav-link-lg nav-link-user dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="mr-2 d-none d-lg-inline text-black-600 small"><?= session()->get("login")->name; ?></div>
                <img alt="image" src="<?= base_url() ?>assets/img/avatar/avatar.png" class="rounded-circle mr-1">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" onclick="showLogoutForm()" href="#">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>

<!-- Logout Modal-->
<div class="modal logout-modal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-discard" onclick="hideLogoutForm()">Cancel</button>
                <a class="btn btn-logout-form" href="<?= base_url("logout"); ?>">Logout</a>
            </div>
        </div>
    </div>
</div>

<script>
    const showLogoutForm = function(e) {
        $(".logout-modal").modal("show")
    }

    const hideLogoutForm = function(e) {
        $(".logout-modal").modal("hide")
    }
</script>
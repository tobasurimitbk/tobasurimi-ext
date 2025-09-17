<!-- Header -->
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg sidebar"><i class="fas fa-bars"></i></a></li>
            <!-- <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li> -->
        </ul>
        <div class="search-element">

            <div class="dropdown dropdown-company">
                <button class="btn btn-discard btn-dropdown-company dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-building fa-sm mr-2" aria-hidden="true"></i><?= session()->get("login")->this_company; ?>
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButton1">
                    <?php foreach (session()->get("login")->arr_company as $allCompany) { ?>
                        <li onclick="changeCompanyAccount(<?= $allCompany["id"]; ?>)"><a class="<?= $allCompany["id"] == session()->get("login")->this_company_id ? "dropdown-item active" : "dropdown-item" ?>"><i class="fa fa-building fa-sm mr-2" aria-hidden="true"></i><?= $allCompany["company"]; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="search-backdrop"></div>
            <div class="search-result">
                <div class="search-header">
                    Histories
                </div>
                <div class="search-item">
                    <a href="#">How to hack NASA using CSS</a>
                    <a href="#" class="search-close"><i class="fas fa-times"></i></a>
                </div>
                <div class="search-item">
                    <a href="#">Kodinger.com</a>
                    <a href="#" class="search-close"><i class="fas fa-times"></i></a>
                </div>
                <div class="search-item">
                    <a href="#">#Stisla</a>
                    <a href="#" class="search-close"><i class="fas fa-times"></i></a>
                </div>
                <div class="search-header">
                    Result
                </div>

                <div class="search-header">
                    Projects
                </div>
                <div class="search-item">
                    <a href="#">
                        <div class="search-icon bg-danger text-white mr-3">
                            <i class="fas fa-code"></i>
                        </div>
                        Stisla Admin Template
                    </a>
                </div>
                <div class="search-item">
                    <a href="#">
                        <div class="search-icon bg-primary text-white mr-3">
                            <i class="fas fa-laptop"></i>
                        </div>
                        Create a new Homepage Design
                    </a>
                </div>
            </div>
        </div>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="nav-item dropdown">
            <a style="color: white;" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-circle-half-stroke fa-lg"></i> </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" onclick="changeTheme('light')" href="#">LIGHT</a>
                <a class="dropdown-item" onclick="changeTheme('dark')" href="#">DARK</a>
            </div>
        </li>
        <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="<?= base_url() ?>/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
                <label class="form-label font-weight-bold"><?= session()->get("login")->name; ?></label>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a onclick="showLogoutForm()" href="#" class="dropdown-item has-icon">
                    <i class="fas fa-sign-out-alt"></i> Logout
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
            <div class="modal-body mb-1">Select "Logout" below if you are ready to end your current session.</div>
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

    const changeCompanyAccount = function(dropdownCompanyId) {
        console.log(dropdownCompanyId)
        $.ajax({
            url: "<?= base_url("change-company"); ?>",
            data: {
                id: dropdownCompanyId
            },
            method: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    window.location.href = '<?= base_url(); ?>'
                }
            }
        })
    }

    const changeTheme = function(theme) {
        $.ajax({
            url: "<?= base_url("dashboard/change-theme"); ?>",
            data: {
                theme: theme
            },
            method: "GET",
            dataType: "json",
            success: function(res) {
                window.location.reload();
            }
        })
    }


    $('a[data-toggle="sidebar"]').on('click', function() {

        console.log('Sidebar toggle link clicked');
        $.ajax({
            url: "<?= base_url("/dashboard/toggle"); ?>",
            method: "GET",
            success: function() {
                location.reload();
            }
        });

    });
</script>
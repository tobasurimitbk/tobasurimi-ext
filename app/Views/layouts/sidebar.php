<!-- Sidebar -->
<div class="main-sidebar bg-dark sidebar-style-2">
    <aside id="sidebar-wrapper">

        <div class="sidebar-brand">
            <img src="<?= base_url("assets/img/logo.png"); ?>" width="170" height="60">
        </div>

        <ul class="sidebar-menu">
            <li class="nav-item dropdown{{ ' active'|is_active('^index(.*)', page)|safe }}">
                <a href="#" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
        </ul>

        <ul class="sidebar-menu">
            <li class="nav-item dropdown{{ ' active'|is_active('^index(.*)', page)|safe }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-address-book"></i><span>User</span></a>
                <ul class="dropdown-menu">
                    <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link" href="<?= base_url() ?>login">Login</a>
            </li>
            <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link" href="<?= base_url() ?>register">Register</a></li>
                <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link" href="<?= base_url() ?>user">User</a></li>
        </ul>
        </li>
        </ul>
        </li>
        </ul>

    </aside>
</div>
<!-- Sidebar -->
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="<?= base_url() ?>">Lyrid</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="<?= base_url() ?>">LY</a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="nav-item dropdown{{ ' active'|is_active('^index(.*)', page)|safe }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                <ul class="dropdown-menu">
                    <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link" href="<?= base_url() ?>blank">Blank Page</a>
            </li>
        </ul>
        </li>
        </ul>

        <ul class="sidebar-menu">
            <li class="menu-header">Management User</li>
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

        <ul class="sidebar-menu">
            <li class="menu-header">Master Data</li>
            <li class="nav-item dropdown{{ ' active'|is_active('^index(.*)', page)|safe }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-archive"></i><span>Master Data</span></a>
                <ul class="dropdown-menu">
                    <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link" href="<?= base_url() ?>">Metadata</a>
            </li>
            <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link" href="<?= base_url() ?>">Role</a></li>
        </ul>
        </li>
        </ul>

    </aside>
</div>
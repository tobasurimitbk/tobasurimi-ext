<?php
$currentUriSegment = service('uri')->getSegment(1);
$arrSettingURI = ['user'];
$arrDashboardURI = ['dashboard'];
?>

<!-- Sidebar -->
<div class="main-sidebar bg-dark sidebar-style-2">
    <aside id="sidebar-wrapper">

        <div class="sidebar-brand">
            <img src="<?= base_url("assets/img/logo.png"); ?>" width="170" height="60">
        </div>

        <ul class="sidebar-menu">
            <li class="nav-item dropdown <?= (in_array($currentUriSegment, $arrDashboardURI) ? "active" : "{{ ' active'|is_active('^index(.*)', page)|safe }}") ?>">
                <a href="<?= base_url("dashboard"); ?>" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
        </ul>

        <ul class="sidebar-menu">
        <li class="nav-item dropdown <?= (in_array($currentUriSegment, $arrSettingURI) ? "active" : "{{ ' active'|is_active('^index(.*)', page)|safe }}") ?>">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-cog"></i><span>Settings</span></a>
                <ul class="dropdown-menu">
                    <?php if($currentUriSegment === "user") { ?>
                    <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link" href="<?= base_url("user"); ?>">Manajemen User</a></li>
                    <?php }else {?>
                    <li><a class="nav-link" href="<?= base_url("user"); ?>">Manajemen User</a></li>
                    <?php } ?>
                    <li><a class="nav-link">Manajemen Role</a></li>
                    <li><a class="nav-link">Manajemen Hak Akses</a></li>
                </ul>
            </li>
        </ul>
        </li>
        </ul>

    </aside>
</div>
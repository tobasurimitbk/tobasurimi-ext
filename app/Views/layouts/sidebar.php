<?php
$currentUriSegment = service('uri')->getSegment(1);
$arrMasterURI = ['employee'];
$arrSettingURI = ['user'];
$arrDashboardURI = ['dashboard'];
?>

<!-- Sidebar -->
<div class="main-sidebar bg-dark sidebar-style-2">
    <aside id="sidebar-wrapper">

        <div class="sidebar-brand">
            <img src="<?= base_url("assets/img/logo.png"); ?>" width="170" height="60">
        </div>

        <div class="sidebar-brand sidebar-brand-sm">
            <img src="<?= base_url("assets/img/favicon.png"); ?>" width="35" height="35">
        </div>


        <ul class="sidebar-menu">
            <li class="nav-item dropdown <?= (in_array($currentUriSegment, $arrDashboardURI) ? "active" : "{{ ' active'|is_active('^index(.*)', page)|safe }}") ?>">
                <a href="<?= base_url("dashboard"); ?>" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
        </ul>

        <ul class="sidebar-menu">
            <li class="nav-item dropdown <?= (in_array($currentUriSegment, $arrMasterURI) ? "active" : "{{ ' active'|is_active('^index(.*)', page)|safe }}") ?>">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-database"></i><span>Master Data</span></a>
                <ul class="dropdown-menu">
                    <?php if($currentUriSegment === "employee") { ?>
                    <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link" href="<?= base_url("employee"); ?>">Employee</a></li>
                    <?php }else {?>
                    <li><a class="nav-link" href="<?= base_url("employee"); ?>">Employee</a></li>
                    <?php } ?>
                </ul>
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
                    <?php if($currentUriSegment === "role") { ?>
                    <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link" href="<?= base_url("role"); ?>">Manajemen Role</a></li>
                    <?php }else {?>
                    <li><a class="nav-link" href="<?= base_url("role"); ?>">Manajemen Role</a></li>
                    <?php } ?>
                    <li><a class="nav-link">Manajemen Hak Akses</a></li>
                </ul>
            </li>
        </ul>
        </li>
        </ul>

    </aside>
</div>
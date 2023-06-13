<?php
$currentUriSegment = "/" . service('uri')->getSegment(1);
?>

<!-- Sidebar -->
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">

        <div class="sidebar-brand">
            <img src="<?= base_url("assets/img/logo.png"); ?>">
        </div>

        <div class="sidebar-brand sidebar-brand-sm">
            <img src="<?= base_url("assets/img/favicon.png"); ?>" width="35" height="35">
        </div>

        <ul class="sidebar-menu">
        <?php 
            foreach(session()->get("login")->this_access as $parent_access) {
            ?>
                <!-- DASHBOARD -->
                <?php if($parent_access->url){ ?>
                <li class="nav-item dropdown <?= ($currentUriSegment === $parent_access->url ? "active" : "{{ ' active'|is_active('^index(.*)', page)|safe }}") ?>">
                    <a href="<?= base_url($parent_access->url); ?>" class="nav-link"><i class="<?= $parent_access->icon; ?>"></i><span><?= $parent_access->menuName; ?></span></a>
                </li>
                <?php } else { ?>
                    <!-- CHECK PARENT ACTIVE MENU -->
                    <?php 
                        $exists = false;
                        foreach($parent_access->child as $child_access){
                            if ($child_access->url === $currentUriSegment) {
                                $exists = true;
                                break;
                            }
                        }
                        ?>
                        
                        <!-- PARENT MENU WITH CHILD -->
                        <li class="nav-item dropdown <?= ($exists ? "active" : "{{ ' active'|is_active('^index(.*)', page)|safe }}") ?>">
                            <a href="#" class="nav-link has-dropdown"><i class="<?= $parent_access->icon; ?>"></i><span><?= $parent_access->menuName; ?></span></a>
                            <ul class="dropdown-menu">
                            <?php foreach($parent_access->child as $child_access){
                                if ($currentUriSegment  === $child_access->url) { ?>
                                    <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link active-bar" href="<?= base_url($child_access->url); ?>"><?= $child_access->name; ?></a></li>
                            <?php } else { ?>
                                    <li><a class="nav-link" href="<?= base_url($child_access->url); ?>"><?= $child_access->name; ?></a></li>
                            <?php } 
                            }?>
                            </ul>
                        </li>

                <?php } ?> 
            <?php 
            } 
        ?>
        </ul>
    </aside>
</div>
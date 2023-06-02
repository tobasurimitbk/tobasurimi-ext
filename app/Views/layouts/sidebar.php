<?php
$currentUriSegment = "/" . service('uri')->getSegment(1);
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
        <?php 
            $parent_access = session()->get("login")->this_access;
            for ($i = 0; $i < count($parent_access); $i++) {
            ?>
                <!-- DASHBOARD -->
                <?php if($i === 0){ ?>
                <li class="nav-item dropdown <?= ($currentUriSegment === $parent_access[$i]->url ? "active" : "{{ ' active'|is_active('^index(.*)', page)|safe }}") ?>">
                    <a href="<?= $parent_access[$i]->url; ?>" class="nav-link"><i class="<?= $parent_access[$i]->icon; ?>"></i><span><?= $parent_access[$i]->menuName; ?></span></a>
                </li>
                <?php } else { ?>
                    <!-- CHECK PARENT ACTIVE MENU -->
                    <?php 
                        $child_access = $parent_access[$i]->child;

                        $exists = false;
                        for ($j = 0; $j < count($child_access); $j++){
                            if ($child_access[$j]->url === $currentUriSegment) {
                                $exists = true;
                                break;
                            }
                        }
                        ?>
                        
                        <!-- PARENT MENU WITH CHILD -->
                        <li class="nav-item dropdown <?= ($exists ? "active" : "{{ ' active'|is_active('^index(.*)', page)|safe }}") ?>">
                        <a href="#" class="nav-link has-dropdown"><i class="<?= $parent_access[$i]->icon; ?>"></i><span><?= $parent_access[$i]->menuName; ?></span></a>
                        <ul class="dropdown-menu">
                        <?php for ($j = 0; $j < count($child_access); $j++){
                         if ($currentUriSegment  === $child_access[$j]->url) { ?>
                            <li{{ ' class="active"'|is_active('^index-0.html', page)|safe }}><a class="nav-link active-bar" href="<?= base_url($child_access[$j]->url); ?>"><?= $child_access[$j]->name; ?></a></li>
                        <?php } else { ?>
                            <li><a class="nav-link" href="<?= base_url($child_access[$j]->url); ?>"><?= $child_access[$j]->name; ?></a></li>
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
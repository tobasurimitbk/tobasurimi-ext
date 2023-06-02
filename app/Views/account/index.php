<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Navigation -->
    <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation" style="cursor:pointer">
            <a class="nav-link active" onclick="removeAllTab()" id="kategori-tab" data-toggle="tab" data-target="#kategori" role="tab" aria-controls="kategori" aria-selected="true">Kategori Akun</a>
        </li>
        <li class="nav-item" role="presentation" style="cursor:pointer">
            <a class="nav-link" onclick="removeAllTab()" id="header-tab" data-toggle="tab" data-target="#header" role="tab" aria-controls="header" aria-selected="false">Header Akun</a>
        </li>
        <li class="nav-item" role="presentation" style="cursor:pointer">
            <a class="nav-link" onclick="removeAllTab()" id="sub-tab" data-toggle="tab" data-target="#sub" role="tab" aria-controls="sub" aria-selected="false">Sub Akun</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="kategori" role="tabpanel" aria-labelledby="kategori-tab">
            <div class="collapse-kategori-list show" id="collapseKategoriList">
               tetete
            </div>
        </div>
        <div class="tab-pane fade" id="header" role="tabpanel" aria-labelledby="header-tab">
            <div class="collapse-header-list show" id="collapseHeaderList">
                sasasowdkwodk
            </div>
        </div>
        <div class="tab-pane fade" id="sub" role="tabpanel" aria-labelledby="sub-tab">
            <div class="collapse-header-list show" id="collapseSubList">
                sweew
            </div>
        </div>
    </div>
</div>

<script>
    // REMOVE ALL OPEN FORM
    const removeAllTab = function() {
        $(".collapse-kategori-list").addClass("show")
        $(".collapse-header-list").addClass("show")
        $(".collapse-sub-list").addClass("show")
    }
</script>

<?= $this->endSection(); ?>
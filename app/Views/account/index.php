<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Navigation -->
    <ul class="nav nav-tabs nav-tabs-pemakaian-utilitas" id="myTab" role="tablist">
        <li class="nav-item" role="presentation" style="cursor:pointer">
            <a class="nav-link active" onclick="removeAllTab()" id="listrik-tab" data-toggle="tab" data-target="#listrik" role="tab" aria-controls="listrik" aria-selected="true">Listrik</a>
        </li>
        <li class="nav-item" role="presentation" style="cursor:pointer">
            <a class="nav-link" onclick="removeAllTab()" id="air-tab" data-toggle="tab" data-target="#air" role="tab" aria-controls="air" aria-selected="false">Air</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="listrik" role="tabpanel" aria-labelledby="listrik-tab">
            <div class="collapse collapse-listrik-list show" id="collapseListrikList">
               tetete
            </div>
        </div>
        <div class="tab-pane fade" id="air" role="tabpanel" aria-labelledby="air-tab">
            <div class="collapse collapse-air-list show" id="collapseAirList">
                sasasowdkwodk
            </div>
        </div>
    </div>
</div>

<script>
    // REMOVE ALL OPEN FORM
    const removeAllTab = function() {
        $(".collapse-listrik-list").addClass("show")
        $(".collapse-air-list").addClass("show")
    }
</script>

<?= $this->endSection(); ?>
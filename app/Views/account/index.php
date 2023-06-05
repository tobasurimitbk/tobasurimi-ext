<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal-kategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal-title-kategori"><label class="title-name-kategori"></label> Kategori Akun</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-kategori" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_kategori" name="id_kategori" id="id_kategori" />
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kelompok_akun_id_kategori" name="kelompok_akun_id_kategori" id="kelompok_akun_id_kategori">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataKelompokAkun)) {
                                        foreach ($dataKelompokAkun as $kelompokAkun) {
                                    ?>
                                            <option value="<?= $kelompokAkun->id; ?>"><?= $kelompokAkun->value; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Kelompok Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control kode_akun_kategori" id="kode_akun_kategori" name="kode_akun_kategori" placeholder="Kode Akun">
                                <label for="floatingInput">Kode Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nama_akun_kategori" id="nama_akun_kategori" name="nama_akun_kategori" placeholder="Nama Akun">
                                <label for="floatingInput">Nama Akun</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard delete-btn delete-btn-kategori">Delete</button>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-form-kategori btn-discard mr-3">Discard</button>
                    <button type="submit" class="btn btn-submit-form">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal add-modal-header" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal-title-header"><label class="title-name-header"></label> Header Akun</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-header" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_header" name="id_header" id="id_header" />
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kelompok_akun_id_header" name="kelompok_akun_id_header" id="kelompok_akun_id_header">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataKelompokAkun)) {
                                        foreach ($dataKelompokAkun as $kelompokAkun) {
                                    ?>
                                            <option value="<?= $kelompokAkun->id; ?>"><?= $kelompokAkun->value; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Kelompok Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control kode_akun_header" id="kode_akun_header" name="kode_akun_header" placeholder="Kode Akun">
                                <label for="floatingInput">Kode Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nama_akun_header" id="nama_akun_header" name="nama_akun_header" placeholder="Nama Akun">
                                <label for="floatingInput">Nama Akun</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard delete-btn delete-btn-header">Delete</button>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-form-header btn-discard mr-3">Discard</button>
                    <button type="submit" class="btn btn-submit-form">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal add-modal-sub" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal-title-sub"><label class="title-name-sub"></label> Sub Akun</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-sub" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_sub" name="id_sub" id="id_sub" />
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kelompok_akun_id_sub" name="kelompok_akun_id_sub" id="kelompok_akun_id_sub">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataKelompokAkun)) {
                                        foreach ($dataKelompokAkun as $kelompokAkun) {
                                    ?>
                                            <option value="<?= $kelompokAkun->id; ?>"><?= $kelompokAkun->value; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Kelompok Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control kode_akun_sub" id="kode_akun_sub" name="kode_akun_sub" placeholder="Kode Akun">
                                <label for="floatingInput">Kode Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nama_akun_sub" id="nama_akun_sub" name="nama_akun_sub" placeholder="Nama Akun">
                                <label for="floatingInput">Nama Akun</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard delete-btn delete-btn-sub">Delete</button>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-form-sub btn-discard mr-3">Discard</button>
                    <button type="submit" class="btn btn-submit-form">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>


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
                <div>
                    <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Account</h4>
                    <button class="btn btn-show-form-kategori btn-add btn-block float-right" data-btn="create-modal" style="margin-top: -40px; width: 176px;">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add New
                    </button>
                </div>
                <div class="mb-2">
                    <input class="form-control search-kategori float-right" placeholder="Search" style="width: 30%" value="" />
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi kategoriDataTable" id="kategoriDataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kelompok Akun</th>
                                <th>No. Kategori Akun</th>
                                <th>Nama Kategori Akun</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="header" role="tabpanel" aria-labelledby="header-tab">
            <div class="collapse-header-list show" id="collapseHeaderList">
                <div>
                    <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Account</h4>
                    <button class="btn btn-show-form-header btn-add btn-block float-right" data-btn="create-modal" style="margin-top: -40px; width: 176px;">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add New
                    </button>
                </div>
                <div class="mb-2">
                    <input class="form-control search-header float-right" placeholder="Search" style="width: 30%" value="" />
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi headerDataTable" id="headerDataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kelompok Akun</th>
                                <th>No. Kategori Akun</th>
                                <th>Nama Kategori Akun</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="sub" role="tabpanel" aria-labelledby="sub-tab">
            <div class="collapse-header-list show" id="collapseSubList">
                <div>
                    <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Account</h4>
                    <button class="btn btn-show-form-sub btn-add btn-block float-right" data-btn="create-modal" style="margin-top: -40px; width: 176px;">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add New
                    </button>
                </div>
                <div class="mb-2">
                    <input class="form-control search-sub float-right" placeholder="Search" style="width: 30%" value="" />
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi subDataTable" id="subDataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kelompok Akun</th>
                                <th>No. Kategori Akun</th>
                                <th>Nama Kategori Akun</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.kelompok_akun_id_kategori').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal-kategori .modal-content")
        })

        $('.kelompok_akun_id_header').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal-header .modal-content")
        })

        $('.kelompok_akun_id_sub').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal-sub .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".kelompok_akun_id_kategori")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

        $(".kelompok_akun_id_kategori")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

        $(".kelompok_akun_id_kategori")
        .parent('div')
        .find('label')
        .css('z-index', '1');

        $(".kelompok_akun_id_header")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

        $(".kelompok_akun_id_header")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

        $(".kelompok_akun_id_header")
        .parent('div')
        .find('label')
        .css('z-index', '1');

        $(".kelompok_akun_id_sub")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

        $(".kelompok_akun_id_sub")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

        $(".kelompok_akun_id_sub")
        .parent('div')
        .find('label')
        .css('z-index', '1');

        $(".btn-show-form-kategori").click(function() {
            $(".title-name-kategori").text("Add New");

            $(".delete-btn-kategori").css('display', 'none');
            $(".add-modal-kategori").modal("show")
        })

        $(".btn-show-form-header").click(function() {
            $(".title-name-header").text("Add New");
            
            $(".delete-btn-header").css('display', 'none');
            $(".add-modal-header").modal("show")
        })

        $(".btn-show-form-sub").click(function() {
            $(".title-name-sub").text("Add New");
            
            $(".delete-btn-sub").css('display', 'none');
            $(".add-modal-sub").modal("show")
        })

        $(".btn-hide-form-kategori").click(function() {
            $(".add-modal-kategori").modal("hide")
        })

        $(".btn-hide-form-header").click(function() {
            $(".add-modal-header").modal("hide")
        })

        $(".btn-hide-form-sub").click(function() {
            $(".add-modal-sub").modal("hide")
        })
    })

    // REMOVE ALL OPEN FORM
    const removeAllTab = function() {
        $(".collapse-kategori-list").addClass("show")
        $(".collapse-header-list").addClass("show")
        $(".collapse-sub-list").addClass("show")
    }
</script>

<?= $this->endSection(); ?>
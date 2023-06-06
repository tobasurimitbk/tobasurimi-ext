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
                                            <option value="<?= $kelompokAkun->value; ?>"><?= $kelompokAkun->value; ?></option>
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
                    <button type="submit" class="btn btn-submit-form btn-submit-form-kategori">Save</button>
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
                                    if (!empty($dataKelompokAkunKategori)) {
                                        foreach ($dataKelompokAkunKategori as $kelompokAkunKategori) {
                                    ?>
                                            <option value="<?= $kelompokAkunKategori->id; ?>"><?= $kelompokAkunKategori->nama_kategori; ?></option>
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
                    <button type="submit" class="btn btn-submit-form btn-submit-form-header">Save</button>
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
                                            <option value="<?= $kelompokAkun->value; ?>"><?= $kelompokAkun->value; ?></option>
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
    const csrfToken = '<?= csrf_token() ?>';

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

        var validator_kategori = $(".create-form-kategori").validate({
            rules: {
                kelompok_akun_id_kategori: {
                    required: true
                },
                kode_akun_kategori: {
                    required: true
                },
                nama_akun_kategori: {
                    required: true
                }
            },
            messages: {
                kelompok_akun_id_kategori: {
                    required: "Kelompok Akun is Required"
                },
                kode_akun_kategori: {
                    required: "Kode Akun is Required"
                },
                nama_akun_kategori: {
                    required: "Nama Akun is Required"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        var validator_header = $(".create-form-header").validate({
            rules: {
                kelompok_akun_id_header: {
                    required: true
                },
                kode_akun_header: {
                    required: true
                },
                nama_akun_header: {
                    required: true
                }
            },
            messages: {
                kelompok_akun_id_header: {
                    required: "Kelompok Akun is Required"
                },
                kode_akun_header: {
                    required: "Kode Akun is Required"
                },
                nama_akun_header: {
                    required: "Nama Akun is Required"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        const kategoriTable = $('.kategoriDataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            serverSide: true,
            ordering: false,
            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            ajax: {
                url: "<?= base_url("kategori-account/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search-kategori").val();
                }
            },
            // scrollX: true,
            "initComplete": function (settings, json) {    
                $('.dataTables_length').empty();    
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show 25 Entries</label></div>"); 
                $('.kategoriDataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            //responsive: true,
            display: "stripe",
            searching: false,
            columns: [{
                data: "kategori_akun",
                className: "text-left"
            },
            {
                data: "no_kategori",
                className: "text-left"
            },
            {
                data: "nama_kategori",
                className: "text-left"
            }],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        const headerTable = $('.headerDataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            serverSide: true,
            ordering: false,
            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            ajax: {
                url: "<?= base_url("header-account/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search-header").val();
                }
            },
            // scrollX: true,
            "initComplete": function (settings, json) {    
                $('.dataTables_length').empty();    
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show 25 Entries</label></div>"); 
                $('.headerDataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            //responsive: true,
            display: "stripe",
            searching: false,
            columns: [{
                data: "header_akun",
                className: "text-left"
            },
            {
                data: "no_header",
                className: "text-left"
            },
            {
                data: "nama_header",
                className: "text-left"
            }],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        $(".dataTable_info").addClass("pt-0");

        $('#kategoriDataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = kategoriTable.row(this).data();
            $(".create-form-kategori")[0].reset()
            $(".delete-btn-kategori").css('display', '');
            let id = data.id;
            $(".title-name-kategori").text("Update");

            $.ajax({
                url: "<?= base_url("kategori-account/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id_kategori").val(id);
                        $(".kelompok_akun_id_kategori").val(res?.data?.kategori_akun).change();
                        $(".kode_akun_kategori").val(res?.data?.no_kategori);
                        $(".nama_akun_kategori").val(res?.data?.nama_kategori);

                        validator_kategori.resetForm();
                        validator_kategori.reset();
                        $(".add-modal-kategori").modal("show")
                        console.log(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $('#headerDataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = headerTable.row(this).data();
            $(".create-form-header")[0].reset()
            $(".delete-btn-header").css('display', '');
            let id = data.id;
            $(".title-name-header").text("Update");

            $.ajax({
                url: "<?= base_url("header-account/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id_header").val(id);
                        $(".kelompok_akun_id_header").val(res?.data?.header_akun).change();
                        $(".kode_akun_header").val(res?.data?.no_header);
                        $(".nama_akun_header").val(res?.data?.nama_header);

                        validator_header.resetForm();
                        validator_header.reset();
                        $(".add-modal-header").modal("show")
                        console.log(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $(".search-kategori").keyup(function () {
            kategoriTable.ajax.reload();
        })

        $(".search-header").keyup(function () {
            headerTable.ajax.reload();
        })

        $(".btn-show-form-kategori").click(function() {
            $(".id_kategori").val("");

            $(".title-name-kategori").text("Add New");
            $(".kelompok_akun_id_kategori").val('').change();

            validator_kategori.resetForm();
            validator_kategori.reset();
            $(".create-form-kategori")[0].reset()

            $(".delete-btn-kategori").css('display', 'none');
            $(".add-modal-kategori").modal("show")
        })

        $(".btn-show-form-header").click(function() {
            $(".id_header").val("");

            $(".title-name-header").text("Add New");
            $(".kelompok_akun_id_header").val('').change();

            validator_header.resetForm();
            validator_header.reset();
            $(".create-form-header")[0].reset()

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

        $(".btn-submit-form-kategori").click(function() {
            if ($(".create-form-kategori").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        let data = new FormData(document.querySelector(".create-form-kategori"));

                        let id = $(".id_kategori").val();
                        // UPDATE
                        if(id)
                        {
                            $.ajax({
                                url: "<?= base_url("kategori-account/update"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        stopLoading()
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            $(".add-modal-kategori").modal("hide")
                                            $(".create-form-kategori")[0].reset()
                                            $(".kelompok_akun_id_kategori").val("").change()

                                            kategoriTable.ajax.reload()
                                        })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        }
                        // CREATE
                        else
                        {
                            $.ajax({
                                url: "<?= base_url("kategori-account/save"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        stopLoading()
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            $(".add-modal-kategori").modal("hide")
                                            $(".create-form-kategori")[0].reset()
                                            $(".kelompok_akun_id_kategori").val("").change()

                                            kategoriTable.ajax.reload()
                                        })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        }
                    }
                })
            }
        })

        $(".btn-submit-form-header").click(function() {
            if ($(".create-form-header").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        let data = new FormData(document.querySelector(".create-form-header"));

                        let id = $(".id_header").val();
                        // UPDATE
                        if(id)
                        {
                            $.ajax({
                                url: "<?= base_url("header-account/update"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        stopLoading()
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            $(".add-modal-header").modal("hide")
                                            $(".create-form-header")[0].reset()
                                            $(".kelompok_akun_id_header").val("").change()

                                            headerTable.ajax.reload()
                                        })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        }
                        // CREATE
                        else
                        {
                            $.ajax({
                                url: "<?= base_url("header-account/save"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        stopLoading()
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            $(".add-modal-header").modal("hide")
                                            $(".create-form-header")[0].reset()
                                            $(".kelompok_akun_id_header").val("").change()

                                            headerTable.ajax.reload()
                                        })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        }
                    }
                })
            }
        })

        $(".delete-btn-kategori").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id_kategori").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("kategori-account/delete"); ?>",
                        data: {
                            id: id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    $(".add-modal-kategori").modal("hide")
                                    $(".create-form-kategori")[0].reset()
                                    $(".kelompok_akun_id_kategori").val("").change()

                                    kategoriTable.ajax.reload()
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })
        
        $(".delete-btn-header").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id_header").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("header-account/delete"); ?>",
                        data: {
                            id: id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    $(".add-modal-header").modal("hide")
                                    $(".create-form-header")[0].reset()
                                    $(".kelompok_akun_id_header").val("").change()

                                    headerTable.ajax.reload()
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
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
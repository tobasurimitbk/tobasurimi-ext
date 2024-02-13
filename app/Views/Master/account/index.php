<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal-kategori" id="add_modal_kategori" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal-title-kategori"><label class="title-name-kategori"></label> Kategori Akun</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-kategori" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_kategori" name="id_kategori" id="id_kategori" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kelompok_akun_id_kategori" name="kelompok_akun_id_kategori" id="kelompok_akun_id_kategori">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Kelompok Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control kode_akun_kategori" id="kode_akun_kategori" name="kode_akun_kategori" placeholder="Kode Akun">
                                <label for="floatingInput">Kode Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_akun_kategori" id="nama_akun_kategori" name="nama_akun_kategori" placeholder="Nama Akun">
                                <label for="floatingInput">Nama Akun</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form-kategori btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-form-kategori">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-btn-kategori">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal add-modal-header" id="add_modal_header" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal-title-header"><label class="title-name-header"></label> Header Akun</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-header" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_header" name="id_header" id="id_header" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select category_id_header" name="category_id_header" id="category_id_header">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Kategori Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control kode_akun_header" id="kode_akun_header" name="kode_akun_header" placeholder="Kode Akun">
                                <label for="floatingInput">Kode Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_akun_header" id="nama_akun_header" name="nama_akun_header" placeholder="Nama Akun">
                                <label for="floatingInput">Nama Akun</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form-header btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-form-header">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-btn-header">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal add-modal-sub" id="add_modal_sub" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal-title-sub"><label class="title-name-sub"></label> Sub Akun</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-sub" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_sub" name="id_sub" id="id_sub" />
                    <input autocomplete="one-time-code" type="hidden" class="category_id_sub" name="category_id_sub" id="category_id_sub" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select header_id_sub" name="header_id_sub" id="header_id_sub">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Header Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control kode_akun_sub" id="kode_akun_sub" name="kode_akun_sub" placeholder="Kode Akun">
                                <label for="floatingInput">Kode Sub Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_akun_sub" id="nama_akun_sub" name="nama_akun_sub" placeholder="Nama Akun">
                                <label for="floatingInput">Nama Sub Akun</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select coa_id_sub" name="coa_id_sub" id="coa_id_sub">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">COA</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3" style="height: 50px;">
                                <label for="floatingInput">Status</label>
                                <div>
                                    <label class="switch">
                                        <input autocomplete="one-time-code" class="status_sub" name="status_sub" id="status_sub" type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form-sub btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-form-sub">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-btn-sub">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Account</h1>
        <!-- Navigation -->
        <ul class="nav nav-tabs float-right" id="myTab" role="tablist">
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
    </div>
    <div class="card">
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="kategori" role="tabpanel" aria-labelledby="kategori-tab">
                    <div class="collapse-kategori-list show" id="collapseKategoriList">
                        <div class="d-flex float-right mb-3">
                            <input autocomplete="one-time-code" class="form-control search search-kategori form-out-search mr-3" placeholder="Search" />
                            <button class="btn btn-show-form btn-add float-right btn-show-form-kategori" data-btn="create-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi kategoriDataTable" id="kategoriDataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th onclick="changeSortKategori('kelompok_akun')" class="sort">Kelompok Akun</th>
                                        <th onclick="changeSortKategori('no_kategori')" class="sort">No. Kategori Akun</th>
                                        <th onclick="changeSortKategori('nama_kategori')" class="sort">Nama Kategori Akun</th>
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
                        <div class="d-flex float-right mb-3">
                            <input autocomplete="one-time-code" class="form-control search cari-header form-out-search mr-3" placeholder="Search" />
                            <button class="btn btn-show-form btn-add float-right btn-show-form-header" data-btn="create-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi headerDataTable" id="headerDataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th onclick="changeSortHeader('nama_kategori')" class="sort">Kategori Akun</th>
                                        <th onclick="changeSortHeader('no_header')" class="sort">No. Header Akun</th>
                                        <th onclick="changeSortHeader('nama_header')" class="sort">Nama Header Akun</th>
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
                        <div class="d-flex float-right mb-3 col-nav-tabs-contents-input-btn">
                            <input autocomplete="one-time-code" class="form-control search search-sub form-out-search mr-3" placeholder="Search" />
                            <select class="form-select status status-sub mr-3 form-select-no-title" name="status" id="status" aria-label="Floating label select example">
                                <option value="Aktif">Aktif</option>
                                <option value="Void">Void</option>
                            </select>
                            <button class="btn btn-show-form btn-add float-right btn-show-form-sub" data-btn="create-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi subDataTable" id="subDataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th onclick="changeSortSub('nama_kategori')" class="sort">Kategori Akun</th>
                                        <th onclick="changeSortSub('no_header')" class="sort">No. Header Akun</th>
                                        <th onclick="changeSortSub('nama_header')" class="sort">Header Akun</th>
                                        <th onclick="changeSortSub('no_sub')" class="sort">No. Sub Akun</th>
                                        <th onclick="changeSortSub('nama_sub')" class="sort">Nama Sub Akun</th>
                                        <th onclick="changeSortSub('akun_coa')" class="sort">COA</th>
                                        <th>Status</th>
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
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sortKategori = "kelompok_akun";
    let sortTypeKategori = "asc";
    let sortHeader = "nama_kategori";
    let sortTypeHeader = "asc";
    let sortSub = "nama_kategori";
    let sortTypeSub = "asc";

    const kategoriTable = $('.kategoriDataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
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
                data.sort = sortKategori;
                data.sortType = sortTypeKategori;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.kategoriDataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            }, {
                data: "kelompok_akun",
                className: "text-center"
            },
            {
                data: "no_kategori",
                className: "text-center"
            },
            {
                data: "nama_kategori",
                className: "text-center"
            }
        ],
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
        ordering: true,
        order: [
            [1, 'asc']
        ],
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
                data.search = $(".cari-header").val();
                data.sort = sortHeader;
                data.sortType = sortTypeHeader;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.headerDataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false
            }, {
                data: "nama_kategori",
                className: "text-center"
            },
            {
                data: "no_header",
                className: "text-center"
            },
            {
                data: "nama_header",
                className: "text-center"
            }
        ],
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

    const subTable = $('.subDataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("sub-account/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search-sub").val();
                data.status_sub = $(".status-sub").val();
                data.status = $(".status").val();
                data.sort = sortSub;
                data.sortType = sortTypeSub;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.subDataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false
            }, {
                data: "nama_kategori",
                className: "text-center"
            },
            {
                data: "no_header",
                className: "text-center"
            },
            {
                data: "nama_header",
                className: "text-center"
            },
            {
                data: "no_sub",
                className: "text-center"
            },
            {
                data: "nama_sub",
                className: "text-center"
            },
            {
                data: "akun_coa",
                className: "text-center"
            },
            {
                data: "status",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row?.id;
                    return `
                <div class="mt-0">
                <label class="switch">
                <input autocomplete="one-time-code" class="status_table" id=${"status_table_" + id} onchange="changeStatus('${id}')" name="status_table" id="status_table" type="checkbox" ${data === "Aktif" ? 'checked' : ''}>
                <span class="slider round"></span>
                </label>
                </div>
                `
                }
            }
        ],
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

    $(document).ready(function() {
        const select2kelompok_akun = {
            dropdownParent: $("#add_modal_kategori"),
            ajax: {
                delay: 300,
                url: `<?= base_url("metadata/dropdown1"); ?>`,
                dataType: 'json',
                data: function(params) {
                    return {
                        name: 'kelompok akun',
                        search: params.term,
                        page: params.page || 1
                    }
                }
            }
        };

        const select2categori_id_header = {
            dropdownParent: $("#add_modal_header"),
            ajax: {
                delay: 300,
                url: `<?= base_url("kategori-account/dropdown"); ?>`,
                dataType: 'json',
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    }
                }
            }
        };

        const select2header_id_sub = {
            dropdownParent: $("#add_modal_sub"),
            ajax: {
                delay: 300,
                url: `<?= base_url("header-account/dropdown"); ?>`,
                dataType: 'json',
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    }
                }
            }
        };

        $('.kelompok_akun_id_kategori').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal-kategori .modal-content")
        })

        $('.category_id_header').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal-header .modal-content")
        })

        $('.header_id_sub').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal-sub .modal-content")
        })

        $('.coa_id_sub').select2({
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

        $(".category_id_header")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".category_id_header")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".category_id_header")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".header_id_sub")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".header_id_sub")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".header_id_sub")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".coa_id_sub")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".coa_id_sub")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".coa_id_sub")
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
                    required: "Kelompok Akun wajib diisi"
                },
                kode_akun_kategori: {
                    required: "Kode Akun wajib diisi"
                },
                nama_akun_kategori: {
                    required: "Nama Akun wajib diisi"
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
                category_id_header: {
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
                category_id_header: {
                    required: "Kelompok Akun wajib diisi"
                },
                kode_akun_header: {
                    required: "Kode Akun wajib diisi"
                },
                nama_akun_header: {
                    required: "Nama Akun wajib diisi"
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

        var validator_sub = $(".create-form-sub").validate({
            rules: {
                header_id_sub: {
                    required: true
                },
                coa_id_sub: {
                    required: true
                },
                kode_akun_sub: {
                    required: true
                },
                nama_akun_sub: {
                    required: true
                }
            },
            messages: {
                header_id_sub: {
                    required: "Header Akun wajib diisi"
                },
                coa_id_sub: {
                    required: "COA wajib diisi"
                },
                kode_akun_sub: {
                    required: "Kode Sub Akun wajib diisi"
                },
                nama_akun_sub: {
                    required: "Nama Sub Akun wajib diisi"
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
                        $(".kode_akun_kategori").val(res?.data?.no_kategori);
                        $(".nama_akun_kategori").val(res?.data?.nama_kategori);

                        validator_kategori.resetForm();
                        validator_kategori.reset();

                        // $.ajax({
                        //     url: `<?= base_url("metadata/dropdown"); ?>`,
                        //     method: "GET",
                        //     data: {
                        //         name: 'kelompok_akun'
                        //     },
                        //     dataType: "json",
                        //     success: function(result) {
                        //         $(".kelompok_akun_id_kategori").empty()
                        //         $(".kelompok_akun_id_kategori").append(`<option value=""></option>`)
                        //         result.data.forEach(function(item) {
                        //             $(".kelompok_akun_id_kategori").append(`<option value="${item.id}">${item.value}</option>`)
                        //         })

                        //         $(".kelompok_akun_id_kategori").val(res?.data?.kelompok_id).change();
                        //         $(".add-modal-kategori").modal("show")
                        //     }
                        // })
                        $('.kelompok_akun_id_kategori').select2(select2kelompok_akun);
                        const $apOption = $("<option selected='selected'></option>").val(res?.data?.kelompok_id).text(res?.data?.meta_name);
                        $(".kelompok_akun_id_kategori").append($apOption).trigger('change');
                        $(".add-modal-kategori").modal("show")

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
                        $(".kode_akun_header").val(res?.data?.no_header);
                        $(".nama_akun_header").val(res?.data?.nama_header);

                        validator_header.resetForm();
                        validator_header.reset();

                        $('.category_id_header').select2(select2categori_id_header);

                        const $apOption = $("<option selected='selected'></option>").val(res.data.kategori_id).text(res?.data?.nama_kategori);
                        $(".category_id_header").append($apOption).trigger('change');
                        $(".add-modal-header").modal("show")
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

        $('#subDataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = subTable.row(this).data();
            $(".create-form-sub")[0].reset()
            $(".delete-btn-sub").css('display', '');
            let id = data.id;
            $(".title-name-sub").text("Update");

            $.ajax({
                url: "<?= base_url("sub-account/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id_sub").val(id);
                        $(".category_id_sub").val(res?.data?.kategori_id);
                        $(".kode_akun_sub").val(res?.data?.no_sub);
                        $(".nama_akun_sub").val(res?.data?.nama_sub);
                        $(".status_sub").prop("checked", res?.data?.status === "Aktif" ? true : false);

                        validator_sub.resetForm();
                        validator_sub.reset();

                        // $.ajax({
                        //     url: `<?= base_url("header-account/dropdown"); ?>`,
                        //     method: "GET",
                        //     dataType: "json",
                        //     success: function(result) {
                        //         $(".header_id_sub").empty()
                        //         $(".header_id_sub").append(`<option value=""></option>`)
                        //         result.data.forEach(function(item) {
                        //             $(".header_id_sub").append(`<option data-kategori="${item.kategori_id}" value="${item.id}">${item.nama_header}</option>`)
                        //         })

                        //         $(".header_id_sub").val(res?.data?.header_id).change();
                        //     }
                        // })
                        $('.header_id_sub').select2(select2header_id_sub);
                        const $apOption = $("<option selected='selected'></option>").val(res?.data?.header_id).text(res?.data?.nama_header);
                        $(".header_id_sub").append($apOption).trigger('change');

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                name: 'akun_coa'
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".coa_id_sub").empty()
                                $(".coa_id_sub").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".coa_id_sub").append(`<option value="${item.id}">${item.value}</option>`)
                                })

                                $(".coa_id_sub").val(res?.data?.coa_id).change();
                                $(".add-modal-sub").modal("show")
                            }
                        })
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

        $(".search-kategori").keyup(function() {
            kategoriTable.ajax.reload();
        })

        $(".cari-header").keyup(function() {
            headerTable.ajax.reload();
        })

        $(".search-sub").keyup(function() {
            subTable.ajax.reload();
        })

        $(".status").change(function() {
            subTable.ajax.reload();
        })

        $(".header_id_sub").on("change", function() {
            let dataKategori = $(".header_id_sub option:selected").data("kategori");
            if (dataKategori) {
                document.getElementById("category_id_sub").value = dataKategori;
            } else {
                document.getElementById("category_id_sub").value = "";
            }
        })

        $(".btn-show-form-kategori").click(function() {
            $(".id_kategori").val("");

            $(".title-name-kategori").text("Tambah");
            //$(".kelompok_akun_id_kategori").val('').change();

            validator_kategori.resetForm();
            validator_kategori.reset();
            $(".create-form-kategori")[0].reset()

            // $.ajax({
            //     url: `<?= base_url("metadata/dropdown1"); ?>`,
            //     method: "GET",
            //     data: {
            //         name: 'kelompok_akun'
            //     },
            //     dataType: "json",
            //     success: function(res) {
            //         $(".kelompok_akun_id_kategori").empty()
            //         $(".kelompok_akun_id_kategori").val('').change();
            //         $(".kelompok_akun_id_kategori").append(`<option value=""></option>`)
            //         res.data.forEach(function(item) {
            //             $(".kelompok_akun_id_kategori").append(`<option value="${item.id}">${item.value}</option>`)
            //         })
            //         $(".delete-btn-kategori").css('display', 'none');
            //         $(".add-modal-kategori").modal("show")
            //     }
            // })
            $(".delete-btn-kategori").css('display', 'none');
            $('.kelompok_akun_id_kategori').select2(select2kelompok_akun);
            $(".add-modal-kategori").modal("show");
        })

        $(".btn-show-form-header").click(function() {
            $(".id_header").val("");

            $(".title-name-header").text("Tambah");

            validator_header.resetForm();
            validator_header.reset();
            $(".create-form-header")[0].reset()

            $(".delete-btn-header").css('display', 'none');
            $('.category_id_header').select2(select2categori_id_header);
            $(".add-modal-header").modal("show");
        })

        $(".btn-show-form-sub").click(function() {
            $(".id_sub").val("");

            $(".title-name-sub").text("Tambah");

            validator_sub.resetForm();
            validator_sub.reset();
            $(".create-form-sub")[0].reset()

            $(".status_sub").prop("checked", true);

            // $.ajax({
            //     url: `<?= base_url("header-account/dropdown"); ?>`,
            //     method: "GET",
            //     dataType: "json",
            //     success: function(res) {
            //         $(".header_id_sub").empty()
            //         $(".header_id_sub").val('').change();
            //         $(".header_id_sub").append(`<option value=""></option>`)
            //         res.data.forEach(function(item) {
            //             $(".header_id_sub").append(`<option data-kategori="${item.kategori_id}" value="${item.id}">${item.nama_header}</option>`)
            //         })
            //     }
            // })
            $('.header_id_sub').select2(select2header_id_sub);

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'akun_coa'
                },
                dataType: "json",
                success: function(result) {
                    $(".coa_id_sub").empty()
                    $(".coa_id_sub").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".coa_id_sub").append(`<option value="${item.id}">${item.value}</option>`)
                    })
                    $(".delete-btn-sub").css('display', 'none');

                }
            })
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
                        if (id) {
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
                        else {
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
                        if (id) {
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
                                                $(".category_id_header").val("").change()

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
                        else {
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
                                                $(".category_id_header").val("").change()

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

        $(".btn-submit-form-sub").click(function() {
            if ($(".create-form-sub").valid()) {
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
                        let data = new FormData(document.querySelector(".create-form-sub"));

                        let id = $(".id_sub").val();
                        // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("sub-account/update"); ?>",
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
                                                $(".add-modal-sub").modal("hide")
                                                $(".create-form-sub")[0].reset()
                                                $(".header_id_sub").val("").change()

                                                subTable.ajax.reload()
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
                        else {
                            $.ajax({
                                url: "<?= base_url("sub-account/save"); ?>",
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
                                                $(".add-modal-sub").modal("hide")
                                                $(".create-form-sub")[0].reset()
                                                $(".header_id_sub").val("").change()

                                                subTable.ajax.reload()
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
                                        $(".category_id_header").val("").change()

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

        $(".delete-btn-sub").click(function() {
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
                    let id = $(".id_sub").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("sub-account/delete"); ?>",
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
                                        $(".add-modal-sub").modal("hide")
                                        $(".create-form-sub")[0].reset()
                                        $(".header_id_sub").val("").change()

                                        subTable.ajax.reload()
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

    const changeStatus = function(id) {
        const csrf = $(`[name="${csrfToken}"]`);
        let value = document.getElementById('status_table_' + id).checked ? true : false;

        let data = {
            id: id
        }

        if (value) {
            data["status"] = true;
        }

        $.ajax({
            url: "<?= base_url("sub-account/update-status"); ?>",
            data: data,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            success: function(response) {
                csrf.val(response.token);
                if (response.status) {
                    Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                        .then(() => {
                            subTable.ajax.reload()
                        })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    })
                }
            },
            onError: function(response) {
                csrf.val(response.token);
                Swal.fire({
                    icon: 'error',
                    title: 'Data Gagal Disimpan, coba Lagi',
                    confirmButtonColor: '#4e73df',
                })
            }
        });
    }

    const changeSortKategori = function(val) {
        if (sortKategori !== val) {
            sortTypeKategori = "asc";
            sortKategori = val;
        } else {
            sortTypeKategori = sortTypeKategori === "asc" ? "desc" : "asc";
        }
    }

    const changeSortHeader = function(val) {
        if (sortHeader !== val) {
            sortTypeHeader = "asc";
            sortHeader = val;
        } else {
            sortTypeHeader = sortTypeHeader === "asc" ? "desc" : "asc";
        }
    }

    const changeSortSub = function(val) {
        if (sortSub !== val) {
            sortTypeSub = "asc";
            sortSub = val;
        } else {
            sortTypeSub = sortTypeSub === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>
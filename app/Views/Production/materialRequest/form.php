<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah Material Request</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("material-request"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-form">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" value="<?= !empty($dataWorkOrders) ? $dataWorkOrders->id : ""; ?>" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Data Produksi</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kode_produksi" name="kode_produksi" id="kode_produksi" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataWorkOrder ?? [] as $dataWO) : ?>
                                    <option value="<?= $dataWO->id ?>" data-nama-barang="<?= $dataWO->nama_barang ?>" data-standart-production="<?= $dataWO->standart_production ?>"><?= $dataWO->wo_no ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Kode Produksi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control barang_jadi" name="barang_jadi" id="barang_jadi" placeholder="Barang Jadi" readonly>
                            <label for="floatingInput">Barang Jadi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control standart_production" name="standart_production" id="standart_production" placeholder="Jumlah Standart Produksi" readonly>
                            <label for="floatingInput">Jumlah Standart Produksi</label>
                        </div>
                    </div>
                </div>
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Header Request</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control date_request" name="date_request" id="date_request" placeholder="Tanggal Request">
                            <label for="floatingInput">Tanggal Request</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control date_production" name="date_production" id="date_production" placeholder="Tanggal Produksi">
                            <label for="floatingInput">Tanggal Produksi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control req_no" id="req_no" name="req_no" placeholder="Kode Produksi">
                                    <label for="floatingInput">Kode Request</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select department_id" name="department_id" id="department_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataDivisi ?? [] as $dataDivisi) : ?>
                                    <option value="<?= $dataDivisi['id'] ?>"><?= $dataDivisi['divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Department</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select warehouse_id" name="warehouse_id" id="warehouse_id" disabled>
                                <option value=""></option>
                            </select>
                            <label for="floatingInput">Warehouse</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">Data Barang</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select select_tipe_bahan" name="select_tipe_bahan" id="select_tipe_bahan" disabled>
                            <option value=""></option>
                            <?php foreach ($tipeBarang as $t) : ?>
                                <option value="<?= $t['description'] ?>">
                                    <?= strtoupper($t['value']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Tipe Bahan</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select select_nama_barang" name="select_nama_barang" id="select_nama_barang" disabled>
                            <option value=""></option>
                        </select>
                        <label for="floatingInput">Nama Barang</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">#</th>
                                <th style="text-align: center;">Tipe Barang</th>
                                <th style="text-align: center;">Dokumen Pabean</th>
                                <th style="text-align: center;">No Aju</th>
                                <th style="text-align: center;">Barang - Spesifikasi</th>
                                <th style="text-align: center;">Satuan</th>
                                <th style="text-align: center;">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="body-table">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <button class="btn btn-show-detail btn-add btn-submit-barang" data-btn="detail-modal" id="select-item-btn">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah Barang
                    </button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="selectedItemTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th style="text-align: center;">Tipe Barang</th>
                                <th style="text-align: center;">Dokumen Pabean</th>
                                <th style="text-align: center;">No Aju</th>
                                <th style="text-align: center;">Barang - Spesifikasi</th>
                                <th style="text-align: center;">Satuan</th>
                                <th style="text-align: center;">Qty Awal</th>
                                <th style="text-align: center;">Qty Digunakan</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    let list_items = [];
    var listStockSelected = [];

    let sortDataBarang = "createdAt";
    let sortTypeDataBarang = "DESC";

    $(document).ready(function() {
        const dataTableBarang = $('.dataTableBarang').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            serverSide: true,
            ordering: true,
            order: [
                [4, 'desc']
            ],
            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            ajax: {
                url: "<?= base_url("material-request/data-barang"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.bc_id = "";
                    data.no_aju = "";
                    data.id = $("#select_nama_barang option:selected").val() ? $("#select_nama_barang option:selected").val() : "";
                    data.spek_id = $("#select_nama_barang option:selected").data("spesifikasi_id") ? $("#select_nama_barang option:selected").data("spesifikasi_id") : "";

                    data.sort = sortDataBarang;
                    data.sortType = sortTypeDataBarang;
                },
                beforeSend: function() {
                    $.LoadingOverlay("show", {
                        image: "",
                        fontawesomeColor: "#222FCC",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
                complete: function() {
                    $.LoadingOverlay("hide", {
                        image: "",
                        fontawesomeColor: "#222FCC",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                    data: "no",
                    className: "text-center",
                    searchable: false,
                    sortable: false,
                    render: function(data, type, full, meta) {
                        // Return a checkbox input
                        return '<input type="checkbox" class="checkbox_no" value="' + data.no + '" data-stock_id="' + data.stock_id + '">';
                    }
                },
                {
                    data: "dokumen",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "stock",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "warehouse",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "kode",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "barang",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "satuan",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "qty",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                }, {
                    data: "no",
                    className: "text-center",
                    searchable: false,
                    sortable: false,
                    render: function(data, type, full, meta) {
                        return '<input type="text" class="form-control jumlahBarang" id="jumlahBarang" name="jumlahBarang" value="">';
                    }
                }, {
                    data: "no",
                    className: "text-center",
                    searchable: false,
                    sortable: false,
                    render: function(data, type, full, meta) {
                        return '<input type="text" class="form-control ketBarang" id="ketBarang" name="ketBarang" value="">';
                    }
                },
            ],
            "drawCallback": function(settings) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            },
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
        // Departemen
        $('.department_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        }).change(function() {
            getListWarehouseAsal();
            $('.select_tipe_bahan').val(null).change();
        });

        //CSS SELECT2 FLOATING LABEL
        $('.department_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.department_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.department_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // select tipe bahan
        $('.select_tipe_bahan').select2({
            placeholder: "Pilih Tipe Bahan",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            getListBarang();
        });

        //CSS SELECT2 FLOATING LABEL
        $('.select_tipe_bahan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.select_tipe_bahan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.select_tipe_bahan')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // select nama barang
        $('.select_nama_barang').select2({
            placeholder: "Pilih Barang",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            getListDokumenPabean();
        });

        //CSS SELECT2 FLOATING LABEL
        $('.select_nama_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.select_nama_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.select_nama_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Kode Produksi
        $('.kode_produksi').select2({
            placeholder: "Pilih kode Produksi",
            theme: "bootstrap-5",
            allowClear: true
        });

        //CSS SELECT2 FLOATING LABEL
        $('.kode_produksi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_produksi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_produksi')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PILIH TIPE Warehouse
        $('.warehouse_id').select2({
            placeholder: "Pilih Warehouse",
            theme: "bootstrap-5"
        }).change(function() {
            $("#select_tipe_bahan").prop('disabled', false);
            $("#select_nama_barang").prop('disabled', false);
        });

        $('.warehouse_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.warehouse_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.warehouse_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KODE BARANG
        $('.kode_barang').select2({
            placeholder: "Pilih Kode Barang",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            tags: false,
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Mengaktifkan datepicker
        $('#date_production').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd/mm/yyyy'
        });
        $('#date_request').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd/mm/yyyy'
        });

        $(".select_nama_barang").change(function() {
            dataTableBarang.ajax.reload();
        })

        var validator_detail = $(".detail-form").validate({
            rules: {
                kode_barang: {
                    required: true
                },
                nama_barang: {
                    required: true
                },
                qty: {
                    required: true
                },
                harga: {
                    required: true
                },
                satuan: {
                    required: true
                }
            },
            messages: {
                kode_barang: {
                    required: "Kode wajib diisi"
                },
                nama_barang: {
                    required: "Nama wajib diisi"
                },
                qty: {
                    required: "Qty wajib diisi"
                },
                harga: {
                    required: "Harga wajib diisi"
                },
                satuan: {
                    required: "Satuan wajib diisi"
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

        var validator = $(".create-form").validate({
            rules: {
                kode_produksi: {
                    required: true
                },
                date_request: {
                    required: true
                },
                date_production: {
                    required: true
                },
                req_no: {
                    required: true
                },
                department_id: {
                    required: true
                },
                warehouse_id: {
                    required: true
                }
            },
            messages: {
                kode_produksi: {
                    required: "Kode produksi wajib diisi"
                },
                date_request: {
                    required: "Tanggal request wajib diisi"
                },
                date_production: {
                    required: "Tanggal produksi wajib diisi"
                },
                req_no: {
                    required: "Nomor request wajib diisi"
                },
                department_id: {
                    required: "Department wajib diisi"
                },
                warehouse_id: {
                    required: "Warehouse wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                console.log(elem);
                if (elem.hasClass("multiple_po_id")) {
                    element = $(".select2-selection--multiple").parent();
                    error.insertAfter(element);
                } else if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.col-md-6').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.col-md-6').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $('.btn-save').click(function() {
            console.log(listStockSelected);
            if (listStockSelected.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang yang akan direquest tidak boleh kosong !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else {
                if ($('.create-form').valid()) {
                    var isValid = true;
                    var dataError = null;

                    $.each(listStockSelected, function(i, v) {
                        var element = $('input[data-id="' + v.id + '"].stok-mutasi');
                        var input_user = parseFloat(element.val());
                        var stok_max = parseFloat(element.data('stok_total'));

                        if (input_user > stok_max || isNaN(input_user) || input_user == undefined || input_user == 0) {
                            dataError = listStockSelected[i];
                            isValid = false;
                        } else {
                            listStockSelected[i].qty = input_user;
                        }
                    });

                    if (!isValid) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Stok barang ' + dataError.barang + ' dengan dokumen ' + dataError.bc_type + ' / ' + dataError.no_aju + ' tidak valid!',
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                    } else {
                        Swal.fire({
                            icon: 'question',
                            title: 'Simpan Data ?',
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: 'Simpan',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let id = $('#id').val();
                                let data = new FormData(document.querySelector(".create-form"));
                                data.append('listMaterial', JSON.stringify(listStockSelected));
                                if (id) {
                                    // UPDATE
                                    $.ajax({
                                        url: "<?= base_url("material-request/update"); ?>",
                                        data: data,
                                        beforeSend: function(xhr) {
                                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                            setLoading();
                                        },
                                        complete: function() {
                                            stopLoading()
                                        },
                                        method: "POST",
                                        dataType: "json",
                                        processData: false,
                                        contentType: false,
                                        success: function(response) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    location.reload();
                                                }
                                            });
                                        },
                                    });
                                } else {
                                    // INSERT
                                    $.ajax({
                                        url: "<?= base_url("material-request/save"); ?>",
                                        data: data,
                                        beforeSend: function(xhr) {
                                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                            setLoading();
                                        },
                                        complete: function() {
                                            stopLoading()
                                        },
                                        method: "POST",
                                        dataType: "json",
                                        processData: false,
                                        contentType: false,
                                        success: function(response) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url('material-request/details/') ?>" + response.id
                                                }
                                            });
                                        },
                                    });
                                }
                            }
                        });
                    }

                }
            }
        });

        $('#department_id').on('change', function() {
            var departmentId = $(this).val();
            $.ajax({
                url: `<?= base_url("warehouse/dropdown/divisi/"); ?>/${departmentId}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $("#warehouse_id").empty();
                    $("#warehouse_id").append(`<option value=""></option>`);
                    res.data.forEach(function(item) {
                        $("#warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`);
                    })
                    $("#warehouse_id").prop('disabled', false);
                    // $("#warehouse_id").val().change();
                }
            })
        });

        $(".kode_produksi").change(function() {
            if ($(".kode_produksi option:selected").val()) {
                let nama_barang = $(".kode_produksi option:selected").data("nama-barang") ? $(".kode_produksi option:selected").data("nama-barang") : "";
                let standart_production = $(".kode_produksi option:selected").data("standart-production") ? $(".kode_produksi option:selected").data("standart-production") : "";

                $(".barang_jadi").val(nama_barang);
                $(".standart_production").val(standart_production);
            } else {
                $(".barang_jadi").val("");
                $(".standart_production").val("");
            }
        })
    });

    $('.btn-hide-detail').click(function() {
        $('.detail-modal').modal('hide');
    });

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".req_no").attr("readonly", true);
            $(".req_no").val("AUTO GENERATE");
        } else {
            $(".req_no").attr("readonly", false);
            $(".req_no").val("");
        }
    }

    const deleteRowDetail = function(id) {
        const indexToRemove = list_items.findIndex(item => item.barang_detail_id === id);
        if (indexToRemove !== -1) {
            list_items.splice(indexToRemove, 1);
        }
        drawTable();
    }

    function getListWarehouseAsal() {
        // GET LIST WAREHOUSE ASAL
        $.ajax({
            url: `<?= base_url('mutasi/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".department_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
            }
        });
    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('mutasi/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".select_tipe_bahan option:selected").val(),
                divisi_id: $(".department_id option:selected").val(),
                warehouse_id: $(".warehouse_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".select_nama_barang").empty()
                $(".select_nama_barang").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    console.log(item);
                    $(".select_nama_barang").append(`<option data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".select_nama_barang").val();
            }
        });
    }

    function getListDokumenPabean() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('mutasi/list-stock-dokumen-bc'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: $(".select_nama_barang option:selected").data('stock_id'),
            },
            dataType: "json",
            success: function(res) {
                // LIST STOK PER BC
                listStockAsal = [];
                listStockAsal = res.data;
                drawTableAsalBarang(res.data);
            }
        });
    }

    function drawTableAsalBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <div class="form-check">
                        <input data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(v.no_aju));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
            table.find('tbody').append(newRow);
        });

        dataTable = $('#dataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: true,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        dataTable.draw();
    }


    $('#select-item-btn').click(function() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();
        var id_selected = getIDListDataSelected();

        $.each(listStockAsal, function(i, v) {
            var currentID = Number(v.id);

            if ($.inArray(currentID, dataIds) !== -1) {
                var isIDSelected = $.grep(listStockSelected, function(item) {
                    return item.id == Number(currentID);
                }).length > 0;

                if (!isIDSelected) {
                    listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                    listStockAsal[i].qty = 0;
                    listStockSelected.push(listStockAsal[i]);
                }
            }
        });

        drawTableSelectedItem(listStockSelected);
    });

    function drawTableSelectedItem(data) {
        if ($.fn.DataTable.isDataTable('#selectedItemTable')) {
            $('#selectedItemTable').DataTable().clear().draw();
            selectedItemTable.destroy();
        }
        const table = $('#selectedItemTable');
        var no = 1;
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
               ${no++} 
            `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(v.no_aju));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <input <?= !empty($mutasi) ? (($mutasi['status_posting'] == "1") ? 'disabled' : '') : '' ?> class="form-control stok-mutasi" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" data-stok_total="${v.stok_total}" class="form-control" type="text" value="${v.qty}">
            `
            ));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <button <?= !empty($mutasi) ? (($mutasi['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
            `
            ));
            table.find('tbody').append(newRow);
        });

        selectedItemTable = $('#selectedItemTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: true,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        selectedItemTable.draw();
    }

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStockSelected, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    function deleteDetail(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listStockSelected.length; i++) {
            if (listStockSelected[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listStockSelected.splice(indexToRemove, 1);
            drawTableSelectedItem(listStockSelected);
        }
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        if (parseFloat(numericValue) <= 0) {
            inputElement.value = 0;
        } else {
            inputElement.value = numericValue;
        }
    }
</script>

<?= $this->endSection(); ?>
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
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
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
                                    <option value="<?= $dataDivisi->id ?>"><?= $dataDivisi->divisi ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Department</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select warehouse_id" name="warehouse_id" id="warehouse_id" disabled>
                                <option value=""></option>
                                <?php if (!empty($dataWorkOrders)) : ?>
                                    <?php foreach ($dataWarehouse ?? [] as $dataWarehouse) : ?>
                                        <option value="<?= $dataWarehouse->id ?>"><?= $dataWarehouse->warehouse_name ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                        <select class="form-select select_tipe_bahan" name="select_tipe_bahan" id="select_tipe_bahan">
                            <option value=""></option>
                            <option value="bahan_baku">Bahan Baku</option>
                            <option value="bahan_penolong">Bahan Penolong</option>
                            <option value="bahan_scrap">Bahan Scrap</option>
                            <option value="bahan_modal">Bahan Modal</option>
                            <option value="kemasan">Kemasan</option>
                        </select>
                        <label for="floatingInput">Tipe Bahan</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select select_nama_barang" name="select_nama_barang" id="select_nama_barang">
                            <option value=""></option>
                        </select>
                        <label for="floatingInput">Nama Barang</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTableBarang" id="dataTableBarang" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Referensi Barang</th>
                                <th>Tanggal Penerimaan</th>
                                <th>Warehouse</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Qty</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <button class="btn btn-show-detail btn-add btn-submit-barang" data-btn="detail-modal">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah Barang
                    </button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTableBarangAdd" id="dataTableBarangAdd" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Referensi Barang</th>
                                <th>Warehouse</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Qty</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        <tfoot class="tfoot">
                            <tr>
                                <td colspan="10" class="text-center">Tidak Ada Data</td>
                            </tr>
                        </tfoot>
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

        $(".select_tipe_bahan").change(function() {
            var type = $(".select_tipe_bahan").val();
            if (type) {
                setLoading();
                $.ajax({
                    url: `<?= base_url("barang/dropdown/type"); ?>`,
                    method: "GET",
                    dataType: "json",
                    data: {
                        type: type
                    },
                    success: function(res) {
                        $(".select_nama_barang").empty();
                        $(".select_nama_barang").append(`<option value=""></option>`);
                        res.data.forEach(function(item) {
                            $(".select_nama_barang").append(`<option value="${item.id}" data-spesifikasi_id="${item.barang_master_spesifikasi_id}">${item.kode_barang} - ${item.barang_name}</option>`);
                        })
                        $(".select_nama_barang").val("").change();
                        stopLoading();
                    }
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    title: "Pilih Tipe Bahan Dahulu",
                    confirmButtonColor: '#4e73df',
                })
            }
        })

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
                barang_id: {
                    required: true
                },
                production_amt: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                target: {
                    required: true
                }
            },
            messages: {
                barang_id: {
                    required: "Barang wajib diisi"
                },
                production_amt: {
                    required: "Hasil wajib diisi"
                },
                satuan_id: {
                    required: "Satuan wajib diisi"
                },
                target: {
                    required: "Target wajib diisi"
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

        $(".btn-save").click(function() {
            if (list_items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang masih kosong",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                if ($(".create-form").valid()) {
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
                            let id = $(".id").val();
                            let formData = new FormData(document.querySelector('.create-form'));
                            formData.append("items", JSON.stringify(list_items));

                            if (id) {
                                // UPDATE
                                <?php if (can('Produksi', 'Dokumen Produksi', 'u')) : ?>
                                    $.ajax({
                                        url: "<?= base_url("material-request/update"); ?>",
                                        data: formData,
                                        method: "POST",
                                        dataType: "json",
                                        beforeSend: function(xhr) {
                                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                            setLoading();
                                        },
                                        complete: function() {
                                            stopLoading();
                                        },
                                        processData: false,
                                        contentType: false,
                                        success: function(response) {
                                            if (response.status) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                    reverseButtons: true,
                                                    confirmButtonText: 'Oke',
                                                }).then((result) => {
                                                    location.reload();
                                                })
                                            }

                                        }
                                    });
                                <?php else : ?>
                                    Swal.fire({
                                        icon: 'error',
                                        title: "Anda tidak memiliki akses update",
                                        confirmButtonColor: '#4e73df',
                                    })
                                <?php endif; ?>
                            } else {
                                // CREATE
                                $.ajax({
                                    url: "<?= base_url("material-request/save"); ?>",
                                    data: formData,
                                    method: "POST",
                                    dataType: "json",
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading();
                                    },
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        if (response.status) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                reverseButtons: true,
                                                confirmButtonText: 'Oke',
                                            }).then((result) => {
                                                window.location.replace("<?= base_url('material-request/id/') ?>" + response.id);
                                            })
                                        }
                                    }
                                });
                            }
                        }
                    })
                }
            }
        })

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
                    $("#warehouse_id").val("").change();
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

        $('.btn-submit-barang').on('click', function() {
            list_items = [];
            // Cari baris yang memiliki kotak centang yang dicentang
            $('.dataTableBarang tbody input[type="checkbox"]:checked').each(function() {
                var rowData = dataTableBarang.row($(this).closest('tr')).data();
                // Pastikan data baris tidak null sebelum disimpan
                if (rowData) {
                    var jmlhBarang = $(this).closest('tr').find('.jumlahBarang').val();
                    if (jmlhBarang) {
                        var item = {
                            barang_detail_id: getID(),
                            no: rowData.no,
                            dokumen: rowData.dokumen,
                            stock: rowData.stock,
                            warehouse: rowData.warehouse,
                            kode: rowData.kode,
                            barang: rowData.barang,
                            satuan: rowData.satuan,
                            qty: rowData.qty,
                            stock_id: rowData.stock_id,
                            bc_id: rowData.bc_id,
                            barang1_id: rowData.barang1_id,
                            barang2_id: rowData.barang2_id,
                            no_aju: rowData.no_aju,
                            jumlahBarang: jmlhBarang, // Mengambil nilai dari input teks dengan class 'jumlahBarang'
                            ketBarang: $(this).closest('tr').find('.ketBarang').val(), // Mengambil nilai dari input teks dengan class 'ketBarang'
                        };
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "Harap isi jumlah barang dahulu",
                            confirmButtonColor: '#4e73df',
                        })
                    }
                    list_items.push(item);
                }
            });

            console.log(list_items);
            drawTable();
        });
    });

    $('.btn-hide-detail').click(function() {
        $('.detail-modal').modal('hide');
    });

    // const submitDetailForm = function() {
    //     let barang_detail_id = $(".barang_detail_id").val();
    //     let barang_id = $(".barang_id").val()
    //     let barang_spesifikasi_id = $(".barang_spesifikasi_id").val();
    //     let kode_barang = $(".kode_barang").val()
    //     let nama_barang = $(".nama_barang").val()
    //     let nama_satuan = $(".satuan").val()
    //     let satuan_id = $(".satuan_id").val()
    //     let qty = $(".qty").val()
    //     let keterangan = $(".keterangan").val() ? $(".keterangan").val() : '-'

    //     let header_barang_name = $('.header_barang_name').val();

    //     let validate_same = false;
    //     let validate_bahan_baku = false;
    //     if (barang_detail_id) {
    //         if ($(".detail-form").valid()) {
    //             // UPDATE DETAIL
    //             $.each(list_items, function(i, v) {
    //                 if (v.barang_detail_id === barang_detail_id) {
    //                     list_items[i].barang_id = barang_id;
    //                     list_items[i].barang_spesifikasi_id = barang_spesifikasi_id;
    //                     list_items[i].kode_barang = kode_barang;
    //                     list_items[i].nama_barang = nama_barang;
    //                     list_items[i].nama_satuan = nama_satuan;
    //                     list_items[i].satuan_id = satuan_id;
    //                     list_items[i].qty = qty;
    //                     list_items[i].keterangan = keterangan;
    //                 }
    //             });
    //             drawTable();
    //             $(".detail-modal").modal("hide");
    //         }
    //     } else {
    //         if ($(".detail-form").valid()) {
    //             // CREATE DETAIL
    //             if (barang_id !== "") {
    //                 list_items.push({
    //                     'barang_detail_id': getID(),
    //                     'barang_id': barang_id,
    //                     'barang_spesifikasi_id': barang_spesifikasi_id,
    //                     'kode_barang': kode_barang,
    //                     'nama_barang': nama_barang,
    //                     'nama_satuan': nama_satuan,
    //                     'satuan_id': satuan_id,
    //                     'qty': qty,
    //                     'keterangan': keterangan
    //                 });
    //             }

    //             drawTable();
    //         }
    //     }
    // }

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
    const getID = function() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    }
    const drawTable = function() {
        $('.body-detail-table').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items.length === 0) {
            row += `
                    <tr>
                        <td colspan="10" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.tfoot').append(row);
        } else {
            list_items.map(item => {
                row += '<tr style="color:whitesmoke;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.dokumen + '</td>';
                row += '<td>' + item.warehouse + '</td>';
                row += '<td>' + item.kode + '</td>';
                row += '<td>' + item.barang + '</td>';
                row += '<td>' + item.satuan + '</td>';
                row += '<td>' + item.qty + '</td>';
                row += '<td>' + item.jumlahBarang + '</td>';
                row += '<td>' + item.ketBarang + '</td>';
                row += '<td>' + `
                    <button class="btn btn-danger" onclick="deleteRowDetail('${item.barang_detail_id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                    '</td>';
                no++;
            });
            $('.body-detail-table').append(row);
        }
    }

    const deleteRowDetail = function(id) {
        const indexToRemove = list_items.findIndex(item => item.barang_detail_id === id);
        if (indexToRemove !== -1) {
            list_items.splice(indexToRemove, 1);
        }
        drawTable();
    }

    // Update
    <?php if (!empty($dataWorkOrders)) : ?>
        <?php foreach ($dataWorkOrderDetails as $i => $d) : ?>
            list_items.push({
                'barang_detail_id': getID(),
                'barang_id': "<?= encrypt($d->barang1_id) ?>",
                'barang_spesifikasi_id': "<?= encrypt($d->barang2_id) ?>",
                'kode_barang': "<?= $d->kode_barang ?>",
                'nama_barang': "<?= $d->nama_barang ?>",
                'nama_satuan': "<?= $d->nama_satuan ?>",
                'satuan_id': "<?= $d->unit ?>",
                'qty': "<?= $d->qty ?>",
                'keterangan': "<?= $d->note ?>"
            });
        <?php endforeach; ?>
        drawTable();
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>
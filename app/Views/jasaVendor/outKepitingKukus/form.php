<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<style>
    /* Pastikan container form-floating tidak mengubah posisi */
    .form-floating > .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 100% !important;
        height: 50px !important; /* Tinggi fix */
        padding: 4px 6px !important;
        display: block !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        overflow-y: auto;   /* Scroll vertikal */
        overflow-x: hidden;
        white-space: normal;
    }

    /* Chip/tag pilihan */
    .select2-container--bootstrap-5 .select2-selection__choice {
        background-color: #e9ecef !important;
        border: none !important;
        padding: 2px 6px !important;
        margin: 2px 4px 0 0 !important;
        font-size: 0.8rem !important;
        border-radius: 0.25rem !important;
        display: inline-flex;
        align-items: center;
    }

    /* Ikon X di chip */
    .select2-container--bootstrap-5 .select2-selection__choice__remove {
        margin-right: 4px !important;
    }

    /* Search box di dalam multiple select */
    .select2-container--bootstrap-5 .select2-search--inline {
        display: inline-flex;
        align-items: center;
    }
    .select2-container--bootstrap-5 .select2-search--inline .select2-search__field {
        margin-top: 0 !important;
        padding: 0 !important;
        height: auto !important;
    }
    
</style>

<section class="section">
    <div class="section-header">
        <h1><?= empty($jasaVendorOut) ? "Tambah Jasa Vendor Barang Keluar Kepiting Kukus" : "Update Jasa Vendor Barang Keluar Kepiting Kukus s" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("jasa-vendor-out-kepiting-kukus"); ?>">
                Kembali
            </a>
            <?php if (!empty($jasaVendorOut)) : ?>
                <?php if ($jasaVendorOut['status_posting'] == "0") : ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($jasaVendorOut['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($jasaVendorOut['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("jasa-vendor-out-kepiting-kukus/print/"); ?><?= encrypt($jasaVendorOut['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("jasa-vendor-out-kepiting-kukus/print/"); ?><?= encrypt($jasaVendorOut['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3"> 
                    <label class="form-label font-weight-bold lable-title">Data Pengeluaran Barang Kepiting Kukus</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($jasaVendorOut) ? encrypt($jasaVendorOut['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'readonly' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($jasaVendorOut) ? $jasaVendorOut['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'readonly' : '') : ''; ?> value="<?= !empty($jasaVendorOut) ? $jasaVendorOut['no_surat_jalan'] : "TOBA-VBK//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="No. Surat Jalan">
                                    <label for="floatingInput">No. Nota Surat Jalan</label>
                                </div>
                                <div style="<?= !empty($jasaVendorOut) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select vendor_id" id="vendor_id" name="vendor_id">
                                <option value=""></option>
                                <?php foreach ($vendor as $v) : ?>
                                    <option <?= !empty($jasaVendorOut) ? ($jasaVendorOut['vendor_id'] == $v['id'] ? 'selected' : '') : '' ?> value="<?= $v['id'] ?>">
                                        <?= strtoupper($v['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Vendor Tujuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Nomor Kontainer" value="<?= !empty($jasaVendorOut) ? $jasaVendorOut['no_kontainer'] : '' ?>" class="form-control no_kontainer" id="no_kontainer" name="no_kontainer" />
                            <label for="floatingInput" style="z-index: 1;">Nomor Kontainer (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($jasaVendorOut) ? ($jasaVendorOut['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id">
                                <option value=""></option>
                                <?php if (!empty($warehouse)) : ?>
                                    <?php foreach ($warehouse as $w) : ?>
                                        <option <?= $jasaVendorOut['warehouse_id'] == $w['id'] ? 'selected' : '' ?> value="<?= $w['id'] ?>">
                                            <?= $w['warehouse_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($jasaVendorOut) ? $jasaVendorOut['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="detail-form-layout">

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Pilih Barang</label>
                    </div>
                </div>
                <form class="detail-form">
                    <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-spp form-floating mb-3">
                                <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id[]">
                                <option value=""></option>
                                </select>
                                <label for="spesifikasi_id">Pilih Spesifikasi Barang</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating mb-3">
                                <select class="form-select supplier_id" id="supplier_id" name="supplier_id">
                                    <option value=""></option>
                                <?php foreach ($supplier as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                                <?php endforeach; ?>
                                </select>
                                <label for="supplier_id">Pilih Supplier</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating mb-3">
                                <input type="text" placeholder="Keterangan" class="form-control keterangan_detail" id="keterangan_detail" name="keterangan_detail" />
                                <label for="keterangan_detail">Keterangan</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                            <button type="button" class="btn btn-primary" id="btnAddBarang">+ Tambah Barang</button>
                        </div>
                    </div>
                </form>
            </div>


            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dikeluarkan</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive" style="margin-top: -10px;">
                        <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts dataTable" id="selectedItemTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">Supplier</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Satuan</th>
                                    <th style="text-align: center;">Keterangan</th>
                                    <th style="text-align: center;">Qty Dikeluarkan</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="11" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var listStockAsal = [];
    var listStockSelected = [];
    let abortController = null;

    var dataTable = $('#dataTable').DataTable({

        processing: false,
        serverSide: false,
        ordering: false,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        lengthMenu: [
            [100],
            [100]
        ],
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

    <?php if (empty($jasaVendorOut)): ?>
        $('.tanggal').change(function() {
            changeStatus();
        })
    <?php endif; ?>

    $('#vendor_barang_id_select').hide();

    <?php if (!empty($jasaVendorOut)) : ?>
        <?php foreach ($jasaVendorOutDetail as $m) : ?>
            // // bikin option preselect
            // var option = new Option(
            //     "<?= $m['barang'] ?> - <?= $m['spesifikasi'] ?>", 
            //     "<?= $m['spesifikasi_id'] ?>",
            //     true, 
            //     true
            // );

            // // kasih data-* biar ga undefined
            // $(option).attr({
            //     "data-master_barang": "<?= $m['barang'] ?>",
            //     "data-spesifikasi": "<?= $m['spesifikasi'] ?>",
            //     "data-satuan": "<?= $m['satuan'] ?>",
            //     "data-qty": "<?= floatval($m['qty']) ?>"
            // });

            // $(".spesifikasi_id").append(option).trigger("change.select2");

            // push ke list selected
            listStockSelected.push({
                id: "<?= $m['spesifikasi_id'] ?>",
                detail_id: "<?= $m['detail_id'] ?>",
                satuan: "<?= $m['satuan'] ?>",
                master_barang: "<?= $m['barang'] ?>",
                supplier_text: "<?= $m['supplier'] ?>",
                supplier_id: "<?= $m['supplier_id'] ?>",
                keterangan: "<?= $m['keterangan'] ?>",
                spesifikasi: "<?= $m['spesifikasi'] ?>",
                qty: "<?= floatval($m['qty']) ?>",
            });
        <?php endforeach; ?>

        // render tabel dari data edit
        drawTableSelectedItem(listStockSelected);

        <?php if ($jasaVendorOut['status_posting']) : ?>
            $('.detail-form-layout').hide()
        <?php endif; ?>
    <?php endif; ?>


    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('#vendor_id').select2({
        placeholder: "Pilih Vendor Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        changeStatus();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        getListWarehouse();
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        width: '100%',
    });

    $(".spesifikasi_id").select2({
        placeholder: "Pilih Spesifikasi Barang",
        theme: "bootstrap-5",
        multiple: true,
        minimumInputLength: 3,
        ajax: {
            delay: 300,
            transport: function(params, success, failure) {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                fetch("<?= base_url('jasa-vendor-out-kepiting-kukus/search-barang'); ?>?" + new URLSearchParams({
                    q: params.data.term
                }), {
                    signal: abortController.signal
                })
                .then(res => res.json())
                .then(success)
                .catch(err => {
                    if (err.name !== "AbortError") failure(err);
                });
            },
            processResults: function(data) {
                return {
                    results: data.data.map(item => ({
                        id: item.spesifikasi_id,
                        text: `${item.master_barang} - ${item.spesifikasi}`,
                        master_barang: item.master_barang,
                        spesifikasi: item.spesifikasi,
                        satuan: item.kode_satuan,
                    }))
                };
            }
        }
    });

    // .on("change", function (e) {
    //     let selectedData = $(this).select2("data");

    //     listStockSelected = selectedData.map(item => ({
    //         id: item.id,
    //         detail_id: item.detail_id || null,
    //         master_barang: item.master_barang || $(item.element).data("master_barang"),
    //         spesifikasi: item.spesifikasi || $(item.element).data("spesifikasi"),
    //         satuan: item.satuan || $(item.element).data("satuan"),
    //         qty: item.qty || $(item.element).data("qty") || 0,
    //     }));

    //     if (listStockSelected.length > 0) {
    //         drawTableSelectedItem(listStockSelected);
    //     }
    // })

    $("#btnAddBarang").on("click", function () {
        let spesifikasi = $("#spesifikasi_id").select2("data");
        let supplierId = $("#supplier_id").val();
        let keterangan = $("#keterangan_detail").val();
        let detailId = $("#id_detail").val(); // kalau kosong berarti data baru

        if (!spesifikasi || !supplierId || !keterangan) {
            Swal.fire({
                icon: 'error',
                title: 'Spesifikasi, Keterangan, dan Supplier yang akan dikirimkan ke vendor tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        }

       listStockSelected = [
            ...listStockSelected,
            ...spesifikasi
                .filter(item => item.id)
                .map(item => ({
                    id: item.id,
                    detail_id: detailId || null,
                    master_barang: item.master_barang || $(item.element).data("master_barang"),
                    spesifikasi: item.spesifikasi || $(item.element).data("spesifikasi"),
                    satuan: item.satuan || $(item.element).data("satuan"),
                    qty: item.qty || 0,
                    supplier_id: supplierId,
                    supplier_text: $("#supplier_id option:selected").text(),
                    keterangan: keterangan
                }))
        ];


        // push ke global array
        // listStockSelected.push(...listStockSelected);

        if (listStockSelected.length > 0) {
            drawTableSelectedItem(listStockSelected);
        }

        $("#spesifikasi_id").val(null).trigger("change"); 
        $("#supplier_id").val("").trigger("change");
        $("#keterangan_detail").val("");
        $("#id_detail").val(""); // reset hidden field juga
    });


    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_surat_jalan: {
                required: true
            },
            vendor_id: {
                required: true
            },
            divisi_id: {
                required: true
            },
            warehouse_id: {
                required: true
            }
        },
        messages: {
            no_surat_jalan: {
                required: "No surat jalan wajib diisi"
            },
            vendor_id: {
                required: "Vendor wajib diisi"
            },
            keterangan: {
                required: "Keterangan wajib diisi"
            },
            divisi_id: {
                required: "Pilih Departemen"
            },
            warehouse_id: {
                required: "Pilih Warehouse"
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


    $('#select-item-btn').click(function() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        if (typePengambilanStock == "FIFO") {
            insertListFifo();
        } else {
            insertListPabean();
        }
    });

    function deleteByStockID(stockID) {
        listStockSelected = listStockSelected.filter(function(item) {
            return item.stock_id != stockID;
        });
    }


    $('.btn-submit-parent').click(function() {
        if (listStockSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan dikirimkan ke vendor tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                var isValid = true;
                var dataError = null;

                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));

                            // Ambil semua field dari .detail-form
                            document.querySelectorAll(".detail-form [name]").forEach(el => {
                                data.append(el.name, el.value);
                            });

                            // Append list barang
                            data.append('listBarang', JSON.stringify(listStockSelected));


                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("jasa-vendor-out-kepiting-kukus/update"); ?>",
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
                                                window.location.href = "<?= base_url("jasa-vendor-out-kepiting-kukus") ?>";
                                            }
                                        });
                                    },
                                });
                            } else {
                                // INSERT
                                $.ajax({
                                    url: "<?= base_url("jasa-vendor-out-kepiting-kukus/save"); ?>",
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
                                        if (response.status == false) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: res.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                        } else {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url("jasa-vendor-out-kepiting-kukus") ?>";
                                                }
                                            });
                                        }

                                    },
                                });
                            }
                        }
                    });

            }
        }
    });


    $("#vendor_id,#warehouse_id,#divisi_id,#type_barang,#spesifikasi_id,#type_pengambilan_stock,#supplier_id,#type_asal_barang,#vendor_barang_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStockSelected, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    function getListWarehouse() {
        $.ajax({
            url: `<?= base_url('jasa-vendor-out/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_id").val();
            }
        });
    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('jasa-vendor-out/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".type_barang option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val(),
                warehouse_id: $(".warehouse_id option:selected").val(),
                asal_barang: $(".type_asal_barang option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".spesifikasi_id").empty()
                $(".spesifikasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_id").append(`<option data-barang_master_id="${item.id}" data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".spesifikasi_id").val();
            }
        });
    }


    // helper: parse stok_total aman
    function parseFloatSafe(val) {
        if (val === null || val === undefined || val === '') return 0;
        return parseFloat(String(val).replace(/,/g, '.')) || 0;
    }

    // helper: buat key unik per record (ubah fields sesuai kebutuhan)
    function makeUniqueKey(v) {
        // include stock_dokumen & bc_id supaya satu id bisa punya banyak dokumen
        return `${v.id}||${v.stock_dokumen || ''}||${v.bc_id || ''}`;
    }

    function drawTableSelectedItem(data) {
        console.log(listStockSelected);
        const table = $('#selectedItemTable');
        var no = 1;
        $('.foot-detail-table').empty();
        $('.body-table').empty();

        if (data.length == 0) {
            var newRow = '';
            newRow += `
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            Tidak Ada Barang
                        </td>
                    </tr>
                `;
            $('.foot-detail-table').append(newRow);
        } else {
            var totalQtyKeluar = 0;
            $.each(data, function(i, v) {
                var newRow = $('<tr>');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                   ${no++} 
                `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.supplier_text));
                newRow.append($('<td style="text-align: center;">').text(v.master_barang + '-' + v.spesifikasi));
                newRow.append($('<td style="text-align: center;">').text(v.satuan));
                newRow.append($('<td style="text-align: center;">').text(v.keterangan));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <input <?= !empty($jasaVendorOut) ? (($jasaVendorOut['status_posting'] == "1") ? 'disabled' : '') : '' ?> onkeyup="this.value = greatFormatRupiah(this.value)" class="form-control stok-out" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" class="form-control" type="text" value="${greatFormatRupiah(v.qty)}">
                `
                ));

                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button <?= !empty($jasaVendorOut) ? (($jasaVendorOut['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
                ));
                table.find('tbody').append(newRow);

                totalQtyKeluar += destroyFormatRupiah(v.qty);
            });

            var newRow = $('<tr class="grand-total" style="color:whitesmoke; background-color:#f2c996;">');
            newRow.append($('<td style="text-align: right;" colspan="5">').html("<b>GRAND TOTAL</b>"));
            newRow.append($('<td class="total-cell">').text(greatFormatQty(totalQtyKeluar)));
            newRow.append($('<td>').text(''));
            table.find('tbody').append(newRow);

            $(document).on("input", ".stok-out", function() {
        
                let id = $(this).data("id");
                let val = destroyFormatRupiah($(this).val()) || 0;

                // update ke listStockSelected
                let item = listStockSelected.find(x => x.id == id);
                if (item) {
                    item.qty = val;
                }

                updateGrandTotal();
            });
        }
    }
    

    function deleteDetail(id) {
        // Pastikan id jadi angka biar perbandingan aman
        id = Number(id);

        // Filter list, sisakan item yang ID-nya beda
        listStockSelected = listStockSelected.filter(item => Number(item.id) !== id);

        // Gambar ulang tabel
        drawTableSelectedItem(listStockSelected);
    }



    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_surat_jalan").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("jasa-vendor-out-kepiting-kukus/get-jasa-vendor-out-no"); ?>`,
                method: "GET",
                data: {
                    warehouse_id: $('#warehouse_id option:selected').val(),
                    tanggal: $('#tanggal').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_surat_jalan").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_surat_jalan").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_surat_jalan").val("");
                    }
                }
            })
        } else {
            $(".no_surat_jalan").attr("readonly", false);
            $(".no_surat_jalan").val("");
        }
    }

    function getDropdownAsalBarang() {
        var typeAsalBarang = $('#type_asal_barang option:selected').val();
        if (typeAsalBarang == 'SUPPLIER') {
            $('#supplier_id_select').show();
            $('#vendor_barang_id_select').hide();
        } else {
            $('#supplier_id_select').hide();
            $('#vendor_barang_id_select').show();
        }
        $('#supplier_id').val(null).change();
        $('#vendor_barang_id').val(null).change();
        $('#warehouse_id').change();

    }

    // fungsi hitung ulang total
    function updateGrandTotal() {
        let total = 0;
        $('.stok-out').each(function () {
            total += destroyFormatRupiah($(this).val());
        });
        // tampilkan clean (ga perlu paksa ".00", kalau mau tambahin ya boleh)
        $('#selectedItemTable tbody tr.grand-total td.total-cell')
            .text(greatFormatQty(total));
    }


    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Jasa Vendor Barang Keluar ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("jasa-vendor-out-kepiting-kukus/posting"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
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
                            }).then((result) => {
                                window.location.href = "<?= base_url("jasa-vendor-out") ?>";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    },
                });
            }
        })
    }

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Jasa Vendor Barang Keluar ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("jasa-vendor-out-kepiting-kukus/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                window.location.href = "<?= base_url("jasa-vendor-out") ?>";
                            });
                        }
                    },
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>
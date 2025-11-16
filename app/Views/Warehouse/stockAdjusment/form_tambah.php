<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>Adjusment Untuk Tambah Stok</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-adjusment"); ?>">
                Kembali
            </a>
            <?php if (empty($adjusment)): ?>
                <a class="btn btn-hide-form btn-discard float-right" style="background-color: #628a2dff !important; color:white !important; border:none !important;" href="#" id="btnModalImport">
                    Import Stok Adjusment
                </a>
            <?php endif; ?>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent" <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?>>
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Adjusment</label>
                </div>
            </div>
            <form class="create-form">
                <input type="hidden" name="id" id="id" value="<?= !empty($adjusment) ? encrypt($adjusment['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($adjusment) ? $adjusment['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($adjusment) ? ($adjusment['status_posting'] == 1 ? 'readonly' : '')  : ''; ?> value="<?= !empty($adjusment) ? $adjusment['no_adjusment'] : ""; ?>" type="text" class="form-control no_adjusment" id="no_adjusment" name="no_adjusment" placeholder="No. Adjusment">
                                    <label for="floatingInput">No. Adjusment</label>
                                </div>
                                <div style="<?= !empty($adjusment) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 20px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($adjusment) ? ($adjusment['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id">
                                <option value=""></option>
                                <?php if (!empty($warehouse)): ?>
                                    <?php foreach ($warehouse as $w): ?>
                                        <option selected value="<?= $w['id'] ?>"><?= $w['warehouse_name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($adjusment) ? $adjusment['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">Barang yang Akan di Adjustment</label>
                    </div>
                </div>
            </div>

            <form class="detail-form">
                <input type="hidden" name="id_detail" id="id_detail" class="id_detail">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select" id="stock_id" name="stock_id">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Barang / Spesifikasi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control qty_adjusment" name="qty_adjusment" id="qty_adjusment" placeholder="Qty Adjusment" onkeyup="this.value = greatFormatRupiah(this.value)">
                            <label for="floatingInput">Qty Adjusment</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select disabled <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select" id="satuan_id" name="satuan_id">
                                <option value=""></option>
                                <?php foreach ($dataSatuan as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Satuan</label>
                        </div>
                    </div>
                </div>

            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right btn-submit-detail" id="btn-submit-detail">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetDetailForm()">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-adjusment" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Dept</th>
                                <th>Warehouse</th>
                                <th>Kode Barang</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Note</th>
                                <th style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="10">Tidak Ada Data</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal detail-modal" id="importModal" tabindex="1">
    <div class="modal-dialog modal-xl" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Upload Stok Adjusment</h5>
            </div>
            <form class="upload-form" role="form" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <input autocomplete="one-time-code" type="file" class="form-control file" id="file" name="file">
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <a href="<?= base_url('assets/import/format_import_adjusment.xls') ?>">Unduh Template Import</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideImportModal">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitImport">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var listStock = [];
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    <?php if (empty($adjusment)): ?>
        changeStatus();
    <?php else: ?>
        listStock = <?= json_encode($dataListBarang) ?>;
        drawTable(listStock);
    <?php endif; ?>

    // TANGGAL
    $('#tanggal').change(function(e) {
        e.preventDefault();
        changeStatus();
    })

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        dropdownWarehouse();
        listStock = [];
        drawTable(listStock);
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: false,
    }).change(function() {
        listStock = [];
        drawTable(listStock);
    });

    $('#satuan_id').select2({
        placeholder: "Pilih Satuan",
        theme: "bootstrap-5",
        allowClear: false,
    }).change(function() {});

    $('#stock_id').select2({
        placeholder: "Cari Kode / Nama Barang",
        theme: "bootstrap-5",
        allowClear: false,
        ajax: {
            url: '<?= base_url("barang/dropdown/type-server-inventori-warehouse-divisi") ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    divisi_id: $('#divisi_id option:selected').val(),
                    warehouse_id: $('#warehouse_id option:selected').val()
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.results, function(item) {
                        return {
                            id: item.id,
                            text: item.text,
                            satuan_id: item.unit_id,
                            kode_barang: item.kode_barang,
                            barang_name: item.barang_name,
                            spesifikasi: item.spesifikasi
                        };
                    })
                };
            },
            cache: false
        },
        minimumInputLength: 1
    });

    //CSS SELECT2 FLOATING LABEL
    $('#satuan_id,#stock_id,#divisi_id,#warehouse_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('#satuan_id,#stock_id,#divisi_id,#warehouse_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#satuan_id,#stock_id,#divisi_id,#warehouse_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#satuan_id,#stock_id,#divisi_id,#warehouse_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#btnModalImport').click(function(e) {
        e.preventDefault();
        var divisiId = $('#divisi_id').val();
        var warehouseId = $('#warehouse_id').val();

        if (divisiId == '' || warehouseId == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih warehouse & departemen terlebih dahulu',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else {
            $('#file').val(null).change();
            $('#importModal').modal('show');
        }

    });

    $('#btnHideImportModal').click(function(e) {
        e.preventDefault();
        $('#importModal').modal('hide');
    });

    $('#stock_id').on('select2:select', function(e) {
        e.preventDefault();
        var data = e.params.data;
        var selectedOption = $(this).find('option:selected');
        selectedOption.data('satuan_id', data.satuan_id);
        selectedOption.data('kode_barang', data.kode_barang);
        selectedOption.data('barang_name', data.barang_name);
        selectedOption.data('spesifikasi', data.spesifikasi);
        $(this).trigger('change');
    });

    $('#stock_id').change(function(e) {
        e.preventDefault();
        var satuan_id = $('#stock_id option:selected').data('satuan_id');
        $('#satuan_id').val(satuan_id).change();
    })

    // VALIDATOR HEADER
    var validator = $(".create-form").validate({
        rules: {
            tanggal: {
                required: true
            },
            no_adjusment: {
                required: true
            },
            divisi_id: {
                required: true
            },
            warehouse_id: {
                required: true
            },
        },
        messages: {
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            no_adjusment: {
                required: "No adjusment wajib diisi"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            warehouse_id: {
                required: "Warehouse wajib diisi"
            },
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

    // UNTUK UPDATE STOCK
    var validatorDetail = $(".detail-form").validate({
        rules: {
            stock_id: {
                required: true
            },
            qty_adjusment: {
                required: true
            },
            satuan_id: {
                required: true
            },
        },
        messages: {
            stock_id: {
                required: "Pilih barang dahulu"
            },
            qty_adjusment: {
                required: "Qty wajib diisi"
            },
            satuan_id: {
                required: "Satuan wajib diisi"
            },
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

    var validatorImport = $(".upload-form").validate({
        rules: {
            file: {
                required: true
            },
        },
        messages: {
            file: {
                required: "Pilih file dahulu"
            },
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


    $('#btnSubmitImport').click(function(e) {
        e.preventDefault();
        if ($('.upload-form').valid()) {
            let csrf = $(`[name="${csrfToken}"]`);
            let divisiId = $('#divisi_id option:selected').val();
            let warehouseId = $('#warehouse_id option:selected').val();
            let formData = new FormData(document.querySelector(".upload-form"));
            formData.append('divisi_id', divisiId);
            formData.append('warehouse_id', warehouseId);

            $.ajax({
                url: "<?= base_url("stock-adjusment/import-preview"); ?>",
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    if (response.status) {
                        var isOke = true;
                        listStock = response.data.dataResult;
                        $.each(listStock, function(i, v) {
                            if (v.status == false) {
                                isOke = false;
                            }
                        });
                        if (!isOke) {
                            $('.btn-submit-parent').attr('disabled', true);
                        } else {
                            $('.btn-submit-parent').attr('disabled', false);
                        }
                        drawTable(listStock);
                        $('#importModal').modal('hide');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        });
                        $('#file').val(null).change();
                    }
                },
            });
        }
    })


    // SUBMIT HEADER
    $('.btn-submit-parent').click(function() {
        if (listStock.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'List adjusment barang kosong',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else {
            if ($('.create-form').valid()) {
                var id = $('.id').val();
                var url = id != '' ? "<?= base_url("stock-adjusment/update"); ?>" : "<?= base_url("stock-adjusment/save"); ?>";
                var formData = new FormData(document.querySelector('.create-form'));
                formData.append("listBarang", JSON.stringify(listStock));
                formData.append("jenis_adjusment", "CREATE");
                // UPDATE
                $.ajax({
                    url: url,
                    data: formData,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url("stock-adjusment"); ?>";
                                })
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
        }
    });

    $('#btn-submit-detail').click(function(e) {
        e.preventDefault();
        if ($('.detail-form').valid()) {
            var id_detail = $('#id_detail').val();
            var stock_id = $('#stock_id option:selected').val();
            var kode_barang = $('#stock_id option:selected').data('kode_barang');
            var barang_name = $('#stock_id option:selected').data('barang_name');
            var spesifikasi = $('#stock_id option:selected').data('spesifikasi');
            var qty_adjusment = destroyFormatRupiah($('#qty_adjusment').val());
            var satuan_id = $('#satuan_id option:selected').val();
            var kode_satuan = $('#satuan_id option:selected').text();
            var divisi = $('#divisi_id option:selected').text();
            var warehouse_name = $('#warehouse_id option:selected').text();

            if (id_detail) {
                var indexSelected = 0;
                for (let i = 0; i < listStock.length; i++) {
                    if (listStock[i].id == id_detail) {
                        indexSelected = i;
                    }
                }

                listStock[indexSelected].stock_id = stock_id;
                listStock[indexSelected].kode_barang = kode_barang;
                listStock[indexSelected].barang_name = barang_name;
                listStock[indexSelected].spesifikasi = spesifikasi;
                listStock[indexSelected].qty_adjusment = qty_adjusment;
                listStock[indexSelected].satuan_id = satuan_id;
                listStock[indexSelected].kode_satuan = kode_satuan;
                listStock[indexSelected].divisi = divisi;
                listStock[indexSelected].warehouse_name = warehouse_name;
            } else {
                listStock.push({
                    id: getID(),
                    stock_id: stock_id,
                    kode_barang: kode_barang,
                    barang_name: barang_name,
                    spesifikasi: spesifikasi,
                    qty_adjusment: qty_adjusment,
                    satuan_id: satuan_id,
                    kode_satuan: kode_satuan,
                    divisi: divisi,
                    warehouse_name: warehouse_name
                })
            }

            drawTable(listStock);
            resetDetailForm();

        }
    });

    function resetDetailForm() {
        $('#id_detail').val(null);
        $('#stock_id').val(null).change();
        $('#qty_adjusment').val(null);
        $('#satuan_id').val(null).change();
    }

    $('.posting-adjusment').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Posting Adjusment ?',
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
                    url: "<?= base_url("stock-adjusment/posting"); ?>",
                    data: {
                        id: $('.id').val()
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
                                window.location.href = "<?= base_url("stock-adjusment"); ?>";
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
    })

    $('.delete-parent').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Adjusment ?',
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
                    url: "<?= base_url("stock-adjusment/delete"); ?>",
                    data: {
                        id: $('.id').val()
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
                                window.location.href = "<?= base_url('stock-adjusment') ?>"
                            });
                        }
                    },
                });
            }
        })
    });


    function remove(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listStock.splice(indexToRemove, 1);
            drawTable(listStock);
        }
    }

    function detail(id) {
        var item = null;
        for (let i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                item = listStock[i];
            }
        }
        $('#id_detail').val(item.id);
        $('#qty_adjusment').val(greatFormatRupiah(item.qty_adjusment));
        // Reset dan isi stock_id
        const $stockId = $("#stock_id");
        $stockId.empty()
            .append('<option value=""></option>')
            .append(`
            <option 
                data-satuan_id="${item.satuan_id}"
                data-kode_barang="${item.kode_barang}" 
                data-barang_name="${item.barang_name}" 
                data-spesifikasi="${item.spesifikasi}" 
                selected 
                value="${item.id}">
                (${item.kode_barang}) ${item.barang_name} - ${item.spesifikasi}
            </option>
        `)
            .val(item.id)
            .trigger('change');
        $('#stock_id').val(item.id).change();
    }

    function drawTable(listStock) {
        const table = $('.table-adjusment');
        const status_posting = <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'true' : 'false') : 'false' ?>;

        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listStock.length === 0) {
            const newRow = $('<tr>');
            newRow.append($('<td colspan="10">').text("Tidak Ada Data"));
            table.find('tfoot').append(newRow);
            return;
        }

        let no = 1;

        $.each(listStock, function(i, v) {
            const newRow = $('<tr style="color:whitesmoke;">');

            newRow.append($('<td>').text(no++));
            newRow.append($('<td>').text(v.divisi));
            newRow.append($('<td>').text(v.warehouse_name));
            newRow.append($('<td>').text(v.kode_barang));
            newRow.append($('<td>').text(v.barang_name));
            newRow.append($('<td>').text(v.spesifikasi));
            newRow.append($('<td>').text(greatFormatRupiah(v.qty_adjusment)));
            newRow.append($('<td>').text(v.kode_satuan));
            newRow.append($('<td>').text(v.message ?? ""));

            // --- tombol aksi ---
            let actionHtml = "";

            // jika object punya key 'status' → jangan tampilkan tombol
            if (!v.hasOwnProperty('status')) {

                // jika status posting = false → tombol muncul
                if (!status_posting) {
                    actionHtml = `
                    <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detail('${v.id}')">
                        <i class="fa fa-pencil fa-sm"></i>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="remove('${v.id}')">
                        <i class="fa fa-trash fa-sm"></i>
                    </button>
                `;
                }
            }

            newRow.append($('<td>').html(actionHtml));
            table.find('tbody').append(newRow);
        });
    }


    function dropdownWarehouse() {
        $.ajax({
            url: `<?= base_url('stock-adjusment/warehouse'); ?>`,
            method: "GET",
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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_adjusment").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("stock-adjusment/get-adjusment-no"); ?>`,
                method: "GET",
                data: {
                    tanggal: $('#tanggal').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_adjusment").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_adjusment").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_adjusment").val("");
                    }
                }
            })
        } else {
            $(".no_adjusment").attr("readonly", false);
            $(".no_adjusment").val("");
        }
    }


    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };
</script>



<?= $this->endSection(); ?>
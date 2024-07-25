<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name">Return Barang</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-barang-lokal-bb"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataPenerimaanBarang)) : ?>
                <?php if (can('Warehouse', 'P. Barang Lokal BB', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-barang-lokal-bb/return-barang/print/"); ?><?= encrypt($dataPenerimaanBarang['id']); ?>')">
                        Print
                    </button>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($dataPenerimaanBarang)) : ?>
                <?php if ($dataPenerimaanBarang['status_post'] === "WAITING") : ?>
                    <?php if (can('Warehouse', 'P. Barang Lokal BB', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Warehouse', 'P. Barang Lokal BB', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-lpb">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Warehouse', 'P. Barang Lokal BB', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
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
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="text" class="penerimaan_barang_id" name="penerimaan_barang_id" id="penerimaan_barang_id" value="<?= !empty($dataPenerimaanBarang) ? encrypt($dataPenerimaanBarang['id']) : ""; ?>" />
                <input autocomplete="one-time-code" type="text" class="pengembalian_barang_id" name="pengembalian_barang_id" id="pengembalian_barang_id" value="<?= !empty($dataPengembalianBarang) ? encrypt($dataPengembalianBarang['id']) : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row mb-1">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Data Return Barang</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" value="" type="text" class="form-control no_return_barang" id="no_return_barang" name="no_return_barang" placeholder="No. Surat Jalan">
                                    <label for="floatingInput">No. Surat Jalan</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" value="" type="text" class="form-control tanggal_return_barang" name="tanggal_return_barang" id="tanggal_return_barang" placeholder="Tanggal Return Barang">
                                <label for="floatingInput">Tanggal Return Barang</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Data LPB</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['no_penerimaan_barang'] : "LPB//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_penerimaan_barang" id="no_penerimaan_barang" name="no_penerimaan_barang" placeholder="No. Penerimaan">
                                    <label for="floatingInput">No. Penerimaan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataSupplier as $supplier) : ?>
                                    <option value="<?= $supplier["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['supplier_id'] === $supplier["id"] ? "selected" : "") : ""; ?>><?= strtoupper($supplier["name"]); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($dataPenerimaanBarang)) : ?>
                                    <?php foreach ($dataDivisi as $divisi) : ?>
                                        <option value="<?= $divisi["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['divisi_id'] === $divisi["id"] ? "selected" : "") : ""; ?>><?= strtoupper($divisi["divisi"]); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Departemen</label>
                        </div>
                        <small class="mb-3"><i>Hanya menampilkan departemen yang nomor PO nya belum sepenuhnya diterima</i></small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> multiple class="form-select multiple_po_id" name="multiple_po_id[]" id="multiple_po_id[]">
                                <?php if (!empty($dataPenerimaanBarang)) : ?>
                                    <?php foreach (json_decode($dataPenerimaanBarang['multiple_po_id']) as $i => $id) : ?>
                                        <option selected value="<?= $id ?>"><?= json_decode($dataPenerimaanBarang['multiple_po_no'])[$i] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">No. PO</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($dataPenerimaanBarang)) : ?>
                                    <?php foreach ($dataWarehouse as $warehouse) : ?>
                                        <option value="<?= $warehouse["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['warehouse_id'] === $warehouse["id"] ? "selected" : "") : ""; ?>><?= strtoupper($warehouse["warehouse_name"]); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select kemasan_id" id="kemasan_id" name="kemasan_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataKemasan as $kemasan) : ?>
                                    <option value="<?= $kemasan["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['kemasan_id'] === $kemasan["id"] ? "selected" : "") : ""; ?>><?= strtoupper($kemasan["name"]); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis Kemasan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['jumlah_kemasan'] : ""; ?>" type="number" class="form-control kemasan" id="jumlah_kemasan" name="jumlah_kemasan" placeholder="Jumlah Kemasan">
                            <label for="floatingInput">Jumlah Kemasan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['kemasan'] : ""; ?>" type="text" class="form-control kemasan" id="kemasan" name="kemasan" placeholder="Kemasan">
                            <label for="floatingInput">Keterangan Kemasan (Opsional)</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['no_surat_jalan'] : ""; ?>" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="Nomor Surat Jalan">
                            <label for="floatingInput">Nomor Surat Jalan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['no_invoice'] : ""; ?>" type="text" class="form-control no_invoice" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice">
                            <label for="floatingInput">Nomor Invoice (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select aju_document_type" id="aju_document_type" name="aju_document_type" aria-label="Floating label select example">
                                <option value="">Pilih Dokumen Pabean</option>
                                <?php foreach ($dataAJU as $aju) : ?>
                                    <option <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['bc_type'] === $aju["id"] ? "selected" : "") : ""; ?> value="<?= $aju["id"]; ?>"><?= $aju["value"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Dokumen Pabean (Opsional)</label>
                        </div>
                        <small class="mb-3"><i>Kosongkan jika non pabean</i></small>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" value="<?= !empty($dataPenerimaanBarang) ?  date('d/m/Y', strtotime($dataPenerimaanBarang['tanggal'])) : date('d/m/Y'); ?>" type="text" class="form-control tanggal_penerimaan_lpb" name="tanggal_penerimaan_lpb" id="tanggal_penerimaan_lpb" placeholder="Tanggal Barang Diterima">
                                <label for="floatingInput">Tanggal Barang Diterima</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['ongkos_kirim'] : ""; ?>" class="form-control ongkos_kirim" type="number" id="ongkos_kirim" name="ongkos_kirim" placeholder="Ongkos Kirim">
                            <label for="floatingInput">Ongkos Kirim (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang <?= !empty($dataPenerimaanBarang) ? "(Hanya Menampilkan Barang berdasarkan data yang sudah diposting pada LPB)" : "" ?></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th style="text-align: center;">Kode Barang</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">No PO</th>
                                <th style="text-align: center;">Satuan</th>
                                <th style="text-align: center;">Jml. Order</th>
                                <th style="text-align: center;">Jml. Diterima LPB ini</th>
                                <th style="text-align: center;">Jml. Diterima Total</th>
                                <th style="text-align: center;">Sisa Total</th>
                                <th style="text-align: center;">Harga</th>
                                <th style="text-align: center;">Sub Total</th>
                                <th style="text-align: center;">Keterangan</th>
                                <th style="text-align:center;">Jml. Return</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td></td>
                                <td></td>
                                <td colspan="3" style="text-align: right;">GRAND TOTAL</td>
                                <td style="text-align: center;"><b>0</b></td>
                                <td style="text-align: center;"><b>0</b></td>
                                <td style="text-align: center;"><b>0</b></td>
                                <td style="text-align: center;"><b>0</b></td>
                                <td style="text-align: center;"><b>0.0</b></td>
                                <td style="text-align: center;"><b>0.0</b></td>
                                <td style="text-align: center;"><b></b></td>
                                <td style="text-align: center;"></td>
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
    var listData = [];
    var listFromDatabase = [];

    $(".tanggal_penerimaan_lpb, .tanggal_return_barang").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    // SELECT2
    $('.multiple_po_id').select2({
        placeholder: "Pilih Nomor PO",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        let arr = $('.multiple_po_id').val();
        $.ajax({
            url: `<?= base_url("penerimaan-barang-lokal-bb/return-barang/list-barang"); ?>`,
            method: "GET",
            data: {
                rm_purchase_order_id: JSON.stringify(arr),
                penerimaan_barang_id: $('.penerimaan_barang_id').val()
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listFromDatabase = [];
                listData = res;
                listFromDatabase = res.result;
                drawTable(listData);
            }
        })
    });

    $('.supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('.warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('.divisi_id').select2({
        placeholder: "Pilih Departemen Purchase Order",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('.kemasan_id').select2({
        placeholder: "Pilih Jenis Kemasan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $('.kemasan_id, .divisi_id, .supplier_id, .warehouse_id, .aju_document_type, .multiple_po_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.kemasan_id, .divisi_id, .supplier_id, .warehouse_id, .aju_document_type, .multiple_po_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.kemasan_id, .divisi_id, .supplier_id, .warehouse_id, .aju_document_type, .multiple_po_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // Validator Detail
    var validator_detail = $(".detail-form").validate({
        rules: {
            jml_diterima_lpb: {
                required: true,
                number: true,
                min: 0
            },
            sisa_total: {
                number: true,
                min: 0
            }
        },
        messages: {
            jml_diterima_lpb: {
                required: "Jumlah diterima wajib diisi",
                number: "Masukkan hanya angka",
                min: "Tidak boleh minus"
            },
            sisa_total: {
                min: "Sisa total tidak boleh minus"
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

    // Validator Parent
    var validator = $(".create-form").validate({
        rules: {
            no_penerimaan_barang: {
                required: true,
            },
            supplier_id: {
                required: true,
            },
            warehouse_id: {
                required: true
            },
            kemasan_id: {
                required: true
            },
            jumlah_kemasan: {
                required: true,
                number: true,
                min: 0
            },
            ongkos_kirim: {
                number: true,
                min: 0
            },
            divisi_id: {
                required: true
            },
            tanggal_penerimaan_lpb: {
                required: true
            }
        },
        messages: {
            no_penerimaan_barang: {
                required: "Nomor penerimaan wajib diisi"
            },
            supplier_id: {
                required: "Supplier wajib diisi"
            },
            warehouse_id: {
                required: "Warehouse wajib diisi"
            },
            kemasan_id: {
                required: "Jenis kemasan wajib diisi"
            },
            jumlah_kemasan: {
                required: "Jumlah kemasan wajib diisi",
                number: "Masukkan hanya angka",
                min: "Tidak boleh minus"
            },
            ongkos_kirim: {
                number: "Masukkan hanya angka",
                min: "Tidak boleh minus"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            tanggal_penerimaan_lpb: {
                required: "Tanggal barang diterima wajib diisi"
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

    $('.btn-submit-parent').click(function(e) {
        e.preventDefault();
        if ($('.create-form').valid()) {
            if (listData.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Pilih nomor PO dahulu",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        var id = $('#id').val();
                        var po_no = $('.multiple_po_id').select2('data').map(function(elem) {
                            return elem.text;
                        });

                        var formData = new FormData(document.querySelector(".create-form"));
                        formData.append("acceptance_type", po_no.length > 1 ? "MULTIPLE ORDER" : "SINGLE ORDER");
                        formData.append("multiple_po_id", JSON.stringify($('.multiple_po_id').val()));
                        formData.append("multiple_po_no", JSON.stringify(po_no));
                        formData.append("barangs", JSON.stringify(listData.result));

                        if (id) {
                            formData.append("id", id);
                            $.ajax({
                                url: "<?= base_url("penerimaan-barang-lokal-bb/return-barang/update"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                beforeSend: function(xhr) {
                                    setLoading();
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {
                                            window.location.href = "<?= base_url("penerimaan-barang-lokal-bb") ?>";
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                    }
                                }
                            });
                        } else {
                            $.ajax({
                                url: "<?= base_url("penerimaan-barang-lokal-bb/return-barang/insert"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                beforeSend: function(xhr) {
                                    setLoading();
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {
                                            window.location.href = "<?= base_url("penerimaan-barang-lokal-bb"); ?>";
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
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

    $('.btn-submit-detail').click(function(e) {
        e.preventDefault();
        if ($(".detail-form").valid()) {
            var indexToRemove = -1;
            for (var i = 0; i < listData.result.length; i++) {
                if (Number(listData.result[i].rm_purchase_order_details_id) == Number($('.rm_purchase_order_details_id').val()) && Number(listData.result[i].rm_purchase_order_id) == Number($('.rm_purchase_order_id').val())) {
                    listData.result[i].jml_diterima_lpb = Number($('.jml_diterima_lpb').val());
                    listData.result[i].jml_diterima_total = Number($('.jml_diterima_total').val());
                    listData.result[i].sisa_total = Number($('.sisa_total').val());
                    listData.result[i].sub_total = Number(formatCurrency($('.sub_total').val()));
                    drawTable(listData);
                    break;
                }
            }
        }
    });

    $('.jml_diterima_lpb').keyup(function() {
        var item = null;
        var jml_diterima_lpb = Number($(this).val()) || 0;
        var jml_diterima_lpb_last = Number($('.jml_diterima_lpb_last').val()) || 0;
        if (jml_diterima_lpb == 0) {
            for (var i = 0; i < listData.result.length; i++) {
                if (Number(listData.result[i].rm_purchase_order_details_id) == Number($('.rm_purchase_order_details_id').val()) && Number(listData.result[i].rm_purchase_order_id) == Number($('.rm_purchase_order_id').val())) {
                    item = listData.result[i];
                    var jml_diterima_total_now = Number(item.jml_diterima_total - jml_diterima_lpb_last);
                    var sisa_total_now = Number(item.sisa_total + jml_diterima_lpb_last);
                    $('.sub_total').val('' +
                        formatRupiah(Number(jml_diterima_lpb) * (Number(item.harga_harian) + Number(item.harga_bulanan) + Number(item.harga_umum))));
                    $('.jml_diterima_total').val(jml_diterima_total_now.toFixed(2));
                    $('.sisa_total').val(sisa_total_now.toFixed(2));

                    break;
                }
            }


        } else {
            for (var i = 0; i < listData.result.length; i++) {
                if (Number(listData.result[i].rm_purchase_order_details_id) == Number($('.rm_purchase_order_details_id').val()) && Number(listData.result[i].rm_purchase_order_id) == Number($('.rm_purchase_order_id').val())) {
                    item = listData.result[i];
                    var jml_diterima_total_now = (Number(item.jml_diterima_total) + Number(jml_diterima_lpb) - jml_diterima_lpb_last);
                    var sisa_total_now = item.jml_order - jml_diterima_total_now;
                    $('.sub_total').val('' +
                        formatRupiah(Number(jml_diterima_lpb) * (Number(item.harga_harian) + Number(item.harga_bulanan) + Number(item.harga_umum))));
                    $('.jml_diterima_total').val(jml_diterima_total_now.toFixed(2));
                    $('.sisa_total').val(sisa_total_now.toFixed(2));
                    break;
                }
            }
        }
    });

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

    function drawTable(listData) {
        const table = $('#dataTable');
        table.find('tbody').empty();

        // console.log(listData);
        if (listData.length == 0) {
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td style="text-align:right;" colspan="3"><b>GRAND TOTAL</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0.0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0.0</b></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);

        } else {
            var no = 1;
            var jmlDiterimaLPBTotal = 0;
            var jmlDiterimaTotal = 0;
            var sisaTotal = 0;
            var subTotal = 0;

            $.each(listData.result, function(i, v) {
                var newRow = $('<tr>');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.nama_barang));
                newRow.append($('<td>').text(v.po_no));
                newRow.append($('<td>').text(v.satuan));
                newRow.append($('<td>').text(v.jml_order));
                newRow.append($('<td>').text(v.jml_diterima_lpb));
                newRow.append($('<td>').text(v.jml_diterima_total));
                newRow.append($('<td>').text(v.sisa_total.toFixed(2)));
                newRow.append($('<td>').text(formatRupiah(parseInt(v.harga_sum).toFixed(2) || 0)));
                newRow.append($('<td>').text(formatRupiah(parseInt(v.sub_total).toFixed(2) || 0)));
                newRow.append($('<td>').text(v.keterangan));
                newRow.append($('<td>').html(`
                    <input style="height:30px;padding: 5px 5px;" class="form-control qty-bahan-request" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-stok_total="${v.jml_diterima_total}" data-index="${i}" class="form-control" type="text" value="">
                `));
                table.find('tbody').append(newRow);
                jmlDiterimaLPBTotal += Number(v.jml_diterima_lpb) || 0;
                jmlDiterimaTotal += Number(v.jml_diterima_total) || 0;
                sisaTotal += Number(v.sisa_total) || 0;
                subTotal += Number(v.sub_total) || 0;
            });
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td style="text-align:right;" colspan="3"><b>GRAND TOTAL</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + listData.jml_order_total.toFixed(2) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + jmlDiterimaLPBTotal.toFixed(2) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + jmlDiterimaTotal.toFixed(2) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + sisaTotal.toFixed(2) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + formatRupiah(parseInt(listData.harga_sum_total).toFixed(2) || 0) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + formatRupiah(parseInt(subTotal).toFixed(2) || 0) + '</b></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);

            // Tambahkan event listener untuk mengikuti perubahan nilai qty-barang-jadi
            $('.qty-bahan-request').on('input change', function() {
                var index = $(this).data('index'); // Dapatkan indeks item dari atribut data-index
                var stok_max = $(this).data('stok_total');
                var input_user = $(this).val();

                if (parseFloat(input_user) > parseFloat(stok_max)) {
                    $(this).val(stok_max).change();
                }
                listData.result[index].qtyReturn = input_user;
            });
        }
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_return_barang").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("penerimaan-barang-lokal-bb/return-barang/generate-po-no"); ?>`,
                method: "GET",
                data: {
                    warehouseID: $('#warehouse_id').val()
                },
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_return_barang").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_return_barang").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_return_barang").val("");
                    }
                }
            })
        } else {
            $(".no_return_barang").attr("readonly", false);
            $(".no_return_barang").val("");
        }
    }

    function formatRupiah(angka) {
        if (angka === null) {
            angka = 0;
        }

        angka = angka.toString();
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return ribuanFormatted;
    }

    function formatCurrency(str) {
        var strs = str.replace(/,..$/, '');
        return strs.replace(/[^0-9]/g, '');
    }
</script>

<?php if (!empty($dataPenerimaanBarang)) : ?>
    <script>
        $('.multiple_po_id').change();

        function print(url) {
            window.open(url, "_blank");
        }

        $('.posting-lpb').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Posting LPB ini?',
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
                        url: "<?= base_url("penerimaan-barang-lokal-bb/return-barang/posting"); ?>",
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
                                    window.location.href = "<?= base_url("penerimaan-barang-lokal-bb") ?>";
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
                title: 'Hapus LPB ini?',
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
                        url: "<?= base_url("penerimaan-barang-lokal-bb/return-barang/delete"); ?>",
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
                                    window.location.href = "<?= base_url('penerimaan-barang-lokal-bb') ?>"
                                });
                            }
                        },
                    });
                }
            })
        });
    </script>
<?php endif; ?>

<?= $this->endSection(); ?>
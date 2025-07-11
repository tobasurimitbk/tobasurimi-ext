<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataPenerimaanBarang) ? "Update Penerimaan Barang Import Bahan Baku" : "Tambah Penerimaan Barang Import Bahan Baku" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-barang-import-bb"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataPenerimaanBarang)) : ?>
                <?php if (can('Warehouse', 'P. Barang Import BB', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-barang-import-bb/print/"); ?><?= encrypt($dataPenerimaanBarang['id']); ?>')">
                        Print
                    </button>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($dataPenerimaanBarang)) : ?>
                <?php if ($dataPenerimaanBarang['status_post'] === "WAITING") : ?>
                    <?php if (can('Warehouse', 'P. Barang Import BB', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Warehouse', 'P. Barang Import BB', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-lpb">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Warehouse', 'P. Barang Import BB', 'u')) : ?>
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
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($dataPenerimaanBarang) ? encrypt($dataPenerimaanBarang['id']) : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row mb-1">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Data PO</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['no_penerimaan_barang'] : "LPB-IBB//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_penerimaan_barang" id="no_penerimaan_barang" name="no_penerimaan_barang" placeholder="No. Penerimaan">
                                    <label for="floatingInput">No. Penerimaan</label>
                                </div>
                                <div style="<?= !empty($dataPenerimaanBarang) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
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
                                <option value=""></option>
                                <?php if (!empty($dataPenerimaanBarang)) : ?>
                                    <?php foreach (json_decode(($dataPenerimaanBarang['multiple_po_id'])) as $i => $id) : ?>
                                        <option selected value="<?= $id ?>"><?= json_decode(($dataPenerimaanBarang['multiple_po_no']))[$i] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
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
                            <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['jumlah_kemasan'] : ""; ?>" type="text" class="form-control kemasan" id="jumlah_kemasan" name="jumlah_kemasan" placeholder="Jumlah Kemasan">
                            <label for="floatingInput">Jumlah Kemasan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" value="<?= !empty($dataPenerimaanBarang) ?  date('d/m/Y', strtotime($dataPenerimaanBarang['tanggal'])) : ''; ?>" onchange="changeStatus()" type="text" class="form-control tanggal_penerimaan_lpb" name="tanggal_penerimaan_lpb" id="tanggal_penerimaan_lpb" placeholder="Tanggal Barang Diterima">
                                <label for="floatingInput">Tanggal Barang Diterima</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
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
                            <label for="floatingInput">Jenis Dokumen Pabean (Opsional)</label>
                        </div>
                        <small class="mb-3"><i>Kosongkan jika non pabean</i></small>
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
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['ongkos_kirim'] : ""; ?>" class="form-control ongkos_kirim" type="number" id="ongkos_kirim" name="ongkos_kirim" placeholder="Ongkos Kirim">
                            <label for="floatingInput">Ongkos Kirim (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang <?= !empty($dataPenerimaanBarang) ? "(Hanya Menampilkan Barang berdasarkan data yang sudah disimpan sebelumnya)" : "(Hanya Menampilkan Barang yang Belum Diterima Full)" ?></label>
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
                                <th style="text-align: center;">Jml. Diterima LPB ini (Konversi)</th>
                                <th style="text-align: center;">Jml. Diterima Total</th>
                                <th style="text-align: center;">Sisa Total</th>
                                <th class="label-harga" style="text-align: center;">Harga</th>
                                <th style="text-align: center;">Sub Total</th>
                                <th style="text-align: center;">Keterangan</th>
                                <th style="text-align:center;">Action</th>
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

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="rm_import_po_id" name="rm_import_po_id" id="rm_import_po_id" />
                    <input autocomplete="one-time-code" type="hidden" class="rm_import_po_details_id" name="rm_import_po_details_id" id="rm_import_po_details_id" />
                    <input autocomplete="one-time-code" type="hidden" class="jml_diterima_lpb_last" name="jml_diterima_lpb_last" id="jml_diterima_lpb_last" />
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Barang</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control po_no" id="po_no" name="po_no" placeholder="Nomor PO">
                                <label for="floatingInput">Nomor PO</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control kode_barang" id="kode_barang" name="kode_barang" placeholder="Kode Barang">
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control satuan_order" id="satuan_order" name="satuan_order" placeholder="Satuan Order">
                                <label for="floatingInput">Satuan Order</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control jml_order" id="jml_order" name="jml_order" placeholder="Jumlah Order">
                                <label for="floatingInput">Jumlah Order</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly="true" class="form-control keterangan" id="keterangan" name="keterangan" placeholder="Keterangan" />
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control jml_diterima_lpb" id="jml_diterima_lpb" name="jml_diterima_lpb" placeholder="Qty Diterima LPB ini">
                                <label for="floatingInput">Qty Diterima Saat ini</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control jml_diterima_total" id="jml_diterima_total" name="jml_diterima_total" placeholder="Qty Diterima Total">
                                <label for="floatingInput">Qty Diterima Total</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly type="text" readonly="true" class="form-control sisa_total" id="sisa_total" name="sisa_total" placeholder="Sisa Total">
                                <label for="floatingInput">Sisa Total</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Detail Barang di Dokumen (Bea Cukai)</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control nama_barang_dokumen" id="nama_barang_dokumen" name="nama_barang_dokumen" placeholder="Nama Barang di dokumen">
                                <label for="floatingInput">Nama Barang di dokumen</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly disabled autocomplete="one-time-code" type="text" class="form-control harga_satuan" name="harga_satuan" id="harga_satuan" placeholder="Harga Barang Satuan">
                                <label for="floatingInput" class="label-input-harga">Harga Barang Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly disabled autocomplete="one-time-code" type="text" class="form-control sub_total" name="sub_total" id="sub_total" placeholder="Total Harga">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listData = [];
    var listFromDatabase = [];

    $(".tanggal_penerimaan_lpb").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    // SELECT2
    $('.multiple_po_id').select2({
        placeholder: "Pilih Nomor PO",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        let arr = $('.multiple_po_id').val();
        $.ajax({
            url: `<?= base_url("penerimaan-barang-import-bb/list-barang"); ?>`,
            method: "GET",
            data: {
                rm_import_po_id: JSON.stringify(arr),
                penerimaan_barang_id: $('.id').val()
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
    }).change(function() {
        // GET DIVISI
        $.ajax({
            url: `<?= base_url('penerimaan-barang-import-bb/get-divisi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                id: $(".supplier_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".divisi_id").empty()
                $(".divisi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".divisi_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                })
                $(".divisi_id").val();
            }
        });
    });

    $('.warehouse_id').select2({
        placeholder: "Pilih Warehouse Penerimaan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        changeStatus();
    });

    $('.divisi_id').select2({
        placeholder: "Pilih Departemen Purchase Order",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET PO
        $.ajax({
            url: `<?= base_url('penerimaan-barang-import-bb/get-po'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                id: $(".supplier_id option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".multiple_po_id").empty()
                $(".multiple_po_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".multiple_po_id").append(`<option value="${item.id}">${item.po_no}</option>`)
                })
                $(".multiple_po_id").val([]);
            }
        });
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('penerimaan-barang-import-bb/warehouse'); ?>`,
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
        listData = [];
        drawTable(listData);
    });

    $('.kemasan_id').select2({
        placeholder: "Pilih Jenis Kemasan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $('.btn-discard').click(function() {
        $('.detail-modal').modal('hide');
    })

    // $('.aju_document_type').select2({
    //     placeholder: "Pilih Dokumen Bea Cukai",
    //     theme: "bootstrap-5",
    //     allowClear: true
    // });

    $('.supplier_id, .warehouse_id, .aju_document_type, .multiple_po_id, .divisi_id, .kemasan_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.supplier_id, .warehouse_id, .aju_document_type, .multiple_po_id, .divisi_id, .kemasan_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.supplier_id, .warehouse_id, .aju_document_type, .multiple_po_id, .divisi_id, .kemasan_id')
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
                                url: "<?= base_url("penerimaan-barang-import-bb/update"); ?>",
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
                                            window.location.href = "<?= base_url('penerimaan-barang-import-bb') ?>"
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
                                url: "<?= base_url("penerimaan-barang-import-bb/insert"); ?>",
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
                                            window.location.href = "<?= base_url("penerimaan-barang-import-bb"); ?>";
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
                if (Number(listData.result[i].rm_import_po_details_id) == Number($('.rm_import_po_details_id').val()) && Number(listData.result[i].rm_import_po_id) == Number($('.rm_import_po_id').val())) {
                    var jml_diterima_lpb = Number($('.jml_diterima_lpb').val());
                    var nilai_konversi = listData.result[i].nilai_konversi;

                    listData.result[i].jml_diterima_lpb = Number($('.jml_diterima_lpb').val());
                    listData.result[i].jml_diterima_total = Number($('.jml_diterima_total').val());
                    listData.result[i].sisa_total = Number($('.sisa_total').val());
                    listData.result[i].sub_total = Number(destroyFormatRupiah($('.sub_total').val()));
                    listData.result[i].jml_diterima_lpb_konversi = Number(jml_diterima_lpb * nilai_konversi);
                    drawTable(listData);
                    $('.detail-modal').modal('hide');
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
                if (Number(listData.result[i].rm_import_po_details_id) == Number($('.rm_import_po_details_id').val()) && Number(listData.result[i].rm_import_po_id) == Number($('.rm_import_po_id').val())) {
                    item = listData.result[i];
                    var jml_diterima_total_now = Number(item.jml_diterima_total - jml_diterima_lpb_last);
                    var sisa_total_now = Math.floor((Number(item.sisa_total + jml_diterima_lpb_last)) * 1000) / 1000;
                    var sub_total = Math.floor((Number(jml_diterima_lpb) * Number(item.harga)) * 1000) / 1000;

                    $('.sub_total').val('' +
                        greatFormatRupiah(sub_total));
                    $('.jml_diterima_total').val(jml_diterima_total_now);
                    $('.sisa_total').val(sisa_total_now);

                    break;
                }
            }


        } else {
            for (var i = 0; i < listData.result.length; i++) {
                if (Number(listData.result[i].rm_import_po_details_id) == Number($('.rm_import_po_details_id').val()) && Number(listData.result[i].rm_import_po_id) == Number($('.rm_import_po_id').val())) {
                    item = listData.result[i];
                    var jml_diterima_total_now = (Number(item.jml_diterima_total) + Number(jml_diterima_lpb) - jml_diterima_lpb_last);
                    var sisa_total_now = Math.floor((item.jml_order - jml_diterima_total_now) * 1000) / 1000;
                    var sub_total = Math.floor((Number(jml_diterima_lpb) * Number(item.harga)) * 1000) / 1000;

                    $('.sub_total').val('' +
                        greatFormatRupiah(sub_total));
                    $('.jml_diterima_total').val(jml_diterima_total_now);
                    $('.sisa_total').val(sisa_total_now);
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
            var jmlOrderTotal = 0;
            var hargaTotal = 0;
            var jmlDiterimaLpbKonversi = 0;

            $.each(listData.result, function(i, v) {
                var newRow = $('<tr>');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.nama_barang));
                if (v.status_penerimaan == 1) {
                    newRow.append($('<td>').text(v.po_no + " (PO CLOSED)"));
                } else {
                    newRow.append($('<td>').text(v.po_no));
                }
                newRow.append($('<td>').text(v.satuan));
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.jml_order))));
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.jml_diterima_lpb))));
                newRow.append(
                    $('<td>').text(greatFormatRupiah(parseFloat(v.jml_diterima_lpb_konversi)) + " (" + v.satuan_konversi + ")")
                );
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.jml_diterima_total))));
                if (v.status_penerimaan == 1) {
                    newRow.append($('<td>').text(0));
                } else {
                    newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.sisa_total))));
                }
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.harga) || 0)));
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.sub_total).toFixed(2) || 0)));
                newRow.append($('<td>').text(v.keterangan));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataPenerimaanBarang)) : ?> <?php if ($dataPenerimaanBarang['status_post'] === "FINISH") : ?> `-`
                        <?php else : ?> `
                <button class="btn btn-warning posting-spp mr-1" onclick="editModal('${v.rm_import_po_id}', '${v.rm_import_po_details_id}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                `
                        <?php endif; ?> <?php else : ?> `
                <button class="btn btn-warning posting-spp mr-1" onclick="editModal('${v.rm_import_po_id}', '${v.rm_import_po_details_id}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                `
                    <?php endif; ?>
                ));
                table.find('tbody').append(newRow);
                jmlDiterimaLPBTotal += Number(v.jml_diterima_lpb) || 0;
                jmlDiterimaTotal += Number(v.jml_diterima_total) || 0;
                sisaTotal += Number(v.sisa_total) || 0;
                subTotal += Number(v.sub_total) || 0;
                jmlOrderTotal += Number(v.jml_order) || 0;
                hargaTotal += Number(v.harga) || 0;
                jmlDiterimaLpbKonversi += Number(v.jml_diterima_lpb_konversi) || 0;
            });
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td style="text-align:right;" colspan="3"><b>GRAND TOTAL</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(Math.floor(jmlOrderTotal.toFixed(2) * 1000) / 1000) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(Math.floor(jmlDiterimaLPBTotal.toFixed(2) * 1000) / 1000) + '</b></td>'));
            newRow.append($('<td style="text-align:left;">' + greatFormatRupiah(Math.floor(jmlDiterimaLpbKonversi.toFixed(2) * 1000) / 1000) + '</td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(Math.floor(jmlDiterimaTotal.toFixed(2) * 1000) / 1000) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(Math.floor(sisaTotal.toFixed(2) * 1000) / 1000) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(parseFloat(hargaTotal.toFixed(2)) || 0) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(parseFloat(subTotal.toFixed(2)) || 0) + '</b></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);
        }

    }

    function editModal(rm_import_po_id, rm_import_po_details_id) {
        $('.detail-modal').modal('show');
        $('.title-detail-name').text("Update Penerimaan ");

        var item = null;
        for (var i = 0; i < listData.result.length; i++) {
            if (Number(listData.result[i].rm_import_po_details_id) == Number(rm_import_po_details_id) && Number(listData.result[i].rm_import_po_id) == Number(rm_import_po_id)) {
                item = listData.result[i];
                break;
            }
        }

        $('.rm_import_po_id').val(item.rm_import_po_id);
        $('.rm_import_po_details_id').val(item.rm_import_po_details_id);
        $('.po_no').val(item.po_no);
        $('.kode_barang').val(item.kode_barang);
        $('.nama_barang').val(item.nama_barang);
        $('.satuan_order').val(item.satuan);
        $('.jml_order').val(item.jml_order);
        $('.keterangan').val(item.keterangan);
        $('.jml_diterima_lpb').val(item.jml_diterima_lpb);
        $('.jml_diterima_total').val(item.jml_diterima_total);
        $('.sisa_total').val(item.sisa_total.toFixed(2));
        $('.nama_barang_dokumen').val(item.nama_barang_master);
        $('.harga_satuan').val("" + greatFormatRupiah(Number(item.harga) || 0));
        $('.sub_total').val("" + greatFormatRupiah(Number(item.sub_total).toFixed(2) || 0));
        $('.jml_diterima_lpb_last').val(item.jml_diterima_lpb);
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        let tanggal = $('#tanggal_penerimaan_lpb').val();
        if (value) {
            $(".no_penerimaan_barang").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("penerimaan-barang-import-bb/generate-po-no"); ?>`,
                method: "GET",
                data: {
                    warehouseID: $('#warehouse_id').val(),
                    tanggal: tanggal,
                    status_penerimaan: "IMPORT",
                    tipe_bahan: "BAKU",
                    prefix: "LPB-IBB"
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_penerimaan_barang").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_penerimaan_barang").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_penerimaan_barang").val("");
                    }
                }
            })
        } else {
            $(".no_penerimaan_barang").attr("readonly", false);
            $(".no_penerimaan_barang").val("");
        }
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
                        url: "<?= base_url("penerimaan-barang-import-bb/posting"); ?>",
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
                                    window.location.href = "<?= base_url('penerimaan-barang-import-bb') ?>"
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                csrf.val(response.token);
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
                        url: "<?= base_url("penerimaan-barang-import-bb/delete"); ?>",
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
                                    window.location.href = "<?= base_url('penerimaan-barang-import-bb') ?>"
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
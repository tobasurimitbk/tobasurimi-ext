<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah PO Import BP</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-import-bahan-penolong"); ?>">
                Batal
            </a>

            <?php if (!empty($dataPOImport)) { ?>

                <?php if ($dataPOImport->is_posted === "0") { ?>
                    <button class="btn btn-hapus delete-parent float-right">
                        Hapus
                    </button>
                <?php } ?>

                <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("po-import-bahan-penolong/print/"); ?><?= $dataPOImport->id; ?>')">
                    Print
                </button>

                <?php if ($dataPOImport->is_posted === "0") { ?>
                    <button class="btn btn-success posting-spp float-right posting-po">
                        Posting
                    </button>
                <?php } ?>

                <?php if ($dataPOImport->is_posted === "1") {
                    if ($dataPOImport->status_penerimaan === "0") { ?>
                        <button class="btn btn-hapus close-parent float-right">
                            Close PO
                        </button>
                <?php }
                } ?>

            <?php } ?>

            <?php if (!empty($dataPOImport)) {
                if ($dataPOImport->is_posted === "0") { ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php }
            } else { ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data PO</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($dataPOImport) ? $dataPOImport->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-control input-picker po_date" id="po_date" name="po_date" placeholder="Tanggal Dibuat" value="<?= !empty($dataPOImport) ? ($dataPOImport->po_date ? date("d/m/Y", strtotime($dataPOImport->po_date)) : "")  : $today; ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? 'readonly' : ''; ?> type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="No. PO" value="<?= !empty($dataPOImport) ? $dataPOImport->po_no : ""; ?>">
                                    <label for="floatingInput">No. PO</label>
                                </div>
                                <div style=" <?= !empty($dataPOImport) ? 'display:none' : ''; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select company_id" id="company_id" name="company_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($company as $c) : ?>
                                    <option <?= !empty($dataPOImport) ? ($dataPOImport->company_id == $c['id'] ? 'selected' : '') : '' ?> value="<?= $c['id'] ?>"><?= strtoupper($c['company']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Company</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select division_id" id="division_id" name="division_id" aria-label="Floating label select example">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataSupplier as $supplier) : ?>
                                    <option <?= !empty($dataPOImport) ? ($dataPOImport->supplier_id === $supplier["id"] ? "selected" : "") : ""; ?> value="<?= $supplier["id"]; ?>" data-name="<?= $supplier["name"]; ?>"><?= strtoupper($supplier["name"]); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" class="form-control input-picker payment_date" id="payment_date" name="payment_date" placeholder="Tanggal Pembayaran" value="<?= !empty($dataPOImport) ? ($dataPOImport->payment_date ? date("d/m/Y", strtotime($dataPOImport->payment_date)) : "")  : ""; ?>">
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-payment-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select currency" id="currency" name="currency" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataValuta as $valuta) : ?>
                                    <option <?= !empty($dataPOImport) ? (($dataPOImport->currency ? formatter($dataPOImport->currency, "STR_TO_INT") : 0) === formatter($valuta["id"], "STR_TO_INT") ? "selected" : "") : ""; ?> value="<?= $valuta["id"]; ?>"><?= $valuta["value"]; ?> - <?= $valuta["description"]; ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Valas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="text" value="<?= !empty($dataPOImport) ? $dataPOImport->payment_term : ""; ?>" class="form-control payment_term" name="payment_term" id="payment_term" placeholder="Termin Pembayaran (Opsional)">
                            <label for="floatingInput">Termin Pembayaran (Opsional)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Lainnya</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOImport) ? $dataPOImport->shipper : ""; ?>" type="text" class="form-control shipper" id="shipper" name="shipper" placeholder="Shipper (Opsional)">
                            <label for="floatingInput">Shipper (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOImport) ? $dataPOImport->consigne : ""; ?>" type="text" class="form-control consigne" id="consigne" name="consigne" placeholder="Consigne (Opsional)">
                            <label for="floatingInput">Consigne (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOImport) ? $dataPOImport->port_origin : ""; ?>" type="text" class="form-control port_origin" id="port_origin" name="port_origin" placeholder="Port Of Origin">
                            <label for="floatingInput">Port Of Origin</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOImport) ? $dataPOImport->port_destination : ""; ?>" type="text" class="form-control port_destination" id="port_destination" name="port_destination" placeholder="Port Of Destination">
                            <label for="floatingInput">Port Of Destination</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOImport) ? $dataPOImport->location_transaction : ""; ?>" type="text" class="form-control location_transaction" id="location_transaction" name="location_transaction" placeholder="Lokasi Transaksi (Opsional)">
                            <label for="floatingInput">Lokasi Transaksi (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select shipment" id="shipment" name="shipment" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataShipment as $shipment) : ?>
                                    <option <?= !empty($dataPOImport) ? (($dataPOImport->shipment ? formatter($dataPOImport->shipment, "STR_TO_INT") : 0) === formatter($shipment["id"], "STR_TO_INT") ? "selected" : "") : ""; ?> value="<?= $shipment["id"]; ?>"><?= $shipment["value"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Shipment</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-control input-picker latest_shipment_date" id="latest_shipment_date" name="latest_shipment_date" placeholder="Latest Shipment" value="<?= !empty($dataPOImport) ? ($dataPOImport->latest_shipment_date ? date("d/m/Y", strtotime($dataPOImport->latest_shipment_date)) : "")  : ""; ?>">
                                    <label for="floatingInput">Latest Shipment Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-latest-shipment-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOImport) ? $dataPOImport->attn : ""; ?>" type="text" class="form-control attn" id="attn" name="attn" placeholder="ATTN">
                            <label for="floatingInput">ATTN</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOImport) ? $dataPOImport->note : ""; ?>" type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
                            <label for="floatingInput">Catatan (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>
                    <div class="col-md-6">
                        <?php if (!empty($dataPOImport)) : ?>
                            <?php if ($dataPOImport->is_posted) : ?>

                            <?php else : ?>
                                <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                                </button>
                            <?php endif; ?>
                        <?php else : ?>
                            <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Barang</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>QTY</th>
                                <th>Disc (%)</th>
                                <th>Tambahan</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="3"></td>
                                <td><b>TOTAL</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0</b></td>
                                <td><b>0</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0.00</b></td>
                                <td></td>
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
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Barang</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="hidden" name="barang_update_id" id="barang_update_id" class="barang_update_id">
                                <select class="form-select barang_id" id="barang_id" name="barang_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($barang as $s) : ?>
                                        <option data-satuan_id="<?= $s['satuan_id'] ?>" data-nama_barang="<?= strtoupper($s['barang_name']) ?>" data-kode_barang="<?= $s['kode_barang'] ?>" value="<?= $s['id'] ?>">
                                            <?= strtoupper($s['kode_barang']) . " ( " . strtoupper($s['barang_name']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Pilih Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-0">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Harga</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select disabled class="form-select satuan_id" name="satuan_id" id="satuan_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($satuan as $s) : ?>
                                        <option data-nama_satuan="<?= strtoupper($s['nama_satuan']) ?>" value="<?= $s['id'] ?>">
                                            <?= strtoupper($s['nama_satuan']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" class="form-control harga_satuan" name="harga_satuan" id="harga_satuan" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control diskon" name="diskon" value="0" id="diskon" placeholder="Discount (%) (Opsional)">
                                <label for="floatingInput">Diskon (%)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" class="form-control biaya_tambahan" name="biaya_tambahan" id="biaya_tambahan" placeholder="Biaya Tambahan (Opsional)">
                                <label for="floatingInput">Biaya Tambahan (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    // init barang list
    var listBarang = [];
    var totalHargaSatuan = 0;
    var totalQty = 0;
    var totalDiskon = 0;
    var totalTambahan = 0;
    var totalHarga = 0;
    // init select2
    $('#company_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    }).change(function() {
        var companyID = $(this).val();
        var formData = new FormData();
        formData.append("companyID", companyID);
        $.ajax({
            url: "<?= base_url("po-import-bahan-penolong/find-divisi"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                $("#division_id").empty();
                $("#division_id").append(`<option value=""></option>`);
                response.data.forEach(function(item) {
                    $("#division_id").append(`<option  value="${item.id}">${item.divisi}</option>`);
                });
                <?php if (!empty($dataPOImport)) : ?>
                    $('#division_id').val("<?= $dataPOImport->divisi_id ?>").change();
                <?php endif; ?>
            }
        });
    });
    $('#division_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    }).change(function() {

    });

    $('#supplier_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    }).change(function() {

    });

    $('#currency').select2({
        placeholder: "",
        theme: "bootstrap-5"
    }).change(function() {

    });

    $('#barang_id').select2({
        placeholder: "Pilih Bahan Penolong",
        theme: "bootstrap-5"
    }).change(function() {
        $('#satuan_id').val(($(this).find("option:selected").data("satuan_id")));
    });

    $("#payment_date,#latest_shipment_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
    $("#division_id,#supplier_id,#currency,#barang_id,#company_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".po_no").attr("readonly", true);
            $(".po_no").val("AUTO GENERATE");
        } else {
            $(".po_no").attr("readonly", false);
            $(".po_no").val("");
        }
    }
    // tambah barang
    $('.btn-add').click(function() {
        $('.title-detail-name').text('Tambah ');
        $('.detail-modal').show();
        resetForm();
    });
    // close modal
    $('.btn-hide-detail').click(function() {
        $('.detail-modal').hide();
    });
    // HARGA SATUAN DAN QTY CHANE
    $('#harga_satuan,#qty,#biaya_tambahan,#diskon').keyup(function() {
        var hargaSatuan = parseInt($('#harga_satuan').val()) || 0;
        var qty = parseInt($('#qty').val()) || 1;
        var biayaTambahan = parseInt($('#biaya_tambahan').val()) || 0;
        var diskon = parseInt($('#diskon').val()) || 0;
        var diskonHarga = (diskon / 100) * (hargaSatuan * qty);

        var total = (((hargaSatuan * qty) - diskonHarga) + biayaTambahan);
        $('#total').val(formatRupiah(total));
    });

    // VALIDATOR DETAIL
    var validatorBarang = $(".detail-form").validate({
        rules: {
            barang_id: {
                required: true
            },
            satuan_id: {
                required: true
            },
            qty: {
                required: true
            },
            harga_satuan: {
                required: true
            },
            diskon: {
                required: true,
            },
            total: {
                required: true,
            }
        },
        messages: {
            barang_id: {
                required: "Pilih Bahan penolong"
            },
            satuan_id: {
                required: "Satuan wajib diisi"
            },
            qty: {
                required: "Kuantitas barang wajib diisi"
            },
            harga_satuan: {
                required: "Harga Satuan wajib diisi"
            },
            diskon: {
                required: "Diskon wajib diisi (beri angka 0 jika tidak ada diskon)"
            },
            total: {
                required: "Total biaya wajib diisi"
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
    // VALIDATOR PO
    var validatorPO = $(".create-form").validate({
        rules: {
            po_date: {
                required: true
            },
            po_no: {
                required: true
            },
            company_id: {
                required: true
            },
            division_id: {
                required: true
            },
            supplier_id: {
                required: true,
            },
            payment_date: {
                required: true,
            },
            currency: {
                required: true
            },
            port_origin: {
                required: true
            },
            port_destination: {
                required: true
            },
            shipment: {
                required: true
            },
            latest_shipment_date: {
                required: true
            },
            attn: {
                required: true
            }
        },
        messages: {
            po_date: {
                required: "Tanggal PO Dibuat wajib diisi"
            },
            po_no: {
                required: "Nomor PO wajib diisi"
            },
            company_id: {
                required: "Pilih unit company"
            },
            division_id: {
                required: "Pilih departemen"
            },
            supplier_id: {
                required: "Pilih supplier"
            },
            payment_date: {
                required: "Tanggal pembayaran wajib diisi"
            },
            currency: {
                required: "Pilih mata uang"
            },
            port_origin: {
                required: "Port of origin wajib diisi"
            },
            port_destination: {
                required: "Port of destination wajib diisi"
            },
            shipment: {
                required: "Pilih jalur pengiriman"
            },
            latest_shipment_date: {
                required: "Waktu pengiriman wajib diisi"
            },
            attn: {
                required: "ATTN wajib diisi"
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

    $('.btn-submit-parent').click(function() {
        if ($('.create-form').valid()) {
            if (listBarang.length == 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Barang masih kosong',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            } else {
                var id = $('#id').val();
                if (id) {
                    // UPDATE
                    Swal.fire({
                        icon: 'question',
                        title: 'Update Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var id = $('#id').val();
                            // required form
                            var poDate = $('#po_date').val();
                            var poNo = $('#po_no').val();
                            var companyID = $('#company_id').val();
                            var divisionID = $('#division_id').val();
                            var supplierID = $('#supplier_id').val();
                            var paymentDate = $('#payment_date').val();
                            var currency = $('#currency').val();
                            var portOrigin = $('#port_origin').val();
                            var portDestination = $('#port_destination').val();
                            var shipment = $('#shipment').val();
                            var latestShipmentDate = $('#latest_shipment_date').val();
                            var attn = $('#attn').val();
                            // optional form
                            var paymentTerm = $('#payment_term').val();
                            var shipper = $('#shipper').val();
                            var consigne = $('#consigne').val();
                            var locationTransaction = $('#location_transaction').val();
                            var note = $('#note').val();
                            // append
                            var formData = new FormData();
                            formData.append("id", id);
                            formData.append("poDate", poDate);
                            formData.append("poNo", poNo);
                            formData.append("companyID", companyID);
                            formData.append("divisionID", divisionID);
                            formData.append("supplierID", supplierID);
                            formData.append("paymentDate", paymentDate);
                            formData.append("currency", currency);
                            formData.append("portOrigin", portOrigin);
                            formData.append("portDestination", portDestination);
                            formData.append("shipment", shipment);
                            formData.append("latestShipmentDate", latestShipmentDate);
                            formData.append("attn", attn);
                            formData.append("paymentTerm", paymentTerm);
                            formData.append("shipper", shipper);
                            formData.append("consigne", consigne);
                            formData.append("locationTransaction", locationTransaction);
                            formData.append("note", note);
                            formData.append("total", totalHarga);
                            formData.append("listBarang", JSON.stringify(listBarang));

                            $.ajax({
                                url: "<?= base_url("po-import-bahan-penolong/update"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                        }
                    })
                } else {
                    // CREATE
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
                            // required form
                            var poDate = $('#po_date').val();
                            var poNo = $('#po_no').val();
                            var companyID = $('#company_id').val();
                            var divisionID = $('#division_id').val();
                            var supplierID = $('#supplier_id').val();
                            var paymentDate = $('#payment_date').val();
                            var currency = $('#currency').val();
                            var portOrigin = $('#port_origin').val();
                            var portDestination = $('#port_destination').val();
                            var shipment = $('#shipment').val();
                            var latestShipmentDate = $('#latest_shipment_date').val();
                            var attn = $('#attn').val();
                            // optional form
                            var paymentTerm = $('#payment_term').val();
                            var shipper = $('#shipper').val();
                            var consigne = $('#consigne').val();
                            var locationTransaction = $('#location_transaction').val();
                            var note = $('#note').val();
                            // append
                            var formData = new FormData();
                            formData.append("poDate", poDate);
                            formData.append("poNo", poNo);
                            formData.append("companyID", companyID);
                            formData.append("divisionID", divisionID);
                            formData.append("supplierID", supplierID);
                            formData.append("paymentDate", paymentDate);
                            formData.append("currency", currency);
                            formData.append("portOrigin", portOrigin);
                            formData.append("portDestination", portDestination);
                            formData.append("shipment", shipment);
                            formData.append("latestShipmentDate", latestShipmentDate);
                            formData.append("attn", attn);
                            formData.append("paymentTerm", paymentTerm);
                            formData.append("shipper", shipper);
                            formData.append("consigne", consigne);
                            formData.append("locationTransaction", locationTransaction);
                            formData.append("note", note);
                            formData.append("total", totalHarga);
                            formData.append("listBarang", JSON.stringify(listBarang));

                            $.ajax({
                                url: "<?= base_url("po-import-bahan-penolong/save"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                            window.location.href = "<?= base_url('po-import-bahan-penolong') ?>"
                                        })
                                    }
                                }
                            });
                        }
                    });
                }
            }
        }
    });

    $('.btn-submit-detail').click(function() {
        if ($('.detail-form').valid()) {
            var barang_update_id = $('#barang_update_id').val();
            if (barang_update_id != "") {
                // UPDATE
                Swal.fire({
                    icon: 'question',
                    title: 'Update barang ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var indexToRemove = -1;
                        for (var i = 0; i < listBarang.length; i++) {
                            if (listBarang[i].barang_id === barang_update_id) {
                                indexToRemove = i;
                                break;
                            }
                        }
                        if (indexToRemove !== -1) {
                            listBarang.splice(indexToRemove, 1);
                            insertList();
                            resetForm();
                            $('.detail-modal').hide();
                        }
                    }
                });
            } else {
                // TAMBAH
                var isAdd = false;
                for (var i = 0; i < listBarang.length; i++) {
                    if (listBarang[i].barang_id === $('#barang_id').val()) {
                        indexToRemove = i;
                        isAdd = true;
                        break;
                    }
                }
                if (isAdd) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Barang Sudah Ada',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        reverseButtons: true,
                        confirmButtonText: 'Oke',
                    })
                } else {
                    Swal.fire({
                        icon: 'question',
                        title: 'Tambah barang ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            insertList();
                            $('.detail-modal').hide();
                        }
                    })
                }

            }

        }
    });

    function insertList() {
        listBarang.push({
            barang_id: $('#barang_id').val(),
            kode_barang: $('#barang_id').find("option:selected").data("kode_barang"),
            nama_barang: $('#barang_id').find("option:selected").data("nama_barang"),
            satuan_id: $('#satuan_id').val(),
            nama_satuan: $('#satuan_id').find("option:selected").data("nama_satuan"),
            qty: $('#qty').val(),
            diskon: $('#diskon').val(),
            harga_satuan: $('#harga_satuan').val() || 0,
            biaya_tambahan: $('#biaya_tambahan').val() || 0,
            total: $('#total').val(),
        });
        $(".detail-form input, .detail-form select").val("");
        $(".barang_id").val("").change();
        $(".diskon").val('0');
        // reset update flag
        $('#barang_update_id').val("")
        drawTabel(listBarang);
    }

    function drawTabel(listBarang) {
        const table = $('#dataTable');
        var no = 1;
        table.find('tbody').empty();
        totalHargaSatuan = 0;
        totalQty = 0;
        totalDiskon = 0;
        totalTambahan = 0;
        totalHarga = 0;
        console.log(listBarang);
        $.each(listBarang, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align:center;">').text(no++));
            newRow.append($('<td>').text(v.kode_barang));
            newRow.append($('<td>').text(v.nama_barang));
            newRow.append($('<td>').text(v.nama_satuan));
            newRow.append($('<td>').text(formatRupiah(v.harga_satuan)));
            newRow.append($('<td>').text(v.qty));
            newRow.append($('<td>').text(v.diskon));
            newRow.append($('<td>').text(formatRupiah(v.biaya_tambahan)));
            newRow.append($('<td>').text(v.total));
            <?php if (!empty($dataPOImport)) : ?>
                <?php if ($dataPOImport->is_posted) : ?>
                    newRow.append($('<td>').html(
                        `
                        -
                    `
                    ));
                <?php else : ?>
                    newRow.append($('<td>').html(
                        `
                        <button class="btn btn-warning posting-spp mr-1" onclick="detailRow('${v.barang_id}')">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button><button class="btn btn-danger" onclick="deleteRow('${v.barang_id}')">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    `
                    ));
                <?php endif ?>
            <?php else : ?>
                newRow.append($('<td>').html(
                    `
                        <button class="btn btn-warning posting-spp mr-1" onclick="detailRow('${v.barang_id}')">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button><button class="btn btn-danger" onclick="deleteRow('${v.barang_id}')">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    `
                ));
            <?php endif; ?>
            table.find('tbody').append(newRow);
            totalHargaSatuan += parseInt(v.harga_satuan);
            totalQty += parseInt(v.qty);
            totalDiskon += parseInt(v.diskon);
            totalTambahan += parseInt(v.biaya_tambahan);
            totalHarga += parseInt(formatCurrency(v.total));
        });
        table.find('tfoot').empty();
        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="4"><b>TOTAL</b></td>'));
        newRow.append($('<td style="text-align:left;"><b>' + formatRupiah(totalHargaSatuan) + '</b></td>'));
        newRow.append($('<td style="text-align:left;"><b>' + totalQty + '</b></td>'));
        newRow.append($('<td style="text-align:left;"><b>' + totalDiskon + '</b></td>'));
        newRow.append($('<td style="text-align:left;"><b>' + formatRupiah(totalTambahan) + '</b></td>'));
        newRow.append($('<td style="text-align:left;"><b>' + formatRupiah(totalHarga) + '</b></td>'));
        newRow.append($('<td></td>'));
        table.find('tfoot').append(newRow);
    }

    function deleteRow(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Barang ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var indexToRemove = -1;
                for (var i = 0; i < listBarang.length; i++) {
                    if (listBarang[i].barang_id === id) {
                        indexToRemove = i;
                        break;
                    }
                }
                if (indexToRemove !== -1) {
                    listBarang.splice(indexToRemove, 1);
                }
                drawTabel(listBarang);
                resetForm();
            }
        })

    }

    function detailRow(id) {
        var item = null;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].barang_id === id) {
                item = listBarang[i];
                break;
            }
        }
        $('#barang_id, #barang_update_id').val(item.barang_id).change();
        $('#harga_satuan').val(item.harga_satuan);
        $('#qty').val(item.qty);
        $('#diskon').val(item.diskon);
        $('#biaya_tambahan').val((item.biaya_tambahan == 0) ? "" : item.biaya_tambahan).val();
        $('#biaya_tambahan').change();
        $('#total').val(item.total);
        $('#barang_id').attr('disabled', true);

        $('.title-detail-name').text('Update ');
        $('.detail-modal').show();

    }

    function resetForm() {
        $('#barang_id').attr('disabled', false);
        $(".detail-form input, .detail-form select").val("");
        $(".barang_id").val("").change();
        $(".diskon").val('0');
    }

    function formatCurrency(str) {
        var strs = str.replace(/,..$/, '');
        return strs.replace(/[^0-9]/g, '');
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
        return ribuanFormatted + ',' + desimal;
    }
</script>
<?php if (!empty($dataPOImport)) : ?>
    <script>
        $('#company_id').change();
        <?php foreach ($dataPOImportDetail as $d) : ?>
            listBarang.push({
                barang_id: "<?= $d['barang_id'] ?>",
                kode_barang: "<?= $d['kode_barang'] ?>",
                nama_barang: <?= json_encode($d['nama_barang']) ?>,
                satuan_id: <?= $d['unit'] ?>,
                nama_satuan: "<?= $d['nama_satuan'] ?>",
                qty: <?= $d['qty'] ?>,
                diskon: <?= $d['disc'] ?>,
                harga_satuan: <?= $d['price'] ?>,
                biaya_tambahan: <?= $d['additional_cost'] ?>,
                total: formatRupiah("<?= (($d['price'] * $d['qty']) - (($d['disc'] / 100) * ($d['price'] * $d['qty']))) + $d['additional_cost'] ?>"),
            });
        <?php endforeach; ?>
        drawTabel(listBarang);

        // HAPUS
        $('.delete-parent').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan hapus PO ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var id = $('#id').val();
                    formData.append("id", id);
                    $.ajax({
                        url: "<?= base_url("po-import-bahan-penolong/delete"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                    window.location.href = "<?= base_url('po-import-bahan-penolong') ?>"
                                })
                            }

                        }
                    });
                }
            })
        });

        //POSTING
        $('.posting-po').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan posting PO ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var id = $('#id').val();
                    formData.append("id", id);
                    $.ajax({
                        url: "<?= base_url("po-import-bahan-penolong/update-status"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                }
            })
        });

        // CLOSE PO
        $('.close-parent').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan close PO ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var id = $('#id').val();
                    formData.append("id", id);
                    $.ajax({
                        url: "<?= base_url("po-import-bahan-penolong/close-po"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                }
            })
        });

        function print(url) {
            window.open(url, "_blank");
        }
    </script>
<?php endif; ?>
<?= $this->endSection(); ?>
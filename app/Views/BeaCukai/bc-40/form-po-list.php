<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section section-form">

    <div class="section-header">
        <h1 class="title-name">Dokumen BC 4.0</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-40"); ?>">
                Kembali
            </a>
            <a class="btn btn-info btn-print float-right text-white" href="<?= base_url('bea-cukai-bc-40/id/header/' . encrypt($bcPo['id'])) ?>">
                Form Ceisa
            </a>
            <?php if ($bcPo['status_posting']): ?>
                <button class="btn btn-show-form btn-save float-right" id="btn_update_no_aju_no_daftar">
                    Ubah No Aju & No Daftar
                </button>
            <?php endif; ?>
            <?php if ($bcPo['status_posting'] === "0") : ?>
                <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                    Hapus
                </button>
                <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting()">
                    Posting
                </button>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="font-weight: bold;  <?= session()->get('theme') == 'dark' ? 'color:white;' : 'color:black;' ?>">
            DATA BARANG UNTUK PEMBUATAN DOKUMEN BEA CUKAI 4.0
        </div>
        <div class="card-body">
            <form class="create-form">
                <input type="hidden" id="bc_purchase_order_id" name="bc_purchase_order_id" value="<?= $bcPo['id'] ?>">
                <input type="hidden" name="po_type" value="<?= $bcPo['po_type']; ?>">
                <input type="hidden" name="supplier_id" value="<?= $bcPo['supplier_id']; ?>">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select disabled class="form-select" id="po_type" name="" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= $bcPo['po_type'] == "LOKAL BAKU" ? 'selected' : '' ?> value="LOKAL BAKU">PO LOKAL BAHAN BAKU</option>
                                <option <?= $bcPo['po_type'] == "LOKAL PENOLONG" ? 'selected' : '' ?> value="LOKAL PENOLONG">PO LOKAL BAHAN PENOLONG</option>

                            </select>
                            <label style="z-index: 1;">Tipe Purchase Order</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select disabled class="form-select" id="supplier_id" name="" aria-label="Floating label select example">
                                <option selected value="<?= $bcPo['supplier_id'] ?>">
                                    <?= $bcPo['supplier_name'] ?>
                                </option>
                            </select>
                            <label style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input value="<?= $noAju ?>" readonly type="text" class="form-control <?= session()->get('theme') == "light" ? "bg-white" : "" ?>" id="no_pengajuan_preview" placeholder="">
                                <label>Nomor Pengajuan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button" onclick="noAjuShowModal()">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">

                            <div class="form-floating mb-3">
                                <input value="<?= $bcPo['no_daftar'] ?>" autocomplete="one-time-code" type="number" class="form-control no_daftar" id="no_daftar" name="no_daftar" placeholder="No Daftar">
                                <label for="floatingInput">Nomor Daftar</label>
                            </div>
                            <?php if (!empty($bc40)): ?>
                                <?php if ($bc40['status_dokumen'] == "Sudah Kirim"): ?>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-success" data-toggle="modal" type="button" onclick="ambilNoDaftar()">
                                            <i class="fa-solid fa-download"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($bcPo) ? date('d/m/Y', strtotime($bcPo['createdAt'])) : "" ?>" autocomplete="one-time-code" type="text" class="form-control tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dokumen">
                                <label for="floatingInput">Tanggal Dokumen</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 21px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <?php if ($bcPo['status_posting'] === "0") : ?>
                <div class="row mt-3">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Daftar Purchase Order yang Belum Dibuat Dokumen Bea Cukai</label>
                        <div class="row mt-3">
                            <div class="col-sm-3">
                                <div class="form-floating mb-2 mt-1" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" class="form-control input-picker start_date" id="start_date" name="start_date" placeholder="Tanggal Mulai PO" value="">
                                            <label for="floatingInput">Filter Tanggal Mulai PO</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-floating mt-1" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" class="form-control input-picker end_date" id="end_date" name="end_date" placeholder="Tanggal Selesai PO" value="<?= date('d/m/Y') ?>">
                                            <label for="floatingInput">Filter Tanggal Selesai PO</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-table-button-tts">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable dataTable1" id="dataTable1" width="100%" border="1" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center; width:5px;">No</th>
                                        <th style="text-align: center; width:5px;">#</th>
                                        <th style="text-align: center;">Tgl PO</th>
                                        <th style="text-align: center;">Tgl LPB</th>
                                        <th style="text-align: center;">No LPB</th>
                                        <th style="text-align: center;">No PO</th>
                                        <th style="text-align: center;">No SPP</th>
                                        <th style="text-align: center;">Kode</th>
                                        <th style="text-align: center;">Barang</th>
                                        <th style="text-align: center;">Qty PO</th>
                                        <th style="text-align: center;">Qty Diterima</th>
                                        <th style="text-align: center;">Harga</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary mt-3" id="select-item-btn">Pilih</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Daftar Purchase Order yang Akan Dibuat Dokumen Bea Cukai</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Pungutan</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row mt-2">
                        <div class="col-md-12 col-table-button-tts">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable dataTable2" id="dataTable2" width="100%" border="1" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align: center; width:5px;">No</th>
                                            <th style="text-align: center;">Tgl PO</th>
                                            <th style="text-align: center;">Tgl LPB</th>
                                            <th style="text-align: center;">No LPB</th>
                                            <th style="text-align: center;">No PO</th>
                                            <th style="text-align: center;">No SPP</th>
                                            <th style="text-align: center;">Kode</th>
                                            <th style="text-align: center;">Barang</th>
                                            <th style="text-align: center;">Qty PO</th>
                                            <th style="text-align: center;">Qty Diterima</th>
                                            <th style="text-align: center;">Qty Diterima (Konversi)</th>
                                            <th style="text-align: center;">Harga</th>
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
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="pungutanPerBarangTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th colspan="8" style="text-align:center;">
                                            Pungutan per barang yang harus diisi
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left; width:5px;">No</th>
                                        <th style="text-align: left;">Kode</th>
                                        <th style="text-align: left;">Barang</th>
                                        <th style="text-align: left;">Qty PO</th>
                                        <th style="text-align: left;">Qty Diterima</th>
                                        <th style="text-align: left;">Harga</th>
                                        <th style="text-align: left;">Pungutan</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>

                            </table>
                        </div>
                        <div class="col-sm-6">

                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="hasilPungutanTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th colspan="5" style="text-align: center;">
                                            Hasil pungutan
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center;width:10px;">No</th>
                                        <th style="text-align: left;">Pungutan</th>
                                        <th style="text-align: center;">Tidak Dipungut</th>
                                        <th style="text-align: left;">Dibebaskan</th>
                                        <th style="text-align: left;">Ditangguhkan</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
<div class="modal fade" id="modalUpdateNoAju" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Ubah Nomor Pengajuan</h5>
            </div>
            <form id="form-update">
                <input type="hidden" name="bc_purchase_order_id" class="bc_purchase_order_id" id="bc_purchase_order_id" value="<?= encrypt($bcPo['id']) ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="tanggal_pengajuan" value="" name="tanggal_pengajuan" type="text" class="tanggal_pengajuan form-control" placeholder="">
                                    <label>Tanggal</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_urut_dokumen" name="no_urut_dokumen" type="number" class="no_urut_dokumen form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Nomor Urut</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="kode_kantor" name="kode_kantor" type="number" class="kode_kantor form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Kode Kantor</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_pengajuan" value="<?= $noAju ?>" name="no_pengajuan" type="text" readonly class="no_pengajuan form-control" placeholder="">
                                <label>Preview Nomor Pengajuan</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="ubahNoAjuButton">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>
<div class="modal fade" id="modalUpdatePungutan" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Pungutan</h5>
            </div>
            <form class="form-update-pungutan">
                <div class="modal-body">
                    <input type="hidden" name="penerimaan_barang_id" id="penerimaan_barang_id">
                    <input type="hidden" name="barang1_id" id="barang1_id">

                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="kode_barang" name="kode_barang" type="text" readonly class="kode_barang form-control" placeholder="Kode Barang">
                                <label>Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="barang_name" name="barang_name" type="text" readonly class="nama_barang form-control" placeholder="Nama Barang">
                                <label>Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="seri_barang" name="seri_barang" type="text" readonly class="seri_barang form-control" placeholder="Seri Barang">
                                <label>Seri Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="harga" name="harga" type="text" readonly class="harga form-control" placeholder="Harga">
                                <label>Harga</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kode_satuan_barang" id="kode_satuan_barang" name="kode_satuan_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label style="z-index: 1;">Kode Satuan Barang</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <select class="form-select kode_jenis_pungutan" id="kode_jenis_pungutan" name="kode_jenis_pungutan" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeJenisPungutan as $k) : ?>
                                    <option <?= $k['value'] == "PPN" ? "selected" : "" ?> value="<?= encrypt($k['value']) ?>">
                                        <?= $k['value']  ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Pungutan</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <select class="form-select kode_jenis_tarif" id="kode_jenis_tarif" name="kode_jenis_tarif" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeJenisTarif as $k) : ?>
                                    <option <?= $k['value'] == "1" ? "selected" : "" ?> value="<?= encrypt($k['value']) ?>">
                                        <?= $k['value'] . " - " . strtoupper($k['description']) . " " ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Jenis Tarif</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input value="11" id="nilai_tarif" name="nilai_tarif" type="number" min="0" max="100" class="form-control nilai_tarif" placeholder="" oninput="$(this).val(Math.max(0, Math.min(100, $(this).val())))">
                            <label>Nilai Tarif (%)</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kode_fasilitas_tarif" id="kode_fasilitas_tarif" name="kode_fasilitas_tarif" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeFasilitasTarif as $k) : ?>
                                    <option <?= $k['value'] == "3" ? 'selected' : '' ?> value="<?= encrypt($k['value']) ?>">
                                        (<?= $k['value'] ?>) <?= $k['description'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Fasilitas Tarif</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="tarif_fasilitas" max="100" value="100" name="tarif_fasilitas" type="number" class="form-control tarif_fasilitas" placeholder="" minlength="1" maxlength="100" oninput="$(this).val(Math.max(0, Math.min(100, $(this).val())))">
                            <label>Tarif Fasilitas (%)</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm" style="margin-right: -20px;">
                                    <button type="button" class="btn btn-add btn-block float-right" id="btn_submit_pungutan" style="float: right;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-pungutan" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center; width:10px;">No</th>
                                    <th style="text-align: center;">Jenis Pungutan</th>
                                    <th style="text-align: center;">Jenis Tarif</th>
                                    <th style="text-align: center;">Nilai Tarif (%)</th>
                                    <th style="text-align: center;">Fasilitas Tarif</th>
                                    <th style="text-align: center;">Tarif Fasilitas (%)</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var listData = [];
    var listDataSelected = [];

    getListPurchaseOrderNotUsed();

    <?php foreach ($daftarPoUsed as $d) : ?>
        listDataSelected.push({
            penerimaan_barang_id: "<?= $d['penerimaan_barang_id'] ?>",
            penerimaan_barang_detail_id: "<?= $d['penerimaan_barang_detail_id'] ?>",
            barang1_id: "<?= $d['barang1_id'] ?>",
            lpb_date: "<?= $d['lpb_date'] ?>",
            lpb_no: "<?= $d['lpb_no'] ?>",
            purchase_order_id: "<?= $d['purchase_order_id'] ?>",
            qty_lpb: "<?= floatval($d['qty_lpb']) ?>",
            qty_lpb_konversi: "<?= floatval($d['qty_lpb_konversi']) ?>",
            qty_po: "<?= floatval($d['qty_po']) ?>",
            barang_id: "<?= $d['barang_id'] ?>",
            po_no: "<?= $d['po_no'] ?>",
            spp_no: "<?= $d['spp_no'] ?>",
            po_date: "<?= $d['po_date'] ?>",
            barang_name: "<?= str_replace('"', '\"', $d['barang_name'])  ?>",
            kode_barang: "<?= $d['kode_barang'] ?>",
            harga_number: "<?= $d['harga'] ?>",
            kode_satuan_lpb: "<?= $d['kode_satuan_lpb'] ?>"
        })
        drawTablePurchaseOrderUsed(listDataSelected);
    <?php endforeach; ?>

    $('#po_type').select2({
        placeholder: "Pilih Tipe Purchase Order",
        theme: "bootstrap-5",
    }).change(function() {});

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
    }).change(function() {});

    $(".start_date,.end_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#start_date,#end_date').change(function() {
        getListPurchaseOrderNotUsed();
    });

    $('#kode_satuan_barang').select2({
        placeholder: "Pilih Kode Satuan Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#modalUpdatePungutan'),
        ajax: {
            url: '<?= base_url('bea-cukai-bc-40/satuan-barang') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                };
            },
            cache: true
        },
        minimumInputLength: 1,
    });

    $('#kode_jenis_pungutan').select2({
        placeholder: "Pilih Jenis Pungutan",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#modalUpdatePungutan'),
    });

    $('#kode_jenis_tarif').select2({
        placeholder: "Pilih Jenis Tarif",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#modalUpdatePungutan'),
    });

    $('#kode_fasilitas_tarif').select2({
        placeholder: "Pilih Fasilitas Tarif",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#modalUpdatePungutan'),
    });

    var dataTable1 = $('#dataTable1').DataTable({

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

    var tablePungutanPerBarang = $('#pungutanPerBarangTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        info: false,
        ajax: {
            url: "<?= base_url('bea-cukai-bc-40/pungutan-per-barang-all') ?>",
            type: 'GET',
            dataSrc: "data",
            data: function(data) {
                data.bc_purchase_order_id = "<?= encrypt($bcPo['id']) ?>";
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        order: [
            [1, 'desc']
        ],
        display: "stripe",
        searching: false,
        columns: [{
                data: 'no',
                width: "5%"
            },
            {
                data: 'kode_barang'
            },
            {
                data: 'barang_name'
            },
            {
                data: 'qty_po'
            },
            {
                data: 'qty_lpb'
            },
            {
                data: 'harga'
            },
            {
                data: "status",
                className: "text-left",
                width: "5%",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status == 1) {
                        htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
                            </div>`
                    }

                    return htmlRes;
                }
            },
            {
                data: "id",
                className: "text-center actions",
                width: "5%",
                render: function(data, type, row) {
                    let penerimaanBarangId = row.penerimaan_barang_id;
                    let barang1Id = row.barang1_id;
                    let kodeSatuanBarangId = row.kode_satuan_barang_id;
                    let kodeSatuanBarang = row.kode_satuan_barang;
                    let kodeBarang = row.kode_barang;
                    let barangName = row.barang_name;
                    let seriBarang = row.seri_barang;
                    let harga = row.harga;

                    let htmlRes = `
                        <a href="#" onclick="updatePungutanBtn(
                            '${penerimaanBarangId}', 
                            '${barang1Id}', 
                            '${kodeBarang}', 
                            '${barangName}', 
                            '${seriBarang}',
                            '${kodeSatuanBarangId}',
                            '${kodeSatuanBarang}',
                            '${harga}'
                            )" 
                            data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    `;

                    return htmlRes;
                }
            },
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada penerimaan outstanding BC 4.0",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var tableHasilPungutan = $('#hasilPungutanTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        info: false,
        ajax: {
            url: "<?= base_url('bea-cukai-bc-40/pungutan-hasil-all') ?>",
            type: 'GET',
            dataSrc: "data",
            data: function(data) {
                data.bc_purchase_order_id = "<?= encrypt($bcPo['id']) ?>";
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        order: [
            [1, 'desc']
        ],
        display: "stripe",
        searching: false,
        columns: [{
                data: 'no',
                width: "5%"
            },
            {
                data: 'pungutan'
            },
            {
                data: 'tidak_dipungut',
                render: function(data) {
                    return greatFormatRupiah(parseFloat(data).toFixed(2));
                }
            },
            {
                data: 'dibebaskan',
                render: function(data) {
                    return greatFormatRupiah(parseFloat(data).toFixed(2));
                }
            },
            {
                data: 'ditangguhkan',
                render: function(data) {
                    return greatFormatRupiah(parseFloat(data).toFixed(2));
                }
            },
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada penerimaan outstanding BC 4.0",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var tableListInformasiPungutan = $('.table-list-informasi-pungutan').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
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
            url: "<?= base_url("bea-cukai-bc-40/id/barang-pungutan-all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_purchase_order_id = "<?= encrypt($bcPo['id']) ?>";
                data.penerimaan_barang_id = $('#penerimaan_barang_id').val();
                data.barang1_id = $('#barang1_id').val();
                data.sort = "bc_barang_tarif.createdAt";
                data.sortType = "DESC";
            }
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
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "kode_jenis_pungutan",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "kode_jenis_tarif",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "tarif_bea_masuk",
                searchable: false,
                sortable: false,
                className: "text-center"
            },
            {
                data: "kode_fasilitas_tarif",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "tarif_fasilitas",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    return `<button type="button" class="btn btn-danger" onclick="removePungutan('${row.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>`;
                }
            }

        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada pungutan",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });


    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#select-item-btn').click(function() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("penerimaan_barang_id");
        }).get();

        var id_selected = getIDListDataSelected();

        $.each(listData, function(i, v) {
            var currentID = Number(v.penerimaan_barang_id);

            if ($.inArray(currentID, dataIds) !== -1) {
                var isIDSelected = $.grep(listDataSelected, function(item) {
                    return item.penerimaan_barang_id == Number(currentID);
                }).length > 0;

                if (!isIDSelected) {
                    listDataSelected.push(listData[i]);
                }
            }
        });
        drawTablePurchaseOrderUsed(listDataSelected);
    });

    var validator = $(".create-form").validate({
        rules: {
            // no_daftar: {
            //     required: true
            // },
            tanggal: {
                required: true
            },
        },
        messages: {
            // no_daftar: {
            //     required: "Nomor Daftar Wajib Diisi"
            // },
            tanggal: {
                required: "Tanggal Wajib Diisi"
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

    var validatorPungutan = $(".form-update-pungutan").validate({
        rules: {
            kode_satuan_barang: {
                required: true
            },
            kode_jenis_pungutan: {
                required: true
            },
            kode_jenis_tarif: {
                required: true
            },
            nilai_tarif: {
                required: true
            },
            kode_fasilitas_tarif: {
                required: true
            },
            tarif_fasilitas: {
                required: true
            },
        },
        messages: {
            kode_satuan_barang: {
                required: "Pilih kode satuan barang"
            },
            kode_jenis_pungutan: {
                required: "Pilih jenis pungutan"
            },
            kode_jenis_tarif: {
                required: "Pilih jenis tarif"
            },
            nilai_tarif: {
                required: "Nilai tarif wajib diisi"
            },
            kode_fasilitas_tarif: {
                required: "Pilih kode fasilitas tarif"
            },
            tarif_fasilitas: {
                required: "Tarif fasilitas wajib diisi"
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

    $('#btn_submit_pungutan').click(function(e) {
        e.preventDefault();
        if ($('.form-update-pungutan').valid()) {
            var bc_purchase_order_id = "<?= encrypt($bcPo['id']) ?>";
            var penerimaan_barang_id = $('#penerimaan_barang_id').val();
            var barang1_id = $('#barang1_id').val();
            var kode_satuan_barang = $('#kode_satuan_barang option:selected').val();
            var seri_barang = $('#seri_barang').val();
            var harga = destroyFormatRupiah($('#harga').val());
            //--------------------------------------------------------------------------
            var kode_jenis_pungutan = $('#kode_jenis_pungutan option:selected').val();
            var kode_jenis_tarif = $('#kode_jenis_tarif option:selected').val();
            var nilai_tarif = $('#nilai_tarif').val();
            var kode_fasilitas_tarif = $('#kode_fasilitas_tarif option:selected').val();
            var tarif_fasilitas = $('#tarif_fasilitas').val();

            var formData = new FormData();
            formData.append("bc_purchase_order_id", bc_purchase_order_id);
            formData.append("penerimaan_barang_id", penerimaan_barang_id);
            formData.append("barang1_id", barang1_id);
            formData.append("seri_barang", seri_barang);
            formData.append("harga", harga);
            //-------------------------------------------
            formData.append("kode_satuan_barang", kode_satuan_barang);
            formData.append("barang_detail_kode_jenis_pungutan", kode_jenis_pungutan);
            formData.append("barang_detail_kode_jenis_tarif", kode_jenis_tarif);
            formData.append("barang_detail_nilai_tarif", nilai_tarif);
            formData.append("barang_detail_kode_fasilitas_tarif", kode_fasilitas_tarif);
            formData.append("barang_detail_tarif_fasilitas", tarif_fasilitas);

            $.ajax({
                url: "<?= base_url("bea-cukai-bc-40/id/barang-pungutan-create"); ?>",
                data: formData,
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
                    if (response.status) {
                        tablePungutanPerBarang.ajax.reload();
                        tableHasilPungutan.ajax.reload();
                        $('#modalUpdatePungutan').modal('hide');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                    }
                },
            });
        }
    });

    $('.btn-submit-parent').click(function() {
        if (listDataSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Data Purhase Order Masih Kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                let data = new FormData(document.querySelector(".create-form"));
                data.append('listBarang', JSON.stringify(listDataSelected));

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
                        let data = new FormData(document.querySelector(".create-form"));
                        data.append('listData', JSON.stringify(listDataSelected));

                        $.ajax({
                            url: "<?= base_url("bea-cukai-bc-40/po/update"); ?>",
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
                    }
                })
            }
        }
    })

    // NO AJU ACTION
    $('#no_urut_dokumen').keyup(function() {
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");
        splitValues[3] = $(this).val();
        $('#no_pengajuan').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    });
    $('#kode_kantor').keyup(function() {
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");
        splitValues[1] = $(this).val();
        $('#no_pengajuan').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    });

    $("#tanggal_pengajuan").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    }).change(function() {
        var tanggalPengajuan = $(this).val();
        var noAju = $('#no_pengajuan').val();
        var tanggalPengajuanSplit = tanggalPengajuan.split("/");
        var noPengajuanSplit = noAju.split("-");
        $('#no_pengajuan').val(noPengajuanSplit[0] + '-' + noPengajuanSplit[1] + '-' + tanggalPengajuanSplit[2] + '' + tanggalPengajuanSplit[1] + '' + tanggalPengajuanSplit[0] + '-' + noPengajuanSplit[3]);
    });

    $('#tanggal').datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#btn_update_no_aju_no_daftar').click(function(e) {
        e.preventDefault();
        var noDaftar = $('#no_daftar').val();
        var tanggalDokumen = $('#tanggal').val();
        var noAju = $('#no_pengajuan_preview').val();
        var bcPurchaseOrderId = $('#bc_purchase_order_id').val();

        if (noDaftar == '') {
            Swal.fire({
                icon: 'error',
                title: 'No Daftar Wajib Diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            })
        } else if (tanggalDokumen == '') {
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Dokumen Wajib Diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            })
        } else if (noAju == '') {
            Swal.fire({
                icon: 'error',
                title: 'No Aju Wajib Diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            })
        } else {
            Swal.fire({
                icon: 'question',
                title: 'Ubah Nomor Aju & No Daftar ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append("bc_purchase_order_id", bcPurchaseOrderId);
                    formData.append('no_pengajuan', noAju);
                    formData.append('tanggal_dokumen', tanggalDokumen);
                    formData.append('no_daftar', noDaftar);

                    $.ajax({
                        url: `<?= base_url("bea-cukai-bc-40/id/update-no-aju-bulk"); ?>`,
                        method: "POST",
                        data: formData,
                        beforeSend: function(xhr) {
                            setLoading();
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        complete: function() {
                            stopLoading();
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: res.message,
                                    confirmButtonColor: '#4e73df',
                                    confirmButtonText: 'Ok'
                                }).then((result) => {
                                    location.reload();
                                });

                            }
                        }
                    })
                }
            })
        }
    })

    $('#ubahNoAjuButton').click(function(e) {
        e.preventDefault();
        if ($('#form-update').valid()) {
            var statusPosting = "<?= $bcPo['status_posting'] ?>";
            if (statusPosting == 1) {
                var noAju = $('#no_pengajuan').val();
                $('#no_pengajuan_preview').val(noAju);

                $('#modalUpdateNoAju').modal('hide');
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Ubah Nomor Aju ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = new FormData(document.querySelector("#form-update"));
                        $.ajax({
                            url: `<?= base_url("bea-cukai-bc-40/id/update-no-aju"); ?>`,
                            method: "POST",
                            data: formData,
                            beforeSend: function(xhr) {
                                setLoading();
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            },
                            complete: function() {
                                stopLoading();
                            },
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: res.message,
                                        confirmButtonColor: '#4e73df',
                                        confirmButtonText: 'Ok'
                                    }).then((result) => {
                                        location.reload();
                                    });

                                }
                            }
                        })
                    }
                })
            }
        }
    });

    // FUNCTION HELPER
    function deleteDetail(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listDataSelected.length; i++) {
            if (listDataSelected[i].penerimaan_barang_id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listDataSelected.splice(indexToRemove, 1);
            drawTablePurchaseOrderUsed(listDataSelected);
        }
    }


    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listDataSelected, function(i, v) {
            id_selected.push(v.penerimaan_barang_id);
        })
        return id_selected;
    }

    function getListPurchaseOrderNotUsed() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-40/list-po'); ?>`,
            method: "GET",
            data: {
                po_type: "<?= $bcPo['po_type'] ?>",
                supplier_id: "<?= $bcPo['supplier_id'] ?>",
                start_date: $(".start_date").val(),
                end_date: $('#end_date').val()
            },
            dataType: "json",
            success: function(res) {
                // DRAW LIST PO TABLE
                var data = res.data;
                listData = [];
                listData = data;

                if ($.fn.DataTable.isDataTable('#dataTable1')) {
                    $('#dataTable1').DataTable().clear().draw();
                    dataTable1.destroy();
                }

                const table = $('#dataTable1');
                table.find('tbody').empty();
                table.find('tfoot').empty();

                var no = 1;
                $.each(data, function(i, v) {
                    var newRow = $('<tr>');
                    newRow.append($('<td style="text-align:center;">').text(no++));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                                <div class="form-check">
                                    <input data-penerimaan_barang_id="${v.penerimaan_barang_id}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                                </div>
                            `
                    ));
                    newRow.append($('<td style="text-align:center;">').text(v.po_date));
                    newRow.append($('<td style="text-align:center;">').text(v.lpb_date));
                    newRow.append($('<td style="text-align:center;">').text(v.lpb_no));
                    newRow.append($('<td style="text-align:center;">').text(v.po_no));
                    newRow.append($('<td style="text-align:center;">').text(v.spp_no));
                    newRow.append($('<td style="text-align:center;">').text(v.kode_barang));
                    newRow.append($('<td style="text-align:center;">').text(v.barang_name));
                    newRow.append($('<td style="text-align:center;">').text(parseFloat(v.qty_po) + " " + v.kode_satuan_po));
                    newRow.append($('<td style="text-align:center;">').text(parseFloat(v.qty_lpb) + " " + v.kode_satuan_lpb));
                    newRow.append($('<td style="text-align:center;">').text(greatFormatRupiah(v.harga_number)));
                    table.find('tbody').append(newRow);
                });

                dataTable1 = $('#dataTable1').DataTable({

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
                    lengthMenu: [
                        [100],
                        [100]
                    ],
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

                dataTable1.draw();
            }
        });
    }

    function drawTablePurchaseOrderUsed(listDataSelected) {
        const table = $('#dataTable2');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listDataSelected.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="11" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            var totalQtyPo = 0;
            var totalQtyDiterima = 0;
            var totalQtyDiterimaKonversi = 0;
            var totalHargaNumber = 0;

            $.each(listDataSelected, function(i, v) {
                totalQtyPo += parseFloat(v.qty_po);
                totalQtyDiterima += parseFloat(v.qty_lpb);
                totalQtyDiterimaKonversi += parseFloat(v.qty_lpb_konversi);
                totalHargaNumber += parseFloat(v.harga_number);

                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                   ${no++} 
                `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.po_date));
                newRow.append($('<td style="text-align: center;">').text(v.lpb_date));
                newRow.append($('<td style="text-align: center;">').text(v.lpb_no));
                newRow.append($('<td style="text-align: center;">').text(v.po_no));
                newRow.append($('<td style="text-align: center;">').text(v.spp_no));
                newRow.append($('<td style="text-align: center;">').text(v.kode_barang));
                newRow.append($('<td style="text-align: center;">').text(v.barang_name));
                newRow.append($('<td style="text-align: center;">').text(greatFormatRupiah(parseFloat(v.qty_po))));
                newRow.append($('<td style="text-align: center;">').text(greatFormatRupiah(parseFloat(v.qty_lpb))));
                newRow.append($('<td style="text-align: center;">').text(greatFormatRupiah(parseFloat(v.qty_lpb_konversi)) + " " + v.kode_satuan_lpb));
                newRow.append($('<td style="text-align: center;">').text(greatFormatRupiah(v.harga_number)));

                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button <?= !empty($bcPo) ? (($bcPo['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.penerimaan_barang_id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
                ));
                table.find('tbody').append(newRow);
            });

            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: right;" colspan="9">').html("<b>GRAND TOTAL</b>"));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiah(totalQtyDiterima.toFixed(2))));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiah(totalQtyDiterimaKonversi.toFixed(2))));
            newRow.append($('<td  style="text-align:center;">').text(greatFormatRupiah(totalHargaNumber.toFixed(2))));
            newRow.append($('<td>').text(''));

            table.find('tbody').append(newRow);

        }
    }

    function deleteAction() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 4.0 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-40/id/delete"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                location.replace("<?= base_url('bea-cukai-bc-40') ?>")
                            });
                        }
                    }
                })
            }
        })
    }

    function posting() {
        Swal.fire({
            icon: 'question',
            title: 'Posting Dokumen BC 4.0 Lokal ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-40/posting"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                location.replace("<?= base_url('bea-cukai-bc-40') ?>")
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            })
                        }
                    }
                })
            }
        })
    }

    function noAjuShowModal() {
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");

        var year = splitValues[2].substring(0, 4);
        var month = splitValues[2].substring(4, 6);
        var day = splitValues[2].substring(6, 8);

        var formattedDate = day + '/' + month + '/' + year;

        $('#tanggal_pengajuan').val(formattedDate);
        $('#no_pengajuan').val(noAju);
        $('#no_urut_dokumen').val(splitValues[3]);
        $('#kode_kantor').val(splitValues[1]);
        $('#modalUpdateNoAju').modal('show');
    }

    function ambilNoDaftar() {
        $.ajax({
            url: `<?= base_url("bea-cukai-bc-40/sync-no-daftar"); ?>`,
            method: "GET",
            data: {
                bc_purchase_order_id: "<?= $bcPo['id'] ?>"
            },
            beforeSend: function(xhr) {
                setLoading();
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    })
                }
            }
        })
    }

    function removePungutan(id) {
        const csrf = $(`[name="${csrfToken}"]`);
        $.ajax({
            url: "<?= base_url("bea-cukai-bc-40/id/barang-pungutan-delete"); ?>",
            data: {
                id: id
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "POST",
            dataType: "json",
            success: function(response) {
                csrf.val(response.token);
                if (response.status) {
                    tableListInformasiPungutan.ajax.reload();
                    tableHasilPungutan.ajax.reload();
                    tablePungutanPerBarang.ajax.reload();
                }
            },
        });

    }

    function updatePungutanBtn(
        penerimaanBarangId,
        barang1Id,
        kodeBarang,
        barangName,
        seriBarang,
        kodeSatuanBarangId,
        kodeSatuanBarang,
        harga
    ) {
        $('#penerimaan_barang_id').val(penerimaanBarangId);
        $('#barang1_id').val(barang1Id);
        $('#kode_barang').val(kodeBarang);
        $('#barang_name').val(barangName);
        $('#seri_barang').val(seriBarang);
        $('#harga').val(harga);
        if (kodeSatuanBarangId != "") {
            // append
            var kodeSatuanBarangSelect = $("#kode_satuan_barang");
            kodeSatuanBarangSelect.empty();
            var opt = $("<option></option>")
                .attr("value", kodeSatuanBarangId)
                .attr("selected", true)
                .text(kodeSatuanBarang);
            kodeSatuanBarangSelect.append(opt);

        }
        tableListInformasiPungutan.ajax.reload();
        $('#modalUpdatePungutan').modal('show');
    }
</script>
<?= $this->endSection(); ?>
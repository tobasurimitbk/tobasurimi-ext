<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <?php if (!isset($data)) : ?>
            <h1 class="title-name">Tambah Penerimaan Hasil Produksi</h1>
        <?php endif; ?>
        <?php if (isset($data)) : ?>
            <h1 class="title-name">Detail Penerimaan Hasil Produksi</h1>
        <?php endif; ?>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("production-result"); ?>">
                Batal
            </a>
            <?php if (isset($data) && $data->is_posted != 1) { ?>
                <button class="btn btn-success float-right" onclick="posting('<?= !empty($data) ? encrypt($data->id) : ''; ?>', 1)">
                    Posting
                </button>
                <button class="btn btn-show-form btn-save float-right btn-submit-form" type="button">
                    Simpan
                </button>
            <?php } else if (!isset($data)) { ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-form" type="button">
                    Simpan
                </button>
            <?php } else { ?>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp form-hp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" value="<?= $data->id ?? ""; ?>" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Bukti Penerimaan</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control res_no" id="res_no" name="res_no" placeholder="Kode Penerimaan" value="<?= isset($data) ? $data->pr_no : "AUTO GENERATE"; ?>" <?= isset($data) ? "readonly" : "readonly"; ?>>
                                    <label for="floatingInput">Kode Penerimaan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control date_picker" name="date_production" id="date_production" placeholder="Tanggal Produksi" value="<?= $data->receive_date ?? ""; ?>" <?= isset($data) ? "readonly" : ""; ?>>
                            <label for="floatingInput">Tanggal Penerimaan</label>
                        </div>
                    </div>
                </div>
                <div class="col-subtitle-modal">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Data Work Order</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <?php if (!isset($data)) : ?>
                                <select class="form-select kode_produksi" name="kode_produksi" id="kode_produksi" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php if (isset($dataWorkOrder)) : ?>
                                        <?php foreach ($dataWorkOrder ?? [] as $dataWO) : ?>
                                            <option value="<?= $dataWO->id ?>" data-nama-barang="<?= $dataWO->nama_barang ?>" data-standart-production="<?= $dataWO->standart_production ?>" data-warehouse="<?= $dataWO->warehouse_id ?>" data-divisi="<?= $dataWO->divisi_id ?>"><?= $dataWO->wo_no ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            <?php endif; ?>
                            <?php if (isset($data)) : ?>
                                <input autocomplete="one-time-code" type="text" class="form-control wo_no" name="wo_no" id="wo_no" placeholder="Kode Produksi" readonly>
                            <?php endif; ?>
                            <label for="floatingInput">Kode Produksi</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control barang_jadi" name="barang_jadi" id="barang_jadi" placeholder="Barang Jadi" readonly>
                            <label for="floatingInput">Barang Jadi</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select department_id_order" name="department_id_order" id="department_id_order" disabled>
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $divisi) : ?>
                                    <option value="<?= $divisi['id'] ?>"><?= $divisi['divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Department</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select warehouse_id_order" name="warehouse_id_order" id="warehouse_id_order" disabled>
                                <option value=""></option>
                                <?php foreach ($dataWarehouse ?? [] as $Warehouse) : ?>
                                    <option value="<?= $Warehouse['id'] ?>"><?= $Warehouse['warehouse_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Warehouse</label>
                        </div>
                    </div>
                </div>
                <div class="col-subtitle-modal">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Data Barang</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_asal_id" id="divisi_asal_id" name="divisi_asal_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option <?= !empty($mutasi) ? ($mutasi['divisi_asal_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen Asal</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_asal_id" id="warehouse_asal_id" name="warehouse_asal_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($warehouseAsal)) : ?>
                                    <?php foreach ($warehouseAsal as $w) : ?>
                                        <option <?= !empty($mutasi) ? ($mutasi['warehouse_asal_id'] == $w['id'] ? 'selected' : '') : '' ?> value="<?= $w['id'] ?>">
                                            <?= $w['warehouse_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse Asal</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select type_pengambilan_stock" id="type_pengambilan_stock" name="type_pengambilan_stock" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= !empty($mutasi) ? ($mutasi['tipe_pengambilan_stock'] == "PABEAN" ? 'selected' : '') : '' ?> value="PABEAN">PABEAN</option>
                                <option <?= !empty($mutasi) ? ($mutasi['tipe_pengambilan_stock'] == "FIFO" ? 'selected' : '') : '' ?> value="FIFO">FIFO</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Pengambilan Stok</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="Qty" readonly oninput="preventNegativeInput(this)" class="form-control qty_mutasi_fifo" id="qty_mutasi_fifo" name="qty_mutasi_fifo" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Qty Mutasi Keluar</label>
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
                    <div class="col-md-12 col-table-button-tts">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">Asal Barang</th>
                                        <th style="text-align: center;">No Dokumen</th>
                                        <th style="text-align: center;">Supplier</th>
                                        <th style="text-align: center;">Dokumen Pabean</th>
                                        <th style="text-align: center;">No Aju</th>
                                        <th style="text-align: center;">Tanggal Penerimaan</th>
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
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <button class="btn btn-show-detail btn-add btn-submit-barang" type="button" data-btn="detail-modal" id="select-item-btn">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah Barang
                        </button>
                    </div>
                </div>
                <!-- details -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-jadi" type="button" role="tab" aria-controls="nav-barang-jadi" aria-selected="true">Barang Jadi</button>
                                <!-- <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-setengah-jadi" type="button" role="tab" aria-controls="nav-barang-setengah-jadi" aria-selected="false">Barang Setengah Jadi</button> -->
                                <button class="nav-link" id="nav-scrap-tab" data-bs-toggle="tab" data-bs-target="#nav-scrap" type="button" role="tab" aria-controls="nav-scrap" aria-selected="false">Scrap</button>
                                <!-- <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-material-return" type="button" role="tab" aria-controls="nav-material-return" aria-selected="false">Material Return</button> -->
                            </div>
                        </nav>
                        <div class="tab-content mt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-barang-jadi" role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="col-subtitle-modal">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="form-label font-weight-bold modal-sub-title">Daftar Barang Jadi</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered nowrap table-hover-tobasurimi tableBarangJadi" id="tableBarangJadi" width="100%" cellspacing="0">
                                            <thead class="thead-dark text-center">
                                                <tr>
                                                    <th style="width: 10px;">No</th>
                                                    <th>Kode Barang</th>
                                                    <th>Jenis Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th>Satuan</th>
                                                    <!-- <th>Qty Target</th> -->
                                                    <th>Qty Hasil</th>
                                                </tr>
                                            </thead>
                                            <tbody class="body-table-barang-jadi" id="body-table-barang-jadi">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-subtitle-modal">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="form-label font-weight-bold modal-sub-title">Daftar Bahan Digunakan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="selectedItemTableBahan" width="100%" cellspacing="0">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="text-align: center;">No</th>
                                                        <th style="text-align: center;">Department</th>
                                                        <th style="text-align: center;">Warehouse</th>
                                                        <th style="text-align: center;">Tipe Barang</th>
                                                        <th style="text-align: center;">Dokumen Pabean</th>
                                                        <th style="text-align: center;">No Aju</th>
                                                        <th style="text-align: center;">Tanggal Penerimaan</th>
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
                            <div class="tab-pane fade" id="nav-scrap" role="tabpanel" aria-labelledby="nav-contact-tab">
                                <?php if (!isset($data)) : ?>
                                    <form class="formBarangScrap" id="formBarangScrap">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <input autocomplete="one-time-code" type="hidden" class="kode" name="kode" id="kode" />
                                                    <select class="form-select kode_barang_scrap" name="kode_barang_scrap" id="kode_barang_scrap" aria-label="Floating label select example">
                                                        <option data-barang_id="" data-nama="" data-satuan="" value=""></option>
                                                    </select>
                                                    <label for="floatingInput">Kode Barang</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <input autocomplete="one-time-code" type="number" class="form-control qty_scrap" name="qty_scrap" id="qty_scrap" placeholder="Qty">
                                                    <label for="floatingInput">Qty</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <select class="form-select department_id_scrap" name="department_id_scrap" id="department_id_scrap" aria-label="Floating label select example">
                                                        <option value=""></option>
                                                        <?php foreach ($dataDivisi ?? [] as $dataDivisi) : ?>
                                                            <option value="<?= $dataDivisi['id'] ?>" data-department-name="<?= $dataDivisi['divisi'] ?>"><?= $dataDivisi['divisi'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="floatingInput">Department</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <select class="form-select warehouse_id_scrap" name="warehouse_id_scrap" id="warehouse_id_scrap" disabled>
                                                        <option value=""></option>
                                                    </select>
                                                    <label for="floatingInput">Warehouse</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <button type="button" class="btn btn-primary button-add-scrap">Tambah Barang Scrap</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                                <div class="table-responsive">
                                    <table class="table nowrap table-hover-tobasurimi tableBarangScrap" id="tableBarangScrap" width="100%" cellspacing="0">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th>No.</th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Department</th>
                                                <th>Warehouse</th>
                                                <th>Jumlah</th>
                                                <?php if (!isset($data)) : ?>
                                                    <th>Action</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table-barang-scrap" id="body-table-barang-scrap" style="cursor: pointer;">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-material-return" role="tabpanel" aria-labelledby="nav-contact-tab">
                                <div class="col-md-12">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi tableBarangReturn" id="tableBarangReturn" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="width: 10px;">No</th>
                                                <th>Referensi</th>
                                                <th>Kode Barang</th>
                                                <th>Jenis Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Satuan</th>
                                                <?php if (!isset($data)) : ?>
                                                    <th>Jumlah Request</th>
                                                <?php endif; ?>
                                                <th>Jumlah Direturn</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table-barang-return" id="body-table-barang-digunakan">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- details -->

            </form>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    var listStockAsal = [];

    let list_items_barang_jadi = [];
    let list_items_barang_digunakan = [];
    let list_items_barang_return = [];
    let list_items_barang_scrap = [];

    $(document).ready(function() {
        <?php if (isset($data)) : ?>
            $(".kode_produksi").val('<?= $data->work_order_id ?>').change();
            $(".wo_no").val('<?= $dataWorkOrder[0]->wo_no ?>').change();
            $(".barang_jadi").val('<?= $dataWorkOrder[0]->nama_barang ?>');
            $(".standart_production").val('<?= $dataWorkOrder[0]->standart_production ?>');
            $(".department_id_order").val('<?= $dataWorkOrder[0]->divisi_id ?>');
            $(".warehouse_id_order").val('<?= $dataWorkOrder[0]->warehouse_id ?>');
            list_items_barang_jadi = [];
            list_items_barang_digunakan = [];
            list_items_barang_scrap = [];
            list_items_barang_return = [];
            <?php foreach ($dataResultBarangJadi as $key => $bj) : ?>
                list_items_barang_jadi.push({
                    'barang_detail_id': getID(),
                    'barang1_id': '<?= $bj->barang1_id; ?>',
                    'barang2_id': '<?= $bj->barang2_id; ?>',
                    'barang_name': '<?= $bj->nama_barang; ?>',
                    'kode_barang': '<?= $bj->kode_barang; ?>',
                    'kode_satuan': '<?= $bj->kode_satuan; ?>',
                    'nama_barang': '<?= $bj->barang_name; ?>',
                    'qty': '<?= $bj->qty; ?>',
                    'type_barang': '<?= $bj->barang_type; ?>',
                    'type_barang_text': '<?= $bj->type_barang_text; ?>',
                });
            <?php endforeach; ?>
            drawTableBarangJadi();
            <?php foreach ($dataResultBarangScrap as $key => $bs) : ?>
                list_items_barang_scrap.push({
                    'barang_detail_id': getID(),
                    'barang1_id': '<?= $bs->barang1_id; ?>',
                    'barang2_id': '<?= $bs->barang2_id; ?>',
                    'barang_name': '<?= $bs->barang_name; ?>',
                    'kode_barang': '<?= $bs->kode_barang; ?>',
                    'kode_satuan': '<?= $bs->kode_satuan; ?>',
                    'nama_barang': '<?= $bs->nama_barang; ?>',
                    'divisi_name': '<?= $bs->divisi; ?>',
                    'warehouse_name': '<?= $bs->warehouse; ?>',
                    'qty': '<?= $bs->qty; ?>',
                    'type_barang': '<?= $bs->barang_type; ?>',
                    'type_barang_text': '<?= $bs->type_barang_text; ?>',
                });
            <?php endforeach; ?>
            drawTableBarangScrap();
            <?php foreach ($dataResultBarangDigunakan as $key => $bd) : ?>
                list_items_barang_digunakan.push({
                    'barang_detail_id': getID(),
                    'barang1_id': '<?= $bd->barang1_id; ?>',
                    'barang2_id': '<?= $bd->barang2_id; ?>',
                    'barang_name': '<?= $bd->barang_name; ?>',
                    'kode_barang': '<?= $bd->kode_barang; ?>',
                    'satuan': '<?= $bd->kode_satuan; ?>',
                    'barang': '<?= $bd->nama_barang; ?>',
                    'stok_total': '<?= $bd->qty; ?>',
                    'qty2': '<?= $bd->qty; ?>',
                    'bc_type': '<?= $bd->no_ref; ?>',
                    'ref_no': '<?= $bd->no_ref; ?>',
                    'no_aju': '<?= $bd->no_aju; ?>',
                    'stock_date': '<?= $bd->stock_date; ?>',
                    'type_barang': '<?= $bd->barang_type; ?>',
                    'type_barang_text': '<?= $bd->type_barang_text; ?>',
                    'departmentID': '<?= $bd->divisi_id; ?> ',
                    'departmentText': '<?= $bd->divisi; ?> ',
                    'warehouseID': '<?= $bd->warehouse_id; ?> ',
                    'warehouseText': '<?= $bd->warehouse_name; ?> ',
                });
            <?php endforeach; ?>
            drawTableSelectedItemBahan(list_items_barang_digunakan);
            <?php foreach ($dataResultBarangReturn as $key => $br) : ?>
                list_items_barang_return.push({
                    'barang_detail_id': getID(),
                    'barang1_id': '<?= $br->barang1_id; ?>',
                    'barang2_id': '<?= $br->barang2_id; ?>',
                    'barang_name': '<?= $br->barang_name; ?>',
                    'kode_barang': '<?= $br->kode_barang; ?>',
                    'satuan': '<?= $br->kode_satuan; ?>',
                    'nama_barang': '<?= $br->nama_barang; ?>',
                    'qty': '<?= $br->qty; ?>',
                    'ref_no': '<?= $bd->no_ref; ?>',
                    'no_aju': '<?= $bd->no_aju; ?>',
                    'type_barang': '<?= $br->barang_type; ?>',
                    'type_barang_text': '<?= $br->type_barang_text; ?>',
                });
            <?php endforeach; ?>
            drawTableBarangReturn();
        <?php endif; ?>
        // Departemen
        $('.department_id_scrap').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        });

        //CSS SELECT2 FLOATING LABEL
        $('.department_id_scrap')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.department_id_scrap')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.department_id_scrap')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PILIH TIPE Warehouse
        $('.warehouse_id_scrap').select2({
            placeholder: "Pilih Warehouse",
            theme: "bootstrap-5"
        });

        $('.warehouse_id_scrap')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.warehouse_id_scrap')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.warehouse_id_scrap')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('#work_order, #warehouse, #barang_setengah_jadi, #scrap').select2({
            placeholder: "",
            theme: "bootstrap-5",
        });

        $("#receive_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        // Mengaktifkan datepicker
        $('.date_picker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            enableOnReadonly: false
        });

        // KODE BARANG
        $('.kode_barang').select2({
            placeholder: "Pilih Kode Barang",
            theme: "bootstrap-5",
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

        $('.satuan_id, #kode_produksi, #kode_request').select2({
            placeholder: "",
            theme: "bootstrap-5",
        })

        //CSS SELECT2 FLOATING LABEL
        $('.satuan_id, #kode_produksi, #kode_request')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.satuan_id, #kode_produksi, #kode_request')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan_id, #kode_produksi, #kode_request')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('#type_pengambilan_stock').select2({
            placeholder: "Pilih Tipe Ambil Stok",
            theme: "bootstrap-5",
        }).change(function() {
            // FIFO
            if ($(this).val() == "FIFO") {
                $('.qty_mutasi_fifo').removeAttr('readonly');
            } else {
                $('.qty_mutasi_fifo').attr('readonly', 'readonly');
            }
            $('#select_tipe_bahan').val(null).change();
            $("#select_tipe_bahan").prop('disabled', false);
            $('#select_nama_barang').val(null).change();
            $("#select_nama_barang").prop('disabled', false);
            listStockAsal = [];
            drawTableAsalBarang();
        });

        $('#warehouse_asal_id').select2({
            placeholder: "Pilih Warehouse Asal",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            // RESET TYPE BARANG
            $('#select_tipe_bahan').val(null).change();
            $("#select_tipe_bahan").prop('disabled', false);
            // RESET SEMUA LIST
            listStockAsal = [];
            drawTableAsalBarang(listStockAsal);
        });

        $('#divisi_asal_id').select2({
            placeholder: "Pilih Departemen Asal",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            // CARI WAREHOUSE ASAL
            getListWarehouseAsal()
            // RESET TYPE BARANG
            $('#select_tipe_bahan').val(null).change();
            // RESET SEMUA LIST
            listStockAsal = [];
            drawTableAsalBarang(listStockAsal);
        });

        $("#divisi_asal_id,#warehouse_asal_id,#operasi,#type_pengambilan_stock")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        // select tipe bahan
        $('.select_tipe_bahan').select2({
            placeholder: "Pilih Tipe Bahan",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            $('#select_nama_barang').val(null).change();
            $("#select_nama_barang").prop('disabled', false);
            listStockAsal = [];
            drawTableAsalBarang();
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
            listStockAsal = [];
            drawTableAsalBarang();
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

        var validator = $(".create-form").validate({
            rules: {
                res_no: {
                    required: true
                },
                date_production: {
                    required: true
                },
                kode_produksi: {
                    required: true
                },
                kode_request: {
                    required: true
                }
            },
            messages: {
                res_no: {
                    required: "No. Penerimaan wajib diisi"
                },
                date_production: {
                    required: "Tanggal Penerimaan wajib diisi"
                },
                kode_produksi: {
                    required: "Work Order wajib diisi"
                },
                kode_request: {
                    required: "Material Request wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
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

        $("#nav-scrap-tab").click(function() {
            var type = "bahan_scrap";
            setLoading();
            $.ajax({
                url: `<?= base_url("barang/dropdown/type"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    type: type
                },
                success: function(res) {
                    $(".kode_barang_scrap").empty();
                    $(".kode_barang_scrap").append(`<option data-barang_name_master="" data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
                    res.data.forEach(function(item) {
                        $(".kode_barang_scrap").append(`<option data-barang_name_master="${item.barang_name_master}" data-barang_spesifikasi_id="${item.barang_master_spesifikasi_id}" data-barang_id="${item.id}" data-nama="${item.barang_name}" data-satuan_id="${item.satuan_1}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
                    })
                    $(".kode_barang_scrap").val("").change();
                    stopLoading()
                }
            })
        });

        $(".btn-submit-form").click(function() {
            if ($(".create-form").valid()) {
                var isValid = true;
                var dataError = null;
                $.each(list_items_barang_digunakan, function(i, v) {
                    var element = $('input[data-id="' + v.id + '"].qty-bahan-request');
                    var input_user = parseFloat(element.val());
                    var stok_max = parseFloat(element.data('stok_total'));

                    if (input_user > stok_max || isNaN(input_user) || input_user == undefined || input_user == 0) {
                        dataError = list_items_barang_digunakan[i];
                        isValid = false;
                    } else {
                        list_items_barang_digunakan[i].qty = stok_max;
                        list_items_barang_digunakan[i].qty2 = input_user;
                    }
                });
                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cek kembali input anda!',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
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
                            $("#department_id_order").prop('disabled', false);
                            $("#department_id_request").prop('disabled', false);
                            $("#warehouse_id_order").prop('disabled', false);
                            $("#warehouse_id_request").prop('disabled', false);
                            const data = new FormData(document.querySelector(".create-form"));
                            const id = $(".id").val();
                            data.append("jadi", JSON.stringify(list_items_barang_jadi));
                            data.append("digunakan", JSON.stringify(list_items_barang_digunakan));
                            data.append("scrap", JSON.stringify(list_items_barang_scrap));
                            data.append("return", JSON.stringify(list_items_barang_return));

                            // UPDATE
                            if (id) {
                                $.ajax({
                                    url: "<?= base_url("production-result/update"); ?>",
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
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("production-result/details/"); ?>" + response.id;
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
                            } else {
                                $.ajax({
                                    url: "<?= base_url("production-result/create"); ?>",
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
                                                    window.location.href = "<?= base_url("production-result/details/"); ?>" + response.id;
                                                })
                                        } else {
                                            stopLoading()
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
                                        stopLoading()
                                    }
                                });
                            }
                        }
                    })
                }
            }
        });

        $(".kode_produksi").on('change', function() {
            if ($(this).val()) {
                let nama_barang = $(".kode_produksi option:selected").data("nama-barang") ? $(".kode_produksi option:selected").data("nama-barang") : "";
                let standart_production = $(".kode_produksi option:selected").data("standart-production") ? $(".kode_produksi option:selected").data("standart-production") : "";
                let warehouse_id = $(".kode_produksi option:selected").data("warehouse") ? $(".kode_produksi option:selected").data("warehouse") : "";
                let divisi_id = $(".kode_produksi option:selected").data("divisi") ? $(".kode_produksi option:selected").data("divisi") : "";

                $(".barang_jadi").val(nama_barang);
                $(".standart_production").val(standart_production);
                $(".warehouse_id_order").val(warehouse_id).change();
                $(".department_id_order").val(divisi_id).change();
                setLoading();
                $.ajax({
                    url: `<?= base_url('production-result/material-request'); ?>`,
                    method: "GET",
                    data: {
                        kode_produksi: $(this).val(),
                    },
                    dataType: "json",
                    success: function(res) {
                        stopLoading()
                        if (res.status) {
                            // Clear existing options
                            $('#kode_request').empty();
                            // Append a default option
                            $('#kode_request').append($('<option>', {
                                value: '',
                                text: ''
                            }));
                            // Iterate over each item in the response data
                            res.data.forEach(function(item) {
                                // Append an option for each item
                                $('#kode_request').append($('<option>', {
                                    value: item.id,
                                    text: item.req_no,
                                    'data-tanggal-request': item.request_date,
                                    'data-user-request': item.user_name,
                                    'data-warehouse-request': item.warehouse_id,
                                    'data-divisi-request': item.divisi_id
                                }));
                            });
                        } else {
                            stopLoading()
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Material Request Belum Di Setujui Warehouse',
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    },
                });
                $.ajax({
                    url: `<?= base_url('production-result/list-work-order'); ?>`,
                    method: "GET",
                    data: {
                        kode_produksi: $(this).val(),
                    },
                    dataType: "json",
                    success: function(res) {
                        list_items_barang_jadi = [];
                        list_items_barang_scrap = [];
                        list_items_barang_digunakan = [];
                        list_items_barang_return = [];
                        res.data.forEach(function(item) {
                            console.log(item);
                            // Push each item into the list_items_barang_jadi array
                            list_items_barang_jadi.push({
                                'barang_detail_id': getID(),
                                'detail_work_order': item.id,
                                'barang1_id': item.barang1_id,
                                'barang2_id': item.barang2_id,
                                'warehouse_id': item.warehouse_id,
                                'divisi_id': item.divisi_id,
                                'barang_name': item.barang_name + " - " + item.spesifikasi,
                                'kode_barang': item.kode_barang,
                                'kode_satuan': item.kode_satuan,
                                'nama_barang': item.nama_barang,
                                'note': item.note,
                                'qty': 0,
                                'type_barang': item.type_barang,
                                'type_barang_text': item.type_barang_text,
                                'unit': item.unit,
                            });
                        });
                        drawTableBarangJadi();
                        stopLoading();
                    }
                });
            } else {
                $(".barang_jadi").val("");
                $(".standart_production").val("");
                $(".warehouse_id_produksi").val("").change();
                $(".divisi_id_produksi").val("").change();
            }
        })

        $(".kode_request").change(function() {
            if ($(".kode_request option:selected").val()) {
                let date_request = $(".kode_request option:selected").data("tanggal-request") ? $(".kode_request option:selected").data("tanggal-request") : "";
                let user_request = $(".kode_request option:selected").data("user-request") ? $(".kode_request option:selected").data("user-request") : "";
                let warehouse_id = $(".kode_request option:selected").data("warehouse-request") ? $(".kode_request option:selected").data("warehouse-request") : "";
                let divisi_id = $(".kode_request option:selected").data("divisi-request") ? $(".kode_request option:selected").data("divisi-request") : "";

                if (date_request) {
                    let parts = date_request.split('-');
                    date_request = parts[2] + '/' + parts[1] + '/' + parts[0];
                }

                $("#date_request").val(date_request);
                $(".user_request").val(user_request);
                $(".warehouse_id_request").val(warehouse_id).change();
                $(".department_id_request").val(divisi_id).change();
                setLoading();
                $.ajax({
                    url: `<?= base_url('production-result/list-material-request'); ?>`,
                    method: "GET",
                    data: {
                        kode_request: $(this).val(),
                    },
                    dataType: "json",
                    success: function(res) {
                        list_items_barang_digunakan = [];
                        list_items_barang_return = [];
                        res.data.forEach(function(item) {
                            if (item.ref_no == "NON PABEAN") {
                                var new_ref_no = item.ref_no;
                            } else {
                                var ref_no = item.ref_no + "/" + item.no_aju + "/" + item.stock_date;

                                // Pisahkan string berdasarkan tanda slash '/'
                                var parts = ref_no.split('/');
                                var partsAju = parts[1].split('-');
                                var partsDate = parts[2].replace(/-/g, '');

                                // // Dapatkan bagian yang Anda inginkan (bagian ke-1 dan ke-4)
                                var new_ref_no = parts[0] + '/' + partsAju[3] + '/' + partsDate;
                            }
                            list_items_barang_digunakan.push({
                                'barang_detail_id': getID(),
                                'bc_id': item.bc_id,
                                'stock_id': item.stock_id,
                                'barang1_id': item.barang1_id,
                                'barang2_id': item.barang2_id,
                                'kode_barang': item.kode_barang,
                                'satuan': item.satuan,
                                'nama_barang': item.nama_barang,
                                'note': item.note,
                                'qty': item.qty_isi,
                                'ref_no': new_ref_no,
                                'no_aju': item.no_aju,
                                'type_barang': item.type_barang,
                                'type_barang_text': item.type_barang_text,
                                'unit': item.unit,
                            });
                            list_items_barang_return.push({
                                'barang_detail_id': getID(),
                                'bc_id': item.bc_id,
                                'stock_id': item.stock_id,
                                'barang1_id': item.barang1_id,
                                'barang2_id': item.barang2_id,
                                'kode_barang': item.kode_barang,
                                'satuan': item.satuan,
                                'nama_barang': item.nama_barang,
                                'note': item.note,
                                'qty': item.qty,
                                'ref_no': new_ref_no,
                                'no_aju': item.no_aju,
                                'type_barang': item.type_barang,
                                'type_barang_text': item.type_barang_text,
                                'unit': item.unit,
                            });
                        });
                        drawTableSelectedItemBahan();
                        drawTableBarangReturn();
                        stopLoading()
                    }
                });
            } else {
                $("#date_request").val("");
                $(".user_request").val("");
                $(".warehouse_id_request").val("").change();
                $(".divisi_id_request").val("").change();
            }
        })

        $('#department_id_scrap').on('change', function() {
            var departmentId = $(this).val();
            $.ajax({
                url: `<?= base_url("warehouse/dropdown/divisi/"); ?>/${departmentId}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $("#warehouse_id_scrap").empty();
                    $("#warehouse_id_scrap").append(`<option value=""></option>`);
                    res.data.forEach(function(item) {
                        $("#warehouse_id_scrap").append(`<option value="${item.id}" data-warehouse-name="${item.warehouse_name}">${item.warehouse_name}</option>`);
                    })
                    $("#warehouse_id_scrap").prop('disabled', false);
                    // $("#warehouse_id").val().change();
                }
            })
        })

        // Event click untuk tombol "Tambah Barang Scrap"
        $(".button-add-scrap").click(function() {
            // Menghapus pesan kesalahan sebelumnya
            $('.error-message').remove();

            // Mengambil nilai dari setiap input
            let kodeBarang = $(".kode_barang_scrap option:selected").val();
            let qtyBarang = $(".qty_scrap").val();
            let department = $(".department_id_scrap option:selected").val();
            let warehouse = $(".warehouse_id_scrap option:selected").val();

            // Memeriksa apakah semua input terisi
            if (kodeBarang && qtyBarang && department && warehouse) {
                // Jika semua input terisi, tambahkan barang scrap
                let barang = $(".kode_barang_scrap option:selected").val();
                let departmentName = $(".department_id_scrap option:selected").data("department-name");
                let warehouseName = $(".warehouse_id_scrap option:selected").data("warehouse-name");

                let nama = $(".kode_barang_scrap option:selected").data("nama");
                let satuan = $(".kode_barang_scrap option:selected").data("satuan");
                let satuan_id = $(".kode_barang_scrap option:selected").data("satuan_id");
                let barang_id = $(".kode_barang_scrap option:selected").data("barang_id");
                let barang_spesifikasi_id = $(".kode_barang_scrap option:selected").data("barang_spesifikasi_id");
                let barang_name_master = $(".kode_barang_scrap option:selected").data("barang_name_master");

                list_items_barang_scrap.push({
                    'barang_detail_id': getID(),
                    'work_order_detail_id': "",
                    'barang_id': barang_id,
                    'barang_spesifikasi_id': barang_spesifikasi_id,
                    'kode_barang': barang,
                    'nama_barang': nama,
                    'nama_satuan': satuan,
                    'satuan_id': satuan_id,
                    'divisi_name': departmentName,
                    'divisi_id': department,
                    'warehouse_id': warehouse,
                    'warehouse_name': warehouseName,
                    'qty': qtyBarang
                });

                drawTableBarangScrap(); // Menggambar tabel
                resetFormDetailScrap(); // Mengatur ulang form
            } else {
                // Menampilkan pesan kesalahan di bawah input yang kosong
                if (!kodeBarang) $(".kode_barang_scrap").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih kode barang.</span>');
                if (!qtyBarang) $(".qty_scrap").closest('.form-floating').append('<span class="error-message text-danger">Harap masukkan jumlah barang.</span>');
                if (!department) $(".department_id_scrap").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih departemen.</span>');
                if (!warehouse) $(".warehouse_id_scrap").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih gudang.</span>');
            }
        });
    });

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".res_no").attr("readonly", true);
            $(".res_no").val("AUTO GENERATE");
        } else {
            $(".res_no").attr("readonly", false);
            $(".res_no").val("");
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

    const drawTableBarangJadi = function() {
        $('.body-table-barang-jadi').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items_barang_jadi.length === 0) {
            row += `
                <tr>
                    <td colspan="6" class="text-center">Data Barang Tidak Ada</td>
                </tr>
            `;
            $('.tfoot').append(row);
        } else {
            list_items_barang_jadi.map((item, index) => { // Tambahkan parameter index untuk mengetahui posisi item
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.type_barang_text + '</td>';
                row += '<td>' + item.barang_name + '</td>';
                row += '<td>' + item.kode_satuan + '</td>';
                // row += '<td>' + item.qty + '</td>';
                row += '<td>' + `
        <input class="form-control qty-barang-jadi" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" value="${item.qty}" <?= isset($data) ? "readonly" : ""; ?>>` +
                    '</td>';

                no++;
            });
            $('.body-table-barang-jadi').append(row);
        }

        // Tambahkan event listener untuk mengikuti perubahan nilai qty-barang-jadi
        $('.qty-barang-jadi').on('input change', function() {
            var index = $(this).data('index'); // Dapatkan indeks item dari atribut data-index
            var newValue = $(this).val(); // Dapatkan nilai yang dimasukkan pengguna
            list_items_barang_jadi[index].qty2 = newValue; // Simpan nilai ke dalam list_items_barang_jadi
        });
    }

    function drawTableSelectedItemBahan(data) {
        if ($.fn.DataTable.isDataTable('#selectedItemTableBahan')) {
            $('#selectedItemTableBahan').DataTable().clear().draw();
            selectedItemTableBahan.destroy();
        }
        const table = $('#selectedItemTableBahan');
        var no = 1;
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
               ${no++} 
            `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.departmentText));
            newRow.append($('<td style="text-align: center;">').text(v.warehouseText));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(v.no_aju));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <input <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "readonly" : ""; ?> class="form-control qty-bahan-request" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" data-stok_total="${v.stok_total}" class="form-control" type="text" value="${v.qty2}">
            `
            ));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <button <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "disabled" : ""; ?> type="button" class="btn btn-discard delete-btn btn-trash" onclick="deleteDetailBahan(${v.id}, ${v.id_material_request_detail})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
            `
            ));
            table.find('tbody').append(newRow);
        });

        selectedItemTableBahan = $('#selectedItemTableBahan').DataTable({
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
            searching: false,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        selectedItemTableBahan.draw();
    }

    const drawTableBarangScrap = function() {
        $('.body-table-barang-scrap').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items_barang_scrap.length === 0) {
            row += `
                <tr>
                    <td colspan="5" class="text-center">Data Barang Tidak Ada</td>
                </tr>
            `;
            http: //localhost:8080/dashboard
                $('.tfoot').append(row);
        } else {
            list_items_barang_scrap.map(item => {
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + item.divisi_name + '</td>';
                row += '<td>' + item.warehouse_name + '</td>';
                row += '<td>' + item.qty + '</td>';
                <?php if (!isset($data)) : ?>
                    row += '<td>' + `
            <button type="button" class="btn btn-danger" onclick="deleteRowDetailScrap('${item.barang_detail_id}')">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>` +
                        '</td>';
                <?php endif; ?>

                no++;
            });
            $('.body-table-barang-scrap').append(row);
        }
    }

    const drawTableBarangReturn = function() {
        $('.body-table-barang-return').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items_barang_return.length === 0) {
            row += `
                    <tr>
                        <td colspan="7" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.tfoot').append(row);
        } else {
            list_items_barang_return.map((item, index) => {
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.ref_no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.type_barang_text + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + item.satuan + '</td>';
                row += '<td>' + item.qty + '</td>';

                <?php if (!isset($data)) : ?>
                    row += '<td>' + `
                <input class="form-control qty-barang-digunakan" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" >` +
                        '</td>';
                <?php endif; ?>
                no++;
            });
            $('.body-table-barang-return').append(row);
        }

        // Tambahkan event listener untuk mengikuti perubahan nilai qty-barang-jadi
        $('.qty-barang-digunakan').on('input change', function() {
            var index = $(this).data('index');
            var newValue = $(this).val();
            list_items_barang_return[index].qty_dikembalikan = newValue;
        });
    }

    function drawTableAsalBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();

        $.each(data, function(i, v) {
            var newRow = $('<tr style="color:whitesmoke;">');
            if (typePengambilanStok == "FIFO" || parseFloat(v.stok_total) == 0) {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                `
                ));
            } else {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <div class="form-check">
                        <input  data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
                ));
            }

            newRow.append($('<td style="text-align:center;">').text(v.sumber));
            newRow.append($('<td style="text-align:center;">').text(v.stock_dokumen));
            newRow.append($('<td style="text-align:center;">').text(v.supplier_name));
            newRow.append($('<td style="text-align:center;">').text(v.bc_type));
            newRow.append($('<td style="text-align:center;">').text(v.no_aju));
            newRow.append($('<td style="text-align:center;">').text(v.stock_date));
            newRow.append($('<td style="text-align:center;">').text(v.barang));
            newRow.append($('<td style="text-align:center;">').text(v.satuan));
            newRow.append($('<td style="text-align:center;">').text(v.stok_total));
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

        dataTable.draw();
    }

    $('#select-item-btn').click(function() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        if (typePengambilanStock == "FIFO") {
            insertListFifo();
        } else {
            insertListPabean();
        }
        console.log(listStockAsal);
    });

    function insertListPabean() {
        var departmentID = $("#divisi_asal_id").val();
        var departmentText = $("#divisi_asal_id option:selected").text();
        var warehouseID = $("#warehouse_asal_id").val();
        var warehouseText = $("#warehouse_asal_id option:selected").text();
        var barangID = $(".select_nama_barang option:selected").data('barang_id');
        var spekID = $(".select_nama_barang option:selected").val();

        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();
        var id_selected = getIDListDataSelected();

        $.each(listStockAsal, function(i, v) {
            var currentID = Number(v.id);
            if ($.inArray(currentID, dataIds) !== -1) {
                var isIDSelected = $.grep(list_items_barang_digunakan, function(item) {
                    return item.id == Number(currentID);
                }).length > 0;

                if (!isIDSelected) {
                    listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                    listStockAsal[i].qty = 0;
                    listStockAsal[i].qty2 = 0;
                    listStockAsal[i].qty_isi = 0;
                    listStockAsal[i].departmentID = departmentID;
                    listStockAsal[i].departmentText = departmentText;
                    listStockAsal[i].warehouseID = warehouseID;
                    listStockAsal[i].warehouseText = warehouseText;
                    listStockAsal[i].barang1_id = barangID;
                    listStockAsal[i].barang2_id = spekID;
                    list_items_barang_digunakan.push(listStockAsal[i]);
                }
            }
        });
        drawTableSelectedItemBahan(list_items_barang_digunakan);
    }

    function insertListFifo() {
        var departmentID = $("#divisi_asal_id").val();
        var departmentText = $("#divisi_asal_id option:selected").text();
        var warehouseID = $("#warehouse_asal_id").val();
        var warehouseText = $("#warehouse_asal_id option:selected").text();

        var dataIds = getIDListDataSelected();
        var qtyMutasiFifo = parseFloat($('#qty_mutasi_fifo').val());
        var stockID = $(".select_nama_barang option:selected").data('stock_id');
        var barangID = $(".select_nama_barang option:selected").data('barang_id');
        var spekID = $(".select_nama_barang option:selected").val();

        if (isNaN(qtyMutasiFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan : Qty Mutasi Keluar Wajib Diisi',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            })
        } else {
            var totalStokTotal = 0;
            $.each(listStockAsal, function(i, v) {
                totalStokTotal += parseFloat(v.stok_total);
            });
            // deleteByStockID(stockID);
            if (qtyMutasiFifo > totalStokTotal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan : Stok barang tidak cukup !',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            } else {
                $.each(listStockAsal, function(i, v) {
                    var currentID = Number(v.id);
                    if ($.inArray(currentID, dataIds) == -1) {
                        var isIDSelected = $.grep(list_items_barang_digunakan, function(item) {
                            return item.id == Number(currentID);
                        }).length > 0;
                        if (!isIDSelected && qtyMutasiFifo != 0 && parseFloat(listStockAsal[i].stok_total) != 0) {
                            var mutasiQty = Math.min(qtyMutasiFifo, parseFloat(listStockAsal[i].stok_total));
                            listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                            listStockAsal[i].qty = 0;
                            listStockAsal[i].qty_isi = 0;
                            listStockAsal[i].departmentID = departmentID;
                            listStockAsal[i].departmentText = departmentText;
                            listStockAsal[i].warehouseID = warehouseID;
                            listStockAsal[i].warehouseText = warehouseText;
                            listStockAsal[i].barang1_id = barangID;
                            listStockAsal[i].barang2_id = spekID;
                            listStockAsal[i].qty2 = parseFloat(mutasiQty.toFixed(2));
                            list_items_barang_digunakan.push(listStockAsal[i]);
                            qtyMutasiFifo = qtyMutasiFifo - mutasiQty;
                        }
                    }
                });
            }
        }
        drawTableSelectedItemBahan(list_items_barang_digunakan);
    }

    function getListWarehouseAsal() {
        setLoading();
        // GET LIST WAREHOUSE ASAL
        $.ajax({
            url: `<?= base_url('mutasi/warehouse'); ?>`,
            method: "GET",
            data: {
                divisi_id: $(".divisi_asal_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_asal_id").empty()
                $(".warehouse_asal_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_asal_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                stopLoading();
            }
        });
    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('material-request/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".select_tipe_bahan option:selected").val(),
                divisi_id: $(".divisi_asal_id option:selected").val(),
                warehouse_id: $(".warehouse_asal_id option:selected").val(),
                kondisi: "nonkimia",
            },
            dataType: "json",
            success: function(res) {
                $(".select_nama_barang").empty()
                $(".select_nama_barang").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".select_nama_barang").append(`<option data-stock_id="${item.stock_id}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
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

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(list_items_barang_digunakan, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    const deleteRowDetailScrap = function(id) {
        const indexToRemove = list_items_barang_scrap.findIndex(item => item.barang_detail_id === id);
        if (indexToRemove !== -1) {
            list_items_barang_scrap.splice(indexToRemove, 1);
        }
        drawTableBarangScrap();
    }

    const resetFormDetailScrap = function() {
        $(".kode_barang_scrap").val('').change()
        $(".qty_scrap").val('')
        $(".department_id_scrap").val('').change()
        $(".warehouse_id_scrap").val('').change()
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

    const posting = function(id, status_posting) {
        const csrf = $(`[name="${csrfToken}"]`);
        Swal.fire({
            icon: 'question',
            title: status_posting == "1" ? "Yakin Akan Diposting ?" : "Yakin Akan di Unposting ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url("production-result/update-status"); ?>",
                    data: {
                        id: id,
                        status_posting: status_posting,
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
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url('production-result') ?>";
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
        })
    }
</script>

<?= $this->endSection(); ?>
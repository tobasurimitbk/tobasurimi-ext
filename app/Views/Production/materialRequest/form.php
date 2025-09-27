<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .kode_produksi+.select2-container--bootstrap-5 .select2-selection__choice {
        font-size: 13px !important;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <?php if (isset($ids)) { ?>
            <h1 class="title-name">Update Material Request</h1>
        <?php } else { ?>
            <h1 class="title-name">Tambah Material Request</h1>
        <?php } ?>
        <div class="col-button-tambah-spp text-right">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("material-request"); ?>">
                Kembali
            </a>
            <?php if (isset($dataMaterialRequests)) { ?>
                <?php if ($dataMaterialRequests->is_posted != 1) { ?>
                    <?php if (can('Produksi', 'Material Request', 'a')) : ?>
                        <button class="btn btn-success mr-1" onclick="posting('<?= !empty($ids) ? $ids : ''; ?>', 1)">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Produksi', 'Material Request', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="handleDelete('<?= !empty($ids) ? $ids : ''; ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <button class="btn btn-show-form btn-save btn-submit-form mr-1">
                        Simpan
                    </button>
                <?php } ?>
                <?php if (can('Produksi', 'Material Request', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("material-request/print/"); ?><?= encrypt($dataMaterialRequests->id) ?>')">
                        Print
                    </button>
                <?php endif; ?>
            <?php } else if (!isset($dataMaterialRequests)) { ?>
                <button class="btn btn-show-form btn-save btn-submit-form mr-1">
                    Simpan
                </button>
            <?php } else { ?>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" value="<?= !empty($ids) ? $ids : ""; ?>" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row mt-3">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Produksi</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kode_produksi" name="kode_produksi[]" id="kode_produksi[]" aria-label="Floating label select example" multiple>
                                <option value=""></option>
                                <?php
                                $selectedWO = explode(",", $dataMaterialRequests->work_order_id ?? "");
                                foreach ($dataWorkOrder ?? [] as $dataWO) :
                                ?>
                                    <option
                                        value="<?= $dataWO->id ?>"
                                        data-nama-barang="<?= $dataWO->nama_barang ?>"
                                        data-standart-production="<?= $dataWO->standart_production ?>"
                                        <?= (in_array($dataWO->id, $selectedWO ?? [])) ? 'selected' : '' ?>>
                                        <?= $dataWO->wo_no ?> - <?= $dataWO->nama_barang ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Kode Produksi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($dataMaterialRequestswithwo) ? $dataMaterialRequestswithwo->nama_barang : "" ?>" autocomplete="one-time-code" type="text" class="form-control barang_jadi" name="barang_jadi" id="barang_jadi" placeholder="Barang Jadi" readonly>
                            <label for="floatingInput">Barang Jadi</label>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control standart_production" name="standart_production" id="standart_production" placeholder="Jumlah Standart Produksi" readonly>
                            <label for="floatingInput">Jumlah Standart Produksi</label>
                        </div>
                    </div> -->
                </div>

                <div class="row mt-3">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Header Request</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataMaterialRequests) ? ($dataMaterialRequests->is_posted == "1" ? 'readonly' : '') : "readonly" ?> value="<?= !empty($dataMaterialRequests->req_no) ? $dataMaterialRequests->req_no : "AUTO GENERATE" ?>" autocomplete="one-time-code" type="text" class="form-control req_no" id="req_no" name="req_no" placeholder="Kode Produksi">
                                    <label for="floatingInput">Kode Request</label>
                                </div>
                                <div <?= !empty($dataMaterialRequests) ? 'style="display:none;"' : ''; ?> class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 25px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($dataMaterialRequests->request_date) ? date('d/m/Y', strtotime($dataMaterialRequests->request_date)) : "" ?>" <?= (!empty($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "readonly" : "" ?> autocomplete="one-time-code" type="text" class="form-control date_request" name="date_request" id="date_request" placeholder="Tanggal Request">
                            <label for="floatingInput">Tanggal Request</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($dataMaterialRequests->production_date) ? date('d/m/Y', strtotime($dataMaterialRequests->production_date)) : "" ?>" <?= (!empty($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "readonly" : "" ?> autocomplete="one-time-code" type="text" class="form-control date_production" name="date_production" id="date_production" placeholder="Tanggal Produksi">
                            <label for="floatingInput">Tanggal Produksi</label>
                        </div>
                    </div>
                </div>
            </form>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true" onclick="pindahTab('BAHAN BAKU')">Bahan Baku Dimutasikan</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false" onclick="pindahTab('LAIN')">Barang Scrap & Barang Setengah Jadi & Barang Jadi Dimutasikan</button>
                </li>

            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_barang" disabled id="type_barang" name="type_barang">
                                    <option value=""></option>
                                    <?php foreach ($tipeBarang as $t) : ?>
                                        <?php if ($t['description'] == "bahan_baku") : ?>
                                            <option selected value="<?= $t['description'] ?>">
                                                <?= strtoupper($t['value']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_asal_barang" id="type_asal_barang" name="type_asal_barang">
                                    <option value=""></option>
                                    <option value="SUPPLIER" selected>SUPPLIER</option>
                                    <option value="VENDOR">VENDOR</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Asal Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select divisi_asal_bahan_baku_id" id="divisi_asal_bahan_baku_id" name="divisi_asal_bahan_baku_id">
                                    <option value=""></option>
                                    <?php foreach ($dataDivisi as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Departemen Asal</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select warehouse_asal_bahan_baku_id" id="warehouse_asal_bahan_baku_id" name="warehouse_asal_bahan_baku_id">
                                    <option value=""></option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Warehouse Asal</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select divisi_tujuan_bahan_baku_id" id="divisi_tujuan_bahan_baku_id" name="divisi_tujuan_bahan_baku_id">
                                    <option value=""></option>
                                    <?php foreach ($dataDivisi as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Departemen Tujuan</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select warehouse_tujuan_bahan_baku_id" id="warehouse_tujuan_bahan_baku_id" name="warehouse_tujuan_bahan_baku_id">
                                    <option value=""></option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Warehouse Tujuan</label>
                            </div>
                        </div>

                        <div class="col-md-4" id="supplier_id_select">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select supplier_id" id="supplier_id" name="supplier_id">
                                    <option value=""></option>
                                    <?php foreach ($dataSupplierBahanBaku as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= $s['name']  ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Supplier (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="vendor_barang_id_select">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select vendor_barang_id" id="vendor_barang_id" name="vendor_barang_id">
                                    <option value=""></option>
                                    <?php foreach ($dataVendor as $v): ?>
                                        <option value="<?= $v['id'] ?>"> <?= strtoupper($v['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Vendor</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id">
                                    <option value=""></option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_pengambilan_stock_bahan_baku" id="type_pengambilan_stock_bahan_baku" name="type_pengambilan_stock_bahan_baku" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="PABEAN">PABEAN</option>
                                    <option value="FIFO">FIFO</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Tipe Pengambilan Stok</label>
                            </div>
                        </div>
                        <div class="col-md-4 form-fifo">
                            <div class="form-floating" style="height: 50px;">
                                <input placeholder="Qty" oninput="preventNegativeInput(this)" class="form-control qty_keluar_fifo" id="qty_keluar_fifo" name="qty_keluar_fifo" />
                                <label for="floatingInput" style="z-index: 1;">Qty Dikeluarkan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
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
                                <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_tujuan_id" id="divisi_tujuan_id" name="divisi_tujuan_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($dataDivisi as $d) : ?>
                                        <option <?= !empty($mutasi) ? ($mutasi['divisi_tujuan_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Departemen Tujuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_tujuan_id" id="warehouse_tujuan_id" name="warehouse_tujuan_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php if (!empty($warehouseTujuan)) : ?>
                                        <?php foreach ($warehouseTujuan as $w) : ?>
                                            <option <?= !empty($mutasi) ? ($mutasi['warehouse_tujuan_id'] == $w['id'] ? 'selected' : '') : '' ?> value="<?= $w['id'] ?>">
                                                <?= $w['warehouse_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Warehouse Tujuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_pengambilan_stock" id="type_pengambilan_stock" name="type_pengambilan_stock" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="PABEAN">PABEAN</option>
                                    <option value="FIFO">FIFO</option>
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
                                        <?php if ($t['description'] != "bahan_baku" && $t['description'] != "bahan_penolong"): ?>
                                            <option value="<?= $t['description'] ?>">
                                                <?= strtoupper($t['value']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Tipe Bahan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input placeholder="Keterangan (Optional)" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Keterangan (Optional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select select_nama_barang" name="select_nama_barang" id="select_nama_barang" disabled>
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select select_supplier" name="select_supplier" id="select_supplier" disabled>
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Supplier</label>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-table-button-tts">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-form-tts" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <!-- <th style="text-align: center;">Asal Barang</th> -->
                                    <th style="text-align: center;">No PO / No VBM</th>
                                    <th style="text-align: center;">Supplier / Vendor</th>
                                    <!-- <th style="text-align: center;">Dokumen Pabean</th> -->
                                    <!-- <th style="text-align: center;">No Aju / No Daftar</th> -->
                                    <th style="text-align: center;">Tgl PO / Tgl Vendor Masuk</th>
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
            <div class="row mt-3 mb-3">
                <div class="col-sm-2">
                    <button class="btn btn-show-detail btn-add btn-submit-barang" data-btn="detail-modal" id="select-item-btn">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah Barang
                    </button>
                </div>
            </div>
            <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home1" type="button" role="tab" aria-controls="home" aria-selected="true">
                        Data Bahan Baku Request
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile1" type="button" role="tab" aria-controls="profile" aria-selected="false">
                        Data Barang Scrap Request
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-toggle="tab" data-target="#contact2" type="button" role="tab" aria-controls="contact" aria-selected="false">
                        Data Barang Setengah Jadi Request
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-toggle="tab" data-target="#contact1" type="button" role="tab" aria-controls="contact" aria-selected="false">
                        Data Barang Jadi Request
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home1" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts dataTable" id="selectedItemTableBahanBaku" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Departemen / Warehouse Asal</th>
                                        <th style="text-align: center;">Departemen / Warehouse Tujuan</th>
                                        <th style="text-align: center;">Supplier / Vendor</th>
                                        <th style="text-align: center;">No PO / No VBM</th>
                                        <th style="text-align: center;">Tgl PO / Tgl Vendor Masuk</th>
                                        <th style="text-align: center;">Barang - Spesifikasi</th>
                                        <th style="text-align: center;">Satuan</th>
                                        <th style="text-align: center;">Qty Awal</th>
                                        <th style="text-align: center;">Qty Direquest</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                                <tfoot class="footer-table">
                                    <td style="text-align: center; font-weight: bold;" colspan="8">Total</td>
                                    <td style="text-align: right; font-weight: bold;"><span class="nilai-total-bahan-baku-awal">0</span></td>
                                    <td style="text-align: right; font-weight: bold;"><span class="nilai-total-bahan-baku-request">0</span></td>
                                    <td style="text-align: right; font-weight: bold;"></td>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold total-bahan-baku-request">Total Bahan Baku Request : </label>
                        </div>
                    </div> -->
                </div>
                <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts dataTable" id="selectedItemTableBahan" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Departemen / Warehouse Asal</th>
                                        <th style="text-align: center;">Departemen / Warehouse Tujuan</th>
                                        <th style="text-align: center;">Tipe Barang</th>
                                        <th style="text-align: center;">Dokumen Pabean</th>
                                        <th style="text-align: center;">No Aju / No Daftar</th>
                                        <th style="text-align: center;">Tanggal Penerimaan</th>
                                        <th style="text-align: center;">Barang - Spesifikasi</th>
                                        <th style="text-align: center;">Satuan</th>
                                        <th style="text-align: center;">Qty Awal</th>
                                        <th style="text-align: center;">Qty Direquest</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                                <tfoot class="footer-table">
                                    <td style="text-align: center; font-weight: bold;" colspan="9">Total</td>
                                    <td style="text-align: right; font-weight: bold;"><span class="nilai-total-barang-scrap-awal">0</span></td>
                                    <td style="text-align: right; font-weight: bold;"><span class="nilai-total-barang-scrap-request">0</span></td>
                                    <td style="text-align: right; font-weight: bold;"></td>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold total-barang-scrap-request">Total Barang Scrap Request : <span class="nilai-total-barang-scrap-request">0</span></label>
                        </div>
                    </div> -->
                </div>
                <div class="tab-pane fade" id="contact2" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="row mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts dataTable" id="selectedItemTableBahanSetengahJadi" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Departemen / Warehouse Asal</th>
                                        <th style="text-align: center;">Departemen / Warehouse Tujuan</th>
                                        <th style="text-align: center;">Tipe Barang</th>
                                        <th style="text-align: center;">Dokumen Pabean</th>
                                        <th style="text-align: center;">No Aju / No Daftar</th>
                                        <th style="text-align: center;">Tanggal Penerimaan</th>
                                        <th style="text-align: center;">Barang - Spesifikasi</th>
                                        <th style="text-align: center;">Satuan</th>
                                        <th style="text-align: center;">Qty Awal</th>
                                        <th style="text-align: center;">Qty Direquest</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                                <tfoot class="footer-table">
                                    <td style="text-align: center; font-weight: bold;" colspan="9">Total</td>
                                    <td style="text-align: right; font-weight: bold;"><span class="nilai-total-barang-setengah-jadi-awal">0</span></td>
                                    <td style="text-align: right; font-weight: bold;"><span class="nilai-total-barang-setengah-jadi-request">0</span></td>
                                    <td style="text-align: right; font-weight: bold;"></td>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold total-barang-setengah-jadi-request">Total Barang Setengah Jadi Request : <span class="nilai-total-barang-setengah-jadi-request">0</span></label>
                        </div>
                    </div> -->
                </div>
                <div class="tab-pane fade" id="contact1" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="row mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts dataTable" id="selectedItemTableBahanJadi" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Departemen / Warehouse Asal</th>
                                        <th style="text-align: center;">Departemen / Warehouse Tujuan</th>
                                        <th style="text-align: center;">Tipe Barang</th>
                                        <th style="text-align: center;">Dokumen Pabean</th>
                                        <th style="text-align: center;">No Aju / No Daftar</th>
                                        <th style="text-align: center;">Tanggal Penerimaan</th>
                                        <th style="text-align: center;">Barang - Spesifikasi</th>
                                        <th style="text-align: center;">Satuan</th>
                                        <th style="text-align: center;">Qty Kaleng</th>
                                        <th style="text-align: center;">Qty Kaleng Direquest</th>
                                        <th style="text-align: center;">Qty Isi Direquest</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                                <tfoot class="footer-table">
                                    <td style="text-align: center; font-weight: bold;" colspan="9">Total</td>
                                    <td style="text-align: right; font-weight: bold;"><span class="nilai-total-barang-jadi-awal">0</span></td>
                                    <td style="text-align: right; font-weight: bold;"><span class="nilai-total-barang-jadi-request">0</span></td>
                                    <td style="text-align: right; font-weight: bold;"><span class="nilai-total-isi-barang-jadi-request">0</span></td>
                                    <td style="text-align: right; font-weight: bold;"></td>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold total-barang-jadi-request">Total Barang Jadi Request : <span class="nilai-total-barang-jadi-request">0</span></label>
                        </div>
                    </div> -->
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var listStockAsal = [];
    var listStockSelectedBahanBaku = [];
    var listStockSelectedBahan = [];
    var listStockSelectedBahanSetengahJadi = [];
    var listStockSelectedBahanJadi = [];

    let sortDataBarang = "createdAt";
    let sortTypeDataBarang = "DESC";

    $('#vendor_barang_id_select').hide();
    $('.form-fifo').hide();
    updateBarangJadi();

    <?php if (!empty($dataMaterialRequestDetailsBahanBaku)): ?>
        <?php foreach ($dataMaterialRequestDetailsBahanBaku as $materialRequestDetails): ?>
            listStockSelectedBahanBaku.push(<?= json_encode([
                                                'id' => $materialRequestDetails['id'],
                                                'id_material_request_detail' => null,
                                                'stock_detail_id' => $materialRequestDetails['stock_detail_id'],
                                                'supplier_id' => $materialRequestDetails['supplier_id'],
                                                'harga_umum' => $materialRequestDetails['harga_umum'],
                                                'harga_harian' => $materialRequestDetails['harga_harian'],
                                                'harga_bulanan' => $materialRequestDetails['harga_bulanan'],
                                                'keterangan' => '',
                                                'barang' => $materialRequestDetails['barang'],
                                                'bc_id' => $materialRequestDetails['bc_id'],
                                                'bc_type' => $materialRequestDetails['bc_type'],
                                                'departmentID' => $materialRequestDetails['divisi_id'],
                                                'departmentText' => $materialRequestDetails['divisi_asal_text'],
                                                'departmentTujuanText' => $materialRequestDetails['divisi_tujuan_text'],
                                                'departmentTujuanID' => $materialRequestDetails['divisi_tujuan_id'],
                                                'warehouseTujuanID' => $materialRequestDetails['warehouse_tujuan_id'],
                                                'no_aju' => $materialRequestDetails['no_aju'],
                                                'no_dokumen_1' => '-',
                                                'no_dokumen_2' => '-',
                                                'qty' => floatval($materialRequestDetails['qty']),
                                                'qty2' => floatval($materialRequestDetails['qty2']),
                                                'qty_isi' => floatval($materialRequestDetails['qty_isi']),
                                                'satuan' => $materialRequestDetails['kode_satuan'],
                                                'stock_date' => $materialRequestDetails['stock_date'],
                                                'stock_dokumen' => $materialRequestDetails['stock_dokumen'],
                                                'stock_id' => $materialRequestDetails['stock_id'],
                                                'stok_total' => floatval($materialRequestDetails['stok_total']),
                                                'realStok' => floatval($materialRequestDetails['realStok']),
                                                'supplier_name' => $materialRequestDetails['supplier_name'],
                                                'type_barang' => $materialRequestDetails['type_barang'],
                                                'type_barang_text' => $materialRequestDetails['type_barang_text'],
                                                'warehouseID' => $materialRequestDetails['warehouse_id'],
                                                'warehouseText' => $materialRequestDetails['warehouse_asal_text'],
                                                'warehouseTujuanText' => $materialRequestDetails['warehouse_tujuan_text'],
                                                'no_daftar' => $materialRequestDetails['no_daftar'],
                                            ]) ?>);
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($dataMaterialRequestDetails)): ?>
        <?php foreach ($dataMaterialRequestDetails as $materialRequestDetails): ?>
            <?php
            $item = [
                'id_material_request_detail' => $materialRequestDetails->id,
                'barang' => $materialRequestDetails->nama_barang,
                'bc_id' => $materialRequestDetails->bc_id,
                'bc_type' => $materialRequestDetails->ref_no,
                'departmentID' => $materialRequestDetails->divisi_id,
                'departmentText' => $materialRequestDetails->divisi_asal_text,
                'departmentTujuanText' => $materialRequestDetails->divisi_tujuan_text,
                'departmentTujuanID' => $materialRequestDetails->divisi_tujuan_id,
                'warehouseTujuanID' => $materialRequestDetails->warehouse_tujuan_id,
                'no_aju' => $materialRequestDetails->no_aju,
                'no_dokumen_1' => '-',
                'no_dokumen_2' => '-',
                'qty' => floatval($materialRequestDetails->qty),
                'qty2' => floatval($materialRequestDetails->qty2),
                'qty_isi' => floatval($materialRequestDetails->qty_isi),
                'satuan' => $materialRequestDetails->kode_satuan,
                'stock_date' => date('d/m/Y', strtotime($materialRequestDetails->stock_date)),
                'stock_dokumen' => $materialRequestDetails->stock_dokumen,
                'stock_id' => $materialRequestDetails->stock_id,
                'stok_total' => floatval($materialRequestDetails->qty),
                'supplier_name' => '-',
                'type_barang' => $materialRequestDetails->barang_type,
                'type_barang_text' => $materialRequestDetails->barang_type_text,
                'warehouseID' => $materialRequestDetails->warehouse_id,
                'warehouseText' => $materialRequestDetails->warehouse_tujuan_text,
                'warehouseTujuanText' => $materialRequestDetails->warehouse_tujuan_text,
                'no_daftar' => $materialRequestDetails->no_daftar,
            ];
            ?>

            <?php if ($materialRequestDetails->barang_type == 'bahan_setengah_jadi'): ?>
                listStockSelectedBahanSetengahJadi.push(<?= json_encode($item) ?>);
            <?php elseif ($materialRequestDetails->barang_type == 'bahan_jadi'): ?>
                listStockSelectedBahanJadi.push(<?= json_encode($item) ?>);
            <?php elseif ($materialRequestDetails->barang_type == 'bahan_scrap'): ?>
                listStockSelectedBahan.push(<?= json_encode($item) ?>);
            <?php endif; ?>
        <?php endforeach; ?>
        console.log(listStockSelectedBahanBaku);

        drawTableSelectedItemBahanBaku(listStockSelectedBahanBaku);
        drawTableSelectedItemBahan(listStockSelectedBahan);
        drawTableSelectedItemBahanSetengahJadi(listStockSelectedBahanSetengahJadi);
        drawTableSelectedItemBahanJadi(listStockSelectedBahanJadi);
    <?php endif; ?>

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

    $('#divisi_tujuan_id').select2({
        placeholder: "Pilih Departemen Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // CARI WAREHOUSE TUJUAN
        getListWarehouseTujuan();
    });

    $('#warehouse_tujuan_id').select2({
        placeholder: "Pilih Warehouse Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

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
        // CARI DIVISI TUJUAN
        // getListDivisiTujuan();
        // CARI WAREHOUSE ASAL
        getListWarehouseAsal()
        // RESET TYPE BARANG
        $('#select_tipe_bahan').val(null).change();
        // RESET SEMUA LIST
        listStockAsal = [];
        drawTableAsalBarang(listStockAsal);
    });

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

    $('.select_nama_barang').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        listStockAsal = [];
        drawTableAsalBarang();
        getListDokumenPabean();
    });

    $('.select_supplier').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        listStockAsal = [];
        drawTableAsalBarang();
        getListDokumenPabean();
    });

    // Kode Produksi
    $('.kode_produksi').select2({
        placeholder: "",
        theme: "bootstrap-5",
        // allowClear: true
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

    $('.kode_barang').select2({
        placeholder: "Pilih Kode Barang",
        theme: "bootstrap-5",
        dropdownParent: $(".detail-modal .modal-content"),
        tags: false,
        allowClear: true
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
    });

    $('#type_asal_barang').select2({
        placeholder: "Pilih Asal Barang",
        theme: "bootstrap-5",
    }).change(function() {
        getDropdownAsalBarang();
        // GET LIST BARANG
        getListBarangBahanBaku();
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
    }).change(function() {
        // LIST DOKUMEN PABEAN
        getListDokumenPabeanBahanBaku();
    });

    $('#vendor_barang_id').select2({
        placeholder: "Pilih Vendor",
        theme: "bootstrap-5",
    }).change(function() {
        // LIST DOKUMEN PABEAN
        getListDokumenPabeanBahanBaku();
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
    }).change(function() {
        // LIST DOKUMEN PABEAN
        getListDokumenPabeanBahanBaku();
    });

    $('#divisi_asal_bahan_baku_id').select2({
        placeholder: "Pilih Departemen Asal",
        theme: "bootstrap-5",
    }).change(function() {
        // GET BARANG
        getListBarangBahanBaku();
        // GET WAREHOUSE
        getListWarehouseAsalBahanBaku();
    });

    $('#divisi_tujuan_bahan_baku_id').select2({
        placeholder: "Pilih Departemen Tujuan",
        theme: "bootstrap-5",
    }).change(function() {
        // GET WAREHOUSE
        getListWarehouseTujuanBahanBaku();
    });


    $('#warehouse_asal_bahan_baku_id').select2({
        placeholder: "Pilih Warehouse Asal",
        theme: "bootstrap-5",
    }).change(function() {
        // GET BARANG
        getListBarangBahanBaku();
    });

    $('#warehouse_tujuan_bahan_baku_id').select2({
        placeholder: "Pilih Warehouse Tujuan",
        theme: "bootstrap-5",
    }).change(function() {

    });

    $('#type_pengambilan_stock_bahan_baku').select2({
        placeholder: "Pilih Tipe Ambil Stok",
        theme: "bootstrap-5",
    }).change(function() {
        var typePengambilanStock = $('#type_pengambilan_stock_bahan_baku option:selected').val();
        if (typePengambilanStock == "PABEAN") {
            $('.form-fifo').hide();
        } else if (typePengambilanStock == "FIFO") {
            $('.form-fifo').show();
        } else {
            $('.form-fifo').hide();

        }

        drawTableAsalBarang(listStockAsal);
    });


    // Mengaktifkan datepicker
    $('#date_production').datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
    $('#date_request').datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    //CSS SELECT2 FLOATING LABEL
    $('.select_tipe_bahan,.kode_barang,.select_nama_barang,.select_supplier,#type_asal_barang,#type_barang,#supplier_id,#vendor_barang_id,#spesifikasi_id,#divisi_asal_bahan_baku_id,#warehouse_asal_bahan_baku_id,#type_pengambilan_stock_bahan_baku,.divisi_tujuan_bahan_baku_id,.warehouse_tujuan_bahan_baku_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.select_tipe_bahan,.kode_barang,.select_nama_barang,.select_supplier,#type_asal_barang,#type_barang,#supplier_id,#vendor_barang_id,#spesifikasi_id,#divisi_asal_bahan_baku_id,#warehouse_asal_bahan_baku_id,#type_pengambilan_stock_bahan_baku,.divisi_tujuan_bahan_baku_id,.warehouse_tujuan_bahan_baku_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.select_tipe_bahan,.kode_barang,.select_nama_barang,.select_supplier,#type_asal_barang,#type_barang,#supplier_id,#vendor_barang_id,#spesifikasi_id,#divisi_asal_bahan_baku_id,#warehouse_asal_bahan_baku_id,#type_pengambilan_stock_bahan_baku,.divisi_tujuan_bahan_baku_id,.warehouse_tujuan_bahan_baku_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $("#divisi_asal_id,#divisi_tujuan_id,#warehouse_asal_id,#warehouse_tujuan_id,#operasi,#type_pengambilan_stock,.select_tipe_bahan,.kode_barang,.select_nama_barang,.select_supplier,#type_asal_barang,#type_barang,#supplier_id,#vendor_barang_id,#spesifikasi_id,#divisi_asal_bahan_baku_id,#warehouse_asal_bahan_baku_id,#type_pengambilan_stock_bahan_baku,.divisi_tujuan_bahan_baku_id,.warehouse_tujuan_bahan_baku_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '30px').css('margin-left', '-7px');

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
            date_request: {
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
            date_request: {
                required: "Tanggal request wajib diisi"
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
            // console.log(elem);
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
        var listMaterialCheck = [].concat(listStockSelectedBahanBaku, listStockSelectedBahan, listStockSelectedBahanSetengahJadi, listStockSelectedBahanJadi);

        if (listMaterialCheck.length == 0) {
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

                $.each(listStockSelectedBahanBaku, function(i, v) {
                    if (parseFloat(listStockSelectedBahanBaku[i].qty2) > parseFloat(listStockSelectedBahanBaku[i].stok_total) || isNaN(listStockSelectedBahanBaku[i].qty2) || listStockSelectedBahanBaku[i].qty2 == undefined || parseFloat(listStockSelectedBahanBaku[i].qty2) == 0) {
                        dataError = listStockSelectedBahanBaku[i];
                        // console.log("Kesini", listStockSelectedBahanBaku[i]);
                        isValid = false;
                    }
                });

                $.each(listStockSelectedBahan, function(i, v) {
                    if (parseFloat(listStockSelectedBahan[i].qty2) > parseFloat(listStockSelectedBahan[i].stok_total) || isNaN(listStockSelectedBahan[i].qty2) || listStockSelectedBahan[i].qty2 == undefined || parseFloat(listStockSelectedBahan[i].qty2) == 0) {
                        dataError = listStockSelectedBahan[i];
                        // console.log("Kesin2");

                        isValid = false;
                    }
                });

                $.each(listStockSelectedBahanSetengahJadi, function(i, v) {
                    if (parseFloat(listStockSelectedBahanSetengahJadi[i].qty2) > parseFloat(listStockSelectedBahanSetengahJadi[i].stok_total) || isNaN(listStockSelectedBahanSetengahJadi[i].qty2) || listStockSelectedBahanSetengahJadi[i].qty2 == undefined || parseFloat(listStockSelectedBahanSetengahJadi[i].qty2) == 0) {
                        dataError = listStockSelectedBahanSetengahJadi[i];
                        // console.log("Kesin3");

                        isValid = false;
                    }
                });

                $.each(listStockSelectedBahanJadi, function(i, v) {
                    if (parseFloat(listStockSelectedBahanJadi[i].qty2) > parseFloat(listStockSelectedBahanJadi[i].stok_total) || isNaN(listStockSelectedBahanJadi[i].qty2) || listStockSelectedBahanJadi[i].qty2 == undefined || parseFloat(listStockSelectedBahanJadi[i].qty2) == 0 || isNaN(listStockSelectedBahanJadi[i].qty_isi) || listStockSelectedBahanJadi[i].qty_isi == undefined || parseFloat(listStockSelectedBahanJadi[i].qty_isi) == 0) {
                        dataError = listStockSelectedBahanJadi[i];
                        // console.log("Kesin4");

                        isValid = false;
                    }
                });

                // console.log(listStockSelectedBahanBaku, listStockSelectedBahan);


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
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));
                            var listMaterial = [].concat(listStockSelectedBahanBaku, listStockSelectedBahan, listStockSelectedBahanSetengahJadi, listStockSelectedBahanJadi);
                            data.append('listMaterial', JSON.stringify(listMaterial));
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
                                                window.location.href = "<?= base_url("material-request"); ?>"
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
                                                window.location.href = "<?= base_url("material-request"); ?>"
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

    $(".kode_produksi").change(function() {
        updateBarangJadi();
    });

    $('.btn-hide-detail').click(function() {
        $('.detail-modal').modal('hide');
    });

    // const changeStatus = function() {
    //     let value = document.getElementById('auto_generate').checked ? true : false;

    //     if (value) {
    //         $(".req_no").attr("readonly", true);
    //         $(".req_no").val("AUTO GENERATE");
    //     } else {
    //         $(".req_no").attr("readonly", false);
    //         $(".req_no").val("");
    //     }
    // }

    const deleteRowDetail = function(id) {
        const indexToRemove = listStockAsal.findIndex(item => item.barang_detail_id === id);
        if (indexToRemove !== -1) {
            listStockAsal.splice(indexToRemove, 1);
        }
        drawTable();
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
                    // console.log(item);
                    $(".select_nama_barang").append(`<option data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".select_nama_barang").val();
            }
        });
    }

    function getListSupplier() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('material-request/list-supplier'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".select_tipe_bahan option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".select_supplier").empty()
                $(".select_supplier").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    // console.log(item);
                    $(".select_supplier").append(`<option value="${item.id}">(${item.kode}) ${item.name}</option>`)
                })
                $(".select_supplier").val();
            }
        });
    }

    function getListDokumenPabean() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('material-request/list-stock-dokumen-bc'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: $(".select_nama_barang option:selected").data('stock_id'),
                supplier_id: $(".select_supplier option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                // LIST STOK PER BC
                // console.log(res.data);
                listStockAsal = [];
                listStockAsal = res.data;
                console.log(res.data);
                drawTableAsalBarang(res.data);
            }
        });
    }

    function getListDivisiTujuan() {
        setLoading();
        // GET LIST DIVISI TUJUAN
        $.ajax({
            url: `<?= base_url('mutasi/list-divisi-except'); ?>`,
            method: "GET",
            data: {
                divisi_id: $(".divisi_asal_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".divisi_tujuan_id").empty()
                $(".divisi_tujuan_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".divisi_tujuan_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                })
                stopLoading();
            }
        });
    }

    function getListWarehouseTujuan() {
        // GET LIST WAREHOUSE TUJUAN
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
                divisi_id: $(".divisi_tujuan_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_tujuan_id").empty()
                $(".warehouse_tujuan_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_tujuan_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
            }
        });
    }

    const posting = function(id, status_posting) {
        // console.log(id);
        Swal.fire({
            icon: 'question',
            title: status_posting == "1" ? "Yakin Akan Diposting ?" : "Yakin Akan di Unposting ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url("material-request/update-status"); ?>",
                    data: {
                        id: id,
                        status_posting: status_posting
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
                        // console.log(response);

                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url('material-request/details/') ?>" + response.id
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

    function drawTableAsalBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();
        var typePengambilanStockBahanBaku = $('#type_pengambilan_stock_bahan_baku option:selected').val();
        var typePengambilanStokFinal = '';

        if (typePengambilanStok != '') {
            typePengambilanStokFinal = typePengambilanStok;
        } else {
            typePengambilanStokFinal = typePengambilanStockBahanBaku;
        }

        $.each(data, function(i, v) {
            var newRow = $('<tr style="color:whitesmoke;">');
            if (typePengambilanStokFinal == "FIFO" || parseFloat(v.stok_total) == 0) {
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

            // newRow.append($('<td style="text-align:center;">').text(v.sumber));
            newRow.append($('<td style="text-align:center;">').text(v.stock_dokumen));
            newRow.append($('<td style="text-align:center;">').text(v.supplier_name));
            // newRow.append($('<td style="text-align:center;">').text(v.bc_type));
            // newRow.append($('<td style="text-align: center;">').text(`${v.no_aju ? v.no_aju : '-'} / ${v.no_daftar ? v.no_daftar : '-'}`));
            newRow.append($('<td style="text-align:center;">').text(v.stock_date));
            newRow.append($('<td style="text-align:center;">').text(v.barang));
            newRow.append($('<td style="text-align:center;">').text(v.satuan));
            newRow.append($('<td style="text-align:center;">').text(v.stok_total));
            table.find('tbody').append(newRow);
        });

        dataTable = $('#dataTable').DataTable({

            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");

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
        var typePengambilanStockBahanBaku = $('#type_pengambilan_stock_bahan_baku option:selected').val();
        var divisiTujuanBahanBaku = $('#divisi_tujuan_bahan_baku_id option:selected').val();
        var warehouseTujuanBahanBaku = $('#warehouse_tujuan_bahan_baku_id option:selected').val();

        if (typePengambilanStock != '') {
            // Bukan Bahan Baku
            if (typePengambilanStock == "FIFO") {
                insertListFifo();
            } else {
                insertListPabean();
            }
        } else if (typePengambilanStockBahanBaku != '') {
            // Bukan Bahan Baku
            if (divisiTujuanBahanBaku == '') {
                Swal.fire({
                    icon: 'error',
                    title: "Pilih Departemen Tujuan",
                    confirmButtonColor: '#4e73df',
                });
                return;
            } else if (warehouseTujuanBahanBaku == '') {
                Swal.fire({
                    icon: 'error',
                    title: "Pilih Warehouse Tujuan",
                    confirmButtonColor: '#4e73df',
                });
                return;
            }
            if (typePengambilanStockBahanBaku == "FIFO") {
                insertListFifo();
            } else {
                insertListPabean();
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: "Pilih Tipe Pengambilan Stok Dahulu",
                confirmButtonColor: '#4e73df',
            });
        }
    });

    function insertListPabean() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        var typePengambilanStockBahanBaku = $('#type_pengambilan_stock_bahan_baku option:selected').val();

        if (typePengambilanStock != '') {
            // Bukan Bahan Baku
            var departmentID = $("#divisi_asal_id").val();
            var departmentText = $("#divisi_asal_id option:selected").text();
            var warehouseID = $("#warehouse_asal_id").val();
            var warehouseText = $("#warehouse_asal_id option:selected").text();
            var departmentTujuanID = $("#divisi_tujuan_id").val();
            var departmentTujuanText = $("#divisi_tujuan_id option:selected").text();
            var warehouseTujuanID = $("#warehouse_tujuan_id").val();
            var keterangan = $("#keterangan").val();
            var warehouseTujuanText = $("#warehouse_tujuan_id option:selected").text();
        } else if (typePengambilanStockBahanBaku != '') {
            // Bahan Baku
            var departmentID = $("#divisi_asal_bahan_baku_id").val();
            var departmentText = $("#divisi_asal_bahan_baku_id option:selected").text();
            var warehouseID = $("#warehouse_asal_bahan_baku_id").val();
            var warehouseText = $("#warehouse_asal_bahan_baku_id option:selected").text();
            var departmentTujuanID = $("#divisi_tujuan_bahan_baku_id").val();
            var departmentTujuanText = $("#divisi_tujuan_bahan_baku_id option:selected").text();
            var warehouseTujuanID = $("#warehouse_tujuan_bahan_baku_id").val();
            var keterangan = $("#keterangan").val();
            var warehouseTujuanText = $("#warehouse_tujuan_bahan_baku_id option:selected").text();
        }

        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();
        var id_selected = getIDListDataSelected();

        $.each(listStockAsal, function(i, v) {
            var currentID = v.id;

            if (listStockAsal[i].type_barang == "bahan_baku") {
                // console.log(currentID, dataIds);

                if ($.inArray(currentID, dataIds) !== -1) {
                    var isIDSelected = $.grep(listStockSelectedBahanBaku, function(item) {
                        return item.id == currentID;
                    }).length > 0;
                    if (!isIDSelected) {
                        listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                        listStockAsal[i].qty = parseFloat(listStockAsal[i].stok_total);
                        listStockAsal[i].qty2 = 0;
                        listStockAsal[i].qty_isi = 0;
                        listStockAsal[i].departmentID = departmentID;
                        listStockAsal[i].departmentText = departmentText;
                        listStockAsal[i].warehouseID = warehouseID;
                        listStockAsal[i].warehouseText = warehouseText;
                        listStockAsal[i].keterangan = keterangan;
                        listStockAsal[i].departmentTujuanID = departmentTujuanID;
                        listStockAsal[i].departmentTujuanText = departmentTujuanText;
                        listStockAsal[i].warehouseTujuanID = warehouseTujuanID;
                        listStockAsal[i].warehouseTujuanText = warehouseTujuanText;
                        listStockSelectedBahanBaku.push(listStockAsal[i]);
                    }
                }
            } else if (listStockAsal[i].type_barang == "bahan_jadi") {
                currentID = Number(currentID);
                if ($.inArray(currentID, dataIds) !== -1) {
                    var isIDSelected = $.grep(listStockSelectedBahanJadi, function(item) {
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
                        listStockAsal[i].keterangan = keterangan;
                        listStockAsal[i].departmentTujuanID = departmentTujuanID;
                        listStockAsal[i].departmentTujuanText = departmentTujuanText;
                        listStockAsal[i].warehouseTujuanID = warehouseTujuanID;
                        listStockAsal[i].warehouseTujuanText = warehouseTujuanText;
                        listStockSelectedBahanJadi.push(listStockAsal[i]);
                    }
                }
            } else if (listStockAsal[i].type_barang == "bahan_setengah_jadi") {
                currentID = Number(currentID);
                if ($.inArray(currentID, dataIds) == -1) {
                    var isIDSelected = $.grep(listStockSelectedBahanSetengahJadi, function(item) {
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
                        listStockAsal[i].keterangan = keterangan;
                        listStockAsal[i].departmentTujuanID = departmentTujuanID;
                        listStockAsal[i].departmentTujuanText = departmentTujuanText;
                        listStockAsal[i].warehouseTujuanID = warehouseTujuanID;
                        listStockAsal[i].warehouseTujuanText = warehouseTujuanText;
                        listStockAsal[i].qty2 = parseFloat(mutasiQty.toFixed(2));
                        listStockSelectedBahanSetengahJadi.push(listStockAsal[i]);
                        qtyMutasiFifo = qtyMutasiFifo - mutasiQty;
                    }
                }
            } else {
                if ($.inArray(currentID, dataIds) == -1) {
                    currentID = Number(currentID);
                    var isIDSelected = $.grep(listStockSelectedBahan, function(item) {
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
                        listStockAsal[i].keterangan = keterangan;
                        listStockAsal[i].departmentTujuanID = departmentTujuanID;
                        listStockAsal[i].departmentTujuanText = departmentTujuanText;
                        listStockAsal[i].warehouseTujuanID = warehouseTujuanID;
                        listStockAsal[i].warehouseTujuanText = warehouseTujuanText;
                        listStockAsal[i].qty2 = parseFloat(mutasiQty.toFixed(2));
                        listStockSelectedBahan.push(listStockAsal[i]);
                        qtyMutasiFifo = qtyMutasiFifo - mutasiQty;
                    }
                }
            }
        });
        drawTableSelectedItemBahanBaku(listStockSelectedBahanBaku);
        drawTableSelectedItemBahan(listStockSelectedBahan);
        drawTableSelectedItemBahanSetengahJadi(listStockSelectedBahanSetengahJadi);
        drawTableSelectedItemBahanJadi(listStockSelectedBahanJadi);
    }

    function insertListFifo() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        var typePengambilanStockBahanBaku = $('#type_pengambilan_stock_bahan_baku option:selected').val();

        if (typePengambilanStock != '') {
            // Bukan Bahan Baku
            var departmentID = $("#divisi_asal_id").val();
            var departmentText = $("#divisi_asal_id option:selected").text();
            var warehouseID = $("#warehouse_asal_id").val();
            var warehouseText = $("#warehouse_asal_id option:selected").text();
            var departmentTujuanID = $("#divisi_tujuan_id").val();
            var departmentTujuanText = $("#divisi_tujuan_id option:selected").text();
            var warehouseTujuanID = $("#warehouse_tujuan_id").val();
            var keterangan = $("#keterangan").val();
            var warehouseTujuanText = $("#warehouse_tujuan_id option:selected").text();
            var qtyMutasiFifo = parseFloat($('#qty_mutasi_fifo').val());

        } else if (typePengambilanStockBahanBaku != '') {
            // Bahan Baku
            var departmentID = $("#divisi_asal_bahan_baku_id").val();
            var departmentText = $("#divisi_asal_bahan_baku_id option:selected").text();
            var warehouseID = $("#warehouse_asal_bahan_baku_id").val();
            var warehouseText = $("#warehouse_asal_bahan_baku_id option:selected").text();
            var departmentTujuanID = $("#divisi_tujuan_bahan_baku_id").val();
            var departmentTujuanText = $("#divisi_tujuan_bahan_baku_id option:selected").text();
            var warehouseTujuanID = $("#warehouse_tujuan_bahan_baku_id").val();
            var keterangan = $("#keterangan").val();
            var warehouseTujuanText = $("#warehouse_tujuan_bahan_baku_id option:selected").text();
            var qtyMutasiFifo = parseFloat($('#qty_keluar_fifo').val());
        }

        var dataIds = getIDListDataSelected();
        var stockID = $(".select_nama_barang option:selected").data('stock_id');

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
            if (parseFloat(qtyMutasiFifo) > parseFloat(totalStokTotal.toFixed(2))) {
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
                    var currentID = v.id;
                    if (listStockAsal[i].type_barang == "bahan_baku") {
                        if ($.inArray(currentID, dataIds) == -1) {
                            var isIDSelected = $.grep(listStockSelectedBahanBaku, function(item) {
                                return item.id == currentID;
                            }).length > 0;
                            console.log(listStockAsal[i], isIDSelected, parseFloat(qtyMutasiFifo), parseFloat(listStockAsal[i].stok_total));

                            if (!isIDSelected && parseFloat(qtyMutasiFifo) > 0 && parseFloat(listStockAsal[i].stok_total) > 0) {
                                var mutasiQty = Math.min(parseFloat(qtyMutasiFifo), parseFloat(listStockAsal[i].stok_total));
                                console.log(mutasiQty);

                                listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                                listStockAsal[i].qty = 0;
                                listStockAsal[i].qty_isi = 0;
                                listStockAsal[i].departmentID = departmentID;
                                listStockAsal[i].departmentText = departmentText;
                                listStockAsal[i].warehouseID = warehouseID;
                                listStockAsal[i].warehouseText = warehouseText;
                                listStockAsal[i].keterangan = keterangan;
                                listStockAsal[i].departmentTujuanID = departmentTujuanID;
                                listStockAsal[i].departmentTujuanText = departmentTujuanText;
                                listStockAsal[i].warehouseTujuanID = warehouseTujuanID;
                                listStockAsal[i].warehouseTujuanText = warehouseTujuanText;
                                listStockAsal[i].qty2 = parseFloat(mutasiQty.toFixed(2));
                                listStockSelectedBahanBaku.push(listStockAsal[i]);
                                qtyMutasiFifo = parseFloat(qtyMutasiFifo) - mutasiQty;
                            }
                        }
                    } else if (listStockAsal[i].type_barang == "bahan_jadi") {
                        currentID = Number(currentID);
                        if ($.inArray(currentID, dataIds) == -1) {
                            var isIDSelected = $.grep(listStockSelectedBahanJadi, function(item) {
                                return item.id == Number(currentID);
                            }).length > 0;
                            if (!isIDSelected && parseFloat(qtyMutasiFifo) > 0 && parseFloat(listStockAsal[i].stok_total) > 0) {
                                var mutasiQty = Math.min(parseFloat(qtyMutasiFifo), parseFloat(listStockAsal[i].stok_total));
                                listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                                listStockAsal[i].qty = 0;
                                listStockAsal[i].qty_isi = 0;
                                listStockAsal[i].departmentID = departmentID;
                                listStockAsal[i].departmentText = departmentText;
                                listStockAsal[i].warehouseID = warehouseID;
                                listStockAsal[i].warehouseText = warehouseText;
                                listStockAsal[i].keterangan = keterangan;
                                listStockAsal[i].departmentTujuanID = departmentTujuanID;
                                listStockAsal[i].departmentTujuanText = departmentTujuanText;
                                listStockAsal[i].warehouseTujuanID = warehouseTujuanID;
                                listStockAsal[i].warehouseTujuanText = warehouseTujuanText;
                                listStockAsal[i].qty2 = parseFloat(mutasiQty.toFixed(2));
                                listStockSelectedBahanJadi.push(listStockAsal[i]);
                                qtyMutasiFifo = parseFloat(qtyMutasiFifo) - mutasiQty;
                            }
                        }
                    } else if (listStockAsal[i].type_barang == "bahan_setengah_jadi") {
                        currentID = Number(currentID);
                        if ($.inArray(currentID, dataIds) == -1) {
                            var isIDSelected = $.grep(listStockSelectedBahanSetengahJadi, function(item) {
                                return item.id == Number(currentID);
                            }).length > 0;
                            if (!isIDSelected && parseFloat(qtyMutasiFifo) > 0 && parseFloat(listStockAsal[i].stok_total) > 0) {
                                var mutasiQty = Math.min(parseFloat(qtyMutasiFifo), parseFloat(listStockAsal[i].stok_total));
                                listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                                listStockAsal[i].qty = 0;
                                listStockAsal[i].qty_isi = 0;
                                listStockAsal[i].departmentID = departmentID;
                                listStockAsal[i].departmentText = departmentText;
                                listStockAsal[i].warehouseID = warehouseID;
                                listStockAsal[i].warehouseText = warehouseText;
                                listStockAsal[i].keterangan = keterangan;
                                listStockAsal[i].departmentTujuanID = departmentTujuanID;
                                listStockAsal[i].departmentTujuanText = departmentTujuanText;
                                listStockAsal[i].warehouseTujuanID = warehouseTujuanID;
                                listStockAsal[i].warehouseTujuanText = warehouseTujuanText;
                                listStockAsal[i].qty2 = parseFloat(mutasiQty.toFixed(2));
                                listStockSelectedBahanSetengahJadi.push(listStockAsal[i]);
                                qtyMutasiFifo = parseFloat(qtyMutasiFifo) - mutasiQty;
                            }
                        }
                    } else {
                        if ($.inArray(currentID, dataIds) == -1) {
                            currentID = Number(currentID);
                            var isIDSelected = $.grep(listStockSelectedBahan, function(item) {
                                return item.id == Number(currentID);
                            }).length > 0;
                            if (!isIDSelected && parseFloat(qtyMutasiFifo) > 0 && parseFloat(listStockAsal[i].stok_total) > 0) {
                                var mutasiQty = Math.min(parseFloat(qtyMutasiFifo), parseFloat(listStockAsal[i].stok_total));
                                listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                                listStockAsal[i].qty = 0;
                                listStockAsal[i].qty_isi = 0;
                                listStockAsal[i].departmentID = departmentID;
                                listStockAsal[i].departmentText = departmentText;
                                listStockAsal[i].warehouseID = warehouseID;
                                listStockAsal[i].warehouseText = warehouseText;
                                listStockAsal[i].keterangan = keterangan;
                                listStockAsal[i].departmentTujuanID = departmentTujuanID;
                                listStockAsal[i].departmentTujuanText = departmentTujuanText;
                                listStockAsal[i].warehouseTujuanID = warehouseTujuanID;
                                listStockAsal[i].warehouseTujuanText = warehouseTujuanText;
                                listStockAsal[i].qty2 = parseFloat(mutasiQty.toFixed(2));
                                listStockSelectedBahan.push(listStockAsal[i]);
                                qtyMutasiFifo = parseFloat(qtyMutasiFifo) - mutasiQty;
                            }
                        }
                    }
                });
            }
        }
        // console.log(listStockAsal);
        // console.log(listStockSelectedBahanBaku);

        drawTableSelectedItemBahanBaku(listStockSelectedBahanBaku);
        drawTableSelectedItemBahan(listStockSelectedBahan);
        drawTableSelectedItemBahanSetengahJadi(listStockSelectedBahanSetengahJadi);
        drawTableSelectedItemBahanJadi(listStockSelectedBahanJadi);
    }

    // function deleteByStockID(stockID) {
    //     listStockSelected = listStockSelected.filter(function(item) {
    //         return item.stock_id != stockID;
    //     });
    // }

    function drawTableSelectedItemBahanBaku(data) {
        // Hancurkan DataTable jika sudah ada
        if ($.fn.DataTable.isDataTable('#selectedItemTableBahanBaku')) {
            $('#selectedItemTableBahanBaku').DataTable().clear().draw();
            $('#selectedItemTableBahanBaku').DataTable().destroy();
        }

        const table = $('#selectedItemTableBahanBaku');
        table.find('tbody').empty(); // Kosongkan tbody sebelum menggambar ulang
        var no = 1;
        var totalQtyRequest = 0;

        // Loop melalui data dan buat baris tabel
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(`${no++}`));
            newRow.append($('<td style="text-align: center;">').text(v.departmentText + ' / ' + v.warehouseText));
            newRow.append($('<td style="text-align: center;">').text(v.departmentTujuanText + ' / ' + v.warehouseTujuanText));
            newRow.append($('<td style="text-align: center;">').text(v.supplier_name));
            newRow.append($('<td style="text-align: center;">').text(v.stock_dokumen));
            // newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            // newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            // newRow.append($('<td style="text-align: center;">').text(`${v.no_aju ? v.no_aju : '-'} / ${v.no_daftar ? v.no_daftar : '-'}`));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.realStok ?? v.stok_total));
            newRow.append($('<td style="text-align: center;">').html(`
                <input <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "readonly" : ""; ?> 
                    class="form-control qty-baku-request" 
                    oninput="preventNegativeInput(this)" 
                    autocomplete="one-time-code" 
                    data-id="${v.id}" 
                    data-stok_total="${v.stok_total}" 
                    data-index="${i}" 
                    type="text" 
                    value="${v.qty2}">
            `));
            newRow.append($('<td style="text-align: center;">').html(`
                <button <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "disabled" : ""; ?> 
                        type="button" 
                        class="btn btn-discard delete-btn btn-trash" 
                        onclick="deleteDetailBahanBaku('${v.id}', ${v.id_material_request_detail})">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>
            `));
            table.find('tbody').append(newRow);

            // Hitung total qty request
            totalQtyRequest += parseFloat(v.qty2) || 0; // Pastikan nilai adalah angka
        });

        // Inisialisasi DataTable
        selectedItemTableBahanBaku = $('#selectedItemTableBahanBaku').DataTable({
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            initComplete: function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");

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

        // Gambar ulang tabel
        selectedItemTableBahanBaku.draw();

        // Update total qty request
        updateTotalQtyRequest();

        // Gunakan event delegation untuk input qty-baku-request
        $(document).on('input change', '.qty-baku-request', function() {
            var index = $(this).data('index');
            var stok_max = $(this).data('stok_total');
            var input_user = $(this).val();

            // Update data di listStockSelectedBahanBaku
            listStockSelectedBahanBaku[index].qty2 = input_user;

            // Update total qty request
            updateTotalQtyRequest();
        });
    }

    function updateTotalQtyRequest() {
        var totalQty = 0;
        var totalQtyAwal = 0;
        // console.log(listStockSelectedBahanBaku);

        $.each(listStockSelectedBahanBaku, function(i, v) {
            totalQtyAwal += parseFloat(listStockSelectedBahanBaku[i].realStok ?? listStockSelectedBahanBaku[i].stok_total) || 0;
            totalQty += parseFloat(listStockSelectedBahanBaku[i].qty2) || 0;
        });
        $('.nilai-total-bahan-baku-request').text(greatFormatRupiah(totalQty.toFixed(2)));
        $('.nilai-total-bahan-baku-awal').text(greatFormatRupiah(totalQtyAwal.toFixed(2)));
    }

    function drawTableSelectedItemBahanSetengahJadi(data) {
        if ($.fn.DataTable.isDataTable('#selectedItemTableBahanSetengahJadi')) {
            $('#selectedItemTableBahanSetengahJadi').DataTable().clear().draw();
            selectedItemTableBahanSetengahJadi.destroy();
        }
        const table = $('#selectedItemTableBahanSetengahJadi');
        var no = 1;
        var totalQtyRequest = 0;
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
               ${no++} 
            `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.departmentText + ' / ' + v.warehouseText));
            newRow.append($('<td style="text-align: center;">').text(v.departmentTujuanText + ' / ' + v.warehouseTujuanText));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(`${v.no_aju ? v.no_aju : '-'} / ${v.no_daftar ? v.no_daftar : '-'}`));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <input <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "readonly" : ""; ?> class="form-control qty-bahan-request" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" data-stok_total="${v.stok_total}" data-index="${i}" class="form-control" type="text" value="${v.qty2}">
            `
            ));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <button <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "disabled" : ""; ?> type="button" class="btn btn-discard delete-btn btn-trash" onclick="deleteDetailBahanSetengahJadi(${v.id}, ${v.id_material_request_detail})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
            `
            ));
            table.find('tbody').append(newRow);
            parseFloat(v.qty2)
            totalQtyRequest += parseFloat(v.qty2) || 0;
        });

        selectedItemTableBahanSetengahJadi = $('#selectedItemTableBahanSetengahJadi').DataTable({

            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");

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

        selectedItemTableBahanSetengahJadi.draw();
        updateTotalQtyRequestSetengahJadi();

        // Tambahkan event listener untuk mengikuti perubahan nilai qty-barang-jadi
        $('.qty-bahan-request').on('input change', function() {
            var index = $(this).data('index'); // Dapatkan indeks item dari atribut data-index
            var stok_max = $(this).data('stok_total');
            var input_user = $(this).val();

            updateTotalQtyRequestSetengahJadi();

            listStockSelectedBahanSetengahJadi[index].qty2 = input_user;
        });
    }

    function updateTotalQtyRequestSetengahJadi() {
        var totalQtyAwal = 0;
        var totalQty = 0;
        $.each(listStockSelectedBahanSetengahJadi, function(i, v) {
            totalQtyAwal += parseFloat(listStockSelectedBahanSetengahJadi[i].stok_total) || 0;
            totalQty += parseFloat(listStockSelectedBahanSetengahJadi[i].qty2) || 0;
        });
        $('.nilai-total-barang-setengah-jadi-awal').text(greatFormatRupiah(totalQtyAwal));
        $('.nilai-total-barang-setengah-jadi-request').text(greatFormatRupiah(totalQty));
    }

    function drawTableSelectedItemBahan(data) {
        if ($.fn.DataTable.isDataTable('#selectedItemTableBahan')) {
            $('#selectedItemTableBahan').DataTable().clear().draw();
            selectedItemTableBahan.destroy();
        }
        const table = $('#selectedItemTableBahan');
        var no = 1;
        var totalQtyRequest = 0;
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
               ${no++} 
            `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.departmentText + ' / ' + v.warehouseText));
            newRow.append($('<td style="text-align: center;">').text(v.departmentTujuanText + ' / ' + v.warehouseTujuanText));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(`${v.no_aju ? v.no_aju : '-'} / ${v.no_daftar ? v.no_daftar : '-'}`));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <input <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "readonly" : ""; ?> class="form-control qty-bahan-request" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" data-stok_total="${v.stok_total}" data-index="${i}" class="form-control" type="text" value="${v.qty2}">
            `
            ));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <button <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "disabled" : ""; ?> type="button" class="btn btn-discard delete-btn btn-trash" onclick="deleteDetailBahan(${v.id}, ${v.id_material_request_detail})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
            `
            ));
            table.find('tbody').append(newRow);
            parseFloat(v.qty2)
            totalQtyRequest += parseFloat(v.qty2) || 0;
        });

        selectedItemTableBahan = $('#selectedItemTableBahan').DataTable({

            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");

            },
            display: "stripe",
            searching: false,
            paging: false,
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
        updateTotalQtyRequestScrap()

        // Tambahkan event listener untuk mengikuti perubahan nilai qty-barang-jadi
        $('.qty-bahan-request').on('input change', function() {
            var index = $(this).data('index'); // Dapatkan indeks item dari atribut data-index
            var stok_max = $(this).data('stok_total');
            var input_user = $(this).val();

            listStockSelectedBahan[index].qty2 = input_user;
            updateTotalQtyRequestScrap()
        });
    }

    function updateTotalQtyRequestScrap() {
        var totalQty = 0;
        var totalQtyAwal = 0;
        $.each(listStockSelectedBahan, function(i, v) {
            totalQtyAwal += parseFloat(listStockSelectedBahan[i].stok_total) || 0;
            totalQty += parseFloat(listStockSelectedBahan[i].qty2) || 0;
        });
        $('.nilai-total-barang-scrap-awal').text(greatFormatRupiah(totalQtyAwal));
        $('.nilai-total-barang-scrap-request').text(greatFormatRupiah(totalQty));
    }

    function drawTableSelectedItemBahanJadi(data) {
        if ($.fn.DataTable.isDataTable('#selectedItemTableBahanJadi')) {
            $('#selectedItemTableBahanJadi').DataTable().clear().draw();
            selectedItemTableBahanJadi.destroy();
        }
        const table = $('#selectedItemTableBahanJadi');
        var no = 1;
        var totalQtyRequest = 0;
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
               ${no++} 
            `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.departmentText + ' / ' + v.warehouseText));
            newRow.append($('<td style="text-align: center;">').text(v.departmentTujuanText + ' / ' + v.warehouseTujuanText));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(`${v.no_aju ? v.no_aju : '-'} / ${v.no_daftar ? v.no_daftar : '-'}`));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <input <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "readonly" : ""; ?> class="form-control qty-jadi-isi" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" data-stok_total="${v.stok_total}" data-index="${i}" class="form-control" type="text" value="${v.qty2}">
            `
            ));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <input <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "readonly" : ""; ?> class="form-control qty-jadi-request" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" data-stok_total="${v.stok_total}" class="form-control" type="text" value="${v.qty_isi}">
            `
            ));
            newRow.append($('<td style="text-align: center;">').html(
                `
                <button <?= (isset($dataMaterialRequests) && $dataMaterialRequests->is_posted == 1) ? "disabled" : ""; ?> type="button" class="btn btn-discard delete-btn btn-trash" onclick="deleteDetailBahanJadi(${v.id}, ${v.id_material_request_detail})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
            `
            ));
            table.find('tbody').append(newRow);
            parseFloat(v.qty2)
            totalQtyRequest += parseFloat(v.qty2) || 0;
        });

        selectedItemTableBahanJadi = $('#selectedItemTableBahanJadi').DataTable({

            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");

            },
            display: "stripe",
            searching: false,
            paging: false,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        selectedItemTableBahanJadi.draw();
        updateTotalQtyRequestJadi()

        // Tambahkan event listener untuk mengikuti perubahan nilai qty-barang-jadi
        $('.qty-jadi-isi, .qty-jadi-request').on('input change', function() {
            var index = $('.qty-jadi-isi').data('index'); // Dapatkan indeks item dari atribut data-index
            var stok_max = $('.qty-jadi-isi').data('stok_total');
            var input_user = $('.qty-jadi-isi').val();
            var input_user_request = $('.qty-jadi-request').val();

            listStockSelectedBahanJadi[index].qty2 = input_user;
            listStockSelectedBahanJadi[index].qty_isi = input_user_request;
            updateTotalQtyRequestJadi()
        });
    }

    function updateTotalQtyRequestJadi() {
        var totalQtyAwal = 0;
        var totalQty = 0;
        var totalQtyIsi = 0;
        // console.log(listStockSelectedBahanJadi);

        $.each(listStockSelectedBahanJadi, function(i, v) {
            totalQtyAwal += parseFloat(listStockSelectedBahanJadi[i].stok_total) || 0;
            totalQty += parseFloat(listStockSelectedBahanJadi[i].qty2) || 0;
            totalQtyIsi += parseFloat(listStockSelectedBahanJadi[i].qty_isi) || 0;
        });
        $('.nilai-total-barang-jadi-awal').text(greatFormatRupiah(totalQtyAwal));
        $('.nilai-total-barang-jadi-request').text(greatFormatRupiah(totalQty));
        $('.nilai-total-isi-barang-jadi-request').text(greatFormatRupiah(totalQtyIsi));
    }

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStockSelectedBahanBaku, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    function deleteDetailBahanBaku(id, iddetail) {
        if (id) {
            var indexToRemove = -1;
            for (var i = 0; i < listStockSelectedBahanBaku.length; i++) {
                if (listStockSelectedBahanBaku[i].id == id) {
                    indexToRemove = i;
                    break;
                }
            }
            if (indexToRemove !== -1) {
                listStockSelectedBahanBaku.splice(indexToRemove, 1);
                drawTableSelectedItemBahanBaku(listStockSelectedBahanBaku);
            }
        }
        if (iddetail) {
            // console.log(iddetail);
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di hapus?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("material-request/delete-detail"); ?>",
                        data: {
                            id: iddetail,
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
                                        location.reload();
                                    })
                            }
                        },
                    });
                }
            })
        }
    }

    function deleteDetailBahan(id, iddetail) {
        if (id) {
            var indexToRemove = -1;
            for (var i = 0; i < listStockSelectedBahan.length; i++) {
                if (listStockSelectedBahan[i].id == id) {
                    indexToRemove = i;
                    break;
                }
            }
            if (indexToRemove !== -1) {
                listStockSelectedBahan.splice(indexToRemove, 1);
                drawTableSelectedItemBahan(listStockSelectedBahan);
            }
        }
        if (iddetail) {
            // console.log(iddetail);
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di hapus?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("material-request/delete-detail"); ?>",
                        data: {
                            id: iddetail,
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
                                        location.reload();
                                    })
                            }
                        },
                    });
                }
            })
        }
    }

    function deleteDetailBahanSetengahJadi(id, iddetail) {
        if (id) {
            var indexToRemove = -1;
            for (var i = 0; i < listStockSelectedBahanSetengahJadi.length; i++) {
                if (listStockSelectedBahanSetengahJadi[i].id == id) {
                    indexToRemove = i;
                    break;
                }
            }
            if (indexToRemove !== -1) {
                listStockSelectedBahanSetengahJadi.splice(indexToRemove, 1);
                drawTableSelectedItemBahanSetengahJadi(listStockSelectedBahanSetengahJadi);
            }
        }
        if (iddetail) {
            // console.log(iddetail);
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di hapus?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("material-request/delete-detail"); ?>",
                        data: {
                            id: iddetail,
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
                                        location.reload();
                                    })
                            }
                        },
                    });
                }
            })
        }
    }

    function deleteDetailBahanJadi(id, iddetail) {
        if (id) {
            var indexToRemove = -1;
            for (var i = 0; i < listStockSelectedBahanJadi.length; i++) {
                if (listStockSelectedBahanJadi[i].id == id) {
                    indexToRemove = i;
                    break;
                }
            }
            if (indexToRemove !== -1) {
                listStockSelectedBahanJadi.splice(indexToRemove, 1);
                drawTableSelectedItemBahanJadi(listStockSelectedBahanJadi);
            }
        }
        if (iddetail) {
            // console.log(iddetail);
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di hapus?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("material-request/delete-detail"); ?>",
                        data: {
                            id: iddetail,
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
                                        location.reload();
                                    })
                            }
                        },
                    });
                }
            })
        }
    }

    // delete
    function handleDelete(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);

                setLoading()
                $.ajax({
                    url: "<?= base_url("material-request/delete"); ?>",
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
                                    window.location.href = "<?= base_url('material-request') ?>"
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
                            title: 'Data Gagal Dihapus, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                        stopLoading()
                    }
                });
            }
        })
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

    function print(url) {
        window.open(url);
    }

    // Tambahan Update Material Request
    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".req_no").attr("readonly", true);
            $(".req_no").val("AUTO GENERATE");
        } else {
            $(".req_no").attr("readonly", false);
            $(".req_no").val("");
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
        $('#warehouse_asal_bahan_baku_id').change();
    }

    function getListBarangBahanBaku() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('material-request/list-barang-stock-init-bahan-baku'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".type_barang option:selected").val(),
                divisi_id: $(".divisi_asal_bahan_baku_id option:selected").val(),
                warehouse_id: $(".warehouse_asal_bahan_baku_id option:selected").val(),
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

    function getListWarehouseAsalBahanBaku() {
        $.ajax({
            url: `<?= base_url('material-request/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_asal_bahan_baku_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_asal_bahan_baku_id").empty()
                $(".warehouse_asal_bahan_baku_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_asal_bahan_baku_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_asal_bahan_baku_id").val();
            }
        });
    }

    function getListWarehouseTujuanBahanBaku() {
        $.ajax({
            url: `<?= base_url('material-request/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_tujuan_bahan_baku_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_tujuan_bahan_baku_id").empty()
                $(".warehouse_tujuan_bahan_baku_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_tujuan_bahan_baku_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_tujuan_bahan_baku_id").val();
            }
        });
    }


    function getListDokumenPabeanBahanBaku() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('material-request/list-stock-dokumen-bc-bahan-baku'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                barang_master_id: $(".spesifikasi_id option:selected").data('barang_master_id'),
                stock_id: $(".spesifikasi_id option:selected").data('stock_id'),
                supplier_id: $(".supplier_id option:selected").val(),
                vendor_id: $('.vendor_barang_id option:selected').val(),
                divisi_asal_bahan_baku_id: $(".divisi_asal_bahan_baku_id option:selected").val(),
                warehouse_asal_bahan_baku_id: $(".warehouse_asal_bahan_baku_id option:selected").val(),
                type_asal_barang: $(".type_asal_barang option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                // LIST STOK PER BC
                listStockAsal = [];
                listStockAsal = res.data;
                console.log(res.data);

                drawTableAsalBarang(res.data);
            }
        });
    }

    function pindahTab(type) {
        if (type == "BAHAN BAKU") {
            // Clear Form Bahan Baku
            $('#type_pengambilan_stock_bahan_baku').val(null).change();
            $('#qty_keluar_fifo').val(null);
        } else {
            // Clear Form Bahan Lain
            $('#type_pengambilan_stock').val(null).change();
            $('#qty_mutasi_fifo').val(null);
        }

        // Clear Stok Asal
        listStockAsal = [];
        drawTableAsalBarang([]);
    }

    function updateBarangJadi() {
        let selected = $(".kode_produksi option:selected");

        if (selected.length > 0) {
            let nama_barang = [];
            let standart_production = [];

            selected.each(function() {
                nama_barang.push($(this).data("nama-barang"));
                standart_production.push($(this).data("standart-production"));
            });

            $(".barang_jadi").val(nama_barang.join(", "));
            $(".standart_production").val(standart_production.join(", "));
        } else {
            $(".barang_jadi").val("");
            $(".standart_production").val("");
        }
    }
</script>

<?= $this->endSection(); ?>
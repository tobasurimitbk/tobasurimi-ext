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
                    <div class="row mt-3">
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
                                            <option value="<?= $dataWO->id ?>" data-nama-barang="<?= $dataWO->nama_barang ?>" data-standart-production="<?= $dataWO->standart_production ?>" data-warehouse="<?= $dataWO->warehouse_id ?>" data-divisi="<?= $dataWO->divisi_id ?>"><?= $dataWO->wo_no ?> - <?= $dataWO->nama_barang ?></option>
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
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control standart_production" name="standart_production" id="standart_production" placeholder="Jumlah Standart Produksi" readonly>
                            <label for="floatingInput">Jumlah Standart Produksi</label>
                        </div>
                    </div> -->
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
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Data Material Request</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <?php if (!isset($data)) : ?>
                                <select class="form-select kode_request" name="kode_request[]" id="kode_request[]" multiple>
                                    <option value=""></option>
                                </select>
                            <?php endif; ?>
                            <?php if (isset($data)) : ?>
                                <input autocomplete="one-time-code" type="text" class="form-control req_no" name="req_no" id="req_no" placeholder="Kode Produksi" readonly>
                            <?php endif; ?>
                            <label for="floatingInput">Kode Request</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control date_picker" name="date_request" id="date_request" placeholder="Tanggal Request" readonly>
                            <label for="floatingInput">Tanggal Request</label>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control user_request" name="user_request" id="user_request" placeholder="User Request" readonly>
                            <label for="floatingInput">User Request</label>
                        </div>
                    </div> -->
                </div>
                <!-- <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select department_id_request" name="department_id_request" id="department_id_request" disabled>
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
                            <select class="form-select warehouse_id_request" name="warehouse_id_request" id="warehouse_id_request" disabled>
                                <option value=""></option>
                                <?php foreach ($dataWarehouse ?? [] as $Warehouse) : ?>
                                    <option value="<?= $Warehouse['id'] ?>"><?= $Warehouse['warehouse_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Warehouse</label>
                        </div>
                    </div>
                </div> -->
                <!-- details -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-jadi" type="button" role="tab" aria-controls="nav-barang-jadi" aria-selected="true">Barang Jadi</button>
                                <!-- <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-setengah-jadi" type="button" role="tab" aria-controls="nav-barang-setengah-jadi" aria-selected="false">Barang Setengah Jadi</button> -->
                                <button class="nav-link" id="nav-scrap-tab" data-bs-toggle="tab" data-bs-target="#nav-scrap" type="button" role="tab" aria-controls="nav-scrap" aria-selected="false">Scrap</button>
                                <button class="nav-link" id="nav-filling-tab" data-bs-toggle="tab" data-bs-target="#nav-filling" type="button" role="tab" aria-controls="nav-filling" aria-selected="false">Filling</button>
                            </div>
                        </nav>
                        <div class="tab-content mt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-barang-jadi" role="tabpanel" aria-labelledby="nav-home-tab">
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
                                            <table class="table table-bordered nowrap table-hover-tobasurimi tableBarangDigunakan text-center" id="tableBarangDigunakan" width="100%" cellspacing="0">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width: 10px;">No</th>
                                                        <th>Referensi</th>
                                                        <?php if (!isset($data)) : ?>
                                                            <th>Kondisi Barang</th>
                                                        <?php endif; ?>
                                                        <th>Kode Barang</th>
                                                        <th>Jenis Barang</th>
                                                        <th>Nama Barang</th>
                                                        <th>Satuan</th>
                                                        <th>Sisa Qty Request</th>
                                                        <th>Jumlah Digunakan</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="body-table-barang-digunakan" id="body-table-barang-digunakan">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-subtitle-modal">
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="form-label font-weight-bold modal-sub-title">Daftar Barang Jadi</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
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
                                                        <th>Berat Isi</th>
                                                        <th>Qty dalam KG</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="body-table-barang-jadi" id="body-table-barang-jadi">
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
                                                    <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" class="form-control qty_scrap" name="qty_scrap" id="qty_scrap" placeholder="Qty">
                                                    <label for="floatingInput">Qty</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <select class="form-select department_id_scrap" name="department_id_scrap" id="department_id_scrap" aria-label="Floating label select example">
                                                        <option value=""></option>
                                                        <?php foreach ($dataDivisi ?? [] as $dataDivisis) : ?>
                                                            <option value="<?= $dataDivisis['id'] ?>" data-department-name="<?= $dataDivisis['divisi'] ?>"><?= $dataDivisis['divisi'] ?></option>
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
                            <div class="tab-pane fade" id="nav-filling" role="tabpanel" aria-labelledby="nav-filling-tab">
                                <?php if (!isset($data)) : ?>
                                    <form class="formBarangFilling" id="formBarangFilling">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <input autocomplete="one-time-code" type="hidden" class="kode" name="kode" id="kode" />
                                                    <select class="form-select kode_barang_filling" name="kode_barang_filling" id="kode_barang_filling" aria-label="Floating label select example">
                                                        <option data-barang_id="" data-nama="" data-satuan="" value=""></option>
                                                    </select>
                                                    <label for="floatingInput">Kode Barang</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" class="form-control qty_filling" name="qty_filling" id="qty_filling" placeholder="Qty">
                                                    <label for="floatingInput">Qty</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <select class="form-select kondisi_barang" name="kondisi_barang" id="kondisi_barang">
                                                        <option value=""></option>
                                                        <option value="ditapak">Ditapak</option>
                                                        <option value="filling">Filling</option>
                                                    </select>
                                                    <label for="floatingInput">Kondisi Barang</label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <select class="form-select department_id_filling" name="department_id_filling" id="department_id_filling" aria-label="Floating label select example">
                                                        <option value=""></option>
                                                        <?php foreach ($dataDivisi ?? [] as $dataDivisis) : ?>
                                                            <option value="<?= $dataDivisis['id'] ?>" data-department-name="<?= $dataDivisis['divisi'] ?>"><?= $dataDivisis['divisi'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="floatingInput">Department</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <select class="form-select warehouse_id_filling" name="warehouse_id_filling" id="warehouse_id_filling" disabled>
                                                        <option value=""></option>
                                                    </select>
                                                    <label for="floatingInput">Warehouse</label>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <button type="button" class="btn btn-primary button-add-filling">Tambah Barang Filling</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                                <div class="table-responsive">
                                    <table class="table nowrap table-hover-tobasurimi tableBarangFilling" id="tableBarangFilling" width="100%" cellspacing="0">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th>No.</th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Kondisi</th>
                                                <th>Jumlah</th>
                                                <?php if (!isset($data)) : ?>
                                                    <th>Action</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table-barang-filling" id="body-table-barang-filling" style="cursor: pointer;">

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

    let list_items_barang_jadi = [];
    let list_items_barang_digunakan = [];
    let list_items_barang_scrap = [];
    let list_items_barang_filling = [];

    $(document).ready(function() {
        <?php if (isset($data)) : ?>
            $(".kode_produksi").val('<?= $data->work_order_id ?>').change();
            $(".wo_no").val('<?= $dataWorkOrder[0]->wo_no ?>').change();
            $(".barang_jadi").val('<?= $dataWorkOrder[0]->nama_barang ?>');
            $(".standart_production").val('<?= $dataWorkOrder[0]->standart_production ?>');
            $(".department_id_order").val('<?= $dataWorkOrder[0]->divisi_id ?>');
            $(".warehouse_id_order").val('<?= $dataWorkOrder[0]->warehouse_id ?>');
            $(".req_no").val('<?= $dataMaterialRequestNo ?>');
            $("#date_request").val('<?= $dataMaterialRequestDate ?>');
            list_items_barang_jadi = [];
            list_items_barang_digunakan = [];
            list_items_barang_scrap = [];
            <?php foreach ($dataResultBarangJadi as $key => $bj) : ?>
                list_items_barang_jadi.push({
                    'barang_detail_id': getID(),
                    'production_result_detail_id': '<?= $bj->id; ?>',
                    'barang1_id': '<?= $bj->barang1_id; ?>',
                    'barang2_id': '<?= $bj->barang2_id; ?>',
                    'barang_name': '<?= $bj->barang_name; ?>',
                    'kode_barang': '<?= $bj->kode_barang; ?>',
                    'kode_satuan': '<?= $bj->kode_satuan; ?>',
                    'nama_barang': '<?= $bj->nama_barang; ?>',
                    'qty': '<?= $bj->qty; ?>',
                    'qty2': '<?= $bj->qty2; ?>',
                    'qty_isi': '<?= $bj->qty_isi; ?>',
                    'type_barang': '<?= $bj->barang_type; ?>',
                    'type_barang_text': '<?= $bj->type_barang_text; ?>',
                });
            <?php endforeach; ?>
            drawTableBarangJadi();
            <?php foreach ($dataResultBarangScrap as $key => $bs) : ?>
                list_items_barang_scrap.push({
                    'barang_detail_id': getID(),
                    'production_result_detail_id': '<?= $bs->id; ?>',
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
                    'production_result_detail_id': '<?= $bd->id; ?>',
                    'barang1_id': '<?= $bd->barang1_id; ?>',
                    'barang2_id': '<?= $bd->barang2_id; ?>',
                    'barang_name': '<?= $bd->barang_name; ?>',
                    'kode_barang': '<?= $bd->kode_barang; ?>',
                    'satuan': '<?= $bd->kode_satuan; ?>',
                    'nama_barang': '<?= $bd->nama_barang; ?>',
                    'qty': '<?= $bd->qty; ?>',
                    'ref_no': '<?= $bd->no_ref; ?>',
                    'no_aju': '<?= $bd->no_aju; ?>',
                    'type_barang': '<?= $bd->barang_type; ?>',
                    'type_barang_text': '<?= $bd->type_barang_text; ?>',
                });
            <?php endforeach; ?>
            drawTableBarangDigunakan();
        <?php endif; ?>
        // Departemen
        $('.kondisi_barang').select2({
            placeholder: "Pilih Kondisi Barang",
            theme: "bootstrap-5"
        });

        // Departemen
        $('.department_id_scrap, .department_id_filling').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        });

        //CSS SELECT2 FLOATING LABEL
        $('.department_id_scrap, .department_id_filling, .kondisi_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.department_id_scrap, .department_id_filling, .kondisi_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.department_id_scrap, .department_id_filling, .kondisi_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PILIH TIPE Warehouse
        $('.warehouse_id_scrap, .warehouse_id_filling').select2({
            placeholder: "Pilih Warehouse",
            theme: "bootstrap-5"
        });

        $('.warehouse_id_scrap, .warehouse_id_filling')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.warehouse_id_scrap, .warehouse_id_filling')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.warehouse_id_scrap, .warehouse_id_filling')
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
        $('.kode_barang, .kode_barang_scrap, .kode_barang_filling').select2({
            placeholder: "Pilih Kode Barang",
            theme: "bootstrap-5",
            tags: false,
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kode_barang, .kode_barang_scrap, .kode_barang_filling')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_barang, .kode_barang_scrap, .kode_barang_filling')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_barang, .kode_barang_scrap, .kode_barang_filling')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.satuan_id, #kode_produksi').select2({
            placeholder: "",
            theme: "bootstrap-5",
        })

        //CSS SELECT2 FLOATING LABEL
        $('.satuan_id, #kode_produksi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.satuan_id, #kode_produksi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan_id, #kode_produksi')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SO
        $('.kode_request').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kode_request')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_request')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_request')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.kode_request')
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
            console.log(list_items_barang_digunakan);
            console.log(list_items_barang_jadi);
            console.log(list_items_barang_scrap);
            console.log(list_items_barang_filling);
            var listMaterialCheck = [].concat(list_items_barang_digunakan, list_items_barang_jadi, list_items_barang_filling);

            console.log(listMaterialCheck);
            if (listMaterialCheck.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang yang akan direquest tidak boleh kosong !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
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
                            data.append("filling", JSON.stringify(list_items_barang_filling));

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
                            $('.kode_request').empty();
                            // Append a default option
                            $('.kode_request').append(`<option value=""></option>`);
                            // Iterate over each item in the response data
                            res.data.forEach(function(item) {
                                // Append an option for each item
                                $('.kode_request').append(`<option  value="${item.id}" data-tanggal-request="${item.request_date}"  data-user-request="${item.user_name}" data-warehouse-request="${item.warehouse_id}" data-divisi-request="${item.divisi_id}">${item.req_no}</option>`);
                            });
                        } else {
                            stopLoading()
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Material Request Tidak Ada',
                                confirmButtonColor: '#4e73df',
                            })
                            // Clear existing options
                            $('.kode_request').empty();
                            // Append a default option
                            $('.kode_request').append(`<option value=""></option>`);
                            $('.date_request').val();
                            list_items_barang_jadi = [];
                            list_items_barang_digunakan = [];
                            drawTableBarangJadi();
                            drawTableBarangDigunakan();
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
                        res.data.forEach(function(item) {
                            // Push each item into the list_items_barang_jadi array
                            list_items_barang_jadi.push({
                                'barang_detail_id': getID(),
                                'detail_work_order': item.id,
                                'barang1_id': item.barang1_id,
                                'barang2_id': item.barang2_id,
                                'barang_name': item.barang_name + " - " + item.spesifikasi,
                                'kode_barang': item.kode_barang,
                                'kode_satuan': item.kode_satuan,
                                'nama_barang': item.nama_barang,
                                'warehouse_id': item.warehouse_id,
                                'divisi_id': item.divisi_id,
                                'note': item.note,
                                'qty': 0,
                                'qty2': 0,
                                'qty_isi': 0,
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
                let user_request = $(".kode_request option:select ed").data("user-request") ? $(".kode_request option:selected").data("user-request") : "";
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

                        $(".kode_barang_filling").empty();
                        $(".kode_barang_filling").append(`<option data-divisi_id="" data-warehouse_id="" data-barang_name_master="" data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
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
                                'material_request_detail_id': item.id,
                                'material_request_id': item.material_request_id,
                                'bc_id': item.bc_id,
                                'stock_id': item.stock_tujuan_id,
                                'stock_date': item.stock_date,
                                'stock_dokumen': item.stock_dokumen,
                                'barang1_id': item.barang1_id,
                                'barang2_id': item.barang2_id,
                                'kode_barang': item.kode_barang,
                                'satuan': item.satuan,
                                'nama_barang': item.nama_barang,
                                'note': item.note,
                                'qty': item.qty_now,
                                'ref_no': new_ref_no,
                                'no_aju': item.no_aju,
                                'type_barang': item.type_barang,
                                'type_barang_text': item.type_barang_text,
                                'unit': item.unit,
                                'warehouse_id': item.warehouse_tujuan_id,
                                'divisi_id': item.divisi_tujuan_id,
                                'kondisi_barang': item.kondisi_barang,
                            });
                            $(".kode_barang_filling").append(`<option 
                            data-material_request_detail_id="${item.id}" 
                            data-material_request_id="${item.material_request_id}" 
                            data-bc_id="${item.bc_id}" 
                            data-stock_id="${item.stock_tujuan_id}" 
                            data-stock_date="${item.stock_date}" 
                            data-stock_dokumen="${item.stock_dokumen}" 
                            data-barang1_id="${item.barang1_id}" 
                            data-barang2_id="${item.barang2_id}" 
                            data-kode_barang="${item.kode_barang}" 
                            data-satuan="${item.satuan}" 
                            data-nama_barang="${item.nama_barang}" 
                            data-note="${item.note}" 
                            data-qty="${item.qty_now}" 
                            data-ref_no="${new_ref_no}" 
                            data-no_aju="${item.no_aju}" 
                            data-type_barang="${item.type_barang}" 
                            data-type_barang_text="${item.type_barang_text}" 
                            data-unit="${item.unit}" 
                            data-warehouse_id="${item.warehouse_tujuan_id}" 
                            data-divisi_id="${item.divisi_tujuan_id}" 
                            value="${item.kode_barang}">(${item.kode_barang}) ${item.nama_barang}</option>`);
                        });
                        $(".kode_barang_filling").val("").change();
                        drawTableBarangDigunakan();
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
            if (departmentId) {
                setLoading();
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
                        stopLoading();
                    }
                })
            }
        })

        $('#department_id_filling').on('change', function() {
            var departmentId = $(this).val();
            if (departmentId) {
                setLoading();
                $.ajax({
                    url: `<?= base_url("warehouse/dropdown/divisi/"); ?>/${departmentId}`,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        $("#warehouse_id_filling").empty();
                        $("#warehouse_id_filling").append(`<option value=""></option>`);
                        res.data.forEach(function(item) {
                            $("#warehouse_id_filling").append(`<option value="${item.id}" data-warehouse-name="${item.warehouse_name}">${item.warehouse_name}</option>`);
                        })
                        $("#warehouse_id_filling").prop('disabled', false);
                        stopLoading();
                    }
                })
            }
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

        // Event click untuk tombol "Tambah Barang filling"
        $(".button-add-filling").click(function() {
            // Menghapus pesan kesalahan sebelumnya
            $('.error-message').remove();

            // Mengambil nilai dari setiap input
            let kodeBarang = $(".kode_barang_filling option:selected").val();
            let qtyBarang = $(".qty_filling").val();
            let kondisiBarang = $(".kondisi_barang option:selected").val();
            let kondisiBarangText = $(".kondisi_barang option:selected").text();

            // Memeriksa apakah semua input terisi
            if (kodeBarang && qtyBarang && kondisiBarang) {
                // Jika semua input terisi, tambahkan barang filling
                let barang = $(".kode_barang_filling option:selected").val();

                let material_request_detail_id = $(".kode_barang_filling option:selected").data("material_request_detail_id");
                let material_request_id = $(".kode_barang_filling option:selected").data("material_request_id");
                let bc_id = $(".kode_barang_filling option:selected").data("bc_id");
                let stock_id = $(".kode_barang_filling option:selected").data("stock_id");
                let stock_date = $(".kode_barang_filling option:selected").data("stock_date");
                let stock_dokumen = $(".kode_barang_filling option:selected").data("stock_dokumen");
                let barang1_id = $(".kode_barang_filling option:selected").data("barang1_id");
                let barang2_id = $(".kode_barang_filling option:selected").data("barang2_id");
                let kode_barang = $(".kode_barang_filling option:selected").data("kode_barang");
                let satuan = $(".kode_barang_filling option:selected").data("satuan");
                let nama_barang = $(".kode_barang_filling option:selected").data("nama_barang");
                let note = $(".kode_barang_filling option:selected").data("note");
                let qty = $(".kode_barang_filling option:selected").data("qty");
                let ref_no = $(".kode_barang_filling option:selected").data("ref_no");
                let no_aju = $(".kode_barang_filling option:selected").data("no_aju");
                let type_barang = $(".kode_barang_filling option:selected").data("type_barang");
                let type_barang_text = $(".kode_barang_filling option:selected").data("type_barang_text");
                let unit = $(".kode_barang_filling option:selected").data("unit");
                let warehouse_id = $(".kode_barang_filling option:selected").data("warehouse_id");
                let divisi_id = $(".kode_barang_filling option:selected").data("divisi_id");

                list_items_barang_filling.push({
                    'barang_detail_id': getID(),
                    'material_request_detail_id': material_request_detail_id,
                    'material_request_id': material_request_id,
                    'bc_id': bc_id,
                    'stock_id': stock_id,
                    'stock_date': stock_date,
                    'stock_dokumen': stock_dokumen,
                    'barang1_id': barang1_id,
                    'barang2_id': barang2_id,
                    'kode_barang': kode_barang,
                    'satuan': satuan,
                    'nama_barang': nama_barang,
                    'note': note,
                    'qty': qtyBarang,
                    'ref_no': ref_no,
                    'no_aju': no_aju,
                    'type_barang': type_barang,
                    'type_barang_text': type_barang_text,
                    'unit': unit,
                    'warehouse_id': warehouse_id,
                    'divisi_id': divisi_id,
                    'kondisi_barang': kondisiBarang,
                    'kondisi_barang_text': kondisiBarangText,
                });

                drawTableBarangFilling(); // Menggambar tabel
                resetFormDetailFilling(); // Mengatur ulang form
            } else {
                // Menampilkan pesan kesalahan di bawah input yang kosong
                if (!kodeBarang) $(".kode_barang_filling").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih kode barang.</span>');
                if (!qtyBarang) $(".qty_filling").closest('.form-floating').append('<span class="error-message text-danger">Harap masukkan jumlah barang.</span>');
                if (!kondisiBarang) $(".kondisi_barang").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih kondisi barang.</span>');
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
        <input class="form-control qty-barang-jadi" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" value="${item.qty}" <?= isset($data) && $data->is_posted == 1 ? "readonly" : ""; ?>>` +
                    '</td>';
                row += '<td>' + `
        <input class="form-control berat-barang-jadi" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" value="${item.qty2}" <?= isset($data) && $data->is_posted == 1 ? "readonly" : ""; ?>>` +
                    '</td>';
                row += '<td>' + `
        <input class="form-control qty-berat-barang-jadi" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" value="${item.qty_isi}" <?= isset($data) && $data->is_posted == 1 ? "readonly" : ""; ?>>` +
                    '</td>';

                no++;
            });
            $('.body-table-barang-jadi').append(row);
        }

        $('.qty-barang-jadi, .berat-barang-jadi').on('input change', function() {
            var index = $(this).data('index');
            var valueQtyBarangJadi = $('input.qty-barang-jadi[data-index="' + index + '"]').val();
            var valueBeratBarangJadi = $('input.berat-barang-jadi[data-index="' + index + '"]').val();
            var newValue = $(this).val();


            var jumlahQtyBeratJadi = parseFloat(valueQtyBarangJadi) * parseFloat(valueBeratBarangJadi);

            $('input.qty-berat-barang-jadi[data-index="' + index + '"]').val(jumlahQtyBeratJadi);
            list_items_barang_jadi[index].qty_jadi = valueQtyBarangJadi;
            list_items_barang_jadi[index].berat_isi_jadi = valueBeratBarangJadi;
            list_items_barang_jadi[index].qty_isi_jadi = jumlahQtyBeratJadi.toFixed(2);
        });
    }

    const drawTableBarangDigunakan = function() {
        $('.body-table-barang-digunakan').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items_barang_digunakan.length === 0) {
            row += `
                    <tr>
                        <td colspan="7" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.tfoot').append(row);
        } else {
            list_items_barang_digunakan.map((item, index) => {
                var qty = item.qty2 ? item.qty2 : item.qty;
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.ref_no + '</td>';
                <?php if (!isset($data)) : ?>
                    row += '<td>' + item.kondisi_barang.toUpperCase() + '</td>';
                <?php endif; ?>
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.type_barang_text + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + item.satuan + '</td>';
                row += '<td>' + item.qty + '</td>';
                row += '<td>' + `
                <input class="form-control qty-barang-digunakan" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" value="${qty}">` +
                    '</td>';

                no++;
            });
            $('.body-table-barang-digunakan').append(row);
        }

        // Tambahkan event listener untuk mengikuti perubahan nilai qty-barang-jadi
        $('.qty-barang-digunakan').on('input change', function() {
            var index = $(this).data('index'); // Dapatkan indeks item dari atribut data-index
            var newValue = $(this).val(); // Dapatkan nilai yang dimasukkan pengguna
            list_items_barang_digunakan[index].qty2 = newValue; // Simpan nilai ke dalam list_items_barang_jadi
        });
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

    const drawTableBarangFilling = function() {
        $('.body-table-barang-filling').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items_barang_filling.length === 0) {
            row += `
                <tr>
                    <td colspan="5" class="text-center">Data Barang Tidak Ada</td>
                </tr>
            `;
            $('.tfoot').append(row);
        } else {
            list_items_barang_filling.map(item => {
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + item.kondisi_barang_text + '</td>';
                row += '<td>' + item.qty + '</td>';
                <?php if (!isset($data)) : ?>
                    row += '<td>' + `
            <button type="button" class="btn btn-danger" onclick="deleteRowDetailFilling('${item.barang_detail_id}')">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>` +
                        '</td>';
                <?php endif; ?>

                no++;
            });
            $('.body-table-barang-filling').append(row);
        }
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

    const deleteRowDetailFilling = function(id) {
        const indexToRemove = list_items_barang_filling.findIndex(item => item.barang_detail_id === id);
        if (indexToRemove !== -1) {
            list_items_barang_filling.splice(indexToRemove, 1);
        }
        drawTableBarangFilling();
    }
    const resetFormDetailFilling = function() {
        $(".kode_barang_filling").val('').change()
        $(".qty_filling").val('')
        $(".kondisi_barang").val('').change()
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
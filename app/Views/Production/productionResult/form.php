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
        <div class="col-button-tambah-spp text-right">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("production-result"); ?>">
                Kembali
            </a>
            <?php if (isset($data)) { ?>
                <?php if ($data->is_posted != 1) { ?>
                    <?php if (can('Produksi', 'Hasil Produksi', 'a')) : ?>
                        <button class="btn btn-success mr-1" onclick="posting('<?= !empty($data) ? encrypt($data->id) : ''; ?>', 1)">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Produksi', 'Hasil Produksi', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="handleDelete('<?= !empty($data) ? encrypt($data->id) : ''; ?>', 1)">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <button class="btn btn-show-form btn-save btn-submit-form mr-1">
                        Simpan
                    </button>
                <?php } ?>
            <?php } else if (!isset($data)) { ?>
                <button class="btn btn-show-form btn-save btn-submit-form mr-1" type="button">
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
                <div class="row mt-3">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Bukti Penerimaan</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control res_no" id="res_no" name="res_no" placeholder="Kode Penerimaan" value="<?= isset($data) ? $data->pr_no : ""; ?>" <?= isset($data) ? "readonly" : ""; ?>>
                                    <label for="floatingInput">Kode Penerimaan</label>
                                </div>
                                <div style="<?= !empty($data) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 0px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
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
                <div class="row mt-3">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Work Order</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kode_produksi" name="kode_produksi[]" id="kode_produksi" multiple>
                                <option value=""></option>
                                <?php
                                $selectedIds = isset($data) ? explode(',', $data->work_order_id) : [];
                                ?>
                                <?php if (isset($dataWorkOrder)) : ?>
                                    <?php foreach ($dataWorkOrder ?? [] as $dataWO) : ?>
                                        <option value="<?= $dataWO->id ?>"
                                            data-nama-barang="<?= $dataWO->nama_barang ?>"
                                            data-standart-production="<?= $dataWO->standart_production ?>"
                                            data-warehouse="<?= $dataWO->warehouse_id ?>"
                                            data-divisi="<?= $dataWO->divisi_id ?>"
                                            <?= in_array($dataWO->id, $selectedIds) ? 'selected' : '' ?>>
                                            <?= $dataWO->wo_no ?> - <?= $dataWO->nama_barang ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Kode Work Order</label>
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
                <div class="row mt-3">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Hasil Produksi</label>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-jadi" type="button" role="tab" aria-controls="nav-barang-jadi" aria-selected="true">Barang Jadi</button>
                                <button class="nav-link" id="nav-material-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-material-request" type="button" role="tab" aria-controls="nav-barang-material-request" aria-selected="false">Barang Material Request</button>
                                <button class="nav-link" id="nav-material-penolong-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-material-request-penolong" type="button" role="tab" aria-controls="nav-barang-material-request-penolong" aria-selected="false">Barang Material Request Penolong</button>
                                <button class="nav-link" id="nav-scrap-tab" data-bs-toggle="tab" data-bs-target="#nav-scrap" type="button" role="tab" aria-controls="nav-scrap" aria-selected="false">Scrap</button>
                                <button class="nav-link" id="nav-filling-tab" data-bs-toggle="tab" data-bs-target="#nav-filling" type="button" role="tab" aria-controls="nav-filling" aria-selected="false">Sisa Produksi</button>
                                <button class="nav-link" id="nav-susut-tab" data-bs-toggle="tab" data-bs-target="#nav-susut" type="button" role="tab" aria-controls="nav-susut" aria-selected="false">Susut Masak</button>
                                <!-- <button class="nav-link" id="nav-barang-jadi-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-jadi" type="button" role="tab" aria-controls="nav-barang-setengah-jadi" aria-selected="false">Barang Setengah Jadi</button> -->
                            </div>
                        </nav>
                        <div class="tab-content mt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-barang-jadi" role="tabpanel" aria-labelledby="nav-barang-jadi">
                                <?php if (!isset($data) || (isset($data) && $data->is_posted != 1)) : ?>
                                    <button type="button" class="btn btn-primary btn-add-barang-jadi" style="float: right;">Tambah Barang Jadi</button>
                                <?php endif; ?>
                                <br> <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered nowrap table-hover-tobasurimi tableBarangJadi dataTable" id="tableBarangJadi" width="100%" cellspacing="0">
                                                <thead class="thead-dark text-center">
                                                    <tr>
                                                        <th style="width: 10px;">No</th>
                                                        <th>Kode Barang</th>
                                                        <th>Jenis Barang</th>
                                                        <th style="text-align:center">Nama Barang</th>
                                                        <th>Satuan</th>
                                                        <th>Qty Hasil</th>
                                                        <th>Berat Isi</th>
                                                        <th>Qty dalam KG</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="body-table-barang-jadi" id="body-table-barang-jadi">
                                                </tbody>
                                                <tfoot class="tfoot-table-barang-jadi" id="tfoot-barang-jadi">
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-scrap" role="tabpanel" aria-labelledby="nav-contact-tab">
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
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary button-add-scrap" style="float: right;">Tambah Barang Scrap</button>
                                            </div>
                                        </div>
                                    </form>
                                <div class="table-responsive">
                                    <table class="table nowrap table-hover-tobasurimi tableBarangScrap dataTable" id="tableBarangScrap" width="100%" cellspacing="0">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th>No.</th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Department</th>
                                                <th>Warehouse</th>
                                                <th>Jumlah</th>
                                                    <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table-barang-scrap" id="body-table-barang-scrap" style="cursor: pointer;">

                                        </tbody>
                                        <tfoot class="tfoot-table-barang-scrap" id="tfoot-barang-scrap">
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-filling" role="tabpanel" aria-labelledby="nav-filling-tab">
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
                                                        <option value="ditapak" selected>Ditapak</option>
                                                        <option value="filling">Filling</option>
                                                        <option value="frozen">FR40</option>
                                                        <option value="canning">Canning</option>
                                                        <option value="rnd">RnD</option>
                                                    </select>
                                                    <label for="floatingInput">Kondisi Barang</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
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
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary button-add-filling" style="float: right;">Tambah Barang Filling</button>
                                            </div>
                                        </div>
                                    </form>
                                <div class="table-responsive">
                                    <table class="table nowrap table-hover-tobasurimi tableBarangFilling dataTable" id="tableBarangFilling" width="100%" cellspacing="0">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th>No.</th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Kondisi</th>
                                                <th>Jumlah</th>
                                                    <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table-barang-filling" id="body-table-barang-filling" style="cursor: pointer;">

                                        </tbody>
                                        <tfoot class="tfoot-table-barang-filling" id="tfoot-barang-filling">
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-susut" role="tabpanel" aria-labelledby="nav-susut-tab">
                                <form class="formBarangSusut" id="formBarangSusut">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-floating mb-3" style="height: 50px;">
                                                <input autocomplete="one-time-code" type="hidden" class="kode" name="kode" id="kode" />
                                                <select class="form-select kode_barang_susut" name="kode_barang_susut" id="kode_barang_susut" aria-label="Floating label select example">
                                                    <option data-barang_id="" data-nama="" data-satuan="" value=""></option>
                                                </select>
                                                <label for="floatingInput">Kode Barang</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating mb-3" style="height: 50px;">
                                                <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" class="form-control qty_susut" name="qty_susut" id="qty_susut" placeholder="Qty">
                                                <label for="floatingInput">Qty</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <button type="button" class="btn btn-primary button-add-susut" style="float: right;">Tambah Barang Susut</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table nowrap table-hover-tobasurimi tableBarangSusut dataTable" id="tableBarangSusut" width="100%" cellspacing="0">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th>No.</th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Jumlah</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table-barang-susut" id="body-table-barang-susut" style="cursor: pointer;">

                                        </tbody>
                                        <tfoot class="tfoot-table-barang-susut" id="tfoot-barang-susut">
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-barang-material-request" role="tabpanel" aria-labelledby="nav-barang-material-request">
                                <!-- <?php if (!isset($data) || (isset($data) && $data->is_posted != 1)) : ?>
                                    <button type="button" class="btn btn-primary btn-add-barang-ditapak" style="float: right; margin-top: 35px;">Tambah Barang Ditapak</button>
                                <?php endif; ?> -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select kode_request" name="kode_request[]" id="kode_request[]" multiple>
                                                <option value=""></option>
                                            </select>
                                            <label for="floatingInput">Kode Request</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control date_picker" name="date_request" id="date_request" placeholder="Tanggal Request" readonly>
                                            <label for="floatingInput">Tanggal Request</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered nowrap table-hover-tobasurimi tableBarangDigunakan text-center dataTable" id="tableBarangDigunakan" width="100%" cellspacing="0">
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
                                                <tfoot id="tfoot-barang-digunakan">
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-barang-material-request-penolong" role="tabpanel" aria-labelledby="nav-barang-material-request">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select kode_request_penolong" name="kode_request_penolong[]" id="kode_request_penolong[]" multiple>
                                                <option value=""></option>
                                            </select>
                                            <label for="floatingInput">Kode Request</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control date_picker" name="date_request_penolong" id="date_request_penolong" placeholder="Tanggal Request" readonly>
                                            <label for="floatingInput">Tanggal Request</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered nowrap table-hover-tobasurimi tableBarangDigunakanPenolong text-center dataTable" id="tableBarangDigunakanPenolong" width="100%" cellspacing="0">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="width: 10px;">No</th>
                                                        <th>Referensi</th>
                                                        <th>Kode Barang</th>
                                                        <th>Jenis Barang</th>
                                                        <th>Nama Barang</th>
                                                        <th>Satuan</th>
                                                        <th>Sisa Qty Request</th>
                                                        <th>Jumlah Digunakan</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="body-table-barang-digunakan-penolong" id="body-table-barang-digunakan-penolong">
                                                </tbody>
                                                <tfoot id="tfoot-barang-digunakan-penolong">
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
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

<div class="modal" id="add_barang_produksi" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang Hasil Produksi</h5>
            </div>
            <div class="modal-body">
                <form class="form-excel" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="hidden" class="id_barang_hasil" name="id_barang_hasil" id="id_barang_hasil" />
                                <select class="form-select kode_barang_add" name="kode_barang_add" id="kode_barang_add" aria-label="Floating label select example">
                                    <option data-barang_id="" data-nama="" data-satuan="" value=""></option>
                                </select>
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly class="form-control satuan_barang_add" name="satuan_barang_add" id="satuan_barang_add" placeholder="Satuan">
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" class="form-control qty_barang_add" name="qty_barang_add" id="qty_barang_add" placeholder="Qty Hasil">
                                <label for="floatingInput">Qty Saat Ini</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" class="form-control kg_barang_add" name="kg_barang_add" id="kg_barang_add" placeholder="Berat Isi">
                                <label for="floatingInput">Berat Isi</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" readonly class="form-control qty_kg_barang_add" name="qty_kg_barang_add" id="qty_kg_barang_add" placeholder="Qty dalam KG">
                                <label for="floatingInput">Qty dalam KG</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-show-form btn-add" id="btn-tambah-sementara">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah ke List
                        </button>
                    </div>
                </form>
                <table class="table table-sm table-bordered mt-3" id="tbl-temp-barang">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Qty</th>
                            <th>Berat Isi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-add-barang mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form btn-add-barang">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="add_barang_ditapak" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang Ditapak</h5>
            </div>
            <div class="modal-body">
                <form class="form-excel" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="hidden" class="id_barang_hasil" name="id_barang_hasil" id="id_barang_hasil" />
                                <select class="form-select kode_barang_add_ditapak" name="kode_barang_add_ditapak" id="kode_barang_add_ditapak" aria-label="Floating label select example">
                                    <option data-barang_id="" data-nama="" data-satuan="" value=""></option>
                                </select>
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly class="form-control satuan_barang_add_ditapak" name="satuan_barang_add_ditapak" id="satuan_barang_add_ditapak" placeholder="Satuan">
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" class="form-control qty_barang_add_ditapak" name="qty_barang_add_ditapak" id="qty_barang_add_ditapak" placeholder="Qty Hasil">
                                <label for="floatingInput">Qty Saat Ini</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" class="form-control kg_barang_add_ditapak" name="kg_barang_add_ditapak" id="kg_barang_add_ditapak" placeholder="Berat Isi">
                                <label for="floatingInput">Berat Isi</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" readonly class="form-control qty_kg_barang_add_ditapak" name="qty_kg_barang_add_ditapak" id="qty_kg_barang_add_ditapak" placeholder="Qty dalam KG">
                                <label for="floatingInput">Qty dalam KG</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-show-form btn-add" id="btn-tambah-sementara">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah ke List
                        </button>
                    </div>
                </form>
                <table class="table table-sm table-bordered mt-3" id="tbl-temp-barang">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Qty</th>
                            <th>Berat Isi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-add-barang mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form btn-add-barang">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    let list_items_barang_jadi = [];
    let list_items_barang_digunakan = [];
    let list_items_barang_digunakan_penolong = [];
    let list_items_barang_scrap = [];
    let list_items_barang_filling = [];
    let list_items_barang_susut = [];
    let tempBarangList = [];

    function drawTempTable() {
        let tbody = $("#tbl-temp-barang tbody");
        tbody.empty();

        tempBarangList.forEach(item => {
            tbody.append(`
          <tr data-uid="${item.uid}">
            <td>${item.kode_barang}</td>
            <td>${item.barang_name}</td>
            <td>${item.qty}</td>
            <td>${item.berat_isi_jadi}</td>
            <td>
              <button class="btn btn-sm btn-primary btn-edit-temp">Edit</button>
              <button class="btn btn-sm btn-danger btn-del-temp">Hapus</button>
            </td>
          </tr>
        `);
        });
    }

    $(document).ready(function() {
        <?php if (isset($data)) : ?>
            setTimeout(function() {
                $("#kode_produksi").trigger("change");
            }, 300);

            const materialRequestIds = <?= json_encode(json_decode($data->material_request_id, true)) ?>;
            const materialRequestPenolongIds = <?= json_encode(json_decode($data->material_request_penolong_id, true)) ?>;

            <?php foreach ($dataResultBarangJadi as $key => $bj) : ?>
                list_items_barang_jadi.push({
                    'production_result_detail_id': '<?= $bj->id; ?>',
                    'barang1_id': '<?= $bj->barang1_id; ?>',
                    'barang2_id': '<?= $bj->barang2_id; ?>',
                    'barang_name': "<?= str_replace('"', '\"', $bj->nama_barang) ?>",
                    'kode_barang': '<?= $bj->kode_barang; ?>',
                    'kode_satuan': '<?= $bj->kode_satuan; ?>',
                    'nama_barang': "<?= str_replace('"', '\"', $bj->nama_barang) ?>",
                    'qty': '<?= floatval($bj->qty); ?>',
                    'qty2': '<?= floatval($bj->qty2); ?>',
                    'qty_isi': '<?= floatval($bj->qty_isi); ?>',
                    'qty_jadi': '<?= floatval($bj->qty); ?>',
                    'berat_isi_jadi': '<?= floatval($bj->qty2); ?>',
                    'qty_isi_jadi': '<?= floatval($bj->qty_isi); ?>',
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
                    'barang_name': '<?= str_replace('"', '\"', $bs->nama_barang); ?>',
                    'kode_barang': '<?= $bs->kode_barang; ?>',
                    'kode_satuan': '<?= $bs->kode_satuan; ?>',
                    'nama_barang': '<?= str_replace('"', '\"', $bs->nama_barang); ?>',
                    'divisi_name': '<?= $bs->divisi; ?>',
                    'warehouse_name': '<?= $bs->warehouse; ?>',
                    'qty': '<?= floatval($bs->qty); ?>',
                    'type_barang': '<?= $bs->barang_type; ?>',
                    'type_barang_text': '<?= $bs->type_barang_text; ?>',
                });
            <?php endforeach; ?>
            drawTableBarangScrap();
            <?php
            foreach ($dataResultBarangDigunakan as $key => $bd) :
                if ($bd->barang_type == 'bahan_penolong') :
            ?>
                    list_items_barang_digunakan_penolong.push({
                        'barang_detail_id': getID(),
                        'production_result_detail_id': '<?= $bd->id; ?>',
                        'barang1_id': '<?= $bd->barang1_id; ?>',
                        'barang2_id': '<?= $bd->barang2_id; ?>',
                        'barang_name': '<?= str_replace('"', '\"', $bd->nama_barang); ?>',
                        'kode_barang': '<?= $bd->kode_barang; ?>',
                        'satuan': '<?= $bd->kode_satuan; ?>',
                        'nama_barang': '<?= str_replace('"', '\"', $bd->nama_barang); ?>',
                        'qty': '<?= floatval($bd->qty); ?>',
                        'qty_now': '<?= floatval($bd->qty_now); ?>',
                        'ref_no': '<?= $bd->no_ref; ?>',
                        'no_aju': '<?= $bd->no_aju; ?>',
                        'type_barang': '<?= $bd->barang_type; ?>',
                        'type_barang_text': '<?= $bd->type_barang_text; ?>',
                    });
                <?php else : ?>
                    list_items_barang_digunakan.push({
                        'barang_detail_id': getID(),
                        'production_result_detail_id': '<?= $bd->id; ?>',
                        'barang1_id': '<?= $bd->barang1_id; ?>',
                        'barang2_id': '<?= $bd->barang2_id; ?>',
                        'barang_name': '<?= str_replace('"', '\"', $bd->nama_barang); ?>',
                        'kode_barang': '<?= $bd->kode_barang; ?>',
                        'satuan': '<?= $bd->kode_satuan; ?>',
                        'nama_barang': '<?= str_replace('"', '\"', $bd->nama_barang); ?>',
                        'qty': '<?= floatval($bd->qty); ?>',
                        'qty_now': '<?= floatval($bd->qty_now); ?>',
                        'ref_no': '<?= $bd->no_ref; ?>',
                        'no_aju': '<?= $bd->no_aju; ?>',
                        'type_barang': '<?= $bd->barang_type; ?>',
                        'type_barang_text': '<?= $bd->type_barang_text; ?>',
                    });
            <?php
                endif;
            endforeach;
            ?>
            drawTableBarangDigunakan();
            drawTableBarangDigunakanPenolong();
            <?php foreach ($dataResultBarangReturn as $key => $br) : ?>
                list_items_barang_filling.push({
                    'barang_detail_id': getID(),
                    'production_result_detail_id': '<?= $br->id; ?>',
                    'barang1_id': '<?= $br->barang1_id; ?>',
                    'barang2_id': '<?= $br->barang2_id; ?>',
                    'barang_name': '<?= str_replace('"', '\"', $br->nama_barang) ?>',
                    'kode_barang': '<?= $br->kode_barang; ?>',
                    'satuan': '<?= $br->kode_satuan; ?>',
                    'nama_barang': '<?= str_replace('"', '\"', $br->nama_barang); ?>',
                    'qty': '<?= floatval($br->qty); ?>',
                    'ref_no': '<?= $br->no_ref; ?>',
                    'no_aju': '<?= $br->no_aju; ?>',
                    'type_barang': '<?= $br->barang_type; ?>',
                    'type_barang_text': '<?= $br->type_barang_text; ?>',
                    'kondisi_barang': '<?= $br->kondisi_barang; ?>',
                    'kondisi_barang_text': '<?= strtoupper($br->kondisi_barang); ?>',
                });
            <?php endforeach; ?>
            drawTableBarangFilling();
            $.ajax({
                url: `<?= base_url('production-result/list-work-order'); ?>`,
                method: "GET",
                data: {
                    kode_produksi: '<?= $data->work_order_id ?>',
                },
                dataType: "json",
                success: function(res) {
                    $(".kode_barang_add").empty();
                    $(".kode_barang_add").append(`<option 
                        data-detail_work_order="" 
                        data-barang1_id="" 
                        data-barang2_id="" 
                        data-barang_name="" 
                        data-kode_barang="" 
                        data-kode_satuan="" 
                        data-nama_barang="" 
                        data-warehouse_id="" 
                        data-divisi_id="" 
                        data-note="" 
                        data-qty="" 
                        data-qty2="" 
                        data-qty_isi="" 
                        data-type_barang="" 
                        data-type_barang_text="" 
                        data-unit="" 
                        value=""></option>`);

                    res.data.forEach(function(item) {
                        // Push each item into the list_items_barang_jadi array
                        $(".kode_barang_add").append(`<option 
                            data-barang_detail_id="${getID()}" 
                            data-detail_work_order="${item.id}" 
                            data-barang1_id="${item.barang1_id}" 
                            data-barang2_id="${item.barang2_id}" 
                            data-barang_name="${item.barang_name + " - " + item.spesifikasi}" 
                            
                            data-kode_barang="${item.kode_barang}" 
                            data-kode_satuan="${item.kode_satuan}" 
                            data-nama_barang="${item.nama_barang}" 
                            data-warehouse_id="${item.warehouse_id}" 
                            
                            data-divisi_id="${item.divisi_id}" 
                            data-note="${item.note}" 
                            data-qty="${0}" 
                            data-qty2="${0}" 
                            
                            data-qty_isi="${0}" 
                            data-type_barang="${item.type_barang}" 
                            data-type_barang_text="${item.type_barang_text}" 
                            data-unit="${item.unit}" 
                            
                            value="${item.barang2_id}">(${item.kode_barang}) ${item.barang_name + " - " + item.spesifikasi}</option>`);
                    });
                    $(".kode_barang_add").val("").change();
                    // drawTableBarangJadi();
                    stopLoading();
                }
            });
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
        $('.kode_barang, .kode_barang_scrap, .kode_barang_filling, .kode_barang_susut').select2({
            placeholder: "Pilih Kode Barang",
            theme: "bootstrap-5",
            allowClear: true
        })

        $('.kode_barang_add').select2({
            placeholder: "Pilih Kode Barang",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $('#add_barang_produksi')
        })

        $('.kode_barang_add_ditapak').select2({
            placeholder: "Pilih Kode Barang",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $('#add_barang_ditapak')
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kode_barang, .kode_barang_scrap, .kode_barang_filling, .kode_barang_add, .kode_barang_add_ditapak, .kode_barang_susut')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_barang, .kode_barang_scrap, .kode_barang_filling, .kode_barang_add, .kode_barang_add_ditapak, .kode_barang_susut')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_barang, .kode_barang_scrap, .kode_barang_filling, .kode_barang_add, .kode_barang_add_ditapak, .kode_barang_susut')
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
        $('.kode_request, .kode_request_penolong').select2({
            placeholder: "",
            theme: "bootstrap-5",
            multiple: true,
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kode_request, .kode_request_penolong')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_request, .kode_request_penolong')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_request, .kode_request_penolong')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.kode_request, .kode_request_penolong')
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

        $("#nav-filling-tab").click(function() {
            var type = "bahan_setengah_jadi";
            setLoading();
            $.ajax({
                url: `<?= base_url("barang/dropdown/type"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    type: type
                },
                success: function(res) {
                    $(".kode_barang_filling").empty();
                    $(".kode_barang_filling").append(`<option data-barang_name_master="" data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
                    res.data.forEach(function(item) {
                        $(".kode_barang_filling").append(`<option data-barang_name_master="${item.barang_name_master}" data-barang_spesifikasi_id="${item.barang_master_spesifikasi_id}" data-barang_id="${item.id}" data-nama="${item.barang_name}" data-satuan_id="${item.satuan_1}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
                    })
                    $(".kode_barang_filling").val("").change();
                    stopLoading()
                }
            })
        });

        $("#nav-susut-tab").click(function() {
            var type = "bahan_baku";
            setLoading();
            $.ajax({
                url: `<?= base_url("barang/dropdown/type"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    type: type
                },
                success: function(res) {
                    $(".kode_barang_susut").empty();
                    $(".kode_barang_susut").append(`<option data-barang_name_master="" data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
                    res.data.forEach(function(item) {
                        $(".kode_barang_susut").append(`<option data-barang_name_master="${item.barang_name_master}" data-barang_spesifikasi_id="${item.barang_master_spesifikasi_id}" data-barang_id="${item.id}" data-nama="${item.barang_name}" data-satuan_id="${item.satuan_1}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
                    })
                    $(".kode_barang_susut").val("").change();
                    stopLoading()
                }
            })
        });

        $(".btn-add-barang-jadi").click(function() {
            $('#add_barang_produksi').modal('show');
        });

        $(".btn-add-barang-ditapak").click(function() {
            $('#add_barang_ditapak').modal('show');
        });

        $(".btn-discard-add-barang").click(function() {
            $(".kode_barang_add").val("").change();
            $(".qty_barang_add").val();
            $(".kg_barang_add").val();
            $(".qty_kg_barang_add").val();
            $('#add_barang_produksi').modal('hide');
        });

        $("#btn-tambah-sementara").click(function() {
            let uid = "tmp-" + Date.now();
            let detail_work_order = $(".kode_barang_add option:selected").data("detail_work_order") ? $(".kode_barang_add option:selected").data("detail_work_order") : "";
            let barang1_id = $(".kode_barang_add option:selected").data("barang1_id") ? $(".kode_barang_add option:selected").data("barang1_id") : "";
            let barang2_id = $(".kode_barang_add option:selected").data("barang2_id") ? $(".kode_barang_add option:selected").data("barang2_id") : "";
            let barang_name = $(".kode_barang_add option:selected").data("barang_name") ? $(".kode_barang_add option:selected").data("barang_name") : "";

            let kode_barang = $(".kode_barang_add option:selected").data("kode_barang") ? $(".kode_barang_add option:selected").data("kode_barang") : "";
            let kode_satuan = $(".kode_barang_add option:selected").data("kode_satuan") ? $(".kode_barang_add option:selected").data("kode_satuan") : "";
            let nama_barang = $(".kode_barang_add option:selected").data("nama_barang") ? $(".kode_barang_add option:selected").data("nama_barang") : "";
            let warehouse_id = $(".kode_barang_add option:selected").data("warehouse_id") ? $(".kode_barang_add option:selected").data("warehouse_id") : "";

            let divisi_id = $(".kode_barang_add option:selected").data("divisi_id") ? $(".kode_barang_add option:selected").data("divisi_id") : "";
            let note = $(".kode_barang_add option:selected").data("note") ? $(".kode_barang_add option:selected").data("note") : "";
            let qty_jadi = $(".qty_barang_add").val() ?? "";
            let berat_isi_jadi = $(".kg_barang_add").val() ?? "";

            let qty_isi_jadi = $(".qty_kg_barang_add").val() ?? "";
            let type_barang = $(".kode_barang_add option:selected").data("type_barang") ? $(".kode_barang_add option:selected").data("type_barang") : "";
            let type_barang_text = $(".kode_barang_add option:selected").data("type_barang_text") ? $(".kode_barang_add option:selected").data("type_barang_text") : "";
            let unit = $(".kode_barang_add option:selected").data("unit") ? $(".kode_barang_add option:selected").data("unit") : "";

            if (qty_jadi <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Qty tidak valid',
                    text: 'Qty harus lebih besar dari 0',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
                return; // hentikan proses
            }

            // Check if the item with the same barang1_id and barang2_id already exists
            let exists = tempBarangList.some(item =>
                item.barang1_id === barang1_id &&
                item.barang2_id === barang2_id &&
                item.qty_isi === qty_isi_jadi
            );

            if (exists) {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang sudah ada',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else {
                tempBarangList.push({
                    'uid': uid,
                    'detail_work_order': detail_work_order,
                    'barang1_id': barang1_id,
                    'barang2_id': barang2_id,
                    'barang_name': barang_name,
                    'kode_barang': kode_barang,
                    'kode_satuan': kode_satuan,
                    'nama_barang': nama_barang,
                    'warehouse_id': warehouse_id,
                    'divisi_id': divisi_id,
                    'note': note,
                    'qty': qty_jadi,
                    'qty2': berat_isi_jadi,
                    'qty_isi': qty_isi_jadi,
                    'qty_jadi': qty_jadi,
                    'berat_isi_jadi': berat_isi_jadi,
                    'qty_isi_jadi': qty_isi_jadi,
                    'type_barang': type_barang,
                    'type_barang_text': type_barang_text,
                    'unit': unit,
                });
                drawTempTable();

                $(".kode_barang_add").val("").change();
                $(".satuan_barang_add").val("");
                $(".qty_barang_add").val("");
                $(".kg_barang_add").val("");
                $(".qty_kg_barang_add").val("");
            }
        });

        $(document).on("click", ".btn-del-temp", function() {
            let uid = $(this).closest("tr").data("uid");
            tempBarangList = tempBarangList.filter(item => item.uid !== uid);
            drawTempTable();
        });

        $(document).on("click", ".btn-edit-temp", function() {
            let uid = $(this).closest("tr").data("uid");
            let item = tempBarangList.find(i => i.uid === uid);
            if (item) {
                $(".kode_barang_add").val(item.barang2_id).change();
                $(".qty_barang_add").val(item.qty).change();
                $(".kg_barang_add").val(item.berat_isi_jadi);
                tempBarangList = tempBarangList.filter(i => i.uid !== uid);
                drawTempTable();
            }
        });

        $(".btn-add-barang").click(function() {
            tempBarangList.forEach(t => {
                // cek apakah sudah ada item dengan barang1_id & barang2_id yang sama
                const sudahAda = list_items_barang_jadi.some(item =>
                    item.barang1_id == t.barang1_id &&
                    item.barang2_id == t.barang2_id &&
                    item.qty_isi == t.qty_isi
                );

                if (!sudahAda) {
                    // hanya push bila kombinasi belum ada
                    list_items_barang_jadi.push({
                        barang_detail_id: getID(),
                        ...t
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: `Barang ${t.barang_name} sudah ada`,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                }
            });

            drawTableBarangJadi();
            tempBarangList = [];
            $("#tbl-temp-barang tbody").empty();
            $('#add_barang_produksi').modal('hide');
        });


        $('.qty_barang_add, .kg_barang_add').on('input change', function() {
            var valueQtyBarangJadi = $('.qty_barang_add').val();
            var valueBeratBarangJadi = $('.kg_barang_add').val();


            var jumlahQtyBeratJadi = parseFloat(valueQtyBarangJadi) * parseFloat(valueBeratBarangJadi);

            $('.qty_kg_barang_add').val(jumlahQtyBeratJadi.toFixed(2));
        });

        $(".btn-save").click(function() {
            var listMaterialCheck = [].concat(list_items_barang_digunakan, list_items_barang_jadi, list_items_barang_filling);

            // console.log(list_items_barang_jadi);
            if (list_items_barang_jadi.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang jadi tidak boleh kosong !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            }
            if (list_items_barang_digunakan.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang digunakan tidak boleh kosong !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            }
            console.log(list_items_barang_digunakan);

            if (listMaterialCheck.length != 0) {
                if ($(".create-form").valid()) {
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
                            setLoading()
                            $("#department_id_order").prop('disabled', false);
                            $("#department_id_request").prop('disabled', false);
                            $("#warehouse_id_order").prop('disabled', false);
                            $("#warehouse_id_request").prop('disabled', false);
                            const data = new FormData(document.querySelector(".create-form"));
                            const id = $(".id").val();
                            data.append("jadi", JSON.stringify(list_items_barang_jadi));
                            data.append("digunakan", JSON.stringify(list_items_barang_digunakan));
                            data.append("digunakan_penolong", JSON.stringify(list_items_barang_digunakan_penolong));
                            data.append("scrap", JSON.stringify(list_items_barang_scrap));
                            data.append("filling", JSON.stringify(list_items_barang_filling));
                            data.append("susut", JSON.stringify(list_items_barang_susut));

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
                                                    window.location.href = "<?= base_url("production-result"); ?>";
                                                })
                                            stopLoading()
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
                                                    window.location.href = "<?= base_url("production-result"); ?>";
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
            let selectedValues = $(this).val();

            if (selectedValues && selectedValues.length > 0) {
                // For multiple selection, you might need to decide how to handle the data
                // Here I'm just taking the first selected item's data as an example
                let firstSelectedOption = $(this).find('option:selected').first();

                let nama_barang = firstSelectedOption.data("nama-barang") || "";
                let standart_production = firstSelectedOption.data("standart-production") || "";
                let warehouse_id = firstSelectedOption.data("warehouse") || "";
                let divisi_id = firstSelectedOption.data("divisi") || "";

                $(".barang_jadi").val(nama_barang);
                $(".standart_production").val(standart_production);
                $(".warehouse_id_order").val(warehouse_id).change();
                $(".department_id_order").val(divisi_id).change();

                setLoading();

                // For multiple values, you might need to adjust your AJAX calls
                // Here's an example using the first selected value
                let firstKodeProduksi = selectedValues;

                $.ajax({
                    url: `<?= base_url('production-result/material-request'); ?>`,
                    method: "GET",
                    data: {
                        kode_produksi: selectedValues,
                        is_edit: <?= isset($data) ? 'true' : 'false' ?>,
                    },
                    dataType: "json",
                    success: function(res) {
                        stopLoading();
                        if (res.status) {
                            $('.kode_request').empty();
                            $('.kode_request').append(`<option value=""></option>`);
                            res.data.forEach(function(item) {
                                $('.kode_request').append(`<option value="${item.id}" data-tanggal-request="${item.request_date}" data-user-request="${item.user_name}" data-warehouse-request="${item.warehouse_id}" data-divisi-request="${item.divisi_id}">${item.req_no}</option>`);
                            });
                            <?php if (isset($data)) : ?>
                                if (Array.isArray(materialRequestIds) && materialRequestIds.length > 0) {
                                    $('.kode_request').val(materialRequestIds).trigger('change');
                                }
                            <?php endif; ?>
                            $('.kode_request_penolong').empty();
                            $('.kode_request_penolong').append(`<option value=""></option>`);
                            res.dataPenolong.forEach(function(item) {
                                $('.kode_request_penolong').append(`<option value="${item.id}" data-tanggal-request="${item.request_date}" data-user-request="${item.user_name}" data-warehouse-request="${item.warehouse_id}" data-divisi-request="${item.divisi_id}">${item.req_no}</option>`);
                            });
                            <?php if (isset($data)) : ?>
                                if (Array.isArray(materialRequestPenolongIds) && materialRequestPenolongIds.length > 0) {
                                    $('.kode_request_penolong').val(materialRequestPenolongIds).trigger('change');
                                }
                            <?php endif; ?>
                        } else {
                            stopLoading();
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Material Request Tidak Ada',
                                confirmButtonColor: '#4e73df',
                            });
                            $('.kode_request').empty();
                            $('.kode_request').append(`<option value=""></option>`);
                            $('.kode_request_penolong').empty();
                            $('.kode_request_penolong').append(`<option value=""></option>`);
                            $('.date_request').val();
                            list_items_barang_jadi = [];
                            list_items_barang_digunakan = [];
                            drawTableBarangJadi();
                            drawTableBarangDigunakan();
                        }
                    },
                });
                // console.log(selectedValues);

                $.ajax({
                    url: `<?= base_url('production-result/list-work-order'); ?>`,
                    method: "GET",
                    data: {
                        kode_produksi: selectedValues.join(',')
                    },
                    dataType: "json",
                    success: function(res) {
                        <?php if (!isset($data)) : ?>
                            list_items_barang_jadi = [];
                            list_items_barang_scrap = [];
                            list_items_barang_digunakan = [];
                        <?php endif; ?>
                        $(".kode_barang_add").empty();
                        $(".kode_barang_add").append(`<option 
                                    data-detail_work_order="" 
                                    data-barang1_id="" 
                                    data-barang2_id="" 
                                    data-barang_name="" 
                                    data-kode_barang="" 
                                    data-kode_satuan="" 
                                    data-nama_barang="" 
                                    data-warehouse_id="" 
                                    data-divisi_id="" 
                                    data-note="" 
                                    data-qty="" 
                                    data-qty2="" 
                                    data-qty_isi="" 
                                    data-type_barang="" 
                                    data-type_barang_text="" 
                                    data-unit="" 
                                    value=""></option>`);

                        res.data.forEach(function(item) {
                            $(".kode_barang_add").append(`<option 
                                        data-barang_detail_id="${getID()}" 
                                        data-detail_work_order="${item.id}" 
                                        data-barang1_id="${item.barang1_id}" 
                                        data-barang2_id="${item.barang2_id}" 
                                        data-barang_name="${item.barang_name + " - " + item.spesifikasi}" 
                                        data-kode_barang="${item.kode_barang}" 
                                        data-kode_satuan="${item.kode_satuan}" 
                                        data-nama_barang="${item.nama_barang}" 
                                        data-warehouse_id="${item.warehouse_id}" 
                                        data-divisi_id="${item.divisi_id}" 
                                        data-note="${item.note}" 
                                        data-qty="${0}" 
                                        data-qty2="${0}" 
                                        data-qty_isi="${0}" 
                                        data-type_barang="${item.type_barang}" 
                                        data-type_barang_text="${item.type_barang_text}" 
                                        data-unit="${item.unit}" 
                                        value="${item.barang2_id}">(${item.kode_barang}) ${item.barang_name + " - " + item.spesifikasi}</option>`);
                        });
                        $(".kode_barang_add").val("").change();
                        stopLoading();
                    }
                });

            } else {
                $(".barang_jadi").val("");
                $(".standart_production").val("");
                $(".warehouse_id_produksi").val("").change();
                $(".divisi_id_produksi").val("").change();
            }
        });

        $(".kode_barang_add").change(function() {
            if ($(this).val()) {
                let kode_satuan = $(".kode_barang_add option:selected").data("kode_satuan") ? $(".kode_barang_add option:selected").data("kode_satuan") : "";
                let qty = $(".kode_barang_add option:selected").data("qty") ? $(".kode_barang_add option:selected").data("qty") : 0;
                let qty2 = $(".kode_barang_add option:selected").data("qty2") ? $(".kode_barang_add option:selected").data("qty2") : 0;
                let qty_isi = $(".kode_barang_add option:selected").data("qty_isi") ? $(".kode_barang_add option:selected").data("qty_isi") : 0;


                $(".satuan_barang_add").val(kode_satuan);
                $(".qty_barang_add").val(qty).change();
                $(".kg_barang_add").val(qty2).change();
                $(".qty_kg_barang_add").val(qty_isi).change();
            }
        });

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
                        <?php if (!isset($data)) : ?>
                            list_items_barang_digunakan = [];
                        <?php endif; ?>
                        <?php if (!isset($data)) : ?>
                            res.data.forEach(function(item) {
                                // console.log(item);
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
                                    'stock_detail_id': item.stock_detail_tujuan_id,
                                    'stock_date': item.stock_date,
                                    'stock_dokumen': item.stock_dokumen,
                                    'barang1_id': item.barang1_id,
                                    'barang2_id': item.barang2_id,
                                    'kode_barang': item.kode_barang,
                                    'satuan': item.satuan,
                                    'nama_barang': item.nama_barang,
                                    'note': item.note,
                                    'qty': item.type_barang == "bahan_jadi" ? item.qty_isi : item.qty_now,
                                    'qty_isi': item.qty_isi,
                                    'ref_no': new_ref_no,
                                    'no_aju': item.no_aju,
                                    'type_barang': item.type_barang,
                                    'type_barang_text': item.type_barang_text,
                                    'unit': item.unit,
                                    'warehouse_id': item.warehouse_tujuan_id,
                                    'divisi_id': item.divisi_tujuan_id,
                                    'kondisi_barang': item.kondisi_barang,
                                    'harga_umum': item.harga_umum,
                                    'harga_harian': item.harga_harian,
                                    'harga_bulanan': item.harga_bulanan,
                                });
                                drawTableBarangDigunakan();
                            });
                        <?php endif; ?>
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

        $(".kode_request_penolong").change(function() {
            if ($(".kode_request_penolong option:selected").val()) {
                let date_request = $(".kode_request_penolong option:selected").data("tanggal-request") ? $(".kode_request_penolong option:selected").data("tanggal-request") : "";

                if (date_request) {
                    let parts = date_request.split('-');
                    date_request = parts[2] + '/' + parts[1] + '/' + parts[0];
                }

                $("#date_request_penolong").val(date_request);
                setLoading();
                $.ajax({
                    url: `<?= base_url('production-result/list-material-request-penolong'); ?>`,
                    method: "GET",
                    data: {
                        kode_request: $(this).val(),
                    },
                    dataType: "json",
                    success: function(res) {
                        list_items_barang_digunakan_penolong = [];
                        <?php if (!isset($data) || empty(json_decode($data->material_request_penolong_id, true))): ?>
                            res.data.forEach(function(item) {
                                // console.log(item);
                                if (item.ref_no == "NON PABEAN") {
                                    var new_ref_no = item.ref_no;
                                } else {
                                    var ref_no = item.ref_no + "/" + item.no_aju + "/" + item.stock_date;

                                    // Pisahkan string berdasarkan tanda slash '/'
                                    var parts = ref_no.split('/');
                                    var partsAju = parts[1].split('-')[3] ?? '-';
                                    var partsDate = parts[2].replace(/-/g, '');

                                    // // Dapatkan bagian yang Anda inginkan (bagian ke-1 dan ke-4)
                                    var new_ref_no = parts[0] + '/' + partsAju + '/' + partsDate;
                                }
                                list_items_barang_digunakan_penolong.push({
                                    'barang_detail_id': getID(),
                                    'material_request_detail_id': item.id,
                                    'material_request_id': item.material_request_id,
                                    'bc_id': item.bc_id,
                                    'stock_id': item.stock_tujuan_id,
                                    'stock_detail_id': item.stock_detail_tujuan_id,
                                    'stock_date': item.stock_date,
                                    'stock_dokumen': item.stock_dokumen,
                                    'barang1_id': item.barang1_id,
                                    'barang2_id': item.barang2_id,
                                    'kode_barang': item.kode_barang,
                                    'satuan': item.satuan,
                                    'nama_barang': item.nama_barang,
                                    'note': item.note,
                                    'qty': item.qty,
                                    'qty2': item.qty2,
                                    'ref_no': new_ref_no,
                                    'no_aju': item.no_aju,
                                    'type_barang': item.type_barang,
                                    'type_barang_text': item.type_barang_text,
                                    'unit': item.unit,
                                    'warehouse_id': item.warehouse_tujuan_id,
                                    'divisi_id': item.divisi_tujuan_id,
                                    'kondisi_barang': item.kondisi_barang,
                                    'harga_umum': item.harga_umum,
                                    'harga_harian': item.harga_harian,
                                    'harga_bulanan': item.harga_bulanan,
                                });
                            });
                            drawTableBarangDigunakanPenolong();
                        <?php endif; ?>
                        stopLoading()
                    }
                });
            } else {
                $("#date_request_penolong").val("");
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
            let departmentId = $(".department_id_filling option:selected").val();
            let warehouseId = $(".warehouse_id_filling option:selected").val();

            // Memeriksa apakah semua input terisi
            if (kodeBarang && qtyBarang && kondisiBarang && departmentId && warehouseId) {
                // Jika semua input terisi, tambahkan barang filling
                // let barang = $(".kode_barang_filling option:selected").val();

                // let material_request_detail_id = $(".kode_barang_filling option:selected").data("material_request_detail_id");
                // let material_request_id = $(".kode_barang_filling option:selected").data("material_request_id");
                // let bc_id = $(".kode_barang_filling option:selected").data("bc_id");
                // let stock_id = $(".kode_barang_filling option:selected").data("stock_id");
                // let stock_detail_id = $(".kode_barang_filling option:selected").data("stock_detail_id");
                // let stock_date = $(".kode_barang_filling option:selected").data("stock_date");
                // let stock_dokumen = $(".kode_barang_filling option:selected").data("stock_dokumen");
                // let barang1_id = $(".kode_barang_filling option:selected").data("barang1_id");
                // let barang2_id = $(".kode_barang_filling option:selected").data("barang2_id");
                // let kode_barang = $(".kode_barang_filling option:selected").data("kode_barang");
                // let satuan = $(".kode_barang_filling option:selected").data("satuan");
                // let nama_barang = $(".kode_barang_filling option:selected").data("nama_barang");
                // let note = $(".kode_barang_filling option:selected").data("note");
                // let qty = $(".kode_barang_filling option:selected").data("qty");
                // let ref_no = $(".kode_barang_filling option:selected").data("ref_no");
                // let no_aju = $(".kode_barang_filling option:selected").data("no_aju");
                // let type_barang = $(".kode_barang_filling option:selected").data("type_barang");
                // let type_barang_text = $(".kode_barang_filling option:selected").data("type_barang_text");
                // let unit = $(".kode_barang_filling option:selected").data("unit");
                // let warehouse_id = $(".kode_barang_filling option:selected").data("warehouse_id");
                // let divisi_id = $(".kode_barang_filling option:selected").data("divisi_id");
                // let harga_umum = $(".kode_barang_filling option:selected").data("harga_umum");
                // let harga_harian = $(".kode_barang_filling option:selected").data("harga_harian");
                // let harga_bulanan = $(".kode_barang_filling option:selected").data("harga_bulanan");

                let barang1_id = $(".kode_barang_filling option:selected").data("barang_id");
                let barang2_id = $(".kode_barang_filling option:selected").data("barang_spesifikasi_id");
                let nama_barang = $(".kode_barang_filling option:selected").data("nama");
                let kode_barang = $(".kode_barang_filling option:selected").val();
                let satuan = $(".kode_barang_filling option:selected").data("satuan");
                let satuan_id = $(".kode_barang_filling option:selected").data("satuan_id");

                list_items_barang_filling.push({
                    'barang_detail_id': getID(),
                    'barang1_id': barang1_id,
                    'barang2_id': barang2_id,
                    'nama_barang': nama_barang,
                    'kode_barang': kode_barang,
                    'satuan': satuan,
                    'unit': satuan_id,
                    'qty': qtyBarang,
                    'type_barang': 'bahan_setengah_jadi',
                    'type_barang_text': 'BAHAN SETENGAH JADI',
                    'warehouse_id': warehouseId,
                    'divisi_id': departmentId,
                    'kondisi_barang': kondisiBarang,
                    'kondisi_barang_text': kondisiBarangText
                });
                drawTableBarangFilling(); // Menggambar tabel
                resetFormDetailFilling(); // Mengatur ulang form
            } else {
                // Menampilkan pesan kesalahan di bawah input yang kosong
                if (!kodeBarang) $(".kode_barang_filling").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih kode barang.</span>');
                if (!qtyBarang) $(".qty_filling").closest('.form-floating').append('<span class="error-message text-danger">Harap masukkan jumlah barang.</span>');
                if (!kondisiBarang) $(".kondisi_barang").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih kondisi barang.</span>');
                if (!departmentId) $(".department_id_filling").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih department tujuan.</span>');
                if (!warehouseId) $(".warehouse_id_filling").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih warehouse tujuan.</span>');
            }
        });

        // Event click untuk tombol "Tambah Barang susut"
        $(".button-add-susut").click(function() {
            // Menghapus pesan kesalahan sebelumnya
            $('.error-message').remove();

            // Mengambil nilai dari setiap input
            let kodeBarang = $(".kode_barang_susut option:selected").val();
            let qtyBarang = $(".qty_susut").val();

            // Memeriksa apakah semua input terisi
            if (kodeBarang && qtyBarang) {
                let barang1_id = $(".kode_barang_susut option:selected").data("barang_id");
                let barang2_id = $(".kode_barang_susut option:selected").data("barang_spesifikasi_id");
                let nama_barang = $(".kode_barang_susut option:selected").data("nama");
                let kode_barang = $(".kode_barang_susut option:selected").val();
                let satuan = $(".kode_barang_susut option:selected").data("satuan");
                let satuan_id = $(".kode_barang_susut option:selected").data("satuan_id");

                list_items_barang_susut.push({
                    'barang_detail_id': getID(),
                    'barang1_id': barang1_id,
                    'barang2_id': barang2_id,
                    'nama_barang': nama_barang,
                    'kode_barang': kode_barang,
                    'satuan': satuan,
                    'unit': satuan_id,
                    'qty': qtyBarang,
                    'type_barang': 'bahan_baku',
                    'type_barang_text': 'BAHAN BAKU',
                    'warehouse_id': 0,
                    'divisi_id': 0
                });

                console.log(list_items_barang_susut);
                
                drawTableBarangSusut(); // Menggambar tabel
                resetFormDetailSusut(); // Mengatur ulang form
            } else {
                // Menampilkan pesan kesalahan di bawah input yang kosong
                if (!kodeBarang) $(".kode_barang_susut").closest('.form-floating').append('<span class="error-message text-danger">Harap pilih kode barang.</span>');
                if (!qtyBarang) $(".qty_susut").closest('.form-floating').append('<span class="error-message text-danger">Harap masukkan jumlah barang.</span>');
            }
        });
    });

    // const changeStatus = function() {
    //     let value = document.getElementById('auto_generate').checked ? true : false;

    //     if (value) {
    //         $(".res_no").attr("readonly", true);
    //         $(".res_no").val("AUTO GENERATE");
    //     } else {
    //         $(".res_no").attr("readonly", false);
    //         $(".res_no").val("");
    //     }
    // }
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
        $('#tfoot-barang-jadi').empty(); // Menggunakan ID khusus untuk footer

        var row = '';
        var no = 1;

        if (list_items_barang_jadi.length === 0) {
            row += `
                <tr>
                    <td colspan="8" class="text-center">Data Barang Tidak Ada</td>
                </tr>
            `;
            $('.body-table-barang-jadi').append(row);
        } else {
            list_items_barang_jadi.map((item, index) => {
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.type_barang_text + '</td>';
                row += '<td>' + item.barang_name + '</td>';
                row += '<td>' + item.kode_satuan + '</td>';
                row += '<td>' + `
                    <input  style="height:40px" class="form-control qty-barang-jadi" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.qty}" <?= isset($data) && $data->is_posted == 1 ? "readonly" : ""; ?>>
                ` + '</td>';
                row += '<td>' + `
                    <input  style="height:40px" class="form-control berat-barang-jadi" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.qty2}" <?= isset($data) && $data->is_posted == 1 ? "readonly" : ""; ?>>
                ` + '</td>';
                row += '<td>' + `
                    <input  style="height:40px" class="form-control qty-berat-barang-jadi" readonly autocomplete="one-time-code" type="text" data-index="${index}" value="${greatFormatRupiah(item.qty_isi)}" <?= isset($data) && $data->is_posted == 1 ? "readonly" : ""; ?>>
                ` + '</td>';
                row += '<td>' + `
                    <button <?= isset($data) ? ($data->is_posted ? 'disabled' : '') : '' ?>  type="button" class="btn btn-discard delete-btn btn-trash" onclick="deleteRowDetailJadi('${item.barang_detail_id}', '${item.production_result_detail_id}')"><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                ` + '</td>';

                no++;
            });
            $('.body-table-barang-jadi').append(row);

            // Tambahkan footer untuk menampilkan total
            var totalQtyHasil = 0;
            var totalBeratIsi = 0;
            var totalQtyKg = 0;
            list_items_barang_jadi.forEach(item => {
                totalQtyHasil += parseFloat(item.qty || 0);
                totalBeratIsi += parseFloat(item.qty2 || 0);
                totalQtyKg += parseFloat(item.qty_isi || 0);
            });

            var footerRow = `
                <tr style="font-weight: bold;">
                    <td colspan="5"></td>
                    <td>${(totalQtyHasil.toFixed(2))}</td>
                    <td>${(totalBeratIsi.toFixed(3))}</td>
                    <td>${(totalQtyKg.toFixed(2))}</td>
                    <td></td>
                </tr>
            `;
            $('#tfoot-barang-jadi').append(footerRow); // Gunakan ID khusus untuk footer
        }

        // Menangani perubahan input Qty Hasil dan Berat Isi
        $('.qty-barang-jadi, .berat-barang-jadi, .qty-berat-barang-jadi').on('input change', function() {
            var index = $(this).data('index');
            var valueQtyBarangJadi = parseFloat($('input.qty-barang-jadi[data-index="' + index + '"]').val()) || 0;
            var valueBeratBarangJadi = parseFloat($('input.berat-barang-jadi[data-index="' + index + '"]').val()) || 0;
            var valueQtyBeratBarangJadi = parseFloat($('input.qty-berat-barang-jadi[data-index="' + index + '"]').val()) || 0;

            // Simpan nilai baru ke array
            list_items_barang_jadi[index].qty = valueQtyBarangJadi;
            list_items_barang_jadi[index].qty2 = valueBeratBarangJadi;
            list_items_barang_jadi[index].qty_isi = parseFloat(valueQtyBeratBarangJadi); // simpan sebagai number, bukan string

            // Render ulang tabel
            drawTableBarangJadi();
        });
    };


    const drawTableBarangDigunakan = function() {
        $('.body-table-barang-digunakan').empty();
        $('#tfoot-barang-digunakan').empty(); // Gunakan ID untuk target footer khusus

        var row = '';
        var no = 1;
        var totalQtyDigunakan = 0; // Total qty digunakan
        var totalQtyRequest = 0; // Total qty permintaan

        if (list_items_barang_digunakan.length === 0) {
            row += `
                    <tr>
                        <td colspan="9" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.body-table-barang-digunakan').append(row);
        } else {
            console.log(list_items_barang_digunakan);
            list_items_barang_digunakan.map((item, index) => {
                var qty = item.qty2 ? item.qty2 : item.type_barang == "bahan_jadi" ? item.qty_isi ?? item.qty : item.qty_now ?? item.qty;

                <?php if (isset($data)) : ?>
                    totalQtyDigunakan += parseFloat(item.qty || 0);
                    totalQtyRequest += parseFloat((item.qty) || 0);
                <?php else: ?>
                    totalQtyDigunakan += parseFloat(qty || 0);
                    totalQtyRequest += parseFloat((item.qty_now ?? item.qty) || 0);
                <?php endif; ?>


                row += '<tr style="color:whitesmoke;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.ref_no + '</td>';
                <?php if (!isset($data)) : ?>
                    row += '<td>' + item.kondisi_barang.toUpperCase() + '</td>';
                <?php endif; ?>
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.type_barang_text + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + (item.type_barang == "bahan_jadi" ? "KG" : item.satuan) + '</td>';
                row += '<td>' + greatFormatRupiah(item.type_barang == "bahan_jadi" ? item.qty_isi ?? item.qty : item.qty_now ?? item.qty) + '</td>';
                <?php if (isset($data)) : ?>
                    row += '<td>' + `
                    <input class="form-control qty-barang-digunakan" <?= isset($data) ? ($data->is_posted ? 'readonly' : '') : '' ?>   style="height:40px" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" value="${item.qty}">` +
                        '</td>';
                <?php else: ?>
                    row += '<td>' + `
                    <input class="form-control qty-barang-digunakan" <?= isset($data) ? ($data->is_posted ? 'readonly' : '') : '' ?>   style="height:40px" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" value="${qty}">` +
                        '</td>';
                <?php endif; ?>

                no++;
            });
            $('.body-table-barang-digunakan').append(row);

            // Tambahkan footer untuk menampilkan total

            <?php if (!isset($data)) : ?>
                var footerRow = `
                    <tr style="font-weight: bold;">
                        <td colspan="7"></td>
                        <td class="text-center">${greatFormatRupiah(totalQtyRequest.toFixed(2))}</td>
                        <td class="text-center">${greatFormatRupiah(totalQtyDigunakan.toFixed(2))}</td>
                    </tr>
                `;
            <?php else: ?>
                var footerRow = `
                    <tr style="font-weight: bold;">
                        <td colspan="6"></td>
                        <td class="text-center">${greatFormatRupiah(totalQtyRequest.toFixed(2))}</td>
                        <td class="text-center">${greatFormatRupiah(totalQtyDigunakan.toFixed(2))}</td>
                    </tr>
                `;
            <?php endif; ?>
            $('#tfoot-barang-digunakan').append(footerRow); // Gunakan ID untuk target footer khusus
        }

        // Tambahkan event listener untuk input qty
        $('.qty-barang-digunakan').on('input change', function() {
            var index = $(this).data('index'); // Dapatkan indeks item
            var newValue = $(this).val(); // Nilai input dari pengguna
            list_items_barang_digunakan[index].qty2 = newValue; // Simpan nilai baru
            drawTableBarangDigunakan(); // Render ulang tabel untuk update total
        });
    };

    const drawTableBarangDigunakanPenolong = function() {
        $('.body-table-barang-digunakan-penolong').empty();
        $('#tfoot-barang-digunakan-penolong').empty(); // Gunakan ID untuk target footer khusus

        var row = '';
        var no = 1;
        var totalQtyDigunakan = 0; // Total qty digunakan
        var totalQtyRequest = 0; // Total qty permintaan

        if (list_items_barang_digunakan_penolong.length === 0) {
            row += `
                    <tr>
                        <td colspan="8" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.body-table-barang-digunakan-penolong').append(row);
        } else {
            list_items_barang_digunakan_penolong.map((item, index) => {
                var qty = item.qty2 ? item.qty2 : item.qty;
                totalQtyDigunakan += parseFloat(qty || 0);
                totalQtyRequest += parseFloat((item.qty_now ?? item.qty2) || 0);

                row += '<tr style="color:whitesmoke;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.ref_no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.type_barang_text + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + item.satuan + '</td>';
                row += '<td>' + greatFormatRupiah(item.qty_now ?? item.qty2) + '</td>';
                row += '<td>' + `
                <input class="form-control qty-barang-digunakan-penolong" <?= isset($data) ? ($data->is_posted ? 'readonly' : '') : '' ?>   style="height:40px" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" value="${qty}">` +
                    '</td>';

                no++;
            });
            $('.body-table-barang-digunakan-penolong').append(row);

            // Tambahkan footer untuk menampilkan total
            var footerRow = `
                <tr style="font-weight: bold;">
                    <td colspan="6"></td>
                    <td class="text-center">${greatFormatRupiah(totalQtyRequest.toFixed(2))}</td>
                    <td colspan="2" >${greatFormatRupiah(totalQtyDigunakan.toFixed(2))}</td>
                </tr>
            `;
            $('#tfoot-barang-digunakan-penolong').append(footerRow); // Gunakan ID untuk target footer khusus
        }

        $('.qty-barang-digunakan-penolong').on('input change', function() {
            var index = $(this).data('index');
            var newValue = $(this).val();
            list_items_barang_digunakan_penolong[index].qty2 = newValue;
            drawTableBarangDigunakanPenolong();
        });
    };

    const drawTableBarangScrap = function() {
        $('.body-table-barang-scrap').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;

        // Cek apakah data barang scrap ada
        if (list_items_barang_scrap.length === 0) {
            row += `
                <tr>
                    <td colspan="5" class="text-center">Data Barang Tidak Ada</td>
                </tr>
            `;
            $('.tfoot').append(row);
        } else {
            // Looping untuk menampilkan data barang scrap
            list_items_barang_scrap.map(item => {
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + item.divisi_name + '</td>';
                row += '<td>' + item.warehouse_name + '</td>';
                row += '<td>' + greatFormatRupiah(item.qty) + '</td>';

                // Hanya tampilkan tombol delete jika data belum diposting
                    row += `
                        <td>
                            <button type="button" class="btn btn-danger" onclick="deleteRowDetailScrap('${item.barang_detail_id}', '${item.production_result_detail_id}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        </td>
                    `;

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

        // Cek apakah data barang filling ada
        if (list_items_barang_filling.length === 0) {
            row += `
                <tr>
                    <td colspan="5" class="text-center">Data Barang Tidak Ada</td>
                </tr>
            `;
            $('.tfoot').append(row);
        } else {
            // Looping untuk menampilkan data barang filling
            list_items_barang_filling.map(item => {
                row += '<tr style="color:whitesmoke;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + item.kondisi_barang_text + '</td>';
                row += '<td>' + greatFormatRupiah(item.qty) + '</td>';

                // Hanya tampilkan tombol delete jika data belum diposting
                    row += `
                        <td>
                            <button type="button" class="btn btn-danger" onclick="deleteRowDetailFilling('${item.barang_detail_id}', '${item.production_result_detail_id}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        </td>
                    `;

                no++;
            });
            $('.body-table-barang-filling').append(row);
        }
    }

    const drawTableBarangSusut = function() {
        $('.body-table-barang-susut').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;

        // Cek apakah data barang susut ada
        if (list_items_barang_susut.length === 0) {
            row += `
                <tr>
                    <td colspan="5" class="text-center">Data Barang Tidak Ada</td>
                </tr>
            `;
            $('.tfoot').append(row);
        } else {
            // Looping untuk menampilkan data barang susut
            list_items_barang_susut.map(item => {
                row += '<tr style="color:whitesmoke;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + greatFormatRupiah(item.qty) + '</td>';

                // Hanya tampilkan tombol delete jika data belum diposting
                row += `
                    <td>
                        <button type="button" class="btn btn-danger" onclick="deleteRowDetailSusut('${item.barang_detail_id}', '${item.production_result_detail_id}')">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    </td>
                `;
                no++;
            });
            $('.body-table-barang-susut').append(row);
        }
    }

    const deleteRowDetailJadi = function(id, iddetail) {
        console.log(id);
        console.log(iddetail);
        if (id && iddetail == "undefined") {
            const indexToRemove = list_items_barang_jadi.findIndex(item => item.barang_detail_id === id);
            console.log(id);
            console.log(indexToRemove);
            if (indexToRemove !== -1) {
                list_items_barang_jadi.splice(indexToRemove, 1);
            }
            drawTableBarangJadi();
        }
        if (iddetail && id == "undefined") {
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
                    const csrf = $(`[name="${csrfToken}"]`);
                    $.ajax({
                        url: "<?= base_url("production-result/delete-detail"); ?>",
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

    const deleteRowDetailScrap = function(id, iddetail) {
        if (id && iddetail == "undefined") {
            const indexToRemove = list_items_barang_scrap.findIndex(item => item.barang_detail_id === id);
            if (indexToRemove !== -1) {
                list_items_barang_scrap.splice(indexToRemove, 1);
            }
            drawTableBarangScrap();
        }
        if (iddetail && iddetail != "undefined") {
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
                    const csrf = $(`[name="${csrfToken}"]`);
                    $.ajax({
                        url: "<?= base_url("production-result/delete-detail"); ?>",
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
    const resetFormDetailScrap = function() {
        $(".kode_barang_scrap").val('').change()
        $(".qty_scrap").val('')
        $(".department_id_scrap").val('').change()
        $(".warehouse_id_scrap").val('').change()
    }

    const deleteRowDetailFilling = function(id, iddetail) {
        console.log(id);
        console.log(iddetail);
        if (id && iddetail == "undefined") {
            const indexToRemove = list_items_barang_filling.findIndex(item => item.barang_detail_id === id);
            if (indexToRemove !== -1) {
                list_items_barang_filling.splice(indexToRemove, 1);
            }
            drawTableBarangFilling();
        }
        if (iddetail && iddetail != "undefined") {
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
                    const csrf = $(`[name="${csrfToken}"]`);
                    $.ajax({
                        url: "<?= base_url("production-result/delete-detail"); ?>",
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
    const resetFormDetailFilling = function() {
        $(".kode_barang_filling").val('').change()
        $(".qty_filling").val('')
        $(".kondisi_barang").val('').change()
        $(".department_id_filling").val('').change()
        $(".warehouse_id_filling").val('').change()
    }

    const deleteRowDetailSusut = function(id, iddetail) {
        console.log(id);
        console.log(iddetail);
        if (id && iddetail == "undefined") {
            const indexToRemove = list_items_barang_susut.findIndex(item => item.barang_detail_id === id);
            if (indexToRemove !== -1) {
                list_items_barang_susut.splice(indexToRemove, 1);
            }
            drawTableBarangSusut();
        }
        if (iddetail && iddetail != "undefined") {
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
                    const csrf = $(`[name="${csrfToken}"]`);
                    $.ajax({
                        url: "<?= base_url("production-result/delete-detail"); ?>",
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
    const resetFormDetailSusut = function() {
        $(".kode_barang_susut").val('').change()
        $(".qty_susut").val('')
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
            cancelButtonText: 'Kembali',
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
                    url: "<?= base_url("production-result/delete"); ?>",
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
                                    window.location.href = "<?= base_url('production-result') ?>"
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
    const print = function(url) {
        window.open(url);
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $.ajax({
                url: `<?= base_url("/production-result/generate-kode-penerimaan"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res) {
                        $("#res_no").val(res);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#res_no").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#res_no").val("");
                    }
                }
            })
        } else {
            $("#res_no").attr("readonly", false);
            $("#res_no").val("");
        }
    }
</script>

<?= $this->endSection(); ?>
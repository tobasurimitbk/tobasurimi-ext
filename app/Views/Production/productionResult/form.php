<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah Penerimaan Hasil Produksi</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("production-result"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-form">
                Simpan
            </button>
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
                                    <input autocomplete="one-time-code" type="text" class="form-control res_no" id="res_no" name="res_no" placeholder="Kode Penerimaan">
                                    <label for="floatingInput">Kode Penerimaan</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control date_picker" name="date_production" id="date_production" placeholder="Tanggal Produksi">
                            <label for="floatingInput">Tanggal Penerimaan</label>
                        </div>
                    </div>
                </div>
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Data Produksi</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kode_produksi" name="kode_produksi" id="kode_produksi" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataWorkOrder ?? [] as $dataWO) : ?>
                                    <option value="<?= $dataWO->id ?>" data-nama-barang="<?= $dataWO->nama_barang ?>" data-standart-production="<?= $dataWO->standart_production ?>" data-warehouse="<?= $dataWO->warehouse_id ?>" data-divisi="<?= $dataWO->divisi_id ?>"><?= $dataWO->wo_no ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Kode Produksi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control barang_jadi" name="barang_jadi" id="barang_jadi" placeholder="Barang Jadi" readonly>
                            <label for="floatingInput">Barang Jadi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control standart_production" name="standart_production" id="standart_production" placeholder="Jumlah Standart Produksi" readonly>
                            <label for="floatingInput">Jumlah Standart Produksi</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select department_id_order" name="department_id" id="department_id_order" disabled>
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
                            <select class="form-select warehouse_id_order" name="warehouse_id" id="warehouse_id_order" disabled>
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
                            <label class="form-label font-weight-bold modal-sub-title">Data Request</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kode_request" name="kode_request" id="kode_request" aria-label="Floating label select example">
                                <option value=""></option>
                                <!-- <?php foreach ($dataMaterialRequest ?? [] as $dataMR) : ?>
                                    <option value="<?= $dataMR->id ?>" data-tanggal-request="<?= date('d/m/Y', strtotime($dataMR->request_date)) ?>" data-user-request="<?= $dataMR->user_name ?>" data-warehouse-request="<?= $dataWO->warehouse_id ?>" data-divisi-request="<?= $dataWO->divisi_id ?>"><?= $dataMR->req_no ?></option>
                                <?php endforeach; ?> -->
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
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control user_request" name="user_request" id="user_request" placeholder="User Request" readonly>
                            <label for="floatingInput">User Request</label>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                </div>
                <!-- details -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-jadi" type="button" role="tab" aria-controls="nav-barang-jadi" aria-selected="true">Barang Jadi</button>
                                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-setengah-jadi" type="button" role="tab" aria-controls="nav-barang-setengah-jadi" aria-selected="false">Barang Setengah Jadi</button>
                                <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-scrap" type="button" role="tab" aria-controls="nav-scrap" aria-selected="false">Scrap</button>
                                <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-material-return" type="button" role="tab" aria-controls="nav-material-return" aria-selected="false">Material Return</button>
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
                                                    <th>Qty Target</th>
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
                                        <table class="table table-bordered nowrap table-hover-tobasurimi tableBarangDigunakan" id="tableBarangDigunakan" width="100%" cellspacing="0">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="width: 10px;">No</th>
                                                    <th>Referensi</th>
                                                    <th>Kode Barang</th>
                                                    <th>Jenis Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th>Satuan</th>
                                                    <th>Jumlah Request</th>
                                                    <th>Jumlah Digunakan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="body-table-barang-digunakan" id="body-table-barang-digunakan">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-barang-setengah-jadi" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select" name="" id="barang_setengah_jadi">
                                                <option value="" selected disabled></option>
                                                <?php foreach ($barangData as $barang) : ?>
                                                    <option data-code="<?= $barang->kode_barang ?>" data-unit="<?= $barang->unit ?>" value="<?= $barang->id ?>"><?= $barang->nama_barang ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="floatingInput">Nama Barang</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control target input-picker" id="setengah_jadi_barang_code" disabled>
                                            <label for="floatingInput">Kode Barang</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control target input-picker" id="setengah_jadi_barang_unit" disabled>
                                            <label for="floatingInput">Satuan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" value="<?= !empty($dataWorkOrders) ? formatter($dataWorkOrders->target, "STR_TO_INT") : ""; ?>" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" id="barangSetengahJadiQty">
                                            <label for="floatingInput">Qty</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <button type="button" class="btn btn-primary btn-pilih" id="addBarangSetengahJadi">Pilih</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table nowrap table-hover-tobasurimi" id="barangSetengahJadiDataTable" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                                <th onclick="changeSort('nama_satuan')" class="sort">Nama Satuan</th>
                                                <th onclick="changeSort('target')" class="sort">Jumlah</th>
                                                <th onclick="changeSort('target')" class="sort">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-scrap" role="tabpanel" aria-labelledby="nav-contact-tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select" name="" id="scrap">
                                                <option value="" selected disabled></option>
                                                <?php foreach ($barangData as $barang) : ?>
                                                    <option data-code="<?= $barang->kode_barang ?>" data-unit="<?= $barang->unit ?>" value="<?= $barang->id ?>"><?= $barang->nama_barang ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="floatingInput">Nama Barang</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control target input-picker" id="scrap_code" disabled>
                                            <label for="floatingInput">Kode Barang</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control target input-picker" id="scrap_unit" disabled>
                                            <label for="floatingInput">Satuan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" value="<?= !empty($dataWorkOrders) ? formatter($dataWorkOrders->target, "STR_TO_INT") : ""; ?>" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" name="" id="scrapQty">
                                            <label for="floatingInput">Qty</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <button type="button" class="btn btn-primary btn-pilih" id="addScrap">Pilih</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table nowrap table-hover-tobasurimi" id="scrapDataTable" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                                <th onclick="changeSort('nama_satuan')" class="sort">Nama Satuan</th>
                                                <th onclick="changeSort('target')" class="sort">Jumlah</th>
                                                <th onclick="changeSort('target')" class="sort">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-material-return" role="tabpanel" aria-labelledby="nav-contact-tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select" name="" id="materialReturn">
                                                <option value="" selected disabled></option>
                                                <?php foreach ($barangData as $barang) : ?>
                                                    <option data-code="<?= $barang->kode_barang ?>" data-unit="<?= $barang->unit ?>" value="<?= $barang->id ?>"><?= $barang->nama_barang ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="floatingInput">Nama Barang</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control target input-picker" id="materialReturnCode" disabled>
                                            <label for="floatingInput">Kode Barang</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control target input-picker" id="materialReturnUnit" disabled>
                                            <label for="floatingInput">Satuan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" value="<?= !empty($dataWorkOrders) ? formatter($dataWorkOrders->target, "STR_TO_INT") : ""; ?>" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" name="" id="materialReturnQty">
                                            <label for="floatingInput">Qty</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <button type="button" class="btn btn-primary btn-pilih" id="addMaterialReturn">Pilih</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table nowrap table-hover-tobasurimi" id="materialReturnDataTable" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                                <th onclick="changeSort('nama_satuan')" class="sort">Nama Satuan</th>
                                                <th onclick="changeSort('target')" class="sort">Jumlah</th>
                                                <th onclick="changeSort('target')" class="sort">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

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

    $(document).ready(function() {
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
            format: 'dd/mm/yyyy'
        });

        //CSS SELECT2 FLOATING LABEL
        $('.barang_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.barang_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.barang_id')
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

        var validator = $(".create-form").validate({
            rules: {
                barang_id: {
                    required: true
                },
                production_amt: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                target: {
                    required: true
                }
            },
            messages: {
                barang_id: {
                    required: "Barang wajib diisi"
                },
                production_amt: {
                    required: "Hasil wajib diisi"
                },
                satuan_id: {
                    required: "Satuan wajib diisi"
                },
                target: {
                    required: "Target wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                console.log(elem);
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

        $(".btn-submit-form").click(function() {
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
                        const data = new FormData(document.querySelector(".create-form"));
                        const id = $(".id").val();

                        const barangSetengahJadiItems = barangSetengahJadiDataTable.rows().data().toArray();
                        data.append("barang_setengah_jadi", JSON.stringify(barangSetengahJadiItems));

                        const scrapItems = scrapDataTable.rows().data().toArray();
                        data.append("scrap", JSON.stringify(scrapItems));

                        const materialReturnItems = materialReturnDataTable.rows().data().toArray();
                        data.append("material_return", JSON.stringify(materialReturnItems));

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
                                                window.location.href = "<?= base_url("production-result/"); ?>" + id;
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
                                    console.log(response)
                                    csrf.val(response.token);
                                    if (response.status) {
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                window.location.href = "<?= base_url("production-result/"); ?>" + response.id;
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
                        }
                    }
                })
            }
        });

        // barang setengah jadi
        const barangSetengahJadiDataTable = $('#barangSetengahJadiDataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            // serverSide: true,
            ordering: true,
            order: [
                [1, 'asc']
            ],

            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            // scrollX: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            //responsive: true,
            display: "stripe",
            searching: false,
            columns: [{
                    data: "no",
                    className: "text-center",
                    orderable: false
                },
                {
                    data: "kode_barang",
                    className: "text-center"
                },
                {
                    data: "nama_barang",
                    className: "text-center"
                },
                {
                    data: "nama_satuan",
                    className: "text-center"
                },
                {
                    data: "jumlah",
                    className: "text-center"
                },
                {
                    data: "id",
                    className: "text-center actions",
                    searchable: false,
                    sortable: false,
                    render: function(data, type, row) {
                        return `
                        <div class="mt-0">
                            <button class="btn btn-danger" data-action="delete-itemSetengahJadi">
                                Delete
                            </button>
                        </div>
                    `
                    }
                }
            ],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        $('#barang_setengah_jadi').change(function() {
            const selectedEl = $(this).find('option:selected');
            const data = selectedEl.data();

            $('#setengah_jadi_barang_code').val(data.code);
            $('#setengah_jadi_barang_unit').val(data.unit);
        });

        $('#addBarangSetengahJadi').click(function() {

            const itemId = $('#barang_setengah_jadi').val();
            const itemQty = $('#barangSetengahJadiQty').val();

            if (itemId && itemQty > 0) {
                const itemName = $('#barang_setengah_jadi option:selected').text();
                const itemCode = $('#setengah_jadi_barang_code').val();
                const itemUnit = $('#setengah_jadi_barang_unit').val();
                const itemData = {
                    id: itemId,
                    kode_barang: itemCode,
                    nama_barang: itemName,
                    nama_satuan: itemUnit,
                    jumlah: itemQty
                };
                barangSetengahJadiDataTable.row.add(itemData).draw(false);

                // clear input
                $('#barang_setengah_jadi').val('').trigger('change');
                $('#barangSetengahJadiQty').val('');
            }

        });

        $('#barangSetengahJadiDataTable').on('click', '[data-action="delete-itemSetengahJadi"]', function() {
            barangSetengahJadiDataTable.row($(this).parent().parent()).remove().draw();
        });
        // barang setengah jadi end

        // scrap
        const scrapDataTable = $('#scrapDataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            // serverSide: true,
            ordering: true,
            order: [
                [1, 'asc']
            ],

            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            // scrollX: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            //responsive: true,
            display: "stripe",
            searching: false,
            columns: [{
                    data: "no",
                    className: "text-center",
                    orderable: false
                },
                {
                    data: "kode_barang",
                    className: "text-center"
                },
                {
                    data: "nama_barang",
                    className: "text-center"
                },
                {
                    data: "nama_satuan",
                    className: "text-center"
                },
                {
                    data: "jumlah",
                    className: "text-center"
                },
                {
                    data: "id",
                    className: "text-center actions",
                    searchable: false,
                    sortable: false,
                    render: function(data, type, row) {
                        return `
                        <div class="mt-0">
                            <button class="btn btn-danger" data-action="delete-scrap">
                                Delete
                            </button>
                        </div>
                    `
                    }
                }
            ],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        $('#scrap').change(function() {
            const selectedEl = $(this).find('option:selected');
            const data = selectedEl.data();

            $('#scrap_code').val(data.code);
            $('#scrap_unit').val(data.unit);
        });

        $('#addScrap').click(function() {

            const itemId = $('#scrap').val();
            const itemQty = $('#scrapQty').val();

            if (itemId && itemQty > 0) {
                const itemName = $('#scrap option:selected').text();
                const itemCode = $('#scrap_code').val();
                const itemUnit = $('#scrap_unit').val();
                const itemData = {
                    id: itemId,
                    kode_barang: itemCode,
                    nama_barang: itemName,
                    nama_satuan: itemUnit,
                    jumlah: itemQty
                };
                scrapDataTable.row.add(itemData).draw(false);

                // clear input
                $('#scrap').val('').trigger('change');
                $('#scrapQty').val('');
            }

        });

        $('#scrapDataTable').on('click', '[data-action="delete-scrap"]', function() {
            scrapDataTable.row($(this).parent().parent()).remove().draw();
        });
        // scrap end

        // material return
        const materialReturnDataTable = $('#materialReturnDataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            // serverSide: true,
            ordering: true,
            order: [
                [1, 'asc']
            ],

            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            // scrollX: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            //responsive: true,
            display: "stripe",
            searching: false,
            columns: [{
                    data: "no",
                    className: "text-center",
                    orderable: false
                },
                {
                    data: "kode_barang",
                    className: "text-center"
                },
                {
                    data: "nama_barang",
                    className: "text-center"
                },
                {
                    data: "nama_satuan",
                    className: "text-center"
                },
                {
                    data: "jumlah",
                    className: "text-center"
                },
                {
                    data: "id",
                    className: "text-center actions",
                    searchable: false,
                    sortable: false,
                    render: function(data, type, row) {
                        return `
                        <div class="mt-0">
                            <button class="btn btn-danger" data-action="delete-material-return">
                                Delete
                            </button>
                        </div>
                    `
                    }
                }
            ],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        $('#materialReturn').change(function() {
            const selectedEl = $(this).find('option:selected');
            const data = selectedEl.data();

            $('#materialReturnCode').val(data.code);
            $('#materialReturnUnit').val(data.unit);
        });

        $('#addMaterialReturn').click(function() {

            const itemId = $('#materialReturn').val();
            const itemQty = $('#materialReturnQty').val();

            if (itemId && itemQty > 0) {
                const itemName = $('#materialReturn option:selected').text();
                const itemCode = $('#materialReturnCode').val();
                const itemUnit = $('#materialReturnUnit').val();
                const itemData = {
                    id: itemId,
                    kode_barang: itemCode,
                    nama_barang: itemName,
                    nama_satuan: itemUnit,
                    jumlah: itemQty
                };
                materialReturnDataTable.row.add(itemData).draw(false);

                // clear input
                $('#materialReturn').val('').trigger('change');
                $('#materialReturnQty').val('');
            }

        });

        $('#materialReturnDataTable').on('click', '[data-action="delete-material-return"]', function() {
            materialReturnDataTable.row($(this).parent().parent()).remove().draw();
        });
        // material return end


        if (id) {
            const barangSetengahJadiData = <?= json_encode($barangSetengahJadi ?? []) ?>;
            barangSetengahJadiDataTable.rows.add(barangSetengahJadiData).draw(false);

            const scrapData = <?= json_encode($scrap ?? []); ?>;
            scrapDataTable.rows.add(scrapData).draw(false);

            const materialReturnData = <?= json_encode($materialReturn ?? []); ?>;
            materialReturnDataTable.rows.add(materialReturnData).draw(false);
        }

        $(".kode_produksi").change(function() {
            if ($(".kode_produksi option:selected").val()) {
                let nama_barang = $(".kode_produksi option:selected").data("nama-barang") ? $(".kode_produksi option:selected").data("nama-barang") : "";
                let standart_production = $(".kode_produksi option:selected").data("standart-production") ? $(".kode_produksi option:selected").data("standart-production") : "";
                let warehouse_id = $(".kode_produksi option:selected").data("warehouse") ? $(".kode_produksi option:selected").data("warehouse") : "";
                let divisi_id = $(".kode_produksi option:selected").data("divisi") ? $(".kode_produksi option:selected").data("divisi") : "";

                $(".barang_jadi").val(nama_barang);
                $(".standart_production").val(standart_production);
                $(".warehouse_id_order").val(warehouse_id).change();
                $(".department_id_order").val(divisi_id).change();
                $.ajax({
                    url: `<?= base_url('production-result/material-request'); ?>`,
                    method: "GET",
                    data: {
                        kode_produksi: $(this).val(),
                    },
                    dataType: "json",
                    success: function(res) {
                        console.log(res);
                        if (res && res.data && Array.isArray(res.data)) {
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
                            console.error('Invalid or empty response data:', res);
                        }
                    }
                });
                $.ajax({
                    url: `<?= base_url('production-result/list-work-order'); ?>`,
                    method: "GET",
                    data: {
                        kode_produksi: $(this).val(),
                    },
                    dataType: "json",
                    success: function(res) {
                        // console.log(res);
                        list_items_barang_jadi = [];
                        res.data.forEach(function(item) {
                            // Push each item into the list_items_barang_jadi array
                            list_items_barang_jadi.push({
                                'barang_detail_id': getID(),
                                'barang1_id': item.barang1_id,
                                'barang2_id': item.barang2_id,
                                'barang_name': item.barang_name,
                                'kode_barang': item.kode_barang,
                                'kode_satuan': item.kode_satuan,
                                'nama_barang': item.nama_barang,
                                'note': item.note,
                                'qty': item.qty,
                                'type_barang': item.type_barang,
                                'type_barang_text': item.type_barang_text,
                                'unit': item.unit,
                            });
                        });
                        drawTableBarangJadi();
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

                $("#date_request").val(date_request);
                $(".user_request").val(user_request);
                $(".warehouse_id_request").val(warehouse_id).change();
                $(".department_id_request").val(divisi_id).change();
                $.ajax({
                    url: `<?= base_url('production-result/list-material-request'); ?>`,
                    method: "GET",
                    data: {
                        kode_request: $(this).val(),
                    },
                    dataType: "json",
                    success: function(res) {
                        // console.log(res);
                        list_items_barang_digunakan = [];
                        res.data.forEach(function(item) {
                            // Push each item into the list_items_barang_jadi array
                            list_items_barang_digunakan.push({
                                'barang_detail_id': getID(),
                                'barang1_id': item.barang1_id,
                                'barang2_id': item.barang2_id,
                                'barang_name': item.barang_name,
                                'kode_barang': item.kode_barang,
                                'satuan': item.satuan,
                                'nama_barang': item.nama_barang,
                                'note': item.note,
                                'qty': item.qty,
                                'ref_no': item.ref_no,
                                'type_barang': item.type_barang,
                                'type_barang_text': item.type_barang_text,
                                'unit': item.unit,
                            });
                        });
                        drawTableBarangDigunakan();
                    }
                });
            } else {
                $("#date_request").val("");
                $(".user_request").val("");
                $(".warehouse_id_request").val("").change();
                $(".divisi_id_request").val("").change();
            }
        })
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
    const resetFormDetail = function() {
        $(".barang_detail_id").val('');
        $(".barang_id").val('').val(null).change()
        $(".barang_spesifikasi_id").val('');
        $(".kode").val(null).change()
        $(".nama_barang").val('')
        $(".satuan").val('')
        $(".satuan_id").val('')
        $(".qty").val('')
        $(".keterangan").val('')
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
            list_items_barang_digunakan.map(item => {
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.ref_no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.type_barang_text + '</td>';
                row += '<td>' + item.barang_name + '</td>';
                row += '<td>' + item.satuan + '</td>';
                row += '<td>' + item.qty + '</td>';
                row += '<td>' + `
                <input class="form-control qty-barang-digunakan" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text">` +
                    '</td>';

                no++;
            });
            $('.body-table-barang-digunakan').append(row);
        }
    }
    const drawTableBarangJadi = function() {
        $('.body-table-barang-jadi').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items_barang_jadi.length === 0) {
            row += `
                    <tr>
                        <td colspan="7" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.tfoot').append(row);
        } else {
            list_items_barang_jadi.map(item => {
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.type_barang_text + '</td>';
                row += '<td>' + item.barang_name + '</td>';
                row += '<td>' + item.kode_satuan + '</td>';
                row += '<td>' + item.qty + '</td>';
                row += '<td>' + `
                <input class="form-control qty-barang-jadi" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text">` +
                    '</td>';

                no++;
            });
            $('.body-table-barang-jadi').append(row);
        }
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        if (parseFloat(numericValue) <= 0) {
            inputElement.value = 0;
        } else {
            inputElement.value = numericValue;
        }
    }
</script>

<?= $this->endSection(); ?>
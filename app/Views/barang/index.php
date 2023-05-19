<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="width: 1200px !important; max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control" placeholder="Kode Barang">
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_barang" name="satuan_barang" id="satuan_barang">
                                    <option value=""></option>
                                    <option value="PCE">PCE</option>
                                </select>
                                <label for="floatingInput">Satuan Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_hs" name="satuan_hs" id="satuan_hs">
                                    <option value=""></option>
                                    <option value="500002.00.00: Sutramentah (Tidak Dipintai)">500002.00.00: Sutramentah (Tidak Dipintai)</option>
                                </select>
                                <label for="floatingInput">Satuan HS</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select add_kategori" name="add_kategori" id="add_kategori">
                                    <option value=""></option>
                                    <option value="Non Header">Non Header</option>
                                </select>
                                <label for="floatingInput">Kategori</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select add_header" name="add_header" id="add_header">
                                    <option value=""></option>
                                    <option value="30152T.C02">30152T.C02</option>
                                </select>
                                <label for="floatingInput">Header</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_pembelian_1" name="akun_pembelian_1" id="akun_pembelian_1">
                                    <option value=""></option>
                                    <option value="30152T.C02">30152T.C02</option>
                                </select>
                                <label for="floatingInput">Akun Pembelian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_pembelian_2" name="akun_pembelian_2" id="akun_pembelian_2">
                                    <option value=""></option>
                                    <option value="30152T.C02">30152T.C02</option>
                                </select>
                                <label for="floatingInput">Akun Pembelian</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control" placeholder="Saldo Awal">
                                <label for="floatingInput">Saldo Awal</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard delete-btn">Delete</button>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3">Discard</button>
                    <button type="submit" class="btn btn-submit-form">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="mb-2">
        <h5 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Barang</h5>
        <div class="form-row mx-0">
            <div class="form-group col float-left">
                <button class="btn btn-show-form btn-add btn-block" data-btn="create-modal" style="width: 176px;">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add New
                </button>
            </div>
        </div>
        <div class="form-row mx-0">
            <div class="form-group col d-flex">
                <input class="form-control search component-right-button" placeholder="Search" value="" />
                &nbsp;
                <select class="form-select component-right-button kategori" aria-label="Floating label select example">
                    <option value=""></option>
                    <option value="All">Kategori: All</option>
                </select>
                &nbsp;
                <select class="form-select component-right-button header" aria-label="Floating label select example">
                    <option value=""></option>
                    <option value="Item">Header: Item</option>
                </select>
                &nbsp;
                <select class="form-select component-right-button periode" aria-label="Floating label select example">
                    <option value=""></option>
                    <option value="PR-2023-01">Periode: PR-2023-01</option>
                </select>
                &nbsp;
                <select class="form-select component-right-button status" aria-label="Floating label select example">
                    <option value=""></option>
                    <option value="Active">Status: Active</option>
                </select>
                &nbsp;
                <button class="btn btn-add btn-block" style="width: 176px;">
                    Export&nbsp;<i class="fa fa-chevron-down fa-sm mr-2" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-3">
                <div class="card card-template border-left-success">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="mb-2 font-weight-bold label-card">
                                    1015
                                </h5>
                                <h5 class="font-weight-normal label-sub-card">Total Data</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-template border-left-success">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="mb-2 font-weight-bold label-card">
                                    18
                                </h5>
                                <h5 class="font-weight-normal label-sub-card">Kategori</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-template border-left-success">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="mb-2 font-weight-bold label-card">
                                    PR-2023-01
                                </h5>
                                <h5 class="font-weight-normal label-sub-card">Periode</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-template border-left-success">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="mb-2 font-weight-bold label-card">
                                    Active
                                </h5>
                                <h5 class="font-weight-normal label-sub-card">Status</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Kategori</th>
                    <th>Kode HS</th>
                    <th>Header</th>
                    <th>Akun Pembelian</th>
                    <th>Akun Penjualan</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody class="body-table" id="body-table" style="cursor: pointer;">

            </tbody>
        </table>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    $(document).ready(function() {
        $('.kategori').select2({
            placeholder: "Kategori: All",
            theme: "bootstrap-5",
            allowClear: true
        })

        $('.header').select2({
            placeholder: "Header: All",
            theme: "bootstrap-5",
            allowClear: true
        })

        $('.periode').select2({
            placeholder: "Periode: All",
            theme: "bootstrap-5",
            allowClear: true
        })

        $('.status').select2({
            placeholder: "Status: All",
            theme: "bootstrap-5",
            allowClear: true
        })

        // SATUAN BARANG
        $('.satuan_barang').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".satuan_barang")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".satuan_barang")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".satuan_barang")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SATUAN HS
        $('.satuan_hs').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.satuan_hs')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.satuan_hs')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan_hs')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // AKUN PEMBELIAN 1
        $('.akun_pembelian_1').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.akun_pembelian_1')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.akun_pembelian_1')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.akun_pembelian_1')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // AKUN PEMBELIAN 2
        $('.akun_pembelian_2').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.akun_pembelian_2')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.akun_pembelian_2')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.akun_pembelian_2')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KATEGORI
        $('.add_kategori').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.add_kategori')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.add_kategori')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.add_kategori')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // HEADER
        $('.add_header').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.add_header')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.add_header')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.add_header')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Add New");
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })
    })
</script>

<?= $this->endSection(); ?>
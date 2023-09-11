<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <input autocomplete="one-time-code" type="hidden" class="parent" name="parent" id="parent" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3" style="height: 50px;">
                                <div for="floatingInput" class="label-modal-master-barang">Product Spec</div>
                                <div class="form-check form-check-inline">
                                    <input autocomplete="one-time-code" class="form-check-input" type="radio" name="productSpec" id="inlineRadio1" value="single">
                                    <label class="form-check-label" for="inlineRadio1">Single Spec</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input autocomplete="one-time-code" class="form-check-input" type="radio" name="productSpec" id="inlineRadio2" value="multi" checked>
                                    <label class="form-check-label" for="inlineRadio2">Multi Spec</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select parent_id" name="parent_id" id="parent_id">
                                    <option value=""></option>
                                    <?php foreach ($dataBarangParent as $parent): ?>
                                    <option value="<?= $parent->id ?>"><?= "$parent->kode_barang - $parent->nama_barang" ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Parent Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control kode_barang" name="kode_barang" id="kode_barang" placeholder="Kode Barang" disabled>
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_barang" name="nama_barang" id="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="is_parent" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control spek" name="spek" id="spek" placeholder="Spesifikasi">
                                    <label for="floatingInput">Spesifikasi</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="number" class="form-control stok" name="stok" id="stok" disabled>
                                    <label for="floatingInput">Stok</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select satuan_id" name="satuan_id" id="satuan_id">
                                        <option value=""></option>
                                        <?php foreach ($satuanData as $satuan): ?>
                                        <option value="<?= $satuan->id ?>"><?= $satuan->nama_satuan ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput">Satuan Barang</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" onkeyup="formatNumber(this)" class="form-control harga_barang" name="harga_barang" id="harga_barang" placeholder="Harga Barang">
                                    <label for="floatingInput">Harga Barang</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select type" onchange="changeType()" name="type" id="type" aria-label="Floating label select example">
                                        <option value="">Pilih Tipe</option>
                                        <option value="BAHAN PENOLONG LOKAL">Bahan Penolong Lokal</option>
                                        <option value="BAHAN PENOLONG IMPORT">Bahan Penolong Import</option>
                                        <option value="BAHAN BAKU IMPORT">Bahan Baku Import</option>
                                        <option value="BAHAN BAKU LOKAL">Bahan Baku Lokal</option>
                                    </select>

                                    <label for="floatingInput">Tipe Supplier (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select multiple class="form-select supplier_id" name="supplier_id[]" id="supplier_id[]">

                                    </select>
                                    <label for="floatingInput">Supplier (Opsional)</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kategori_id" name="kategori_id" id="kategori_id">
                                        <option value=""></option>
                                        <?php foreach ($kategoriBarangData as $kategori): ?>
                                        <option value="<?= $kategori->id ?>"><?= $kategori->value ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput">Kategori (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select hs_id" name="hs_id" id="hs_id">
                                        <option value=""></option>
                                        <?php foreach ($dataKodeHS as $dataAccount): ?>
                                        <option value="<?= $dataAccount->id ?>"><?= $dataAccount->code ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput">Kode HS (Opsional)</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select ap_id" name="ap_id" id="ap_id">
                                        <option value=""></option>
                                        <?php foreach ($aparData as $accountData): ?>
                                        <option value="<?= $accountData->id ?>"><?= "[$accountData->no_sub]$accountData->nama_sub" ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput">Akun Pembelian (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select ar_id" name="ar_id" id="ar_id">
                                        <option value=""></option>
                                        <?php foreach ($aparData as $accountData): ?>
                                        <option value="<?= $accountData->id ?>"><?= "[$accountData->no_sub]$accountData->nama_sub" ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput">Akun Penjualan (Opsional)</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" onkeyup="formatNumber(this)" class="form-control" name="tax" id="tax" placeholder="Pajak Barang">
                                    <label for="floatingInput">Tax  (Opsional) (%)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5 class="modal-sub-title">Spesifikasi</h5>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-add-row btn-add btn-block float-right" style="width: 106px;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table-inside table-bordered nowrap table-hover-tobasurimi table-add-modal-master-barang" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Spesifikasi</th>
                                <th>Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-spek" id="body-detail-spek" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Barang</h1>
    <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </button>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end row-col-spp row-form-select-master-barang-index">
            <div class="col mb-3">
                <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Nama Barang / Kode Barang" value="" />
            </div>
            <div class="col mb-3">
                <select class="form-select kategori form-out-search" name="kategori" id="kategori" aria-label="Floating label select example">
                    <option value="">Kategori: All</option>
                    <?php
                    if (!empty($dataKategori)) {
                        foreach ($dataKategori as $kategori) {
                    ?>
                            <option value="<?= $kategori["id"]; ?>"><?= $kategori["value"]; ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th onclick="changeSort('parent_barang')" class="sort">Parent Barang</th>
                            <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                            <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                            <th onclick="changeSort('type')" class="sort">Tipe Supplier</th>
                            <th onclick="changeSort('harga_barang')" class="sort">Harga Barang</th>
                            <th onclick="changeSort('kode_satuan')" class="sort">Satuan</th>
                            <th onclick="changeSort('kategori')" class="sort">Kategori</th>
                            <th onclick="changeSort('code_hs')" class="sort">Kode HS</th>
                            <th onclick="changeSort('sub_akun_ap')" class="sort">Akun Pembelian</th>
                            <th onclick="changeSort('sub_akun_ar')" class="sort">Akun Penjualan</th>
                            <th onclick="changeSort('stok')" class="sort">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table" style="cursor: pointer;">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "parent_barang";
    let sortType = "asc";
    let list_spek = [];
    var row_detail = 0;
    let changeParent = true;

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[1, 'asc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("barang/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.kategori = $(".kategori").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function (settings, json) {    
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
            sortable: false
        }, 
        {
            data: "parent_barang",
            className: "text-center"
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
            data: "type",
            className: "text-center"
        },
        {
            data: "harga_barang",
            className: "text-center"
        },
        {
            data: "kode_satuan",
            className: "text-center"
        },
        {
            data: "kategori",
            className: "text-center"
        },
        {
            data: "code_hs",
            className: "text-center"
        },
        {
            data: "sub_akun_ap",
            className: "text-center"
        },
        {
            data: "sub_akun_ar",
            className: "text-center"
        },
        {
            data: "stok",
            className: "text-center",
            render: function(data, type, row) {
                return `
                <div class="text-danger">
                ${data}
                </div>
                `
            }
        }],
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

    $(document).ready(function() {
        $('.create-form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $('.kategori').select2({
            placeholder: "Kategori: All",
            theme: "bootstrap-5",
            allowClear: true
        })

        // SUPPLIER
        $('.supplier_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".supplier_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".supplier_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".supplier_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

         // PARENT BARANG
         $('.parent_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".parent_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".parent_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".parent_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SATUAN BARANG
        $('.satuan_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".satuan_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".satuan_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".satuan_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SATUAN HS
        $('.satuan_hs').select2({
            placeholder: "",
            theme: "bootstrap-5",
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
        $('.ap_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.ap_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.ap_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.ap_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // AKUN PEMBELIAN 2
        $('.ar_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.ar_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.ar_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.ar_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KATEGORI
        $('.kategori_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kategori_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kategori_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kategori_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KODE HS
        $('.hs_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.hs_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.hs_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.hs_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $(".create-form").validate({
            rules: {
                nama_barang: {
                    required: true
                },
                spesifikasi: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                harga_barang: {
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
                harga_barang: {
                    required: "Harga wajib diisi"
                },
                satuan_id: {
                    required: "Satuan wajib diisi"
                },
                stok: {
                    required: "Stok wajib diisi"
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

        $(".btn-show-form").click(function() {
            $(".parent_id").removeAttr('disabled');
            validator.resetForm();
            validator.reset();
            // $(".body-detail-spek").empty()
            // list_spek = [];
            // row_detail = 0;
            $('.kode_barang').rules('add', {
                required: true
            });
            $(".is_parent").css("display", "none");
            $('.stok').rules('remove', 'required');
            $("#parent_id").val('').trigger('change');
            $("#kode_barang").val('');
            $("#nama_barang").val('');
            $(".type").val();
            $('.spek').val();
            $(".supplier_id").val([]).change();
            $(".supplier_id").empty();
            $('.parent').val();
            $('.kode_barang').val();
            $('.nama_barang').val();
            $('.harga_barang').val();
            $(".id").val("");
            $(".title-name").text("Tambah");
            $(".delete-btn").css('display', 'none');
            $(".stok").attr("readonly", false);
            $(".kode_barang").attr("readonly", false);

            /* $.ajax({
                url: `<?= base_url("barang/dropdown/parent"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".parent_id").empty()
                    $(".parent_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".parent_id").append(`<option value="${item.id}">${item.kode_barang} - ${item.nama_barang}</option>`)
                    })

                    $(".parent_id").val("").change();
                }
            }) */

            /* $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'kategori_barang'
                },
                dataType: "json",
                success: function(res) {
                    $(".kategori_id").empty()
                    $(".kategori_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".kategori_id").append(`<option value="${item.id}">${item.value}</option>`)
                    })

                    $(".kategori_id").val("").change();
                }
            }) */

            /* $.ajax({
                url: `<?= base_url("ap-ar/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".ap_id").empty()
                    $(".ar_id").empty()

                    $(".ap_id").append(`<option value=""></option>`)
                    $(".ar_id").append(`<option value=""></option>`)

                    res.data.forEach(function(item) {
                        $(".ap_id").append(`<option value="${item.id}">[${item.no_sub}]${item.nama_sub}</option>`)
                        $(".ar_id").append(`<option value="${item.id}">[${item.no_sub}]${item.nama_sub}</option>`)
                    })

                    $(".ap_id").val("").change();
                    $(".ar_id").val("").change();
                }
            }) */

            /* $.ajax({
                url: `<?= base_url("satuan/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".satuan_id").empty()

                    $(".satuan_id").append(`<option value=""></option>`)

                    res.data.forEach(function(item) {
                        $(".satuan_id").append(`<option value="${item.id}">${item.nama_satuan}</option>`)
                    })

                    $(".satuan_id").val("").change();
                }
            }) */

            /* $.ajax({
                url: `<?= base_url("hs-code/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".hs_id").empty()

                    $(".hs_id").append(`<option value=""></option>`)

                    res.data.forEach(function(item) {
                        $(".hs_id").append(`<option value="${item.id}">${item.code}</option>`)
                    })

                    $(".hs_id").val("").change();
                    $(".add-modal").modal("show")
                }
            }) */

            $(".add-modal").modal("show")
        })

        $('[name="productSpec"]').change(function() {
            const specVal = $(this).val();

            if (specVal == 'single') {
                $('#parent_id').val('').trigger('change.select2');
                $('#parent_id').prop('disabled', true);
                haciu2();
            } else {
                $('#parent_id').prop('disabled', false);
                haciu1();
            }
        });

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            changeParent = false;
            $('.stok').rules('remove', 'required');
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");
            $(".stok").attr("readonly", true);
            $(".kode_barang").attr("readonly", true);
            // $(".body-detail-spek").empty()
            // row_detail = 0;
            // list_spek = [];
            $(".parent_id").attr('disabled', 'true');

            $.ajax({
                url: "<?= base_url("barang/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        // let new_spek = JSON.parse(res?.data?.spek);
                        // let tag_html = "";
                        // console.log(new_spek);
                        // new_spek.forEach((item) => {
                        //     row_detail++;
                        //     list_spek.push(
                        //     {
                        //         row: row_detail,
                        //         display: "",
                        //     })
                        //     tag_html += `<tr class="table_${row_detail}">`;
                        //     tag_html += `<td>`;
                        //     tag_html += `<input autocomplete="one-time-code" type="text" onkeypress="return lettersOnly(event)" value="${item}" class="form-control spek_${row_detail}" id="spek_${row_detail}" name="spek_${row_detail}">`;
                        //     tag_html += `</td>`;
                        //     tag_html += `<td>`;
                        //     tag_html += `<button onclick='deleteChildRow(${row_detail})'>X</button>`;
                        //     tag_html += `</td>`;
                        //     tag_html += `</tr>`;
                        // })

                        // $(".body-detail-spek").append(tag_html)

                        $(".id").val(id);
                        $('.parent').val(res?.data?.parent_id);
                        $('.kode_barang').rules('remove', 'required');
                        if(res?.data?.parent_id !== "0" || res?.data?.spec_type == 'single')
                        {
                            $(".is_parent").css("display", "");
                            $('.stok').rules('remove', 'required');
                            $('.harga_barang').rules('add', {
                                required: true
                            });
                            $('.satuan_id').rules('add', {
                                required: true
                            });
                            $('.tax').rules('add', {
                                required: true
                            });
                        }
                        else
                        {
                            $(".is_parent").css("display", "none");
                            $('.stok').rules('remove', 'required');
                            $('.harga_barang').rules('remove', 'required');
                            $('.satuan_id').rules('remove', 'required');
                            $('.tax').rules('remove', 'required');
                        }

                        $(`[name="productSpec"][value="${res?.data?.spec_type}"]`).prop('checked', true);
                        $(".kode_barang").val(res?.data?.kode_barang);
                        $(".spek").val(res?.data?.spek);
                        $(".nama_barang").val(res?.data?.nama_barang);
                        $(".stok").val(res?.data?.stok);
                        $(".harga_barang").val(res?.data?.harga_barang ? Number(res.data.harga_barang).toLocaleString() : 0);
                        $('#tax').val(res.data.tax);
                        
                        validator.resetForm();
                        validator.reset();

                        $.ajax({
                            url: `<?= base_url("barang/dropdown/parent"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".parent_id").empty()
                                $(".parent_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    if(item.id == Number(res?.data?.parent_id))
                                    {
                                        $(".parent_id").append(`<option selected value="${item.id}">${item.kode_barang} - ${item.nama_barang}</option>`)
                                    }
                                    else
                                    {
                                        $(".parent_id").append(`<option value="${item.id}">${item.kode_barang} - ${item.nama_barang}</option>`)
                                    }
                                })
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                name: 'kategori_barang'
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".kategori_id").empty()
                                $(".kategori_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".kategori_id").append(`<option value="${item.id}">${item.value}</option>`)
                                })

                                $(".kategori_id").val(res?.data?.kategori_id).change();
                            }
                        })

                        $(".ap_id").val(res?.data?.ap_id).change();
                        $(".ar_id").val(res?.data?.ar_id).change();
                        /* $.ajax({
                            url: `<?= base_url("ap-ar/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".ap_id").empty()
                                $(".ar_id").empty()

                                $(".ap_id").append(`<option value=""></option>`)
                                $(".ar_id").append(`<option value=""></option>`)

                                result.data.forEach(function(item) {
                                    $(".ap_id").append(`<option value="${item.id}">[${item.no_sub}]${item.nama_sub}</option>`)
                                    $(".ar_id").append(`<option value="${item.id}">[${item.no_sub}]${item.nama_sub}</option>`)
                                })

                                $(".ap_id").val(res?.data?.ap_id).change();
                                $(".ar_id").val(res?.data?.ar_id).change();
                            }
                        }) */

                        $(".supplier_id").empty()
                        $(".type").val(res?.data?.type)

                        let arr_supplier_id = []
                        res?.dataSupplier.forEach(function(item) {
                            arr_supplier_id.push(Number(item.supplier_id));
                        })

                        if(res?.data?.type === "BAHAN BAKU LOKAL")
                        {
                            $.ajax({
                                url: `<?= base_url("supplier-bahan-baku/dropdown"); ?>`,
                                method: "GET",
                                dataType: "json",
                                success: function(result) {
                                    $(".supplier_id").empty()

                                    result.data.forEach(function(item) {
                                        $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                                    })

                                    $(".supplier_id").val(arr_supplier_id).change();
                                }
                            })
                        }
                        if(res?.data?.type === "BAHAN BAKU IMPORT")
                        {
                            $.ajax({
                                url: `<?= base_url("supplier-bahan-baku-import/dropdown"); ?>`,
                                method: "GET",
                                dataType: "json",
                                success: function(result) {
                                    $(".supplier_id").empty()

                                    result.data.forEach(function(item) {
                                        $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                                    })

                                    $(".supplier_id").val(arr_supplier_id).change();
                                }
                            })
                        }
                        if(res?.data?.type === "BAHAN PENOLONG LOKAL")
                        {
                            $.ajax({
                                url: `<?= base_url("supplier-bahan-penolong/dropdown"); ?>`,
                                method: "GET",
                                dataType: "json",
                                success: function(result) {
                                    $(".supplier_id").empty()

                                    result.data.forEach(function(item) {
                                        $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                                    })

                                    $(".supplier_id").val(arr_supplier_id).change();
                                }
                            })
                        }
                        if(res?.data?.type === "BAHAN PENOLONG IMPORT")
                        {
                            $.ajax({
                                url: `<?= base_url("supplier-bahan-penolong-import/dropdown"); ?>`,
                                method: "GET",
                                dataType: "json",
                                success: function(result) {
                                    $(".supplier_id").empty()

                                    result.data.forEach(function(item) {
                                        $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                                    })

                                    $(".supplier_id").val(arr_supplier_id).change();
                                }
                            })
                        }

                        $(".satuan_id").val(res?.data?.satuan_id).change();
                        $(".hs_id").val(res?.data?.hs_id).change();
                        changeParent = true;
                        $(".add-modal").modal("show")
                        /* $.ajax({
                            url: `<?= base_url("satuan/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".satuan_id").empty()

                                $(".satuan_id").append(`<option value=""></option>`)

                                result.data.forEach(function(item) {
                                    $(".satuan_id").append(`<option value="${item.id}">${item.nama_satuan}</option>`)
                                })

                                $(".satuan_id").val(res?.data?.satuan_id).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("hs-code/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".hs_id").empty()

                                $(".hs_id").append(`<option value=""></option>`)

                                result.data.forEach(function(item) {
                                    $(".hs_id").append(`<option value="${item.id}">${item.code}</option>`)
                                })

                                $(".hs_id").val(res?.data?.hs_id).change();
                                changeParent = true;
                                $(".add-modal").modal("show")
                            }
                        }) */
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $(".search").keyup(function () {
            table.ajax.reload();
        })

        $(".kategori").change(function () {
            table.ajax.reload();
        })

        $(".parent_id").change(function () {
            if(changeParent){
            // $(".kode_barang").val("");
            // $(".nama_barang").val("");
            $(".satuan_id").val("").change();
            $(".harga_barang").val("");
            $(".supplier_id").val([]).change();
            $(".kategori_id").val("").change();
            $(".hs_id").val("").change();
            $(".ap_id").val("").change();
            $(".ar_id").val("").change();
            $(".type").val("");
            $(".spek").val("");
            $(".stok").val("");

            if($(".parent_id").val())
            {
                haciu2()
                // $(".is_parent").css("display", "");
                // $('.stok').rules('add', {
                //     required: true
                // });
                // $('.harga_barang').rules('add', {
                //     required: true
                // });
                // $('.type').rules('add', {
                //     required: true
                // });
                // $('.satuan_id').rules('add', {
                //     required: true
                // });
                // $('.kategori_id').rules('add', {
                //     required: true
                // });
                // $('.hs_id').rules('add', {
                //     required: true
                // });
                // $('.ap_id').rules('add', {
                //     required: true
                // });
                // $('.ar_id').rules('add', {
                //     required: true
                // });
            }
            else
            {
                haciu1()
                // $(".is_parent").css("display", "none");
                // $('.stok').rules('remove', 'required');
                // $('.harga_barang').rules('remove', 'required');
                // $('.type').rules('remove', 'required');
                // $('.satuan_id').rules('remove', 'required');
                // $('.kategori_id').rules('remove', 'required');
                // $('.hs_id').rules('remove', 'required');
                // $('.ap_id').rules('remove', 'required');
                // $('.ar_id').rules('remove', 'required');
            }
            }
        })

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
                        let data = new FormData(document.querySelector(".create-form"));

                        data.append("supplier_id", JSON.stringify($('.supplier_id').val()));

                        let id = $(".id").val();

                        // let update_list_spek = [];
                        // console.log(list_spek)
                        // list_spek.forEach((item) => {
                        //     if(item.display != "none")
                        //     {
                        //         update_list_spek.push($(".spek_" + item.row).val());
                        //     }
                        // })

                        // data.append("spek", JSON.stringify(update_list_spek));

                        // UPDATE
                        if(id)
                        {
                            $.ajax({
                                url: "<?= base_url("barang/update"); ?>",
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
                                            table.ajax.reload()
                                            $(".add-modal").modal("hide")
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
                        // CREATE
                        else
                        {
                            $.ajax({
                                url: "<?= base_url("barang/save"); ?>",
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
                                            table.ajax.reload()
                                            $(".add-modal").modal("hide")
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
        })

        $(".delete-btn").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("barang/delete"); ?>",
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
                                    table.ajax.reload()
                                    $(".add-modal").modal("hide")
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
            })
        })
    })

    $(".btn-add-row").click(function() {
        row_detail++;
        list_spek.push(
        {
            row: row_detail,
            display: "",
        })
        
        let tag_html = "";
        tag_html += `<tr class="table_${row_detail}">`;
        tag_html += `<td>`;
        tag_html += `<input autocomplete="one-time-code" type="text" onkeypress="return lettersOnly(event)" class="form-control spek_${row_detail}" id="spek_${row_detail}" name="spek_${row_detail}">`;
        tag_html += `</td>`;
        tag_html += `<td>`;
        tag_html += `<button onclick='deleteChildRow(${row_detail})'>X</button>`;
        tag_html += `</td>`;
        tag_html += `</tr>`;

        $(".body-detail-spek").append(tag_html)
    })

    const deleteChildRow = function(id) {
        $(".table_" + id).css("display", "none")
        let new_list_spek = []
        list_spek.forEach((item) => {
            if(item.row !== id)
            {
                new_list_spek.push(item)
            }
            else
            {
                new_list_spek.push({row: id, display: "none"})
            }
        })

        list_spek = new_list_spek;
    }

    const changeType = function() {
        let value = $(".type").val();
        $(".type").val()
        $(".supplier_id").empty()
        $(".supplier_id").val([]).change()

        if(value === "BAHAN BAKU LOKAL")
        {
            $.ajax({
                url: `<?= base_url("supplier-bahan-baku/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".supplier_id").empty()

                    res.data.forEach(function(item) {
                        $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                    })

                    $(".supplier_id").val([]).change();
                }
            })
        }
        if(value === "BAHAN BAKU IMPORT")
        {
            $.ajax({
                url: `<?= base_url("supplier-bahan-baku-import/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".supplier_id").empty()

                    res.data.forEach(function(item) {
                        $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                    })

                    $(".supplier_id").val([]).change();
                }
            })
        }
        if(value === "BAHAN PENOLONG LOKAL")
        {
            $.ajax({
                url: `<?= base_url("supplier-bahan-penolong/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".supplier_id").empty()

                    res.data.forEach(function(item) {
                        $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                    })

                    $(".supplier_id").val([]).change();
                }
            })
        }
        if(value === "BAHAN PENOLONG IMPORT")
        {
            $.ajax({
                url: `<?= base_url("supplier-bahan-penolong-import/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".supplier_id").empty()

                    res.data.forEach(function(item) {
                        $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                    })

                    $(".supplier_id").val([]).change();
                }
            })
        }
    }

    const changeSort = function(val) {
        if(sort !== val)
        {
            sortType = "ASC";
            sort = val;
        }
        else
        {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const haciu1 = () => {
        $(".is_parent").css("display", "none");
        $('.stok').rules('remove', 'required');
        $('.harga_barang').rules('remove', 'required');
        $('.satuan_id').rules('remove', 'required');
        $('.tax').rules('remove', 'required');
    };

    const haciu2 = () => {
        $(".is_parent").css("display", "");
        $('.stok').rules('add', {
            required: true
        });
        $('.harga_barang').rules('add', {
            required: true
        });
        $('.satuan_id').rules('add', {
            required: true
        });
        $('.tax').rules('add', {
            required: true
        });
    };
</script>

<?= $this->endSection(); ?>
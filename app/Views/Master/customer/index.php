<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Customer</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-lokal" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe_customer" value="LOKAL">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" minlength="16" maxlength="16" class="form-control nik" id="nik" name="nik" placeholder="NIK (Opsional)">
                                <label for="floatingInput">NIK (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no_npwp" id="no_npwp" name="no_npwp" placeholder="NPWP (Opsional)">
                                <label for="floatingInput"> NPWP (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <textarea autocomplete="one-time-code" class="form-control address" id="address" name="address"></textarea>
                                <label for="floatingInput">Alamat</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select province_parent_id" name="province_parent_id" id="province_parent_id" onchange="getCityParent()">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($dataProvinces)) {
                                                foreach ($dataProvinces as $province) {
                                            ?>
                                                    <option value="<?= $province["id"]; ?>"><?= $province["province_name"]; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="floatingInput">Provinsi (Opsional)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select city_parent_id" name="city_parent_id" id="city_parent_id">
                                            <option value="" data-code=""></option>
                                        </select>
                                        <label for="floatingInput">Kota (Opsional)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" minlength="5" maxlength="5" class="form-control parent_postal_code" id="parent_postal_code" name="parent_postal_code" placeholder="Kode Pos (Opsional)">
                                        <label for="floatingInput">Kode Pos (Opsional)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control phone" id="phone" name="phone" placeholder="No. Telepon (Opsional)">
                                <label for="floatingInput">No. Telepon (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control contact_person" id="contact_person" name="contact_person" placeholder="Contact Person (Opsional)">
                                <label for="floatingInput">Nama PIC (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="email" class="form-control email" id="email" name="email" placeholder="Email (Opsional)">
                                <label for="floatingInput">Email (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select currency" id="currency" name="currency">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Mata Uang (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select termin" id="termin" name="termin">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Termin (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" value="0" type="text" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);" class="form-control piutang" id="piutang" name="piutang" placeholder="Limit Piutang">
                                <label for="floatingInput">Limit Piutang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tipe_pelanggan" name="tipe_pelanggan" id="tipe_pelanggan">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Tipe Pelanggan (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select jenis_penjualan" name="jenis_penjualan" id="jenis_penjualan" <?= !empty($data) ? 'disabled' : ''; ?>>
                                    <option value=""></option>
                                    <option value="1" <?= !empty($data) ? ($data->jenis_penjualan == 1 ? "selected" : "") : ""; ?>>By Sales</option>
                                    <option value="2" <?= !empty($data) ? ($data->jenis_penjualan == 2 ? "selected" : "") : ""; ?>>By Office</option>
                                    <option value="3" <?= !empty($data) ? ($data->jenis_penjualan == 3 ? "selected" : "") : ""; ?>>By Ecommerce</option>
                                </select>
                                <label for="floatingInput">Jenis Penjualan</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" disabled value="<?= session()->get('login')->name; ?>" class="form-control sales_id" id="sales_id" name="sales_id" placeholder="Nama Sales">
                                <label for="floatingInput">Nama Sales</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent-lokal">Simpan</button>
                <?php if (can('Master Data', 'Customer Global', 'd')) : ?>
                    <button type="button" class="btn btn-discard delete-btn delete-form">Hapus</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal add-modal-internasional" id="add_modal_internasional" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Customer</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-internasional" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe_customer" class="tipe_customer" id="tipe_customer" value="INTERNASIONAL">
                    <input autocomplete="one-time-code" type="hidden" class="internasional_id" name="id" id="internasional_id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama Customer</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select country_id" name="country_id" id="country_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataCountry)) {
                                        foreach ($dataCountry as $dc) {
                                    ?>
                                            <option value="<?= $dc["id"]; ?>">(<?= $dc["code"]; ?>) <?= $dc['country_name'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Pilih Negara</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <textarea autocomplete="one-time-code" class="form-control address" id="address" name="address"></textarea>
                                <label for="floatingInput">Alamat (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" disabled value="<?= session()->get('login')->name; ?>" class="form-control sales_id" id="sales_id" name="sales_id" placeholder="Nama Sales">
                                <label for="floatingInput">Nama Sales</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent-internasional">Simpan</button>
                <?php if (can('Master Data', 'Customer Global', 'd')) : ?>
                    <button type="button" class="btn btn-discard delete-btn delete-form-internasional">Hapus</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Customer Global</h1>
        <!-- Navigation -->
        <ul class="nav nav-tabs float-right" id="myTab" role="tablist">
            <li class="nav-item" role="presentation" style="cursor:pointer">
                <a class="nav-link active" onclick="removeAllTab()" id="lokal-tab" data-toggle="tab" data-target="#lokal" role="tab" aria-controls="lokal" aria-selected="true">Lokal</a>
            </li>
            <li class="nav-item" role="presentation" style="cursor:pointer">
                <a class="nav-link" onclick="removeAllTab()" id="internasional-tab" data-toggle="tab" data-target="#internasional" role="tab" aria-controls="internasional" aria-selected="false">Internasional</a>
            </li>
        </ul>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="lokal" role="tabpanel" aria-labelledby="lokal-tab">
                    <div class="collapse-lokal-list show" id="collapseLokalList">
                        <?php if (can('Master Data', 'Customer Global', 'c')) : ?>
                            <button class="btn btn-show-form btn-add mb-1 float-right btn-show-form-lokal" data-btn="create-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        <?php endif; ?>
                        <div class="row justify-content-end row-col-spp mb-3">
                            <div class="col-md-3 mt-1">
                                <select class="form-select company_lokal_search" name="company_lokal_search" id="company_lokal_search" aria-label="Floating label select example">
                                    <option value="">SEMUA COMPANY</option>
                                    <?php foreach ($dataCompany as $c) : ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['company'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mt-1">
                                <input autocomplete="one-time-code" class="form-control search form-out-search form-search-lokal" placeholder="Cari Customer / Sales" value="" style="float: right;" />
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTableLokal" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No.</th>
                                            <th onclick="changeSort('kode')" class="sort">Kode</th>
                                            <th onclick="changeSort('companyName')" class="sort">Company</th>
                                            <th onclick="changeSort('name')" class="sort">Nama Customer</th>
                                            <th onclick="changeSort('namaSales')" class="sort">Nama Sales</th>
                                            <th onclick="changeSort('phone')" class="sort">Kontak</th>
                                            <th onclick="changeSort('address')" class="sort">Alamat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-table" id="body-table" style="cursor: pointer;">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="internasional" role="tabpanel" aria-labelledby="lokal-tab">
                    <div class="collapse-internasional-list show" id="collapseInternasionalList">
                        <?php if (can('Master Data', 'Customer Global', 'c')) : ?>
                            <button class="btn btn-show-form btn-add float-right mb-1 btn-show-form-internasional" data-btn="create-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        <?php endif; ?>
                        <div class="row justify-content-end row-col-spp mb-3">
                            <div class="col-md-3 mt-1">
                                <select class="form-select company_internasional_search" name="company_internasional_search" id="company_lokal_search" aria-label="Floating label select example">
                                    <option value="">SEMUA COMPANY</option>
                                    <?php foreach ($dataCompany as $c) : ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['company'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mt-1">
                                <input autocomplete="one-time-code" class="form-control search form-out-search form-search-internasional" placeholder="Cari Customer / Sales" value="" style="float: right;" />
                            </div>
                        </div>

                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTableInternasional" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No.</th>
                                            <th onclick="changeSort('kode')" class="sort">Kode</th>
                                            <th onclick="changeSort('companyName')" class="sort">Company</th>
                                            <th onclick="changeSort('name')" class="sort">Nama Customer</th>
                                            <th onclick="changeSort('namaSales')" class="sort">Nama Sales</th>
                                            <th onclick="changeSort('country')" class="sort">Negara</th>
                                            <th onclick="changeSort('address')" class="sort">Alamat</th>
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
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "kode";
    let sortType = "desc";

    const dataTableLokal = $('#dataTableLokal').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            // [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("customer/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".form-search-lokal").val();
                data.sort = sort;
                data.sortType = sortType;
                data.company_id = $(".company_lokal_search").val();
                data.customerType = "LOKAL";
            }
        },
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
                sortable: false,
                width: "5%"
            }, {
                data: "kode",
                className: "text-center"
            },
            {
                data: "companyName",
                className: "text-center"
            },
            {
                data: "name",
                className: "text-center"
            }, {
                data: "namaSales",
                className: "text-center"
            }, {
                data: "phone",
                className: "text-center"
            }, {
                data: "address",
                className: "text-center",
            },

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

    const dataTableInternasional = $('#dataTableInternasional').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            // [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("customer/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".form-search-internasional").val();
                data.company_id = $(".company_internasional_search").val();
                data.sort = sort;
                data.sortType = sortType;
                data.customerType = "INTERNASIONAL";
            }
        },
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
            sortable: false,
            width: "5%"
        }, {
            data: "kode",
            className: "text-center"
        }, {
            data: "companyName",
            className: "text-center"
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "namaSales",
            className: "text-center"
        }, {
            data: "countryName",
            className: "text-center"
        }, {
            data: "address",
            className: "text-center",
        }, ],
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

        $(".nik").mask("AAAAAAAAAAAAAAAA", {
            translation: {
                "A": {
                    pattern: /[0-9]/,
                }
            }
        })

        $(".parent_postal_code").mask("AAAAA", {
            translation: {
                "A": {
                    pattern: /[0-9]/,
                }
            }
        })

        // TERMIN
        //CSS SELECT2 FLOATING LABEL
        $('.termin').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        });
        $(".termin")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".termin")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".termin")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // MATA UANG
        //CSS SELECT2 FLOATING LABEL
        $('.currency').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: false,
            dropdownParent: $(".add-modal .modal-content")
        });

        $(".currency")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".currency")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".currency")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // COUNTRY
        //CSS SELECT2 FLOATING LABEL
        $(".country_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".country_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.country_id').select2({
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal-internasional .modal-content")
        })

        $(".country_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-8px');

        // TIPE PELANGGAN
        //CSS SELECT2 FLOATING LABEL
        $('.tipe_pelanggan').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        });

        $(".tipe_pelanggan")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".tipe_pelanggan")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".tipe_pelanggan")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PROVINCE PARENT
        //CSS SELECT2 FLOATING LABEL
        $('.province_parent_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        });

        $(".province_parent_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".province_parent_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".province_parent_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');



        // CITY PARENT
        //CSS SELECT2 FLOATING LABEL
        $('.city_parent_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        });

        $('.city_parent_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.city_parent_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.city_parent_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $(".create-form-lokal").validate({
            rules: {
                name: {
                    required: true
                },
                address: {
                    required: true
                },
                nik: {
                    minlength: 16,
                    maxlength: 16
                },
                parent_postal_code: {
                    minlength: 5,
                    maxlength: 5
                },
                email: {
                    email: true
                },
                piutang: {
                    required: true
                },
                jenis_penjualan: {
                    required: true
                }

            },
            messages: {
                name: {
                    required: "Nama wajib diisi"
                },
                address: {
                    required: "Alamat wajib diisi"
                },
                nik: {
                    minlength: "NIK Minimal 16 Digit",
                    maxlength: "NIK Maksimal 16 Digit"
                },
                parent_postal_code: {
                    minlength: "Kode Pos Minimal 5 Digit",
                    maxlength: "Kode Pos Maksimal 5 Digit"
                },
                email: {
                    email: "Email Harus Valid"
                },
                piutang: {
                    required: "Limit Piutang Wajib Diisi"
                },
                jenis_penjualan: {
                    required: "Jenis Penjualan wajib diisi"
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


        var validatorInternasional = $(".create-form-internasional").validate({
            rules: {
                name: {
                    required: true
                },
                country_id: {
                    required: true
                },
            },
            messages: {
                name: {
                    required: "Nama customer wajib diisi"
                },
                country_id: {
                    required: "Pilih negara"
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


        $(".phone").mask("0000000000000")

        $(".postal_code").mask("00000")

        $(".no_npwp").mask("000000000000000")

        $(".form-search-internasional").keyup(function() {
            dataTableInternasional.ajax.reload();
        });

        $(".form-search-lokal").keyup(function() {
            dataTableLokal.ajax.reload();
        });

        $(".company_internasional_search").change(function() {
            dataTableInternasional.ajax.reload();
        });

        $(".company_lokal_search").change(function() {
            dataTableLokal.ajax.reload();
        });

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-form-lokal").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah");

            validator.resetForm();
            validator.reset();

            $(".create-form-lokal")[0].reset()

            $(".province_parent_id").val('').change()
            $(".city_parent_id").val('').change()
            $(".city_parent_id").empty()
            $(".city_parent_id").append(`<option value=""></option>`)

            $(".delete-form").css('display', 'none');

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'termin'
                },
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(result) {
                    $(".termin").empty()
                    $(".termin").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".termin").append(`<option value="${item.id}">${item.value}</option>`)
                    })

                    $(".termin").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'Valuta'
                },
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(result) {
                    $(".currency").empty()
                    $(".currency").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".currency").append(`<option value="${item.id}">${item.value}</option>`)
                    })

                    $(".currency").attr("disabled", true).val("30").change();
                }
            })

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'tipe_pelanggan'
                },
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(result) {
                    $(".tipe_pelanggan").empty()
                    $(".tipe_pelanggan").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".tipe_pelanggan").append(`<option value="${item.id}">${item.value}</option>`)
                    })

                    $(".tipe_pelanggan").val("").change();
                    $(".add-modal").modal("show");
                }
            })
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $('#dataTableInternasional tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = dataTableInternasional.row(this).data();
            $(".create-form-internasional")[0].reset()
            $(".delete-form-internasional").show();
            $(".title-name").text("Update")
            let id = data.id;

            $.ajax({
                url: "<?= base_url("customer/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                success: function(res) {
                    if (res.data) {
                        $(".internasional_id").val(id);
                        $(".name").val(res.data.name);
                        $(".address").val(res.data.address);
                        $(".country_id").val(res.data.country_id).change();
                        validatorInternasional.resetForm();
                        validatorInternasional.reset();

                        $('#add_modal_internasional').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            });

            $('#add-modal-internasional')
        });

        $('#dataTableLokal tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = dataTableLokal.row(this).data();
            $(".create-form-lokal")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update")

            $.ajax({
                url: "<?= base_url("customer/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                success: function(res) {
                    if (res.data) {
                        $(".id").val(id);
                        $(".name").val(res.data.name);
                        $(".address").val(res.data.address);
                        $(".no_npwp").val(res.data.no_npwp);
                        $(".phone").val(res.data.phone);

                        $(".contact_person").val(res.data.contact_person);
                        $(".email").val(res.data.email);
                        $(".parent_postal_code").val(res.data.postal_code);
                        $(".province_parent_id").val(res.data.province_id).change();
                        $(".piutang").val(res.data.piutang).change();

                        $(".jenis_penjualan").val(res.data.jenis_penjualan).change();

                        $(".nik").val(res.data.nik);


                        validator.resetForm();
                        validator.reset();

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
                            beforeSend: function() {
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                            },
                            data: {
                                name: 'termin'
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".termin").empty()
                                $(".termin").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".termin").append(`<option value="${item.id}">${item.value}</option>`)
                                })

                                $(".termin").val(res.data.termin).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
                            beforeSend: function() {
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                            },
                            data: {
                                name: 'Valuta'
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".currency").empty()
                                $(".currency").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".currency").append(`<option value="${item.id}">${item.value}</option>`)
                                })

                                $(".currency").attr('disabled', true).val(res.data.currency).change();
                            }
                        })

                        // AJAX GET CITY
                        $.ajax({
                            url: `<?= base_url("city"); ?>/${res.data.province_id}`,
                            method: "GET",
                            dataType: "json",
                            beforeSend: function() {
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                            },
                            success: function(result) {
                                $(".city_parent_id").empty()
                                $(".city_parent_id").val("").change()
                                $(".city_parent_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".city_parent_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                                })

                                $(".city_parent_id").val(res.data.city_id).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                name: 'tipe_pelanggan'
                            },
                            beforeSend: function() {
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".tipe_pelanggan").empty()
                                $(".tipe_pelanggan").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".tipe_pelanggan").append(`<option value="${item.id}">${item.value}</option>`)
                                })

                                $(".tipe_pelanggan").val(res.data.tipe_pelanggan).change();
                                $(".add-modal").modal("show");
                            }
                        })
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

        // delete lokal
        $(".delete-form").click(function() {
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
                        url: "<?= base_url("customer/delete"); ?>",
                        data: {
                            id: id
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
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        dataTableLokal.ajax.reload()
                                        $(".add-modal").modal("hide")
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        },
                    });
                }
            })
        });

        // delete internasional
        $(".delete-form-internasional").click(function() {
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
                    let id = $(".internasional_id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("customer/delete"); ?>",
                        data: {
                            id: id
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
                            csrf.val(response.token);
                            if (response.status) {
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        dataTableInternasional.ajax.reload()
                                        $(".add-modal-internasional").modal("hide")
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        },
                    });
                }
            })
        });

        $(".btn-submit-parent-lokal").click(function() {
            if ($(".create-form-lokal").valid()) {
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
                        let data = new FormData(document.querySelector(".create-form-lokal"));
                        let id = $(".id").val();
                        data.append("currency", "30");

                        if (id) {
                            <?php if (can('Master Data', 'Customer Global', 'u')) : ?>
                                $.ajax({
                                    url: "<?= base_url("customer/update"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading();
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
                                                    $(".add-modal").modal("hide")
                                                    dataTableLokal.ajax.reload()
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
                                        stopLoading()
                                    }
                                });
                            <?php else : ?>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Anda tidak punya akses untuk update',
                                    confirmButtonColor: '#4e73df',
                                })
                            <?php endif; ?>

                        } else {
                            $.ajax({
                                url: "<?= base_url("customer/save"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
                                },
                                complete: function() {
                                    stopLoading();
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
                                                $(".add-modal").modal("hide")
                                                dataTableLokal.ajax.reload()
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
                                    stopLoading()
                                }
                            });
                        }
                    }
                })
            }
        })
    });

    // INTERNASIONAL
    $('.btn-show-form-internasional').click(function() {
        $('.add-modal-internasional').modal('show');
        $('.title-name').text("Tambah");
        $('.internasional_id').val(null);
        $('.name').val(null);
        $('.country_id').val(null).change();
        $('.address').val(null);
        $('.delete-form-internasional').hide();
    });

    $('.btn-hide-form').click(function() {
        $('.add-modal-internasional').modal('hide');
        $('.internasional_id').val(null);
        $('.name').val(null);
        $('.country_id').val(null).change();
        $('.address').val(null);
    });

    $(".btn-submit-parent-internasional").click(function() {
        if ($(".create-form-internasional").valid()) {
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
                    let data = new FormData(document.querySelector(".create-form-internasional"));
                    let id = $(".internasional_id").val();

                    if (id) {
                        <?php if (can('Master Data', 'Customer Global', 'u')) : ?>
                            $.ajax({
                                url: "<?= base_url("customer/update"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    $("#add_modal_internasional").modal("hide");
                                    if (response.status) {
                                        stopLoading()
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                $(".add_modal_internasional").modal("hide")
                                                dataTableInternasional.ajax.reload()
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
                        <?php else : ?>
                            Swal.fire({
                                icon: 'error',
                                title: 'Anda tidak punya akses untuk update',
                                confirmButtonColor: '#4e73df',
                            })
                        <?php endif; ?>

                    } else {
                        $.ajax({
                            url: "<?= base_url("customer/save"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                            },
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                csrf.val(response.token);
                                $("#add_modal_internasional").modal("hide");
                                if (response.status) {
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            dataTableInternasional.ajax.reload()
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


                }
            })
        }
    })

    const getCityParent = function() {
        const id = $(".province_parent_id option:selected").val()

        if (id) {
            $.ajax({
                url: `<?= base_url("city"); ?>/${id}`,
                method: "GET",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(res) {
                    $(".city_parent_id").empty()
                    $(".city_parent_id").val("").change()
                    $(".city_parent_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".city_parent_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                    })
                }
            })
        }
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    // REMOVE ALL OPEN FORM
    const removeAllTab = function() {
        $(".collapse-lokal-list").addClass("show")
        $(".collapse-internasional-list").addClass("show")
    }

    function formatRupiah(angka) {
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return '' + ribuanFormatted + ',' + desimal;
    }
</script>


<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Supplier Bahan Penolong Import</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control kode" id="kode" name="kode" placeholder="Kode">
                                <label for="floatingInput">Kode</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control address" id="address" name="address" placeholder="Address">
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
                                                    <option value="<?= $province->id; ?>"><?= $province->province_name; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="floatingInput">Provinsi</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select city_parent_id" name="city_parent_id" id="city_parent_id" onchange="getPostalCodeParent()">
                                            <option value="" data-code=""></option>
                                        </select>
                                        <label for="floatingInput">Kota</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" class="form-control parent_postal_code" id="parent_postal_code" name="postal_code" placeholder="Postal Code">
                                        <label for="floatingInput">Kode Pos</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                     <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control no_npwp" id="no_npwp" name="no_npwp" placeholder="Nomor NPWP">
                                <label for="floatingInput">Nomor NPWP</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control phone" id="phone" name="phone" placeholder="Phone">
                                <label for="floatingInput">No. Telepon</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control contact_person" id="contact_person" name="contact_person" placeholder="Contact Person">
                                <label for="floatingInput">Contact Person</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="email" class="form-control email" id="email" name="email" placeholder="Email">
                                <label for="floatingInput">Email</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control no_rekening" id="no_rekening" name="no_rekening" placeholder="No. Rekening">
                                <label for="floatingInput">No. Rekening</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select supplier_buyer" name="supplier_buyer" id="supplier_buyer">
                                    <option value="SUPPLIER + BUYER">SUPPLIER + BUYER</option>
                                    <option value="SUPPLIER">SUPPLIER</option>
                                    <option value="BUYER">BUYER</option>
                                </select>
                                <label for="floatingInput">Supplier / Buyer</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select ap_id" name="ap_id" id="ap_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Akun AP</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select ar_id" name="ar_id" id="ar_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Akun AR</label>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- <div class="col-subtitle-modal">
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <h5 class="modal-sub-title">List Alamat Pengiriman</h5>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal" style="width: 106px;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div> -->
                    <!-- <div class="table-responsive mt-2">
                        <table class="table-inside table-bordered nowrap table-hover-tobasurimi" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Alamat</th>
                                    <th>Kota</th>
                                    <th>Provinsi</th>
                                    <th>Kode Pos</th>
                                    <th>Main Address</th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                            </tbody>
                        </table>
                    </div> -->
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                    <button type="button" class="btn btn-discard delete-form delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Alamat Pengiriman</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control detail_address" id="detail_address" name="detail_address" placeholder="Address">
                                <label for="floatingInput">Address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select province_id" name="province_id" id="province_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataProvinces)) {
                                        foreach ($dataProvinces as $province) {
                                    ?>
                                            <option value="<?= $province->id; ?>"><?= $province->province_name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Provinsi</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select city_id" name="city_id" id="city_id" onchange="getPostalCode()">
                                    <option value="" data-code=""></option>
                                </select>
                                <label for="floatingInput">Kota</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly="true" type="text" class="form-control postal_code" id="postal_code" name="postal_code" placeholder="Postal Code">
                                <label for="floatingInput">Kode Pos</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-discard delete-detail delete-btn">Hapus</button>
                    <button type="button" class="btn btn-hide-detail btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
            </div>
        </div>
    </div>
</div> -->

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Supplier Bahan Penolong Import</h1>
    <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </button>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end mb-3">
            <div class="col-md-2">
                <input class="form-control search form-out-search" placeholder="Search" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th onclick="changeSort('kode')" class="sort">Kode</th>
                            <th onclick="changeSort('name')" class="sort">Nama</th>
                            <th onclick="changeSort('address')" class="sort">Alamat</th>
                            <th onclick="changeSort('province_name')" class="sort">Provinsi</th>
                            <th onclick="changeSort('city_name')" class="sort">Kota</th>
                            <th onclick="changeSort('postal_code')" class="sort">Kode Pos</th>
                            <th onclick="changeSort('no_npwp')" class="sort">NPWP</th>
                            <th onclick="changeSort('phone')" class="sort">No. Telepon</th>
                            <th onclick="changeSort('contact_person')" class="sort">Contact Person</th>
                            <th onclick="changeSort('email')" class="sort">Email</th>
                            <th onclick="changeSort('no_rekening')" class="sort">No. Rekening</th>
                            <th onclick="changeSort('supplier_buyer')" class="sort">Supplier / Buyer</th>
                            <th onclick="changeSort('ap_name')" class="sort">AP</th>
                            <th onclick="changeSort('ar_name')" class="sort">AR</th>
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
    let sort = "kode";
    let sortType = "asc";
    let trigger = true;

    let list_address = [];
    let list_delete = [];
    var row = 0;

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
            url: "<?= base_url("supplier-bahan-penolong-import/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
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
            sortable: false
        }, {
            data: "kode",
            className: "text-center"
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "address",
            className: "text-center"
        }, {
            data: "province_name",
            className: "text-center"
        }, {
            data: "city_name",
            className: "text-center"
        }, {
            data: "postal_code",
            className: "text-center"
        }, {
            data: "no_npwp",
            className: "text-center"
        }, {
            data: "phone",
            className: "text-center"
        }, {
            data: "contact_person",
            className: "text-center"
        }, {
            data: "email",
            className: "text-center"
        }, {
            data: "no_rekening",
            className: "text-center"
        }, {
            data: "supplier_buyer",
            className: "text-center"
        }, {
            data: "ap_name",
            className: "text-center"
        }, {
            data: "ar_name",
            className: "text-center"
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

    var validator_detail = $(".detail-form").validate({
            rules: {
                detail_address: {
                    required: true
                },
                province_id: {
                    required: true
                },
                city_id: {
                    required: true
                }
            },
            messages: {
                detail_address: {
                    required: "Address wajib diisi"
                },
                province_id: {
                    required: "Province wajib diisi"
                },
                city_id: {
                    required: "City wajib diisi"
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

    $(document).ready(function() {
        const select2Prop = {
            dropdownParent: $("#add_modal"),
            ajax: {
                delay: 300,
                url: `<?= base_url("sub-account/dropdown"); ?>`,
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    }
                }
            }
        };

        // PROVINCE
        $('.province_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".province_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".province_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".province_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PROVINCE PARENT
        $('.province_parent_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
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

        // CITY
        $('.city_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".city_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".city_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".city_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // CITY PARENT
        $('.city_parent_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
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

        // AP
        $('.ap_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
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

        // AR
        $('.ar_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
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

        var validator = $(".create-form").validate({
            rules: {
                kode: {
                    required: true
                },
                name: {
                    required: true
                },
                address: {
                    required: true
                },
                no_npwp: {
                    required: true,
                    minlength: 15,
                    maxlength: 15,
                },
                phone: {
                    required: true
                },
                contact_person: {
                    required: true
                },
                email: {
                    required: true
                },
                no_rekening: {
                    required: true
                },
                supplier_buyer: {
                    required: true
                },
                province_parent_id: {
                    required: true
                },
                city_parent_id: {
                    required: true
                },
                ap_id: {
                    required: true
                },
                ar_id: {
                    required: true
                }
            },
            messages: {
                kode: {
                    required: "Kode wajib diisi"
                },
                name: {
                    required: "Nama wajib diisi"
                },
                address: {
                    required: "Alamat wajib diisi"
                },
                no_npwp: {
                    required: "Nomor NPWP wajib diisi",
                    minlength: "Nomor NPWP minimal 15 angka",
                    maxlength: "Nomor NPWP maksimal 15 angka",
                },
                phone: {
                    required: "No. Telepon wajib diisi"
                },
                contact_person: {
                    required: "Contact Person wajib diisi"
                },
                email: {
                    required: "Email wajib diisi"
                },
                no_rekening: {
                    required: "No. Rekening wajib diisi"
                },
                supplier_buyer: {
                    required: "Supplier / Buyer wajib diisi"
                },
                province_parent_id: {
                    required: "Provinsi wajib diisi"
                },
                city_parent_id: {
                    required: "Kota wajib diisi"
                },
                ap_id: {
                    required: "Akun AP wajib diisi"
                },
                ar_id: {
                    required: "Akun AR wajib diisi"
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

        $(".phone").mask("0000000000000")

        $(".postal_code").mask("00000")

        $(".no_npwp").mask("000000000000000")

        $(".no_rekening").mask("000000000000000")

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-detail").click(function() {
            $(".delete-detail").css('display', 'none');
            $(".province_id").val('').change()
            $(".city_id").val('').change()
            $(".city_id").empty()
            $(".city_id").append(`<option value=""></option>`)

            $(".title-detail-name").text("Tambah")
            $(".id_detail").val('')
            $(".detail_address").val('')
            
            $(".postal_code").val('')

            validator_detail.resetForm();
            validator_detail.reset();

            $(".detail-modal").modal("show")
        })

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah");

            $(".province_parent_id").val('').change()
            $(".city_parent_id").val('').change()
            $(".city_parent_id").empty()
            $(".city_parent_id").append(`<option value=""></option>`)

            $(".kode").attr("readonly", false);

            $(".body-detail-table").empty()

            row = 0;

            list_address = [];

            validator.resetForm();
            validator.reset();

            $(".create-form")[0].reset()
            $(".delete-form").css('display', 'none');
            $(".body-detail-table").empty()

            /* $.ajax({
                url: `<?= base_url("sub-account/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".ap_id").empty()
                    $(".ar_id").empty()

                    $(".ap_id").append(`<option value=""></option>`)
                    $(".ar_id").append(`<option value=""></option>`)

                    res.data.forEach(function(item) {
                        $(".ap_id").append(`<option value="${item.id}">${item.nama_sub}</option>`)
                        $(".ar_id").append(`<option value="${item.id}">${item.nama_sub}</option>`)
                    })

                    $(".ap_id").val('').change();
                    $(".ar_id").val('').change();

                    $(".add-modal").modal("show")
                }
            }) */

            $('.ap_id').select2(select2Prop);
            $('.ar_id').select2(select2Prop);

            $(".add-modal").modal("show");
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".province_id").change(function() {
            const id = $(".province_id option:selected").val()

            if (id && trigger) {
                $.ajax({
                    url: `<?= base_url("city"); ?>/${id}`,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        $(".city_id").empty()
                        $(".city_id").val("").change()
                        $(".city_id").append(`<option value=""></option>`)
                        res.data.forEach(function(item) {
                            $(".city_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                        })
                    }
                })
            }
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $(".kode").attr("readonly", true);

            $.ajax({
                url: "<?= base_url("supplier-bahan-penolong-import/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".kode").val(res?.data?.kode);
                        $(".name").val(res?.data?.name);
                        $(".address").val(res?.data?.address);
                        $(".no_npwp").val(res?.data?.no_npwp);
                        $(".phone").val(res?.data?.phone);
                        $(".contact_person").val(res?.data?.contact_person);
                        $(".email").val(res?.data?.email);
                        $(".no_rekening").val(res?.data?.no_rekening);
                        $(".supplier_buyer").val(res?.data?.supplier_buyer).change();
                        $(".province_parent_id").val(res?.data?.province_id).change();

                        row = res?.data?.list_address.length;

                        list_address = [];

                        let tag_html = "";

                        $(".body-detail-table").empty()

                        res?.data?.list_address.map((item, index) => {
                            list_address.push({
                                id: item.id,
                                supplier_id: item.supplier_id,
                                row: index + 1,
                                address: item.address,
                                province_id: item.province_id,
                                province_name: item.province_name,
                                city_id: item.city_id,
                                city_name: item.city_name,
                                postal_code: item.postal_code,
                                main_address: item.main_address
                            })

                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += index + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += item.address;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += item.city_name;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += item.province_name;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += item.postal_code;
                            tag_html += "</td>";
                            tag_html += "<td class='actions'>";
                            if(item.main_address == 1)
                            {
                                tag_html += `<input type="radio" checked id="main" name="main" value="${index + 1}">`;
                            }   
                            else
                            {
                                tag_html += `<input type="radio" id="main" name="main" value="${index + 1}">`;
                            } 
                            tag_html += "</td>";
                            tag_html += "</tr>";
                        })
                        
                        $(".body-detail-table").append(tag_html)

                        validator.resetForm();
                        validator.reset();
                        list_delete = [];

                        // AJAX GET CITY
                        $.ajax({
                            url: `<?= base_url("city"); ?>/${res?.data?.province_id}`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".city_parent_id").empty()
                                $(".city_parent_id").val("").change()
                                $(".city_parent_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".city_parent_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                                })

                                $(".city_parent_id").val(res?.data?.city_id).change();
                                $(".parent_postal_code").val(res?.data?.postal_code);
                            }
                        })

                        /* $.ajax({
                            url: `<?= base_url("sub-account/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".ap_id").empty()
                                $(".ar_id").empty()

                                $(".ap_id").append(`<option value=""></option>`)
                                $(".ar_id").append(`<option value=""></option>`)

                                result.data.forEach(function(item) {
                                    $(".ap_id").append(`<option value="${item.id}">${item.nama_sub}</option>`)
                                    $(".ar_id").append(`<option value="${item.id}">${item.nama_sub}</option>`)
                                })

                                $(".ap_id").val(res?.data?.ap_id).change();
                                $(".ar_id").val(res?.data?.ar_id).change();
                                $(".add-modal").modal("show")
                            }
                        }) */

                        $('.ap_id').select2(select2Prop);
                        $('.ar_id').select2(select2Prop);

                        const $apOption = $("<option selected='selected'></option>").val(res?.data?.ap_id).text(res?.data?.ap_name);
                        $(".ap_id").append($apOption).trigger('change');
                        const $arOption = $("<option selected='selected'></option>").val(res?.data?.ar_id).text(res?.data?.ar_name);
                        $(".ar_id").append($arOption).trigger('change');
                        
                        $(".add-modal").modal("show");

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

        // delete
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
                        url: "<?= base_url("supplier-bahan-penolong-import/delete"); ?>",
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

        // $(".btn-submit-detail").click(function() {
        //     let row_detail = $(".id_detail").val();
        //     let address = $(".detail_address").val()
        //     let province_id = $(".province_id option:selected").val()
        //     let province_name = $(".province_id option:selected").text()
        //     let city_id = $(".city_id option:selected").val()
        //     let city_name = $(".city_id option:selected").text()
        //     let postal_code = $(".postal_code").val();

        //     // update detail
        //     if(row_detail)
        //     {
        //         let main_address = document.querySelector('input[name="main"]:checked').value;

        //         if ($(".detail-form").valid()) {
        //             Swal.fire({
        //                 icon: 'question',
        //                 title: 'Simpan Data?',
        //                 confirmButtonColor: '#4e73df',
        //                 cancelButtonColor: '#d33',
        //                 showCancelButton: true,
        //                 reverseButtons: true,
        //                 confirmButtonText: 'Simpan',
        //                 cancelButtonText: 'Batal',
        //             }).then((result) => {
        //                 if (result.isConfirmed) {
        //                     console.log(id)
        //                     let new_list_address = []
        //                     let tag_html = "";

        //                     row = 0;

        //                     $(".body-detail-table").empty()

        //                     list_address.map(item => {
        //                         if(item.row == row_detail)
        //                         {
        //                             tag_html += `<tr>`;
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                             tag_html += row + 1;
        //                             tag_html += "</td>";
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                             tag_html += address;
        //                             tag_html += "</td>";
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                             tag_html += province_name;
        //                             tag_html += "</td>";
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                             tag_html += city_name;
        //                             tag_html += "</td>";
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                             tag_html += postal_code;
        //                             tag_html += "</td>";
        //                             tag_html += "<td class='actions'>";
        //                             if(item.row == main_address)
        //                             {
        //                                 tag_html += `<input type="radio" checked id="main" name="main" value="${row + 1}">`;
        //                             }
        //                             else
        //                             {
        //                                 tag_html += `<input type="radio" id="main" name="main" value="${row + 1}">`;
        //                             }
        //                             tag_html += "</td>";
        //                             tag_html += "</tr>";

        //                             new_list_address.push({
        //                                 id: item.id,
        //                                 supplier_id: item.supplier_id,
        //                                 row: row + 1,
        //                                 address: address,
        //                                 province_id: province_id,
        //                                 province_name: province_name,
        //                                 city_id: city_id,
        //                                 city_name: city_name,
        //                                 postal_code: postal_code,
        //                                 main_address: item.main_address
        //                             });

        //                             row = row + 1;
        //                         }
        //                         else
        //                         {
        //                             tag_html += `<tr>`;
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
        //                             tag_html += row + 1;
        //                             tag_html += "</td>";
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
        //                             tag_html += item.address;
        //                             tag_html += "</td>";
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
        //                             tag_html += item.province_name;
        //                             tag_html += "</td>";
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
        //                             tag_html += item.city_name;
        //                             tag_html += "</td>";
        //                             tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
        //                             tag_html += item.postal_code;
        //                             tag_html += "</td>";
        //                             tag_html += "<td class='actions'>";
        //                             if(item.row == main_address)
        //                             {
        //                                 tag_html += `<input type="radio" checked id="main" name="main" value="${row + 1}">`;
        //                             }
        //                             else
        //                             {
        //                                 tag_html += `<input type="radio" id="main" name="main" value="${row + 1}">`;
        //                             }
        //                             tag_html += "</td>";
        //                             tag_html += "</tr>";

        //                             new_list_address.push(item);

        //                             row = row + 1;
        //                         }
        //                     })

        //                     list_address = [];

        //                     list_address = new_list_address;

        //                     $(".body-detail-table").append(tag_html)

        //                     $(".detail-modal").modal("hide")
        //                 }
        //             })
        //         }
        //     }
        //     // create detail
        //     else
        //     {
        //         if ($(".detail-form").valid()) {
        //             Swal.fire({
        //                 icon: 'question',
        //                 title: 'Simpan Data?',
        //                 confirmButtonColor: '#4e73df',
        //                 cancelButtonColor: '#d33',
        //                 showCancelButton: true,
        //                 reverseButtons: true,
        //                 confirmButtonText: 'Simpan',
        //                 cancelButtonText: 'Batal',
        //             }).then((result) => {
        //                 if (result.isConfirmed) {
        //                     list_address.push({
        //                         id: '',
        //                         supplier_id: '',
        //                         row: row + 1,
        //                         address: address,
        //                         province_id: province_id,
        //                         province_name: province_name,
        //                         city_id: city_id,
        //                         city_name: city_name,
        //                         postal_code: postal_code,
        //                         main_address: 0
        //                     })
        //                 let tag_html = "";
        //                     tag_html += `<tr>`;
        //                     tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                     tag_html += row + 1;
        //                     tag_html += "</td>";
        //                     tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                     tag_html += address;
        //                     tag_html += "</td>";
        //                     tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                     tag_html += province_name;
        //                     tag_html += "</td>";
        //                     tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                     tag_html += city_name;
        //                     tag_html += "</td>";
        //                     tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${address}" data-province="${province_id}" data-city="${city_id}" data-postalcode="${postal_code}">`;
        //                     tag_html += postal_code;
        //                     tag_html += "</td>";
        //                     tag_html += "<td class='actions'>";
        //                     if(row === 0)
        //                     {
        //                         tag_html += `<input type="radio" checked id="main" name="main" value="${row + 1}">`;
        //                     }   
        //                     else
        //                     {
        //                         tag_html += `<input type="radio" id="main" name="main" value="${row + 1}">`;
        //                     } 
        //                     tag_html += "</td>";
        //                     tag_html += "</tr>";
        //                     $(".body-detail-table").append(tag_html)
        //                     $(".detail-modal").modal("hide")
        //                     row = row + 1;
        //                 }
        //             })
        //         }
        //     }
        // })

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")
            // if(list_address.length == 0)
            // {
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'List Alamat Pengiriman Tidak Boleh Kosong',
            //         confirmButtonColor: '#4e73df',
            //     })
            // }
            // else
            // {
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
                            let data = new FormData(document.querySelector(".create-form"));

                            let update_list_address = [];
                            // let main_address = document.querySelector('input[name="main"]:checked').value;

                            // if(list_delete.length !== 0)
                            // {
                            //     list_delete.map(obj => {
                            //         update_list_address.push(
                            //             {
                            //                 id: obj.id,
                            //                 supplier_id: obj.supplier_id,
                            //                 address: obj.address,
                            //                 province_id: obj.province_id,
                            //                 city_id: obj.city_id,
                            //                 main_address: 0,
                            //                 isDelete: true
                            //             }
                            //         )
                            //     })
                            // }
                            
                            // list_address.map(obj => {
                            //     if (main_address == obj.row) {
                            //         if (obj.id) {
                            //             update_list_address.push(
                            //                 {
                            //                     id: obj.id,
                            //                     supplier_id: obj.supplier_id,
                            //                     address: obj.address,
                            //                     province_id: obj.province_id,
                            //                     city_id: obj.city_id,
                            //                     main_address: 1
                            //                 }
                            //             )
                            //         }
                            //         else
                            //         {
                            //             update_list_address.push(
                            //                 {
                            //                     address: obj.address,
                            //                     province_id: obj.province_id,
                            //                     city_id: obj.city_id,
                            //                     main_address: 1
                            //                 }
                            //             )
                            //         }
                            //     }
                            //     else
                            //     {
                            //         if (obj.id) {
                            //             update_list_address.push(
                            //                 {
                            //                     id: obj.id,
                            //                     supplier_id: obj.supplier_id,
                            //                     address: obj.address,
                            //                     province_id: obj.province_id,
                            //                     city_id: obj.city_id,
                            //                     main_address: 0
                            //                 }
                            //             )
                            //         }
                            //         else
                            //         {
                            //             update_list_address.push(
                            //                 {
                            //                     address: obj.address,
                            //                     province_id: obj.province_id,
                            //                     city_id: obj.city_id,
                            //                     main_address: 0
                            //                 }
                            //             )
                            //         }
                            //     }
                            // })

                            // data.append("list_address", JSON.stringify(update_list_address))

                            let id = $(".id").val();

                            $.ajax({
                                url: id ? "<?= base_url("supplier-bahan-penolong-import/update"); ?>" : "<?= base_url("supplier-bahan-penolong-import/save"); ?>",
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
                                                $(".add-modal").modal("hide")
                                                table.ajax.reload()
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
                }
            // }
        })
    })

    // $(document).on('click', '.delete-detail', function() {
    //     let id = $(".id_detail").val()
    //     let main_address = document.querySelector('input[name="main"]:checked').value;

    //     console.log(id)
    //     console.log(main_address)

    //     if(id === main_address)
    //     {
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Main Address Tidak Dapat Dihapus',
    //             confirmButtonColor: '#4e73df',
    //         })
    //     }
    //     else
    //     {
    //         Swal.fire({
    //             icon: 'question',
    //             title: 'Hapus Data?',
    //             confirmButtonColor: '#4e73df',
    //             cancelButtonColor: '#d33',
    //             showCancelButton: true,
    //             reverseButtons: true,
    //             confirmButtonText: 'Hapus',
    //             cancelButtonText: 'Batal',
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 console.log(id)
    //                 let new_list_address = []
    //                 let tag_html = "";

    //                 $(".body-detail-table").empty()

    //                 row = 0;

    //                 console.log(list_address)

    //                 list_address.map(item => {
    //                     if(item.row != id)
    //                     {
    //                         tag_html += `<tr>`;
    //                         tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
    //                         tag_html += row + 1;
    //                         tag_html += "</td>";
    //                         tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
    //                         tag_html += item.address;
    //                         tag_html += "</td>";
    //                         tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
    //                         tag_html += item.province_name;
    //                         tag_html += "</td>";
    //                         tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
    //                         tag_html += item.city_name;
    //                         tag_html += "</td>";
    //                         tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
    //                         tag_html += item.postal_code;
    //                         tag_html += "</td>";
    //                         tag_html += "<td class='actions'>";
    //                         if(main_address == item.row)
    //                         {
    //                             tag_html += `<input type="radio" checked id="main" name="main" value="${row + 1}">`;
    //                         }   
    //                         else
    //                         {
    //                             tag_html += `<input type="radio" id="main" name="main" value="${row + 1}">`;
    //                         } 
    //                         tag_html += "</td>";
    //                         tag_html += "</tr>";

    //                         new_list_address.push({...item, row: row + 1});

    //                         row = row + 1;
    //                     }
    //                     else
    //                     {
    //                         // sent parameter isDelete if have supplier id and id
    //                         if(item.id)
    //                         {
    //                             list_delete.push(item)
    //                         }
    //                     }
    //                 })

    //                 list_address = [];

    //                 list_address = new_list_address;

    //                 $(".body-detail-table").append(tag_html)

    //                 $(".detail-modal").modal("hide")
    //             }
    //         })
    //     }
    // })

    // $(document).on('show.bs.modal','.detail-modal', function () {
    //    document.getElementById("add_modal").style = "display: block; z-index: 999 !important";
    // })

    // $(document).on('hide.bs.modal','.detail-modal', function () {
    //     document.getElementById("add_modal").style = "display: block;";
    //     $(".add-modal").css("overflow-y", "auto");
    // })

    // $(document).on('click', '.edit-table-detail', function(evt) {
    //     // if(!$(evt.target).is('.actions')) {
    //         $(".title-detail-name").text("Update")
    //         $(".delete-detail").css('display', '');
    //         let address = $(this).data('address')
    //         let province_id = $(this).data('province')
    //         let city_id = $(this).data('city')
    //         let postal_code = $(this).data('postalcode')
    //         let rowid = $(this).data('row')
    //         let id = $(this).data('id')

    //         validator_detail.resetForm();
    //         validator_detail.reset();
    //         trigger = false;
    //         $(".province_id").val(province_id).change()

    //         $(".id_detail").val(rowid)
    //         $(".detail_address").val(address)

    //         // AJAX GET CITY
    //         $.ajax({
    //             url: `<?= base_url("city"); ?>/${province_id}`,
    //             method: "GET",
    //             dataType: "json",
    //             success: function(result) {
    //                 $(".city_id").empty()
    //                 $(".city_id").val("").change()
    //                 $(".city_id").append(`<option value=""></option>`)
    //                 result.data.forEach(function(item) {
    //                     $(".city_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
    //                 })

    //                 $(".city_id").val(city_id).change()
    //                 $(".postal_code").val(postal_code)
    //                 trigger = true;
    //                 $(".detail-modal").modal("show")
    //             }
    //         })
    //     // }
    // })

    const getCityParent = function() {
        const id = $(".province_parent_id option:selected").val()

        if (id) {
            $.ajax({
                url: `<?= base_url("city"); ?>/${id}`,
                method: "GET",
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

    // const getPostalCode = function() {
    //     $(".postal_code").val($(".city_id option:selected").attr("data-code"))
    // }

    const getPostalCodeParent = function() {
        $(".parent_postal_code").val($(".city_parent_id option:selected").attr("data-code"))
    }

    const changeSort = function(val) {
        if(sort !== val)
        {
            sortType = "asc";
            sort = val;
        }
        else
        {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>


<?= $this->endSection(); ?>
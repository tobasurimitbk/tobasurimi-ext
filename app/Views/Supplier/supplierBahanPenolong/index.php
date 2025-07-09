<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Supplier Bahan Penolong</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" class="form-control kode" id="kode" name="kode" value="<?= !empty($dataSPP) ? $dataSPP->spp_no : ""; ?>">
                                    <label for="floatingInput">Kode Supplier</label>
                                </div>
                                <div style="<?= !empty($dataSPP) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama Supplier</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <textarea autocomplete="one-time-code" class="form-control address text-area-all" name="address" id="address" placeholder="Alamat (Opsional)"></textarea>
                                <label for="floatingInput">Alamat (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select country_code" name="country_code" id="country_code">
                                    <option value=""></option>
                                    <?php foreach ($country as $c) : ?>
                                        <option value="<?= $c->code; ?>">
                                            <?= $c->country_name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Negara (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
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
                                <label for="floatingInput">Provinsi (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select city_parent_id" name="city_parent_id" id="city_parent_id" onchange="getPostalCodeParent()">
                                    <option value="" data-code=""></option>
                                </select>
                                <label for="floatingInput">Kota (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control parent_postal_code" id="parent_postal_code" name="postal_code" placeholder="Kode Pos (Opsional)">
                                <label for="floatingInput">Kode Pos (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no_npwp" id="no_npwp" name="no_npwp" placeholder="NPWP (Opsional)" onkeyup="this.value = formatNpwp(this.value.replace(/\D/g, ''))">
                                <label for="floatingInput">NPWP (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no_ktp" id="no_ktp" name="no_ktp" placeholder="KTP (Opsional)">
                                <label for="floatingInput">KTP (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <textarea name="phone" id="phone" class="form-control phone"></textarea>
                                <label for="floatingInput">No. Telepon (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control contact_person" id="contact_person" name="contact_person" placeholder="Contact Person (Opsional)">
                                <label for="floatingInput">Contact Person (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="email" class="form-control email" id="email" name="email" placeholder="Email">
                                <label for="floatingInput">Email (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                <button type="button" class="btn btn-discard delete-form delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Supplier Bahan Penolong</h1>
        <?php if (can('Supplier', 'Bahan Penolong', 'p')): ?>
            <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 20px;">
                Import / Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item btn-upload-excel">Import Excel</button></li>
                <li><button class="dropdown-item" onclick="exportExcel('BAHAN PENOLONG')">Export Excel</button></li>
            </ul>
        <?php endif; ?>
        <?php if (can('Supplier', 'Bahan Penolong', 'c')): ?>
            <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Kode / Nama" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th onclick="changeSort('kode')" class="sort">Kode</th>
                                <th onclick="changeSort('name')" class="sort">Nama</th>
                                <th onclick="changeSort('no_npwp')" class="sort">NPWP</th>
                                <th onclick="changeSort('no_ktp')" class="sort">KTP</th>
                                <th onclick="changeSort('address')" class="sort">No Telephone</th>
                                <th onclick="changeSort('phone')" class="sort">Alamat</th>
                                <th onclick="changeSort('phone')" class="sort">Histori</th>
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
<div class="modal" id="import_excel_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Supplier</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary text-black" role="alert">
                    UNDUH TEMPLEATE EXCEL <a href="<?= base_url('assets/import/IMPORT_EXCEL_SUPPLIER.xlsx') ?>" style="text-decoration: none;"><b style="color: black;">DISINI</b></a>
                </div>
                <form class="form-excel" method="post">
                    <input type="hidden" name="type" value="BAHAN PENOLONG">
                    <div class="form-floating" style="height: 50px;">
                        <input type="file" name="file" id="file" accept=".xlsx" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-import-excel mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-excel">Simpan</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="historiModal" tabindex="-1" role="dialog" aria-labelledby="historiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historiModalLabel">Histori Purchase Order</h5>
                <button style="right: 10px;" class="btn btn-warning btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                    Export
                </button>

                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <li><button class="dropdown-item" onclick="printExcelPoLokal()">EXCEL</button></li>
                </ul>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-end ">
                    <div class="col-md-3">
                        <input autocomplete="one-time-code" style="height: 40px;" value="" type="text" placeholder="Tanggal PO" class="form-control form-control-lg po-date-lokal">
                    </div>
                    <div class="col-md-3">
                        <input autocomplete="one-time-code" style="height: 40px;" placeholder="Cari Data" value="" type="text" class="form-control form-control-lg search-po-lokal">
                    </div>

                </div>
                <table class="table-inside table-borderd nowrap table-hover-tobasurimi tablePoLokal mt-3" id="tablePoLokal" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col" onclick="changeShortPoLokal('am_purchase_orders.purchase_request_id')" class="sort">No SPP</th>
                            <th scope="col" onclick="changeShortPoLokal('penerimaan_barang.no_penerimaan_barang')" class="sort">No LPB</th>
                            <th scope="col" onclick="changeShortPoLokal('am_purchase_orders.po_date')" class="sort">Tanggal</th>
                            <th scope="col" onclick="changeShortPoLokal('suppliers.name')" class="sort">Supplier</th>
                            <th scope="col" onclick="changeShortPoLokal('barang_master_spesifikasi.spesifikasi')" class="sort">Barang</th>
                            <th scope="col" onclick="changeShortPoLokal('am_purchase_orders.note')" class="sort">Keterangan</th>
                            <th scope="col" onclick="changeShortPoLokal('am_purchase_orders.division_id')" class="sort">Departemen</th>
                            <th scope="col" onclick="changeShortPoLokal('am_purchase_order_details.qty')" class="sort">Qty</th>
                            <th scope="col" onclick="changeShortPoLokal('am_purchase_order_details.unit')" class="sort">Satuan</th>
                            <th scope="col" onclick="changeShortPoLokal('am_purchase_order_details.price')" class="sort">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "kode";
    let sortType = "desc";
    let trigger = true;
    let sortPoLokal = "am_purchase_orders.id";
    let sortTypePoLokal = "desc";
    let tablePoLokal = null;
    let supplierId = null;

    // Init changeStatus
    changeStatus();

    $('.province_parent_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    })

    $('.city_parent_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    })

    $('.country_code').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).change(function() {
        var code = $(this).val();
        if (code === "ID") {
            $('.province_parent_id').attr('disabled', false);
            $('.city_parent_id').attr('disabled', false);
        } else {
            $('.province_parent_id').attr('disabled', true);
            $('.city_parent_id').attr('disabled', true);
            $('.province_parent_id').val(null).change();
            $('.city_parent_id').val(null).change();
        }

    });

    //CSS SELECT2 FLOATING LABEL
    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '1px');

    $('.form-select')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $(".po-date-lokal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    const table = $('.dataTable').DataTable({

        processing: true,
        serverSide: true,
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
        ajax: {
            url: "<?= base_url("supplier-bahan-penolong/all"); ?>",
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
            data: "no_npwp",
            className: "text-center"
        }, {
            data: "no_ktp",
            className: "text-center"
        }, {
            data: "phone",
            className: "text-center"
        }, {
            data: "address",
            className: "text-center"
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                return `
                    <div class="mt-0 actions">
                        <button onclick="displayHistory('${row.id}')" class="btn btn-success posting-spp actions">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </button>
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
        tablePoLokal = $('#tablePoLokal').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            order: [
                [2, 'desc']
            ],
            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            ajax: {
                url: "<?= base_url("barang-bahan-penolong/histori-supplier"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.supplier_id = supplierId;
                    data.search = $(".search-po-lokal").val();
                    data.po_date = $(".po-date-lokal").val();
                    data.sort = sortPoLokal;
                    data.sortType = sortTypePoLokal;
                }
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.tablePoLokal').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                data: "no",
                className: "text-center",
                sortable: false
            }, {
                data: "spp_no",
                className: "text-center"
            }, {
                data: "no_penerimaan_barang",
                className: "text-center"
            }, {
                data: "po_date",
                className: "text-center"
            }, {
                data: "nama_supplier",
                className: "text-center"
            }, {
                data: "nama_barang",
                className: "text-center"
            }, {
                data: "note",
                className: "text-center"
            }, {
                data: "divisi",
                className: "text-center"
            }, {
                data: "qty",
                className: "text-center"
            }, {
                data: "kode_satuan",
                className: "text-center"
            }, {
                data: "price",
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

    })

    $(".search-po-lokal").keyup(function() {
        tablePoLokal.ajax.reload();
    })

    $(".po-date-lokal").change(function() {
        tablePoLokal.ajax.reload();
    })

    function displayHistory(id) {
        supplierId = id;
        tablePoLokal.ajax.reload();
        $('.search-po-lokal').val();
        $('.po-date-lokal').val();
        $('#historiModal').modal('show');
    }

    $(document).ready(function() {
        var validator = $(".create-form").validate({
            rules: {
                kode: {
                    required: true
                },
                name: {
                    required: true
                },

            },
            messages: {
                kode: {
                    required: "Kode wajib diisi"
                },
                name: {
                    required: "Nama wajib diisi"
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

        $(".no_npwp").mask("000000000000000000000")

        // $(".phone").mask("0000000000000")

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah");

            validator.resetForm();
            validator.reset();

            $(".create-form")[0].reset()
            $(".delete-form").css('display', 'none');

            $(".province_parent_id").val('').change()
            $(".city_parent_id").val('').change()
            $(".city_parent_id").empty()
            $(".city_parent_id").append(`<option value=""></option>`)
            $(".country_code").val('').change()

            $.ajax({
                url: "<?= base_url("supplier/generate/BP"); ?>",
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".kode").val(res.data)
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

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".btn-hide-harga").click(function() {
            $(".harga-modal").modal("hide")
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $.ajax({
                url: "<?= base_url("supplier/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        validator.resetForm();
                        validator.reset();

                        $(".id").val(id);
                        $(".kode").val(res.data.kode);
                        $(".name").val(res.data.name);
                        $(".address").val(res.data.address);
                        $(".no_npwp").val(formatNpwp(res.data.no_npwp));
                        $(".no_ktp").val(formatNpwp(res.data.no_ktp));
                        $(".phone").val(res.data.phone);
                        $(".contact_person").val(res.data.contact_person);
                        $(".email").val(res.data.email);
                        $(".province_parent_id").val(res.data.province_id).change();
                        $(".country_code").val(res.data.country_code).change();
                        $('#auto_generate').css('display', 'none');
                        $("#kode").prop("readonly", false);
                        $('.modal').on('hidden.bs.modal', function() {
                            $('#auto_generate').css('display', '');

                        });

                        // AJAX GET CITY
                        $.ajax({
                            url: `<?= base_url("city"); ?>/${res.data.province_id}`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".city_parent_id").empty()
                                $(".city_parent_id").val("").change()
                                $(".city_parent_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".city_parent_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                                })

                                $(".city_parent_id").val(res.data.city_id).change();
                                $(".parent_postal_code").val(res.data.postal_code);
                            }
                        })

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
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("supplier/delete"); ?>",
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

        $(".btn-submit-parent").click(function() {
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
                        let data = new FormData(document.querySelector(".create-form"));
                        let id = $(".id").val();

                        $.ajax({
                            url: id ? "<?= base_url("supplier-bahan-penolong/update"); ?>" : "<?= base_url("supplier-bahan-penolong/save"); ?>",
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
        })
    })

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

    const getPostalCodeParent = function() {
        $(".parent_postal_code").val($(".city_parent_id option:selected").attr("data-code"))
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function formatNpwp(value) {
        if (typeof value === 'string') {
            return value.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6');
        }
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $.ajax({
                url: `<?= base_url("/supplier-bahan-penolong/generate-kode-supplier-bp"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res) {
                        $("#kode").val(res);
                        $("#kode").attr("readonly", true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#kode").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#kode").val("");
                    }
                }
            })
        } else {
            $("#kode").attr("readonly", false);
            $("#kode").val("");
        }
    }

    $('.btn-upload-excel').click(function() {
        $('#file').val(null);
        $('#import_excel_modal').modal('show');

    });

    $('.btn-discard-import-excel').click(function() {
        $('#import_excel_modal').modal('hide');
    });

    $('.btn-submit-excel').click(function() {
        if ($('.form-excel').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Import Excel?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrf = $(`[name="${csrfToken}"]`);
                    let formData = new FormData(document.querySelector(".form-excel"));
                    $.ajax({
                        url: "<?= base_url("supplier/import-excel"); ?>",
                        data: formData,
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
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then(() => {
                                    table.ajax.reload();
                                    $('#import_excel_modal').modal('hide');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                        },
                    });

                }
            })
        }
    });

    const exportExcel = function(type) {
        var url = "<?= base_url('supplier/export-excel') ?>";
        window.open(url + `?type=${type}&sort=${sort}&sortType=${sortType}&`, "_blank");
    }

    function changeShortPoLokal(val) {
        if (sortPoLokal !== val) {
            sortTypePoLokal = "asc";
            sortPoLokal = val;
        } else {
            sortTypePoLokal = sortTypePoLokal === "asc" ? "desc" : "asc";
        }
        tablePoLokal.ajax.reload();
    }

    function convertDateFormat(dateStr) {
        const parts = dateStr.split("/");
        if (parts.length === 3) {
            return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
        return "";
    }

    function printExcelPoLokal() {
        var tanggal = $(".po-date-lokal").val() ? convertDateFormat($(".po-date-lokal").val()) : "";
        var search = $(".search-po-lokal").val() ?? "";
        if (!supplierId) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Supplier belum dipilih!',
            });
            return;
        }

        const url = "<?= base_url("barang-bahan-penolong/export-histori-supplier") ?>";
        const params = new URLSearchParams({
            supplier_id: supplierId,
            po_date: tanggal,
            search: search,
            sort: sortPoLokal,
            sortType: sortTypePoLokal,
        });

        window.open(url + "?" + params.toString(), "_blank");
    }
</script>


<?= $this->endSection(); ?>
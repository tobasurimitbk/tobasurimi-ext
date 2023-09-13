<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Customer</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
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
                                <label for="floatingInput">Contact Person (Opsional)</label>
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
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select currency" id="currency" name="currency">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Mata Uang (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select termin" id="termin" name="termin">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Termin (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tipe_pelanggan" name="tipe_pelanggan" id="tipe_pelanggan">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Tipe Pelanggan (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select sales_id" name="sales_id" id="sales_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Sales (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-form">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Customer</h1>
        <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-4">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Nama Pelanggan" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('kode')" class="sort">Kode</th>
                                <th onclick="changeSort('name')" class="sort">Nama Pelanggan</th>
                                <th onclick="changeSort('phone')" class="sort">Telepon</th>
                                <th onclick="changeSort('contact_person')" class="sort">Kontak</th>
                                <th onclick="changeSort('saldo')" class="sort">Saldo</th>
                                <th onclick="changeSort('currencyName')" class="sort">Mata Uang</th>
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

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            url: "<?= base_url("customer/all"); ?>",
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
            sortable: false,
            width: "5%"
        }, {
            data: "kode",
            className: "text-center"
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "phone",
            className: "text-center"
        }, {
            data: "contact_person",
            className: "text-center"
        }, {
            data: "saldo",
            className: "text-center"
        }, {
            data: "currencyName",
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

        // SALES
        $('.sales_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".sales_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".sales_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".sales_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

         // TERMIN
        $('.termin').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
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
        $('.currency').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
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

        // TIPE PELANGGAN
        $('.tipe_pelanggan').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
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

        var validator = $(".create-form").validate({
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

        // $(".no_rekening").mask("000000000000000")

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

            $(".province_parent_id").val('').change()
            $(".city_parent_id").val('').change()
            $(".city_parent_id").empty()
            $(".city_parent_id").append(`<option value=""></option>`)

            $(".delete-form").css('display', 'none');

            $.ajax({
                url: `<?= base_url("employee-division/dropdown"); ?>`,
                method: "GET",
                data: {
                    division: 'SALES'
                },
                dataType: "json",
                success: function(result) {
                    $(".sales_id").empty()
                    $(".sales_id").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".sales_id").append(`<option value="${item.id}">${item.nip} - ${item.name}</option>`)
                    })

                    $(".sales_id").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
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

                    $(".termin").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
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

                    $(".currency").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'tipe_pelanggan'
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

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update")

            $.ajax({
                url: "<?= base_url("customer/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.data) {
                        $(".id").val(id);
                        $(".name").val(res?.data?.name);
                        $(".address").val(res?.data?.address);
                        $(".no_npwp").val(res?.data?.no_npwp);
                        $(".phone").val(res?.data?.phone);

                        $(".contact_person").val(res?.data?.contact_person);
                        $(".sales").val(res?.data?.sales);
                        $(".email").val(res?.data?.email);
                        $(".parent_postal_code").val(res?.data?.postal_code);
                        $(".province_parent_id").val(res?.data?.province_id).change();

                        $(".nik").val(res?.data?.nik);

                        $(".sales").val(res?.data?.sales);

                        validator.resetForm();
                        validator.reset();

                        $.ajax({
                            url: `<?= base_url("employee-division/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                division: 'SALES'
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".sales_id").empty()
                                $(".sales_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".sales_id").append(`<option value="${item.id}">${item.nip} - ${item.name}</option>`)
                                })

                                $(".sales_id").val(res?.data?.sales_id).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
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

                                $(".termin").val(res?.data?.termin).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
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

                                $(".currency").val(res?.data?.currency).change();
                            }
                        })

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
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                name: 'tipe_pelanggan'
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".tipe_pelanggan").empty()
                                $(".tipe_pelanggan").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".tipe_pelanggan").append(`<option value="${item.id}">${item.value}</option>`)
                                })

                                $(".tipe_pelanggan").val(res?.data?.tipe_pelanggan).change();
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
                        url: "<?= base_url("customer/delete"); ?>",
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
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.querySelector(".create-form"));

                        let id = $(".id").val();

                        $.ajax({
                            url: id ? "<?= base_url("customer/update"); ?>" : "<?= base_url("customer/save"); ?>",
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

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>


<?= $this->endSection(); ?>
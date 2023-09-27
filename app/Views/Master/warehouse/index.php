<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Gudang</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control code_warehouse" id="code_warehouse" name="code_warehouse" placeholder="Kode Gudang" maxlength="30">
                                <label for="floatingInput">Kode Gudang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control warehouse_name" id="warehouse_name" name="warehouse_name" placeholder="Warehouse Name" maxlength="30">
                                <label for="floatingInput">Nama Gudang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control address" id="address" name="address" placeholder="Address">
                                <label for="floatingInput">Alamat (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control phone" id="phone" name="phone" placeholder="Phone" maxlength="30">
                                <label for="floatingInput">Nomor Telepon (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select province_id" name="province_id" id="province_id" onchange="getCity()">
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
                                <select class="form-select city_id" name="city_id" id="city_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Kota (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control zip_code" id="zip_code" name="zip_code" placeholder="Zip Code">
                                <label for="floatingInput">Kode Pos (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="email" class="form-control email" id="email" name="email" placeholder="Email">
                                <label for="floatingInput">Email (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select pic_id" name="pic_id" id="pic_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataPic)) {
                                        foreach ($dataPic as $pic) {
                                    ?>
                                            <option value="<?= $pic["id"]; ?>"><?= $pic["nip"] . " - " . $pic["name"]; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">PIC (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
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
        <h1>Warehouse</h1>
        <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('code_warehouse')" class="sort">Kode Warehouse</th>
                                <th onclick="changeSort('warehouse_name')" class="sort">Nama Warehouse</th>
                                <th onclick="changeSort('address')" class="sort">Alamat</th>
                                <th onclick="changeSort('phone')" class="sort">Nomor Telepon</th>
                                <th onclick="changeSort('email')" class="sort">Email</th>
                                <th onclick="changeSort('province_name')" class="sort">Provinsi</th>
                                <th onclick="changeSort('city_name')" class="sort">Kota</th>
                                <th onclick="changeSort('zip_code')" class="sort">Kode Pos</th>
                                <th onclick="changeSort('pic_name')" class="sort">PIC</th>
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
    let sort = "code_warehouse";
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
            url: "<?= base_url("warehouse/all"); ?>",
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
            data: "code_warehouse",
            className: "text-center"
        }, {
            data: "warehouse_name",
            className: "text-center"
        }, {
            data: "address",
            className: "text-center"
        }, {
            data: "phone",
            className: "text-center"
        }, {
            data: "email",
            className: "text-center"
        }, {
            data: "province_name",
            className: "text-center"
        }, {
            data: "city_name",
            className: "text-center"
        }, {
            data: "zip_code",
            className: "text-center"
        }, {
            data: "pic_name",
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
        var validator = $(".create-form").validate({
            rules: {
                code_warehouse: {
                    required: true
                },
                warehouse_name: {
                    required: true
                },
                // address: {
                //     required: true
                // },
                // province_id: {
                //     required: true
                // },
                // city_id: {
                //     required: true
                // },
                // zip_code: {
                //     required: true
                // },
                // phone: {
                //     required: true
                // },
                // email: {
                //     required: true,
                //     email: true,
                // },
                // pic_id: {
                //     required: true
                // },
            },
            messages: {
                code_warehouse: {
                    required: "Kode wajib diisi"
                },
                warehouse_name: {
                    required: "Nama wajib diisi"
                },
                // address: {
                //     required: "Address wajib diisi"
                // },
                // province_id: {
                //     required: "Province wajib diisi"
                // },
                // city_id: {
                //     required: "City wajib diisi"
                // },
                // zip_code: {
                //     required: "Zip code wajib diisi"
                // },
                // phone: {
                //     required: "Phone wajib diisi"
                // },
                // email: {
                //     required: "Email wajib diisi",
                //     email: "Email must be valid",
                // },
                // pic_id: {
                //     required: "PIC wajib diisi"
                // },
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

        $(".zip_code").mask("00000")

        // PROVINCE
        $('.province_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
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

        // CITY
        $('.city_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
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

        // PIC
        $('.pic_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".pic_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".pic_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".pic_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');


        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dataTable_info").addClass("pt-0");


        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah");

            $(".province_id").val("").change();
            $(".city_id").val("").change();
            $(".pic_id").val("").change();

            $(".city_id").empty()
            $(".city_id").append(`<option value=""></option>`)

            validator.resetForm();
            validator.reset();

            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');

            // $.ajax({
            //     url: `<?= base_url("employee-pic/dropdown"); ?>`,
            //     method: "GET",
            //     dataType: "json",
            //     success: function(result) {
            //         $(".pic_id").empty()
            //         $(".pic_id").val("").change()
            //         $(".pic_id").append(`<option value=""></option>`)
            //         result.data.forEach(function(item) {
            //             $(".pic_id").append(`<option value="${item.id}">${item.nip} - ${item.name}</option>`)
            //         })

            //         $(".pic_id").val('').change();
            //         $(".add-modal").modal("show")
            //     }
            // })
            $(".add-modal").modal("show")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            validator.resetForm();
            validator.reset();

            $.ajax({
                url: "<?= base_url("warehouse/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".code_warehouse").val(res?.data?.code_warehouse);
                        $(".warehouse_name").val(res?.data?.warehouse_name);
                        $(".address").val(res?.data?.address);
                        $(".phone").val(res?.data?.phone);
                        $(".email").val(res?.data?.email);
                        $(".province_id").val(res?.data?.province_id).change();

                        // $.ajax({
                        //     url: `<?= base_url("employee-pic/dropdown"); ?>`,
                        //     method: "GET",
                        //     dataType: "json",
                        //     success: function(result) {
                        //         $(".pic_id").empty()
                        //         $(".pic_id").val("").change()
                        //         $(".pic_id").append(`<option value=""></option>`)
                        //         result.data.forEach(function(item) {
                        //             $(".pic_id").append(`<option value="${item.id}">${item.nip} - ${item.name}</option>`)
                        //         })

                        //         $(".pic_id").val(res?.data?.pic_id).change();
                        //     }
                        // })
                        $(".pic_id").val(res?.data?.pic_id).change()

                        // AJAX GET CITY
                        $.ajax({
                            url: `<?= base_url("city"); ?>/${res?.data?.province_id}`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".city_id").empty()
                                $(".city_id").val("").change()
                                $(".city_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".city_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                                })

                                $(".city_id").val(res?.data?.city_id).change();
                                $(".zip_code").val(res?.data?.zip_code);
                                $(".add-modal").modal("show")
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
                        url: "<?= base_url("warehouse/delete"); ?>",
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
                        let data = new FormData(document.querySelector(".create-form"));

                        let id = $(".id").val();

                        $.ajax({
                            url: id ? "<?= base_url("warehouse/update"); ?>" : "<?= base_url("warehouse/save"); ?>",
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

    const getCity = function() {
        const id = $(".province_id option:selected").val()
        if (id) {
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
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Supplier</h5>
            </div>
            <div class="modal-body">
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row mb-3">
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
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control address" id="address" name="address" placeholder="Address">
                                <label for="floatingInput">Alamat</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control no_npwp" id="no_npwp" name="no_npwp" placeholder="Nomor NPWP">
                                <label for="floatingInput">Nomor NPWP</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control phone" id="phone" name="phone" placeholder="Phone">
                                <label for="floatingInput">Phone</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control contact_person" id="contact_person" name="contact_person" placeholder="Contact Person">
                                <label for="floatingInput">Contact Person</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="email" class="form-control email" id="email" name="email" placeholder="Email">
                                <label for="floatingInput">Email</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control no_rekening" id="no_rekening" name="no_rekening" placeholder="No. Rekening">
                                <label for="floatingInput">No. Rekening</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
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
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select country" name="country" id="country">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Country</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select provinsi" name="provinsi" id="provinsi">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Provinsi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kota" name="kota" id="kota">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Kota</label>
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
    <div>
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Supplier</h4>
        <button class="btn btn-show-form btn-add btn-block float-right" data-btn="create-modal" style="margin-top: -40px; width: 176px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add New
        </button>
    </div>
    <div class="mb-2">
        <input class="form-control search float-right" placeholder="Search" style="width: 30%" value="" />
    </div>
    <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Country</th>
                    <th>Provinsi</th>
                    <th>Kota</th>
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
                }
            },
            messages: {
                kode: {
                    required: "Kode is Required"
                },
                name: {
                    required: "Nama is Required"
                },
                address: {
                    required: "Alamat is Required"
                },
                no_npwp: {
                    required: "Nomor NPWP is Required",
                    minlength: "Nomor NPWP min length is 15",
                    maxlength: "Nomor NPWP max length is 15",
                },
                phone: {
                    required: "Phone is Required"
                },
                contact_person: {
                    required: "Contact Person is Required"
                },
                email: {
                    required: "Email is Required"
                },
                no_rekening: {
                    required: "No. Rekening is Required"
                },
                supplier_buyer: {
                    required: "Supplier / Buyer is Required"
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

        $(".no_npwp").mask("000000000000000")

        $(".no_rekening").mask("000000000000000")

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Add New");

            $(".kode").attr("readonly", false);

            validator.resetForm();
            validator.reset();

            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        const table = $('.dataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            serverSide: true,
            ordering: false,
            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            ajax: {
                url: "<?= base_url("supplier/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                }
            },
            // scrollX: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show 25 Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            //responsive: true,
            display: "stripe",
            searching: false,
            columns: [{
                data: "kode",
                className: "text-left"
            }, {
                data: "name",
                className: "text-left"
            }, {
                data: "address",
                className: "text-left"
            }, {
                data: "country",
                className: "text-left"
            }, {
                data: "province",
                className: "text-left"
            }, {
                data: "city",
                className: "text-left"
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

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $(".kode").attr("readonly", true);

            $.ajax({
                url: "<?= base_url("supplier/id"); ?>" + "/" + id,
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

                        validator.resetForm();
                        validator.reset();
                        $(".add-modal").modal("show")
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
                            url: id ? "<?= base_url("supplier/update"); ?>" : "<?= base_url("supplier/save"); ?>",
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
</script>

<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="width: 1200px !important; max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Customer</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control name" id="name" name="name" placeholder="Full Name" maxlength="30">
                                <label for="floatingInput">Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control name" id="name" name="address" placeholder="Address">
                                <label for="floatingInput">Address</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input class="form-control province" id="province" name="province" placeholder="Province">
                                <label for="floatingInput">Province</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="city" class="form-control city" id="city" name="city" placeholder="City">
                                <label for="floatingInput">City</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control zip_code" id="zip_code" name="zip_code" placeholder="Zip Code">
                                <label for="floatingInput">Zip Code</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control phone_no" id="phone_no" name="phone_no" placeholder="Phone" maxlength="30">
                                <label for="floatingInput">Phone</label>
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
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard delete-btn">Delete</button>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3">Discard</button>
                    <button type="submit" class="btn btn-submit-form" onclick="saveForm()">Save</button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="mb-5">
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Customer</h4>
        <button class="btn btn-show-form btn-add btn-block" data-btn="create-modal" style="width: 176px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Create Customer
        </button>
    </div>
    <div class="mb-2">
        <h5 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">= List Customer</h5>
        <input class="form-control" placeholder="Search" style="width: 30%" value="" />
    </div>
    <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-pbtc" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody class="body-table" id="body-table" style="cursor: pointer;">
                <?php
                if (!empty($dataCustomers)) {
                    foreach ($dataCustomers as $customer) {
                ?>
                        <tr class="row-table" data-id="<?= $customer->id; ?>">
                            <td><?= $customer->name; ?></td>
                            <td><?= $customer->address; ?></td>
                            <td><?= $customer->phone; ?></td>
                            <td><?= $customer->email; ?></td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    $(document).ready(function() {
        var validator = $(".create-form").validate({
            rules: {
                name: {
                    required: true
                },
                address: {
                    required: true
                },
                province: {
                    required: true
                },
                city: {
                    required: true
                },
                zip_code: {
                    required: true
                },
                phone_no: {
                    required: true
                },
                email: {
                    email: true,
                },
            },
            messages: {
                name: {
                    required: "Name is required"
                },
                address: {
                    required: "Address is required"
                },
                province: {
                    required: "Province is required"
                },
                city: {
                    required: "City is required"
                },
                zip_code: {
                    required: "Zip code is required"
                },
                phone_no: {
                    required: "Phone is required"
                },
                email: {
                    email: "Email must be valid",
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



        $(".btn-show-form").click(function() {
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show")
        })


        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
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

                        $.ajax({
                            url: "<?= base_url("customer/save"); ?>",
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
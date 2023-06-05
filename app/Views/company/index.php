<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Company</h5>
            </div>
            <div class="modal-body">
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <img class="preview-photo" height="230" width="175" id="preview_photo" src="<?= base_url() ?>assets/img/avatar/logo.png" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6" style="height: 50px;">
                            <input onchange="previewPhoto();" type="file" class="form-control input-image logo" id="logo" name="logo" accept="image/png, image/jpg, image/jpeg">
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control company" id="company" name="company" placeholder="Company">
                                <label for="floatingInput">Company</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control holding_company" id="holding_company" name="holding_company" placeholder="Holding Company">
                                <label for="floatingInput">Holding Company</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control address" id="address" name="address" placeholder="Address">
                                <label for="floatingInput">Address</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select province_id" name="province_id" id="province_id" onchange="getCity()">
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
                                <label for="floatingInput">Province</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select city_id" name="city_id" id="city_id">
                                    <option value=""></option>
                                </select>
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
                                <input type="text" class="form-control phone" id="phone" name="phone" placeholder="Phone" maxlength="30">
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
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select pic_id" name="pic_id" id="pic_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataEmployees)) {
                                        foreach ($dataEmployees as $employee) {
                                    ?>
                                            <option value="<?= $employee->id; ?>"><?= $employee->name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">PIC</label>
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
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Company</h4>
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
                    <th>Company</th>
                    <th>Holding Company</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
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

        var validator = $(".create-form").validate({
            rules: {
                company: {
                    required: true
                },
                holding_company: {
                    required: true
                },
                address: {
                    required: true
                },
                phone: {
                    required: true
                },
                zip_code: {
                    required: true
                },
                province_id: {
                    required: true
                },
                city_id: {
                    required: true
                },
                pic_id: {
                    required: true
                },
                email: {
                    required: true,
                    email: true,
                },
            },
            messages: {
                logo: {
                    required: "Logo is Required"
                },
                company: {
                    required: "Company is required"
                },
                holding_company: {
                    required: "Holding Company is required"
                },
                address: {
                    required: "Address is required"
                },
                phone: {
                    required: "Phone is required"
                },
                zip_code: {
                    required: "Zip Code is required"
                },
                province_id: {
                    required: "Province is required"
                },
                city_id: {
                    required: "City is required"
                },
                pic_id: {
                    required: "PIC is required"
                },
                email: {
                    required: "Email is required",
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
            $(".id").val("");
            $('.logo').rules('add', {
                required: true
            });
            $(".title-name").text("Add New");
            $(".province_id").val('').change();
            $(".city_id").val('').change();
            $(".pic_id").val('').change();

            $(".city_id").empty()
            $(".city_id").append(`<option value=""></option>`)

            document.getElementById("preview_photo").src = "<?= base_url() ?>assets/img/avatar/logo.png";
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
                url: "<?= base_url("company/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                }
            },
            // scrollX: true,
            "initComplete": function (settings, json) {    
                $('.dataTables_length').empty();    
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show 25 Entries</label></div>"); 
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            //responsive: true,
            display: "stripe",
            searching: false,
            columns: [{
                data: "company",
                className: "text-left"
            },
            {
                data: "holding_company",
                className: "text-left"
            },
            {
                data: "address",
                className: "text-left"
            },
            {
                data: "phone",
                className: "text-left"
            },
            {
                data: "email",
                className: "text-left"
            },],
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

        $(".dataTable_info").addClass("pt-0");

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $('.logo').rules('remove', 'required');
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $.ajax({
                url: "<?= base_url("company/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".company").val(res?.data?.company);
                        $(".holding_company").val(res?.data?.holding_company);
                        $(".pic_id").val(res?.data?.pic_id).change();
                        $(".address").val(res?.data?.address);
                        $(".email").val(res?.data?.email);
                        $(".phone").val(res?.data?.phone);
                        $(".zip_code").val(res?.data?.zip_code);
                        $(".province_id").val(res?.data?.province_id).change();

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
                                    $(".city_id").append(`<option value="${item.id}">${item.city_name}</option>`)
                                })

                                $(".city_id").val(res?.data?.city_id).change();
                            }
                        })

                        document.getElementById("preview_photo").src = res?.data?.logo;
                        validator.resetForm();
                        validator.reset();
                        $(".add-modal").modal("show")
                        console.log(res.data);
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

                        let id = $(".id").val();
                        // UPDATE
                        if(id)
                        {
                            $.ajax({
                                url: "<?= base_url("company/update"); ?>",
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
                                url: "<?= base_url("company/save"); ?>",
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
                        url: "<?= base_url("company/delete"); ?>",
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
                        $(".city_id").append(`<option value="${item.id}">${item.city_name}</option>`)
                    })
                }
            })
        }
    }

    //change picture
    const previewPhoto = function() {
        let file = document.getElementById("logo").files[0];
        document.getElementById("preview_photo").src = window.URL.createObjectURL(file);
    }
</script>

<?= $this->endSection(); ?>
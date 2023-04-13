<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Role</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control name" id="name" name="name" placeholder="Full Name" maxlength="30">
                                <label for="floatingInput">Role</label>
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
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Role</h4>
        <button class="btn btn-show-form btn-add btn-block" data-btn="create-modal" style="width: 176px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Create Role
        </button>
   </div>
   <div class="mb-2">
        <h5 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">= List Role</h5>
        <input class="form-control" placeholder="Search" style="width: 30%" value="" />
   </div>
   <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-pbtc" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody class="body-table" id="body-table" style="cursor: pointer;">
            <?php
                if (!empty($dataRole)) {
                    foreach ($dataRole as $role) {
                ?>
                    <tr class="row-table" data-id="<?= $role->id; ?>">
                        <td><?= $role->name; ?></td>
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
        // $('.division_id').select2({
        //     theme: 'bootstrap4'
        // })
        var validator = $(".create-form").validate({
            rules: {
                name: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Full Name is Required"
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
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');                      

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');   
            },
        });

        $(".btn-show-form").click(function() {
            $(".title-name").text("Create");
            validator.resetForm();
            validator.reset();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
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
                        url: "<?= base_url("role/delete"); ?>",
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
                                    $(".add-modal").modal("hide")

                                    var tag_html = "";

                                    // TABLE SEMENTARA RELOAD
                                    $.ajax({
                                        url: "<?= base_url("role/all"); ?>",
                                        method: "GET",
                                        dataType: "json",
                                        success: function(res) {
                                            if (res.status) {
                                                $(".body-table").empty();
                                                res.data.forEach((item) => {
                                                    tag_html += `<tr class="row-table" style="cursor: pointer;" data-id='`+ item.id +`'>`;
                                                    tag_html += "<td>";
                                                    tag_html += item.name;
                                                    tag_html += "</td>";
                                                    tag_html += "</tr>";
                                                })
                                                $(".body-table").append(tag_html);
                                            }
                                            else
                                            {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                            }
                                        }
                                    })


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

        $(document).on('click', '.row-table', function() {
            $('.employeeImg').rules('remove', 'required');
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = $(this).data('id');
            $(".title-name").text("Update");

            $.ajax({
                url: "<?= base_url("role/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".name").val(res?.data?.name);
                        validator.resetForm();
                        validator.reset();
                        $(".add-modal").modal("show")
                        console.log(res.data);
                    }
                    else
                    {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })
    })

    const saveForm = function() {
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
                            url: "<?= base_url("role/update"); ?>",
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

                                        var tag_html = "";

                                        // TABLE SEMENTARA RELOAD
                                        $.ajax({
                                            url: "<?= base_url("role/all"); ?>",
                                            method: "GET",
                                            dataType: "json",
                                            success: function(res) {
                                                if (res.status) {
                                                    $(".body-table").empty();
                                                    res.data.forEach((item) => {
                                                        tag_html += `<tr class="row-table" style="cursor: pointer;" data-id='`+ item.id +`'>`;
                                                        tag_html += "<td>";
                                                        tag_html += item.name;
                                                        tag_html += "</td>";
                                                        tag_html += "</tr>";
                                                    })
                                                    $(".body-table").append(tag_html);
                                                }
                                                else
                                                {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: response.message,
                                                        confirmButtonColor: '#4e73df',
                                                    })
                                                }
                                            }
                                        })


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
                            url: "<?= base_url("role/save"); ?>",
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

                                        var tag_html = "";

                                        // TABLE SEMENTARA RELOAD
                                        $.ajax({
                                            url: "<?= base_url("role/all"); ?>",
                                            method: "GET",
                                            dataType: "json",
                                            success: function(res) {
                                                if (res.status) {
                                                    $(".body-table").empty();
                                                    res.data.forEach((item) => {
                                                        tag_html += `<tr class="row-table" style="cursor: pointer;" data-id='`+ item.id +`'>`;
                                                        tag_html += "<td>";
                                                        tag_html += item.name;
                                                        tag_html += "</td>";
                                                        tag_html += "</tr>";
                                                    })
                                                    $(".body-table").append(tag_html);
                                                }
                                                else
                                                {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: response.message,
                                                        confirmButtonColor: '#4e73df',
                                                    })
                                                }
                                            }
                                        })


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
    }
</script>

<?= $this->endSection(); ?>
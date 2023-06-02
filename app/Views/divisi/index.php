<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Divisi</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control divisi" id="divisi" name="divisi">
                                <label for="floatingInput">Divisi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="number" class="form-control libur" id="libur" name="libur">
                                <label for="floatingInput">Libur</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="number" class="form-control jam_kerja" id="jam_kerja" name="jam_kerja">
                                <label for="floatingInput">Jam Kerja</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input onkeydown="return false" class="form-control input-picker jam_istirahat" id="jam_istirahat" name="jam_istirahat">
                                    <label for="floatingInput">Jam Istirahat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                        <i class="fa fa-clock fa-jam-istirahat icon-form"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input onkeydown="return false" class="form-control input-picker jam_masuk" id="jam_masuk" name="jam_masuk">
                                    <label for="floatingInput">Jam Masuk</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                        <i class="fa fa-clock fa-jam-masuk icon-form"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input onkeydown="return false" class="form-control input-picker jam_pulang" id="jam_pulang" name="jam_pulang">
                                    <label for="floatingInput">Jam Keluar</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                        <i class="fa fa-clock fa-jam-pulang icon-form"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input onkeydown="return false" class="form-control input-picker mulai_istirahat" id="mulai_istirahat" name="mulai_istirahat">
                                    <label for="floatingInput">Jam Mulai Istirahat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                        <i class="fa fa-clock fa-mulai-istirahat icon-form"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input onkeydown="return false" class="form-control input-picker selesai_istirahat" id="selesai_istirahat" name="selesai_istirahat">
                                    <label for="floatingInput">Jam Selesai Istirahat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                        <i class="fa fa-clock fa-selesai-istirahat icon-form"></i>
                                    </span>
                                </div>
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
    <div class="mb-5">
        <h4 style="padding-top: 6px; color: #3B4758;" class="m-0 font-weight-bold my-2">Divisi</h4>
        <button class="btn btn-show-form btn-add btn-block" data-btn="create-modal" style="width: 176px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add New
        </button>
   </div>
   <div class="mb-2">
        <input class="form-control search" placeholder="Search" style="width: 30%" value="" />
   </div>
   <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th>Divisi</th>
                    <th>Libur</th>
                    <th>Jam Kerja</th>
                    <th>Jam Istirahat</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Jam Mulai Istirahat</th>
                    <th>Jam Selesai Istirahat</th>
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
        $(".jam_istirahat, .jam_masuk, .jam_pulang, .mulai_istirahat, .selesai_istirahat").datetimepicker({
            format: 'HH:mm',
            useCurrent: false,
            showTodayButton: true,
            showClear: true,
            toolbarPlacement: 'bottom',
            sideBySide: true,
            showClose: true,
            icons: {
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                today: "fa fa-clock",
                clear: "fa fa-trash",
                close: "fa fa-close"
            }
        });

        $('.fa-jam-istirahat').click(function() {
            $(".jam_istirahat").focus();
        });

        $('.fa-jam-masuk').click(function() {
            $(".jam_masuk").focus();
        });

        $('.fa-jam-pulang').click(function() {
            $(".jam_pulang").focus();
        });

        $('.fa-mulai-istirahat').click(function() {
            $(".mulai_istirahat").focus();
        });

        $('.fa-selesai-istirahat').click(function() {
            $(".selesai_istirahat").focus();
        });

        var validator = $(".create-form").validate({
            rules: {
                divisi: {
                    required: true
                },
                libur: {
                    required: true
                },
                jam_kerja: {
                    required: true
                },
                jam_istirahat: {
                    required: true
                },
                jam_masuk: {
                    required: true
                },
                jam_pulang: {
                    required: true
                },
                mulai_istirahat: {
                    required: true
                },
                selesai_istirahat: {
                    required: true
                }
            },
            messages: {
                divisi: {
                    required: "Divisi is Required"
                },
                libur: {
                    required: "Libur is Required"
                },
                jam_kerja: {
                    required: "Jam Kerja is Required"
                },
                jam_istirahat: {
                    required: "Jam Istirahat is Required"
                },
                jam_masuk: {
                    required: "Jam Masuk is Required"
                },
                jam_pulang: {
                    required: "Jam Pulang is Required"
                },
                mulai_istirahat: {
                    required: "Mulai Istirahat is Required"
                },
                selesai_istirahat: {
                    required: "Selesai Istirahat is Required"
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
            $(".id").val("");
            validator.resetForm();
            validator.reset();
            $(".title-name").text("Add New");
            $(".divisi").val('').change();
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
                url: "<?= base_url("divisi/all"); ?>",
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
                data: "divisi",
                className: "text-left"
            },
            {
                data: "libur",
                className: "text-left"
            },
            {
                data: "jam_kerja",
                className: "text-left"
            },
            {
                data: "jam_istirahat",
                className: "text-left"
            },
            {
                data: "jam_masuk",
                className: "text-left"
            },
            {
                data: "jam_pulang",
                className: "text-left"
            },
            {
                data: "mulai_istirahat",
                className: "text-left"
            },
            {
                data: "selesai_istirahat",
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

        $(".dataTable_info").addClass("pt-0");

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $('.logo').rules('remove', 'required');
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $.ajax({
                url: "<?= base_url("divisi/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".divisi").val(res?.data?.divisi);
                        $(".libur").val(res?.data?.libur);
                        $(".jam_kerja").val(res?.data?.jam_kerja);
                        $(".jam_istirahat").val(res?.data?.jam_istirahat);
                        $(".jam_masuk").val(res?.data?.jam_masuk);
                        $(".jam_pulang").val(res?.data?.jam_pulang);
                        $(".mulai_istirahat").val(res?.data?.mulai_istirahat);
                        $(".selesai_istirahat").val(res?.data?.selesai_istirahat);
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
                                url: "<?= base_url("divisi/update"); ?>",
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
                                url: "<?= base_url("divisi/save"); ?>",
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
                        url: "<?= base_url("divisi/delete"); ?>",
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
</script>

<?= $this->endSection(); ?>
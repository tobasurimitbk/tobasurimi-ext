<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Employee</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-3 mb-3 view-cols-image">
                            <img class="preview-photo" id="preview_photo" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAACCCAMAAAC93eDPAAAAMFBMVEXk5ueutLfn6eqyt7qrsbTh4+TDx8q2u77Z3N3Jzc/U19nO0dPq7Oy8wcSnrrHd4OEuWFw9AAADAUlEQVR4nO2a23LjIAxAjQAbsIH//9vFTjpNUhckRyI7u5ynTF84lSVuYpoGg8FgMBgMBoPB4H8DYNs2gE+NvkEOfo4xzj7YD2jAFKIy3+jZ9bUA67Ux6hFjlnXqJwH+Zfy7hQ5bJwF3KrCTou0SCP+bwBEIJ+8Ac/rdYJcI0g6wVGJw+xirrAPElkGJg6gDzG0D2W8BK8agOIgZTBlnoNQiFQZYkAZiKQmhXo5PWBGFSeMNzCwRBmwu3h0kwoDPhEPBC4TBUYJQ4F80cbPSN4l/vdpoMZBISEuoyIOF24BWDwfsNTFTDZLjVohkBfZJmjA13uDPR7KCYlcgG6jIrUAuiH9SgWzAr/D5iqAt1YcC+3JNn5q4t/JQO0meK2ReA9re9UAzGxSoCpHdYCPmo8TJkrhhSOwCZdtECwL7xDRR968m8BsQDrU7MgdbShgEtvAHFq0gkgk7+F10EjpY73MDzkHyxgtXmCYKXsIC5mxr2M9RZActfAkMoeFgBFbIVwenahJi5fjkYCt1IXK5csLm07lEUrlXUwTKNu7nDiZ164jcJCa/mIdYGKNi6NgXukvkNepbh0wtc8gfaRXCBpPN2drjV+/BYf+fc3YuFJxz2d7/1mf0MnbwcdFafTUqjzlxieVr2EncA8D5qFM6bxKmpEtWCFrAZp/L4JziIdO5BcheN4f/LlF2C4C9Q40b/8tiWRlXTID1tUONslAz02QF00oLwINEmjkiAUFfFLhFwr8bCciI3miVsni95bCdvxIgSsTrgYCM3LS3uNy+hXA1DX86XLt928i3SzWHK8fc1jsFqoOmJ8S7lfAT4r4S806BiKEdcQQMFO0aEDxrHnxBOGw2D22XHdBHrSwjoPCXkdiLjCvgbiPpF94UFtSBS9IA1SKgduipIKqCdMF5gXZGSgehTFDNLyEsUMLQaqdLzUoPCo29g8zi8OJQr0uQN2g9bMgi69Mz9SuxC89FLlBtnMmX5E5939AhGxvPISHqDtTf+kAXqh9iMBgMBn8LfwAfLCKVi1nppAAAAABJRU5ErkJggg==" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-input-files-img" style="height: 50px;">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onchange="previewPhoto();" type="file" class="form-control input-image employeeImg" id="employeeImg" name="employeeImg" accept="image/png, image/jpg, image/jpeg">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nip" id="nip" name="nip" placeholder="Nip" maxlength="30">
                                <label for="floatingInput">NIP</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select division_id" name="division_id" id="division_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Divisi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control name" id="name" name="name" placeholder="Full Name" maxlength="30">
                                <label for="floatingInput">Nama Lengkap</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control phone_no" id="phone_no" name="phone_no" placeholder="Phone" maxlength="30">
                                <label for="floatingInput">Nomor Telepon</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="email" class="form-control email" id="email" name="email" placeholder="Email" maxlength="30">
                                <label for="floatingInput">Email</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control address" id="address" name="address" placeholder="Address">
                                <label for="floatingInput">Alamat</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input class="form-control input-picker dob" id="dob" name="dob" placeholder="Date of Birth">
                                        <label for="floatingInput">Tanggal Lahir</label>
                                    </div>
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                            <i class="fa fa-calendar icon-form"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select gender" name="gender" id="floatingSelect" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="Pria">Pria</option>
                                    <option value="Wanita">Wanita</option>
                                </select>
                                <label for="floatingInput">Jenis Kelamin</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control acc_no" id="acc_no" name="acc_no" placeholder="No. Rekening" maxlength="30">
                                <label for="floatingInput">No. Rekening</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-pin">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control pin" id="pin" name="pin" oninput="this.value=this.value.replace(/[^0-9]/g,'');" placeholder="PIN">
                                <label for="floatingInput">PIN</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select status" name="status" id="floatingSelect" aria-label="Floating label select example">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Non Aktif">Non Aktif</option>
                                </select>
                                <label for="floatingInput">Status</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="d-flex">
                    <button type="button" class="btn btn-discard delete-btn">Hapus</button>
                </div>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Employee</h1>
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
                <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th onclick="changeSort('nip')" class="sort">NIP</th>
                            <th onclick="changeSort('name')" class="sort">Nama Lengkap</th>
                            <th onclick="changeSort('divisionName')" class="sort">Divisi</th>
                            <th onclick="changeSort('email')" class="sort">Email</th>
                            <th onclick="changeSort('phone_no')" class="sort">No. Telepon</th>
                            <th onclick="changeSort('address')" class="sort">Alamat</th>
                            <th onclick="changeSort('dob')" class="sort">Tanggal Lahir</th>
                            <th onclick="changeSort('gender')" class="sort">Jenis Kelamin</th>
                            <th onclick="changeSort('acc_no')" class="sort">No. Rekening</th>
                            <th onclick="changeSort('status')" class="sort">Status</th>
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
    let sort = "nip";
    let sortType = "asc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[0, 'asc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("employee/all"); ?>",
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
            data: "nip",
            className: "text-center"
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "divisionName",
            className: "text-center"
        }, {
            data: "email",
            className: "text-center"
        }, {
            data: "phone_no",
            className: "text-center"
        }, {
            data: "address",
            className: "text-center"
        }, {
            data: "dob",
            className: "text-center"
        }, {
            data: "gender",
            className: "text-center"
        }, {
            data: "acc_no",
            className: "text-center"
        }, {
            data: "status",
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
                nip: {
                    required: true
                },
                name: {
                    required: true
                },
                gender: {
                    required: true
                },
                dob: {
                    required: true
                },
                address: {
                    required: true,
                },
                phone_no: {
                    required: true
                },
                email: {
                    email: true,
                    required: true
                },
                acc_no: {
                    required: true
                },
                division_id: {
                    required: true,
                }
            },
            messages: {
                employeeImg: {
                    required: "Foto wajib diisi"
                },
                nip: {
                    required: "NIP wajib diisi"
                },
                name: {
                    required: "Nama Lengkap wajib diisi"
                },
                gender: {
                    required: "Jenis Kelamin wajib diisi"
                },
                dob: {
                    required: "Tanggal Lahir wajib diisi"
                },
                address: {
                    required: "Alamat wajib diisi"
                },
                phone_no: {
                    required: "Nomor Telepon wajib diisi"
                },
                email: {
                    email: "Email harus benar",
                    required: "Email harus diisi"
                },
                acc_no: {
                    required: "Nomor Rekening wajib diisi"
                },
                division_id: {
                    required: "Divisi wajib diisi"
                },
                pin: {
                    required: "PIN wajib diisi",
                    minlength: "Minimal dan Maksimal 6 Karakter",
                    maxlength: "Minimal dan Maksimal 6 Karakter"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $(".select2-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.col-md-6').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.col-md-6').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $(".dob").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $('.fa-calendar').click(function() {
            $(".dob").focus();
        });

        $(".phone_no").mask("0000000000000")

        $(".acc_no").mask("000000000000000")

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".form-pin").css("display", "");
            $('.employeeImg').rules('add', {
                required: true
            });
            $('.pin').rules('add', {
                required: true,
                minlength: 6,
                maxlength: 6
            });
            $(".title-name").text("Tambah");
            validator.resetForm();
            validator.reset();
            document.getElementById("preview_photo").src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAACCCAMAAAC93eDPAAAAMFBMVEXk5ueutLfn6eqyt7qrsbTh4+TDx8q2u77Z3N3Jzc/U19nO0dPq7Oy8wcSnrrHd4OEuWFw9AAADAUlEQVR4nO2a23LjIAxAjQAbsIH//9vFTjpNUhckRyI7u5ynTF84lSVuYpoGg8FgMBgMBoPB4H8DYNs2gE+NvkEOfo4xzj7YD2jAFKIy3+jZ9bUA67Ux6hFjlnXqJwH+Zfy7hQ5bJwF3KrCTou0SCP+bwBEIJ+8Ac/rdYJcI0g6wVGJw+xirrAPElkGJg6gDzG0D2W8BK8agOIgZTBlnoNQiFQZYkAZiKQmhXo5PWBGFSeMNzCwRBmwu3h0kwoDPhEPBC4TBUYJQ4F80cbPSN4l/vdpoMZBISEuoyIOF24BWDwfsNTFTDZLjVohkBfZJmjA13uDPR7KCYlcgG6jIrUAuiH9SgWzAr/D5iqAt1YcC+3JNn5q4t/JQO0meK2ReA9re9UAzGxSoCpHdYCPmo8TJkrhhSOwCZdtECwL7xDRR968m8BsQDrU7MgdbShgEtvAHFq0gkgk7+F10EjpY73MDzkHyxgtXmCYKXsIC5mxr2M9RZActfAkMoeFgBFbIVwenahJi5fjkYCt1IXK5csLm07lEUrlXUwTKNu7nDiZ164jcJCa/mIdYGKNi6NgXukvkNepbh0wtc8gfaRXCBpPN2drjV+/BYf+fc3YuFJxz2d7/1mf0MnbwcdFafTUqjzlxieVr2EncA8D5qFM6bxKmpEtWCFrAZp/L4JziIdO5BcheN4f/LlF2C4C9Q40b/8tiWRlXTID1tUONslAz02QF00oLwINEmjkiAUFfFLhFwr8bCciI3miVsni95bCdvxIgSsTrgYCM3LS3uNy+hXA1DX86XLt928i3SzWHK8fc1jsFqoOmJ8S7lfAT4r4S806BiKEdcQQMFO0aEDxrHnxBOGw2D22XHdBHrSwjoPCXkdiLjCvgbiPpF94UFtSBS9IA1SKgduipIKqCdMF5gXZGSgehTFDNLyEsUMLQaqdLzUoPCo29g8zi8OJQr0uQN2g9bMgi69Mz9SuxC89FLlBtnMmX5E5939AhGxvPISHqDtTf+kAXqh9iMBgMBn8LfwAfLCKVi1nppAAAAABJRU5ErkJggg==";
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');

            $.ajax({
                url: `<?= base_url("divisi/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".division_id").empty()
                    $(".division_id").val("").change()
                    $(".divisionid").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".division_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                    })

                    $(".division_id").val('').change();
                    $(".add-modal").modal("show")
                }
            })
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
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
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("employee/update"); ?>",
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
                        else {
                            $.ajax({
                                url: "<?= base_url("employee/save"); ?>",
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
                        url: "<?= base_url("employee/delete"); ?>",
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

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".form-pin").css("display", "none");
            $('.employeeImg').rules('remove', 'required');
            $('.pin').rules('remove', 'required');
            $('.pin').rules('remove', 'minlength');
            $('.pin').rules('remove', 'maxlength');
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            validator.resetForm();
            validator.reset();

            $.ajax({
                url: "<?= base_url("employee/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".address").val(res?.data?.address);
                        $(".acc_no").val(res?.data?.acc_no);
                        $(".dob").val(res?.data?.dob);
                        $(".email").val(res?.data?.email);
                        $(".gender").val(res?.data?.gender);
                        $(".name").val(res?.data?.employee_name);
                        $(".nip").val(res?.data?.nip);
                        $(".phone_no").val(res?.data?.phone_no);
                        $(".status").val(res?.data?.status);
                        document.getElementById("preview_photo").src = res?.data?.employee_img;

                        $.ajax({
                            url: `<?= base_url("divisi/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".division_id").empty()
                                $(".division_id").val("").change()
                                $(".divisionid").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".division_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                                })

                                $(".division_id").val(res?.data?.division_id);
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
    })

    //change picture
    const previewPhoto = function() {
        let file = document.getElementById("employeeImg").files[0];
        document.getElementById("preview_photo").src = window.URL.createObjectURL(file);
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
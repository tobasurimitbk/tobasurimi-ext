<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Mesin Finger Outsource</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="nama" name="nama" placeholder="Nama">
                                <label for="floatingInput">Nama</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control ip" id="ip" name="ip" placeholder="IP Unit">
                                <label for="floatingInput">IP Unit</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control unit_key" id="unit_key" name="unit_key" placeholder="Unit Key">
                                <label for="floatingInput">Unit Key</label>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="ffloat mb-3" style="height: 50px;">
                                <label for="floatingInput">Master</label>
                                <div>
                                    <input autocomplete="one-time-code" class="master" name="master" id="master" value="1" type="checkbox">
                                </div>
                            </div>

                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form" id="btnSubmitForm">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal ping-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Ping IP Mesin Finger</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control ip_finger" id="ip_finger" name="ip_finger" placeholder="IP Finger" readonly>
                                <label for="floatingInput">IP Mesin Finger</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-success" role="alert" id="alertOK">

                            </div>
                            <div class="alert alert-danger" role="alert" id="alertFailed">

                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-ping mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form" onclick="pingAction()">Test Ping Finger</button>
            </div>
        </div>
    </div>
</div>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Mesin Finger</h1>
        <!-- <button class="btn btn-copy-unit btn-add" float-right data-btn="create-modal" style="right:130px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Copy Data Finger
        </button> -->
        <?php if (can('Personalia', 'Mesin Finger', 'c')): ?>
            <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Data" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('name')" class="sort">Nama</th>
                                <th onclick="changeSort('ip')" class="sort">IP Unit</th>
                                <th style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "nomor";
    let sortType = "desc";

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
            url: "<?= base_url("hr-outsourcing-finger-machine/attendances-unit/all"); ?>",
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
                width: "3%"
            }, {
                data: "name",
                className: "text-left"
            },
            {
                data: "ip",
                className: "text-left"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let ip = row.ip;
                    let res = '';

                    res += `
                    <?php if (can('Personalia', 'Mesin Finger', 'u')): ?>
                        <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    <?php endif ?>
                    <?php if (can('Personalia', 'Mesin Finger', 'd')): ?>
                        <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    <?php endif ?>
                     <a href="javascript:void(0)" onclick="pingModal('${ip}')" data-toggle="tooltip" title="Ping Finger" class="btn btn-info">
                           <i class="fa-solid fa-wifi"></i>
                        </a>
                         <a href="javascript:void(0)" onclick="resetFinger('${id}')" data-toggle="tooltip" title="Reset Finger" class="btn btn-warning">
                            <i class="fa-solid fa-rotate"></i>
                        </a>
                    `;

                    return res;
                }
            }
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var validator = $(".create-form").validate({
        rules: {
            nama: {
                required: true
            },
            ip: {
                required: true
            },
            unit_key: {
                required: true
            }

        },
        messages: {
            nama: {
                required: "Tanggal wajib diisi"
            },
            ip: {
                required: "Nama wajib diisi"
            },
            unit_key: {
                required: "Unit Key wajib diisi"
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


    $(".btn-copy-unit").click(function() {
        $(".copy-modal").modal("show")
    })


    $(".btn-show-form").click(function() {
        $(".id").val("");
        $(".title-name").text("Tambah");
        validator.resetForm();
        validator.reset();
        $(".create-form")[0].reset()
        $(".delete-btn").css('display', 'none');
        $(".add-modal").modal("show")
    })

    $(".btn-hide-form").click(function() {
        $(".add-modal").modal("hide")
    })

    $(".btn-hide-copy").click(function() {
        $(".copy-modal").modal("hide")
    })

    $(".dataTable_info").addClass("pt-0");

    function edit(id) {
        $(".create-form")[0].reset()
        $(".delete-btn").css('display', '');
        $(".title-name").text("Update");

        validator.resetForm();
        validator.reset();

        $.ajax({
            url: "<?= base_url("hr-outsourcing-finger-machine/attendances-unit/id"); ?>" + "/" + id,
            method: "GET",
            dataType: "json",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    $(".id").val(id);
                    $("#nama").val(res.data.name);
                    $("#ip").val(res.data.ip);
                    $("#unit_key").val(res.data.unit_key);
                    if (res.data.master == '1')
                        $('#master').attr('checked', true);
                    else
                        $('#master').attr('checked', false);

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
    }

    function remove(id) {
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
                setLoading()
                $.ajax({
                    url: "<?= base_url("hr-outsourcing-finger-machine/attendances-unit/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
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
                        }
                    },
                });
            }
        });
    }

    function pingModal(ip) {
        $('#alertOK,#alertFailed').hide();
        $('#ip_finger').val(ip);
        $('.ping-modal').modal('show');
    }

    function pingAction() {
        $('#alertOK,#alertFailed').hide();

        const ipFinger = $('#ip_finger').val();
        const csrf = $(`[name="${csrfToken}"]`);
        const formData = new FormData();
        formData.set('ip_finger', ipFinger);
        $.ajax({
            url: "<?= base_url("hr-outsourcing-finger-machine/attendances-unit/ping"); ?>",
            beforeSend: function(xhr) {
                setLoading();
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            data: formData,
            complete: function() {
                stopLoading();
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                if (response.status == false) {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    });
                } else {
                    if (response.data.status_finger) {
                        $('#alertOK').show();
                        $('#alertOK').html('<i class="fa-solid fa-fingerprint fa-lg mr-1"></i> ' + response.data.message);
                    } else {
                        $('#alertFailed').show();
                        $('#alertFailed').html('<i class="fa-solid fa-fingerprint fa-lg mr-1"></i> ' + response.data.message);
                    }
                }
                // if (response.status) {
                //     Swal.fire({
                //             icon: 'success',
                //             title: response.message,
                //             confirmButtonColor: '#4e73df',
                //         })
                //         .then(() => {
                //             $(".copy-modal").modal("hide")
                //         })
                // } else {
                //     Swal.fire({
                //         icon: 'error',
                //         title: response.message,
                //         confirmButtonColor: '#4e73df',
                //     })
                //     stopLoading()
                // }
            }
        });
    }

    $(".search").keyup(function() {
        table.ajax.reload();
    });

    $('.btn-discard-ping').click(function(e) {
        e.preventDefault();
        $('.ping-modal').modal('hide');
    })

    $('#btnPilihSemua').click(function(e) {
        e.preventDefault();
        if (employeeIdArr.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Data karyawan kosong',
                confirmButtonColor: '#4e73df',
            });
            return '';
        } else {
            var employeeSelect = $("select[name='employee_id[]']");
            employeeSelect.empty();

            var emptyOption = $("<option></option>")
                .attr("value", "")
                .text("Pilih Karyawan");
            employeeSelect.append(emptyOption);
            $.each(employeeIdArr, function(index, data) {
                employeeIdSelectedFinger.push(data.id);
                var option = $("<option selected></option>")
                    .attr("value", data.id)
                    .text(data.name);
                employeeSelect.append(option);
            });
        }
    })


    function resetFinger(id) {
        Swal.fire({
            icon: 'question',
            title: 'Reset Log Absensi Mesin Finger ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                const formData = new FormData();
                formData.set('id', id);

                $.ajax({
                    url: "<?= base_url("hr-outsourcing-finger-machine/attendances-unit/reset-data-finger"); ?>",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    data: formData,
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
                                })
                                .then(() => {
                                    $(".copy-modal").modal("hide")
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    }
                });
            }
        })
    }

    $("#btnSubmitForm").click(function() {
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
                    let url = id == '' ? "<?= base_url("hr-outsourcing-finger-machine/attendances-unit/save"); ?>" : "<?= base_url("hr-outsourcing-finger-machine/attendances-unit/update"); ?>";
                    $.ajax({
                        url: url,
                        data: data,
                        beforeSend: function(xhr) {
                            setLoading();
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                            }
                        },
                    });
                }
            })
        }
    });

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
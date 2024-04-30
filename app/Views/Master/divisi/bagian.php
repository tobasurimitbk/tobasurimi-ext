<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name">Set Bagian Departemen</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("divisi"); ?>">
                Batal
            </a>

        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <table width="100%" class="mb-4">
                <tbody>
                    <tr style="color: black;">
                        <td width="150px">Departemen</td>
                        <td width="5px">:</td>
                        <td><?= $divisi['divisi'] ?></td>
                    </tr>
                    <tr style="color: black; height: 20px;">
                        <td colspan="3"></td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px">Jam Kerja</td>
                        <td width="25px">:</td>
                        <td><?= $divisi['jenis'] ?></td>
                    </tr>
                </tbody>
            </table>
            <form class="form-data" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control" name="kode_bagian" placeholder="Kode Bagian">
                                    <label for="floatingInput">Kode Bagian</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 20px; margin-left: -30px;" id="generate_new_code" name="generate_new_code" type="checkbox" onchange="generateNewCode()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" name="nama_bagian" id="nama_bagian">
                            <label for="floatingInput">Nama Bagian</label>
                        </div>
                    </div>
                </div>

            </form>
            <div class="col-subtitle-modal">
                <div class="row ">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right" onclick="submitForm()" id="btn-submit-bagian">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i> Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetForm()">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            <div>
                <div class="row justify-content-end mb-3">
                    <div class="col-md-2">
                        <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Kode / Nama Bagian" value="" />
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10">No</th>
                                <th>Kode Bagian</th>
                                <th>Nama Bagian</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "divisi";
    let sortType = "desc";

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
            url: "<?= base_url("divisi/bagian/data/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.divisionID = "<?= $divisi['id'] ?>";
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
                data: "kode_bagian",
                className: "text-center"
            }, {
                data: "nama_bagian",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                width: "10%",
                render: function(data, type, row) {
                    let id = row.id;
                    return `
                        <button onclick="updateForm(${id})"  class="btn btn-warning posting-spp">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button>
                        <button onclick="deleteForm(${id})" class="btn btn-danger">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    `
                }
            },
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "<?= "Tidak ada bagian Departemen " . $divisi['divisi'] ?>",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    var validator = $(".form-data").validate({
        rules: {
            kode_bagian: {
                required: true
            },
            nama_bagian: {
                required: true
            },
        },
        messages: {
            kode_bagian: {
                required: "Kode bagian wajib diisi"
            },
            nama_bagian: {
                required: "Nama bagian wajib diisi"
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

    function generateNewCode() {
        let csrfToken = '<?= csrf_token() ?>';
        let value = document.getElementById('generate_new_code').checked ? true : false;
        let csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $("input[name='kode_bagian']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("divisi/bagian/generate-new-kode"); ?>`,
                data: {
                    divisionID: "<?= $divisi['id'] ?>"
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                success: function(res) {
                    csrf.val(res.token);
                    $("input[name='kode_bagian']").attr("readonly", true);
                    $("input[name='kode_bagian']").val(res.codeNew);
                }
            })
        } else {
            $("input[name='kode_bagian']").attr("readonly", false);
            $("input[name='kode_bagian']").val("");
        }
    }

    function submitForm() {
        if ($(".form-data").valid()) {
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
                    let id = $('input[name="id"]').val();
                    let csrf = $(`[name="${csrfToken}"]`);
                    let namaBagian = $('input[name="nama_bagian"]').val();
                    let kodeBagian = $('input[name="kode_bagian"]').val();

                    var formData = new FormData();
                    formData.append('id', id);
                    formData.append('namaBagian', namaBagian);
                    formData.append('kodeBagian', kodeBagian);
                    formData.append('divisionID', "<?= $divisi['id'] ?>");

                    if (id) {
                        $.ajax({
                            url: "<?= base_url("divisi/bagian/update"); ?>" + "/" + id,
                            data: formData,
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
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            table.ajax.reload();
                                        });

                                    resetForm();

                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                }

                            }
                        });
                    } else {
                        $.ajax({
                            url: "<?= base_url("divisi/bagian/save"); ?>",
                            data: formData,
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
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            table.ajax.reload();
                                            resetForm();
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {

                                    });
                                }
                            }
                        });
                    }
                }
            })

        }
    }

    function resetForm() {
        let form = document.querySelector(".form-data");
        $("input[name='kode_bagian']").attr("readonly", false);
        $("#generate_new_code").attr('checked', false).show();
        $("input[name='id']").val(null);
        form.reset();
    }

    function updateForm(id) {
        let csrf = $(`[name="${csrfToken}"]`);
        $.ajax({
            url: "<?= base_url("divisi/bagian/id"); ?>" + "/" + id,
            data: null,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "GET",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                if (response.status) {
                    resetForm();
                    $('input[name="nama_bagian"]').val(response.data.nama_bagian);
                    $('input[name="kode_bagian"]').val(response.data.kode_bagian);
                    $("input[name='kode_bagian']").attr("readonly", true);
                    $('input[name="id"]').val(response.data.id);
                    $("#generate_new_code").attr('checked', true).hide();

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Gagal Diambil, coba Lagi',
                        confirmButtonColor: '#4e73df',
                    })
                }

            }
        });
    }

    function deleteForm(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Bagian?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            let csrf = $(`[name="${csrfToken}"]`);
            var formData = new FormData();
            formData.append('id', id);

            $.ajax({
                url: "<?= base_url("divisi/bagian/delete"); ?>" + "/" + id,
                data: formData,
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
                        Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            .then(() => {
                                table.ajax.reload();
                                resetForm();
                            })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        }).then(() => {

                        });
                    }
                }
            });
        });

    }
</script>

<?= $this->endSection(); ?>
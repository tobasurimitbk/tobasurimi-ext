<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Set Data Karyawan</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("/hr-outsourcing-company"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <table width="100%" class="mb-3">
                <tbody>
                    <tr style="color: black;">
                        <td width="150px">Nama company</td>
                        <td width="5px">:</td>
                        <td><?= $company['name'] ?></td>
                    </tr>
                    <tr style="color: black; height: 20px;">
                        <td colspan="3"></td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px">Alamat</td>
                        <td width="25px">:</td>
                        <td><?= $company['address'] ?>
                        <td>
                    </tr>
                </tbody>
            </table>
            <br>
            <form class="karyawan-form" role="form" method="POST">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="company_id" id="company_id" value="<?= $company['id'] ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input type="text" class="form-control badge_karyawan" id="badge_karyawan" name="badge_karyawan" placeholder="badge Karyawan" required>
                                    <label for="badge_karyawan">No Badge / Induk Karyawan</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input style="z-index: 99; margin-bottom: 20px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control nama" id="nama" name="nama" placeholder="Nama" required>
                            <label for="nama">Nama</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="date" class="form-control tanggal_masuk_kerja" id="tanggal_masuk_kerja" name="tanggal_masuk_kerja" placeholder="tanggal_masuk_kerja" required>
                            <label for="tanggal_masuk_kerja">Tanggal Masuk Kerja</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right btn-submit">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetForm()">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="row justify-content-end mb-3">
                    <div class="col-md-4">
                        <input class="form-control search form-out-search" placeholder="Cari Nama / badge Employee" />
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10" onclick="changeSort('hr_outsourcing_employee.id')">No</th>
                                <th onclick="changeSort('hr_outsourcing_employee.tanggal_masuk_kerja')">Tanggal Masuk Kerja</th>
                                <th onclick="changeSort('hr_outsourcing_employee.badge')">Badge</th>
                                <th onclick="changeSort('hr_outsourcing_employee.nama')">Nama</th>
                                <th style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "createdAt";
    let sortType = "desc";

    var table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25]
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("hr-outsourcing-company/employee/getByCompany"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.company_id = "<?= $company['id'] ?>";
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
            },
            {
                data: "tanggal_masuk_kerja",
                className: "text-center",
            },
            {
                data: "badge",
                className: "text-center",
            },
            {
                data: "nama",
                className: "text-center",
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-warning mr-1 edit-table-detail" data-id="${row.id}" data-badge="${row.badge}" data-tanggal_masuk_kerja="${row.tanggal_masuk_kerja}" data-nama="${row.nama}">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteForm('${row.id}')">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    `;
                }
            }
        ],
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
        $(document).on('click', '.edit-table-detail', function() {
            $('#id').val($(this).data('id'));
            $('#badge_karyawan').val($(this).data('badge'));
            $('#nama').val($(this).data('nama'));
            $('#tanggal_masuk_kerja').val($(this).data('tanggal_masuk_kerja'));
            $('#auto_generate').prop('checked', false);
            $(".badge_karyawan").attr("readonly", false);
        });
    });

    $('.search').keyup(function() {
        table.ajax.reload();
    });

    var validator = $(".karyawan-form").validate({
        rules: {
            badge_karyawan: {
                required: true
            },
            nama: {
                required: true
            }
        },
        messages: {
            badge_karyawan: {
                required: "badge karyawan wajib diisi"
            },
            nama: {
                required: "Nama wajib diisi"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
    });

    $(".btn-submit").click(function() {
        if ($('.karyawan-form').valid()) {
            const csrf = $(`[name="${csrfToken}"]`);
            let id = $('#id').val();
            let url = id ? "<?= base_url("hr-outsourcing-company/employee/update"); ?>" : "<?= base_url("hr-outsourcing-company/employee/save"); ?>";

            let data = {
                id: id,
                badge_karyawan: $('#badge_karyawan').val(),
                tanggal_masuk_kerja: $('#tanggal_masuk_kerja').val(),
                nama: $('#nama').val(),
                company_id: $('#company_id').val()
            };

            $.ajax({
                url: url,
                data: data,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                method: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        resetForm();
                        csrf.val(response.token);
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        });
                    }
                }
            });
        }
    });

    function changeStatus() {
        let value = $('#auto_generate').is(':checked');
        const csrf = $(`[name="${csrfToken}"]`);

        if (value) {
            $(".badge_karyawan").attr("readonly", true);
            $.ajax({
                url: "<?= base_url("hr-outsourcing-company/employee/generateKode"); ?>",
                method: "POST",
                data: {
                    company_id: $('#company_id').val()
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                dataType: "json",
                success: function(response) {
                    $(".badge_karyawan").val(response);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan pada sistem',
                        confirmButtonColor: '#4e73df',
                    });
                }
            });
        } else {
            $(".badge_karyawan").attr("readonly", false);
            $(".badge_karyawan").val("");
        }
    }

    function deleteForm(id) {
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
                $.ajax({
                    url: "<?= base_url("hr-outsourcing-company/employee/delete"); ?>",
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
                        if (response.status) {
                            table.ajax.reload();
                        }
                    }
                });
            }
        });
    }

    function resetForm() {
        validator.resetForm();
        $('#id').val('');
        $('#badge_karyawan').val('');
        $('#nama').val('');
        $('#auto_generate').prop('checked', false);
        $(".badge_karyawan").attr("readonly", false);
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
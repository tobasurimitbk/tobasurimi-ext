<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>List Form Lembur</h1>
        <?php if (can('Personalia', 'Form Lembur', 'p')) : ?>
            <button class="btn btn-discard btn-dropdown-export float-right" type="button" onclick="exportFormLembur()">
                <i class="fa fa-download"></i> Export
            </button>
        <?php endif; ?>
        <?php if (can('Personalia', 'Form Lembur', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("lembur/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-sm-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" value="<?= date('Y-m') ?>" class="form-control month" id="month" name="month" />
                            <label style="z-index: 1;" style="z-index: 1;">Pilih Bulan</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-floating">
                        <select class="form-select" name="divisi_id" id="divisi_id">
                            <option value="">
                                Cari Departemen
                            </option>
                            <?php foreach ($divisi as $d) : ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['divisi']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cari Departemen</label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-floating">
                        <select class="form-select" name="bagian_id" id="bagian_id">
                            <option value="">
                                Cari Bagian
                            </option>
                        </select>
                        <label for="floatingInput">Cari Bagian</label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-floating">
                        <select class="form-select" name="tipe" id="tipe">
                            <option value="">
                                Cari Tipe / Golongan
                            </option>
                            <?php foreach ($golongan as $g) : ?>
                                <option value="<?= $g['golongan_name'] ?>">
                                    <?= $g['golongan_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cari Tipe / Golongan</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search fos-jk mb-3" placeholder="Cari Data" id="search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;" class="sort">No</th>
                                <th onclick="changeSort('employees.nip')" class="sort">NIP</th>
                                <th onclick="changeSort('employees.name')" class="sort">Karyawan</th>
                                <th onclick="changeSort('employees.division_id')" class="sort">Dept</th>
                                <th onclick="changeSort('employees.bagian_id')" class="sort">Bagian</th>
                                <th onclick="changeSort('form_lembur.periode')" class="sort">Tgl Lembur</th>
                                <th onclick="changeSort('form_lembur.jam_mulai_lembur')" class="sort">Mulai Lembur</th>
                                <th onclick="changeSort('form_lembur.jam_selesai_lembur')" class="sort">Selesai Lembur</th>
                                <th onclick="changeSort('form_lembur.total_jam_lembur')" class="sort">Total Jam Lembur</th>
                                <th onclick="changeSort('form_lembur.total_uang_lembur')" class="sort">Nominal Lembur</th>
                                <th class="sort">Action</th>
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
    let sort = "form_lembur.id";
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
            url: "<?= base_url("lembur/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $('#search').val();
                data.month = $('#month').val();
                data.tipe = $('#tipe').val();
                data.divisi_id = $('#divisi_id').val();
                data.bagian_id = $('#bagian_id').val();
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
                data: "nip",
                className: "text-left",
                width: "10%"
            },
            {
                data: "name",
                className: "text-left"
            },
            {
                data: "divisi",
                className: "text-left"
            },
            {
                data: "nama_bagian",
                className: "text-left"
            },
            {
                data: "periode",
                className: "text-left"
            },
            {
                data: "jam_mulai_lembur",
                className: "text-left"
            },
            {
                data: "jam_selesai_lembur",
                className: "text-left"
            },
            {
                data: "total_jam_lembur",
                className: "text-left"
            },
            {
                data: "total_uang_lembur",
                className: "text-left"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                width: "10%",
                render: function(data, type, row) {
                    let id = row.id;
                    let res = '';

                    res += `
                  <?php if (can('Personalia', 'Form Lembur', 'u')): ?>
                        <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    <?php endif ?>
                  <?php if (can('Personalia', 'Form Lembur', 'd')): ?>
                        <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    <?php endif ?>
                 
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
            emptyTable: "Data Form Lembur tidak ada", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("lembur/id"); ?>/${data.id}`);
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    $(".month").datepicker({
        format: "yyyy-mm",
        startView: "months", // langsung tampilin bulan
        minViewMode: "months", // cuma bisa pilih bulan
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
    });

    $("#search").keyup(function() {
        table.ajax.reload();
    });

    $('#month').change(function() {
        table.ajax.reload();
    });

    $('#divisi_id,#tipe,#bagian_id').change(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $("#divisi_id").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    }).change(function(e) {
        e.preventDefault();
        let csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('divisionID', $(this).val());
        $.ajax({
            url: `<?= base_url("list-attendance/get-bagian"); ?>`,
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(result) {
                csrf.val(result.token);
                $("#bagian_id").empty()
                $("#bagian_id").append(`<option value=""></option>`)
                result.data.forEach(function(item) {
                    $("#bagian_id").append(`<option value="${item.id}">${item.kode_bagian.toUpperCase()} - ${item.nama_bagian.toUpperCase()}</option>`)
                });
            }
        });
    });

    $("#tipe").select2({
        placeholder: "Cari Tipe / Golongan",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("#bagian_id").select2({
        placeholder: "Cari Bagian",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.form-select')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    function edit(id) {
        location.replace(`<?= base_url("lembur/id"); ?>/${id}`);
    }

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Lembur?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("lembur/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading()
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
                                })
                        }
                    },

                });
            }
        });

    }

    function exportFormLembur() {
        var month = $('#month').val();
        var divisiId = $('#divisi_id').val();

        if (month == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih periode terlebih dahulu",
                confirmButtonColor: '#4e73df',
            })
        } else if (divisiId == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih departemen terlebih dahulu",
                confirmButtonColor: '#4e73df',
            })
        } else {
            var url = "<?= base_url('lembur/export-excel') ?>" + "?month=" + month + '&divisi_id=' + divisiId;
            window.location.href = url;
        }
    }
</script>

<?= $this->endSection(); ?>
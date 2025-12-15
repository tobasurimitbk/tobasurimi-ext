<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>List Custom Gaji Harian</h1>
        <?php if (can('Personalia', 'Custom Gaji Harian', 'c')): ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("custom-gaji-harian/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end">
                <?= csrf_field() ?>
                <div class="col-sm-3">
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
                <div class="col-md-3 mb-3">
                    <div class="form-floating">
                        <input autocomplete="one-time-code" class="form-control search" id="search" placeholder="Cari Data" value="" />
                        <label for="floatingInput">Cari Data</label>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('employees.nip')" class="sort">NIP</th>
                                <th onclick="changeSort('employees.name')" class="sort">Karyawan</th>
                                <th onclick="changeSort('employees.division_id')" class="sort">Dept</th>
                                <th onclick="changeSort('employees.division_id')" class="sort">Bagian</th>
                                <th onclick="changeSort('payroll_custom_gaji_harian.tanggal')" class="sort">Tgl</th>
                                <th onclick="changeSort('payroll_custom_gaji_harian.checkin')" class="sort">Masuk</th>
                                <th onclick="changeSort('payroll_custom_gaji_harian.checkout')" class="sort">Pulang</th>
                                <th onclick="changeSort('payroll_custom_gaji_harian.total_jam')" class="sort">Total Jam</th>
                                <th onclick="changeSort('payroll_custom_gaji_harian.nominal')" class="sort">Nominal</th>
                                <th onclick="changeSort('payroll_custom_gaji_harian.keterangan')" class="sort">Keterangan</th>
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
    let sort = "id";
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
            url: "<?= base_url("custom-gaji-harian/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $("#search").val();
                data.month = $('#month').val();
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
            className: "text-left"
        }, {
            data: "name",
            className: "text-left"
        }, {
            data: "divisi",
            className: "text-left"
        }, {
            data: "nama_bagian",
            className: "text-left"
        }, {
            data: "tanggal",
            className: "text-left"
        }, {
            data: "checkin",
            className: "text-left"
        }, {
            data: "checkout",
            className: "text-left"
        }, {
            data: "total_jam",
            className: "text-left"
        }, {
            data: "nominal",
            className: "text-left",
            render: function(data) {
                return greatFormatRupiah(data);
            }
        }, {
            data: "keterangan",
            className: "text-left"
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                let htmlRes = '';

                htmlRes += `
                    <?php if (can('Personalia', 'Custom Gaji Harian', 'u')): ?>
                        <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    <?php endif ?>
                    <?php if (can('Personalia', 'Custom Gaji Harian', 'd')): ?>
                        <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    <?php endif ?>

                    `;

                return htmlRes;
            }
        }],
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

    $(".month").datepicker({
        format: "yyyy-mm",
        startView: "months", // langsung tampilin bulan
        minViewMode: "months", // cuma bisa pilih bulan
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
    });

    $('#month').change(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#search').change(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $(".dataTable_info").addClass("pt-0");

    function edit(id) {
        location.replace(`<?= base_url("custom-gaji-harian/id"); ?>/${id}`);
    }

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                const formData = new FormData();
                formData.set('id', id);

                $.ajax({
                    url: "<?= base_url("custom-gaji-harian/delete"); ?>",
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
                                    table.ajax.reload();
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
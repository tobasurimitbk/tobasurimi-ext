<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Jam Kerja</h1>
        <div class="col-button-tambah-spp">

            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("jam-kerja"); ?>">
                Kembali
            </a>
            <?php if (can('Personalia', 'Jam Kerja', 'c')) : ?>
                <a class="btn btn-show-form btn-save float-right btn-submit" href="<?= base_url("jam-kerja/create/divisi/" . encrypt($divisis[0]['id'])); ?>">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?= csrf_field() ?>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select disabled class="form-select divisi_id" name="divisi_id" id="divisi_id">
                            <option value=""></option>
                            <?php foreach ($divisis as $divisi) : ?>
                                <option selected value="<?= $divisi['id']; ?>"><?= $divisi['divisi']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Filter Departemen</label>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;" class="sort">No</th>
                                <th onclick="changeSort('jenis')" class="sort">Jenis Jam Kerja</th>
                                <th onclick="changeSort('shift')" class="sort">Shift</th>
                                <th onclick="changeSort('jam_terlambat')" class="sort">Jam Masuk</th>
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
    let sort = "jam_kerja.divisi_id";
    let sortType = "desc";
    let trigger = true;

    var row = 0;

    $(document).ready(function() {
        $(".search").keyup(function() {
            table.ajax.reload();
        });

        $('.divisi_id').select2({
            placeholder: "Filter Departemen",
            theme: "bootstrap-5",
            allowClear: false
        }).change(function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        $('.divisi_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.divisi_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.divisi_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');
    });

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
            url: "<?= base_url("jam-kerja/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.divisi_id = $(".divisi_id option:selected").val();
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
            orderable: false,
        }, {
            data: "jenis",
            className: "text-left"
        }, {
            data: "shift",
            className: "text-left"
        }, {
            data: "jam_terlambat",
            className: "text-left"
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                let res = '';

                res += `
                    <?php if (can('Personalia', 'Jam Kerja', 'u')): ?>
                        <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    <?php endif ?>
                    <?php if (can('Personalia', 'Jam Kerja', 'd')): ?>
                        <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    <?php endif ?>
                    `;

                return res;
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

    function edit(id) {
        location.replace(`<?= base_url("jam-kerja/id"); ?>/${id}`);
    }

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Jam Kerja?',
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
                    url: "<?= base_url("jam-kerja/delete"); ?>",
                    data: {
                        jamKerjaID: id
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
                            stopLoading()
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
                    },
                });
            }
        });
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
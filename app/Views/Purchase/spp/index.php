<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Permintaan Pembelian</h1>
        <?php if (can('Pembelian', 'SPP', 'p')) : ?>
            <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item" onclick="pdf('<?= base_url("spp/print-table"); ?>')">PDF</button></li>
            </ul>
        <?php endif; ?>
        <?php if (can('Pembelian', 'SPP', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("spp/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" disabled placeholder="Tanggal Awal" value="01<?= date('/m/Y') ?>">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" disabled placeholder="Tanggal Akhir">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating spp-ptspp" style="height: 50px;">
                        <select class="form-select form-out-search is_posted" name="is_posted" id="is_posted" aria-label="Floating label select example">
                            <!-- <option value="">PILIH STATUS SPP</option> -->
                            <option value="SUDAH POSTING">STATUS : SUDAH POSTING</option>
                            <option value="BELUM POSTING" selected>STATUS : BELUM POSTING</option>
                        </select>
                        <label for="floatingInput" class="l-spp-ptspp">Tipe SPP</label>
                    </div>
                </div>
                <!-- <div class="col-md-2">
                    <div class="form-floating spp-ptspp" style="height: 50px;">
                        <select class="form-select kategori spp_type form-out-search" name="spp_type" id="spp_type" aria-label="Floating label select example">
                            <option value="">PILIH TIPE SPP</option>
                            <?php foreach ($dataSppType as $d) : ?>
                                <option <?= (!empty($dataSPP) ? ($dataSPP->spp_type == $d['value'] ? 'selected' : '') : '') ?> value="<?= $d['value'] ?>"><?= strtoupper($d['value']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput" class="l-spp-ptspp">Tipe SPP</label>
                    </div>
                </div> -->
                <div class="col-md-2">
                    <div class="form-floating spp-ptspp" style="height: 50px;">
                        <select class="form-select divisi_id form-out-search" name="divisi_id" id="divisi_id" aria-label="Floating label select example">
                            <option value="">PILIH DEPARTEMEN</option>
                            <?php foreach ($dataDivisi as $d) : ?>
                                <option <?= (!empty($dataSPP) ? ($dataSPP->divisi_id == $d['value'] ? 'selected' : '') : '') ?> value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput" class="l-spp-ptspp"></label>
                    </div>
                </div>
                <div class="col mb-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Data" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('sppType')" class="sort">Tipe SPP</th>
                                <th onclick="changeSort('sppNo')" class="sort">No. SPP</th>
                                <th onclick="changeSort('divisi')" class="sort">Departemen</th>
                                <th>Jumlah Order</th>
                                <th onclick="changeSort('requestDate')" class="sort">Tanggal Order</th>
                                <th onclick="changeSort('is_posted')">Status</th>
                                <th>Action</th>
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
    let sort = "createdAt";
    let sortType = "desc";

    let search = $('.search').val();
    // let spp_type = $('.spp_type').val();
    let currentPage = 1;
    const table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [6, 'desc']
        ],

        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("spp/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.spp_type = $(".spp_type").val();;
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.is_posted = $(".is_posted").val();
                data.divisi_id = $('.divisi_id').val();
                data.sort = sort;
                data.sortType = sortType;
            },
        },
        "drawCallback": function(settings) {
            //for set current page print
            currentPage = settings.json.currentPage;
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
                orderable: false
            },
            {
                data: "spp_types",
                className: "text-center"
            },
            {
                data: "spp_no",
                className: "text-center"
            },
            {
                data: "divisiName",
                className: "text-center"
            },
            {
                data: "itemCount",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "request_date",
                className: "text-center",
            },
            {
                data: "status",
                className: "text-center",
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let is_posted = row.is_posted;
                    let spp_type = row.spp_type;
                    let status = row.status;


                    if (is_posted === "0") {
                        return `
                            <div class="mt-0">
                                <?php if (can('Pembelian', 'SPP', 'p')) : ?>
                                    <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("spp/print/"); ?>${id}')" style="box-shadow: none !important;">
                                        <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>
                                <?php if (can('Pembelian', 'SPP', 'a')) : ?>
                                    <button data-toggle="tooltip" title="Posting" onclick="updateStatus('${id}', 1)" class="btn btn-success posting-spp">
                                        <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>

                                <?php if (can('Pembelian', 'SPP', 'd')) : ?>
                                    <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>

                            </div>
                        `
                    }
                    if (is_posted === "1") {
                        var res = '';

                        if (status == "OPEN") {
                            res += `
                                <?php if (can('Pembelian', 'SPP', 'ua')) : ?>
                                    <button data-toggle="tooltip" title="Un-Posting" onclick="updateStatus('${id}', 0)" class="btn btn-danger posting-spp">
                                        <i class="fa-solid fa-ban"></i>    
                                    </button>
                                <?php endif; ?>
                            `;
                        }
                        res += `
                            <?php if (can('Pembelian', 'SPP', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("spp/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        `;

                        if (status == "OPEN" && is_posted == "1") {
                            res += `
                                <?php if (can('Pembelian', 'SPP', 'a')) : ?>
                                    <button data-toggle="tooltip" title="Close SPP" onclick="closeSPP('${id}')" class="btn btn-danger posting-spp">
                                        <i class="fa fa-xmark fa-sm" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>
                            `;
                        }


                        return `
                            <div class="mt-0">
                                ${res}
                            </div>
                        `
                    }
                }
            },
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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
        $(".dateStart").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".dateEnd").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $('.icon-dateStart').click(function() {
            $(".dateStart").focus();
        });

        $('.icon-dateEnd').click(function() {
            $(".dateEnd").focus();
        });

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".spp_type").change(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd, .is_posted, .divisi_id").change(function() {
            table.ajax.reload();
        })

        $(".is_posted").change(function() {
            var is_posted = $(this).val();
            if (is_posted == "SUDAH POSTING") {
                // Ubah Status Disbled StartDate dan EndDate menjadi false
                $(".dateStart, .dateEnd").attr('disabled', false);
            } else {
                // Ubah Status Disbled StartDate dan EndDate menjadi false
                $(".dateStart, .dateEnd").attr('disabled', true);

            }
        });

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("spp/id"); ?>/${data.id}`);
        })
    })

    const updateStatus = function(id, status) {
        Swal.fire({
            icon: 'question',
            title: status == '1' ? 'Yakin akan diposting ?' : 'Batalkan Posting ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("spp/update-status"); ?>",
                    data: {
                        id: id,
                        status: status
                    },
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
        })
    }

    const remove = function(id, tipe) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di hapus?',
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
                    url: "<?= base_url("spp/delete"); ?>",
                    data: {
                        id: id,
                    },
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
        })
    }

    const closeSPP = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan Close SPP?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Close',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("spp/close-spp"); ?>",
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
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    },
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Close SPP, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    }


    const print = function(url) {
        window.open(url, "_blank");
    }

    const pdf = function(url) {
        let search = $(".search").val();
        let spp_type = $(".spp_type").val();
        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();

        window.open(url + `?search=${search}&spp_type=${spp_type}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
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
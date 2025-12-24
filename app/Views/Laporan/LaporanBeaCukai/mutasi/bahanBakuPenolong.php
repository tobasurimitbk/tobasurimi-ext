<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Mutasi Bahan Baku dan Penolong</h1>
        <div class="col-button-tambah-spp">
            <?php if (can('Laporan', 'Bea Cukai', 'p')): ?>
                <a class="btn btn-warning btn-print float-right" href="#" id="btnExport">
                    <i class="fa fa-download"></i> Export
                </a>
            <?php endif; ?>
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-bea-cukai"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row ">
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input value="01/<?= date('m/Y') ?>" placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" />
                            <label style="z-index: 1;" style="z-index: 1;">Tgl Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input value="<?= date('d/m/Y') ?>" placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" />
                            <label style="z-index: 1;" style="z-index: 1;">Tgl Akhir</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select type_barang" name="type_barang" id="type_barang">
                            <option selected value="bahan_baku,bahan_penolong">ALL</option>
                            <option value="bahan_baku">BAHAN BAKU</option>
                            <option value="bahan_penolong">BAHAN PENOLONG</option>
                        </select>
                        <label style="z-index: 1;">Pilih Tipe Barang</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input type="text" name="search" id="search" class="form-control search" placeholder="Cari Data">
                            <label style="z-index: 1;">Cari Data</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-lg" style="height: 45px; background-color:#B8522A; color:whitesmoke;" onclick="handleFilter()">
                        <span style="font-size: 15px;">
                            <i class="fas fa-search"></i> Filter
                        </span>
                    </button>

                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('barang_master.parent_type_id')">No</th>
                                <th onclick="changeSort('barang_master.parent_type_id')">Kategori</th>
                                <th onclick="changeSort('barang_master.kode_barang')">Kode Barang</th>
                                <th onclick="changeSort('barang_master.barang_name')">Barang</th>
                                <th onclick="changeSort('barang_master_spesifikasi.satuan_1')">Satuan</th>
                                <th>Awal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Akhir</th>
                                <th>Action</th>
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
    var csrfToken = '<?= csrf_token() ?>';
    var sort = "barang_master.barang_name";
    var sortType = "asc";

    var dataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url('laporan-bea-cukai/mutasi/all') ?>",
            type: "GET",
            data: function(d) {
                d.dateStart = $('#dateStart').val();
                d.dateEnd = $('#dateEnd').val();
                d.type_barang = $('#type_barang').val();
                d.search = $('#search').val();
                d.sort = sort;
                d.sortType = sortType;
            }
        },
        columns: [{
                data: 'no',
                orderable: false
            },
            {
                data: 'parent_name'
            },
            {
                data: 'kode_barang'
            },
            {
                data: 'barang_name'
            },
            {
                data: 'kode_satuan'
            },
            {
                data: 'awal',
                render: function(data) {
                    if (data != "") {
                        return greatFormatRupiah(parseFloat(data).toFixed(2));
                    }
                    return "";
                }
            },
            {
                data: 'masuk',
                render: function(data) {
                    if (data != "") {
                        return greatFormatRupiah(parseFloat(data).toFixed(2));
                    }
                    return "";
                }
            },
            {
                data: 'keluar',
                render: function(data) {
                    if (data != "") {
                        return greatFormatRupiah(parseFloat(data).toFixed(2));
                    }
                    return "";
                }
            },
            {
                data: 'akhir',
                render: function(data) {
                    if (data != "") {
                        return greatFormatRupiah(parseFloat(data).toFixed(2));
                    }
                    return "";
                }
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    return `
                        <div class="mt-0 actions">
                            <a href="javascript:void(0)" onclick="detail('${id}')" data-toggle="tooltip" title="Detail Stok" class="btn btn-success posting-spp actions">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    `
                }
            }
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
        display: "stripe",
        searching: false,
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
        },
    });

    $(".dateStart,.dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: false
    });

    $("#divisi_id,#warehouse_id,#type_barang")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    function handleFilter() {
        dataTable.ajax.reload();
    }

    function detail(id) {
        const width = 800;
        const height = 600;
        const left = window.innerWidth / 2 - width / 2;
        const top = window.innerHeight / 2 - height / 2;
        const dateStart = $('#dateStart').val();
        const dateEnd = $('#dateEnd').val();
        if (dateStart == '' || dateEnd == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal mulai & tanggal selesai',
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            window.open(
                "<?= base_url('laporan-bea-cukai/mutasi/id/') ?>" + id + "?dateStart=" + dateStart + "&dateEnd=" + dateEnd,
                "_blank",
                `width=${width},height=${height},top=${top},left=${left},resizable=yes`
            );
        }


    }

    $('#btnExport').click(function(e) {
        e.preventDefault();
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        var typeBarang = $('#type_barang').val();

        if (dateStart == '' || dateEnd == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal mulai & tanggal selesai',
                confirmButtonColor: '#4e73df',
            });

            return;
        } else {
            var url = "<?= base_url('laporan-bea-cukai/mutasi/excel') ?>" + '?dateStart=' + dateStart + '&dateEnd=' + dateEnd + '&type_barang=' + typeBarang;
            window.location.href = url;
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
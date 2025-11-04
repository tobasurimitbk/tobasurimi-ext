<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>BC 2.7 Out Outstanding</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" href="#" onclick="exportExcel()">
                <i class="fa-solid fa-print"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url("bea-cukai-bc-27"); ?>">
                Kembali
            </a>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="row justify-content-end row-col-spp">
                    <div class="col-md-3 mb-3">
                        <input autocomplete="one-time-code" class="form-control search search form-out-search" placeholder="Cari Data" value="" />
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('mutasi_global.no_mutasi')">No Mutasi</th>
                                <th onclick="changeSort('mutasi_global.company_asal_id')">Company Asal</th>
                                <th onclick="changeSort('mutasi_global.divisi_asal_id')">Dept Asal</th>
                                <th onclick="changeSort('mutasi_global.warehouse_asal_id')">Warehouse Asal</th>
                                <th onclick="changeSort('mutasi_global.company_tujuan_id')">Company Tujuan</th>
                                <th onclick="changeSort('mutasi_global.tanggal')">Tgl Mutasi</th>
                                <th onclick="changeSort('barang_master.kode_barang')">Kode Barang</th>
                                <th onclick="changeSort('barang_master.barang_name')">Barang</th>
                                <th onclick="changeSort('barang_master_spesifikasi.spesifikasi')">Spesifikasi</th>
                                <th onclick="changeSort('mutasi_global.qty_konversi')">Qty Mutasi</th>
                                <th onclick="changeSort('mutasi_global.unit_id_konversi')">Satuan</th>
                                <th onclick="changeSort('stock_revamp_detail.type_bc')">Doc Masuk</th>
                                <th onclick="changeSort('bc_purchase_order.no_aju')">No Aju</th>
                                <th onclick="changeSort('bc_purchase_order.no_daftar')">No Daftar</th>
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
    let sort = "mutasi_global.tanggal";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("bea-cukai-bc-27/bc-27-outstanding-all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $('.search').val();
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
                orderable: false
            },
            {
                data: "no_mutasi",
            },
            {
                data: "company_asal",
            },
            {
                data: "divisi_asal",
            },
            {
                data: "warehouse_asal",
            },
            {
                data: "company_tujuan",
            },
            {
                data: "tanggal",
            },
            {
                data: "kode_barang",
            },
            {
                data: "barang_name",
            },
            {
                data: "spesifikasi",
            }, {
                data: "qty_konversi",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            }, {
                data: "kode_satuan",
            }, {
                data: "type_bc",
            }, {
                data: "no_aju",
            },
            {
                data: "no_daftar",
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

    $('.search').keyup(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    function exportExcel() {
        window.location.href = "<?= base_url('bea-cukai-bc-27/bc-27-outstanding-export') ?>";
    }
</script>


<?= $this->endSection(); ?>
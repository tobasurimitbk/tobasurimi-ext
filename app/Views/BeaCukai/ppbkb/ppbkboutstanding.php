<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>PPBKB Outstanding</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" href="#" onclick="exportExcel()">
                <i class="fa-solid fa-print"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url("bea-cukai-ppbkb"); ?>">
                Kembali
            </a>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control search search form-out-search" placeholder="Cari Data" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('mutasi.no_mutasi')">No Mutasi</th>
                                <th onclick="changeSort('mutasi.divisi_asal_id')">Dept Asal</th>
                                <th onclick="changeSort('mutasi.warehouse_asal_id')">Warehouse Asal</th>
                                <th onclick="changeSort('mutasi.divisi_tujuan_id')">Dept Tujuan</th>
                                <th onclick="changeSort('mutasi.warehouse_tujuan_id')">Warehouse Tujuan</th>
                                <th onclick="changeSort('mutasi.tanggal')">Tgl Mutasi</th>
                                <th onclick="changeSort('barang_master.kode_barang')">Kode Barang</th>
                                <th onclick="changeSort('barang_master.barang_name')">Barang</th>
                                <th onclick="changeSort('barang_master_spesifikasi.spesifikasi')">Spesifikasi</th>
                                <th onclick="changeSort('mutasi_detail.qty_konversi')">Qty Mutasi</th>
                                <th onclick="changeSort('mutasi_detail.unit_id_konversi')">Satuan</th>
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
    let sort = "mutasi.tanggal";
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
            url: "<?= base_url("bea-cukai-ppbkb/ppbkb-outstanding-all"); ?>",
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
                data: "divisi_asal",
            },
            {
                data: "warehouse_asal",
            },
            {
                data: "divisi_tujuan",
            },
            {
                data: "warehouse_tujuan",
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
        window.location.href = "<?= base_url('bea-cukai-ppbkb/ppbkb-outstanding-export') ?>";
    }
</script>


<?= $this->endSection(); ?>
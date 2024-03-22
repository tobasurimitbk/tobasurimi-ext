<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Material Request Gudang</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("material-warehouse"); ?>">
                Batal
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <!-- <div class="row justify-content-end row-col-spp">
                <div class="col-md-4 mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Kode Produksi / Kode Barang / Nama Barang" value="" />
                </div>
            </div> -->
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTableMaterialRequest" id="dataTableMaterialRequest" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Referensi</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
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
    let sortDataBarang = "createdAt";
    let sortTypeDataBarang = "DESC";
    $(document).ready(function() {
        const dataTableMaterialRequest = $('.dataTableMaterialRequest').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
                url: "<?= base_url("material-warehouse/data-detail-material"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.id = "<?= encrypt($ids) ?>";
                    data.search = "";

                    data.sort = sortDataBarang;
                    data.sortType = sortTypeDataBarang;
                },
                beforeSend: function() {
                    $.LoadingOverlay("show", {
                        image: "",
                        fontawesomeColor: "#222FCC",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
                complete: function() {
                    $.LoadingOverlay("hide", {
                        image: "",
                        fontawesomeColor: "#222FCC",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
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
                    searchable: false,
                    sortable: false
                },
                {
                    data: "ref_no",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "nama_barang",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "satuan",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "total",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "note",
                    className: "text-center",
                    searchable: false,
                    sortable: false
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
    });
</script>

<?= $this->endSection(); ?>
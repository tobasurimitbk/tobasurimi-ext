<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Stok Fisik Bea Cukai</h1>

        <div class="col-button-tambah-spp">
            <?php if (can('Bea Cukai', 'Stok Fisik', 'p')) : ?>
                <button class="btn btn-discard btn-dropdown-export float-right" style="background-color: #eda848ff !important; color:white !important;" type="button" onclick="exportExcel()">
                    <i class="fa fa-download"></i> Export
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row mb-4 justify-content-end">
                <div class="col-sm-3 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data </label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 10px;">No</th>
                            <th onclick="changeSort('parent_type_id')">Kategori</th>
                            <th onclick="changeSort('kode_barang')">Kode Barang</th>
                            <th onclick="changeSort('barang_name')">Barang</th>
                            <th>Qty</th>
                            <th>Kode Satuan</th>
                            <th style="width: 70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>

<script>
    let sort = "'barang_master.barang_name";
    let sortType = "asc";

    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

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
            url: "<?= base_url("stock-fisik/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
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
                className: "text-left",
                orderable: false
            },
            {
                data: "parent_name",
                className: "text-left"
            },
            {
                data: "kode_barang",
                className: "text-left"
            },
            {
                data: "barang_name",
                className: "text-left"
            },
            {
                data: "total_qty",
                className: "text-left",
                searchable: false,
                sortable: false,
                render: function(data) {
                    let datax = parseFloat(data).toFixed(2);
                    return greatFormatRupiah(datax);
                }
            },
            {
                data: "kode_satuan",
                className: "text-left",
                searchable: false,
                sortable: false,
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

    $('.search').change(function(e) {
        table.ajax.reload();
    });

    function detail(id) {
        const width = 800;
        const height = 600;
        const left = window.innerWidth / 2 - width / 2;
        const top = window.innerHeight / 2 - height / 2;

        window.open(
            "<?= base_url('stock-fisik/id/') ?>" + id,
            "_blank",
            `width=${width},height=${height},top=${top},left=${left},resizable=yes`
        );

    }

    function changeSort(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function exportExcel() {
        window.location.href = "<?= base_url('stock-fisik/export-excel') ?>"
    }
</script>

<?= $this->endSection(); ?>
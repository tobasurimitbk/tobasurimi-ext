<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>Stok Histori</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs">
                <?php foreach ($typeAll as $t) : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $t['description'] == $typeSelected ? 'active' : '' ?>" href="<?= base_url('stock-histori?type=' . $t['description']) ?>"><?= $t['value'] ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="row justify-content-end mb-3 mt-3">
                <div class="col-md-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <?php if ($typeSelected == "bahan_baku") : ?>
                                <tr>
                                    <th>No.</th>
                                    <th onclick="changeSort('stock_details.stock_date')">Tanggal</th>
                                    <th onclick="changeSort('companies.company')">Company</th>
                                    <th onclick="changeSort('stock_details.warehouse_id')">Warehouse</th>
                                    <th>Kondisi</th>
                                    <th onclick="changeSort('stock_details.barang_id')">Barang</th>
                                    <th onclick="changeSort('stock_details.spesifikasi')">Spesifikasi</th>
                                    <th onclick="changeSort('stock_details.status')">Status</th>
                                    <th onclick="changeSort('stock_details.qty')">Stok</th>
                                </tr>
                            <?php elseif ($typeSelected == "bahan_penolong" || $typeSelected == "bahan_jadi" || $typeSelected == "bahan_scrap") : ?>
                                <tr>
                                    <th>No.</th>
                                    <th onclick="changeSort('stock_details.stock_date')">Tanggal</th>
                                    <th onclick="changeSort('companies.company')">Company</th>
                                    <th onclick="changeSort('stock_details.warehouse_id')">Warehouse</th>
                                    <th>Kondisi</th>
                                    <th onclick="changeSort('stock_details.barang_id')">Barang</th>
                                    <th>Satuan</th>
                                    <th onclick="changeSort('stock_details.status')">Status</th>
                                    <th onclick="changeSort('stock_details.qty')">Stok</th>
                                </tr>
                            <?php endif; ?>
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
    let sort = "stock_details.id";
    let sortType = "desc";
    let table = null;
</script>
<?php if ($typeSelected == "bahan_baku") : ?>
    <script>
        table = $('.dataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
                url: "<?= base_url("stock-histori/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.sort = sort;
                    data.sortType = sortType;
                    data.typeBarang = "<?= $typeSelected ?>"
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
                    sortable: false,
                    width: "5%"
                }, {
                    data: "tanggal",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "company",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "warehouse",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "kondisi",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "barang",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "spesifikasi",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "status",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "stok",
                    className: "text-center",
                    width: "10%",
                    render: function(data, type, row) {
                        var status = row?.status;
                        if (status == "IN") {
                            return `
                            <div class="text-success">
                            <b>(+) ${data}</b>
                            </div>
                        `
                        } else {
                            return `
                            <div class="text-danger">
                            <b>(-) ${data}</b>
                            </div>
                        `
                        }

                    }
                }
            ],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Tidak ada stok histori bahan baku",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
    </script>
<?php elseif ($typeSelected == "bahan_penolong" || $typeSelected == "bahan_jadi" || $typeSelected == "bahan_scrap") : ?>
    <script>
        table = $('.dataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
                url: "<?= base_url("stock-histori/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.sort = sort;
                    data.sortType = sortType;
                    data.typeBarang = "<?= $typeSelected ?>"
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
                    sortable: false,
                    width: "5%"
                }, {
                    data: "tanggal",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "company",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "warehouse",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "kondisi",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "barang",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "satuan",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "status",
                    className: "text-center",
                    width: "10%"
                },
                {
                    data: "stok",
                    className: "text-center",
                    width: "10%",
                    render: function(data, type, row) {
                        var status = row?.status;
                        if (status == "IN") {
                            return `
                            <div class="text-success">
                            <b>(+) ${data}</b>
                            </div>
                        `
                        } else {
                            return `
                            <div class="text-danger">
                            <b>(-) ${data}</b>
                            </div>
                        `
                        }

                    }
                }
            ],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Tidak ada stok histori " + "<?= str_replace('_', ' ', $typeSelected) ?>",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
    </script>
<?php endif; ?>
<script>
    $(".search").keyup(function() {
        table.ajax.reload();
    });
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "ASC";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>
<?= $this->endSection(); ?>
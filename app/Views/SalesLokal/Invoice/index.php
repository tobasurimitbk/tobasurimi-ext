<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Invoice Lokal</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("invoice-penjualan-lokal/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Nama Pelanggan</th>
                                <th>Kode Pelanggan</th>
                                <th>No Faktur</th>
                                <th>Total Invoice</th>
                                <th>Keterangan</th>
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
    let sort = "";
    let sortType = "asc";
    let trigger = true;

    let list_address = [];
    let list_delete = [];
    var row = 0;

    const table = $('.dataTable').DataTable({
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
            url: "<?= base_url("invoice-penjualan-lokal/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
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
            sortable: false
        }, {
            data: "nama_pelanggan",
            className: "text-center"
        }, {
            data: "kode_pelanggan",
            className: "text-center"
        }, {
            data: "no_faktur",
            className: "text-center"
        }, {
            data: "total_invoice",
            className: "text-center"
        }, {
            data: "keterangan",
            className: "text-center"
            // }, {
            //     data: "id",
            //     className: "text-center actions",
            //     searchable: false,
            //     sortable: false,
            //     render: function(data, type, row) {
            //         let id = row?.id;
            //         return row.is_posted ? "-" : `<button type="button" onclick="handleDelete('${id}')" class="btn btn-discard delete-btn">Hapus</button>
            //         `
            //     }
        }],
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
</script>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Sales Kontrak</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("sales-kontrak/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp mb-3">
                <div class="col-md-3">
                    <select class="form-select status" name="status" id="status" aria-label="Floating label select example">
                        <option value="NEW">NEW</option>
                        <option value="POSTED">POSTED</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input class="form-control search form-out-search" placeholder="Cari No. SC / No. PO / Buyer" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('sales_contract_no')" class="sort">No. SC</th>
                                <th onclick="changeSort('customer_po_no')" class="sort">No. PO</th>
                                <th onclick="changeSort('customer_name')" class="sort">Buyer</th>
                                <th onclick="changeSort('dicharge_port')" class="sort">Tujuan Pengiriman</th>
                                <th onclick="changeSort('shipment_date')" class="sort">Shipment Date</th>
                                <th onclick="changeSort('crreatedAt')" class="sort">Tanggal Pembuatan</th>
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
let sort = "sales_contract_no";
let sortType = "asc";

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
        url: "<?= base_url("sales-kontrak/all"); ?>",
        dataSrc: "data",
        data: function(data) {
            data.search = $(".search").val();
            data.status = $(".status").val();
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
        orderable: false
    },{
        data: "sales_contract_no",
        className: "text-center"
    },{
        data: "customer_po_no",
        className: "text-center"
    },{
        data: "customer_name",
        className: "text-center"
    },{
        data: "dicharge_port",
        className: "text-center"
    },{
        data: "shipment_date",
        className: "text-center"
    },{
        data: "createdAt",
        className: "text-center"
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

$(document).ready(function() {
    $(".dataTable_info").addClass("pt-0");

    $(".status").change(function() {
        table.ajax.reload();
    })

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("sales-kontrak/id/"); ?>${data.id}`);
    })
})
</script>

<?= $this->endSection(); ?>
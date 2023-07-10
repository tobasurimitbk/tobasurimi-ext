<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Tanda Terima Faktur Lokal</h1>
    <a class="btn btn-show-form btn-add float-right" href="<?= base_url("terima-faktur-lokal/create"); ?>">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </a>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end row-col-spp">
            <div class="col mb-3">
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <input class="form-control search form-out-search" placeholder="Search" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th onclick="changeSort('faktur_no')" class="sort">No. Terima Faktur</th>
                            <th onclick="changeSort('sender')" class="sort">Supplier</th>
                            <th onclick="changeSort('nominal_faktur')" class="sort">Nominal Faktur</th>
                            <th onclick="changeSort('due_date')" class="sort">Jatuh Tempo</th>
                            <th onclick="changeSort('date_of')" class="sort">Tanggal Penerimaan</th>
                            <th onclick="changeSort('recipient')" class="sort">Penerima</th>
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
let sort = "faktur_no";
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
        url: "<?= base_url("terima-faktur-lokal/all"); ?>",
        dataSrc: "data",
        data: function(data) {
            data.search = $(".search").val();
            data.dateStart = $(".dateStart").val();
            data.dateEnd = $(".dateEnd").val();
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
    },
    {
        data: "faktur_no",
        className: "text-center"
    },
    {
        data: "sender",
        className: "text-center"
    },
    {
        data: "nominal_faktur",
        className: "text-center"
    },
    {
        data: "due_date",
        className: "text-center"
    },
    {
        data: "date_of_receipt",
        className: "text-center"
    },
    {
        data: "recipient",
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

    $(".dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("terima-faktur-lokal/id"); ?>/${data.id}`);
    })
})
</script>

<?= $this->endSection(); ?>
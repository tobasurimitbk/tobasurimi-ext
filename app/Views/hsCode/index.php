<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Kode HS</h1>
</div>
<div class="card">
    <div class="card-body">
    <div class="row justify-content-end mb-3">
        <div class="col-md-2">
            <input class="form-control search form-out-search" placeholder="Search" value="" />
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th onclick="changeSort('komoditi')" class="sort">Komoditi</th>
                        <th onclick="changeSort('code')" class="sort">HS Code</th>
                        <th onclick="changeSort('uraian_barang')" class="sort">Uraian Barang</th>
                        <th onclick="changeSort('satuan_barang')" class="sort">Satuan</th>
                        <th onclick="changeSort('uraian_satuan')" class="sort">Uraian Satuan</th>
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
    let sort = "komoditi";
    let sortType = "asc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[0, 'asc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("hs-code/all"); ?>",
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
            data: "komoditi",
            className: "text-center"
        }, {
            data: "code",
            className: "text-center"
        }, {
            data: "uraian_barang",
            className: "text-center"
        }, {
            data: "satuan_barang",
            className: "text-center"
        }, {
            data: "uraian_satuan",
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
        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dataTable_info").addClass("pt-0");
    })

    const changeSort = function(val) {
        if(sort !== val)
        {
            sortType = "asc";
            sort = val;
        }
        else
        {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>


<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Rencana Produksi</h1>

        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("material-request/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-3 mb-3">
                    <input class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Kode Produksi</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Hasil</th>
                                <th>Permintaan Material</th>
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
    let sort = "";
    let sortType = "desc";

    let search = $('.search').val();
    // let spp_type = $('.spp_type').val();
    let currentPage = 1;

    // const table = $('.dataTable').DataTable({
    //     dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
    //     processing: true,
    //     serverSide: true,
    //     ordering: true,
    //     order: [
    //         [6, 'desc']
    //     ],

    //     fixedHeader: true,
    //     lengthMenu: [
    //         [25],
    //         [25],
    //     ],
    //     pageLength: 25,
    //     ajax: {
    //         url: "<?= base_url("spp/all"); ?>",
    //         dataSrc: "data",
    //         data: function(data) {
    //             data.search = $(".search").val();
    //             data.sort = sort;
    //             data.sortType = sortType;
    //         },
    //     },
    //     "drawCallback": function(settings) {
    //         //for set current page print
    //         currentPage = settings.json.currentPage;
    //     },
    //     // scrollX: true,
    //     "initComplete": function(settings, json) {
    //         $('.dataTables_length').empty();
    //         $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
    //         $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    //     },
    //     //responsive: true,
    //     display: "stripe",
    //     searching: false,
    //     columns: [{
    //             data: "no",
    //             className: "text-center",
    //             orderable: false
    //         },
    //         {
    //             data: "no",
    //             className: "text-center"
    //         }
    //     ],
    //     columnDefs: [{
    //         defaultContent: "-",
    //         targets: "_all"
    //     }],
    //     language: {
    //         emptyTable: "Tidak Ada Data",
    //         lengthMenu: "Show _MENU_ entries",
    //         paginate: {
    //             previous: '<i class="fa fa-angle-left"></i>',
    //             next: '<i class="fa fa-angle-right"></i>'
    //         }
    //     }
    // });

    $(document).ready(function() {

        // $(".dataTable_info").addClass("pt-0");

        // $(".search").keyup(function() {
        //     table.ajax.reload();
        // })

        // $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        //     const data = table.row(this).data();
        //     location.replace(`<?= base_url("material-request/id"); ?>/${data.id}`);
        // })
    })

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
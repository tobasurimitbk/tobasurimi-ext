<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Pinjaman Karyawan</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("pinjaman-karyawan/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
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
                                <th onclick="changeSort('id')" class="sort">ID</th>

                                <th onclick="changeSort('employeeName')" class="sort">Employee Name</th>

                                <th onclick="changeSort('amount')" class="sort">amount</th>

                                <th onclick="changeSort('installment_month')" class="sort">installment_month</th>

                                <th onclick="changeSort('remaining_amount')" class="sort">remaining_amount</th>

                                <th onclick="changeSort('loan_date')" class="sort">loan_date</th>

                                <th onclick="changeSort('term')" class="sort">term</th>

                                <th onclick="changeSort('status')" class="sort">status</th>

                                <th onclick="changeSort('approve_by')" class="sort">approve_by</th>

                                <th onclick="changeSort('nip')" class="sort">nip</th>
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
    let sort = "loan_date";
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
            [0, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("pinjamanKaryawan/all"); ?>",
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
            data: "id",
            className: "text-center"
        }, {
            data: "employeeName",
            className: "text-center"
        }, {
            data: "amount",
            className: "text-center"
        }, {
            data: "installment_month",
            className: "text-center"
        }, {
            data: "remaining_amount",
            className: "text-center"
        }, {
            data: "loan_date",
            className: "text-center"
        }, {
            data: "term",
            className: "text-center"
        }, {
            data: "status",
            className: "text-center"
        }, {
            data: "approve_by",
            className: "text-center"
        }, {
            data: "nip",
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
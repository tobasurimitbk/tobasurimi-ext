<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Cuti</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("cuti/create"); ?>">
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

                                <th onclick="changeSort('amount')" class="sort">Amount</th>

                                <th onclick="changeSort('installment_month')" class="sort">Installment Month</th>

                                <th onclick="changeSort('remaining_amount')" class="sort">Remaining Amount</th>

                                <th onclick="changeSort('loan_date')" class="sort">Loan Date</th>

                                <th onclick="changeSort('term')" class="sort">Term</th>

                                <th onclick="changeSort('status')" class="sort">Status</th>

                                <th onclick="changeSort('approve_by')" class="sort">Approve By</th>

                                <th onclick="changeSort('nip')" class="sort">NIP</th>

                                <th onclick="changeSort('is_posting')" class="sort">Posting</th>
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

    $(document).ready(function() {
        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("pinjaman-karyawan/id"); ?>/${data.id}`);
        })
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
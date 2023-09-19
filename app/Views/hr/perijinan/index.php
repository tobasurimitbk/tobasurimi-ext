<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Form Perijinan</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("form-perijinan/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <?= csrf_field() ?>
                <div class="col-sm-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Berdasarkan NIP" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('employeeNip')" class="sort">NIP</th>
                                <th onclick="changeSort('employeeName')" class="sort">Nama Karyawan</th>
                                <th onclick="changeSort('divisionName')" class="sort">Divisi</th>
                                <th onclick="changeSort('mulai')" class="sort">Mulai</th>
                                <th onclick="changeSort('selesai')" class="sort">Selesai</th>
                                <th onclick="changeSort('keterangan')" class="sort">Keterangan</th>
                                <th onclick="changeSort('approval')" class="sort">Status Approval</th>
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
    let sort = "periode";
    let sortType = "asc";
    let trigger = true;
    let year = new Date().getFullYear();

    var row = 0;

    $(document).ready(function() {

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd").change(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("form-perijinan/id"); ?>/${data.kode}`);
        })
    });

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
            url: "<?= base_url("form-perijinan/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.nip = $(".search").val();
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
            sortable: false,
            width: "5%"
        }, {
            data: "employeeNip",
            className: "text-center"
        }, {
            data: "employeeName",
            className: "text-center"
        }, {
            data: "divisionName",
            className: "text-center"
        }, {
            data: "mulai",
            className: "text-center"
        }, {
            data: "selesai",
            className: "text-center"
        }, {
            data: "keterangan",
            className: "text-center"
        }, {
            data: "approval",
            className: "text-center"
        }, ],
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
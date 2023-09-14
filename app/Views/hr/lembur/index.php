<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Form Lembur</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("lembur/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <?= csrf_field() ?>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search fos-jk mb-3" placeholder="Cari Data Berdasarkan NIP" id="filterSearch" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;" class="sort">No</th>
                                <th onclick="" class="sort">NIP</th>
                                <th onclick="" class="sort">Nama Karyawan</th>
                                <th onclick="" class="sort">Divisi</th>
                                <th onclick="" class="sort">Tgl Lembur</th>
                                <th onclick="" class="sort">Jam Lembur</th>
                                <th onclick="" class="sort">Uang Lembur</th>
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
    let sort = "nomor";
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
            url: "<?= base_url("lembur/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.nip = $('#filterSearch').val();
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
                data: "nip",
                className: "text-center",
                width: "10%"
            },
            {
                data: "name",
                className: "text-center"
            },
            {
                data: "divisi",
                className: "text-center"
            },
            {
                data: "periode",
                className: "text-center"
            },
            {
                data: "jam_lembur",
                className: "text-center"
            },
            {
                data: "uang_lembur",
                className: "text-center"
            }
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Data Form Lembur tidak ada", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("lembur/id"); ?>/${data.id}`);
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    $("#filterSearch").keyup(function() {
        table.ajax.reload();
    });
</script>

<?= $this->endSection(); ?>
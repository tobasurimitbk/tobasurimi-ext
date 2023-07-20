<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Penerimaan Barang Import</h1>
    <a class="btn btn-show-form btn-add float-right" href="<?= base_url("penerimaan-barang-import/create"); ?>">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </a>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end mb-3 row-col-spp">
            <div class="col">
            <?= csrf_field() ?>
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <select class="form-select status" name="status" id="status" aria-label="Floating label select example">
                    <option value="waiting">Waiting</option>
                    <option value="finish">Finish</option>
                </select>
            </div>
            <div class="col">
                <input class="form-control search form-out-search" placeholder="Ketik No Penerimaan" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th onclick="changeSort('no_penerimaan_barang')" class="sort">No. Penerimaan</th>
                            <th onclick="changeSort('no_po')" class="sort">No. PO</th>
                            <th onclick="changeSort('acceptance_type')" class="sort">Single/Multiple</th>
                            <th onclick="changeSort('aju_type')" class="sort">Jenis Dokumen</th>
                            <th onclick="changeSort('aju_no')" class="sort">No. AJU</th>
                            <th onclick="changeSort('validation_date')" class="sort">Tanggal Daftar</th>
                            <th onclick="changeSort('sender_name')" class="sort">Pengirim</th>
                            <th>Status</th>
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
    let sort = "no_penerimaan_barang";
    let sortType = "asc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[1, 'asc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("penerimaan-barang-import/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.status = $(".status").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function (settings, json) {    
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
            data: "no_penerimaan_barang",
            className: "text-center"
        },
        {
            data: "multiple_po_no",
            className: "text-center"
        },
        {
            data: "acceptance_type",
            className: "text-center"
        },
        {
            data: "aju_type",
            className: "text-center"
        },
        {
            data: "aju_no",
            className: "text-center"
        },
        {
            data: "validation_date",
            className: "text-center"
        },
        {
            data: "sender_name",
            className: "text-center"
        },
        {
            data: "status_post",
            className: "text-center",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                if(data === "WAITING")
                {
                    return `
                    <label class="label-waiting">
                    ${data}
                    </label>
                    `
                }
                if(data === "FINISH")
                {
                    return `
                    <label class="label-finish">
                    ${data}
                    </label>
                    `
                }
            }
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

        $(".search").keyup(function () {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd, .status").change(function () {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("penerimaan-barang-import/id"); ?>/${data.id}`);
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
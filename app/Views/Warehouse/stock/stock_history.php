<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Stock Histori</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($dataDivisi as $divisi) : ?>
                                <option value="<?= $divisi["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['divisi_id'] === $divisi["id"] ? "selected" : "") : ""; ?>><?= strtoupper($divisi["divisi"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Departemen</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">

                        </select>
                        <label style="z-index: 1;">Warehouse</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data </label>
                    </div>
                </div>
                <div class="col-md-4 mt-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Tipe Barang</th>
                            <th onclick="changeSort('stock_details2.supplier_id')">Supplier</th>
                            <th>Kode Barang</th>
                            <th onclick="changeSort('barang_master.barang_name')">Barang</th>
                            <th onclick="changeSort('divisis.divisi')">Departemen</th>
                            <th onclick="changeSort('warehouses.warehouse_name')">Warehouse</th>
                            <th onclick="changeSort('stock_details2.bc_id')">Dokumen Pabean</th>
                            <th onclick="changeSort('stock_details2.bc_id')">Dokumen Referensi</th>
                            <th onclick="changeSort('stock_details.sumber')">Sumber</th>
                            <th onclick="changeSort('stock_details.no_dokumen')">Referensi</th>
                            <th onclick="changeSort('stock_details.keterangan')">Keterangan</th>
                            <th onclick="changeSort('stock_details2.tanggal')">Tanggal</th>
                            <th onclick="changeSort('stock_details2.qty')">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>



<script>
    let sort = "createdAt";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-histori/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.divisi_id = $("#divisi_id option:selected").val();
                data.warehouse_id = $("#warehouse_id option:selected").val();
                data.dateStart = $('#dateStart').val();
                data.dateEnd = $('#dateEnd').val();
                data.sort = sort;
                data.sortType = sortType;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                orderable: false
            },
            {
                data: "parent_type",
                className: "text-center",
                orderable: false
            },
            {
                data: "supplier_name",
                className: "text-center",
            },
            {
                data: "kode_barang",
                className: "text-center",
                orderable: false
            },
            {
                data: "barang",
                className: "text-center",
                orderable: false
            },
            {
                data: "divisi",
                className: "text-center",
                orderable: false

            },
            {
                data: "warehouse",
                className: "text-center",
                orderable: false
            },
            {
                data: "dokumen_pabean",
                className: "text-center"
            },
            {
                data: "dokumen_referensi",
                className: "text-center",
                orderable: false
            },
            {
                data: "sumber",
                className: "text-center",
            },

            {
                data: "dokumen",
                className: "text-center",
            },
            {
                data: "keterangan",
                className: "text-center",
            },
            {
                data: "tanggal",
                className: "text-center",
            },
            {
                data: "stok",
                className: "text-center",
            },
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('stock-list/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_id").val();
            }
        });
        table.ajax.reload();
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $('#status_stok').select2({
        placeholder: "Pilih Status Stok",
        theme: "bootstrap-5",
    }).change(function() {
        table.ajax.reload();
    });

    $('.search').change(function() {
        table.ajax.reload();
    });

    $('#dateStart,#dateEnd').change(function() {
        table.ajax.reload();
    });

    $("#parent_type,#divisi_id,#warehouse_id,#parent_name,#status_stok")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const stokDetail = function(id) {
        location.replace("<?= base_url('stock-list/id/') ?>" + id, "");
    }

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
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan WIP</h1>
        <div class="col-button-tambah-spp">
            <?php if (can('Laporan', 'Bea Cukai', 'p')): ?>
                <a class="btn btn-warning btn-print float-right" href="#" id="btnExport">
                    <i class="fa fa-download"></i> Export
                </a>
            <?php endif; ?>
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-bea-cukai"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="row ">
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input value="01/<?= date('m/Y') ?>" placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" />
                            <label style="z-index: 1;" style="z-index: 1;">Tgl Awal Produksi</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input value="<?= date('d/m/Y') ?>" placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" />
                            <label style="z-index: 1;" style="z-index: 1;">Tgl Akhir Produksi</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select divisi_id" name="divisi_id" id="divisi_id" onchange="getListWarehouse()">
                            <option disabled selected value=""></option>
                            <?php foreach ($dataDivisi as $d) : ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['divisi']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Pilih Departemen</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select warehouse_id" name="warehouse_id" id="warehouse_id">
                            <option disabled selected value=""></option>
                        </select>
                        <label style="z-index: 1;">Pilih Warehouse</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input type="text" name="search" id="search" class="form-control search" placeholder="Cari Data">
                            <label style="z-index: 1;">Cari Data</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-lg" style="height: 45px; background-color:#B8522A; color:whitesmoke;" onclick="handleFilter()">
                        <span style="font-size: 15px;">
                            <i class="fas fa-search"></i> Filter
                        </span>
                    </button>

                </div>
            </div>


            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Tgl Prod</th>
                                <th>Wo No</th>
                                <th>Barang Jadi</th>
                                <th>Dept</th>
                                <th>Warehouse</th>
                                <th>Mr No</th>
                                <th>Tgl Doc</th>
                                <th>No Aju</th>
                                <th>No Daftar</th>
                                <th>Kode Barang</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="13" class="text-right">TOTAL</th>
                                <th id="total_qty_wip"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    var csrfToken = '<?= csrf_token() ?>';
    var dataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url('laporan-bea-cukai/all-wip') ?>",
            type: "GET",
            data: function(d) {
                d.dateStart = $('#dateStart').val();
                d.dateEnd = $('#dateEnd').val();
                d.divisi_id = $('#divisi_id').val();
                d.warehouse_id = $('#warehouse_id').val();
                d.search = $('#search').val();
            }
        },
        order: [
            [2, 'desc']
        ],
        columns: [{
                data: 'no',
                orderable: false
            },
            {
                data: 'production_date'
            },
            {
                data: 'wo_no'
            },
            {
                data: 'barang_jadi'
            },
            {
                data: 'divisi'
            },
            {
                data: 'warehouse_name'
            },
            {
                data: 'req_no'
            },
            {
                data: 'no_aju'
            },
            {
                data: 'no_daftar'
            },
            {
                data: 'tanggal_daftar'
            },
            {
                data: 'kode_barang'
            },
            {
                data: 'barang_name'
            },
            {
                data: 'spesifikasi'
            },
            {
                data: 'qty',
                className: 'text-end total-col',
                render: function(data) {
                    if (data != "") {
                        return greatFormatRupiah(parseFloat(data).toFixed(2));
                    }
                    return "";
                }
            },
            {
                data: 'kode_satuan'
            },
        ],
        display: "stripe",
        searching: false,
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
        },
        footerCallback: function(row, data, start, end, display) {
            const api = this.api();
            const total = api.ajax.json().footerTotals || 0;

            if (total) {
                $('#total_qty_wip').html(greatFormatRupiah(total.toFixed(2)));
            } else {
                $('#total_qty_wip').html(greatFormatRupiah(0));

            }
        },
    });

    $(".dateStart,.dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });


    $("#divisi_id,#warehouse_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    function handleFilter() {
        dataTable.ajax.reload();
    }

    function getListWarehouse() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('proses-rebus/warehouse'); ?>`,
            method: "GET",
            data: {
                divisi_id: $("#divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $("#warehouse_id").empty()
                $("#warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $("#warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $("#warehouse_id").val();
            }
        });
    }

    $('#btnExport').click(function(e) {
        e.preventDefault();
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        var divisiId = $('#divisi_id option:selected').val();
        var warehouseId = $('#warehouse_id option:selected').val();

        if (dateStart == '' || dateEnd == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal mulai & tanggal selesai',
                confirmButtonColor: '#4e73df',
            });

            return;
        } else {
            var url = "<?= base_url('laporan-bea-cukai/all-wip/excel') ?>" + '?dateStart=' + dateStart + '&dateEnd=' + dateEnd + '&divisi_id=' + divisiId + '&warehouse_id=' + warehouseId;
            window.location.href = url;
        }
    })
</script>

<?= $this->endSection(); ?>
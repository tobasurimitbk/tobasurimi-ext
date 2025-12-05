<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>BC 4.1 Outstanding</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" onclick="exportExcel()">
                <i class="fa-solid fa-print"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url(" bea-cukai-bc-41"); ?>">
                Kembali
            </a>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" value="01/<?= date("m/Y") ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal Order Form</label>
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
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" value="<?= date('t/m/Y') ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir Order Form</label>
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
                        <select class="form-select jenis_pengeluaran" name="jenis_pengeluaran" id="jenis_pengeluaran">
                            <option value="" data-code=""></option>
                            <option value="ORDER FORM LOKAL">ORDER FORM LOKAL</option>

                        </select>
                        <label style="z-index: 1;">Jenis Pengeluaran</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th>Tujuan Pengeluaran</th>
                                <th>Tanggal</th>
                                <th>Reference No</th>
                                <th>Customer</th>
                                <th>Kode</th>
                                <th>Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Nilai Barang</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('bea-cukai-bc-41/bc-41-outstanding-all') ?>",
            type: 'GET',
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $('.dateStart').val();
                data.dateEnd = $('.dateEnd').val();
                data.search = $('.search').val();
                data.jenis_pengeluaran = $('.jenis_pengeluaran').val();
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        order: [
            [2, 'desc']
        ],
        pageLength: 25, // <- Ini untuk default 25 per halaman
        display: "stripe",
        searching: false,
        columns: [{
                data: 'no',
                width: "5%"
            },
            {
                data: 'tujuan_pengeluaran'
            },
            {
                data: 'tanggal'
            },
            {
                data: 'reference_no'
            },
            {
                data: 'customer_name'
            },
            {
                data: 'kode_barang'
            },
            {
                data: 'barang_name'
            },
            {
                data: 'qty',
                render: function(data) {
                    let qty = parseFloat(data).toFixed(2);
                    return greatFormatRupiah(qty);
                }
            },
            {
                data: 'kode_satuan'
            },
            {
                data: 'amount',
                render: function(data) {
                    let amount = parseFloat(data).toFixed(2);
                    return greatFormatRupiah(amount);
                }
            },
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('.dateStart,.dateEnd,.jenis_pengeluaran').change(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('.search').keyup(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $(".dateStart,.dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
    $('#jenis_pengeluaran').select2({
        placeholder: "Pilih Jenis Pengeluaran",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $("#jenis_pengeluaran")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    function exportExcel() {
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();

        if (dateStart == '' || dateEnd == '') {
            alert('Tanggal mulai & Tanggal Akhir wajib diisi');
        } else {
            window.location.href = '<?= base_url('bea-cukai-bc-41/bc-41-outstanding-export') ?>?dateStart=' + dateStart + '&dateEnd=' + dateEnd;
        }

    }
</script>


<?= $this->endSection(); ?>
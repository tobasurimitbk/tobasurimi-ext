<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>BC 4.0 Outstanding</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" onclick="exportExcel()">
                <i class="fa-solid fa-print"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url(" bea-cukai-bc-40"); ?>">
                Kembali
            </a>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" value="01/<?= date("m/Y") ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal LPB</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" value="<?= date('t/m/Y') ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir LPB</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select tipe_bahan" name="tipe_bahan" id="tipe_bahan">
                            <option value="" data-code=""></option>
                            <option value="BAKU">LOKAL BAKU</option>
                            <option value="PENOLONG">LOKAL PENOLONG</option>

                        </select>
                        <label style="z-index: 1;">Tipe Bahan</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select divisi_id" name="divisi_id" id="divisi_id">
                            <option value="" data-code=""></option>
                            <?php foreach ($divisi as $d) : ?>
                                <option value="<?= $d['id']; ?>"><?= $d["divisi"]; ?></option>
                            <?php endforeach; ?>

                        </select>
                        <label style="z-index: 1;">Pilih Departemen</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th style="text-align:left;">No</th>
                                <th style="text-align:left;">Tgl Posting PO</th>
                                <th style="text-align:left;">Tipe Bahan</th>
                                <th style="text-align:left;">Departemen</th>
                                <th style="text-align:left;">Supplier</th>
                                <th style="text-align:left;">Tgl PO</th>
                                <th style="text-align:left;">Tgl LPB </th>
                                <th style="text-align:left;">No LPB</th>
                                <th style="text-align:left;">No PO</th>
                                <th style="text-align:left;">Kode</th>
                                <th style="text-align:left;">Barang</th>
                                <th style="text-align:left;">Qty PO</th>
                                <th style="text-align:left;">Qty LPB</th>
                                <th style="text-align:left;">Kemasan</th>
                                <th style="text-align:left;">Qty Kemasan</th>
                                <th style="text-align:left;">Total Harga</th>
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
            url: "<?= base_url('bea-cukai-bc-40/bc-40-outstanding-all') ?>",
            type: 'GET',
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $('.dateStart').val();
                data.dateEnd = $('.dateEnd').val();
                data.search = $('.search').val();
                data.tipe_bahan = $('.tipe_bahan').val();
                data.divisi_id = $('.divisi_id').val();
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        order: [
            [1, 'desc']
        ],
        pageLength: 25, // <- Ini untuk default 25 per halaman
        display: "stripe",
        searching: false,
        columns: [{
                data: 'no',
                width: "5%"
            },
            {
                data: 'updated_at'
            },
            {
                data: 'tipe_bahan'
            },
            {
                data: 'divisi'
            },
            {
                data: 'supplier'
            },
            {
                data: 'po_date'
            },
            {
                data: 'lpb_date'
            },
            {
                data: 'no_penerimaan_barang'
            },
            {
                data: 'po_no'
            },
            {
                data: 'kode_barang'
            },
            {
                data: 'barang'
            },
            {
                data: 'qty_po'
            },
            {
                data: 'qty_lpb'
            },
            {
                data: 'kemasan'
            },
            {
                data: 'qty_kemasan'
            },
            {
                data: 'sub_total'
            }
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada penerimaan outstanding BC 4.0",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#tipe_bahan').select2({
        placeholder: "Pilih Tipe Bahan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#divisi_id,#tipe_bahan,#dateStart,#dateEnd').change(function() {
        table.ajax.reload();
    });

    $('#search').keyup(function() {
        table.ajax.reload();
    })

    $("#divisi_id,#tipe_bahan")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $(".dateStart,.dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    function exportExcel() {
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        var tipeBahan = $('#tipe_bahan').val();
        var divisiId = $('#divisi_id').val();

        if (dateStart == '' || dateEnd == '') {
            alert('Tanggal mulai & Tanggal Akhir wajib diisi');
        } else {
            window.open('<?= base_url('bea-cukai-bc-40/bc-40-outstanding-export') ?>?dateStart=' + dateStart + '&dateEnd=' + dateEnd + '&tipe_bahan=' + tipeBahan + '&divisi_id=' + divisiId);
        }

    }
</script>


<?= $this->endSection(); ?>
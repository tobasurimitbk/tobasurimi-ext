<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Jurnal Umum</h1>
        <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <a class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-accounting/jurnalumum/printExcel"); ?>')">Excel</a>
        </ul>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                    <div class="col-md-12">
                        <?= csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal" value="<?= $dateStart; ?>" disabled style="height: 50px;">
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir" value="<?= $dateEnd; ?>" style="height: 50px;">
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select class="form-select type_transaksi" name="type_transaksi" id="type_transaksi">
                                        <option value="" data-code=""></option>
                                        <?php
                                        if (!empty($dataMetadataTipeTransaksi)) {
                                            foreach ($dataMetadataTipeTransaksi as $Tipe) {
                                        ?>
                                                <option value="<?= $Tipe->hexid; ?>"><?= $Tipe->value; ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label for="floatingInput">Tipe Transaksi</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select class="form-select subs_akun" name="subs_akun" id="subs_akun">
                                        <option value="" data-code=""></option>
                                        <?php
                                        if (!empty($dataSubAkuns)) {
                                            foreach ($dataSubAkuns as $subs) {
                                        ?>
                                                <option value="<?= $subs->id; ?>"><?= $subs->nama_sub; ?> - <?= $subs->no_sub; ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label for="floatingInput">Akun COA</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-hover" id="myTable" width="100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Department</th>
                                <th>Desc</th>
                                <th>Reference</th>
                                <th>Supplier</th>
                                <th>Currency</th>
                                <th>Exchange Rate</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="7"><strong>Total Transaksi</strong></td>
                                <td id="jumlahDebet"><strong>Rp 0,00</strong></td>
                                <td id="jumlahKredit"><strong>Rp 0,00</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Set default tanggal: awal bulan - hari ini
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    function formatDate(date) {
        // Format dd/mm/yyyy
        let dd = String(date.getDate()).padStart(2, '0');
        let mm = String(date.getMonth() + 1).padStart(2, '0'); // Januari = 0
        let yyyy = date.getFullYear();
        return dd + '/' + mm + '/' + yyyy;
    }

    $(".dateStart").val(formatDate(firstDay));
    $(".dateEnd").val(formatDate(today));

    $(document).ready(function() {
        $('.type_transaksi, .subs_akun').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true
        });

        $('.type_transaksi, .subs_akun')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.type_transaksi, .subs_akun')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.type_transaksi, .subs_akun')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var table = $('#myTable').DataTable({
            ajax: {
                url: "<?= base_url('laporan-accounting/jurnalumum/getData') ?>",
                type: "GET",
                data: function(d){
                    d.dateStart = $('#dateStart').val();
                    d.dateEnd = $('#dateEnd').val();
                    d.type_transaksi = $('#type_transaksi').val();
                    d.subs_akun = $('#subs_akun').val();
                },
                dataSrc: "data"
            },
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            paging: false,
            searching: false,
            info: false,
            order: [],
            columns: [
                { data: "tanggal_jurnal" },
                { data: "nama_divisi" },
                { data: "desc" },
                { data: "reference" },
                { data:"supplier" },
                { data:"currency" },
                { data:"exchange_rate" },
                { data:"debit",  render: $.fn.dataTable.render.number('.', ',', 2, 'Rp ') },
                { data:"kredit", render: $.fn.dataTable.render.number('.', ',', 2, 'Rp ') }
            ],

            createdRow: function(row, data){
                if(data.is_header){

                    $(row).addClass('header-row').css({
                        "background":"#fff8e6",
                        "font-weight":"700"
                    });

                    $('td:eq(0)', row).html(`
                        <div><b>${data.tanggal_jurnal ?? ''}</b></div>
                    `);

                    $('td:eq(2)', row).html(`
                        <div><b>${data.desc ?? ''}</b></div>
                    `);

                    [1,3,4,5,6,7,8].forEach(i => $('td:eq('+i+')', row).html(''));
                }
            },

            drawCallback: function(){
                let api = this.api();

                let totalDebit  = api.column(7).data().reduce((a,b)=>a+(parseFloat(b)||0),0);
                let totalKredit = api.column(8).data().reduce((a,b)=>a+(parseFloat(b)||0),0);

                $('#jumlahDebet').html('Rp ' + totalDebit.toLocaleString('id-ID',{minimumFractionDigits:2}));
                $('#jumlahKredit').html('Rp ' + totalKredit.toLocaleString('id-ID',{minimumFractionDigits:2}));
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

        $('.icon-dateStart').click(function() {
            $(".dateStart").focus();
        });

        $('.icon-dateEnd').click(function() {
            $(".dateEnd").focus();
        });

        $('#dateStart, #dateEnd, #type_transaksi, #subs_akun').change(function(){
            table.ajax.reload();
        });
    });

    const convertDateFormat = function(dateString) {
        var dateParts = dateString.split("/");
        var formattedDate = dateParts[2] + "-" + dateParts[1] + "-" + dateParts[0];
        return formattedDate;
    }

    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var filter = $(".type_transaksi").val() ? $(".type_transaksi").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter;

        window.open(url2, "_blank");
    }
</script>
<?= $this->endSection(); ?>
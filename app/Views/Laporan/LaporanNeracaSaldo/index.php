<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Neraca Saldo</h1>
        <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <a class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-accounting/neracasaldo/printExcel"); ?>')">Excel</a>
        </ul>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="input-group">
                                    <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal" value="<?= $dateStart; ?>">
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group">
                                    <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir" value="<?= $dateEnd; ?>">
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group">
                                    <button type="submit" name="cariTanggal" class="btn btn-primary" value="cari">Cari</button>
                                </div>
                            </div>
                        </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-hover" id="myTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="2" rowspan="2">Daftar Akun</th>
                                <th colspan="2" style="text-align: center;">Saldo Awal</th>
                                <th colspan="2" style="text-align: center;">Pergerakan</th>
                                <th colspan="2" style="text-align: center;">Saldo Akhir</th>
                            </tr>
                            <tr>
                                <th style="text-align: center;">Debit</th>
                                <th style="text-align: center;">Kredit</th>
                                <th style="text-align: center;">Debit</th>
                                <th style="text-align: center;">Kredit</th>
                                <th style="text-align: center;">Debit</th>
                                <th style="text-align: center;">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold table-secondary">
                                <th colspan="2" class="text-center">TOTAL</th>
                                <th class="text-end" id="total_saldo_awal_debit"></th>
                                <th class="text-end" id="total_saldo_awal_kredit"></th>
                                <th class="text-end" id="total_pergerakan_debit"></th>
                                <th class="text-end" id="total_pergerakan_kredit"></th>
                                <th class="text-end" id="total_saldo_akhir_debit"></th>
                                <th class="text-end" id="total_saldo_akhir_kredit"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Mendapatkan tanggal saat ini
        var currentDate = new Date();

        // Inisialisasi datepicker untuk dateStart dengan nilai default tanggal 1 di bulan berjalan
        $(".dateStart").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true,
            // Atur nilai awal menjadi tanggal 1 di bulan berjalan
            defaultViewDate: {
                year: currentDate.getFullYear(),
                month: currentDate.getMonth(),
                day: 1
            }
        });

        // Inisialisasi datepicker untuk dateEnd
        $(".dateEnd").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        // untuk me-load data tabel
        function loadTable(){
            $('#myTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: false,
                paging: false,
                searching: false,
                ordering: false,
                autoWidth: false,
                ajax: {
                    url: "<?= base_url('laporan-accounting/neracasaldo/getData') ?>",
                    data: {
                        dateStart: $('#dateStart').val(),
                        dateEnd: $('#dateEnd').val()
                    }
                },
                columns: [
                    { 
                        data: 0,
                        className: "text-left",
                        sortable: false 
                    },
                    { 
                        data: 1,
                        className: "text-center",
                        sortable: false  
                    },
                    { 
                        data: 2,
                        className: "text-right",
                        sortable: false 
                    },
                    { 
                        data: 3,
                        className: "text-right",
                        sortable: false
                    },
                    { 
                        data: 4,
                        className: "text-right",
                        sortable: false
                    },
                    { 
                        data: 5,
                        className: "text-right",
                        sortable: false
                    },
                    { 
                        data: 6,
                        className: "text-right",
                        sortable: false
                    },
                    { 
                        data: 7,
                        className: "text-right",
                        sortable: false 
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    function toNumber(str){
                        if(!str) return 0;
                        return parseFloat(
                            str.replace(/\./g, '')
                            .replace(',', '.')
                        ) || 0;
                    }

                    let sum = [2,3,4,5,6,7].map(i =>
                        data.reduce((a,b)=> a + toNumber(b[i]), 0)
                    );

                    $('#total_saldo_awal_debit').text(sum[0].toLocaleString('id-ID', {minimumFractionDigits: 2}));
                    $('#total_saldo_awal_kredit').text(sum[1].toLocaleString('id-ID', {minimumFractionDigits: 2}));
                    $('#total_pergerakan_debit').text(sum[2].toLocaleString('id-ID', {minimumFractionDigits: 2}));
                    $('#total_pergerakan_kredit').text(sum[3].toLocaleString('id-ID', {minimumFractionDigits: 2}));
                    $('#total_saldo_akhir_debit').text(sum[4].toLocaleString('id-ID', {minimumFractionDigits: 2}));
                    $('#total_saldo_akhir_kredit').text(sum[5].toLocaleString('id-ID', {minimumFractionDigits: 2}));
                }
            });
        }

        loadTable(); // pertama kali load

        // klo tombol cari ditekan, reload table
        $('button[type=submit]').click(function(e){
            e.preventDefault();
            loadTable();
        });
    });
    const convertDateFormat = function(dateString) {
        // Memisahkan tanggal, bulan, dan tahun dari string
        var dateParts = dateString.split("/");

        // Membalikkan urutan elemen array untuk membuat format "YYYY-MM-DD"
        var formattedDate = dateParts[2] + "-" + dateParts[1] + "-" + dateParts[0];

        return formattedDate;
    }
    
    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir;
        // console.log(url2);
        window.open(url2, "_blank");
    }
</script>


<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <div class="col-md-10">
            <h1>Neraca Saldo</h1>
        </div>
        <div class="col-md-2 text-right">
            <div class="btn-group">
                <button type="button" class="btn btn-warning">Export</button>
                <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item" onclick="printPDF('<?= base_url("/laporan-accounting/neracasaldo/printPDF"); ?>')">PDF</a>
                    <a class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-accounting/neracasaldo/printExcel"); ?>')">Excel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                    <form method="post" action="<?= base_url('/laporan-accounting/neracasaldo') ?>">
                        <?= csrf_field(); ?>
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
                    </form>
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
                            <?php
                            function format_ribuan($nilai)
                            {
                                $nilaiFloat = floatval($nilai);
                                return number_format($nilaiFloat, 2, ',', '.');
                            }
                            // kategori
                            $total_debit_awal  = 0;
                            $total_kredit_awal = 0;
                            $total_debit_pergerakan  = 0;
                            $total_kredit_pergerakan = 0;
                            $total_debit_akhir  = 0;
                            $total_kredit_akhir = 0;
                            foreach ($dataMetadata as $MetaData) :
                                foreach ($dataKategoriAkun as $kategoriAkunData) :
                                    if ($MetaData->id == $kategoriAkunData->kelompok_id) :
                                        foreach ($dataJurnalUmumWithGroup as $JurnalUmumDataGroup) :
                                            if ($kategoriAkunData->id == $JurnalUmumDataGroup->kategori_id) :
                            ?>
                                                <tr>
                                                    <td colspan="8"><?= $MetaData->value; ?></td>
                                                </tr>
                                                <?php
                                            endif;
                                        endforeach;
                                    endif;
                                endforeach;
                                foreach ($dataKategoriAkun as $kategoriAkunData) :
                                    if ($MetaData->id == $kategoriAkunData->kelompok_id) :
                                        foreach ($dataHeaderAkun as $headerAkunData) :
                                            if ($kategoriAkunData->id == $headerAkunData->kategori_id) :
                                                foreach ($dataJurnalUmumWithGroupHeader as $jurnalUmumDataGroupHeader) :
                                                    if ($headerAkunData->id == $jurnalUmumDataGroupHeader->id_header) :
                                                        $saldoawaldebit = 0;
                                                        $saldoawalkredit = 0;
                                                        $saldodebit = 0;
                                                        $saldokredit = 0;
                                                        foreach ($dataJurnalUmum as $jurnalUmumData) :
                                                            if ($jurnalUmumData->type_transaksi == '1404' && $jurnalUmumData->id_header == $headerAkunData->id) {
                                                                $saldoawaldebit  = $saldoawaldebit + $jurnalUmumData->debit;
                                                                $saldoawalkredit = $saldoawalkredit + $jurnalUmumData->kredit;
                                                            } elseif ($jurnalUmumData->type_transaksi != '1404' && $jurnalUmumData->id_header == $headerAkunData->id) {
                                                                $saldodebit  = $saldodebit + $jurnalUmumData->debit;
                                                                $saldokredit = $saldokredit + $jurnalUmumData->kredit;
                                                            }
                                                        endforeach;
                                                        $total_debit_awal += $saldoawaldebit;
                                                        $total_kredit_awal += $saldoawalkredit;
                                                        $total_debit_pergerakan += $saldodebit;
                                                        $total_kredit_pergerakan += $saldokredit;
                                                        $total_debit_akhir = $total_debit_awal + $total_debit_pergerakan;
                                                        $total_kredit_akhir = $total_kredit_awal + $total_kredit_pergerakan;
                                                ?>
                                                        <tr>
                                                            <td><?= $headerAkunData->no_header; ?></td>
                                                            <td><?= $headerAkunData->nama_header; ?></td>
                                                            <td style="text-align: right;"><?= format_ribuan($saldoawaldebit); ?></td>
                                                            <td style="text-align: right;"><?= format_ribuan($saldoawalkredit); ?></td>
                                                            <td style="text-align: right;"><?= format_ribuan($saldodebit); ?></td>
                                                            <td style="text-align: right;"><?= format_ribuan($saldokredit); ?></td>
                                                            <td style="text-align: right;"><?= format_ribuan($saldoawaldebit + $saldodebit); ?></td>
                                                            <td style="text-align: right;"><?= format_ribuan($saldoawalkredit + $saldokredit); ?></td>
                                                        </tr>
                            <?php
                                                    endif;
                                                endforeach;
                                            endif;
                                        endforeach;
                                    endif;
                                endforeach;
                            endforeach;
                            // akhir sub akun
                            ?>
                            <tr>
                                <td colspan="2"><strong>Total Transaksi</strong></td>
                                <td style="text-align: right;"><?= format_ribuan($total_debit_awal); ?></td>
                                <td style="text-align: right;"><?= format_ribuan($total_kredit_awal); ?></td>
                                <td style="text-align: right;"><?= format_ribuan($total_debit_pergerakan); ?></td>
                                <td style="text-align: right;"><?= format_ribuan($total_kredit_pergerakan); ?></td>
                                <td style="text-align: right;"><?= format_ribuan($total_debit_akhir); ?></td>
                                <td style="text-align: right;"><?= format_ribuan($total_kredit_akhir); ?></td>
                            </tr>
                        </tbody>
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

        // Tambahkan event listener untuk mengatur dateStart saat dateEnd berubah
        $(".dateEnd").on("changeDate", function(e) {
            // Ambil tanggal yang dipilih pada dateEnd
            var selectedDate = e.date;

            // <?php
                // // Jika dateStart tidak kosong, maka atur dateStart menjadi tanggal yang dipilih pada dateEnd
                // if ($dateStart) {
                // 
                ?>
            //     $(".dateStart").datepicker("setDate", new Date(selectedDate));
            // <?php
                // }
                // 
                ?>
            // // Atur dateStart menjadi tanggal 1 di bulan yang sama
            // $(".dateStart").datepicker("setDate", new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1));
        });

        // Set nilai awal dateStart pada saat dokumen siap (document ready)

        // $(".dateStart").datepicker("setDate", new Date(currentDate.getFullYear(), currentDate.getMonth(), 1));
        // $(".dateEnd").datepicker("setDate", new Date(currentDate));
        $(".clickable").click(function(e) {
            e.preventDefault();
            var targetClass = $(this).data('target');
            if ($(targetClass).hasClass('out')) {
                $(targetClass).addClass('in')
                $(targetClass).removeClass('out')
            } else {
                $(targetClass).addClass('out')
                $(targetClass).removeClass('in')
            }
            $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
        });
    });
    const convertDateFormat = function(dateString) {
        // Memisahkan tanggal, bulan, dan tahun dari string
        var dateParts = dateString.split("/");

        // Membalikkan urutan elemen array untuk membuat format "YYYY-MM-DD"
        var formattedDate = dateParts[2] + "-" + dateParts[1] + "-" + dateParts[0];

        return formattedDate;
    }
    const printPDF = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir;
        // console.log(url2);
        window.open(url2, "_blank");
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
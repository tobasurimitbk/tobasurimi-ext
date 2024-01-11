<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Jurnal Umum</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                    <form method="post" action="<?= base_url('/laporan-accounting/jurnalumum') ?>">
                        <?= csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="input-group">
                                    <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal" readonly>
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
                            <div class="col-md-2 mb-3">
                                <div class="input-group">
                                    <button type="submit" name="cariTanggal" class="btn btn-primary" value="cari">Cari</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
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
                                    <label for="floatingInput">Filter</label>
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
                                <th colspan="2">Tanggal</th>
                                <th colspan="2">Desc</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            function format_ribuan($nilai)
                            {
                                $nilaiFloat = floatval($nilai);
                                return "Rp " . number_format($nilaiFloat, 2, ',', '.');
                            }
                            $flag = 0;
                            foreach ($dataMetadataTipeTransaksi as $Tipe) :
                                foreach ($dataTransaksiJurnal as $transaksiJurnalData) :
                                    foreach ($dataJurnalUmumWithGroup as $jurnalUmumWithGroupData) :
                                        if ($jurnalUmumWithGroupData->id_transaksi == $transaksiJurnalData->id && $transaksiJurnalData->type_transaksi === $Tipe->id) :
                            ?>
                                            <tr data-header-id="<?= ($transaksiJurnalData->type_transaksi === $Tipe->id) ? $Tipe->hexid : 0; ?>">
                                                <td colspan="2"><?= date('d-m-Y', strtotime($jurnalUmumWithGroupData->tanggal_jurnal)); ?></td>
                                                <td colspan="4"><?= $jurnalUmumWithGroupData->no_transaksi . " " . $jurnalUmumWithGroupData->value; ?></td>
                                            </tr>
                                        <?php
                                        endif;
                                    endforeach;
                                    // kategori
                                    $total_debit  = 0;
                                    $total_kredit = 0;
                                    foreach ($dataJurnalUmum as $jurnalUmumData) :
                                        $flag = 1;
                                        $total_debit  += $jurnalUmumData->debit;
                                        $total_kredit += $jurnalUmumData->kredit;
                                        if ($jurnalUmumData->id_transaksi == $transaksiJurnalData->id && $transaksiJurnalData->type_transaksi === $Tipe->id) :
                                        ?>
                                            <tr data-header-id="<?= ($transaksiJurnalData->type_transaksi === $Tipe->id) ? $Tipe->hexid : 0; ?>">
                                                <td colspan="2"></td>
                                                <td colspan="2"><?= $jurnalUmumData->no_sub . " - " . $jurnalUmumData->nama_sub; ?></td>
                                                <td class="yy"><?= format_ribuan($jurnalUmumData->debit); ?></td>
                                                <td class="xx"><?= format_ribuan($jurnalUmumData->kredit); ?></td>
                                            </tr>
                            <?php
                                        endif;
                                    endforeach;
                                endforeach;
                            endforeach;
                            // akhir sub akun
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"><strong>Total Transaksi</strong></td>
                                <td id="jumlahDebet"><?= ($flag != 0) ? format_ribuan($total_debit) :  format_ribuan(0); ?></td>
                                <td id="jumlahKredit"><?= ($flag != 0) ? format_ribuan($total_kredit) : format_ribuan(0); ?></td>
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

        // Tambahkan event listener untuk mengatur dateStart saat dateEnd berubah
        $(".dateEnd").on("changeDate", function(e) {
            // Ambil tanggal yang dipilih pada dateEnd
            var selectedDate = e.date;

            // Atur dateStart menjadi tanggal 1 di bulan yang sama
            $(".dateStart").datepicker("setDate", new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1));
        });

        // Set nilai awal dateStart pada saat dokumen siap (document ready)
        $(".dateStart").datepicker("setDate", new Date(currentDate.getFullYear(), currentDate.getMonth(), 1));

        $('.type_transaksi').select2({
            placeholder: "Filter",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.type_transaksi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.type_transaksi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.type_transaksi')
            .parent('div')
            .find('label')
            .css('z-index', '1');

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

        $("#type_transaksi").change(function() {
            var selectedValue = $(this).val();
            var inputsDebit = 0;
            var inputsKredit = 0;

            // Check if the selected value is empty
            if (!selectedValue) {
                // Reset totalInputs menjadi 0 setiap kali dropdown berubah
                totalInputsDebit = 0;
                totalInputsKredit = 0;
                // Reset data-header-id and show all rows
                $("tbody tr").each(function() {
                    if ($(this).attr('data-header-id')) {
                        $(this).show();
                    }
                    inputsDebit = $(this).find('.yy');
                    inputsDebit.each(function() {
                        var inputValue = parseFloat(inputsDebit.text().replace('Rp ', '').replace('.', '').replace(',', '.'));
                        totalInputsDebit += inputValue;
                    });
                    inputsKredit = $(this).find('.xx');
                    inputsKredit.each(function() {
                        var inputValue = parseFloat(inputsKredit.text().replace('Rp ', '').replace('.', '').replace(',', '.'));
                        totalInputsKredit += inputValue;
                    });
                });
                $("#jumlahDebet").text(formatRupiah(totalInputsDebit.toString()));
                $("#jumlahKredit").text(formatRupiah(totalInputsKredit.toString()));
            } else {
                // Reset totalInputs menjadi 0 setiap kali dropdown berubah
                totalInputsDebit = 0;
                totalInputsKredit = 0;

                $("tbody tr").each(function() {
                    var headerIdValue = $(this).data('header-id');
                    if (headerIdValue === selectedValue) {
                        inputsDebit = $(this).find('.yy');
                        inputsDebit.each(function() {
                            var inputValue = parseFloat(inputsDebit.text().replace('Rp ', '').replace('.', '').replace(',', '.'));
                            totalInputsDebit += inputValue;
                        });
                        inputsKredit = $(this).find('.xx');
                        inputsKredit.each(function() {
                            var inputValue = parseFloat(inputsKredit.text().replace('Rp ', '').replace('.', '').replace(',', '.'));
                            totalInputsKredit += inputValue;
                        });
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                $("#jumlahDebet").text(formatRupiah(totalInputsDebit.toString()));
                $("#jumlahKredit").text(formatRupiah(totalInputsKredit.toString()));
            }
        });



        function formatRupiah(angka) {
            angka = angka.replace(/\./g, ',');
            angka = angka.replace(/[^\d,]/g, '');
            var parts = angka.split(',');
            var ribuan = parts[0];
            var desimal = parts[1] || '00';
            var reverse = ribuan.toString().split('').reverse().join('');
            var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
            return 'Rp. ' + ribuanFormatted + ',' + desimal;
        }
    });
</script>


<?= $this->endSection(); ?>
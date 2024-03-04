<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <div class="col-md-10">
            <h1>Jurnal Umum</h1>
        </div>
        <div class="col-md-2 text-right">
            <div class="btn-group">
                <button type="button" class="btn btn-warning">Export</button>
                <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item" onclick="printPDF('<?= base_url("/laporan-accounting/jurnalumum/printPDF"); ?>')">PDF</a>
                    <a class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-accounting/jurnalumum/printExcel"); ?>')">Excel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="<?= base_url("laporan-accounting/jurnalumum"); ?>" method="post" id="formSubmit" enctype="multipart/form-data">
                <div class="row justify-content-end row-col-spp">
                    <div class="col-md-12">
                        <?= csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal" value="<?= $dateStart; ?>" readonly style="height: 50px;">
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
                                    <select class="form-select type_transaksi" name="type_transaksi" id="type_transaksi" onchange="changeFilter()">
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
                                    <select class="form-select no_bukti" name="no_bukti" id="no_bukti" onchange="changeFilter()">
                                        <option value="" data-code=""></option>
                                        <?php
                                        if (!empty($dataTransaksiJurnal)) {
                                            foreach ($dataTransaksiJurnal as $transaksiJurnal) {
                                        ?>
                                                <option value="<?= $transaksiJurnal->tipe_transaksi_hex; ?>"><?= $transaksiJurnal->no_bukti; ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label for="floatingInput">No Bukti</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select class="form-select subs_akun" name="subs_akun" id="subs_akun">
                                        <option value="" data-code=""></option>
                                        <?php
                                        if (!empty($dataSubAkuns)) {
                                            foreach ($dataSubAkuns as $subs) {
                                        ?>
                                                <option value="<?= $subs->id; ?>"><?= $subs->nama_sub; ?></option>
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
            </form>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-hover" id="myTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th data-sortable="false" colspan="2">Tanggal</th>
                                <th data-sortable="false" colspan="2">Desc</th>
                                <th data-sortable="false">Debit</th>
                                <th data-sortable="false">Kredit</th>
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
                                            <tr onclick="updateJurnal('<?= ($jurnalUmumWithGroupData->id_transaksi == $transaksiJurnalData->id) ? $transaksiJurnalData->id_transaksi_hex : 0; ?>')" data-header-id="<?= ($transaksiJurnalData->type_transaksi === $Tipe->id) ? $Tipe->hexid : 0; ?>" data-transaksi-id="<?= ($jurnalUmumWithGroupData->id_transaksi == $transaksiJurnalData->id) ? $transaksiJurnalData->tipe_transaksi_hex : 0; ?>">
                                                <td colspan="2"><?= date('d-m-Y', strtotime($jurnalUmumWithGroupData->tanggal_jurnal)); ?></td>
                                                <td colspan="4">
                                                    <span class="badge badge-primary">
                                                        <?= $jurnalUmumWithGroupData->no_bukti ?>
                                                    </span>
                                                    <span class="badge badge-warning">
                                                        <?= $jurnalUmumWithGroupData->keterangan; ?>
                                                    </span>
                                                    <span class="badge badge-info">
                                                        <?= $jurnalUmumWithGroupData->valas . '(' . $jurnalUmumWithGroupData->exchange_rate . ')'; ?>
                                                    </span>
                                                </td>
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
                                            <tr onclick="updateJurnal('<?= ($jurnalUmumData->id_transaksi == $transaksiJurnalData->id) ? $transaksiJurnalData->id_transaksi_hex : 0; ?>')" data-header-id="<?= ($transaksiJurnalData->type_transaksi === $Tipe->id) ? $Tipe->hexid : 0; ?>" data-transaksi-id="<?= ($jurnalUmumData->id_transaksi == $transaksiJurnalData->id) ? $transaksiJurnalData->tipe_transaksi_hex : 0; ?>">
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
                                <td id="jumlahDebet"><strong><?= ($flag != 0) ? format_ribuan($total_debit) :  format_ribuan(0); ?></strong></td>
                                <td id="jumlahKredit"><strong><?= ($flag != 0) ? format_ribuan($total_kredit) : format_ribuan(0); ?></strong></td>
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
        $(".dateStart[readonly]").datepicker("destroy");
        $(".dateEnd[readonly]").datepicker("destroy");

        // Inisialisasi datepicker untuk dateStart dengan nilai default tanggal 1 di bulan berjalan
        $(".dateStart").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true,
            // Atur nilai awal menjadi tanggal 1 di bulan berjalan
            // defaultViewDate: {
            //     year: currentDate.getFullYear(),
            //     month: currentDate.getMonth(),
            //     day: 1
            // }
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
        // $(".dateStart").datepicker("setDate", new Date(currentDate.getFullYear(), currentDate.getMonth(), 1));

        $('.type_transaksi, .no_bukti, .subs_akun').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.type_transaksi, .no_bukti, .subs_akun')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.type_transaksi, .no_bukti, .subs_akun')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.type_transaksi, .no_bukti, .subs_akun')
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

        $("#dateEnd").change(function(e) {
            $('#formSubmit').submit();
        });
    });

    const changeFilter = function() {
        var selectedValueTypeTransaksi = $("#type_transaksi").val();
        var selectedValueNoBukti = $("#no_bukti").val();

        console.log(selectedValueTypeTransaksi);
        console.log(selectedValueNoBukti);
        var inputsDebit = 0;
        var inputsKredit = 0;

        // Check if the selected value is empty
        if (selectedValueTypeTransaksi || selectedValueNoBukti) {
            // Reset totalInputs menjadi 0 setiap kali dropdown berubah
            totalInputsDebit = 0;
            totalInputsKredit = 0;

            $("tbody tr").each(function() {
                var headerIdValue = $(this).data('header-id');
                var transaksiIdValue = $(this).data('transaksi-id');
                if (headerIdValue === selectedValueTypeTransaksi && !selectedValueNoBukti) {
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
                } else if (transaksiIdValue === selectedValueNoBukti && !selectedValueTypeTransaksi) {
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
                } else if (headerIdValue === selectedValueTypeTransaksi && transaksiIdValue === selectedValueNoBukti) {
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
        } else {
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
        }

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
    }

    const updateJurnal = function(transaksiId) {
        console.log(transaksiId);
        window.location.href = '<?= base_url("jurnal/update") ?>/' + transaksiId;
    }


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
        var filter = $(".type_transaksi").val() ? $(".type_transaksi").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter;
        // console.log(url2);
        window.open(url2, "_blank");
    }
    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var filter = $(".type_transaksi").val() ? $(".type_transaksi").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter;
        // console.log(url2);
        window.open(url2, "_blank");
    }
</script>


<?= $this->endSection(); ?>
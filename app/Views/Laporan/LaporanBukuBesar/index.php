<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Buku Besar</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                    <form method="post" action="<?= base_url('/laporan-accounting/bukubesar') ?>" class="create-form" role="form">
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
                                    <select class="form-select id_header" name="id_header" id="id_header">
                                        <option value="">All</option>
                                        <?php foreach ($dataHeaderAkun ?? [] as $HeaderAkunData) : ?>
                                            <option value="<?= $HeaderAkunData->hexid; ?>" data-header-id=""><?= $HeaderAkunData->nama_header; ?></option>
                                        <?php endforeach; ?>
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
                                <th>Nama Akun / Tanggal</th>
                                <th>Transaksi</th>
                                <th>No.</th>
                                <th>Deskripsi</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            function format_ribuan($nilai)
                            {
                                $nilais = "";
                                if ($nilai < 0) {
                                    $nilaiFloat = floatval($nilai);
                                    $nilais = '(' . number_format(abs($nilaiFloat), 2, ',', '.') . ')';
                                } else {
                                    $nilaiFloat = floatval($nilai);
                                    $nilais = number_format($nilaiFloat, 2, ',', '.');
                                }
                                return $nilais;
                            }
                            // kelompok
                            foreach ($dataHeaderAkun as $HeaderAkunData) :
                                foreach ($dataJurnalUmumWithGroup as $JurnalUmumDataGroup) :
                                    if ($JurnalUmumDataGroup->header_id == $HeaderAkunData->id) :
                            ?>
                                        <tr class="clickable" data-toggle="collapse" data-target=".collapse_<?= $HeaderAkunData->hexid; ?>" aria-expanded="false" data-header-id="<?= $HeaderAkunData->hexid; ?>">
                                            <td colspan="7"><i class="fas fa-chevron-down"></i><?= $HeaderAkunData->no_header . "-" . $HeaderAkunData->nama_header; ?></td>
                                        </tr>

                                    <?php
                                    endif;
                                endforeach;
                                $saldo = 0;
                                $totalsaldo = 0;
                                $totaldebit = 0;
                                $totalkredit = 0;
                                foreach ($dataJurnalUmum as $JurnalUmumData) :
                                    if ($JurnalUmumData->header_id == $HeaderAkunData->id) :
                                        if ($JurnalUmumData->type_transaksi == "penjualan") {
                                            $transaksi_format = "Sales Invoice";
                                        } else if ($JurnalUmumData->type_transaksi == "pembelian") {
                                            $transaksi_format = "Purchase Invoice";
                                        } else if ($JurnalUmumData->type_transaksi == "penerimaan") {
                                            $transaksi_format = "Receive Payment";
                                        } else if ($JurnalUmumData->type_transaksi == "biaya") {
                                            $transaksi_format = "Expense";
                                        } else {
                                            $transaksi_format = "Saldo Awal";
                                        }
                                        if ($JurnalUmumData->debit == 0) {
                                            $saldo = $saldo + $JurnalUmumData->debit - $JurnalUmumData->kredit;
                                        } else {
                                            $saldo = $saldo + $JurnalUmumData->debit;
                                        }
                                        $totaldebit += $JurnalUmumData->debit;
                                        $totalkredit += $JurnalUmumData->kredit;
                                    ?>
                                        <tr class="collapse_<?= $HeaderAkunData->hexid; ?> collapse out">
                                            <td><?= date('d-m-Y', strtotime($JurnalUmumData->tanggal_jurnal)); ?></td>
                                            <td><?= $transaksi_format; ?></td>
                                            <td><?= $JurnalUmumData->no_transaksi; ?></td>
                                            <td><?= $JurnalUmumData->keterangan; ?></td>
                                            <td><?= format_ribuan($JurnalUmumData->debit); ?></td>
                                            <td><?= format_ribuan($JurnalUmumData->kredit); ?></td>
                                            <td><?= format_ribuan($saldo); ?></td>
                                        </tr>
                                    <?php
                                    endif;
                                endforeach;
                                foreach ($dataJurnalUmumWithGroup as $JurnalUmumDataGroup) :
                                    if ($JurnalUmumDataGroup->header_id == $HeaderAkunData->id) :
                                    ?>
                                        <tr data-header-id="<?= $HeaderAkunData->hexid; ?>">
                                            <td colspan="4" style="text-align: right;">Total <?= $HeaderAkunData->no_header . "-" . $HeaderAkunData->nama_header; ?></td>
                                            <td><?= format_ribuan($totaldebit); ?></td>
                                            <td><?= format_ribuan($totalkredit); ?></td>
                                            <td><?= format_ribuan($totaldebit - $totalkredit); ?></td>
                                        </tr>
                            <?php
                                    endif;
                                endforeach; // akhir kelompok 
                            endforeach; // akhir kelompok 
                            ?>
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

        //CSS SELECT2 FLOATING LABEL
        $('.id_header').select2({
            placeholder: "Filter",
            theme: "bootstrap-5"
        });
        $('.id_header')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_header')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_header')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Tambahkan event listener untuk mengatur dateStart saat dateEnd berubah
        $(".dateEnd").on("changeDate", function(e) {
            // Ambil tanggal yang dipilih pada dateEnd
            var selectedDate = e.date;

            // Atur dateStart menjadi tanggal 1 di bulan yang sama
            $(".dateStart").datepicker("setDate", new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1));
        });

        // Set nilai awal dateStart pada saat dokumen siap (document ready)
        $(".dateStart").datepicker("setDate", new Date(currentDate.getFullYear(), currentDate.getMonth(), 1));
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

        $("#id_header").change(function() {
            var selectedValue = $(this).val();

            // Show all rows initially
            // $("tbody tr").show();

            // Hide rows that don't match the selected value
            if (selectedValue) {
                // console.log(selectedValue);
                $("tbody tr").each(function() {
                    var headerIdValue = $(this).data('header-id');
                    var targetClass = $(this).data('target');
                    if (headerIdValue === selectedValue) {
                        $(this).show();
                        // if ($(targetClass).hasClass('out')) {
                        //     $(targetClass).addClass('in')
                        //     $(targetClass).removeClass('out')
                        // } else {
                        //     $(targetClass).addClass('out')
                        //     $(targetClass).removeClass('in')
                        // }
                    } else {
                        // console.log(headerIdValue);
                        $(this).hide();
                        if ($(targetClass).hasClass('in')) {
                            $(targetClass).addClass('out')
                            $(targetClass).removeClass('in')
                        }
                    }
                });
            }
        });
    });
</script>


<?= $this->endSection(); ?>
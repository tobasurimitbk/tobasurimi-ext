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
                                <th colspan="2">Akun</th>
                                <th colspan="2">Tanggal</th>
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
                            // kategori
                            $total_debit  = 0;
                            $total_kredit = 0;
                            foreach ($dataJurnalUmum as $jurnalUmumData) :
                                $total_debit  += $jurnalUmumData->debit;
                                $total_kredit += $jurnalUmumData->kredit;
                            ?>
                                <tr>
                                    <td colspan="2"><?= $jurnalUmumData->no_sub . " - " . $jurnalUmumData->nama_sub; ?></td>
                                    <td colspan="2"><?= date('d-m-Y', strtotime($jurnalUmumData->tanggal_jurnal)); ?></td>
                                    <td><?= format_ribuan($jurnalUmumData->debit); ?></td>
                                    <td><?= format_ribuan($jurnalUmumData->kredit); ?></td>
                                </tr>
                            <?php
                            endforeach;
                            // akhir sub akun
                            ?>
                            <tr>
                                <td colspan="4"><strong>Total Transaksi</strong></td>
                                <td><?= format_ribuan($total_debit); ?></td>
                                <td><?= format_ribuan($total_kredit); ?></td>
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
    });
</script>


<?= $this->endSection(); ?>
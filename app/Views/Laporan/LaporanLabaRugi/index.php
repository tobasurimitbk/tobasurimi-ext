<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Neraca</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                    <form method="post" action="<?= base_url('/laporan-accounting/labarugi') ?>">
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
                        <?php
                        function format_ribuan($nilai)
                        {
                            $nilaiFloat = floatval($nilai);
                            return number_format($nilaiFloat, 2, ',', '.');
                        }
                        // kelompok
                        $total_kelompok_beban = 0;
                        $total_kelompok_pendapatan = 0;
                        $total_laba_rugi = 0;
                        foreach ($dataMetadata as $MetaData) :
                        ?>
                            <thead class="thead-dark">
                                <tr>
                                    <th colspan="2"><?= $MetaData->value; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // kategori
                                $total_kategori_beban = 0;
                                $total_kategori_pendapatan = 0;
                                foreach ($dataKategoriAkun as $KategoriAkun) :
                                    if ($MetaData->id == $KategoriAkun->kelompok_id) :
                                ?>
                                        <tr>
                                            <td colspan="2"><strong><?= $KategoriAkun->nama_kategori; ?></strong></td>
                                        </tr>
                                        <?php
                                        // sub akun
                                        foreach ($dataSubAkuns as $SubAkun) :
                                            if ($KategoriAkun->id == $SubAkun->kategori_id) :
                                        ?>
                                                <tr>
                                                    <td style="padding-left: 50px;"><?= $SubAkun->no_sub . " - " . $SubAkun->nama_sub; ?></td>
                                                    <td style="text-align: right;">
                                                        <?php
                                                        $saldolamadebit = 0;
                                                        $saldolamakredit = 0;
                                                        foreach ($dataJurnalUmum as $JurnalUmum) :
                                                            $debit = floatval($JurnalUmum->debit);
                                                            $kredit = floatval($JurnalUmum->kredit);
                                                            if ($SubAkun->id == $JurnalUmum->id_coa) {
                                                                if (stripos($MetaData->value, "Beban") !== false) {
                                                                    if ($JurnalUmum->debit == 0) {
                                                                        $saldolamadebit = $saldolamadebit + $JurnalUmum->debit - $JurnalUmum->kredit;
                                                                    } else {
                                                                        $saldolamadebit = $saldolamadebit + $JurnalUmum->debit;
                                                                    }
                                                                } else {
                                                                    if ($JurnalUmum->kredit == 0) {
                                                                        $saldolamakredit = $saldolamakredit + $JurnalUmum->kredit - $JurnalUmum->debit;
                                                                    } else {
                                                                        $saldolamakredit = $saldolamakredit + $JurnalUmum->kredit;
                                                                    }
                                                                }
                                                            }
                                                        endforeach;
                                                        $total_kategori_beban += $saldolamadebit;
                                                        $total_kategori_pendapatan += $saldolamakredit;
                                                        // echo "beban : " . $total_kategori_beban;
                                                        // echo "pendapatan : " . $total_kategori_pendapatan;
                                                        echo "Rp ";
                                                        if (stripos($MetaData->value, "Beban") !== false) {
                                                            echo format_ribuan($saldolamadebit);
                                                        } else {
                                                            echo format_ribuan($saldolamakredit);
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>

                                        <?php
                                            endif;
                                        endforeach;
                                        $total_kelompok_beban += $total_kategori_beban;
                                        $total_kelompok_pendapatan += $total_kategori_pendapatan;
                                        // akhir sub akun
                                        ?>
                                        <tr>
                                            <td><strong><?= "Total " . $KategoriAkun->nama_kategori; ?></strong></td>
                                            <td style="text-align: right;"><strong><?= stripos($MetaData->value, "Beban") !== false ? "Rp " . format_ribuan($total_kategori_beban) : "Rp " . format_ribuan($total_kategori_pendapatan); ?></strong></td>
                                        </tr>
                                <?php
                                    endif;
                                endforeach;
                                // akhir kategori
                                ?>
                            </tbody>
                        <?php
                            $total_laba_rugi = $total_kelompok_pendapatan - $total_kelompok_beban;
                        endforeach; // akhir kelompok 
                        ?>
                        <tr class="bg-primary">
                            <td><strong><?= "Total Laba Rugi" ?></strong></td>
                            <td style="text-align: right;"><strong><?= "Rp " . format_ribuan($total_laba_rugi); ?></strong></td>
                        </tr>
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
    });
</script>
<?= $this->endSection(); ?>
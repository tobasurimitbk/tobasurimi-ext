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
                    <form method="post" action="<?= base_url('/laporan-accounting/neraca') ?>">
                        <?= csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="input-group">
                                    <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                                    <div class="input-group-append">
                                        <button type="submit" name="cariTanggal" class="btn btn-primary" value="cari">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover" id="myTable" width="100%" cellspacing="0">
                        <?php
                        function format_ribuan($nilai)
                        {
                            $nilaiFloat = floatval($nilai);
                            return number_format($nilaiFloat, 2, ',', '.');
                        }
                        // kelompok
                        $total_kelompok_aktiva = 0;
                        $total_kelompok_pasiva = 0;
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
                                $total_kategori = 0;
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
                                                        $saldolama = 0;
                                                        foreach ($dataJurnalUmum as $JurnalUmum) :
                                                            $debit = floatval($JurnalUmum->debit);
                                                            $kredit = floatval($JurnalUmum->kredit);
                                                            if ($SubAkun->id == $JurnalUmum->id_coa) {
                                                                if (stripos($MetaData->value, "Aktiva") !== false) {
                                                                    if ($JurnalUmum->debit == 0) {
                                                                        $saldolama = $saldolama + $JurnalUmum->debit - $JurnalUmum->kredit;
                                                                    } else {
                                                                        $saldolama = $saldolama + $JurnalUmum->debit;
                                                                    }
                                                                    $total_kategori += $saldolama;
                                                                    $total_kelompok_aktiva += $total_kategori;
                                                                } else {
                                                                    if ($JurnalUmum->kredit == 0) {
                                                                        $saldolama = $saldolama + $JurnalUmum->kredit - $JurnalUmum->debit;
                                                                    } else {
                                                                        $saldolama = $saldolama + $JurnalUmum->kredit;
                                                                    }
                                                                    $total_kategori += $saldolama;
                                                                    $total_kelompok_pasiva += $total_kategori;
                                                                }
                                                            }
                                                        endforeach;
                                                        echo "Rp ";
                                                        echo format_ribuan($saldolama);
                                                        ?>
                                                    </td>
                                                </tr>

                                        <?php
                                            endif;
                                        endforeach;
                                        // akhir sub akun
                                        ?>
                                        <tr>
                                            <td><strong><?= "Total " . $KategoriAkun->nama_kategori; ?></strong></td>
                                            <td style="text-align: right;"><strong><?= "Rp " . format_ribuan($total_kategori); ?></strong></td>
                                        </tr>
                                    <?php
                                    endif;
                                endforeach;
                                // akhir kategori
                                if (stripos($MetaData->value, "Aktiva") !== false) {
                                    ?>
                                    <tr class="bg-primary">
                                        <td><strong><?= "Total Aktiva" ?></strong></td>
                                        <td style="text-align: right;"><strong><?= "Rp " . format_ribuan($total_kelompok_aktiva); ?></strong></td>
                                    </tr>
                                <?php }
                                if (stripos($MetaData->value, "Modal") !== false) { ?>
                                    <tr class="bg-primary">
                                        <td><strong><?= "Total Passiva" ?></strong></td>
                                        <td style="text-align: right;"><strong><?= "Rp " . format_ribuan($total_kelompok_pasiva); ?></strong></td>
                                    </tr>
                            </tbody>
                    <?php
                                }
                            endforeach; // akhir kelompok 
                    ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        $(".dateStart").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })
    });
</script>
<?= $this->endSection(); ?>
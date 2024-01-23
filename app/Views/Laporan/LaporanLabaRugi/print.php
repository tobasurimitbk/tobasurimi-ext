<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Laba Rugi</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      font-size: 5px;
    }

    h5 {
      font-weight: normal;
      font-size: 18px;
      margin-bottom: 10px;
      text-align: center;
      font-weight: bold;
      margin-top: 8px;
    }

    h6 {
      font-weight: normal;
      font-size: 13px;
      text-align: center;
      font-weight: bold;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    @page {
      size: 7.44in 10in landscape;
      margin: 5px;
      padding: 20px;
    }

    table {
      border-collapse: collapse !important;
    }

    #table1,
    th,
    td {
      border: 1px solid #999;
    }
  </style>
</head>

<body>
  <h5>Laporan Laba Rugi</h5>
  <h6>PT. TOBA SURIMI INDUSTRIES, Tbk ()</h6>
  <h6><?= ($dateStart != "All") ? $dateStart : "" ?> - <?= ($dateEnd != "Now") ? $dateEnd : "" ?></h6>

  <table width="100%" id="table1
      style=" margin-top: -20px;">
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
</body>

</html>
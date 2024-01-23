<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Neraca</title>
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
  <h5>Laporan Neraca</h5>
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
</body>

</html>
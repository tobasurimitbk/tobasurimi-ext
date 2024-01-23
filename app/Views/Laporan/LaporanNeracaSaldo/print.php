<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Neraca Saldo</title>
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
      size: 7.44in 10in portrait;
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
  <h5>Laporan Neraca Saldo</h5>
  <h6>PT. TOBA SURIMI INDUSTRIES, Tbk ()</h6>
  <h6><?= ($dateStart != "All") ? $dateStart : "" ?> - <?= ($dateEnd != "Now") ? $dateEnd : "" ?></h6>

  <table width="100%" id="table1
      style=" margin-top: -20px;">
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
        return "Rp " . number_format($nilaiFloat, 2, ',', '.');
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
                      if ($jurnalUmumData->type_transaksi == 'saldoawal' && $jurnalUmumData->id_header == $headerAkunData->id) {
                        $saldoawaldebit  = $saldoawaldebit + $jurnalUmumData->debit;
                        $saldoawalkredit = $saldoawalkredit + $jurnalUmumData->kredit;
                      } elseif ($jurnalUmumData->type_transaksi != 'saldoawal' && $jurnalUmumData->id_header == $headerAkunData->id) {
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
</body>

</html>
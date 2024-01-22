<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Jurnal Umum</title>
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
  <h5>Laporan Jurnal Umum</h5>
  <h6>PT. TOBA SURIMI INDUSTRIES, Tbk ()</h6>
  <h6><?= ($dateStart != "All") ? $dateStart : "" ?> - <?= ($dateEnd != "Now") ? $dateEnd : "" ?></h6>

  <table width="100%" id="table1
      style=" margin-top: -20px;">
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
</body>

</html>
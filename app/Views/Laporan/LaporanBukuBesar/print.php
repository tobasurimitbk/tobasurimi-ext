<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Buku Besar</title>
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
  <h5>Laporan Buku Besar</h5>
  <h6>PT. TOBA SURIMI INDUSTRIES, Tbk ()</h6>
  <h6><?= ($dateStart != "All") ? $dateStart : "" ?> - <?= ($dateEnd != "Now") ? $dateEnd : "" ?></h6>

  <table width="100%" id="table1
      style=" margin-top: -20px;">
    <thead>
      <tr>
        <th>Nama Akun / Tanggal</th>
        <th>Transaksi</th>
        <th>No</th>
        <th>Deskripsi</th>
        <th>Debit</th>
        <th>Kredit</th>
        <th>Saldo</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($dataJurnalUmumWithGroup)  == 0): ?>
        <tr>
          <td colspan="7">Tidak Ada Transaksi</td>
        </tr>
      <?php else: ?>
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
              <tr class="clickable" data-toggle="collapse" data-target=".collapse_<?= $HeaderAkunData->id; ?>" aria-expanded="false" data-header-id="<?= $HeaderAkunData->id; ?>">
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
              if ($JurnalUmumData->debit == 0) {
                $saldo = $saldo + $JurnalUmumData->debit - $JurnalUmumData->kredit;
              } else {
                $saldo = $saldo + $JurnalUmumData->debit;
              }
              $totaldebit += $JurnalUmumData->debit;
              $totalkredit += $JurnalUmumData->kredit;
            ?>
              <tr class="collapse_<?= $HeaderAkunData->id; ?> collapse out">
                <td><?= date('d-m-Y', strtotime($JurnalUmumData->tanggal_jurnal)); ?></td>
                <td><?= $JurnalUmumData->value; ?></td>
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
              <tr data-header-id="<?= $HeaderAkunData->id; ?>">
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
      <?php endif ?>
    </tbody>
  </table>
</body>

</html>
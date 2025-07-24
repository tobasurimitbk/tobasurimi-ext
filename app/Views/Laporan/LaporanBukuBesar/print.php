<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Buku Besar</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      font-size: 11px;
      margin: 0;
    }

    @page {
      size: auto;
      margin: 0 5px; /* Reduced margins to maximize space */
    }

    h5 {
      font-weight: bold;
      font-size: 18px;
      margin-bottom: 5px;
      margin-top: 5px;
      text-align: center;
    }

    h6 {
      font-weight: bold;
      font-size: 13px;
      margin: 5px 0;
      text-align: center;
    }

    table {
      border-collapse: collapse !important;
      width: 100%;
      table-layout: fixed; /* Ensures column width consistency */
    }

    #table1, th, td {
      border: 1px solid #999;
      word-wrap: break-word; /* Allows text to wrap within cells */
    }
  </style>
</head>

<body>
  <h5>Laporan Buku Besar</h5>
  <h6>PT. TOBA SURIMI INDUSTRIES, Tbk</h6>
  <h6><b>Laporan Buku Besar</b> Periode <?= $_POST['dateStart'] ?> s.d <?= @$_POST['dateEnd'] ?></h6>

  <?php if (count($jurnalUmum) > 0): ?>
    <?php foreach ($jurnalUmum as $j): ?>
      <div style="margin-top: 8px; margin-bottom:8px;">
        <b><?= $j['number'] ?> - <?= $j['name'] ?></b>
      </div>
      <table id="table1">
        <thead>
          <tr>
            <th style="width: 7%;">Tanggal</th>
            <th style="width: 8%;">No Trx</th>
            <th style="width: 10%;">Supplier</th>
            <th style="width: 35%;">Desc</th> <!-- Significantly wider description column -->
            <th style="width: 8%;">Currency</th>
            <th style="width: 7%;">Exch</th>
            <th style="width: 8%;">Debit</th>
            <th style="width: 8%;">Kredit</th>
            <th style="width: 9%;">Balance</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="4">Saldo Awal : </td>
            <td><?= toRupiah($j['saldo_lama']) ?></td>
          </tr>
          <?php
          $sisaSaldo = $j['saldo_lama'];
          $totalKredit = 0;
          $totalDebit = 0;
          ?>
          <?php foreach ($j['result'] as $r): ?>
            <?php
            $sisaSaldo += ($r['debit'] * $r['kurs']) - ($r['kredit'] * $r['kurs']);
            $totalDebit += $r['debit'] * $r['kurs'];
            $totalKredit += $r['kredit'] * $r['kurs'];
            ?>
            <tr>
              <td><?= date('d/m/Y', strtotime($r['tanggal_jurnal'])) ?></td>
              <td><?= $r['no_transaksi'] ?></td>
              <td><?= $r['supplier_name'] ?></td>
              <td style="text-align: left;"><?= $r['keterangan'] ?></td> <!-- Left aligned for better readability -->
              <td><?= toRupiah($r['kredit'] / $r['kurs']) . " " . "<b>" . $r['valas'] . "</b>" ?></td>
              <td><?= $r['kurs'] == "1" ? "" : toRupiah($r['kurs']) ?></td>
              <td style="text-align: right;"><?= toRupiah($r['debit'] * $r['kurs']) ?></td>
              <td style="text-align: right;"><?= toRupiah($r['kredit']) ?></td>
              <td style="text-align: right;"><?= toRupiah($sisaSaldo) ?></td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="6" style="text-align: center;font-weight:bold;">
              <b>Sub Total</b>
            </td>
            <td style="text-align: right;">
              <b><?= toRupiah($totalDebit) ?></b>
            </td>
            <td style="text-align: right;">
              <b><?= toRupiah($totalKredit) ?></b>
            </td>
            <td></td>
          </tr>
          <tr>
            <td colspan="6" style="text-align: center;">
              <b>Total</b>
            </td>
            <td></td>
            <td></td>
            <td style="text-align: right;">
              <b><?= toRupiah($sisaSaldo) ?></b>
            </td>
          </tr>
        </tbody>
      </table>
    <?php endforeach; ?>
  <?php else: ?>
    <div style="text-align: center;font-size:16px;" role="alert">
      <h6> Silahkan Pilih Akun yang Akan Dieksekusi</h6>
    </div>
  <?php endif; ?>
</body>
</html>
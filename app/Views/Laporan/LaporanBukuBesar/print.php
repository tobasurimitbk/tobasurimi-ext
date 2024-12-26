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
  <h6>PT. TOBA SURIMI INDUSTRIES, Tbk</h6>
  <h6><b>Laporan Buku Besar</b> Periode <?= $_POST['dateStart'] ?> s.d <?= @$_POST['dateEnd'] ?></h6>

  <?php if (count($jurnalUmum) > 0): ?>
    <?php foreach ($jurnalUmum as $j): ?>
      <div style="margin-top: 10px; margin-bottom:10px;">
        <b><?= $j['number'] ?> - <?= $j['name'] ?></b>
      </div>
      <table width="100%" id="table1
      style=" margin-top: -20px;">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Jenis Transaksi</th>
            <th>No Transaksi</th>
            <th>Desc</th>
            <th>Currency</th>
            <th>Exchange Rate</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Balance</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td colspan="8">Saldo Awal : </td>
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
              <td><?= $r['jenis_transaksi'] ?></td>
              <td><?= $r['no_transaksi'] ?></td>
              <td><?= $r['keterangan'] ?></td>
              <td><?= toRupiah(abs($r['debit'] - $r['kredit'])) . " " . "<b>" . $r['valas'] . "</b>" ?></td>
              <td><?= $r['kurs'] == "1" ? "" : toRupiah($r['kurs']) ?></td>
              <td><?= toRupiah($r['debit'] * $r['kurs']) ?></td>
              <td><?= toRupiah($r['kredit'] * $r['kurs']) ?></td>
              <td><?= toRupiah($sisaSaldo) ?></td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="6" style="text-align: center;font-weight:bold;">
              <b>Sub Total</b>
            </td>
            <td>
              <b>
                <?= toRupiah($totalDebit) ?>
              </b>
            </td>
            <td>
              <b>
                <?= toRupiah($totalKredit) ?>
              </b>
            </td>
            <td></td>
          </tr>
          <tr>
            <td colspan="6" style="text-align: center;">
              <b>Total</b>
            </td>
            <td></td>
            <td></td>
            <td>
              <b>
                <?= toRupiah($sisaSaldo) ?>
              </b>
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
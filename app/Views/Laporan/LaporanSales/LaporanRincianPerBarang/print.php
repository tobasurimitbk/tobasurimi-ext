<!DOCTYPE html>
<html>

<head>
  <title>Laporan Rincian Sales Per Barang</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
    }

    th {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .bold {
      font-weight: bold;
    }

    .group-header {
      font-weight: bold;
    }

    .total-row {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <h1 style="text-align: center;">TOBA FISH</h1>
  <h2 style="text-align: center;">LAPORAN RINCIAN SALES PER BARANG</h2>
  <p style="text-align: center;">Periode: <?= $dateStart ?> - <?= $dateEnd ?></p>

  <table>
    <thead>
      <tr>
        <th>No. Faktur</th>
        <th>Tanggal Faktur</th>
        <th>Keterangan</th>
        <th>Kuantitas</th>
        <th>Satuan</th>
        <th>Jumlah</th>
        <th>Nilai HPP</th>
        <th>Laba Kotor</th>
        <th>Nama Pelanggan</th>
        <th>Nama Penjual</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
        <?php if (isset($row['is_customer']) && $row['is_customer']): ?>
          <tr class="group-header">
            <td colspan="10"><?= $row['kode_barang'] ?> - <?= $row['barang_name'] ?></td>
          </tr>
        <?php elseif (isset($row['is_total']) && $row['is_total']): ?>
          <tr class="total-row">
            <td colspan="3"></td>
            <td class="text-right"><?= $row['total_qty'] ?></td>
            <td class="text-right"></td>
            <td class="text-right"><?= $row['total_invoice'] ?></td>
            <td class="text-right"><?= $row['total_hpp'] ?></td>
            <td class="text-right"><?= $row['total_laba'] ?></td>
            <td colspan="2"></td>
          </tr>
        <?php elseif (isset($row['is_grand_total']) && $row['is_grand_total']): ?>
          <tr class="grand-total-row">
              <td colspan="3"><strong>GRAND TOTAL</strong></td>
              <td class="text-right"><strong><?= $row['total_qty'] ?></strong></td>
              <td class="text-right"></td>
              <td class="text-right"><strong><?= $row['total_invoice'] ?></strong></td>
              <td class="text-right"><strong><?= $row['total_hpp'] ?></strong></td>
              <td class="text-right"><strong><?= $row['total_laba'] ?></strong></td>
              <td colspan="2"></td>
          </tr>
        <?php else: ?>
          <tr>
            <td><?= $row['no_faktur'] ?></td>
            <td><?= $row['tanggal_faktur'] ?></td>
            <td><?= $row['keterangan'] ?></td>
            <td class="text-right"><?= $row['qty_invoice'] ?></td>
            <td><?= $row['kode_satuan'] ?></td>
            <td class="text-right"><?= $row['total_invoice'] ?></td>
            <td class="text-right"><?= $row['amt_harga_pokok'] ?></td>
            <td class="text-right"><?= $row['amt_laba'] ?></td>
            <td><?= $row['nama_pelanggan'] ?></td>
            <td><?= $row['nama_sales'] ?></td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>
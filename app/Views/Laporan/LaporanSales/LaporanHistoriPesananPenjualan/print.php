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
  <h2 style="text-align: center;">Histori Pesanan Penjualan</h2>
  <p style="text-align: center;">Periode: <?= $dateStart ?> - <?= $dateEnd ?></p>

  <table>
    <thead>
      <tr>
        <th>Tipe Proses</th>
        <th>No Faktur</th>
        <th>Tanggal Faktur</th>
        <th>Qty Faktur</th>
        <th>Nama Pelanggan</th>
        <th>Nama Barang</th>
        <th>Nama Penjual</th>
        <th>Kuantitas</th>
        <th>Satuan</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): ?>
        <?php if (isset($row['is_customer']) && $row['is_customer']): ?>
          <tr class="group-header">
            <td colspan="10"><?= $row['tipe_proses'] ?></td>
          </tr>
        <?php elseif (isset($row['is_total']) && $row['is_total']): ?>
          <tr class="total-row">
            <td colspan="3">Total</td>
            <td class="text-right"><?= $row['qty_faktur'] ?></td>
            <td colspan="3"></td>
            <td class="text-right"><?= $row['qty_order'] ?></td>
            <td colspan="2"></td>
          </tr>
        <?php else: ?>
          <tr>
            <td><?= $row['tipe_proses'] ?></td>
            <td><?= $row['no_faktur'] ?></td>
            <td><?= $row['tanggal_faktur'] ?></td>
            <td class="text-right"><?= $row['qty_faktur'] ?></td>
            <td><?= $row['nama_pelanggan'] ?></td>
            <td><?= $row['nama_barang'] ?></td>
            <td><?= $row['nama_sales'] ?></td>
            <td class="text-right"><?= $row['qty_order'] ?></td>
            <td><?= $row['satuan'] ?></td>
            <td><?= $row['keterangan'] ?></td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>
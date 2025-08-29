<!DOCTYPE html>
<html>

<head>
  <title>Laporan Penjualan Per Barang</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 10px;
    }

    .header {
      text-align: center;
    }

    .header h1 {
      margin-bottom: 5px;
    }

    .info {
      margin-bottom: 15px;
    }

    .text-center {
      text-align: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table,
    th,
    td {
      border: 1px solid #000;
    }

    th,
    td {
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
      text-align: center;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .total-row {
      font-weight: bold;
      background-color: #e0e0e0;
    }
  </style>
</head>

<body>
  <div class="header">
    <h2>TOBA FISH</h2>
    <h2 style="color: red;">PENJUALAN PER BARANG</h2>
    <p><strong>Periode:</strong> <?= $dateStart ?> - <?= $dateEnd ?></p>
  </div>

  <table>
    <thead>
      <tr>
        <th width="5%">No</th>
        <th width="25%">Keterangan Barang</th>
        <th width="10%">Kuantitas</th>
        <th width="10%">Satuan</th>
        <th width="15%">Jumlah</th>
        <th width="15%">Nilai HPP</th>
        <th width="15%">Laba Kotor</th>
        <th width="10%">Jumlah Data</th>
        <th width="10%">No. Barang</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($data)): ?>
        <?php foreach ($data as $row): ?>
          <tr>
            <td class="text-center"><?= $row['no'] ?></td>
            <td class="text-left"><?= $row['barang_name'] ?></td>
            <td class="text-center"><?= $row['qty_invoice'] ?></td>
            <td class="text-center"><?= $row['kode_satuan'] ?></td>
            <td class="text-right"><?= $row['sum_amount_invoice'] ?></td>
            <td class="text-right"><?= $row['amt_harga_pokok'] ?></td>
            <td class="text-right"><?= $row['amt_laba'] ?></td>
            <td class="text-center"><?= $row['count_invoice'] ?></td>
            <td class="text-center"><?= $row['kode_barang'] ?></td>
          </tr>
        <?php endforeach; ?>
        <tr class="total-row">
          <td colspan="2" class="text-center">TOTAL</td>
          <td class="text-center"><?= $totalQty ?></td>
          <td class="text-right"></td>
          <td class="text-right"><?= $totalInvoice ?></td>
          <td class="text-right"><?= $totalHpp ?></td>
          <td class="text-right"><?= $totalLabaKotor ?></td>
          <td class="text-center"><?= $totalData ?></td>
          <td class="text-right"></td>
        </tr>
      <?php else: ?>
        <tr>
          <td colspan="9" class="text-center">Tidak ada data</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>

</html>
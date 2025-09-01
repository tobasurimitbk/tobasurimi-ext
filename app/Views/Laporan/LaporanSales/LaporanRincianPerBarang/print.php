<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rincian Penjualan Per Barang</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      font-size: 10px;
      padding: 20px;
    }

    h5 {
      font-size: 16px;
      text-align: center;
      font-weight: bold;
      margin: 6px 0;
    }

    h6 {
      font-size: 12px;
      text-align: center;
      font-weight: bold;
      margin: 6px 0;
    }

    @page {
      size: A4 landscape;
      margin: 10px;
    }

    table {
      border-collapse: collapse !important;
      width: 100%;
    }

    #table1 th,
    #table1 td {
      border: 1px solid #999;
      font-size: 10px;
      padding: 4px;
    }

    .group-row {
      font-weight: bold;
      background: #f0f0f0;
    }

    .total-row {
      font-weight: bold;
      background: #e0e0e0;
    }
  </style>
</head>

<body>
  <h6>TOBA FISH</h6>
  <h5>Rincian Penjualan per Barang</h5>
  <h6>
    Dari <?= ($dateStart != "All") ? $dateStart : "-" ?>
    s/d <?= ($dateEnd != "Now") ? $dateEnd : "-" ?>
  </h6>

  <table id="table1">
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
        <th>Nama Barang</th>
        <th>Nama Pelanggan</th>
        <th>Nama Penjual</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $value) : ?>
        <?php if (isset($value['is_customer']) && $value['is_customer']) : ?>
          <tr class="group-row">
            <td colspan="9"><?= $value['barang_name'] ?></td>
          </tr>
        <?php elseif (isset($value['is_total']) && $value['is_total']) : ?>
          <tr class="total-row">
            <td colspan="9">Total</td>
          </tr>
        <?php else : ?>
          <tr>
            <td><?= $value['no_faktur'] ?></td>
            <td><?= $value['tanggal_faktur'] ?></td>
            <td><?= $value['keterangan'] ?></td>
            <td style="text-align:right"><?= $value['qty_invoice'] ?></td>
            <td><?= $value['kode_satuan'] ?></td>
            <td style="text-align:right"><?= $value['total_invoice'] ?></td>
            <td><?= $value['barang_name'] ?></td>
            <td><?= $value['nama_pelanggan'] ?></td>
            <td><?= $value['nama_sales'] ?></td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>
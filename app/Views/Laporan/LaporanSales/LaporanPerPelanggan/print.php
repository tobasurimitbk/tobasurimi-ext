<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rincian Penjualan Per Pelanggan</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      font-size: 5px;
      padding: 20px;
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
      padding: 30px;
    }

    table {
      border-collapse: collapse !important;
    }

    #table1,
    th,
    td {
      border: 1px solid #999;
      font-size: 12px;
    }
  </style>
</head>

<body>
  <h6>TOBA FISH</h6>
  <h5>Rincian Penjualan per Pelanggan</h5>
  <h6>Dari <?= ($dateStart != "All") ? $dateStart : "-" ?> s/d <?= ($dateEnd != "Now") ? $dateEnd : "-" ?></h6>

  <table width="100%" id="table1">
    <thead>
      <tr>
        <th>No. Faktur</th>
        <th>Tanggal Faktur</th>
        <th>Keterangan</th>
        <th>Jumlah</th>
        <th>Nama Pelanggan</th>
        <th>Nama Penjual</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $currentCustomer = null; // Variabel untuk melacak pelanggan saat ini
      foreach ($data as $value) :
        if (isset($value['is_customer'])) {
          // Hanya tampilkan nama pelanggan jika belum ditampilkan
          if ($currentCustomer !== $value['no_faktur']) {
            $currentCustomer = $value['no_faktur'];
      ?>
            <tr>
              <td colspan="6" style="font-weight: bold;"><?= $value['no_faktur'] ?></td>
            </tr>
          <?php
          }
        } elseif (isset($value['is_total'])) {
          // Tampilkan total hanya untuk pelanggan yang sama
          ?>
          <tr>
            <td colspan="3"></td>
            <td style="font-weight: bold;"><?= $value['no_faktur'] ?></td>
            <td colspan="2"></td>
          </tr>
        <?php
        } else {
        ?>
          <tr>
            <td><?= $value['no_faktur'] ?></td>
            <td><?= $value['tanggal_faktur'] ?></td>
            <td><?= $value['keterangan'] ?></td>
            <td><?= $value['total_invoice'] ?></td>
            <td><?= $value['nama_pelanggan'] ?></td>
            <td><?= $value['nama_sales'] ?></td>
          </tr>
      <?php
        }
      endforeach;
      ?>
    </tbody>
  </table>
</body>

</html>
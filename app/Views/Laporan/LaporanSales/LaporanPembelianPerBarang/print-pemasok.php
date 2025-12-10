<!DOCTYPE html>
<html>

<head>
  <title>Laporan Pembelian Barang per Pemasok</title>
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
    <h2 style="color: red;">PEMBELIAN BARANG PER PEMASOK</h2>
    <p><strong>Periode:</strong> <?= $dateStart ?> - <?= $dateEnd ?></p>
  </div>

  <table>
    <thead>
      <tr>
        <th width="5%">No</th>
        <th width="35%">Keterangan Barang</th>

        <?php foreach ($listSupplier as $s): ?>
            <th><?= $s['name'] ?></th>
        <?php endforeach; ?>

      </tr>
    </thead>

    <tbody>
      <?php if (!empty($data)): ?>
        <?php foreach ($data as $row): ?>
          <tr>
            <td class="text-center"><?= $row['no'] ?></td>
            <td><?= $row['barang_name'] ?></td>

            <?php foreach ($listSupplier as $s): ?>
              <td class="text-right">
                  <?= $row['sup_' . $s['id']] ?>
              </td>
            <?php endforeach; ?>

          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="<?= count($listSupplier)+2 ?>" class="text-center">Tidak ada data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>

</html>
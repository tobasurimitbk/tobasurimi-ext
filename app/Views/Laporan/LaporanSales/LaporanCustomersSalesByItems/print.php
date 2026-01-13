<!DOCTYPE html>
<html>
<head>
    <title>Laporan Customers Sales By Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 2px;
            word-wrap: break-word;
        }

        th {
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .total-row {
            background: #eaeaea;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h3 style="text-align:center">TOBA FISH</h3>
<h2 style="text-align:center;color:red">Customers Sales By Items</h2>
<p style="text-align:center">
    Periode: <?= $dateStart ?> - <?= $dateEnd ?>
</p>

<table>
    <thead>
        <tr>
            <th style="width:200px">Customer</th>
            <?php foreach ($header as $h): ?>
                <th class="text-right"><?= $h['nama_barang'] ?></th>
            <?php endforeach; ?>
            <th class="text-right">Total</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td class="text-left"><?= $row['customer_name'] ?></td>

                <?php foreach ($header as $h): ?>
                    <td class="text-right"><?= number_format($row['items'][$h['barang_id']] ?? 0, 0) ?></td>
                <?php endforeach; ?>

                <td class="text-right"><?= number_format($row['total'], 0) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>

    <tfoot>
        <tr class="total-row">
            <td class="text-center">TOTAL</td>

            <?php foreach ($header as $h): ?>
                <td class="text-right"><?= number_format($footer['per_barang'][$h['barang_id']] ?? 0, 0) ?></td>
            <?php endforeach; ?>

            <td class="text-right"><?= number_format($footer['grand_total'], 0) ?></td>
        </tr>
    </tfoot>
</table>

</body>
</html>

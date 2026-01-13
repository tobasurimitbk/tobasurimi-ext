<!DOCTYPE html>
<html>
<head>
    <title>Laporan Items Sales By Customers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
        }

        h2, h3, p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
            word-wrap: break-word;
        }

        th {
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .text-right { text-align: right; }
        .text-left  { text-align: left; }
        .text-center{ text-align: center; }

        .total-row {
            background: #e0e0e0;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h3 style="text-align:center;">TOBA FISH</h3>
<h2 style="text-align:center; color:red;">ITEMS SALES BY CUSTOMERS</h2>
<p style="text-align:center;">
    Periode: <?= esc($dateStart) ?> - <?= esc($dateEnd) ?>
</p>

<table>
    <thead>
        <tr>
            <th style="width:180px;">Customer</th>

            <?php foreach ($header as $h): ?>
                <th><?= esc($h['nama_barang']) ?></th>
            <?php endforeach; ?>

            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td class="text-left">
                    <?= esc($row['customer_name']) ?>
                </td>

                <?php foreach ($header as $h): ?>
                    <td class="text-right">
                        <?= number_format($row['items'][$h['barang_id']] ?? 0, 0) ?>
                    </td>
                <?php endforeach; ?>

                <td class="text-right">
                    <?= number_format($row['total'], 0) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>

    <tfoot>
        <tr class="total-row">
            <td class="text-center">TOTAL</td>

            <?php foreach ($header as $h): ?>
                <td class="text-right">
                    <?= number_format($footer['per_barang'][$h['barang_id']] ?? 0, 0) ?>
                </td>
            <?php endforeach; ?>

            <td class="text-right">
                <?= number_format($footer['grand_total'], 0) ?>
            </td>
        </tr>
    </tfoot>
</table>

</body>
</html>

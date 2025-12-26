<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #eee;
        }

        .text-left {
            text-align: left;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;"><?= $title ?></h2>
    <p style="text-align: center;">Periode: <?= $date_range ?></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Valas</th>
                <th>Exchange Rate</th>
                <th>Nominal (Rp)</th>
                <th>Remaining (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($data as $item): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="text-left"><?= $item->tanggal_invoice ?></td>
                    <td class="text-left"><?= $item->no_invoice ?></td>
                    <td class="text-left"><?= $item->valas_name ?? 'IDR' ?></td>
                    <td class="text-left"><?= number_format(($item->nilai_kurs ?? 1), 2, '.', ''); ?></td>
                    <td><?= number_format($item->total * ($item->nilai_kurs ?? 1), 2, ',', '.'); ?></td>
                    <td><?= number_format(($item->total - $item->remaining) * ($item->nilai_kurs ?? 1), 2, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
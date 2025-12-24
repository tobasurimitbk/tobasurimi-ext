<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .periode {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        th {
            background: #eee;
            text-align: center;
            font-weight: bold;
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
    </style>
</head>

<body>

    <h2>LAPORAN PUNGUTAN BC 2.3</h2>
    <div class="periode">Periode : <?= $periode ?></div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Supplier</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No Aju</th>
                <th rowspan="2">No Daftar</th>
                <th rowspan="2">Tipe PO</th>
                <th colspan="2">PPN</th>
                <th colspan="2">PPH</th>
                <th colspan="2">BM</th>
            </tr>
            <tr>
                <th>Nilai</th>
                <th>Status</th>
                <th>Nilai</th>
                <th>Status</th>
                <th>Nilai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $total = ['PPN' => 0, 'PPH' => 0, 'BM' => 0];
            foreach ($data as $r):
                $total['PPN'] += $r['pungutan']['PPN']['nilai'];
                $total['PPH'] += $r['pungutan']['PPH']['nilai'];
                $total['BM']  += $r['pungutan']['BM']['nilai'];
            ?>
                <tr>
                    <td class="text-center"><?= $r['no'] ?></td>
                    <td><?= $r['supplier_name'] ?></td>
                    <td class="text-center"><?= $r['date'] ?></td>
                    <td><?= $r['no_aju'] ?></td>
                    <td><?= $r['no_daftar'] ?></td>
                    <td><?= $r['po_type'] ?></td>

                    <td class="text-right"><?= number_format($r['pungutan']['PPN']['nilai'], 2) ?></td>
                    <td><?= $r['pungutan']['PPN']['status'] ?></td>

                    <td class="text-right"><?= number_format($r['pungutan']['PPH']['nilai'], 2) ?></td>
                    <td><?= $r['pungutan']['PPH']['status'] ?></td>

                    <td class="text-right"><?= number_format($r['pungutan']['BM']['nilai'], 2) ?></td>
                    <td><?= $r['pungutan']['BM']['status'] ?></td>
                </tr>
            <?php endforeach; ?>

            <tr class="bold">
                <td colspan="6" class="text-right">TOTAL PPN</td>
                <td class="text-right"><?= number_format($total['PPN'], 2) ?></td>
                <td colspan="5"></td>
            </tr>
            <tr class="bold">
                <td colspan="6" class="text-right">TOTAL PPH</td>
                <td class="text-right"><?= number_format($total['PPH'], 2) ?></td>
                <td colspan="5"></td>
            </tr>
            <tr class="bold">
                <td colspan="6" class="text-right">TOTAL BM</td>
                <td class="text-right"><?= number_format($total['BM'], 2) ?></td>
                <td colspan="5"></td>
            </tr>
            <tr class="bold">
                <td colspan="6" class="text-right">GRAND TOTAL PUNGUTAN</td>
                <td class="text-right"><?= number_format($total['PPN'] + $total['PPH'] + $total['BM'], 2) ?></td>
                <td colspan="5"></td>
            </tr>

        </tbody>
    </table>

</body>

</html>
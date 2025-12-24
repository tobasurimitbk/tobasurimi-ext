<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pungutan BC 4.0</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background: #f2f2f2;
        }

        .table-info {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .table-info tr,
        .table-info td {
            border: none !important;
            padding: 0;
        }
    </style>
</head>

<body>

    <h2>LAPORAN PUNGUTAN BC 4.0</h2>

    <table class="table-info">
        <tr>
            <td width="50">Periode</td>
            <td width="10">:</td>
            <td>
                <?php if ($condition['mulaiTanggalBC40'] && $condition['selesaiTanggalBC40']) : ?>
                    <?= date('d/m/Y', strtotime($condition['mulaiTanggalBC40'])); ?>
                    s/d
                    <?= date('d/m/Y', strtotime($condition['selesaiTanggalBC40'])); ?>
                <?php else : ?>
                    ALL
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <br>


    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Supplier</th>
                <th>Tanggal</th>
                <th>No Aju</th>
                <th>No Daftar</th>
                <th>Tipe PO</th>
                <th>PPN Nilai</th>
                <th>PPN Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td class="text-center"><?= $row['no']; ?></td>
                    <td><?= $row['supplier_name']; ?></td>
                    <td class="text-center"><?= $row['date']; ?></td>
                    <td><?= $row['no_aju']; ?></td>
                    <td><?= $row['no_daftar']; ?></td>
                    <td><?= $row['po_type']; ?></td>
                    <td class="text-right"><?= number_format($row['ppn_nilai'], 2); ?></td>
                    <td class="text-center"><?= $row['ppn_status']; ?></td>
                </tr>
            <?php endforeach; ?>

            <tr class="total-row">
                <td colspan="6" class="text-center">TOTAL PPN</td>
                <td class="text-right"><?= number_format($totalPPN, 2); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>

</html>
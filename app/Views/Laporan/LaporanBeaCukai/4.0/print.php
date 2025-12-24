<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pungutan BC 4.0</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .item-table th,
        .item-table td {
            border: 1px solid;
            font-size: 12px;
            padding: 5px;
            text-align: left;
        }

        .item-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .w-100 {
            width: 100%;
        }

        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>LAPORAN PUNGUTAN BC 4.0</h2>

    <table class="w-100">
        <tbody>
            <tr>
                <td style="width:150px">Tgl Mulai / Tgl Akhir</td>
                <td style="width:10px">:</td>
                <?php if ($condition['mulaiTanggalBC40'] != "" && $condition['selesaiTanggalBC40'] != "") : ?>
                    <td style="width:80px"><?= date('d/m/Y', strtotime($condition['mulaiTanggalBC40'])); ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= date('d/m/Y', strtotime($condition['selesaiTanggalBC40'])); ?></td>
                <?php else : ?>
                    <td colspan="3">ALL</td>
                <?php endif; ?>
            </tr>
        </tbody>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Supplier</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No Aju</th>
                <th rowspan="2">No Daftar</th>
                <th rowspan="2">Tipe PO</th>
                <th colspan="3">PPN</th>
            </tr>
            <tr>
                <th>Tidak Dipungut</th>
                <th>Di Bebaskan</th>
                <th>Di Tangguhkan</th>
            </tr>
        </thead>
        <tbody>

            <?php
            // INISIALISASI TOTAL
            $totalTidakDipungut = 0;
            $totalDiBebaskan   = 0;
            $totalDiTangguhkan = 0;
            ?>

            <?php foreach ($data as $row) : ?>
                <tr>
                    <td><?= $row['no']; ?></td>
                    <td><?= $row['supplier_name']; ?></td>
                    <td><?= date('d/m/Y', strtotime($row['date'])); ?></td>
                    <td><?= $row['no_aju']; ?></td>
                    <td><?= $row['no_daftar']; ?></td>
                    <td><?= $row['po_type']; ?></td>
                    <td><?= number_format($row['dataBCTarif']['PPN']['tidak_dipungut'], 2); ?></td>
                    <td><?= number_format($row['dataBCTarif']['PPN']['di_bebaskan'], 2); ?></td>
                    <td><?= number_format($row['dataBCTarif']['PPN']['di_tangguhkan'], 2); ?></td>
                </tr>

                <?php
                // AKUMULASI TOTAL
                $totalTidakDipungut += $row['dataBCTarif']['PPN']['tidak_dipungut'];
                $totalDiBebaskan   += $row['dataBCTarif']['PPN']['di_bebaskan'];
                $totalDiTangguhkan += $row['dataBCTarif']['PPN']['di_tangguhkan'];
                ?>
            <?php endforeach; ?>

            <!-- BARIS TOTAL -->
            <tr class="total-row">
                <td colspan="6">TOTAL PPN</td>
                <td><?= number_format($totalTidakDipungut, 2); ?></td>
                <td><?= number_format($totalDiBebaskan, 2); ?></td>
                <td><?= number_format($totalDiTangguhkan, 2); ?></td>
            </tr>

            <!-- GRAND TOTAL -->
            <tr class="total-row">
                <td colspan="6">GRAND TOTAL PPN</td>
                <td colspan="3">
                    <?= number_format(
                        $totalTidakDipungut + $totalDiBebaskan + $totalDiTangguhkan,
                        2
                    ); ?>
                </td>
            </tr>

        </tbody>
    </table>
</body>

</html>
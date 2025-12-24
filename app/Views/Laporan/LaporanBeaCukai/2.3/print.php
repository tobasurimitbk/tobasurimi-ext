<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pungutan BC 2.3</title>
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
    </style>
</head>

<body>
    <h2>LAPORAN PUNGUTAN BC 2.3</h2>

    <table class="w-100">
        <tbody>
            <tr>
                <td style="width:150px">Tgl Mulai / Tgl Akhir</td>
                <td style="width:10px">:</td>
                <?php if ($condition['mulaiTanggalBC23'] != "" && $condition['selesaiTanggalBC23'] != "") : ?>
                    <td style="width:80px"><?= date('d/m/Y', strtotime($condition['mulaiTanggalBC23'])); ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= date('d/m/Y', strtotime($condition['selesaiTanggalBC23'])); ?></td>
                <?php else : ?>
                    <td colspan="3">ALL</td>
                <?php endif; ?>
            </tr>
        </tbody>
    </table>

    <?php
    // ================= TOTAL INITIAL =================
    $total = [
        'PPN' => [
            'tidak_dipungut' => 0,
            'di_bebaskan' => 0,
            'di_tangguhkan' => 0,
        ],
        'PPH' => [
            'tidak_dipungut' => 0,
            'di_bebaskan' => 0,
            'di_tangguhkan' => 0,
        ],
        'BM' => [
            'tidak_dipungut' => 0,
            'di_bebaskan' => 0,
            'di_tangguhkan' => 0,
        ],
    ];
    ?>

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
                <th colspan="3">PPH</th>
                <th colspan="3">BM</th>
            </tr>
            <tr>
                <th>Tidak Dipungut</th>
                <th>Di Bebaskan</th>
                <th>Di Tangguhkan</th>
                <th>Tidak Dipungut</th>
                <th>Di Bebaskan</th>
                <th>Di Tangguhkan</th>
                <th>Tidak Dipungut</th>
                <th>Di Bebaskan</th>
                <th>Di Tangguhkan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row) : ?>

                <?php
                // ================= AKUMULASI TOTAL =================
                foreach (['PPN', 'PPH', 'BM'] as $jenis) {
                    foreach (['tidak_dipungut', 'di_bebaskan', 'di_tangguhkan'] as $fasilitas) {
                        $total[$jenis][$fasilitas] += $row['dataBCTarif'][$jenis][$fasilitas];
                    }
                }
                ?>

                <tr>
                    <td><?= $row['no']; ?></td>
                    <td><?= $row['supplier_name']; ?></td>
                    <td><?= $row['date']; ?></td>
                    <td><?= $row['no_aju']; ?></td>
                    <td><?= $row['no_daftar']; ?></td>
                    <td><?= $row['po_type']; ?></td>

                    <td><?= number_format($row['dataBCTarif']['PPN']['tidak_dipungut'], 2); ?></td>
                    <td><?= number_format($row['dataBCTarif']['PPN']['di_bebaskan'], 2); ?></td>
                    <td><?= number_format($row['dataBCTarif']['PPN']['di_tangguhkan'], 2); ?></td>

                    <td><?= number_format($row['dataBCTarif']['PPH']['tidak_dipungut'], 2); ?></td>
                    <td><?= number_format($row['dataBCTarif']['PPH']['di_bebaskan'], 2); ?></td>
                    <td><?= number_format($row['dataBCTarif']['PPH']['di_tangguhkan'], 2); ?></td>

                    <td><?= number_format($row['dataBCTarif']['BM']['tidak_dipungut'], 2); ?></td>
                    <td><?= number_format($row['dataBCTarif']['BM']['di_bebaskan'], 2); ?></td>
                    <td><?= number_format($row['dataBCTarif']['BM']['di_tangguhkan'], 2); ?></td>
                </tr>
            <?php endforeach; ?>

            <!-- ================= TOTAL ROW ================= -->
            <tr>
                <td colspan="6" style="text-align:right; font-weight:bold;">TOTAL</td>

                <td><?= number_format($total['PPN']['tidak_dipungut'], 2); ?></td>
                <td><?= number_format($total['PPN']['di_bebaskan'], 2); ?></td>
                <td><?= number_format($total['PPN']['di_tangguhkan'], 2); ?></td>

                <td><?= number_format($total['PPH']['tidak_dipungut'], 2); ?></td>
                <td><?= number_format($total['PPH']['di_bebaskan'], 2); ?></td>
                <td><?= number_format($total['PPH']['di_tangguhkan'], 2); ?></td>

                <td><?= number_format($total['BM']['tidak_dipungut'], 2); ?></td>
                <td><?= number_format($total['BM']['di_bebaskan'], 2); ?></td>
                <td><?= number_format($total['BM']['di_tangguhkan'], 2); ?></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
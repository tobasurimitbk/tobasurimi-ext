<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap All Barang (Summary)</title>
    <style>
        .company-name {
            font-weight: 700;
            border: 1px solid;
            padding: 5px;
            border-radius: 7px;
            margin-bottom: 10px;
            display: inline-block;
            min-width: 70px
        }

        .description-container {
            border: 1px solid;
            border-radius: 7px;
            height: 65px;
            margin-top: 20px;
            width: 60%;
            position: relative;
            padding-top: 7px;
            padding-left: 17px;
        }

        .description-label {
            position: absolute;
            top: -10px;
            background: white;
            left: 15px;
            padding-left: 3px;
            padding-right: 5px;
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            height: 230px;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .item-table th {
            border-right: 1px solid;
            border-bottom: 1px solid;
            font-size: 13px;
            font-weight: normal;
            padding: 2px
        }

        .item-table td {
            border: 1px solid;
            font-size: 10px;
            padding: 2px
        }

        .signature-table {
            border-spacing: 30px 0;
            margin-top: 10px;
        }

        .txt-bold {
            font-weight: 700;
        }

        .txt-center {
            text-align: center;
        }

        .txt-right {
            text-align: right;
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body>
    <h2><?= $header; ?></h2>
    <table class="w-100">
        <tbody>
            <tr>
                <td style="width:100px">Tanggal</td>
                <td style="width:10px">:</td>
                <?php if (!empty($tanggalAwal) || !empty($tanggalAkhir)) : ?>
                    <td style="width:80px"><?= $tanggalAwal; ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= $tanggalAkhir; ?></td>
                <?php else : ?>
                    <td colspan="3" style="width:80px">ALL</td>

                <?php endif; ?>
            </tr>
        </tbody>
    </table>

    <table class="w-100 item-table">
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Barang</th>
            <th rowspan="2">Spesifikasi</th>

            <th rowspan="2">Departemen</th>
            <th rowspan="2">Qty</th>
            <th rowspan="2">Satuan</th>
            <th colspan="3">Umum</th>
            <th colspan="3">Tambahan Harian</th>
            <th colspan="3">Tambahan Bulanan</th>
            <th colspan="3">Tambahan Langsung</th>
            <th colspan="1" rowspan="2">Total</th>
        </tr>
        <tr>
            <th>DPP</th>
            <th>PPh</th>
            <th>Dibayarkan</th>

            <th>DPP</th>
            <th>PPh</th>
            <th>Dibayarkan</th>

            <th>DPP</th>
            <th>PPh</th>
            <th>Dibayarkan</th>

            <th>DPP</th>
            <th>PPh</th>
            <th>Dibayarkan</th>

        </tr>
        <?php if (!empty($dataOrder)) : ?>
            <?php foreach ($dataOrder as $do) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $do->barangName; ?></td>
                    <td><?= $do->spekName; ?></td>
                    <td><?= $do->bagianName; ?></td>
                    <td><?= $do->qtyPO; ?></td>
                    <td><?= $do->satuanName; ?></td>
                    <td><?= number_format($do->dppUmum, 2); ?></td>
                    <td><?= number_format($do->pphUmum, 2); ?></td>
                    <td><?= number_format($do->totalUmum, 2); ?></td>

                    <td><?= number_format($do->dppHarian, 2); ?></td>
                    <td><?= number_format($do->pphHarian, 2); ?></td>
                    <td><?= number_format($do->totalHarian, 2); ?></td>

                    <td><?= number_format($do->dppBulanan, 2); ?></td>
                    <td><?= number_format($do->pphBulanan, 2); ?></td>
                    <td><?= number_format($do->totalBulanan, 2); ?></td>

                    <td><?= number_format($do->subsidi, 2); ?></td>
                    <td><?= number_format($do->pphSubsidi, 2); ?></td>
                    <td><?= number_format($do->totalSubsidi, 2); ?></td>

                    <td><?= number_format($do->totalRow, 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr style="font-weight: bold;">
                <td colspan="6">Total</td>
                <td id="totalDppUmum"><?= number_format($totalDppUmum, 2); ?></td>
                <td id="totalPphUmum"><?= number_format($totalPphUmum, 2); ?></td>
                <td id="totalTotalUmum"><?= number_format($totalTotalUmum, 2); ?></td>
                <td id="totalDppHarian"><?= number_format($totalDppHarian, 2); ?></td>
                <td id="totalPphHarian"><?= number_format($totalPphHarian, 2); ?></td>
                <td id="totalTotalHarian"><?= number_format($totalTotalHarian, 2); ?></td>
                <td id="totalDppBulanan"><?= number_format($totalDppBulanan, 2); ?></td>
                <td id="totalPphBulanan"><?= number_format($totalPphBulanan, 2); ?></td>
                <td id="totalTotalBulanan"><?= number_format($totalTotalBulanan, 2); ?></td>
                <td id="totalDppSubsidi"><?= number_format($totalDppSubsidi, 2); ?></td>
                <td id="totalPphSubsidi"><?= number_format($totalPphSubsidi, 2); ?></td>
                <td id="totalTotalSubsidi"><?= number_format($totalTotalSubsidi, 2); ?></td>
                <td id="totalTotalRow"><?= number_format($totalTotalRow, 2); ?></td>
            </tr>
        <?php else : ?>
            <tr>
                <td colspan="26">Tidak ada data yang tersedia.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>

</html>
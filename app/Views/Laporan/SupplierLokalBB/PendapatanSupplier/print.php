<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                <?php if (!empty($tanggalAwal) && !empty($tanggalAkhir)) : ?>
                    <td style="width:80px"><?= $tanggalAwal; ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= $tanggalAkhir; ?></td>
                <?php else : ?>
                    <td colspan="3" style="width:80px">ALL</td>

                <?php endif; ?>
            </tr>
            <tr>
                <td>Po No</td>
                <td>:</td>
                <td colspan="3" style="text-transform: uppercase;"><?= $poNo; ?></td>
            </tr>
            <tr>
                <td>Bahan Baku</td>
                <td>:</td>
                <td colspan="3" style="text-transform: uppercase;"><?= $bahanBaku; ?></td>
            </tr>
            <tr>
                <td>Lokasi Gudang</td>
                <td>:</td>
                <td colspan="3" style="text-transform: uppercase;"><?= $warehouse; ?></td>
            </tr>
            <tr>
                <td>Supplier</td>
                <td>:</td>
                <td colspan="3" style="text-transform: uppercase;"><?= $supplier; ?></td>
            </tr>
        </tbody>
    </table>

    <table class="w-100 item-table">
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Supplier</th>
            <th rowspan="2">No PO</th>
            <th rowspan="2">Tgl PO</th>
            <th rowspan="2">Bahan Baku</th>
            <th rowspan="2">Spesifikasi</th>
            <th rowspan="2">Gudang</th>
            <th rowspan="2">Qty</th>
            <th rowspan="2">Satuan</th>
            <th rowspan="2">Unit</th>
            <th colspan="3">Harian</th>
            <th colspan="3">Tambahan Harian</th>
            <th colspan="3">Tambahan Bulanan</th>
            <th colspan="3">Subsidi</th>
            <th rowspan="2">Total</th>
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
                    <td><?= $do->supplierName; ?></td>
                    <td><?= $do->poNum; ?></td>
                    <td><?= $do->poDate; ?></td>
                    <td><?= $do->barangName; ?></td>
                    <td><?= $do->spekName; ?></td>
                    <td><?= $do->warehouseName; ?></td>
                    <td><?= $do->qtyPO; ?></td>
                    <td><?= $do->satuanName; ?></td>
                    <td><?= $do->companyName; ?></td>
                    <td><?= number_format($do->dppUmum); ?></td>
                    <td><?= number_format($do->pphUmum); ?></td>
                    <td><?= number_format($do->totalUmum); ?></td>
                    <td><?= number_format($do->dppHarian); ?></td>
                    <td><?= number_format($do->pphHarian); ?></td>
                    <td><?= number_format($do->totalHarian); ?></td>
                    <td><?= number_format($do->dppBulanan); ?></td>
                    <td><?= number_format($do->pphBulanan); ?></td>
                    <td><?= number_format($do->totalBulanan); ?></td>
                    <td><?= number_format($do->subsidi); ?></td>
                    <td><?= number_format($do->pphSubsidi); ?></td>
                    <td><?= number_format($do->totalSubsidi); ?></td>
                    <td><?= number_format($do->totalRow); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="10">Total</td>
                <td id="totalDppUmum"><?= number_format($totalDppUmum); ?></td>
                <td id="totalPphUmum"><?= number_format($totalPphUmum); ?></td>
                <td id="totalTotalUmum"><?= number_format($totalTotalUmum); ?></td>
                <td id="totalDppHarian"><?= number_format($totalDppHarian); ?></td>
                <td id="totalPphHarian"><?= number_format($totalPphHarian); ?></td>
                <td id="totalTotalHarian"><?= number_format($totalTotalHarian); ?></td>
                <td id="totalDppBulanan"><?= number_format($totalDppBulanan); ?></td>
                <td id="totalPphBulanan"><?= number_format($totalPphBulanan); ?></td>
                <td id="totalTotalBulanan"><?= number_format($totalTotalBulanan); ?></td>
                <td id="totalDppSubsidi"><?= number_format($totalDppSubsidi); ?></td>
                <td id="totalPphSubsidi"><?= number_format($totalPphSubsidi); ?></td>
                <td id="totalTotalSubsidi"><?= number_format($totalTotalSubsidi); ?></td>
                <td id="totalTotalRow"><?= number_format($totalTotalRow); ?></td>

            </tr>
        <?php else : ?>
            <tr>
                <td colspan="24">Tidak ada data yang tersedia.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap All Supplier (Pembelian)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            margin: 0;
            padding: 5px;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header h2 {
            margin: 0;
            font-size: 12pt;
        }

        .info {
            margin-bottom: 5px;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 8pt;
            padding: 4px;
        }

        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            margin: 5px 0;
            background-color: #e0e0e0;
            padding: 3px;
            font-size: 9pt;
        }

        /* Column widths */
        .col-no {
            width: 3%;
        }

        .col-supplier {
            width: 12%;
        }

        .col-divisi {
            width: 8%;
        }

        .col-barang {
            width: 10%;
        }

        .col-spek {
            width: 8%;
        }

        .col-satuan {
            width: 5%;
        }

        .col-qty {
            width: 4%;
        }

        .col-amount {
            width: 5%;
        }

        .col-total {
            width: 6%;
        }

        /* Prevent page breaks inside groups */
        .group-container {
            page-break-inside: avoid;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2><?= $header; ?></h2>
    </div>

    <div class="info">
        Tanggal:
        <?php if (!empty($tanggalAwal) && !empty($tanggalAkhir)) : ?>
            <?= $tanggalAwal; ?> s/d <?= $tanggalAkhir; ?>
        <?php else : ?>
            ALL
        <?php endif; ?>
        <br>Bahan Baku: <?= $bahanBaku; ?>
    </div>

    <div class="table-container">
        <?php foreach ($groupedData as $barangName => $groups) : ?>
            <div class="group-container">
                <div class="section-title">Bahan Baku: <?= $barangName; ?></div>
                <table>
                    <thead>
                        <tr>
                            <th class="col-no" rowspan="2">No</th>
                            <th class="col-supplier" rowspan="2">Supplier</th>
                            <th class="col-divisi" rowspan="2">Divisi</th>
                            <th class="col-barang" rowspan="2">Barang</th>
                            <th class="col-spek" rowspan="2">Spek</th>
                            <th class="col-satuan" rowspan="2">Satuan</th>
                            <th class="col-qty" rowspan="2">QTY</th>
                            <th class="col-amount">Tambahan Bulanan</th>
                            <th class="col-total" rowspan="2">TOTAL</th>
                        </tr>
                        <tr>
                            <th class="col-amount">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subTotal = [
                            'qtyPO' => 0,
                            'totalBulanan' => 0,
                            'totalRow' => 0,
                        ];
                        ?>

                        <?php foreach ($groups as $group) : ?>
                            <tr>
                                <td class="col-no"><?= $group['no']; ?></td>
                                <td class="col-supplier text-left"><?= $group['supplierName']; ?></td>
                                <td class="col-divisi text-left"><?= $group['divisiName']; ?></td>
                                <td class="col-barang text-left"><?= $group['barangName']; ?></td>
                                <td class="col-spek text-left"><?= $group['spekName']; ?></td>
                                <td class="col-satuan"><?= $group['satuanName']; ?></td>
                                <td class="col-qty"><?= number_format($group['qtyPO'], 0); ?></td>

                                <td class="col-amount text-right"><?= number_format($group['totalBulanan'], 2); ?></td>

                                <td class="col-total text-right"><?= number_format($group['totalRow'], 2); ?></td>
                            </tr>

                            <?php
                            $subTotal['qtyPO'] += $group['qtyPO'];
                            $subTotal['totalBulanan'] += $group['totalBulanan'];
                            $subTotal['totalRow'] += $group['totalRow'];
                            ?>
                        <?php endforeach; ?>

                        <!-- Sub Total per Bahan Baku -->
                        <tr class="total-row">
                            <td class="text-right" colspan="6">TOTAL <?= $barangName; ?></td>
                            <td class="col-qty"><?= number_format($subTotal['qtyPO'], 0); ?></td>

                            <td class="col-amount text-right"><?= number_format($subTotal['totalBulanan'], 2); ?></td>

                            <td class="col-total text-right"><?= number_format($subTotal['totalRow'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>
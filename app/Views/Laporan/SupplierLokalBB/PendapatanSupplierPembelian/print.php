<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $header; ?></title>
    <style>
        @page {
            margin: 15px 20px;
        }

        body {
            font-size: 10px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0px;
        }

        h2 {
            margin-bottom: 5px;
            text-align: center;
        }

        .info {
            margin-bottom: 10px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 8px;
            page-break-inside: auto;
            page-break-after: auto;
            page-break-before: auto;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            word-wrap: break-word;
            page-break-inside: avoid !important;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-left {
            text-align: left;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        .group-title {
            margin-top: 25px;
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 11px;
            page-break-before: auto;
        }

        .col-no {
            width: 25px;
        }

        .col-supplier {
            width: 110px;
        }

        .col-nopo {
            width: 70px;
        }

        .col-date {
            width: 60px;
        }

        .col-dept {
            width: 100px;
        }

        .col-gudang {
            width: 80px;
        }

        .col-qty,
        .col-satuan,
        .col-unit {
            width: 45px;
        }

        .col-group {
            width: 55px;
        }
    </style>
</head>

<body>
    <h2><?= $header; ?></h2>
    <div class="info">
        Tanggal:
        <?php if (!empty($tanggalAwal) && !empty($tanggalAkhir)) : ?>
            <?= $tanggalAwal; ?> s/d <?= $tanggalAkhir; ?>
        <?php else : ?>
            ALL
        <?php endif; ?>
    </div>

    <?php if (!empty($groupedData)) : ?>
        <?php foreach ($groupedData as $barangName => $group): ?>
            <div class="group-title">Bahan Baku: <?= $barangName; ?></div>

            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="col-no">No.</th>
                        <th rowspan="2" class="col-supplier">Supplier</th>
                        <th rowspan="2" class="col-nopo">No PO</th>
                        <th rowspan="2" class="col-date">Tgl PO</th>
                        <th rowspan="2" class="col-dept">Department</th>
                        <th rowspan="2" class="col-gudang">Gudang</th>
                        <th rowspan="2" class="col-qty">Qty</th>
                        <th rowspan="2" class="col-satuan">Satuan</th>
                        <th class="col-group">Tambahan Bulanan</th>
                        <th rowspan="2" class="col-group">Total</th>
                    </tr>
                    <tr>
                        <th class="col-group">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($group['data'] as $row): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td class="text-left"><?= $row['supplierName']; ?></td>
                            <td><?= $row['poNum']; ?></td>
                            <td><?= $row['poDate']; ?></td>
                            <td class="text-left"><?= $row['divisiName']; ?></td>
                            <td class="text-left"><?= $row['warehouseName']; ?></td>
                            <td><?= number_format($row['qtyPO'], 2); ?></td>
                            <td><?= $row['satuanName']; ?></td>

                            <td><?= number_format($row['totalBulanan'], 2); ?></td>

                            <td><?= number_format($row['totalRow'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr style="font-weight: bold; background-color: #eee;">
                        <td colspan="6">TOTAL <?= strtoupper($barangName); ?></td>
                        <td><?= number_format($group['summary']['totalQtyPO'], 2); ?></td>
                        <td></td>

                        <td><?= number_format($group['summary']['totalTotalBulanan'], 2); ?></td>

                        <td><?= number_format($group['summary']['totalTotalRow'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Tidak ada data yang tersedia.</p>
    <?php endif; ?>
</body>

</html>
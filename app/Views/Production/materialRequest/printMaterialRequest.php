<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Material Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 10px;
        }

        h5 {
            font-weight: normal;
            font-size: 12px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
            margin-top: 8px;
        }

        h6 {
            font-weight: normal;
            font-size: 11px;
            text-align: left;
            font-weight: bold;
            margin-top: 10px;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
        }

        @page {
            size: 7.44in 10.00in landscape;
            margin: 29px;
            padding: 29px;
        }

        #dashed-border-table {
            border-collapse: collapse;
        }

        #dashed-border-table th,
        #dashed-border-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h5>
        Data Material Request
    </h5>

    <table width="100%" border="1" id="dashed-border-table" style="">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Supplier</th>
                <th colspan="<?= $banyakHeader ?>">Spesifikasi</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <?php foreach ($dataHeader as $h): ?>
                    <th><?= $h->spesifikasi ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;
            $counterPivotSupplier = count($pivotSupplier);
            ?>
            <?php foreach ($pivotSupplier as $supplier): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td style="text-align:left"><?= $supplier['supplier_name'] ?></td>

                    <?php foreach ($dataHeader as $h): ?>
                        <td style="text-align:center">
                            <?= $supplier['specs'][$h->barang2_id] ?? 0 ?>
                        </td>
                    <?php endforeach; ?>

                    <td style="text-align:center"><?= $supplier['total'] ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($counterPivotSupplier > 0): ?>
                <tr>
                    <td></td>
                    <td style="text-align:right; font-weight:bold;">TOTAL</td>

                    <?php foreach ($dataHeader as $h): ?>
                        <td style="text-align:center; font-weight:bold;">
                            <?= $footerTotalSupplier[$h->barang2_id] ?? 0 ?>
                        </td>
                    <?php endforeach; ?>

                    <td style="text-align:center; font-weight:bold;"><?= $footerGrandTotalSupplier; ?></td>
                </tr>
            <?php endif; ?>

            <?php
            $noVendor = 1;
            $counterPivotJasaVendor = count($pivotJasaVendor);
            ?>
            <?php foreach ($pivotJasaVendor as $jasavendor): ?>
                <tr>
                    <td><?= $noVendor++ ?></td>
                    <td style="text-align:left"><?= $jasavendor['keterangan_full'] ?? $jasavendor['vendor_name'] ?></td>

                    <?php foreach ($dataHeader as $h): ?>
                        <td style="text-align:center">
                            <?= $jasavendor['specs'][$h->barang2_id] ?? 0 ?>
                        </td>
                    <?php endforeach; ?>

                    <td style="text-align:center"><?= $jasavendor['total'] ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($counterPivotJasaVendor > 0): ?>
                <tr>
                    <td></td>
                    <td style="text-align:right; font-weight:bold;">TOTAL</td>

                    <?php foreach ($dataHeader as $h): ?>
                        <td style="text-align:center; font-weight:bold;">
                            <?= $footerTotalJasaVendor[$h->barang2_id] ?? 0 ?>
                        </td>
                    <?php endforeach; ?>

                    <td style="text-align:center; font-weight:bold;"><?= $footerGrandTotalJasaVendor; ?></td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>
</body>

</html>
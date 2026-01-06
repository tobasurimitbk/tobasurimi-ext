<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Material Request Canning</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 6px;
        }

        h5 {
            font-weight: normal;
            font-size: 8px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
            margin-top: 8px;
        }

        h6 {
            font-weight: normal;
            font-size: 7px;
            text-align: left;
            font-weight: bold;
            margin-top: 10px;
        }

        hr {
            border: none;
            border-top: 0.5px dashed #000;
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
            border: 0.5px solid #000;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h5>
        Data Material Request
    </h5>

    <table width="100%" border="1" id="dashed-border-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Size</th>
                <th width="20%">Kopek (Vendor)</th>
                <th width="35%">Supplier</th>
                <th width="10%">Total</th>
            </tr>
        </thead>
        <tbody>

        <?php $no = 1; ?>
        <?php foreach ($pivotJasaVendor as $size): ?>

            <?php
            // HITUNG TOTAL BARIS DALAM 1 SIZE
            $rowspan = 0;
            foreach ($size['vendors'] as $vendor) {
                $rowspan += count($vendor['suppliers']);
            }

            $isFirstRow = true;
            ?>

            <?php foreach ($size['vendors'] as $vendor): ?>
                <?php foreach ($vendor['suppliers'] as $supplier): ?>
                    <tr>
                        <?php if ($isFirstRow): ?>
                            <td rowspan="<?= $rowspan ?>" style="text-align:center; vertical-align:middle;">
                                <?= $no++ ?>
                            </td>
                            <td rowspan="<?= $rowspan ?>" style="vertical-align:middle;">
                                <?= $size['size_name'] ?>
                            </td>
                            <?php $isFirstRow = false; ?>
                        <?php endif; ?>

                        <td><?= $vendor['vendor_name'] ?></td>
                        <td><?= $supplier['supplier_label'] ?></td>
                        <td style="text-align:center"><?= $supplier['total'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>

        <?php endforeach; ?>

        </tbody>
    </table>

</body>

</html>
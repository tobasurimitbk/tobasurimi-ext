<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Purchase Order</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        /* @page {
            size: 7.27in 5.50in landscape;
            margin: 25px;
            padding: 25px;
        } */

        .body {
            margin-left: 30px;
            margin-right: 30px;
        }

        .vendor-detail {
            font-weight: bold;
            font-size: 14px;
        }

        .head-table {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        .sub-head-table {
            margin-top: 5px;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
            border: 1px solid black;
        }

        .table th,
        .table td {
            padding: 0.25rem;
            vertical-align: top;
            border-top: 1px solid black;
            border-right: 1px solid black;
        }

        .table th:last-child,
        .table td:last-child {
            border-right: none;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid black;
        }
    </style>
</head>

<body>
    <div class="body">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: center;">
                    <h2>
                        <u>
                            LAPORAN PURCHASE ORDER
                        </u>
                        <br>
                    </h2>
                    <h4 style="margin-top: -10px;">
                        SUPPLIER : <?= strtoupper($supplier['name']) ?>
                    </h4>
                </td>

            </tr>
        </table>
        <table class="table" style="margin-top: 30px;">
            <thead>
                <tr style="text-align: center; font-weight:bold;">
                    <td>
                        No. PO
                    </td>
                    <td>
                        Tgl PO
                    </td>
                    <td>
                        Supplier
                    </td>
                    <td>
                        Kode
                    </td>
                    <td>
                        Barang
                    </td>
                    <td>
                        Jumlah
                    </td>
                    <td>Total Harga</td>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($result as $r) : ?>
                    <tr style="text-align: center; font-weight:bold;">
                        <td><?= $r['po_no'] ?></td>
                        <td><?= $r['po_date'] ?></td>
                        <td><?= $supplier['name'] ?></td>
                        <td><?= $r['kode_barang'] ?></td>
                        <td><?= $r['barang_name'] ?></td>
                        <td><?= $r['qty_lpb'] ?></td>
                        <td><?= $r['harga'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</body>

</html>
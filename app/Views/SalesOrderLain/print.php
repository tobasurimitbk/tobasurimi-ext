<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form Lain</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 9.27in 5.50in landscape;
            margin: 25px;
            padding: 25px;
        }

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
    <?php if (!empty($salesOrderLain)) : ?>
        <div class="body">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 45%;">
                        <h2><?= $company['company'] ?></h2> <br>
                    </td>
                    <td style="text-align: right;">
                        <h2>
                            <u>
                                ORDER FORM / INVOICE
                            </u>
                        </h2>
                    </td>
                </tr>
            </table>
            <table style="width: 100%; margin-top:-20px;">
                <tr>
                    <td style="width: 45%;">
                        <div class="vendor-detail">
                            Kepada Yth. <br>
                            <?= $salesOrderLain['customer_name'] ?>
                        </div>
                    </td>
                    <td style="text-align: right;">

                    </td>
                </tr>
            </table>


            <table class="table" style="margin-top: 30px;">
                <thead>
                    <tr>
                        <td class="head-table">
                            No
                        </td>
                        <td class="head-table">
                            No Dokumen
                        </td>
                        <td class="head-table">
                            Supplier
                        </td>
                        <td class="head-table">
                            Barang
                        </td>
                        <td class="head-table">
                            Qty Order
                        </td>
                        <td class="head-table">
                            Harga Satuan
                        </td>
                        <td class="head-table">
                            Potongan Harga
                        </td>
                        <td class="head-table">
                            Biaya Tambahan
                        </td>
                        <td class="head-table">
                            Total Harga
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    $totalHarga = 0; ?>
                    <?php foreach ($salesOrderLainDetail as $s) : ?>
                        <?php $totalHarga += $s['total_harga'] ?>
                        <tr style="text-align: center;">
                            <td>
                                <?= $i++ ?>
                            </td>
                            <td>
                                <?= $s['stock_dokumen'] ?>
                            </td>
                            <td>
                                <?= $s['supplier_name'] ?>
                            </td>
                            <td>
                                <?= $s['barang'] ?>
                            </td>
                            <td>
                                <?= $s['qty_order'] . " " . $s['satuan_order_text'] ?>
                            </td>
                            <td>
                                <?= number_format($s['harga_satuan'], 2) ?>
                            </td>
                            <td>
                                <?= number_format($s['potongan_harga'], 2) ?>
                            </td>
                            <td>
                                <?= number_format($s['biaya_tambahan'], 2) ?>
                            </td>
                            <td>
                                <?= number_format($s['total_harga'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="8" style="text-align: right;">TOTAL HARGA</td>
                        <td style="text-align: center;"><?= number_format($totalHarga, 2) ?></td>
                    </tr>
                </tbody>
            </table>
            <br><br><br>
            <table style="width: 130%;margin-top:5px;">
                <tr>
                    <td>
                        <b>Diketahui Oleh</b>
                    </td>
                    <td>
                        <b>Barang Sudah Diterima</b>
                    </td>
                    <td>
                        <b>Yang Menyerahkan</b>
                    </td>
                </tr>
            </table>
        </div>

    <?php endif; ?>
</body>

</html>
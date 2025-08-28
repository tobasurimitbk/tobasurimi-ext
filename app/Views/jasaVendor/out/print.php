<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasa Vendor Barang Keluar</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 8.27in 5.50in landscape;
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
    <?php if (!empty($jasaVendorOut)) : ?>
        <div class="body">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 45%;">
                        <h2>PT. TOBA SURIMI INDUSTRIES</h2> <br>
                    </td>
                    <td style="text-align: right;">
                        <h2>
                            <u>
                                NOTA JALAN / DELIVERY NOTE
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
                            <?= $jasaVendorOut['vendor_name'] ?>, <?= $jasaVendorOut['address'] ?>
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <div class="vendor-detail">
                            No Surat Jalan: <?= $jasaVendorOut['no_surat_jalan'] ?>
                        </div>
                    </td>
                </tr>
            </table>


            <table class="table" style="margin-top: 30px;">
                <thead>
                    <tr>
                        <td class="head-table">
                            Tanggal <br>
                            <div class="sub-head-table">
                                <?= date('d/m/Y', strtotime($jasaVendorOut['tanggal'])) ?>
                            </div>
                        </td>
                        <td class="head-table">
                            Mobil BK
                        </td>
                        <td class="head-table">
                            Nama Kapal
                        </td>
                        <td class="head-table">
                            No. Kontainer <br>
                            <div class="sub-head-table">
                                <?= $jasaVendorOut['no_kontainer'] ?>
                            </div>
                        </td>
                        <td class="head-table">

                        </td>
                        <td class="head-table">

                        </td>
                    </tr>
                    <tr style="text-align: center; font-weight:bold;">
                        <!-- <td>
                            Banyaknya
                        </td> -->
                        <td colspan="4">
                            Keterangan / Uraian Nama Barang
                        </td>
                        <td>
                            Berat Satuan
                        </td>
                        <td>
                            Qty
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jasaVendorDetail as $j) : ?>
                        <tr style="text-align: center;">
                            <!-- <td>

                            </td> -->
                            <td colspan="4">
                                <?= $j['barang'] ?>
                            </td>
                            <td>
                                <?= number_format($j['qty'], 3) . " " . $j['satuan'] ?>
                            </td>
                            <td>
                                <?= number_format($j['qty'], 3) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
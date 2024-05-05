<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasa Vendor Barang Masuk</title>
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
    <?php if (!empty($jasaVendorIn)) : ?>
        <div class="body">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center;">
                        <h2>
                            <u>
                                LAPORAN TIMBANGAN BAHAN BAKU
                            </u>
                            <br>
                        </h2>
                        <h4 style="margin-top: -10px;">
                            NO : <?= $jasaVendorIn['no_penerimaan_surat_jalan'] ?>
                        </h4>
                    </td>

                </tr>
            </table>

            <table>
                <tr>
                    <td>NAMA VENDOR</td>
                    <td>:</td>
                    <td><?= $vendor == null ? "-" : strtoupper($vendor['name']) ?></td>
                </tr>
                <tr>
                    <td>TANGGAL</td>
                    <td>:</td>
                    <td><?= date('d/m/Y', strtotime($jasaVendorIn['tanggal'])) ?></td>
                </tr>
                <tr>
                    <td>KETERANGAN</td>
                    <td>:</td>
                    <td><?= $jasaVendorIn['keterangan'] ?></td>
                </tr>
            </table>

            <table class="table" style="margin-top: 30px;">
                <thead>
                    <tr style="text-align: center; font-weight:bold;">
                        <td>
                            Barang - Spesifikasi
                        </td>
                        <td>
                            Satuan
                        </td>
                        <td>
                            Qty Kotor
                        </td>
                        <td>
                            Qty Bersih
                        </td>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($jasaVendorInDetail as $j) : ?>
                        <tr style="text-align: center; font-weight:bold;">
                            <td><?= strtoupper($j['barang_name']) . "-" . strtoupper($j['spesifikasi']) ?></td>
                            <td><?= $j['kode_satuan'] ?></td>
                            <td><?= number_format($j['qty_kotor'], 2) ?></td>
                            <td><?= number_format($j['qty_bersih'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


            <br><br><br>
            <table style="width: 100%;margin-top:20px;">
                <tr>
                    <td style="text-align: center;">
                        <b>DIPERIKSA OLEH</b>
                    </td>
                    <td style="text-align: center;">
                        <b>DIKETAHUI OLEH</b>
                    </td>
                    <td style="text-align: center;">
                        <b>DITIMBANG OLEH</b>
                    </td>
                </tr>
            </table>
        </div>

    <?php endif; ?>
</body>

</html>
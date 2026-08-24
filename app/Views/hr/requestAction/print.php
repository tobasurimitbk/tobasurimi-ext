<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #000;
        }

        .page {
            width: 100%;
            height: 194mm;
        }

        .form {
            height: 62mm;
            position: relative;
        }

        /* Garis pemisah antar form */
        .separator {
            height: 3mm;
            border-bottom: 1px dashed #555;
            margin-bottom: 2mm;
        }

        .header {
            text-align: center;
            margin-bottom: 2mm;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 1mm;
        }

        .number {
            font-size: 11px;
            font-weight: bold;
        }

        .info {
            width: 100%;
            margin-bottom: 2mm;
        }

        .info td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .tanggal-label {
            width: 45mm;
        }

        .tanggal-value {
            width: 50mm;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
        }

        .main-table th {
            height: 7mm;
            font-size: 10px;
            text-align: center;
            vertical-align: middle;
        }

        .main-table td {
            height: 25mm;
            vertical-align: top;
            padding: 2mm;
        }

        .nama {
            width: 33.33%;
        }

        .pekerjaan {
            width: 33.33%;
        }

        .keterangan {
            width: 33.34%;
        }

        .approval {
            width: 100%;
            margin-top: 2mm;
            border-collapse: collapse;
        }

        .approval td {
            border: none;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }

        .approval-title {
            font-size: 9px;
            margin-bottom: 5mm;
        }

        .signature {
            display: inline-block;
            width: 35mm;
            border-bottom: 1px solid #000;
            height: 5mm;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <?php
    // 1 halaman = 3 nomor
    $chunks = array_chunk($nomorList, 3);

    foreach ($chunks as $pageIndex => $forms):
        ?>

        <div class="page">

            <?php foreach ($forms as $index => $row): ?>

                <div class="form">

                    <div class="header">
                        <div class="title">
                            <?= $row['judul_form'] ?>
                        </div>

                        <div class="number">
                            NO :
                            <?= esc($row['nomor'] ?? '') ?>
                        </div>
                    </div>

                    <table class="info">
                        <tr>
                            <td class="tanggal-label" style="width:30px;">
                                <strong>Tanggal</strong>
                            </td>
                            <td>
                                :
                            </td>
                            <td class="tanggal-value">

                            </td>
                        </tr>
                    </table>

                    <table class="main-table">
                        <thead>
                            <tr>
                                <th class="nama">Nama</th>
                                <th class="pekerjaan">Pekerjaan</th>
                                <th class="keterangan">Keterangan</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="approval">
                        <tr>
                            <td>
                                <div class="approval-title">
                                    Dibuat Oleh,
                                </div>

                                <div class="signature"></div>
                            </td>

                            <td>
                                <div class="approval-title">
                                    Diketahui Oleh,
                                </div>

                                <div class="signature"></div>
                            </td>

                            <td>
                                <div class="approval-title">
                                    Disetujui oleh,
                                </div>

                                <div class="signature"></div>
                            </td>
                        </tr>
                    </table>

                </div>

                <?php if ($index < count($forms) - 1): ?>
                    <div class="separator" style="margin-top:30px;"></div>
                <?php endif; ?>

            <?php endforeach; ?>

        </div>

        <?php if ($pageIndex < count($chunks) - 1): ?>
            <div class="page-break"></div>
        <?php endif; ?>

    <?php endforeach; ?>

</body>

</html>
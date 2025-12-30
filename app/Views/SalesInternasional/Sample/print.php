<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dataSample['no_sample'] ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
        }

        .label-header {
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div style="margin-top: -20px;" class="label-header">
        <?= strtoupper(str_ireplace(', Tbk', '', $company['holding_company'])) . " (" . $company['company'] . ")" ?>
        <br><br>
        RESI PERMINTAAN BARANG <br>
        <table style="margin-left: -2.5px;">
            <tr>
                <td>
                    TANGGAL
                </td>
                <td>
                    :
                </td>
                <td>
                    <?= strtoupper(date('F d, Y', strtotime($dataSample['tanggal']))); ?>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px;">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 10px; height:2%;">NO</th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    DEPT
                </th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    PRODUCTS
                </th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    QTY
                </th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    UNIT
                </th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    SPECIFICATIONS
                </th>
                <?php if ($note['isNote']): ?>
                    <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                        NOTE
                    </th>
                <?php endif; ?>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;width:300px;">
                    DIKIRIM KE
                </th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd; width:50px;">
                    A/N
                </th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;width:50px;">
                    TGL
                </th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;width:50px;">
                    VIA
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $kodeSatuan = ""
            ?>
            <?php foreach ($dataBarangList as $i => $d): ?>
                <tr>
                    <td style="padding: 3px; border: 1px solid #ddd;"><?= $no++ ?></td>
                    <td style="padding: 3px; border: 1px solid #ddd;text-align: left;">
                        <?= $d['divisi_barang_text'] ?>
                    </td>
                    <td style="padding: 3px; border: 1px solid #ddd;text-align: left;">
                        <?= $d['barang'] ?> <br>
                        <?= $d['note'] ?>
                    </td>
                    <td style="padding: 3px; border: 1px solid #ddd; text-align: left;">
                        <?= number_format($d['qty'], 2) ?>
                    </td>
                    <td style="padding: 3px; border: 1px solid #ddd; text-align: left;">
                        <?= $d['kode_satuan'] ?>
                    </td>
                    <td style="padding: 3px; border: 1px solid #ddd; text-align: left;">
                        <b>
                            <?= $d['grade']  ?>
                        </b>
                    </td>
                    <?php if ($note['isNote']): ?>
                        <td style="padding: 3px; border: 1px solid #ddd; text-align: left;">
                            <?= $d['note'] ?>
                        </td>
                    <?php endif; ?>
                    <?php if ($i == 0): ?>
                        <td style="padding:3px; border:1px solid #ddd; text-align:left; vertical-align: top;"
                            rowspan="<?= count($dataBarangList) ?>">
                            <b style="display:block;">
                                <u><?= $dataSample['recipient_details'] ?></u>
                            </b>
                            <b><?= $dataSample['delivery_address'] ?></b><br>
                            <?php if (!empty($dataSample['attn_no'])): ?>
                                <b>Attn To : <?= $dataSample['attn_no'] ?></b><br>
                            <?php endif; ?>
                            <?php if (!empty($dataSample['nb'])): ?>
                                <b>NB : <?= $dataSample['nb'] ?></b>
                            <?php endif; ?>
                        </td>

                        <td style="padding:3px; border:1px solid #ddd; text-align:left; vertical-align: top;"
                            rowspan="<?= count($dataBarangList) ?>">
                            <?= $d['an'] ?>
                        </td>

                        <td style="padding:3px; border:1px solid #ddd; text-align:left; vertical-align: top;"
                            rowspan="<?= count($dataBarangList) ?>">
                            <?= $d['pickup_date'] ?>
                        </td>

                        <td style="padding:3px; border:1px solid #ddd; text-align:left; vertical-align: top;"
                            rowspan="<?= count($dataBarangList) ?>">
                            <?= $d['via'] ?>
                        </td>
                    <?php endif; ?>

                </tr>
            <?php endforeach; ?>
            <?php if ($dataSample['total_berat_bersih'] != 0 && !empty($dataSample['total_berat_bersih'])): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;" colspan="<?= $note['totalCols'] ?>">
                    <td colspan="2" style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                        TOTAL NET WEIGHT (KG)
                    </td>
                    <td style="padding: 6px; border: 1px solid #ddd; " colspan="<?= $note['totalColsBeratBersih'] ?>">
                        <?= number_format($dataSample['total_berat_bersih'], 2) ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ($dataSample['total_berat_kotor'] != 0 && !empty($dataSample['total_berat_kotor'])): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;" colspan="<?= $note['totalCols'] ?>">
                    <td colspan="2" style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                        TOTAL GROSS WEIGHT (KG)
                    </td>
                    <td style="padding: 6px; border: 1px solid #ddd; " colspan="<?= $note['totalColsBeratBersih'] ?>">
                        <?= number_format($dataSample['total_berat_kotor'], 2) ?>
                    </td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>
    <table border="1" style="width: 100%; border: 1px solid black; border-collapse: collapse;" class="label">
        <tbody>
            <tr>
                <td>
                    <b>
                        NOTES
                    </b><br>
                    <hr>
                    <?= $dataSample['description_notes'] ?>
                    <!-- <table border="0" style="width: 100%;">
                        <tr>
                            <td>
                                <b>
                                    <i>
                                        Delivery Address/CNEE/Notify Party
                                    </i> <br>
                                    <?= $dataSample['delivery_address'] ?>

                                </b>
                            </td>

                        </tr>
                    </table> -->
                </td>
            </tr>

            <tr>
                <td style=" padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;" class="label">
                        <tr>
                            <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;text-align:center;">
                                DIMINTA OLEH
                            </td>
                            <td style="width: 16.66%; border: 1px solid black; border-left: none; border-top: none; border-bottom: none; text-align:center;">
                                DISETUJUI OLEH
                            </td>

                            <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;text-align:center;">
                                DIBUAT OLEH
                            </td>
                            <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;text-align:center;">
                                DIKETAHUI OLEH
                            </td>
                        </tr>
                    </table>
                </td>

            </tr>
            <tr>
                <td style="border: none; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;" class="label">
                        <tr>

                            <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                <br><br><br><br>
                                <center>
                                    ( MARKETING )
                                </center>
                            </td>
                            <td style="width: 16.66%; border: 1px solid black; border-left: none; border-top: none; border-bottom: none;">
                                <br><br><br><br>
                                <center>
                                    ( <?= $dataSample['approved_by'] ?> )
                                </center>
                            </td>
                            <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                <br><br><br><br>
                                <center>
                                    ( QC <?= $company['company'] ?> )
                                </center>
                            </td>
                            <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                <br><br><br><br>
                                <center>
                                    ( EXIM )
                                </center>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </tbody>
    </table>


</body>

</html>
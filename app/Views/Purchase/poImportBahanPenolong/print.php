<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Import Bahan Penolong</title>
    <style>
        @media print {
            body {
                margin: 0;
            }

            @page {
                size: 210mm 330mm;
                margin: 20mm;
            }
        }

        .content {
            font-size: 13px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        hr {
            border: none;
            border-top: 4px solid #000;
            margin: 1em 0;
        }

        /* Style untuk membuat tabel dengan border Bootstrap 4 */
        .table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f8f9fa;
        }

        /* Style untuk membuat tabel striped (baris bergantian warna) */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Style untuk membuat tabel hover (warna berubah saat dihover) */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }
    </style>
</head>

<body>
    <table border="0" style="width: 100%; margin-top:-20px;">
        <tr style="vertical-align: top;">
            <?php if ($company['id'] != 15): ?>
                <td>
                    <?php if ($company['id'] == 2): ?>
                        <div style="text-align: right; margin-left:-20px; margin-right:40px; ">
                            <img src="<?= $company['logo'] ?>" style="width: 140px; height:100px; text-align:right; margin-top:-17px" alt="">
                        </div>
                    <?php else : ?>
                        <div style="text-align: right; margin-left:-20px; margin-right:40px; ">
                            <img src="<?= $company['logo'] ?>" style="width: 190px; text-align:right; margin-top:-17px" alt="">
                        </div>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
            <?php if ($company['id'] == 1 || $company['id'] == 2): ?>
                <td>
                    <?php if ($company['id'] == 2): ?>
                        <h1 style="margin-top:-10px; margin-left:-30px; font-size:37px;">

                            <b><?= strtoupper(str_ireplace(', Tbk', '', $company['holding_company'])) ?></b> <br>
                        </h1>
                        <table style="width: 100%; margin-top:-15px; margin-left:-30px; font-size:12px;">

                            <tr style="vertical-align: top;">
                                <td style="width: 50px;">Office</td>
                                <td>:</td>
                                <td>
                                    <?= $company['office_kop'] ?>
                                </td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>Factory</td>
                                <td>:</td>
                                <td><b><?= $company['factory'] ?></b></td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <h1 style="margin-top:-10px; margin-left:-30px; font-size:35px;">

                            <b><?= strtoupper(str_ireplace(', Tbk', '', $company['holding_company'])) ?></b> <br>
                            <!-- Ini Kim 1 Yha -->
                            <b>
                                <center style="text-align: center; font-size: 30px; margin-left:-40px;">
                                    PLANT I
                                </center>
                            </b>

                        </h1>
                        <table style="width: 100%; margin-top:-15px; margin-left:-30px; font-size:12px;">

                            <tr style="vertical-align: top;">
                                <td style="width: 50px;">Office</td>
                                <td>:</td>
                                <td>
                                    <?= $company['office_kop'] ?>
                                </td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>Factory</td>
                                <td>:</td>
                                <td><b><?= $company['factory'] ?></b></td>
                            </tr>
                        </table>
                    <?php endif; ?>
                </td>
            <?php endif ?>
            <?php if ($company['id'] == 15): ?>
                <td style="text-align: center;">
                    <h1 style="margin-top: -10px;">
                        <b><?= strtoupper($company['holding_company']) ?></b>
                    </h1>
                    <table style="width: 100%; margin-top: -15px; font-size: 13px;">
                        <tr>
                            <td style="text-align: center;">
                                <?= $company['factory'] ?>
                            </td>
                        </tr>
                    </table>
                </td>

            <?php endif; ?>

            <?php if ($company['id'] == 16): ?>
                <td style="text-align: left;">
                    <h1 style="margin-top: -10px;">
                        <b><?= strtoupper($company['holding_company']) ?></b>
                    </h1>

                    <?php
                    function spacedTextPreserveHTML($html)
                    {
                        return preg_replace_callback('/(<[^>]+>)|([^<]+)/u', function ($matches) {
                            if (!empty($matches[1])) {
                                // Jika ini adalah HTML tag, kembalikan tanpa perubahan
                                return $matches[1];
                            } else {
                                // Proses teks biasa
                                $text = $matches[2];
                                $result = '';
                                $length = mb_strlen($text, 'UTF-8');

                                for ($i = 0; $i < $length; $i++) {
                                    $char = mb_substr($text, $i, 1, 'UTF-8');

                                    if ($char === ' ') {
                                        $result .= ''; // 2 spasi untuk spasi asli
                                    } else {
                                        $result .= $char . ' '; // tambahkan spasi setelah setiap huruf
                                    }
                                }

                                return rtrim($result); // Hapus spasi ekstra di akhir
                            }
                        }, $html);
                    }



                    // Bersihkan karakter aneh
                    $factoryText = preg_replace('/[^\P{C}?]+/u', '', $company['factory']);

                    // Proses dengan aman
                    $factoryTextWithSpacing = spacedTextPreserveHTML($factoryText);
                    ?>

                    <table style="width: 100%; margin-top: -28px; font-size: 14px;">
                        <tr>
                            <td style="text-align: justify;">
                                <table style="font-size: 14px;">
                                    <tr style="vertical-align: top;">
                                        <td style="width: 60px;">Address</td>
                                        <td style="width: 10px;">:</td>
                                        <td style="letter-spacing: 1;">
                                            <?= $company['address'] ?>
                                        </td>
                                    </tr>
                                    <tr style="vertical-align: top;">
                                        <td>Telp</td>
                                        <td>:</td>
                                        <td style="letter-spacing: 1;">
                                            <?= $company['phone'] ?>
                                        </td>
                                    </tr>
                                    <tr style="vertical-align: top;">
                                        <td>Fax</td>
                                        <td>:</td>
                                        <td style="letter-spacing: 1;">
                                            <?= $company['fax'] ?>
                                        </td>
                                    </tr>
                                    <tr style="vertical-align: top;">
                                        <td>Email</td>
                                        <td>:</td>
                                        <td style="letter-spacing: 1;">
                                            <?= $company['email'] ?>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>
                </td>
            <?php endif; ?>
        </tr>
    </table>
    <hr style="margin-top: -1px;">

    <div class="content" style="margin-top: -15px; font-size:12px;">

        <h2 style="text-align: center;">
            PURCHASE ORDER
        </h2>

        <table style="width: 100%;">
            <tr>
                <td>
                    <b><u>SELLER/SHIPPER :</u></b>
                </td>
                <td>PO NO : <?= $dataPO->po_no ?></td>
            </tr>
            <tr>
                <td style="width: 400px;">
                    <?= $dataPO->supplierName ?>, <br>
                    <?= $dataPO->supplierAddress == '' ? '-<br>-' : $dataPO->supplierAddress ?>
                </td>
                <td>
                    <div style="margin-top: -22px;">
                        DATE OF ISSUE: <?= date('F d, Y', strtotime($dataPO->po_date)) ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%; margin-left:-3px">
                        <tr>
                            <td style="width: 40px;">Tel</td>
                            <td style="width: 10px;">:</td>
                            <td><?= $dataPO->supplierPhone ?></td>
                        </tr>
                        <tr>
                            <td>Fax</td>
                            <td>:</td>
                            <td><?= $dataPO->supplierFax ?></td>
                        </tr>
                        <tr>
                            <td>ATTN</td>
                            <td>:</td>
                            <td><?= $dataPO->attn ?></td>
                        </tr>
                        <tr>
                            <td>ORIGIN</td>
                            <td>:</td>
                            <td><?= $dataPO->port_origin == "-" ? "-" : strtoupper($dataPO->port_origin); ?></td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>
        </table>
        <table style="width: 100%; margin-top:10px;">
            <tr>
                <td>
                    <b><u>CONSIGNEE & NOTIFY PARTY :</u></b> <br>
                    <div style="margin-top: 8px;">
                        <?= strtoupper($dataPO->consigne) ?>
                    </div>
                </td>
                <td>
                    <b>SHIPMENT METHOD</b> <br>
                    <div style="margin-top: 8px;">
                        <?= strtoupper($shipmentName) ?>
                    </div>
                </td>
            </tr>
        </table>
        <table style="width: 50%; margin-top:10px;">
            <tr style="vertical-align: top;">
                <td style="width: 50px;">OFFICE</td>
                <td>:</td>
                <td><?= strtoupper($alamatKantor['value']); ?></td>
            </tr>
            <tr style="vertical-align: top;">
                <td>FACTORY</td>
                <td>:</td>
                <td><?= strtoupper($company['address']); ?></td>
            </tr>
            <tr>
                <td>TEL</td>
                <td>:</td>
                <td><?= $telpKantor ?></td>
            </tr>
            <tr>
                <td>FAX</td>
                <td>:</td>
                <td><?= $faxKantor ?></td>
            </tr>
            <tr>
                <td>ATTN</td>
                <td>:</td>
                <td><?= $dataPO->note ?></td>
            </tr>
            <tr>
                <td>DESTINATION</td>
                <td>:</td>
                <td><?= $dataPO->port_destination == "-" ? "-" : strtoupper($dataPO->port_destination); ?></td>
            </tr>
        </table>
        <?php
        $chunkedDetails = array_chunk($dataPODetail, 15);
        $no = 1;
        $totalPrice = 0;
        $totalDisc = 0;
        $diskonTotal = 0;
        $totalWithAdditional = 0;
        foreach ($chunkedDetails as $index => $chunk) :
            if ($index > 0) {
                echo '<div style="page-break-before: always;"></div>';
            }
        ?>
            <table border="1" style="width: 100%; margin-top:10px;" class="table no-border">
                <thead>
                    <tr>
                        <td style="text-align: center;">MARKS & NO</td>
                        <td style="text-align: center;">PARTICULAR</td>
                        <td style="text-align: center;">QTTY</td>
                        <td style="text-align: center;">UNIT PRICE <br><?= $valutaName ?></td>
                        <td style="text-align: center;">TOTAL AMOUNT<br><?= $valutaName ?></td>
                    </tr>
                    <!-- <tr>
                        <td style="border:0px;"></td>
                        <td style="border:0px;"></td>
                        <td style="border:0px;"></td>
                        <td colspan="2" style="text-align: center;border:0px;">
                            <?= strtoupper($valuta) ?>
                        </td>
                    </tr> -->
                </thead>
                <tbody>
                    <?php foreach ($chunk as $detail) : ?>
                        <?php
                        $totalDisc = (formatter($detail["price"], "CURR_TO_FLOAT") * formatter($detail["qty"], "STR_TO_FLOAT")) * ((float)$detail["disc"] / 100);
                        $totalWithAdditional = (formatter($detail["price"], "CURR_TO_FLOAT") * formatter($detail["qty"], "STR_TO_FLOAT") - $totalDisc) + formatter($detail["additional_cost"], "CURR_TO_INT");
                        $totalPrice += $totalWithAdditional;
                        $diskonTotal += $totalDisc;
                        ?>
                        <tr>
                            <td style="text-align: center; border:0px;"><?= $no++; ?></td>
                            <td style="border:0px;">
                                <?= $detail["nama_barang"] . " " . $detail['spesifikasi'] . ($detail['note'] == null ? "" : " ( " . $detail['note'] . " )") ?>
                            </td>
                            <td style="text-align: center;border:0px;">
                                <?= $detail["qty"] . " " . $detail["kode_satuan"] ?>
                            </td>
                            <td style="text-align: center;border:0px;">
                                <?= number_format($detail["price"], 2) ?>
                            </td>
                            <td style="text-align: center;border:0px;">
                                <?= number_format($totalWithAdditional, 2, '.', ','); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($index === count($chunkedDetails) - 1): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">DISCOUNT</td>
                            <td style="text-align: center;"><?= number_format(formatter(($dataPO->potongan_harga), "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center;">TOTAL</td>
                            <td style="text-align: center;"><?= number_format(formatter(($dataPO->total), "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>


            <div style="position: absolute; bottom: 20px; right: 30px; font-size: 12px;">
                Page <?= $index + 1 ?> of <?= count($chunkedDetails) ?>
            </div>

        <?php endforeach; ?>

        <table style="margin-top: 15px;">
            <tr>
                <td>LATEST SHIPMENT DATE</td>
                <td>:</td>
                <td><?= $dataPO->latest_shipment_date; ?></td>
            </tr>
            <tr>
                <td>PAYMENT TERM</td>
                <td>:</td>
                <td><?= $dataPO->payment_term; ?></td>
            </tr>
        </table>

        <table style="margin-top: 50px;">
            <tr>
                <td>
                    YOUR FAITHFULLY, <br>
                    <?= strtoupper($dataPO->shipper) ?> <br>
                    <br><br><br><br>

                    <b><u><?= $dataPO->direktur == "-" ? "" : strtoupper($dataPO->direktur); ?></u></b><br>
                    DIRECTOR
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
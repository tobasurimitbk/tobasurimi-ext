<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dataInvoice['no_container'] ?></title>
    <style>
        @media print {
            body {
                margin: 0;
            }

            @page {
                size: 210mm 330mm;
                /* margin: 20mm; */
            }
        }

        .header {
            display: flex !important;
            justify-content: space-between !important;
        }

        .sign-table {
            border-collapse: collapse !important;
            text-align: left !important;
            width: 100% !important;
            margin-top: 2.5rem;
        }

        .sign-table tr th {
            text-align: left !important;
        }

        .item-table {
            border-collapse: collapse !important;
            text-align: center !important;
            width: 100% !important;
        }

        .item-table tr th {
            text-align: left !important;
        }

        .item-table tr td {
            text-align: left !important;
        }

        .item-table tbody tr {
            border-top: 1px solid !important;
        }

        .mt-025 {
            margin-top: 0.25rem;
        }

        .mt-1 {
            margin-top: 0.6rem;
        }

        .txt-left {
            text-align: left;
        }

        .txt-center {
            text-align: center;
        }

        .txt-right {
            text-align: right;
        }

        .w-30 {
            width: 30%;
        }

        .w-50 {
            width: 50%;
        }

        .w-100 {
            width: 100%;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .label-header {
            font-weight: bold;
            font-size: 12px;
        }

        .po-customer {
            margin-top: -20px !important;
        }

        .rev-customer {
            margin-top: -20px !important;
        }

        .justify-content-center {
            justify-content: center !important;
        }

        .ttd-section {
            page-break-inside: avoid;
            margin-top: 20px;
        }

        .label-header {
            font-weight: bold;
            font-size: 12px;
        }

        body {
            font-family: 'Times New Roman', Times, serif
        }

        .pagebreak {
            clear: both;
            page-break-after: always;
        }
    </style>
</head>

<body>

    <table border="0" style="width: 100%; margin-top:-20px;">
        <tr style="vertical-align: top;">
            <?php if ($company['id'] != 15): ?>
                <td>
                    <?php if ($company['id'] == 2): ?>
                        <!-- KIM 2 -->
                        <div style="text-align: right; margin-left:-20px; margin-right:40px; ">
                            <img src="<?= $company['logo'] ?>" style="width: 140px; height:100px; text-align:right; margin-top:-5px" alt="">
                        </div>
                    <?php else : ?>
                        <!-- KIM 1 -->
                        <div style="text-align: right; margin-left:-20px; margin-right:40px; ">
                            <img src="<?= $company['logo'] ?>" style="width: 190px; height:140px; text-align:right; margin-top:-5px" alt="">
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
                        <table style="width: 100%; margin-top:-25px; margin-left:-30px; font-size:12px;">

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
                        <table style="width: 110%; margin-top:-15px; margin-left:-30px; font-size:12px;">

                            <tr style="vertical-align: top;">
                                <td style="width: 10px;">Office</td>
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
                    <table style="width: 100%; margin-top: -15px; font-size: 12px;">
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
    <div class="header">
        <div class="txt-center" style="margin-top: -10px;">
            <h3>COMMERCIAL INVOICE</h3>
        </div>
    </div>
    <table style="width: 100%;">
        <tr>
            <td>
                <table class="label" style="font-size: 12px;">
                    <tr>
                        <td>DATE</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($dataSalesOrderExport->tanggal_invoice)) ?></td>
                    </tr>
                    <tr>
                        <td style="width: 140px;">INVOICE NO</td>
                        <td>:</td>
                        <td><?= $dataSalesOrderExport->no_invoice  ?></td>
                    </tr>
                    <tr>
                        <td style="width: 140px;">CONSIGNEE</td>
                        <td>:</td>
                        <td style="width: 180px;"><?= $dataSalesOrderExport->customer_name ?></td>
                    </tr>
                    <tr>
                        <td>ADDRESS</td>
                        <td>:</td>
                        <td><?= $dataInvoice['alamat'] ?></td>
                    </tr>
                    <tr>
                        <td>PORT OF LOADING</td>
                        <td>:</td>
                        <td><?= strip_tags($dataInvoice['loading_port']) ?></td>
                    </tr>
                    <tr>
                        <td>PORT OF DISCHARGE</td>
                        <td>:</td>
                        <td><?= strip_tags($dataInvoice['dicharge_port']) ?></td>
                    </tr>
                    <tr>
                        <td>CONTRACT</td>
                        <td>:</td>
                        <td><?= $dataSalesOrderExport->sales_contract_no ?></td>
                    </tr>
                    <?php if (!empty($dataSalesOrderExport->customer_po_no)): ?>
                        <tr>
                            <td>PO NO</td>
                            <td>:</td>
                            <td><?= $dataSalesOrderExport->customer_po_no ?></td>
                        </tr>
                    <?php endif; ?>
                </table>

            </td>
            <td>
                <table class="label" style="font-size: 12px; width:100%;">
                    <tr style="text-align: left;">
                        <td style="width: 150px;">VESSEL'S NAME</td>
                        <td>:</td>
                        <td><?= $dataInvoice['vessels_name'] ?></td>
                    </tr>
                    <tr>
                        <td>DEPARTURE DATE</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($dataInvoice['departure_date'])) ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">TERMS OF PAYMENT</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;"><?= $dataInvoice['payment_term'] ?></td>
                    </tr>
                    <tr>
                        <td>NOTIFY PARTY</td>
                        <td>:</td>
                        <td><?= $dataInvoice['notify_party'] ?></td>
                    </tr>
                    <?php if (!empty($dataInvoice['notify_party2'])): ?>
                        <tr>
                            <td style="vertical-align: top;">2ND NOTIFY PARTY</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;"><?= $dataInvoice['notify_party2'] ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>
    </table>

    <?php
    $kodeSatuan = "";
    foreach ($dataListBarang as $d) {
        $kodeSatuan = $d['kode_satuan'];
    }
    ?>

    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 12px; ">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #7a7a78;">
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #7a7a78; width: 4%; height:2.5%;">NO</th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #7a7a78;">
                    DESCRIPTION AND QUANTITY OF PRODUCT / GOODS
                </th>
                <th style="padding: 6px; text-align: center; font-weight: bold; border: 1px solid #7a7a78;">
                    QUANTITY <br> (<?= $kodeSatuan ?>)
                </th>
                <th style="padding: 6px; text-align: center; font-weight: bold; border: 1px solid #7a7a78;">
                    UNIT PRICE
                </th>
                <th style="padding: 6px; text-align: center; font-weight: bold; border: 1px solid #7a7a78;">
                    AMOUNT <br>
                    <?= $dataSalesOrderExport->tipe_harga ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;
            $totalQty = 0;
            $totalTotalHarga = 0;
            foreach ($dataListBarang as $key => $detail):
                $totalQty += $detail['qty'];
                $totalTotalHarga += $detail['total_harga_barang'];
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 6px; border: 1px solid #7a7a78; vertical-align: top;"><?= $no++ ?></td>
                    <td style="padding: 6px; border: 1px solid #7a7a78; vertical-align: top;">
                        <?= $detail['nama_barang'] ?> <br>
                        <?= $detail['catatan'] ?>
                    </td>
                    <td style="padding: 6px; border: 1px solid #7a7a78; vertical-align: top;text-align:right;">
                        <?= number_format($detail['qty'], 2) ?>
                    </td>
                    <td style="padding: 6px; border: 1px solid #7a7a78; vertical-align: top;text-align:right;">
                        <?= number_format($detail['harga_satuan_barang'], 2) ?>
                    </td>
                    <td style="padding: 6px; border: 1px solid #7a7a78; vertical-align: top;text-align:right;">
                        <span style="float:left;">
                            <?= $dataInvoice['valas_name'] ?>
                        </span>
                        <?= number_format($detail['total_harga_barang'], 2) ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td colspan="2" style="padding: 6px; border: 1px solid #7a7a78; vertical-align: top;"></td>
                <td style="text-align: right; font-weight:bold;padding: 6px; border: 1px solid #7a7a78;">
                    <?= number_format($totalQty, 2) ?>
                </td>
                <td style="text-align: right; font-weight:bold;padding: 6px; border: 1px solid #7a7a78;">
                </td>
                <td style="text-align: right; font-weight:bold;padding: 6px; border: 1px solid #7a7a78;">
                    <span style="float:left;">
                        <?= $dataInvoice['valas_name'] ?>
                    </span>
                    <?= number_format($totalTotalHarga, 2) ?>

                </td>
            </tr>

            <?php foreach ($dataListBiayaTambahan as $d): ?>
                <?php
                if ($d['tipe_biaya_tambahan'] == "MINUS") {
                    $color = "red";
                } else {
                    $color = "black";
                }
                ?>
                <tr style="font-weight: bold; background-color: #e9ecef;">
                    <td style="text-align: right; font-weight:bold;padding: 6px; border: 1px solid #7a7a78;"></td>
                    <td colspan="3" style="text-align: left; font-weight:bold;padding: 6px; border: 1px solid #7a7a78;"><?= $d['biaya_tambahan'] ?></td>
                    <td style="padding: 6px; border: 1px solid #7a7a78; text-align: right; ">
                        <span style="float: left;">
                            <?= $dataInvoice['valas_name'] ?>
                        </span>
                        <span style="color:<?= $color ?>">
                            <?= number_format($d['nilai_biaya_tambahan'], 2) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>

            <tr style="font-weight: bold; background-color: #e9ecef; ">
                <td style="padding: 6px; border: 1px solid #7a7a78;"></td>
                <td colspan="3" style="text-align: right; font-weight:bold;padding: 6px; border: 1px solid #7a7a78;">
                    GRAND TOTAL
                </td>
                <td style="padding: 6px; border: 1px solid #7a7a78; text-align: right; ">
                    <span style="float: left;">
                        <?= $dataInvoice['valas_name'] ?>
                    </span>
                    <?= number_format($dataInvoice['total_nilai_invoice'], 2) ?>
                    </span>
                </td>
            </tr>
            <tr style="font-weight: bold; background-color: #e9ecef; ">
                <td style="padding: 6px; border: 1px solid #7a7a78;"></td>
                <td style="padding: 6px; border: 1px solid #7a7a78; text-align: right; " colspan="4">
                    <table>
                        <tr>
                            <td>
                                <b>AMOUNT IN WORLD :</b><br>
                                <b><?= strtoupper(terbilangInggris($dataInvoice['total_nilai_invoice'])) ?></b>
                            </td>
                            <!-- <?php if (!empty($dataInvoice['payment_description'])): ?>
                                <td>
                                    <b>
                                        <?= $dataInvoice['payment_description'] ?>
                                    </b>
                                </td>
                            <?php endif; ?> -->
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="font-weight: bold; background-color: #e9ecef; ">
                <td style="padding: 6px; border: 1px solid #7a7a78;"></td>
                <td style="padding: 6px; border: 1px solid #7a7a78; text-align: right; " colspan="4">
                    <table>
                        <tr style="font-weight: bold;">
                            <td>
                                TOTAL
                            </td>
                            <td>:</td>
                            <td>
                                <?= number_format($totalQty, 2) . " " . $kodeSatuan ?>
                            </td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td>
                                NETTO WEIGHT
                            </td>
                            <td>:</td>
                            <td>
                                <?= number_format($dataInvoice['total_berat_bersih'], 2) ?> Kg
                            </td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td>
                                GROSS WEIGHT
                            </td>
                            <td>:</td>
                            <td>
                                <?= number_format($dataInvoice['total_berat_kotor'], 2) ?> Kg
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="font-weight: bold; background-color: #e9ecef; ">
                <td style="padding: 6px; border: 1px solid #7a7a78;"></td>
                <td style="padding: 6px; border: 1px solid #7a7a78; text-align: right; " colspan="4">
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <table style="width: 100%;">
                                    <tr>
                                        <td>
                                            <b>
                                                CONTAINER NO. <br>
                                                <?= $dataInvoice['no_container'] ?><br><br>
                                            </b>
                                        </td>
                                        <td>
                                            <b>
                                                SEAL NO : <br>
                                                <?= $dataInvoice['no_seal'] ?><br><br>
                                            </b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>
                                                MEASUREMENT. <br>
                                                <?= $dataInvoice['measurement'] ?>
                                            </b>
                                        </td>
                                        <td>
                                            <b>
                                                COUNTRY OF ORIGIN. <br>
                                                <?= $dataInvoice['country_of_origin'] ?>
                                            </b>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                            <td style="text-align: center; ">
                                <b>
                                    <center>
                                        <b>
                                            FOR AND BEHALF OF
                                        </b>
                                        <br><br><br><br><br>
                                        <?= $dataInvoice['penanda_tangan'] ?> <br>
                                        AUTHORIZED SIGNATURE
                                    </center>
                                </b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </tbody>
    </table>

    <div class="pagebreak"></div>
    <table border="0" style="width: 100%; margin-top:-20px;">
        <tr style="vertical-align: top;">
            <?php if ($company['id'] != 15): ?>
                <td>
                    <?php if ($company['id'] == 2): ?>
                        <!-- KIM 2 -->
                        <div style="text-align: right; margin-left:-20px; margin-right:40px; ">
                            <img src="<?= $company['logo'] ?>" style="width: 140px; height:100px; text-align:right; margin-top:-5px" alt="">
                        </div>
                    <?php else : ?>
                        <!-- KIM 1 -->
                        <div style="text-align: right; margin-left:-20px; margin-right:40px; ">
                            <img src="<?= $company['logo'] ?>" style="width: 190px; height:140px; text-align:right; margin-top:-5px" alt="">
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
                        <table style="width: 100%; margin-top:-25px; margin-left:-30px; font-size:12px;">

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
                        <table style="width: 110%; margin-top:-15px; margin-left:-30px; font-size:12px;">

                            <tr style="vertical-align: top;">
                                <td style="width: 10px;">Office</td>
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
                    <table style="width: 100%; margin-top: -15px; font-size: 12px;">
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
    <div class="header">
        <div class="txt-center" style="margin-top: -10px;">
            <h3>PACKING LIST</h3>
        </div>
    </div>
    <table style="width: 100%;">
        <tr>
            <td>
                <table class="label" style="font-size: 12px;">
                    <tr>
                        <td>DATE</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($dataSalesOrderExport->tanggal_invoice)) ?></td>
                    </tr>
                    <tr>
                        <td style="width: 140px;">INVOICE NO</td>
                        <td>:</td>
                        <td><?= $dataSalesOrderExport->no_invoice  ?></td>
                    </tr>
                    <tr>
                        <td style="width: 140px;">CONSIGNEE</td>
                        <td>:</td>
                        <td style="width: 180px;"><?= $dataSalesOrderExport->customer_name ?></td>
                    </tr>
                    <tr>
                        <td>ADDRESS</td>
                        <td>:</td>
                        <td><?= $dataInvoice['alamat'] ?></td>
                    </tr>
                    <tr>
                        <td>PORT OF LOADING</td>
                        <td>:</td>
                        <td><?= strip_tags($dataSalesOrderExport->loading_port) ?></td>
                    </tr>
                    <tr>
                        <td>PORT OF DISCHARGE</td>
                        <td>:</td>
                        <td><?= strip_tags($dataSalesOrderExport->dicharge_port) ?></td>
                    </tr>
                    <tr>
                        <td>CONTRACT</td>
                        <td>:</td>
                        <td><?= $dataSalesOrderExport->sales_contract_no ?></td>
                    </tr>
                    <?php if (!empty($dataSalesOrderExport->customer_po_no)): ?>
                        <tr>
                            <td>PO NO</td>
                            <td>:</td>
                            <td><?= $dataSalesOrderExport->customer_po_no ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td>NO SEAL</td>
                        <td>:</td>
                        <td><?= $dataInvoice['no_seal'] ?></td>
                    </tr>
                    <tr>
                        <td>NO CONTAINER</td>
                        <td>:</td>
                        <td><?= $dataInvoice['no_container'] ?></td>
                    </tr>
                </table>

            </td>
            <td>
                <table class="label" style="font-size: 12px; width:100%;">
                    <tr style="text-align: left;">
                        <td style="width: 150px;">VESSEL'S NAME</td>
                        <td>:</td>
                        <td><?= $dataInvoice['vessels_name'] ?></td>
                    </tr>
                    <tr>
                        <td>DEPARTURE DATE</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($dataInvoice['departure_date'])) ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">TERMS OF PAYMENT</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;"><?= $dataInvoice['payment_term'] ?></td>
                    </tr>
                    <tr>
                        <td>NOTIFY PARTY</td>
                        <td>:</td>
                        <td><?= $dataInvoice['notify_party'] ?></td>
                    </tr>
                    <?php if (!empty($dataInvoice['notify_party2'])): ?>
                        <tr>
                            <td style="vertical-align: top;">2ND NOTIFY PARTY</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;"><?= $dataInvoice['notify_party2'] ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 12px; ">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #7a7a78;">
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #7a7a78; width: 4%; height:2.5%;">NO</th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #7a7a78;">
                    DESCRIPTION AND QUANTITY OF PRODUCT / GOODS
                </th>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;
            foreach ($dataListPacking as $key => $detail):
            ?>
                <tr style="border-bottom: 1px solid #7a7a78;">
                    <td style="padding: 6px; border: 1px solid #7a7a78; vertical-align: top;"><?= $no++ ?></td>
                    <td style="padding: 6px; border: 1px solid #7a7a78; vertical-align: top;">
                        <?= $detail['nama_barang'] ?> <br>
                        HS CODE : <?= $detail['hs_code_name'] ?> <br>
                        <?= $detail['catatan'] ?>
                        <div>
                            <?php if (!empty($detail['size_breakdown'])): ?>
                                <?php
                                // Identify which columns have data
                                $columns_to_show = [];
                                $all_columns = [
                                    'can' => ['label' => 'QTY (CAN)', 'width' => '7%'],
                                    'case' => ['label' => 'QTY (CARTONS)', 'width' => '7%'],
                                    'kg' => ['label' => 'QTY (KG)', 'width' => '7%'],
                                    'lb' => ['label' => 'QTY (LB)', 'width' => '7%'],
                                    'inner_box' => ['label' => 'INNER', 'width' => '7%'],
                                    'pc' => ['label' => 'QTY (PC)', 'width' => '7%'],
                                    'bag' => ['label' => 'QTY (Bag)', 'width' => '7%'],
                                    'persen' => ['label' => '%', 'width' => '3%'], // tidak pakai qty
                                    'cup' => ['label' => 'QTY (CUP)', 'width' => '7%'],
                                    'palet' => ['label' => 'PALLET', 'width' => '7%'],
                                    'vgm' => ['label' => 'VGM', 'width' => '7%'],
                                    'drammed' => ['label' => 'DRAINED', 'width' => '7%'],

                                ];

                                // Check which columns have data
                                foreach ($all_columns as $col => $col_data) {
                                    foreach ($detail['size_breakdown'] as $breakdown) {
                                        if (!empty($breakdown[$col])) {
                                            $columns_to_show[$col] = $col_data;

                                            if (!empty($breakdown['drammed'])) {
                                                $columns_to_show['drammed'] = $col_data;
                                            }

                                            if (!empty($breakdown['vgm'])) {
                                                $columns_to_show['vgm'] = $col_data;
                                            }
                                            break;
                                        }
                                    }
                                }

                                $show_can_column = isset($columns_to_show['can']);
                                $show_case_column = isset($columns_to_show['case']);
                                $show_kg_column = isset($columns_to_show['kg']);
                                $show_lb_column = isset($columns_to_show['lb']);
                                $show_inner_box_column = isset($columns_to_show['inner_box']);
                                $show_pc_column = isset($columns_to_show['pc']);
                                $show_bag_column = isset($columns_to_show['bag']);
                                $show_persen_column = isset($columns_to_show['persen']);
                                $show_cup_column = isset($columns_to_show['cup']);
                                $show_palet_column = isset($columns_to_show['palet']);
                                $show_vgm_column = isset($columns_to_show['vgm']);
                                $show_drammed_column = isset($columns_to_show['drammed']);

                                // Ini untuk mengetahui Qty Satuan apa yang dipakek (ambil paling utama)
                                $kodeSatuan =  $detail['kode_satuan'];

                                ?>

                                <div style="margin-top: 6px;">
                                    <div style=" font-weight: bold;">DETAIL PACKING:</div>
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; ">
                                        <thead>
                                            <tr style="background-color: #f3f4f6;">
                                                <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;"></th>
                                                <?php if ($show_can_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (CAN)</th>
                                                <?php endif; ?>

                                                <?php if ($show_case_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (CARTON)</th>
                                                <?php endif; ?>

                                                <?php if ($show_kg_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (KG)</th>
                                                <?php endif; ?>

                                                <?php if ($show_lb_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (LB)</th>
                                                <?php endif; ?>

                                                <?php if ($show_inner_box_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (INNER BOX)</th>
                                                <?php endif; ?>

                                                <?php if ($show_pc_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (PC)</th>
                                                <?php endif; ?>

                                                <?php if ($show_bag_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (BAG)</th>
                                                <?php endif; ?>

                                                <?php if ($show_persen_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (%)</th>
                                                <?php endif; ?>

                                                <?php if ($show_cup_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (CUP)</th>
                                                <?php endif; ?>

                                                <?php if ($show_palet_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">QTY (PALET)</th>
                                                <?php endif; ?>

                                                <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">WEIGHT NETTO</th>
                                                <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">WEIGHT GROSS</th>

                                                <?php if ($show_vgm_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">VGM</th>
                                                <?php endif; ?>

                                                <?php if ($show_drammed_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #7a7a78; width: 4.5%;text-align: center;">DRAMMED</th>
                                                <?php endif; ?>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $breakdown_can = 0;
                                            $breakdown_case = 0;
                                            $breakdown_kg = 0;
                                            $breakdown_lb = 0;
                                            $breakdown_inner_box = 0;
                                            $breakdown_pc = 0;
                                            $breakdown_bag = 0;
                                            $breakdown_persen = 0;
                                            $breakdown_cup = 0;
                                            $breakdown_palet = 0;
                                            $breakdown_vgm = 0;
                                            $breakdown_drammed = 0;
                                            $breakdown_berat_kotor = 0;
                                            $breakdown_berat_bersih = 0;

                                            foreach ($detail['size_breakdown'] as $breakdown):
                                                $breakdown_berat_bersih += $breakdown['berat_bersih'];
                                                $breakdown_berat_kotor += $breakdown['berat_kotor'];

                                                if (isset($breakdown['can'])) {
                                                    $breakdown_can += $breakdown['can'];
                                                }
                                                if (isset($breakdown['case'])) {
                                                    $breakdown_case += $breakdown['case'];
                                                }
                                                if (isset($breakdown['kg'])) {
                                                    $breakdown_kg += $breakdown['kg'];
                                                }
                                                if (isset($breakdown['lb'])) {
                                                    $breakdown_lb += $breakdown['lb'];
                                                }
                                                if (isset($breakdown['inner_box'])) {
                                                    $breakdown_inner_box += $breakdown['inner_box'];
                                                }
                                                if (isset($breakdown['pc'])) {
                                                    $breakdown_pc += $breakdown['pc'];
                                                }
                                                if (isset($breakdown['bag'])) {
                                                    $breakdown_bag += $breakdown['bag'];
                                                }
                                                if (isset($breakdown['persen'])) {
                                                    $breakdown_persen += $breakdown['persen'];
                                                }
                                                if (isset($breakdown['cup'])) {
                                                    $breakdown_cup += $breakdown['cup'];
                                                }
                                                if (isset($breakdown['palet'])) {
                                                    $breakdown_palet += $breakdown['palet'];
                                                }
                                                if (isset($breakdown['vgm'])) {
                                                    $breakdown_vgm += $breakdown['vgm'];
                                                }
                                                if (isset($breakdown['drammed'])) {
                                                    $breakdown_drammed += $breakdown['drammed'];
                                                }
                                            ?>
                                                <tr>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                    </td>

                                                    <?php if ($show_can_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['can'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_case_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['case'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_kg_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['kg'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_lb_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['lb'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_inner_box_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['inner_box'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_pc_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['pc'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_bag_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['bag'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_persen_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['persen'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_cup_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['cup'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_palet_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['palet'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;"><?= number_format($breakdown['berat_bersih'], 2)  ?></td>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;"><?= number_format($breakdown['berat_kotor'], 2)  ?></td>

                                                    <?php if ($show_vgm_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['vgm'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_drammed_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right;">
                                                            <?= number_format($breakdown['drammed'], 2); ?>
                                                        </td>
                                                    <?php endif; ?>

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <?php
                                        // Hitung jumlah kolom utama (misalnya dari thead)
                                        $base_columns = count($columns_to_show);
                                        if ($show_can_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_case_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_kg_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_lb_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_inner_box_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_pc_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_bag_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_persen_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_cup_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_palet_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_vgm_column) {
                                            $base_columns -= 1;
                                        }
                                        if ($show_drammed_column) {
                                            $base_columns -= 1;
                                        }
                                        ?>

                                        <tfoot>
                                            <tr style="background-color: #e9ecef;">
                                                <td colspan="<?= $base_columns ?>" style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                    TOTAL
                                                </td>

                                                <?php if ($show_can_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_can, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_case_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_case, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_kg_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_kg, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_lb_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_lb, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_inner_box_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_inner_box, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_pc_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_pc, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_bag_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_bag, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_persen_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_persen, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_cup_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_cup, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_palet_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_palet, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                    <?= number_format($breakdown_berat_bersih, 2)  ?>
                                                </td>

                                                <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                    <?= number_format($breakdown_berat_kotor, 2)  ?>
                                                </td>

                                                <?php if ($show_vgm_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_vgm, 2)  ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_drammed_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #7a7a78; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_drammed, 2)  ?>
                                                    </td>
                                                <?php endif; ?>



                                            </tr>
                                        </tfoot>

                                    </table>
                                </div>
                            <?php endif; ?>



                        </div>
                    </td>

                </tr>
            <?php endforeach ?>


        </tbody>
    </table>
    <!-- SIGNATURE TABLE -->
    <br>
    <table class="mt-1 sign-table border-collapse" style="margin-top: 18px; width: 100%; text-align:center;">
        <tbody>
            <tr>
                <th style="width: 350px;">
                </th>
                <th>
                    <center>
                        <label class="label-header">FOR AND BEHALF OF</label>
                    </center>
                </th>
            </tr>
            <tr>
                <th style="width: 350px;">
                </th>
                <th>
                    <br><br><br>
                    <center>
                        <label class="label-header"><?= $dataInvoice['penanda_tangan'] ?></label><br>
                        <i style="font-size: 10px;">
                            Authorized Signature
                        </i>
                    </center>

                </th>
            </tr>
        </tbody>

    </table>
</body>

</html>
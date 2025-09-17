<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dataPI['no_pi'] ?></title>
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
    </style>
</head>

<body>

    <table border="0" style="width: 100%; margin-top:-20px;">
        <tr style="vertical-align: top;">
            <?php if ($company['id'] != 15): ?>
                <td>
                    <?php if ($company['id'] == 2): ?>
                        <div style="text-align: right; margin-left:-20px; margin-right:40px; ">
                            <img src="<?= $company['logo'] ?>" style="width: 140px; height:100px; text-align:right; margin-top:-5px" alt="">
                        </div>
                    <?php else : ?>
                        <div style="text-align: right; margin-left:-20px; margin-right:40px; ">
                            <img src="<?= $company['logo'] ?>" style="width: 190px; text-align:right; margin-top:-5px" alt="">
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
    <div class="header">
        <div class="txt-center" style="margin-top: -10px;">
            <h3>PROFORMA INVOICE</h3>
        </div>
    </div>

    <table style="width: 100%;">
        <tr>
            <td>
                <table class="label" style="font-size: 12px;">
                    <tr>
                        <td>BUYER</td>
                        <td>:</td>
                        <td><?= $dataSalesOrderExport->customer_name ?></td>
                    </tr>
                    <tr>
                        <td>ADDRESS</td>
                        <td>:</td>
                        <td><?= $dataPI['alamat_customer'] ?></td>
                    </tr>
                    <tr>
                        <td>SC</td>
                        <td>:</td>
                        <td><?= $dataSalesOrderExport->sales_order_export_no ?></td>
                    </tr>
                </table>

            </td>
            <td>
                <table class="label" style="font-size: 12px;">
                    <tr>
                        <td>PI NO</td>
                        <td>:</td>
                        <td><?= $dataPI['no_pi'] ?></td>
                    </tr>
                    <tr>
                        <td>DATE</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($dataPI['tanggal_pi'])) ?></td>
                    </tr>
                    <tr>
                        <td>TERM OF PAYMENT</td>
                        <td>:</td>
                        <td><?= $dataPI['payment_term'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <?php
    $kodeSatuan = "";
    foreach ($dataPIBarang as $d) {
        $kodeSatuan = $d['kode_satuan'];
    }
    ?>

    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 12px; ">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 4%; height:2.5%;">NO</th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    DESCRIPTION AND QUANTITY OF PRODUCT / GOODS
                </th>
                <th style="padding: 6px; text-align: center; font-weight: bold; border: 1px solid #ddd; width:15%;">
                    QUANTITY <br>
                    (<?= $kodeSatuan ?>)
                </th>
                <th style="padding: 6px; text-align: center; font-weight: bold; border: 1px solid #ddd; width:15%;">
                    UNIT PRICE <br>
                    (<?= $dataPI['valas_name'] ?>/<?= $kodeSatuan ?>)
                </th>
                <th style="padding: 6px; text-align: center; font-weight: bold; border: 1px solid #ddd; width:17%;">
                    TOTAL AMOUNT <br>
                    (<?= $dataPI['valas_name']  ?>)
                </th>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;
            $totalQty = 0;
            $totalTotalHarga = 0;
            foreach ($dataPIBarang as $key => $barang) {
                $totalQty += $barang['qty_barang'];
                $totalTotalHarga += $barang['total_harga'];
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"><?= $no++ ?></td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <?= $barang['nama_barang'] ?>
                    </td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <?= number_format($barang['qty_barang'], 2) ?>
                    </td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <?= number_format($barang['harga_satuan'], 2) ?>
                    </td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <span>
                            <?= $dataPI['valas_name'] ?>
                        </span>
                        <span class="text-align:right;">
                            <?= number_format($barang['total_harga'], 2) ?>
                        </span>

                    </td>
                </tr>
            <?php } ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;" colspan="4">
                    <?= $dataPI['packing'] ?>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                    <br>
                    PLEASE MAKE YOUR PAYMENT TO OUR BANK ACCOUNT WITH DETAILS BELLOW: <br>
                    <table style="margin-left:-3px;">
                        <tr>
                            <td style="width: 103px;"><b>BANK NAME</b></td>
                            <td>:</td>
                            <td><b><?= $dataPI['nama_bank'] ?></b></td>
                        </tr>
                        <tr>
                            <td><b>ACCOUNT NO</b></td>
                            <td>:</td>
                            <td><b><?= $dataPI['no_rekening'] ?></b></td>
                        </tr>
                        <tr>
                            <td><b>SWIFT CODE</b></td>
                            <td>:</td>
                            <td><b><?= $dataPI['kode_bank'] ?></b></td>
                        </tr>
                        <tr>
                            <td><b>REMARKS</b></td>
                            <td>:</td>
                            <td><b>BENECIFIARY ACCOUNT:FULL AMOUNT</b></td>
                        </tr>
                        <tr>
                            <td><b>ACCOUNT NAME</b></td>
                            <td>:</td>
                            <td><b><?= $dataPI['atas_nama'] ?></b></td>
                        </tr>
                    </table>
                </td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                    <b>
                        TOTAL
                    </b>
                </td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"><b><?= number_format($totalQty, 2) ?></b></td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                    <b>
                        <span>
                            <?= $dataPI['valas_name'] ?>
                        </span>
                        <span class="text-align:right;">
                            <?= number_format($totalTotalHarga, 2) ?>
                        </span>
                    </b>


                </td>
            </tr>
            <?php foreach ($dataPIPaymentTerm as $d): ?>
                <?php
                if ($d['is_penagihan']) {
                    $bgColor = "yellow";
                } else {
                    $bgColor = "white";
                }
                ?>
                <tr style="border-bottom: 1px solid #eee; background-color:<?= $bgColor ?>">
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <b>
                            <?= $d['payment_term'] ?>
                        </b>
                    </td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <b>
                            <span>
                                <?= $dataPI['valas_name'] ?>
                            </span>
                            <span class="text-align:right;">
                                <?= number_format($d['nilai_payment_term'], 2) ?>
                            </span>
                        </b>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                    <b>
                        AMOUNT IN WORLD :
                    </b>
                    <br>
                    <b>
                        <?= $dataPI['valas_name'] . " " . strtoupper(terbilangInggris((float)$dataPI['total_pi'])) ?>
                    </b>
                </td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;" colspan="3" rowspan="2">
                    <center>
                        <b>
                            FOR AND BEHALF OF
                        </b>
                        <br><br><br><br><br>
                        <?= $dataPI['penanda_tangan'] ?> <br>
                        AUTHORIZED SIGNATURE
                    </center>

                </td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                    <b>
                        PAYMENT INSTRUCTION :
                    </b>
                    <br>
                    <b>
                        <?= $dataPI['payment_instruction'] ?> <br>
                        1011/<?= $dataPI['no_pi'] ?> (<?= number_format($dataPI['total_pi'], 2) ?>)
                    </b>
                </td>

            </tr>

        </tbody>
    </table>


</body>

</html>
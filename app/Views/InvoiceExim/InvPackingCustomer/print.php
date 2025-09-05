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
            <h3>COMMERCIAL INVOICE</h3>
        </div>
    </div>
    <table style="width: 100%;">
        <tr>
            <td>
                <table class="label" style="font-size: 12px;">
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
                        <td>PHONE</td>
                        <td>:</td>
                        <td><?= $dataInvoice['phone'] ?></td>
                    </tr>
                    <tr>
                        <td>ATTN</td>
                        <td>:</td>
                        <td><?= $dataInvoice['attn'] ?></td>
                    </tr>
                    <tr>
                        <td>EMAIL</td>
                        <td>:</td>
                        <td><?= $dataInvoice['email'] ?></td>
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
                </table>

            </td>
            <td>
                <table class="label" style="font-size: 12px;">
                    <tr>
                        <td style="width: 140px;">INVOICE NO</td>
                        <td>:</td>
                        <td><?= $dataSalesOrderExport->no_invoice  ?></td>
                    </tr>
                    <tr>
                        <td>DATE</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($dataSalesOrderExport->tanggal_invoice)) ?></td>
                    </tr>
                    <tr>
                        <td>VESSEL'S NAME</td>
                        <td>:</td>
                        <td><?= $dataInvoice['vessels_name'] ?></td>
                    </tr>
                    <tr>
                        <td>DEPARTURE DATE</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($dataInvoice['departure_date'])) ?></td>
                    </tr>
                    <tr>
                        <td>TERMS OF PAYMENT</td>
                        <td style="vertical-align: top;">:</td>
                        <td><?= $dataInvoice['payment_term'] ?></td>
                    </tr>
                    <tr>
                        <td>NOTIFY PARTY</td>
                        <td>:</td>
                        <td><?= $dataInvoice['notify_party'] ?></td>
                    </tr>
                    <tr>
                        <td>PO NO</td>
                        <td>:</td>
                        <td><?= $dataSalesOrderExport->customer_po_no ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 12px; ">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 4%; height:2.5%;">NO</th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    DESCRIPTION AND QUANTITY OF PRODUCT / GOODS
                </th>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;
            foreach ($dataListBarang as $key => $detail):
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"><?= $no++ ?></td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <?= $detail['nama_barang'] ?> <br>
                        <?= $detail['catatan'] ?>
                        <div>
                            <?php if (!empty($detail['size_breakdown'])): ?>
                                <?php
                                // Identify which columns have data
                                $columns_to_show = [];
                                $all_columns = [
                                    'size' => ['label' => 'SIZE', 'width' => '8%'], // tidak pakai qty
                                    'grade' => ['label' => 'GRADE', 'width' => '8%'], // tidak pakai qty
                                    'packing' => ['label' => 'PACKING', 'width' => '8%'], // tidak pakai qty
                                    'can' => ['label' => 'QTY (CAN)', 'width' => '7%'],
                                    'cased' => ['label' => 'QTY (CASE)', 'width' => '7%'],
                                    'case' => ['label' => 'QTY (CASE)', 'width' => '7%'],
                                    'kg' => ['label' => 'QTY (KG)', 'width' => '7%'],
                                    'lb' => ['label' => 'QTY (LB)', 'width' => '7%'],
                                    'inner_box' => ['label' => 'INNER', 'width' => '8%'],
                                    'pc' => ['label' => 'QTY (PC)', 'width' => '7%'],
                                    'bag' => ['label' => 'QTY (Bag)', 'width' => '7%'],
                                    'cup' => ['label' => 'QTY (CUP)', 'width' => '6%'],
                                    'persen' => ['label' => '%', 'width' => '3%'], // tidak pakai qty
                                    'remark' => ['label' => 'REMARK', 'width' => '10%'],  // tidak pakai qty
                                    'palet' => ['label' => 'PALLET', 'width' => '10%'] // tidak pakai qty
                                ];

                                // Check which columns have data
                                foreach ($all_columns as $col => $col_data) {
                                    foreach ($detail['size_breakdown'] as $breakdown) {
                                        if (!empty($breakdown[$col])) {
                                            $columns_to_show[$col] = $col_data;
                                            break;
                                        }
                                    }
                                }

                                // Check if percentage column exists and should be shown
                                $show_persen_column = isset($columns_to_show['persen']);
                                $show_cased_column = isset($columns_to_show['cased']);

                                // Ini untuk mengetahui Qty Satuan apa yang dipakek (ambil paling utama)
                                $satuanQty = "";
                                foreach ($detail['size_breakdown'] as $breakdown):
                                    $satuanQty =  $breakdown['satuan_size_code'];
                                endforeach;
                                ?>

                                <div style="margin-top: 6px;">
                                    <div style="font-size: 11px; font-weight: bold;">DETAIL GRADE / SIZE:</div>
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 11px;">
                                        <thead>
                                            <tr style="background-color: #f3f4f6;">
                                                <?php foreach ($columns_to_show as $col => $col_data): ?>
                                                    <?php if ($col != 'persen' && $col != 'cased'): ?>
                                                        <th style="padding: 3px; border: 1px solid #ddd; width: <?= $col_data['width'] ?>"><?= $col_data['label'] ?></th>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>

                                                <?php if ($show_cased_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%;text-align: center;">QTY (CASE)</th>
                                                <?php endif; ?>

                                                <?php if ($show_persen_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%;text-align: center;">%</th>
                                                <?php endif; ?>

                                                <th style=" padding: 3px; border: 1px solid #ddd; width: 4.5%; text-align: center;">QTY (<?= $satuanQty ?>)</th>
                                                <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%; text-align: center;">UNIT PRICE (<?= $dataInvoice['valas_name'] . "/" . $satuanQty ?>)</th>
                                                <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%; text-align: center;">TOTAL AMOUNT (<?= $dataSalesOrderExport->tipe_harga ?>)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $breakdown_qty = 0;
                                            $breakdown_total = 0;
                                            $breakdown_unit_price_total = 0;
                                            $breakdown_persen = 0;
                                            $breakdown_cased = 0;

                                            foreach ($detail['size_breakdown'] as $breakdown):
                                                $breakdown_qty += $breakdown['qty'];
                                                $breakdown_total += $breakdown['total'];
                                                $breakdown_unit_price_total += $breakdown['harga'];
                                                if (isset($breakdown['persen']) && is_numeric($breakdown['persen'])) {
                                                    $breakdown_persen += $breakdown['persen'];
                                                }

                                                if (isset($breakdown['cased']) && is_numeric($breakdown['cased'])) {
                                                    $breakdown_cased += $breakdown['cased'];
                                                }
                                            ?>
                                                <tr>
                                                    <?php foreach ($columns_to_show as $col => $col_data): ?>
                                                        <?php if ($col != 'persen' && $col != 'cased'): ?>
                                                            <td style="padding: 3px; border: 1px solid #ddd;"><?= $breakdown[$col] ?></td>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>

                                                    <?php if ($show_cased_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #ddd; text-align: right;">
                                                            <?= !empty($breakdown['cased']) ? number_format($breakdown['cased'], 2) : '' ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_persen_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #ddd; text-align: right;">
                                                            <?= !empty($breakdown['persen']) ? number_format($breakdown['persen'], 2) . " %" : '' ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['qty'], 2)  ?></td>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['harga'], 2) ?></td>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['total'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <?php
                                        // Hitung jumlah kolom utama (misalnya dari thead)
                                        $base_columns = count($columns_to_show);

                                        if ($show_cased_column) {
                                            $base_columns -= 1;
                                        }

                                        if ($show_persen_column) {
                                            $base_columns -= 1;
                                        }

                                        ?>

                                        <tfoot>
                                            <tr style="background-color: #e9ecef;">
                                                <td colspan="<?= $base_columns ?>" style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                    TOTAL
                                                </td>

                                                <?php if ($show_cased_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                        <?= $breakdown_cased > 0 ? number_format($breakdown_cased, 2) : '' ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_persen_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                        <?= $breakdown_persen > 0 ? number_format($breakdown_persen, 2) . " %" : '' ?>
                                                    </td>
                                                <?php endif; ?>

                                                <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                    <?= number_format($breakdown_qty, 2)  ?>
                                                </td>
                                                <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">-</td>
                                                <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                    <?= number_format($breakdown_total, 2) ?>
                                                </td>
                                            </tr>
                                        </tfoot>

                                    </table>
                                </div>
                            <?php endif; ?>

                        </div>
                    </td>

                </tr>
            <?php endforeach ?>

            <?php foreach ($dataListBiayaTambahan as $d): ?>
                <?php
                if ($d['tipe_biaya_tambahan'] == "MINUS") {
                    $color = "red";
                } else {
                    $color = "black";
                }
                ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right; ">

                        <span style="float: left;"><?= $d['biaya_tambahan'] ?></span>
                        (<?= $dataInvoice['valas_name'] ?>)<span style="color:<?= $color ?>">
                            <?= number_format($d['nilai_biaya_tambahan'], 2) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>

            <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                <td style="padding: 6px; border: 1px solid #ddd;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; text-align: right; ">
                    <span style="float: left;">
                        BALANCE AMOUNT TO BE PAID
                    </span>
                    (<?= $dataInvoice['valas_name'] ?>) <?= number_format($dataInvoice['total_nilai_invoice'], 2) ?>
                    </span>
                </td>
            </tr>
            <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                <td style="padding: 6px; border: 1px solid #ddd;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; text-align: right; ">
                    <table>
                        <tr>
                            <td>
                                <b>AMOUNT IN WORLD :</b><br>
                                <b><?= strtoupper(terbilangInggris($dataInvoice['total_nilai_invoice'])) ?></b>
                            </td>
                            <td>
                                <b>
                                    PLEASE FILL THE FOLLOWING CODES IN THE FIELD 70 ON SWIFT MT103
                                </b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                <td style="padding: 6px; border: 1px solid #ddd;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; text-align: right; ">
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <b>TOTAL CARTON : <?= number_format($dataInvoice['total_carton'], 2) ?></b><br>
                                <b>NETTO WEIGHT : <?= number_format($dataInvoice['total_berat_bersih'], 2) ?></b><br>
                                <b>GROSS WEIGHT : <?= number_format($dataInvoice['total_berat_kotor'], 2) ?></b><br>

                            </td>
                            <td style="text-align: center; background-color:yellow">
                                <b>
                                    <?= $dataSalesOrderExport->no_invoice ?> (<?= number_format($dataInvoice['total_nilai_invoice'], 2) ?>)
                                </b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                <td style="padding: 6px; border: 1px solid #ddd;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; text-align: right; ">
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


    <div class="pagebreak">



    </div>
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
            <h3>PACKING LIST</h3>
        </div>
    </div>
    <table style="width: 100%;">
        <tr>
            <td>
                <table class="label" style="font-size: 12px;">
                    <tr>
                        <td style="width: 140px;">APPLICANT</td>
                        <td>:</td>
                        <td style="width: 180px;"><?= $dataSalesOrderExport->customer_name ?></td>
                    </tr>
                    <tr>
                        <td>ADDRESS</td>
                        <td>:</td>
                        <td><?= $dataInvoice['alamat'] ?></td>
                    </tr>
                    <tr>
                        <td>PHONE</td>
                        <td>:</td>
                        <td><?= $dataInvoice['phone'] ?></td>
                    </tr>
                    <tr>
                        <td>ATTN</td>
                        <td>:</td>
                        <td><?= $dataInvoice['attn'] ?></td>
                    </tr>
                    <tr>
                        <td>EMAIL</td>
                        <td>:</td>
                        <td><?= $dataInvoice['email'] ?></td>
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

                </table>

            </td>
            <td>
                <table class="label" style="font-size: 12px;">
                    <tr>
                        <td style="width: 140px;">INVOICE NO</td>
                        <td>:</td>
                        <td><?= $dataSalesOrderExport->no_invoice  ?></td>
                    </tr>
                    <tr>
                        <td>DATE</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($dataSalesOrderExport->tanggal_invoice)) ?></td>
                    </tr>
                    <tr>
                        <td>VESSEL'S NAME</td>
                        <td>:</td>
                        <td><?= $dataInvoice['vessels_name'] ?></td>
                    </tr>
                    <tr>
                        <td>DEPARTURE DATE</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($dataInvoice['departure_date'])) ?></td>
                    </tr>
                    <tr>
                        <td>TERMS OF PAYMENT</td>
                        <td style="vertical-align: top;">:</td>
                        <td><?= $dataInvoice['payment_term'] ?></td>
                    </tr>
                    <tr>
                        <td>NOTIFY PARTY</td>
                        <td>:</td>
                        <td><?= $dataInvoice['notify_party'] ?></td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 12px; ">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 4%; height:2.5%;">NO</th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    DESCRIPTION AND QUANTITY OF PRODUCT / GOODS
                </th>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;
            foreach ($dataListPacking as $key => $detail):
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"><?= $no++ ?></td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <?= $detail['nama_barang_packing'] ?> <br>
                        HS CODE : <?= $detail['hs_code_name'] ?> <br>
                        <?= $detail['keterangan_packing'] ?>
                        <div>
                            <?php if (!empty($detail['size_breakdown'])): ?>
                                <?php
                                // Identify which columns have data
                                $columns_to_show = [];
                                $all_columns = [
                                    'packing' => ['label' => 'Packing', 'width' => '8%'], // tidak pakai qty
                                    'can_dimension' => ['label' => 'Can Dimension', 'width' => '8%'], // tidak pakai qty
                                    'brand_packing' => ['label' => 'Brand', 'width' => '8%'], // tidak pakai qty
                                    'eu_approval_number' => ['label' => 'Eu Approval Number', 'width' => '9%'],
                                    'qty_carton' => ['label' => 'QTY (CARTON)', 'width' => '7%'],
                                    'qty_cans' => ['label' => 'QTY (CANS)', 'width' => '7%'],
                                    'berat_bersih' => ['label' => 'NETTO (KGS)', 'width' => '7%'],
                                    'berat_kotor' => ['label' => 'GROSS (KGS)', 'width' => '7%'],
                                    'vgm' => ['label' => 'VBM (KGS)', 'width' => '7%'],
                                    'drammed' => ['label' => 'DRAMMED (KGS)', 'width' => '7%'],
                                ];

                                // Check which columns have data
                                foreach ($all_columns as $col => $col_data) {
                                    foreach ($detail['size_breakdown'] as $breakdown) {
                                        if (!empty($breakdown[$col])) {
                                            $columns_to_show[$col] = $col_data;
                                            break;
                                        }
                                    }
                                }

                                $show_carton_column = isset($columns_to_show['qty_carton']);
                                $show_cans_column = isset($columns_to_show['qty_cans']);
                                $show_berat_bersih_column = isset($columns_to_show['berat_bersih']);
                                $show_berat_kotor_column = isset($columns_to_show['berat_kotor']);
                                $show_vgm_column = isset($columns_to_show['vgm']);
                                $show_drammed_column = isset($columns_to_show['drammed']);

                                ?>

                                <div style="margin-top: 6px;">
                                    <div style="font-size: 11px; font-weight: bold;">DETAIL PACKING:</div>
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 11px;">
                                        <thead>
                                            <tr style="background-color: #f3f4f6;">
                                                <?php foreach ($columns_to_show as $col => $col_data): ?>
                                                    <?php if ($col != 'qty_carton' && $col != 'qty_cans' && $col != 'berat_bersih' && $col != 'berat_kotor' && $col != 'vgm' && $col != 'drammed'): ?>
                                                        <th style="padding: 3px; border: 1px solid #ddd; width: <?= $col_data['width'] ?>"><?= $col_data['label'] ?></th>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>

                                                <?php if ($show_carton_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%;text-align: center;">QTY CARTONS</th>
                                                <?php endif; ?>

                                                <?php if ($show_cans_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%;text-align: center;">QTY CANS</th>
                                                <?php endif; ?>
                                                <?php if ($show_berat_bersih_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%;text-align: center;">WEIGHT NETTO</th>
                                                <?php endif; ?>
                                                <?php if ($show_berat_kotor_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%;text-align: center;">WEIGHT GROSS</th>
                                                <?php endif; ?>
                                                <?php if ($show_vgm_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%;text-align: center;">WEIGHT VBM</th>
                                                <?php endif; ?>
                                                <?php if ($show_drammed_column): ?>
                                                    <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%;text-align: center;">WEIGHT DRAMMED</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $breakdown_carton = 0;
                                            $breakdown_cans = 0;
                                            $breakdown_berat_bersih = 0;
                                            $breakdown_berat_kotor = 0;
                                            $breakdown_vgm = 0;
                                            $breakdown_drammed = 0;

                                            foreach ($detail['size_breakdown'] as $breakdown):
                                                $breakdown_carton += $breakdown['qty_carton'];
                                                $breakdown_cans += $breakdown['qty_cans'];
                                                $breakdown_berat_bersih += $breakdown['berat_bersih'];
                                                $breakdown_berat_kotor += $breakdown['berat_kotor'];
                                                $breakdown_vgm += $breakdown['vgm'];
                                                $breakdown_drammed += $breakdown['drammed'];

                                            ?>
                                                <tr>
                                                    <?php foreach ($columns_to_show as $col => $col_data): ?>
                                                        <?php if ($col != 'qty_carton' && $col != 'qty_cans' && $col != 'berat_bersih' && $col != 'berat_kotor' && $col != 'vgm' && $col != 'drammed'): ?>
                                                            <td style="padding: 3px; border: 1px solid #ddd;"><?= $breakdown[$col] ?></td>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>

                                                    <?php if ($show_carton_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #ddd; text-align: right;">
                                                            <?= !empty($breakdown['qty_carton']) ? number_format($breakdown['qty_carton'], 2) : '' ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_cans_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #ddd; text-align: right;">
                                                            <?= !empty($breakdown['qty_cans']) ? number_format($breakdown['qty_cans'], 2) : '' ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_berat_bersih_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #ddd; text-align: right;">
                                                            <?= !empty($breakdown['berat_bersih']) ? number_format($breakdown['berat_bersih'], 2) : '' ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_berat_kotor_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #ddd; text-align: right;">
                                                            <?= !empty($breakdown['berat_kotor']) ? number_format($breakdown['berat_kotor'], 2) : '' ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_vgm_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #ddd; text-align: right;">
                                                            <?= !empty($breakdown['vgm']) ? number_format($breakdown['vgm'], 2) : '' ?>
                                                        </td>
                                                    <?php endif; ?>

                                                    <?php if ($show_drammed_column): ?>
                                                        <td style="padding: 3px; border: 1px solid #ddd; text-align: right;">
                                                            <?= !empty($breakdown['drammed']) ? number_format($breakdown['drammed'], 2) : '' ?>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <?php
                                        // Hitung jumlah kolom utama (misalnya dari thead)
                                        $base_columns = count($columns_to_show);

                                        if ($show_carton_column) {
                                            $base_columns -= 1;
                                        }

                                        if ($show_cans_column) {
                                            $base_columns -= 1;
                                        }

                                        if ($show_berat_bersih_column) {
                                            $base_columns -= 1;
                                        }


                                        if ($show_berat_kotor_column) {
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
                                                <td colspan="<?= $base_columns ?>" style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                    TOTAL
                                                </td>

                                                <?php if ($show_carton_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                        <?= $breakdown_carton > 0 ? number_format($breakdown_carton, 2) : '' ?>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if ($show_cans_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                        <?= $breakdown_cans > 0 ? number_format($breakdown_cans, 2) : '' ?>
                                                    </td>
                                                <?php endif; ?>
                                                <?php if ($show_berat_bersih_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_berat_bersih, 2)  ?>
                                                    </td>
                                                <?php endif; ?>
                                                <?php if ($show_berat_kotor_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_berat_kotor, 2) ?>
                                                    </td>
                                                <?php endif; ?>
                                                <?php if ($show_vgm_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_vgm, 2) ?>
                                                    </td>
                                                <?php endif; ?>
                                                <?php if ($show_drammed_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                        <?= number_format($breakdown_drammed, 2) ?>
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
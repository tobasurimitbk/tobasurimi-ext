<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $salesKontrak['sales_contract_no'] ?></title>
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
        <div class="txt-center"><label class="label-header">SALES CONTRACT</label></div>
        <div class="txt-center"><label class="label-header">NO. <?= $salesKontrak['sales_contract_no']; ?></label></div>
        <div class="d-flex flex-column">
            <!-- <div class="txt-left">
                <label class="label-header">DATE: </label>
            </div> -->
            <div class="txt-right po-customer">
                <?php if (!empty($salesKontrak['customer_po_no'])): ?>
                    <label class="label-header">PO NO: <?= $salesKontrak['customer_po_no']; ?></label>
                <?php endif; ?>
                <br>
                <?php if (!empty($salesKontrak['no_container'])): ?>
                    <label class="label-header">CONTAINER: <?= $salesKontrak['no_container']; ?></label>
                <?php endif; ?>

                <?php $no = 1; ?>
                <br>
                <?php foreach ($revisionList as $r): ?>
                    <label class="label-header">REV: <?= $no ?>: <?= date('d/m/Y', strtotime($r['date_revision'])) ?></label> <br>
                    <?php $no++ ?>
                <?php endforeach; ?>
            </div>

        </div>
        <div class="d-flex flex-column" style="line-height: 1;">
            <table>
                <tr>
                    <td>
                        <label class="label-header">DATE</label>
                    </td>
                    <td>:</td>
                    <td>
                        <label class="label-header"><?= strtoupper(date('F d, Y', strtotime($salesKontrak['createdAt']))); ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="label-header">SELLER</label>
                    </td>
                    <td>:</td>
                    <td>
                        <label class="label-header"> <?= strtoupper(str_ireplace(', Tbk', '', $company['holding_company'])) ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="label-header">BANK</label>
                    </td>
                    <td>:</td>
                    <td>
                        <label class="label-header"> <?= $salesKontrak['nama_bank']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="label-header">SWIFT CODE</label>
                    </td>
                    <td>:</td>
                    <td>
                        <label class="label-header"> <?= $salesKontrak['kode_bank']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="label-header">ACCOUNT #</label>
                    </td>
                    <td>:</td>
                    <td>
                        <label class="label-header"> <?= $salesKontrak['no_rekening']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="label-header">BENEFICIARY </label>
                    </td>
                    <td>:</td>
                    <td>
                        <label class="label-header"> <?= $salesKontrak['atas_nama'] ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="label-header">BUYER</label>
                    </td>
                    <td>:</td>
                    <td>
                        <label class="label-header"> <?= $salesKontrak['customer_name']; ?></label>
                    </td>
                </tr>
                <?php if (!empty($salesKontrak['address'])): ?>
                    <tr>
                        <td>
                            <label class="label-header">ADDRESS</label>
                        </td>
                        <td>:</td>
                        <td>
                            <label class="label-header"> <?= $salesKontrak['address']; ?></label>
                        </td>
                    </tr>
                <?php endif ?>

            </table>

        </div>
        <div class="mt-1 justify-content-center">
            <div class="label-header" style="color: red;">
                <?= nl2br(htmlspecialchars($salesKontrak['banking_information'])) ?>
            </div>
        </div>

        <div class="mt-1">
            <div class="label-header" style="font-size:12.2px;">THIS SALES CONTRACT IS MADE BY AND BETWEEN THE BUYER AND SELLER, WHEREBY THE BUYER AGREES TO PURCHASE AND THE SELLER AGREES TO SELL THE UNDER MENTIONED COMMODITIES AS PER THE TERMS AND CONDITIONS STIPULATED BELOW:</div>
        </div>
    </div>
    <div class="header">
        <div class="mt-1 txt-left"><label class="label-header"> I. DESCRIPTION OF GOODS </label></div>
    </div>
    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 11px;">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 4%;">NO</th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    PRODUCT
                    <span style="float: right;">
                        <!-- TOTAL (<?= $salesKontrak['mata_uang'] ?>) <br> -->
                        <?= $salesKontrak['tipe_harga'] . " " . ($salesKontrak['tipe_harga'] == "FOB" ? $salesKontrak['loading_port'] : $salesKontrak['dicharge_port']) ?>
                    </span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_qty = 0;
            $total_amount = 0;
            $total_persen = 0;
            $no = 1;

            // Buat array untuk mengelompokkan berdasarkan satuan
            $groupBySatuan = [];
            foreach ($salesKontrakdetail as $detail) {
                if (!empty($detail['size_breakdown'])) {
                    foreach ($detail['size_breakdown'] as $breakdown) {
                        $satuan = $breakdown['satuan_size_code'];
                        if (!isset($groupBySatuan[$satuan])) {
                            $groupBySatuan[$satuan] = [
                                'qty' => 0,
                                'total' => 0
                            ];
                        }
                        $groupBySatuan[$satuan]['qty'] += $breakdown['qty'];
                        $groupBySatuan[$satuan]['total'] += $breakdown['total'];
                    }
                }
            }


            $totalSalesKontrakdetail = count($salesKontrakdetail);
            $currentItemSaleskontrakdetail = 0;
            foreach ($salesKontrakdetail as $key => $detail) {
                $currentItemSaleskontrakdetail++;
                $total_amount = $total_amount + formatter($detail["total_harga"], "STR_TO_FLOAT");
                $total_qty += $detail['qty'];
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"><?= $no++ ?></td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <div style="font-weight: bold; font-size: 11px;"><?= $detail["nama_barang"]; ?></div>
                        <div style="font-size: 11px; margin-top: 4px; line-height: 1;">
                            <table style="margin-left: -3px;">
                                <?php if (!empty($detail['species'])): ?>
                                    <tr>
                                        <td style="font-weight: bold; vertical-align: top;">SPECIES</td>
                                        <td style="vertical-align: top;">:</td>
                                        <td style="vertical-align: top;"><?= trim($detail['species']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($detail['specs'])): ?>
                                    <tr>
                                        <td style="font-weight: bold; vertical-align: top;">SPECS</td>
                                        <td style="vertical-align: top;">:</td>
                                        <td style="vertical-align: top;"><?= trim($detail['specs']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($detail['brand'])): ?>
                                    <tr>
                                        <td style="font-weight: bold;vertical-align: top;">BRAND</td>
                                        <td style="vertical-align: top;">:</td>
                                        <td style="vertical-align: top;"><?= trim($detail['brand']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (!empty($detail['kemasan'])): ?>
                                    <tr>
                                        <td style="font-weight: bold;vertical-align: top;">PACKING</td>
                                        <td style="vertical-align: top;">:</td>
                                        <td style="vertical-align: top;"><?= trim($detail['kemasan']) ?></td>
                                    </tr>
                                <?php endif; ?>

                            </table>

                        </div>

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
                                <div style="font-size: 11px; font-weight: bold;">SIZE & BREAKDOWN:</div>
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
                                            <th style="padding: 3px; border: 1px solid #ddd; width: 4.5%; text-align: center;">UNIT PRICE (<?= $salesKontrak['mata_uang'] . "/" . $satuanQty ?>)</th>
                                            <th style="padding: 3px; border: 1px solid #ddd; width: 5%; text-align: center;">TOTAL AMOUNT (<?= $salesKontrak['mata_uang'] ?>)</th>
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
                                                $total_persen += $breakdown['persen'];
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

                                    <?php if (count($salesKontrakdetail) > 1): ?>
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
                                    <?php endif; ?>

                                </table>
                            </div>
                        <?php endif; ?>

                    </td>
                </tr>

            <?php } ?>

            <?php
            // Calculate total adjustments
            $total_adjustments = 0;

            // Deductions (always subtracted)
            if ($salesKontrak['royalty_price'] > 0) {
                $total_adjustments -= $salesKontrak['royalty_price'];
            }
            if ($salesKontrak['rebate_price'] > 0) {
                $total_adjustments -= $salesKontrak['rebate_price'];
            }
            if ($salesKontrak['can_deduction_price'] > 0) {
                $total_adjustments -= $salesKontrak['can_deduction_price'];
            }

            // Freight (always added)
            if ($salesKontrak['estimated_freight_price'] > 0) {
                $total_adjustments += $salesKontrak['estimated_freight_price'];
            }

            // Handle others (can be positive or negative)
            $others_value = 0;
            if ($salesKontrak['others_price'] > 0) {
                $others_value = (float) $salesKontrak['others_price'];
                if ($salesKontrak['others_type'] == "PLUS") {
                    $total_adjustments += $others_value;
                } else {
                    $total_adjustments -= $others_value;
                }
            }

            // Calculate final amount
            $grand_total = $total_amount + $total_adjustments;
            ?>

            <!-- Quantity Row -->
            <!-- <?php if (count($groupBySatuan) == 1): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                        <span style="float: left;">TOTAL QTY</span>
                        <?= number_format($total_qty, 2) ?>
                    </td>
                </tr>
            <?php endif; ?> -->
            <?php if ($total_adjustments != 0): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                        <span style="float: left;">TOTAL</span>
                        <?= number_format($total_amount, 2) ?>
                    </td>
                </tr>
            <?php endif; ?>

            <!-- Royalty -->
            <?php if ($salesKontrak['royalty_price'] > 0): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right; color:red;">
                        <span style="float: left;"><?= $salesKontrak['royalty'] ?></span>
                        ( <?= number_format($salesKontrak['royalty_price'], 2) ?> )
                    </td>
                </tr>
            <?php endif; ?>

            <!-- Rebate -->
            <?php if ($salesKontrak['rebate_price'] > 0): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right; color:red;">
                        <span style="float: left;"><?= $salesKontrak['rebate'] ?></span>
                        ( <?= number_format($salesKontrak['rebate_price'], 2) ?> )
                    </td>
                </tr>
            <?php endif; ?>

            <!-- Can Deduction -->
            <?php if ($salesKontrak['can_deduction_price'] > 0): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right; color:red;">
                        <span style="float: left;"><?= $salesKontrak['can_deduction'] ?></span>
                        ( <?= number_format($salesKontrak['can_deduction_price'], 2) ?> )
                    </td>
                </tr>
            <?php endif; ?>

            <!-- Freight -->
            <?php if ($salesKontrak['estimated_freight_price'] > 0): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                        <span style="float: left;"><?= $salesKontrak['estimated_freight'] ?></span>
                        <?= number_format($salesKontrak['estimated_freight_price'], 2) ?>
                    </td>
                </tr>
            <?php endif; ?>

            <!-- Others (with +/- sign) -->
            <?php if ($salesKontrak['others_price'] > 0): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                        <span style="float: left;"><?= $salesKontrak['others'] ?></span>
                        <?= $salesKontrak['others_type'] == "MINUS" ? "( " . number_format($salesKontrak['others_price'], 2) . " )" : number_format($salesKontrak['others_price'], 2) ?>
                    </td>
                </tr>
            <?php endif; ?>

            <!-- Percentage Row (if exists) -->
            <!-- <?php if ($total_persen > 0): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                        <span style="float: left;">TOTAL %</span>
                        <?= number_format($total_persen, 2) . " %" ?>
                    </td>
                </tr>
            <?php endif; ?> -->

            <!-- Final Amount Row -->
            <tr style="font-weight: bold; background-color: #e9ecef; font-size: 10px; line-height: 1;">
                <td style="padding: 4px; border: 1px solid #ddd;"></td>
                <td style="padding: 4px; border: 1px solid #ddd;">
                    <table style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                        <tr style="vertical-align: middle;">
                            <!-- Kolom 1: GRAND TOTAL Label -->
                            <td style="width: 35%; text-align: left; vertical-align: middle; white-space: nowrap;">
                                <span style="margin-left: -3px;">
                                    GRAND TOTAL <?= !empty($salesKontrak['total_container']) ? "(" . $salesKontrak['total_container'] . ")" : "" ?>
                                </span>
                            </td>

                            <!-- Kolom 2: Tabel Satuan -->
                            <td style="width: 30%; text-align: center;">
                                <?php if ($currentItemSaleskontrakdetail === $totalSalesKontrakdetail): ?>
                                    <?php if (!empty($groupBySatuan)) : ?>
                                        <table style="width: auto; margin: 0 auto; border-collapse: collapse; font-size: 10px;">
                                            <tbody>
                                                <tr>
                                                    <?php
                                                    $grand_total_qty = 0;
                                                    foreach ($groupBySatuan as $satuan => $data):
                                                        $grand_total_qty += $data['qty'];
                                                    ?>
                                                        <td style="padding: 0 2px; text-align: right; width: 100px;">
                                                            <?= number_format($data['qty'], 2) ?> <?= $satuan ?>
                                                        </td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <!-- Kolom 3: Nilai Grand Total -->
                            <td style="width: 35%; text-align: right; vertical-align: middle; white-space: nowrap;">
                                (<?= $salesKontrak['mata_uang'] ?>) <?= number_format($grand_total, 2) ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </tbody>
    </table>
    <div style="margin-top: 5px;line-height:1;">
        <table style="border-spacing: 0 4px; width: 100%;">
            <tbody>
                <?php
                $counter = 2;

                if ($grand_total > 0): ?>
                    <tr class="label-header">
                        <td style="width: 25px;"><?= strtoupper(numToRoman($counter++)) ?>.</td>
                        <td style="width: 180px;">TOTAL AMOUNT (<?= $salesKontrak['mata_uang'] ?>)</td>
                        <td style="width: 10px;">:</td>
                        <td><?= number_format($grand_total, 2) ?> (<?= strtoupper(terbilangInggris($grand_total)) . " ONLY" ?>)</td>
                    </tr>
                <?php endif; ?>

                <?php if (!empty($salesKontrak['tolerance'])): ?>
                    <tr class="label-header">
                        <td><?= strtoupper(numToRoman($counter++)) ?>.</td>
                        <td>TOLERANCE</td>
                        <td>:</td>
                        <td><?= $salesKontrak['tolerance'] ?></td>
                    </tr>
                <?php endif ?>

                <?php if (!empty($salesKontrak['shipment_date'])): ?>
                    <tr class="label-header">
                        <td><?= strtoupper(numToRoman($counter++)) ?>.</td>
                        <td>ESTIMATED SHIPMENT DATE</td>
                        <td>:</td>
                        <td><?= $salesKontrak['shipment_date'] ?></td>
                    </tr>
                <?php endif ?>

                <?php if (!empty($salesKontrak['loading_port'])): ?>
                    <tr class="label-header">
                        <td><?= strtoupper(numToRoman($counter++)) ?>.</td>
                        <td>PORT OF LOADING</td>
                        <td>:</td>
                        <td><?= $salesKontrak['loading_port'] ?></td>
                    </tr>
                <?php endif ?>

                <?php if (!empty($salesKontrak['dicharge_port'])): ?>
                    <tr class="label-header">
                        <td><?= strtoupper(numToRoman($counter++)) ?>.</td>
                        <td>PORT OF DISCHARGE</td>
                        <td>:</td>
                        <td><?= $salesKontrak['dicharge_port'] ?></td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>

    <!-- Mulai blok yang harus utuh di halaman yang sama -->
    <div class="ttd-section" style="margin-top: -5px; line-height:1;">
        <table style="border-spacing: 0 4px; width: 100%;">
            <tbody>
                <?php if (!empty($salesKontrak['payment_term'])): ?>
                    <tr class="label-header">
                        <td style="width: 25px; vertical-align: top;"><?= strtoupper(numToRoman($counter++)) ?>.</td>
                        <td style="width: 180px; vertical-align: top;">PAYMENT TERM</td>
                        <td style="width: 10px; vertical-align: top;">:</td>
                        <td><?= nl2br(htmlspecialchars($salesKontrak['payment_term'])) ?></td>
                    </tr>
                <?php endif ?>

                <?php if (!empty($salesKontrak['shipment_insurance'])): ?>
                    <tr class="label-header">
                        <td><?= strtoupper(numToRoman($counter++)) ?>.</td>
                        <td>INSURANCE</td>
                        <td>:</td>
                        <td><?= $salesKontrak['shipment_insurance'] ?></td>
                    </tr>
                <?php endif ?>

                <?php if (!empty($salesKontrak['documents_required'])): ?>
                    <tr class="label-header">
                        <td><?= strtoupper(numToRoman($counter++)) ?>.</td>
                        <td>DOCUMENT REQUIRED</td>
                        <td>:</td>
                        <td><?= nl2br(htmlspecialchars($salesKontrak['documents_required'])) ?></td>
                    </tr>
                <?php endif ?>

                <?php if (!empty($salesKontrak['special_instructions'])): ?>
                    <tr class="label-header">
                        <td style="vertical-align: top;"><?= strtoupper(numToRoman($counter++)) ?>.</td>
                        <td style="vertical-align: top;">ADDITIONAL CLAUSES</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;"><?= nl2br(htmlspecialchars($salesKontrak['special_instructions'])) ?></td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td colspan="4">
                        <br>
                        <label class="label-header">
                            FOR THOSE ITEMS WHICH ARE NOT COVERED IN THIS CONTRACT, BOTH PARTIES WILL NEGOTIATE AND COME TO COMPROMISE.
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>

        <!-- SIGNATURE TABLE -->
        <table class="mt-1 sign-table border-collapse" style="margin-top: -18px; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 350px;">
                        <label class="label-header">THE BUYER,</label>
                    </th>
                    <th>
                        <label class="label-header">THE SELLER,</label>
                    </th>
                </tr>
                <tr>
                    <th style="width: 350px;">
                        <label class="label-header"><?= $customer != null ? $customer['name'] : ''; ?></label>
                    </th>
                    <th>
                        <label class="label-header"><?= strtoupper(str_ireplace(', Tbk', '', $company['holding_company'])) ?></label>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: 70px;"></td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        &nbsp;
                        <div style="border-top: 1px solid #000; width: 80%;">
                        </div>
                    </td>
                    <td>
                        <label class="label-header"><?= $salesKontrak['signature_by'] ?></label>
                        <div style="border-top: 1px solid #000; width: 80%;"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <?php
    // Helper function to convert numbers to Roman numerals
    function numToRoman($num)
    {
        $n = intval($num);
        $result = '';

        $lookup = [
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];

        foreach ($lookup as $roman => $value) {
            $matches = intval($n / $value);
            $result .= str_repeat($roman, $matches);
            $n = $n % $value;
        }

        return $result;
    }
    ?>
</body>

</html>
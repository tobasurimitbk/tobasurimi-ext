<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dataSO->sales_order_export_no ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
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
            text-align: center !important;
        }

        .item-table tr td {
            text-align: center !important;
        }

        .item-table tbody tr {
            border-top: 1px solid !important;
        }

        .mt-025 {
            margin-top: 0.25rem;
        }

        .mt-1 {
            margin-top: 1rem;
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
            font-size: 10px;
        }

        .label-child {
            font-weight: bold;
            font-size: 8px;
        }

        .po-customer {
            margin-top: -20px !important;
        }

        .justify-content-center {
            justify-content: center !important;
        }

        .label {
            font-size: 12px;
        }

        * {
            font-family: 'DejaVu Serif' !important;
        }

        p {
            margin-top: -1.5px !important;
        }

        ul {
            margin-top: -1.5px !important;
        }
    </style>

    <?php if ($displayPrice == "false"): ?>
        <style>
            .price {
                display: none;
                visibility: hidden;
            }
        </style>

    <?php endif; ?>
    <style>
        .keep-together {
            page-break-inside: avoid;
        }
    </style>
    <style>
        .section-border {
            width: 100%;
            border: 1px solid #000;
            font-size: 12px;
            padding: 6px;
            position: relative;
            page-break-inside: auto;
        }

        /* Tambahkan border bawah manual setiap page break */
        .section-border::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            border-bottom: 1px solid #000;
        }

        /* Trik buat munculin border atas di halaman baru */
        .section-border>* {
            page-break-inside: avoid;
        }
    </style>

</head>

<body>
    <?php if (!empty($dataSO) && !empty($dataSODetail)) { ?>

        <div class="header" style="margin-top: -50px;">
            <div class="txt-center">
                <h3>
                    <span style="margin-top: -20px;">
                        <?= strtoupper(str_ireplace(', Tbk', '', $company['holding_company'])) . " (" . $subCompany . ")" ?> <br>
                        ORDER FORM <?= $dataSO->container ?>
                        </br>
                </h3>
            </div>

            <table style="width: 100%; margin-top:-10px;line-height: 0.8 !important;">
                <tr>
                    <td>
                        <table style="width: 100%; font-size:12px;">
                            <tr>
                                <td>CONSIGNEE</td>
                                <td>:</td>
                                <td><?= $dataSO->customer_name ?></td>
                            </tr>
                            <?php if (!empty($dataSO->tax_id)): ?>
                                <tr>
                                    <td>TAX ID#</td>
                                    <td>:</td>
                                    <td><?= $dataSO->tax_id ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td>DESTINATION</td>
                                <td>:</td>
                                <td><?= $dataSO->destination ?></td>
                            </tr>
                            <tr>
                                <td>DEADLINE</td>
                                <td>:</td>
                                <td><?= $dataSO->deadline ?></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <?php
                        $panjangKolom = 0;
                        $panjangKolomOrder = 0;

                        if (strlen($dataSO->sales_order_export_no) >= 34) {
                            $panjangKolom = 100;
                            $panjangKolomOrder = 190;
                        } else {
                            $panjangKolom = 80;
                            $panjangKolomOrder = 0;
                        }
                        ?>
                        <table style="width: <?= $panjangKolom ?>%; font-size:12px; float:right;" border="0">
                            <tr>
                                <td style="text-align: left; width:3px;">ORDER</td>
                                <td style="width: 5px; text-align: right;">:</td>
                                <td style="text-align: left; width:<?= $panjangKolomOrder ?>px;"><?= $dataSO->sales_order_export_no ?></td>
                            </tr>

                            <?php if (!empty($dataSO->po_no)): ?>
                                <tr>
                                    <td style="white-space: nowrap; text-align: left;">PO NO</td>
                                    <td style="text-align: right;">:</td>
                                    <td style="text-align: left;"><?= $dataSO->po_no ?></td>
                                </tr>
                            <?php endif; ?>

                            <tr>
                                <td style="white-space: nowrap; text-align: left;">DATE</td>
                                <td style="text-align: right;">:</td>
                                <td style="text-align: left;"><?= strtoupper($tanggal_of) ?></td>
                            </tr>

                            <?php $no = 1;
                            foreach ($dataSalesOrderRevision as $d): ?>
                                <tr style="background-color:#dee2e6">
                                    <td style="white-space: nowrap; text-align: left;">REVISED-<?= $no++ ?></td>
                                    <td style="text-align: right;">:</td>
                                    <td style="text-align: left;"><?= date('d-M-Y', strtotime($d['date_revision'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>


                </tr>
            </table>


            <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 12px;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 4%;">NO</th>
                        <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                            DESCRIPTION OF GOODS
                            <span style="float: right; text-align:right;">
                                <!-- PRICE (<?= $dataSO->mata_uang ?>) <br> -->
                                <?= $dataSO->tipe_harga . " " . ($dataSO->tipe_harga == "FOB" ? $dataSO->loading_port : $dataSO->dicharge_port) ?>
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
                    foreach ($dataSODetail['salesContractDetailList'] as $detail) {
                        if (!empty($detail['size_breakdown'])) {
                            foreach ($detail['size_breakdown'] as $breakdown) {
                                $satuan = $breakdown['satuan_size_code'];
                                if (!isset($groupBySatuan[$satuan])) {
                                    $groupBySatuan[$satuan] = [
                                        'qty_input' => 0,
                                        'total_input' => 0
                                    ];
                                }
                                $groupBySatuan[$satuan]['qty_input'] += $breakdown['qty_input'];
                                $groupBySatuan[$satuan]['total_input'] += $breakdown['total_input'];
                            }
                        }
                    }


                    $totalSalesKontrakdetail = count($dataSODetail['salesContractDetailList']);
                    $currentItemSaleskontrakdetail = 0;


                    foreach ($dataSODetail['salesContractDetailList'] as $key => $detail) {
                        $currentItemSaleskontrakdetail++;
                        $total_amount = $total_amount + formatter($detail["total_input"], "STR_TO_FLOAT");
                        $total_qty += $detail['qty_input'];
                    ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"><?= $no++ ?></td>
                            <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                                <div style="font-weight: bold; font-size: 12px;">
                                    <span style="float: left;">
                                        <?= $detail["barang_name"]; ?>
                                    </span>
                                    <?php if (!empty($detail['divisi_name'])): ?>
                                        <span style="float: right;">
                                            DEPT. <?= $detail["divisi_name"]; ?>
                                        </span>
                                    <?php endif; ?>
                                </div><br>
                                <div style="font-size: 12px; margin-top: 4px; line-height: 0.8;">
                                    <table style="margin-left: -3px;">
                                        <?php if (!empty($detail['species'])): ?>
                                            <tr>
                                                <td style="font-weight: bold; vertical-align:top;">SPECIES</td>
                                                <td style="vertical-align: top;">:</td>
                                                <td style="vertical-align: top;"><?= trim($detail['species']) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($detail['specs'])): ?>
                                            <tr>
                                                <td style="font-weight: bold;vertical-align: top;">SPECS</td>
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
                                        <?php if (!empty($detail['packing'])): ?>
                                            <tr>
                                                <td style="font-weight: bold;vertical-align: top;">PACKING</td>
                                                <td style="vertical-align: top;">:</td>
                                                <td style="vertical-align: top;"><?= trim($detail['packing']) ?></td>
                                            </tr>
                                        <?php endif; ?>

                                    </table>
                                </div>

                                <?php if (!empty($detail['size_breakdown'])): ?>
                                    <?php
                                    // Identify which columns have data
                                    $columns_to_show = [];
                                    $all_columns = [
                                        'size' => ['label' => 'SIZE', 'width' => '8%'],
                                        'grade' => ['label' => 'GRADE', 'width' => '13%'],
                                        'packing' => ['label' => 'PACKING', 'width' => '8%'],
                                        'can' => ['label' => 'QTY (CAN)', 'width' => '7%'],
                                        'cased' => ['label' => 'QTY (CASE)', 'width' => '7%'],
                                        'case' => ['label' => 'QTY (CASE)', 'width' => '7%'],
                                        'kg' => ['label' => 'QTY (KG)', 'width' => '7%'],
                                        'lb' => ['label' => 'QTY (LB)', 'width' => '7%'],
                                        'inner_box' => ['label' => 'INNER', 'width' => '8%'],
                                        'pc' => ['label' => 'QTY (PC)', 'width' => '7%'],
                                        'bag' => ['label' => 'QTY (BAG)', 'width' => '7%'],
                                        'cup' => ['label' => 'QTY (CUP)', 'width' => '6%'],
                                        'persen' => ['label' => '%', 'width' => '2%'],
                                        'remark' => ['label' => 'REMARK', 'width' => '10%'],
                                        'palet' => ['label' => 'PALLET', 'width' => '10%']

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

                                    <div style="margin-top: 0px;">
                                        <div style="font-size: 12px; font-weight: bold;">SIZE & BREAKDOWN:</div>
                                        <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 11px;">
                                            <thead>
                                                <tr style="background-color: #f3f4f6;">
                                                    <?php foreach ($columns_to_show as $col => $col_data): ?>
                                                        <?php if ($col != 'persen' && $col != 'cased'): ?>
                                                            <th style=" border: 1px solid #ddd; width: <?= $col_data['width'] ?>"><?= $col_data['label'] ?></th>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>

                                                    <?php if ($show_cased_column): ?>
                                                        <th style=" border: 1px solid #ddd; width: 4.5%;text-align: center;">QTY (CASE)</th>
                                                    <?php endif; ?>

                                                    <?php if ($show_persen_column): ?>
                                                        <th style=" border: 1px solid #ddd; width: 4.5%;text-align: right;">%</th>
                                                    <?php endif; ?>

                                                    <th style=" border: 1px solid #ddd; width: 4.5%; text-align: center;">QTY (<?= $satuanQty ?>)</th>
                                                    <th style=" border: 1px solid #ddd; width: 8%; text-align: center;" class="price">UNIT PRICE <br> (<?= $dataSO->mata_uang . "/" . $satuanQty ?>)</th>
                                                    <th style=" border: 1px solid #ddd; width: 8%; text-align: center;" class="price">TOTAL AMOUNT <br> (<?= $dataSO->mata_uang ?>)</th>
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
                                                    $breakdown_qty += $breakdown['qty_input'];
                                                    $breakdown_total += $breakdown['total_input'];
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
                                                                <td style=" border: 1px solid #ddd;">
                                                                    <?= $breakdown[$col] ?> <br>
                                                                    <?php if (!empty($breakdown["note_" . $col])): ?>
                                                                        (<?= $breakdown["note_" . $col] ?>)
                                                                    <?php endif; ?>
                                                                </td>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>

                                                        <?php if ($show_cased_column): ?>
                                                            <td style=" border: 1px solid #ddd; text-align: right;">
                                                                <?= !empty($breakdown['cased']) ? number_format($breakdown['cased'], 2) : '' ?>
                                                            </td>
                                                        <?php endif; ?>

                                                        <?php if ($show_persen_column): ?>
                                                            <td style=" border: 1px solid #ddd; text-align: right;">
                                                                <?= !empty($breakdown['persen']) ? number_format($breakdown['persen'], 2) . " %" : '' ?>
                                                            </td>
                                                        <?php endif; ?>

                                                        <td style=" border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['qty_input'], 2) ?></td>
                                                        <td style=" border: 1px solid #ddd; text-align: right;" class="price"><?= number_format($breakdown['harga'], 2) ?></td>
                                                        <td style=" border: 1px solid #ddd; text-align: right;" class="price"><?= number_format($breakdown['total_input'], 2) ?></td>
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
                                            <?php if (count($detail['size_breakdown']) > 1): ?>
                                                <tfoot>
                                                    <tr style="background-color: #e9ecef;">
                                                        <td colspan="<?= $base_columns ?>" style="border: 1px solid #ddd; text-align: right; font-weight: bold;">TOTAL</td>

                                                        <?php if ($show_cased_column): ?>
                                                            <td style=" border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                                <?= $breakdown_cased > 0 ? number_format($breakdown_cased, 2) : '' ?>
                                                            </td>
                                                        <?php endif; ?>

                                                        <?php if ($show_persen_column): ?>
                                                            <td style=" border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                                <?= $breakdown_persen > 0 ? number_format($breakdown_persen, 2) . " %" : '' ?>
                                                            </td>
                                                        <?php endif; ?>

                                                        <td style=" border: 1px solid #ddd; text-align: right; font-weight: bold;"><?= number_format($breakdown_qty, 2)  ?></td>
                                                        <?php if ($displayPrice == "true"): ?>
                                                            <td style=" border: 1px solid #ddd; text-align: right; font-weight: bold;"></td>
                                                            <td style=" border: 1px solid #ddd; text-align: right; font-weight: bold;" class="price"><?= number_format($breakdown_total, 2) ?></td>
                                                        <?php endif; ?>
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
                    if ($dataSODetail['royaltyPriceFinal'] > 0) {
                        $total_adjustments -= $dataSODetail['royaltyPriceFinal'];
                    }
                    if ($dataSODetail['rebatePriceFinal'] > 0) {
                        $total_adjustments -= $dataSODetail['rebatePriceFinal'];
                    }
                    if ($dataSODetail['canDeductionPriceFinal'] > 0) {
                        $total_adjustments -= $dataSODetail['canDeductionPriceFinal'];
                    }

                    // Freight (always added)
                    if ($dataSODetail['estimatedFreightPriceFinal'] > 0) {
                        $total_adjustments += $dataSODetail['estimatedFreightPriceFinal'];
                    }

                    // Handle others untuk sales kontrak (can be positive or negative)
                    $others_value = 0;
                    if ($dataSODetail['othersPriceFinal'] > 0) {
                        $others_value = (float) $dataSODetail['othersPriceFinal'];
                        if ($dataSODetail['othersTypeFinal'] == "PLUS") {
                            $total_adjustments += $others_value;
                        } else {
                            $total_adjustments -= $others_value;
                        }
                    }

                    // Handle others untuk order form (can be positive or negative)
                    // $others_value_so = 0;
                    // if ($dataSO->additional_detail_price > 0) {
                    //     $others_value = (float) $dataSO->additional_detail_price;
                    //     if ($dataSO->additional_detail_type == "PLUS") {
                    //         $total_adjustments += $others_value;
                    //     } else {
                    //         $total_adjustments -= $others_value;
                    //     }
                    // }

                    foreach ($dataSalesExportAdditional as $d) {
                        if ($d['additional_detail_type'] == "PLUS") {
                            $total_adjustments += $d['additional_detail_price'];
                        } else {
                            $total_adjustments -= $d['additional_detail_price'];
                        }
                    }


                    // Palet fumigation
                    if ($dataSO->palet_fumigation > 0) {
                        $total_adjustments += $dataSO->palet_fumigation_price;
                    }

                    // Calculate final amount
                    $grand_total = $total_amount + $total_adjustments;
                    ?>


                    <?php if ($displayPrice == "true" && $totalSalesKontrakdetail > 1 && $total_adjustments > 0): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                                <span style="float: left;">TOTAL</span>
                                <?= number_format($total_amount, 2) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Royalty -->
                    <?php if ($dataSODetail['royaltyPriceFinal'] > 0): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;" class="price">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; color:red;">
                                <span style="float: left;"><?= $dataSO->royalty ?></span>
                                ( <?= number_format($dataSODetail['royaltyPriceFinal'], 2) ?> )
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Rebate -->
                    <?php if ($dataSODetail['rebatePriceFinal'] > 0): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;" class="price">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; color:red;">
                                <span style="float: left;"><?= $dataSO->rebate ?></span>
                                ( <?= number_format($dataSODetail['rebatePriceFinal'], 2) ?> )
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Can Deduction -->
                    <?php if ($dataSODetail['canDeductionPriceFinal'] > 0): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;" class="price">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; color:red;">
                                <span style="float: left;"><?= $dataSO->can_deduction ?></span>
                                ( <?= number_format($dataSODetail['canDeductionPriceFinal'], 2) ?> )
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Freight -->
                    <?php if ($dataSODetail['estimatedFreightPriceFinal'] > 0): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;" class="price">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                                <span style="float: left;"><?= $dataSO->estimated_freight ?></span>
                                <?= number_format($dataSODetail['estimatedFreightPriceFinal'], 2) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Others (with +/- sign) -->
                    <?php if ($dataSODetail['othersPriceFinal'] > 0): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;" class="price">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                                <span style="float: left;"><?= $dataSODetail['salesContract']['others'] ?></span>
                                <?= $dataSODetail['othersPriceFinal'] == "PLUS" ? "( " . number_format($dataSODetail['othersPriceFinal'], 2) . " )" : number_format($dataSODetail['othersPriceFinal'], 2) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Palet & Fumigation -->
                    <?php if ($dataSO->palet_fumigation > 0): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;" class="price">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                                <span style="float: left;"><?= $dataSO->palet_fumigation ?></span>
                                <?= number_format($dataSO->palet_fumigation_price, 2) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Additional Details -->
                    <?php foreach ($dataSalesExportAdditional as $d): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;" class="price">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="<?= $d['additional_detail_type'] == "MINUS" ? 'padding: 6px; border: 1px solid #ddd; text-align: right; color:red;' : 'padding: 6px; border: 1px solid #ddd; text-align: right;' ?>">
                                <span style="float: left;"><?= $d['additional_detail'] ?></span>
                                <?= $d['additional_detail_type'] == "MINUS" ? "( " . number_format($d['additional_detail_price'], 2) . " )" : number_format($d['additional_detail_price'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Final Amount Row -->
                    <tr style="font-weight: bold; background-color: #e9ecef; font-size: 11px;">
                        <td style="padding: 6px; border: 1px solid #ddd;"></td>
                        <td style="padding: 6px; border: 1px solid #ddd;">
                            <table style="width: 100%; table-layout: fixed;">
                                <tr style="vertical-align: middle;">
                                    <!-- Kolom 1: GRAND TOTAL Label -->
                                    <td style="width: 25%; text-align: left; vertical-align: middle; white-space: nowrap;">
                                        <span style="margin-left: -3px; font-size:11px;">
                                            GRAND TOTAL <?= !empty($salesKontrak['total_container']) ? "(" . $salesKontrak['total_container'] . ")" : "" ?>
                                        </span>
                                    </td>

                                    <!-- Kolom 2: Tabel Satuan -->
                                    <td style="width: auto; text-align: center; vertical-align: middle;">
                                        <?php if ($currentItemSaleskontrakdetail === $totalSalesKontrakdetail): ?>
                                            <?php if (!empty($groupBySatuan)) : ?>
                                                <table style="width: auto; margin: 0 auto; border-collapse: collapse; font-size: 11px;">
                                                    <!-- <thead>
                                                        <tr style="background-color: #f3f4f6;">
                                                            <?php foreach ($groupBySatuan as $satuan => $data): ?>
                                                                <th style="padding: 5px; border: 1px solid #ddd; text-align: right;"><?= $satuan ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead> -->
                                                    <tbody>
                                                        <tr>
                                                            <?php
                                                            $grand_total_qty = 0;
                                                            foreach ($groupBySatuan as $satuan => $data):
                                                                $grand_total_qty += $data['qty_input'];
                                                            ?>
                                                                <td style="padding: 0 2px; text-align: right; width: 100px;">
                                                                    <?= number_format($data['qty_input'], 2) ?> <?= $satuan ?>
                                                                </td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Kolom 3: Nilai Grand Total -->
                                    <td style="width: 25%; text-align: right; vertical-align: middle; white-space: nowrap;font-size:12px;" class="price">
                                        (<?= $dataSO->mata_uang ?>) <?= number_format($grand_total, 2) ?>
                                    </td>

                                </tr>

                            </table>
                        </td>
                    </tr>

                    <?php if (!empty($dataSO->commision)): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 12px;">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd;">
                                <?= $dataSO->commision ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($dataSO->freight != ""): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 12px;">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd;">
                                <b>
                                    <?= $dataSO->freight ?>
                                </b>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($dataSO->additional != ""): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 12px;">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd;">
                                <?= $dataSO->additional ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($dataSO->additional_2 != ""): ?>
                        <tr style="font-weight: bold; background-color: #e9ecef; font-size: 12px;">
                            <td style="padding: 6px; border: 1px solid #ddd;"></td>
                            <td style="padding: 6px; border: 1px solid #ddd;">
                                <?= $dataSO->additional_2 ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>

            <div border="1" style="width: 100%; border: 1px solid black; border-collapse: collapse; font-size:12px;">
                <div class="" style="margin-bottom:-3px;">
                    <b>
                        DOCS & CERTIFICATE REQUIRED
                    </b>
                </div>

                <hr>
                <?php if ($dataSO->document_required != ""): ?>
                    <b>
                        - DOCUMENT REQUIRED
                    </b>
                    <?= $dataSO->document_required ?>
                <?php endif; ?>
                <?php if ($dataSO->payment_term != ""): ?>
                    <b>
                        - PAYMENT TERM
                    </b>
                    <br>
                    <?= $dataSO->payment_term ?>
                <?php endif; ?>

                <?php if ($dataSO->shipment_an != ""): ?>
                    <b>
                        - SHIPMENT A/N
                    </b>
                    <br>
                    <?= $dataSO->shipment_an ?>
                <?php endif; ?>

                <?php if ($dataSO->consigne_docs != ""): ?>
                    <b>
                        - CONSIGNEE
                    </b>
                    <br>
                    <?= $dataSO->consigne_docs ?>
                <?php endif; ?>

                <?php if ($dataSO->notify_party != ""): ?>
                    <b>
                        - NOTIFY PARTY
                    </b>
                    <br>
                    <?= $dataSO->notify_party ?>
                <?php endif; ?>

                <?php if ($dataSO->additional_detail_docs != ""): ?>
                    <b>
                        - ADDITIONAL DETAILS
                    </b>
                    <br>
                    <?= $dataSO->additional_detail_docs ?>
                <?php endif; ?>

                <hr>
                <div style="margin-top: -90px; margin-bottom:-3px;">
                    <b>
                        SPECIAL INSTRUCTIONS
                    </b>
                </div>
                <hr>
                <?php if ($dataSO->product_specs != ""): ?>
                    <b>
                        - PRODUCT SPECS
                    </b>
                    <br>
                    <?= $dataSO->product_specs ?>

                <?php endif; ?>

                <?php if ($dataSO->processing_method != ""): ?>
                    <b>
                        - PROCESSING METHOD
                    </b>
                    <br>
                    <?= $dataSO->processing_method ?>

                <?php endif; ?>

                <?php if ($dataSO->packaging != ""): ?>
                    <b>
                        - PACKAGING
                    </b>
                    <br>
                    <?= $dataSO->packaging ?>
                <?php endif; ?>

                <?php if ($dataSO->code_stamping != ""): ?>
                    <b>
                        - CODE STAMPING
                    </b>
                    <br>
                    <?= $dataSO->code_stamping ?>
                <?php endif; ?>

                <?php if ($dataSO->loading != ""): ?>
                    <b>
                        - LOADING
                    </b>
                    <br>
                    <?= $dataSO->loading ?>
                <?php endif; ?>


                <?php if ($dataSO->foto_loading != ""): ?>
                    <b>
                        - FOTO LOADING
                    </b>
                    <br>
                    <?= $dataSO->foto_loading ?>
                <?php endif; ?>


                <?php if ($dataSO->stuffing != ""): ?>
                    <b>
                        - STUFFING
                    </b>
                    <br>
                    <?= $dataSO->stuffing ?>
                <?php endif; ?>


                <?php if ($dataSO->additional_detail != ""): ?>
                    <b>
                        - ADDITIONAL DETAIL
                    </b>
                    <br>
                    <?= $dataSO->additional_detail ?>
                <?php endif; ?>

                <?php if (count($dataSalesOrderSpecs) != 0): ?>
                    <div style="margin: 10px 0;">
                        <b>
                            - PRODUCT SPECS
                        </b> <br>
                        <table style="width:50%; border-collapse: collapse; margin-left:6px; margin-top:3px; font-size:11px;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #000; padding: 4px;"><b>GRADE</b></th>
                                    <th style="border: 1px solid #000; padding: 4px;"><b>SPECIFICATIONS</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dataSalesOrderSpecs as $d): ?>
                                    <tr>
                                        <td colspan="2" style="border: 1px solid #000; padding: 4px;">- <?= $d['size_packing'] ?></td>
                                    </tr>
                                    <?php foreach ($d['grade_specs'] as $g): ?>
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 4px;"><?= $g['grade'] ?></td>
                                            <td style="border: 1px solid #000; padding: 4px;"><?= $g['specification'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>


                    </div>
                <?php endif; ?>
            </div>

            <table border="1" style="width: 100.2%; border: 1px solid black; border-collapse: collapse;" class="label">
                <tbody>
                    <tr class="keep-together">
                        <td>
                            <b>
                                APPROVED BY
                            </b>
                        </td>
                    </tr>

                    <tr>
                        <td style=" padding: 0;">
                            <table style="width: 100%; border-collapse: collapse;" class="label">
                                <tr>
                                    <td style="width: 16.66%; border: 1px solid black; border-left: none; border-top: none; border-bottom: none;">
                                        M.DIRECTOR
                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                        QC
                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                        EXIM
                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                        ACCOUNTING
                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                        PRODUCTION
                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-right: none; border-top: none; border-bottom: none;">
                                        MARKETING
                                    </td>
                                </tr>
                            </table>
                        </td>

                    </tr>
                    <tr>
                        <td style="border: none; padding: 0;">
                            <table style="width: 100%; border-collapse: collapse;" class="label">
                                <tr>
                                    <td style="width: 16.66%; border: 1px solid black; border-left: none; border-top: none; border-bottom: none;">
                                        <br><br><br><br>
                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                        <br><br><br><br>

                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                        <br><br><br><br>

                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                        <br><br><br><br>

                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-top: none; border-bottom: none;">
                                        <br><br><br><br>

                                    </td>
                                    <td style="width: 16.66%; border: 1px solid black; border-right: none; border-top: none; border-bottom: none;">
                                        <br><br><br><br>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </tbody>
            </table>

        <?php } ?>
</body>

</html>
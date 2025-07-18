<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $salesKontrak['sales_contract_no'] ?></title>
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
    </style>
</head>

<body>
    <div class="header">
        <div class="txt-center"><label class="label-header">SALES CONTRACT</label></div>
        <div class="txt-center"><label class="label-header">NO. <?= $salesKontrak['sales_contract_no']; ?></label></div>
        <div class="d-flex flex-column">
            <div class="txt-left">
                <label class="label-header">DATE: <?= date('F d, Y', strtotime($salesKontrak['createdAt'])); ?></label>
            </div>
            <div class="txt-right po-customer">
                <?php if (!empty($salesKontrak['customer_po_no'])): ?>
                    <label class="label-header">PO NO: <?= $salesKontrak['customer_po_no']; ?></label>
                <?php endif; ?>
                <br>
                <?php if (!empty($salesKontrak['no_container'])): ?>
                    <label class="label-header">CONTAINER: <?= $salesKontrak['no_container']; ?></label>
                <?php endif; ?>

            </div>

        </div>
        <div class="d-flex flex-column">
            <div class="txt-left">
                <label class="label-header">SELLER: PT.TOBA SURIMI INDUSTRIES</label>
            </div>
            <div class="txt-left">
                <label class="label-header">BUYER: <?= $salesKontrak['customer_name']; ?></label>
            </div>
            <div class="txt-left">
                <label class="label-header">BANK: <?= $salesKontrak['nama_bank']; ?></label>
            </div>
            <div class="txt-left">
                <label class="label-header">SWIFT CODE: <?= $salesKontrak['kode_bank']; ?></label>
            </div>
            <div class="txt-left">
                <label class="label-header">ACCOUNT # : <?= $salesKontrak['no_rekening']; ?></label>
            </div>
            <div class="txt-left">
                <label class="label-header">BENEFICIARY # : <?= $salesKontrak['atas_nama']; ?></label>
            </div>
            <!-- <div class="txt-right rev-customer">
                <label class="label-header">Revision: <?= $salesKontrak['jumlah_unpost']; ?></label>
            </div> -->
        </div>
        <div class="mt-1 justify-content-center">
            <label class="label-header">
                <?= $salesKontrak['banking_information'] ?>
            </label>
        </div>

        <div class="mt-1 justify-content-center"><label class="label-header">THIS SALES CONTRACT
                IS MADE BY AND BETWEEN THE BUYER AND SELLER, WHEREBY THE BUYER AGREES TO PURCHASE AND THE SELLER
                AGREES TO SELL THE UNDER MENTIONED COMMODITIES AS PER THE TERMS AND CONDITIONS STIPULATED BELOW:</label></div>
    </div>
    <div class="header">
        <div class="mt-1 txt-left"><label class="label-header"> I. DESCRIPTION OF GOODS </label></div>
    </div>
    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-family: Arial, sans-serif; font-size: 9px;">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 4%; height:1.5%;">NO</th>
                <th style="padding: 6px; text-align: left; font-weight: bold; border: 1px solid #ddd;">
                    PRODUCT
                    <span style="float: right;">
                        TOTAL (<?= $salesKontrak['mata_uang'] ?>) <br>
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
            foreach ($salesKontrakdetail as $detail) {
                $total_qty = $total_qty + formatter($detail["qty"], "STR_TO_INT");
                $total_amount = $total_amount + formatter($detail["total_harga"], "STR_TO_INT");
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;"><?= $no++ ?></td>
                    <td style="padding: 6px; border: 1px solid #ddd; vertical-align: top;">
                        <div style="font-weight: bold; font-size: 9px;"><?= $detail["nama_barang"]; ?></div>
                        <div style="font-size: 9px; margin-top: 4px; line-height: 1.4;">
                            <?php if (!empty($detail['species'])): ?>
                                <span style="display: inline-block; width: 60px;">SPECIES:</span> <?= $detail['species'] ?> <br>
                            <?php endif; ?>
                            <?php if (!empty($detail['specs'])): ?>
                                <span style="display: inline-block; width: 50px;">SPECS:</span> <?= $detail['specs'] ?> <br>
                            <?php endif; ?>
                            <span style="display: inline-block; width: 60px;">BRAND:</span> <?= $detail['brand'] ?> <br>
                            <span style="display: inline-block; width: 60px;">PACKING:</span> <?= $detail['kemasan'] ?> <br>

                        </div>

                        <?php if (!empty($detail['size_breakdown'])): ?>
                            <?php
                            // Identify which columns have data
                            $columns_to_show = [];
                            $all_columns = [
                                'size' => ['label' => 'Size', 'width' => '8%'],
                                'grade' => ['label' => 'Grade', 'width' => '8%'],
                                'packing' => ['label' => 'Packing', 'width' => '8%'],
                                'can' => ['label' => 'Can', 'width' => '7%'],
                                'cased' => ['label' => 'Case', 'width' => '7%'],
                                'kg' => ['label' => 'Kg', 'width' => '7%'],
                                'lb' => ['label' => 'LB', 'width' => '7%'],
                                'inner_box' => ['label' => 'Inner', 'width' => '8%'],
                                'pc' => ['label' => 'PC', 'width' => '7%'],
                                'bag' => ['label' => 'Bag', 'width' => '7%'],
                                'persen' => ['label' => '%', 'width' => '6%'],
                                'remark' => ['label' => 'Remarks', 'width' => '10%']
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
                            ?>

                            <div style="margin-top: 6px;">
                                <div style="font-size: 9px; font-weight: bold;">SIZE BREAKDOWN:</div>
                                <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 9px;">
                                    <thead>
                                        <tr style="background-color: #f3f4f6;">
                                            <?php foreach ($columns_to_show as $col => $col_data): ?>
                                                <?php if ($col != 'persen'): ?>
                                                    <th style="padding: 3px; border: 1px solid #ddd; width: <?= $col_data['width'] ?>"><?= $col_data['label'] ?></th>
                                                <?php endif; ?>
                                            <?php endforeach; ?>

                                            <?php if ($show_persen_column): ?>
                                                <th style="padding: 3px; border: 1px solid #ddd; width: 6%;text-align: right;">%</th>
                                            <?php endif; ?>

                                            <th style=" padding: 3px; border: 1px solid #ddd; width: 9%; text-align: right;">Qty</th>
                                            <th style="padding: 3px; border: 1px solid #ddd; width: 10%; text-align: right;">Unit Price</th>
                                            <th style="padding: 3px; border: 1px solid #ddd; width: 10%; text-align: right;">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $breakdown_qty = 0;
                                        $breakdown_total = 0;
                                        $breakdown_unit_price_total = 0;
                                        $breakdown_persen = 0;
                                        foreach ($detail['size_breakdown'] as $breakdown):
                                            $breakdown_qty += $breakdown['qty'];
                                            $breakdown_total += $breakdown['total'];
                                            $breakdown_unit_price_total += $breakdown['harga'];
                                            if (isset($breakdown['persen']) && is_numeric($breakdown['persen'])) {
                                                $breakdown_persen += $breakdown['persen'];
                                                $total_persen += $breakdown['persen'];
                                            }
                                        ?>
                                            <tr>
                                                <?php foreach ($columns_to_show as $col => $col_data): ?>
                                                    <?php if ($col != 'persen'): ?>
                                                        <td style="padding: 3px; border: 1px solid #ddd;"><?= $breakdown[$col] ?></td>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>

                                                <?php if ($show_persen_column): ?>
                                                    <td style="padding: 3px; border: 1px solid #ddd; text-align: right;">
                                                        <?= !empty($breakdown['persen']) ? number_format($breakdown['persen'], 2) . " %" : '' ?>
                                                    </td>
                                                <?php endif; ?>

                                                <td style="padding: 3px; border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['qty'], 2) . " " . $breakdown['satuan_size_code'] ?></td>
                                                <td style="padding: 3px; border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['harga'], 2) ?></td>
                                                <td style="padding: 3px; border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['total'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #e9ecef;">
                                            <td colspan="<?= count($columns_to_show) - ($show_persen_column ? 1 : 0) ?>" style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">TOTAL</td>

                                            <?php if ($show_persen_column): ?>
                                                <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">
                                                    <?= $breakdown_persen > 0 ? number_format($breakdown_persen, 2) . " %" : '' ?>
                                                </td>
                                            <?php endif; ?>

                                            <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;"><?= number_format($breakdown_qty, 2) . " " . $breakdown['satuan_size_code'] ?></td>
                                            <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;">-</td>
                                            <td style="padding: 3px; border: 1px solid #ddd; text-align: right; font-weight: bold;"><?= number_format($breakdown_total, 2) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php } ?>
            <!-- Royalty Row (if exists) -->
            <?php if ($salesKontrak['royalty_price'] > 0): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size:10px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                        <span style="float: left;"><?= $salesKontrak['royalty'] ?></span>
                        <?= number_format($salesKontrak['royalty_price'], 2) ?>
                    </td>
                </tr>
            <?php endif; ?>

            <!-- Quantity Row -->
            <!-- <tr style="font-weight: bold; background-color: #e9ecef; font-size:10px;">
                <td style="padding: 6px; border: 1px solid #ddd;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                    <span style="float: left;">TOTAL QTY</span>
                    <?= number_format($total_qty, 2) ?>
                </td>
            </tr> -->

            <!-- Percentage Row (if exists) -->
            <!-- <?php if ($total_persen > 0): ?>
                <tr style="font-weight: bold; background-color: #e9ecef; font-size:10px;">
                    <td style="padding: 6px; border: 1px solid #ddd;"></td>
                    <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                        <span style="float: left;">TOTAL %</span>
                        <?= number_format($total_persen, 2) . " %" ?>
                    </td>
                </tr>
            <?php endif; ?> -->

            <!-- Final Amount Row -->
            <tr style="font-weight: bold; background-color: #e9ecef; font-size:10px;">
                <td style="padding: 6px; border: 1px solid #ddd;"></td>
                <td style="padding: 6px; border: 1px solid #ddd; text-align: right;">
                    <span style="float: left;">GRAND TOTAL </span>
                    (<?= $salesKontrak['mata_uang'] ?>) <?= number_format($total_amount - ($salesKontrak['royalty_price'] > 0 ? $salesKontrak['royalty_price'] : 0), 2) ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="header">
        <?php
        $counter = 2; // Start counter for Roman numerals

        // II. TOTAL AMOUNT
        if ($salesKontrak['royalty_price'] > 0 || $salesKontrak['total_amount'] > 0): ?>
            <div class="mt-1 txt-left">
                <label class="label-header">
                    <?= strtoupper(numToRoman($counter++)) ?>. TOTAL AMOUNT (<?= $salesKontrak['mata_uang'] ?>) :
                    <?= number_format($salesKontrak['royalty_price'] > 0 ?
                        ($total_amount - $salesKontrak['royalty_price']) :
                        $salesKontrak['total_amount'], 2) ?>
                </label>
            </div>
        <?php endif;

        // III. TOLERANCE
        if (!empty($salesKontrak['tolerance'])): ?>
            <div class="mt-1 txt-left">
                <label class="label-header">
                    <?= strtoupper(numToRoman($counter++)) ?>. TOLERANCE: <?= $salesKontrak['tolerance'] ?>
                </label>
            </div>
        <?php endif;

        // IV. SHIPMENT DATE
        if (!empty($salesKontrak['shipment_date'])): ?>
            <div class="mt-1 txt-left">
                <label class="label-header">
                    <?= strtoupper(numToRoman($counter++)) ?>. ESTIMATED SHIPMENT DATE: <?= $salesKontrak['shipment_date'] ?>
                </label>
            </div>
        <?php endif;

        // V. PORT OF LOADING
        if (!empty($salesKontrak['loading_port'])): ?>
            <div class="mt-1 txt-left">
                <label class="label-header">
                    <?= strtoupper(numToRoman($counter++)) ?>. PORT OF LOADING: <?= $salesKontrak['loading_port'] ?>
                </label>
            </div>
        <?php endif;

        // VI. PORT OF DISCHARGE
        if (!empty($salesKontrak['dicharge_port'])): ?>
            <div class="mt-1 txt-left">
                <label class="label-header">
                    <?= strtoupper(numToRoman($counter++)) ?>. PORT OF DISCHARGE: <?= $salesKontrak['dicharge_port'] ?>
                </label>
            </div>
        <?php endif;

        // VII. PAYMENT TERM
        if (!empty($salesKontrak['payment_term'])): ?>
            <div class="mt-1 txt-left">
                <label class="label-header">
                    <?= strtoupper(numToRoman($counter++)) ?>. PAYMENT TERM: <?= $salesKontrak['payment_term'] ?>
                </label>
            </div>
        <?php endif;

        // VIII. INSURANCE
        if (!empty($salesKontrak['shipment_insurance'])): ?>
            <div class="mt-1 txt-left">
                <label class="label-header">
                    <?= strtoupper(numToRoman($counter++)) ?>. INSURANCE: <?= $salesKontrak['shipment_insurance'] ?>
                </label>
            </div>
        <?php endif;

        // IX. DOCUMENTS REQUIRED
        if (!empty($salesKontrak['documents_required'])): ?>
            <div class="mt-1 txt-left">
                <label class="label-header">
                    <?= strtoupper(numToRoman($counter++)) ?>. DOCUMENT REQUIRED:
                </label>
            </div>
            <div class="txt-left">
                <label class="label-header"><?= nl2br($salesKontrak['documents_required']) ?></label>
            </div>
        <?php endif;

        // X. ADDITIONAL CLAUSES
        if (!empty($salesKontrak['special_instructions'])): ?>
            <div class="mt-1 txt-left">
                <label class="label-header">
                    <?= strtoupper(numToRoman($counter++)) ?>. ADDITIONAL CLAUSES:
                </label>
            </div>
            <div class="txt-left">
                <label class="label-header"><?= nl2br($salesKontrak['special_instructions']) ?></label>
            </div>
        <?php endif; ?>

        <!-- Final statement (no number) -->
        <div class="mt-1 txt-left">
            <label class="label-header">
                FOR THOSE ITEMS WHICH ARE NOT COVERED IN THIS CONTRACT, BOTH PARTIES WILL NEGOTIATE AND COME TO COMPROMISE.
            </label>
        </div>
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
    <table class="mt-1 sign-table border-collapse">
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
                    <label class="label-header">PT. TOBA SURIMI INDUSTRIES</label>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="height: 100px;"></td>
                <td></td>
            </tr>
            <tr>
                <td style="width: 350px;">
                    <div style="border-top: 1px solid !important; width: 80%;"></div>
                </td>
                <td>
                    <?= $salesKontrak['signature_by'] ?>
                    <div style="border-top: 1px solid !important; width: 80%;"></div>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
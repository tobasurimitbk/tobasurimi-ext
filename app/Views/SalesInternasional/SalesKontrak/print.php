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
                <label class="label-header">PO NO: <?= $salesKontrak['customer_po_no']; ?></label>
                <br>
                <label class="label-header">CONTAINER: <?= $salesKontrak['no_container']; ?></label>

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
        <div class="mt-1 txt-left"><label class="label-header"> I. DESCRIPTION OF GOODS, MARKS </label></div>
    </div>
    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; font-family: Arial, sans-serif; font-size: 9px;">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                <th style="padding: 3px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 3%;">NO</th>
                <th style="padding: 3px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 40%;">PARTICULAR</th>
                <th style="padding: 3px; text-align: left; font-weight: bold; border: 1px solid #ddd; width: 10%;">PACKING</th>
                <th style="padding: 3px; text-align: right; font-weight: bold; border: 1px solid #ddd; width: 12%;">UNIT PRICE (<?= $salesKontrak['mata_uang'] ?>)</th>
                <th style="padding: 3px; text-align: right; font-weight: bold; border: 1px solid #ddd; width: 12%;">
                    TOTAL (<?= $salesKontrak['mata_uang'] ?>) <br>
                    <?= $salesKontrak['tipe_harga'] . " " . $salesKontrak['dicharge_port'] ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_qty = 0;
            $total_amount = 0;
            $no = 1;
            foreach ($salesKontrakdetail as $detail) {
                $total_qty = $total_qty + formatter($detail["qty"], "STR_TO_INT");
                $total_amount = $total_amount + formatter($detail["total_harga"], "STR_TO_INT");
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 3px; border: 1px solid #ddd; vertical-align: top;"><?= $no++ ?></td>
                    <td style="padding: 3px; border: 1px solid #ddd; vertical-align: top;">
                        <div style="font-weight: bold; font-size: 8.5px;"><?= $detail["nama_barang"]; ?></div>
                        <div style="font-size: 7.5px; margin-top: 2px; line-height: 1.2;">
                            <span style="display: inline-block; width: 50px;">SPECIES:</span> <?= $detail['species'] ?> <br>
                            <span style="display: inline-block; width: 40px;">SPECS:</span> <?= $detail['specs'] ?> <br>
                            <span style="display: inline-block; width: 50px;">PACKING:</span> <?= $detail['kemasan'] ?> <br>
                        </div>

                        <?php if (!empty($detail['size_breakdown'])): ?>
                            <?php
                            // Cari kolom mana saja yang memiliki data
                            $columns_to_show = [];
                            $all_columns = [
                                'size' => ['label' => 'Size', 'width' => '6%'],
                                'grade' => ['label' => 'Grade', 'width' => '6%'],
                                'packing' => ['label' => 'Pack', 'width' => '6%'],
                                'can' => ['label' => 'Can', 'width' => '5%'],
                                'cased' => ['label' => 'Case', 'width' => '5%'],
                                'kg' => ['label' => 'Kg', 'width' => '5%'],
                                'lb' => ['label' => 'LB', 'width' => '5%'],
                                'inner_box' => ['label' => 'Inner', 'width' => '6%'],
                                'pc' => ['label' => 'PC', 'width' => '5%'],
                                'bag' => ['label' => 'Bag', 'width' => '5%'],
                                'persen' => ['label' => '%', 'width' => '4%'],
                                'remark' => ['label' => 'Remarks', 'width' => '8%']
                            ];

                            foreach ($all_columns as $col => $col_data) {
                                foreach ($detail['size_breakdown'] as $breakdown) {
                                    if (!empty($breakdown[$col])) {
                                        $columns_to_show[$col] = $col_data;
                                        break;
                                    }
                                }
                            }
                            ?>

                            <div style="margin-top: 4px;">
                                <div style="font-size: 7.5px; font-weight: bold;">SIZE BREAKDOWN:</div>
                                <table style="width: 100%; border-collapse: collapse; margin-top: 2px; font-size: 7px;">
                                    <thead>
                                        <tr style="background-color: #f3f4f6;">
                                            <?php foreach ($columns_to_show as $col => $col_data): ?>
                                                <th style="padding: 1px; border: 1px solid #ddd; width: <?= $col_data['width'] ?>"><?= $col_data['label'] ?></th>
                                            <?php endforeach; ?>
                                            <th style="padding: 1px; border: 1px solid #ddd; width: 7%; text-align: right;">Qty</th>
                                            <th style="padding: 1px; border: 1px solid #ddd; width: 8%; text-align: right;">Price</th>
                                            <th style="padding: 1px; border: 1px solid #ddd; width: 8%; text-align: right;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $breakdown_qty = 0;
                                        $breakdown_total = 0;
                                        foreach ($detail['size_breakdown'] as $breakdown):
                                            $breakdown_qty += $breakdown['qty'];
                                            $breakdown_total += $breakdown['total'];
                                        ?>
                                            <tr>
                                                <?php foreach ($columns_to_show as $col => $col_data): ?>
                                                    <td style="padding: 1px; border: 1px solid #ddd;"><?= $breakdown[$col] ?></td>
                                                <?php endforeach; ?>
                                                <td style="padding: 1px; border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['qty'], 2) ?></td>
                                                <td style="padding: 1px; border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['harga'], 2) ?></td>
                                                <td style="padding: 1px; border: 1px solid #ddd; text-align: right;"><?= number_format($breakdown['total'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #e9ecef;">
                                            <td colspan="<?= count($columns_to_show) ?>" style="padding: 1px; border: 1px solid #ddd; text-align: right; font-weight: bold;">TOTAL</td>
                                            <td style="padding: 1px; border: 1px solid #ddd; text-align: right; font-weight: bold;"><?= number_format($breakdown_qty, 2) ?></td>
                                            <td style="padding: 1px; border: 1px solid #ddd; text-align: right; font-weight: bold;">-</td>
                                            <td style="padding: 1px; border: 1px solid #ddd; text-align: right; font-weight: bold;"><?= number_format($breakdown_total, 2) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 3px; border: 1px solid #ddd; vertical-align: top; text-align: right;"><?= number_format($detail["qty"], 2) . " " . $detail['satuan_order_name'] ?></td>
                    <td style="padding: 3px; border: 1px solid #ddd; vertical-align: top; text-align: right;"><?= number_format($detail["harga"], 2); ?></td>
                    <td style="padding: 3px; border: 1px solid #ddd; vertical-align: top; text-align: right;"><?= number_format($detail["total_harga"], 2); ?></td>
                </tr>
            <?php } ?>
            <tr style="font-weight: bold; background-color: #e9ecef;">
                <td style="padding: 3px; border: 1px solid #ddd;">TOTAL</td>
                <td style="padding: 3px; border: 1px solid #ddd;"></td>
                <td style="padding: 3px; border: 1px solid #ddd; text-align: right;"><?= number_format($total_qty, 2); ?></td>
                <td style="padding: 3px; border: 1px solid #ddd; text-align: right;"><?= number_format($total_amount - $salesKontrak['potongan_harga'], 2); ?></td>
                <td style="padding: 3px; border: 1px solid #ddd; text-align: right;"><?= number_format($total_amount, 2); ?></td>
            </tr>
        </tbody>
    </table>
    <div class="header">
        <div class="mt-1 txt-left"><label class="label-header">II. TOTAL AMOUNT: <?= $salesKontrak['total_amount'] ? number_format($salesKontrak['total_amount'], 2) : 0; ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">III. TOLERANCE: <?= $salesKontrak['tolerance']; ?></label></div>
        <!-- <div class="mt-1 txt-left"><label class="label-header">DUE DATE: <?= date('d M Y', strtotime($salesKontrak['due_date'])); ?></label></div> -->
        <div class="mt-1 txt-left"><label class="label-header">IV. ESTIMATED SHIPMENT DATE: </label></div>
        <div class="mt-1 txt-left"><label class="label-header">V. PORT LOADING: <?= $salesKontrak['loading_port']; ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">VI. PORT OF DISCHARGE: <?= $salesKontrak['dicharge_port']; ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">VII. PAYMENT TERM: <?= $salesKontrak['payment_term']; ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">VIII. DOCUMENT REQUIRED:</label></div>
        <div class="txt-left"><label class="label-header"><?= nl2br($salesKontrak['documents_required']); ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">IX. ADDITIONAL CLAUSES:</label></div>
        <div class="txt-left"><label class="label-header"><?= nl2br($salesKontrak['special_instructions']); ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">X. INSURANCE: <?= $salesKontrak['shipment_insurance']; ?></label></div>

        <div class="mt-1 txt-left"><label class="label-header">FOR THOSE ITEMS WHICH ARE NOT COVERED IN
                THIS CONTRACT, BOTH PARTIES WILL NEGOTIATE AND COME TO COMPROMISE.</label></div>
    </div>
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
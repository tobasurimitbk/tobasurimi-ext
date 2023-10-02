<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
            font-size: 12px;
        }

        .po-customer {
            margin-top: -20px !important;
        }

        .justify-content-center {
            justify-content: center !important;
        }
    </style>
</head>
<body>
<?php if(!empty($dataSO) && !empty($dataSODetail)){ ?>
    <div class="header">
        <div class="txt-center"><label class="label-header">SALES CONTRACT</label></div>
        <div class="txt-center"><label class="label-header">NO. <?= $dataSO->sales_contract_no; ?></label></div>
        <div class="d-flex flex-column">
            <div class="txt-left">
                <label class="label-header">DATE: <?= date('F d, Y', strtotime($dataSO->createdAt)); ?></label>
            </div>
            <div class="txt-right po-customer">
                <label class="label-header">PO CUST: <?= $dataSO->customer_po_no; ?></label>
            </div>
        </div>
        <div class="txt-left"><label class="label-header">SELLER: PT.TOBA SURIMI INDUSTRIES</label></div>
        <div class="mt-1 justify-content-center"><label class="label-header">THIS SALES CONTRACT 
        IS MADE BY AND BETWEEN THE BUYER AND SELLER, WHEREBY THE BUYER AGREES TO PURCHASE AND THE SELLER 
        AGREES TO SELL THE UNDER MENTIONED COMMODITIES AS PER THE TERMS AND CONDITIONS STIPULATED BELOW:</label></div>
    </div>
    <table class="mt-1 item-table border-collapse">
        <thead>
            <tr>
                <th>
                    <label class="label-header">DESCRIPTION OF GOODS</label>
                </th>
                <th>
                    <label class="label-header">QTY</label>
                </th>
                <th>
                    <label class="label-header">UNIT PRICE FOB/CNF</label>
                </th>
                <th>
                    <label class="label-header">TOTAL AMOUNT (US$)</label>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_qty = 0;
            $total_amount = 0;
            foreach($dataSODetail as $detail){
                $total_qty = $total_qty + formatter($detail["qty"], "STR_TO_INT");
                $total_amount = $total_amount + formatter($detail["total_price"], "STR_TO_INT");
            ?>
            <tr>
                <td><label class="label-header"><?= $detail["kode_barang"]; ?> <?= $detail["nama_barang"]; ?></label></td>
                <td><label class="label-header"><?= formatter($detail["qty"], "STR_TO_INT"); ?></label></td>
                <td><label class="label-header"><?= number_format(formatter($detail["price"], "STR_TO_INT")); ?></label></td>
                <td><label class="label-header"><?= number_format(formatter($detail["total_price"], "STR_TO_INT")); ?></label></td>
            </tr>
            <tr>
                <td><label class="label-child"><?= $detail["remark"]; ?></label></td>
                <td colspan="3"></td>
            </tr>
            <?php } ?>
            <tr>
                <td><label class="label-header">TOTAL</label></td>
                <td><label class="label-header"><?= $total_qty; ?></label></td>
                <td></td>
                <td><label class="label-header"><?= number_format($total_amount); ?></label></td>
            </tr>
        </tbody>
    </table>
    <div class="header">
        <div class="mt-1 txt-left"><label class="label-header">TOTAL AMOUNT: <?= $dataSO->total_amount ? number_format($dataSO->total_amount) : 0; ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">TOLERANCE: <?= $dataSO->tolerance; ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">DUE DATE: <?= date('d M Y', strtotime($dataSO->due_date)); ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">ESTIMATED SHIPMENT DATE: <?= date('d M Y', strtotime($dataSO->shipment_date)); ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">PORT LOADING: <?= $dataSO->loading_port; ?></label></div>
        <div class="txt-left"><label class="label-header">PORT OF DISCHARGE: <?= $dataSO->dicharge_port; ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">PAYMENT TERM: <?= $dataSO->payment_term; ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">DOCUMENT REQUIRED:</label></div>
        <div class="txt-left"><label class="label-header"><?=  nl2br($dataSO->documents_required); ?></label></div>
        <div class="mt-1 txt-left"><label class="label-header">SPECIAL INSTRUCTIONS:</label></div>
        <div class="txt-left"><label class="label-header"><?= nl2br($dataSO->special_instructions); ?></label></div>
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
                    <label class="label-header"><?= $dataSO->customer_name; ?></label>
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
                <td style="width: 350px;"><div style="border-top: 1px solid !important; width: 80%;"></div></td>
                <td><div style="border-top: 1px solid !important; width: 80%;"></div></td>
            </tr>
        </tbody>
    </table>
<?php } ?>
</body>
</html>
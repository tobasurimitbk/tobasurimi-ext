<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
    <style>
        body {
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
    <table border="0" style="width: 100%;">
        <tr style="vertical-align: top;">
            <td>
                <div style="text-align: right; margin-left:-20px; margin-right:40px; ">
                    <img src="<?= $company['logo'] ?>" style="width: 150px; text-align:right; margin-top:-17px" alt="">
                </div>
            </td>
            <td>
                <h1 style="margin-top:-10px; margin-left:-30px;"><b><?= strtoupper($company['holding_company']) ?></b></h1>
                <table style="width: 100%; margin-top:-15px; margin-left:-30px;">
                    <tr style="vertical-align: top;">
                        <td style="width: 50px;">Office</td>
                        <td>:</td>
                        <td>
                            <?= $alamatKantor['value']; ?>
                            Telp. 62-61 6871022 Fax. 62-61 6871007 Email: marketing@tobasurimi.id, pt.tobasurimiindustries@gmail.com
                            Website: www.tobasurimi.com Medan 20371-Sumatera Utara - Indonesia
                        </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td>Factory</td>
                        <td>:</td>
                        <td><?= $company['address'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr style="margin-top: -1px;">

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
                    <?= strtoupper($dataPO->shipper) ?>
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
            <td><?= $attnKantor ?></td>
        </tr>
    </table>
    <table border="1" style="width: 100%; margin-top:10px;" class="table no-border">
        <thead>
            <tr>
                <td style="text-align: center;">MARKS & NO</td>
                <td style="text-align: center;">PARTICULAR</td>
                <td style="text-align: center;">QTTY</td>
                <td style="text-align: center;">UNIT PRICE <br>USD</td>
                <td style="text-align: center;">TOTAL AMOUNT<br>USD</td>
            <tr>
                <td style="border:0px;"></td>
                <td style="border:0px;"></td>
                <td style="border:0px;"></td>
                <td colspan="2" style="text-align: center;border:0px;">
                    <?= strtoupper($valuta) ?>
                </td>
            </tr>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $totalPrice = 0;
            $totalDisc = 0;
            $diskonTotal = 0;
            $totalWithAdditional = 0;
            ?>
            <?php foreach ($dataPODetail as $detail) : ?>
                <?php
                $totalDisc =  (formatter($detail["price"], "CURR_TO_INT") * formatter($detail["qty"], "STR_TO_FLOAT")) * ((float)$detail["disc"] / 100);
                $totalWithAdditional = (formatter($detail["price"], "CURR_TO_INT") * formatter($detail["qty"], "STR_TO_FLOAT") - $totalDisc) +  formatter($detail["additional_cost"], "CURR_TO_INT");
                $totalPrice += $totalWithAdditional;
                $diskonTotal += $totalDisc;
                ?>
                <tr>
                    <td style="text-align: center; border:0px;"><?= $no++; ?></td>
                    <td style="text-align: center;border:0px;">
                        <?= strtoupper($detail["kode_barang"]) . ' ' . strtoupper($detail["nama_barang"]) ?>
                    </td>
                    <td style="text-align: center;border:0px;">
                        <?= $detail["qty"] . " " . $detail["kode_satuan"] ?>
                    </td>
                    <td style="text-align: center;border:0px;">
                        <?= number_format(formatter($detail["price"], "STR_TO_INT"), 2, '.', ',') ?>
                    </td>
                    <td style="text-align: center;border:0px;">
                        <?= number_format($totalWithAdditional, 2, '.', ','); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" style="text-align: center;">TOTAL</td>
                <td style="text-align: center;"><?= number_format(formatter(($totalPrice), "STR_TO_FLOAT"), 2, '.', ',') ?></td>
            </tr>
        </tbody>
    </table>
    <table style="margin-top: 15px;">
        <tr>
            <td>SHIPMENT</td>
            <td>:</td>
            <td><?= $shipmentPO; ?></td>
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

                <b><u><?= strtoupper(session()->get("login")->name); ?></u></b><br>
                DIRECTOR
            </td>
        </tr>
    </table>
</body>

</html>
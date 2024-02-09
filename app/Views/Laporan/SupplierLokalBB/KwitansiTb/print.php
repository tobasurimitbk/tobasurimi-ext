<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kwitansi TB</title>
    <style>
        body {
            font-size: 15px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin: 29px;
            padding: 29px;
        }

        .pagebreak {
            clear: both;
            page-break-after: always;
        }

        .cong-table tr td {
            border: 0 !important;
            border-right: 1px solid !important;
        }

        .content-between {
            justify-content: space-between;
        }

        .d-flex {
            display: flex;
        }

        .item-table {
            width: 100%;
            border: 1px solid;
            border-collapse: collapse;
            border-spacing: 10px;
        }

        .item-table tr td {
            padding-left: 3px;
            padding-right: 3px;
        }

        .item-table tr th {
            border: 1px solid;
            padding-left: 3px;
            padding-right: 3px;
        }

        .item-table tr td:not(.skip) {
            border: 1px solid;
        }

        .mt-05 {
            margin-top: 0.5rem;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .mt-4 {
            margin-top: 4rem;
        }

        .signed-info {
            justify-content: space-around;
            text-align: center;
        }

        .txt-bold {
            font-weight: 700;
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

        .txt-underline {
            text-decoration: underline;
        }

        .w-100 {
            width: 100%;
        }

        .table-border {
            border: 1px solid black;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .sign-table td:not(:last-child) {
            border: 1px solid;
        }

        .sign-row {
            display: flex;
            justify-content: space-around;
            margin-top: 1rem;
            width: 100%;
        }

        .sign-row>div {
            width: 200px;
            border-top: 1px solid;
            margin-top: 2rem
        }

        .sign-row-second {
            display: flex;
            justify-content: space-around;
            margin-top: 1rem;
            width: 100%;
        }

        .sign-row-second>div {
            width: 150px;
            border-top: 1px solid;
            margin-top: 2rem
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 90px;
        }
    </style>
</head>

<body>
    <div style="text-align: right;">
        <b>
            <u>
                Tanggal: <?= date('d-m-Y', strtotime($tanggal))  ?>
            </u>
        </b><br><br>
        Kwitansi <br>
        No. Nota : <?= $noKwitansi ?>
    </div>
    <div class="w-50 d-flex content-between" style="margin-top: -40px;">
        <div style="border: 3px solid;border-style: double;width: 60%;padding: 0.5rem;">
            <?= $company['company'] ?><br>
            <?= $company['address'] ?>
        </div>

    </div>

    <table class="w-100 mt-2">
        <tr>
            <td style="vertical-align: top; width: 40%;">SUDAH TERIMA DARI <br> (RECEIVED FROM)</td>
            <td style="vertical-align: top;">: </td>
            <td style="vertical-align: top; width: 55%;"><?= strtoupper($company['company']) ?></td>
        </tr>
        <tr>
            <td style="vertical-align: top;">BANYAKNYA UANG <br> (AMOUNT)</td>
            <td style="vertical-align: top;">: </td>
            <td style="vertical-align: top;"><?= strtoupper(terbilang(formatter(($kwitansi['hargaBulananWithQtyPphTotal']), "STR_TO_FLOAT"))) ?></td>
        </tr>
        <tr>
            <td style="vertical-align: top;">UNTUK PEMBAYARAN <br> (FOR PAYMENT)</td>
            <td style="vertical-align: top;">: </td>
            <td style="vertical-align: top;">PEMBELIAN <?= $kwitansi['barangName'] ?> SEBANYAK <?= $kwitansi['qtyTotal'] ?> KG DARI <?= $kwitansi['supplierName'] ?></td>
        </tr>
    </table>

    <table class="mt-1" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double;">
        <tr>
            <td>Bruto</td>
            <td>Rp.</td>
            <td class="txt-right"><?= number_format(formatter($kwitansi['hargaBulananTotal'], "STR_TO_FLOAT"), 2, '.', ',') ?></td>
        </tr>
        <tr>
            <td>PPh</td>
            <td>Rp.</td>
            <td class="txt-right"><?= number_format($kwitansi['hargaBulananWithQtyTotal'], 2, '.', ',') ?></td>
        </tr>
        <tr>
            <td>Dibayarkan</td>
            <td>Rp.</td>
            <td class="txt-right"><?= number_format($kwitansi['hargaBulananWithQtyPphTotal'], 2, '.', ',') ?></td>
        </tr>
    </table>

    <div class="w-100" style="margin-top: -20px;text-align:center;">
        <div style="text-align: right;">
            <div><?= $provinsi['province_name'] ?>, <?= date('d-m-Y', strtotime($tanggal))  ?></div>
            <div>yang Menerima</div><br><br>
            <div class="mt-2">(<?= $kwitansi['supplierName'] ?>)</div>
        </div>
    </div>

</body>

</html>
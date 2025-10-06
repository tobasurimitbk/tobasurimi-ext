<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kwitansi TB</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Times New Roman', Times, serif;
            text-transform: uppercase;
            font-weight: normal;
        }

        @page {
            /* size: 8.27in 5.50in landscape; */
            size: 8.27in 6in landscape;
            margin: 25px;
            /* Margin diperkecil */
            padding: 25px;
            /* Padding diperkecil */
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

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <?php foreach ($dataResult as $i => $kwitansi) : ?>

        <?php if ($i == 0) : ?>
            <table style="width: 100%;">
                <tr>
                    <td style="font-size:14px;">
                        <div style="font-size: 15px; font-weight:bold;">
                            <u>
                                <?= $kwitansi['company']['holding_company'] ?> (<?= $kwitansi['company']['company'] ?>) <br>
                            </u>
                        </div>
                    </td>
                    <td style="text-align: right;">
                        Tanggal: <?= date('d-m-Y', strtotime($kwitansi['tanggal']))  ?>
                    </td>
                </tr>

            </table>
            <center style="margin-top: 20px;">
                <div style="font-size: 15px; font-weight:bold;">
                    <u>
                        KWITANSI BULANAN
                    </u>
                </div>
                <div style="font-size: 13px;">
                    No : <?= $kwitansi['noKwitansi'] ?>
                </div>
            </center>
            <div class="w-50 d-flex content-between" style="margin-top: -40px;">
                <div style="width: 60%;padding: 0.5rem;">
                </div>

            </div>

            <table class="w-100 mt-2">
                <tr>
                    <td style="vertical-align: top; width: 20%;">SUDAH TERIMA DARI <br> (RECEIVED FROM)</td>
                    <td style="vertical-align: top; width:2%">: </td>
                    <td style="vertical-align: top; width: 55%;"><?= $kwitansi['company']['holding_company'] ?> (<?= strtoupper($kwitansi['company']['company']) ?>)</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">BANYAKNYA UANG <br> (AMOUNT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;"><?= strtoupper(terbilang(formatter(($kwitansi['kwintansi']['harga_bulanan_pph']), "STR_TO_FLOAT"))) ?> RUPIAH</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">UNTUK PEMBAYARAN <br> (FOR PAYMENT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PEMBELIAN <?= $kwitansi['kwintansi']['nama_barang'] ?> SEBANYAK <?= number_format($kwitansi['kwintansi']['qty'], 2) ?> KG DARI <?= $kwitansi['kwintansi']['supplier']['name'] ?></td>
                </tr>
            </table>

            <table class="mt-1" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double; margin-top:35px;">
                <tr style="font-size:14px;">
                    <td style="height:5%;">Bruto</td>
                    <td style="height:5%;">Rp.</td>
                    <td class="txt-right" style="height:2.5%;"><?= number_format($kwitansi['kwintansi']['harga_bulanan'], 2, '.', ',') ?></td>
                </tr>
                <tr style="font-size:14px;">
                    <td style="height:5%;">PPh</td>
                    <td style="height:5%;">Rp.</td>
                    <td class="txt-right" style="height:2.5%;"><?= number_format($kwitansi['kwintansi']['pph'], 2, '.', ',') ?></td>
                </tr>
                <tr style="font-size:14px;">
                    <td style="height:5%;">Dibayarkan</td>
                    <td style="height:5%;">Rp.</td>
                    <td class="txt-right" style="height:2.5%;"><?= number_format($kwitansi['kwintansi']['harga_bulanan_pph'], 2, '.', ',') ?></td>
                </tr>
            </table>

            <div class="w-100" style="margin-top: 50px;text-align:center;">
                <div style="text-align: right;">
                    <div>Medan, <?= date('d-m-Y', strtotime($kwitansi['tanggal']))  ?></div>
                    <div>yang Menerima</div><br><br>
                    <div class="mt-2">(<?= $kwitansi['kwintansi']['supplier']['name'] ?>)</div>
                </div>
            </div>
        <?php else : ?>
            <div class="page-break">
                <table style="width: 100%;">
                    <tr>
                        <td style="font-size:14px;">
                            <div style="font-size: 15px; font-weight:bold;">
                                <u>
                                    <?= $kwitansi['company']['holding_company'] ?> (<?= $kwitansi['company']['company'] ?>) <br>
                                </u>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            Tanggal: <?= date('d-m-Y', strtotime($kwitansi['tanggal']))  ?>
                        </td>
                    </tr>

                </table>
                <center style="margin-top: 20px;">
                    <div style="font-size: 15px; font-weight:bold;">
                        <u>
                            KWITANSI BULANAN
                        </u>
                    </div>
                    <div style="font-size: 13px;">
                        No : <?= $kwitansi['noKwitansi'] ?>
                    </div>
                </center>
                <div class="w-50 d-flex content-between" style="margin-top: -40px;">
                    <div style="width: 60%;padding: 0.5rem;">
                    </div>
                </div>

                <table class="w-100 mt-2">
                    <tr>
                        <td style="vertical-align: top; width: 20%;">SUDAH TERIMA DARI <br> (RECEIVED FROM)</td>
                        <td style="vertical-align: top; width:2%">: </td>
                        <td style="vertical-align: top; width: 55%;"><?= $kwitansi['company']['holding_company'] ?> (<?= strtoupper($kwitansi['company']['company']) ?>)</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">BANYAKNYA UANG <br> (AMOUNT)</td>
                        <td style="vertical-align: top;">: </td>
                        <td style="vertical-align: top;"><?= strtoupper(terbilang(formatter(($kwitansi['kwintansi']['harga_bulanan_pph']), "STR_TO_FLOAT"))) ?> RUPIAH</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">UNTUK PEMBAYARAN <br> (FOR PAYMENT)</td>
                        <td style="vertical-align: top;">: </td>
                        <td style="vertical-align: top;">PEMBELIAN <?= $kwitansi['kwintansi']['nama_barang'] ?> SEBANYAK <?= number_format($kwitansi['kwintansi']['qty'], 2) ?> KG DARI <?= $kwitansi['kwintansi']['supplier']['name'] ?></td>
                    </tr>
                </table>

                <table class="mt-1" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double; margin-top:35px;">
                    <tr style="font-size:14px;">
                        <td style="height:5%;">Bruto</td>
                        <td style="height:5%;">Rp.</td>
                        <td class="txt-right" style="height:2.5%;"><?= number_format($kwitansi['kwintansi']['harga_bulanan'], 2, '.', ',') ?></td>
                    </tr>
                    <tr style="font-size:14px;">
                        <td style="height:5%;">PPh</td>
                        <td style="height:5%;">Rp.</td>
                        <td class="txt-right" style="height:2.5%;"><?= number_format($kwitansi['kwintansi']['pph'], 2, '.', ',') ?></td>
                    </tr>
                    <tr style="font-size:14px;">
                        <td style="height:5%;">Dibayarkan</td>
                        <td style="height:5%;">Rp.</td>
                        <td class="txt-right" style="height:2.5%;"><?= number_format($kwitansi['kwintansi']['harga_bulanan_pph'], 2, '.', ',') ?></td>
                    </tr>
                </table>

                <div class="w-100" style="margin-top: 50px;text-align:center;">
                    <div style="text-align: right;">
                        <div>Medan, <?= date('d-m-Y', strtotime($kwitansi['tanggal']))  ?></div>
                        <div>yang Menerima</div><br><br>
                        <div class="mt-2">(<?= $kwitansi['kwintansi']['supplier']['name'] ?>)</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php endforeach; ?>
</body>

</html>
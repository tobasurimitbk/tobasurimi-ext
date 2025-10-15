<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak kwintansi TB All</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Times New Roman', Times, serif;
            width: 8.27in;
            height: 13in;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
        }

        @page {
            size: 216mm 330mm;
            margin: 0;
        }

        .align-items-center {
            align-items: center;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .bank-table th,
        .bank-table td {
            border: 1px solid;
        }

        .box-sm {
            border: 1px solid;
            margin-left: 1rem;
            margin-right: 0.5rem;
            height: 20px;
            width: 40px;
        }

        .bukti-pengeluaran {
            display: inline-block;
            vertical-align: middle;
        }

        .flex-column {
            flex-direction: column;
        }

        .h-100 {
            height: 100%;
        }

        .justify-content-end {
            justify-content: end;
        }

        .justify-content-even {
            justify-content: space-evenly;
        }

        .pagebreak {
            clear: both;
            page-break-after: always;
        }

        .sign-table td {
            border: 1px solid;
        }

        .sign-table td:last-child {
            border: none;
        }

        .txt-center {
            text-align: center;
        }

        .txt-left {
            text-align: left;
        }

        .txt-right {
            text-align: right;
        }

        .w-100 {
            width: 100%;
        }

        /* Fold marks */
        .fold-mark {
            position: absolute;
            width: 100%;
            /* border-top: 1px dashed #999; */
        }

        .fold-mark-1 {
            top: 4.33in;
        }

        .fold-mark-2 {
            top: 8.67in;
        }

        .section {
            position: relative;
            height: 3.9in;
            padding: 10px;
            margin-left: 15px;
            box-sizing: border-box;
        }

        .dotted-line {
            border-top: 1px dotted #000;
            margin: 3px 0;
        }

        .page {
            page-break-after: always;
        }

        .page {
            page-break-after: always;
            /* memaksa halaman baru saat cetak */
        }

        .section {
            margin-top: -5px;
        }

        .dotted-line {
            border-top: 1px dotted #000;
            margin: 20px 0;
        }

        /* Optional styling untuk tabel, teks kanan, dsb */
        .txt-right {
            text-align: right;
        }

        .w-100 {
            width: 100%;
        }

        .w-50 {
            width: 50%;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .content-between {
            justify-content: space-between;
        }
    </style>
</head>

<body>

    <?php foreach (array_chunk($dataResult, 3) as $chunk): ?>
        <div class="page" style="margin-top: 25px;">
            <?php foreach ($chunk as $index => $d): ?>
                <div class="section">
                    <div class="fold-mark fold-mark-1"></div>
                    <div class="d-flex content-between" style="margin-top: 0px;">
                        <table style="width: 100%;">
                            <tr>
                                <td>
                                    <div style="font-size: 15px; font-weight:bold;">
                                        <u>
                                            <?= $d["company"]["holding_company"] ?> (<?= $d["company"]["company"] ?>)
                                        </u>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    Tanggal: <?= date(
                                                    "d-m-Y",
                                                    strtotime($d["tanggal"])
                                                ) ?>
                                </td>
                            </tr>

                        </table>

                    </div>

                    <center style="margin-top: 20px;">
                        <div style="font-size: 15px; font-weight:bold;">
                            <u>
                                KWITANSI BULANAN
                            </u>
                        </div>
                        <div style="font-size: 13px;">
                            No. Nota : <?= $d["noKwitansi"] ?>
                        </div>
                    </center>

                    <table class="w-100 mt-2" style="margin-top: 20px;">
                        <tr>
                            <td style="vertical-align: top; width: 40%;">SUDAH TERIMA DARI <br> (RECEIVED FROM)</td>
                            <td style="vertical-align: top;">: </td>
                            <td style="vertical-align: top; width: 55%;"><?= $d["company"]["holding_company"] ?> (<?= strtoupper(
                                                                                                                        $d["company"]["company"]
                                                                                                                    ) ?>)</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">BANYAKNYA UANG <br> (AMOUNT)</td>
                            <td style="vertical-align: top;">: </td>
                            <td style="vertical-align: top;"><?= strtoupper(
                                                                    terbilang(
                                                                        formatter(
                                                                            $d["kwintansi"]["harga_bulanan_pph"],
                                                                            "STR_TO_FLOAT"
                                                                        )
                                                                    )
                                                                ) ?> RUPIAH</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">UNTUK PEMBAYARAN <br> (FOR PAYMENT)</td>
                            <td style="vertical-align: top;">: </td>
                            <td style="vertical-align: top;">PEMBELIAN <?= $d["kwintansi"]["nama_barang"] ?> SEBANYAK <?= number_format(
                                                                                                                            $d["kwintansi"]["qty"],
                                                                                                                            2
                                                                                                                        ) ?> KG DARI <?= $d["kwintansi"]["supplier"]["name"] ?></td>
                        </tr>
                    </table>

                    <table class="mt-1" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double; margin-top:15px;">
                        <tr style="font-size:14px;">
                            <td>Bruto</td>
                            <td>Rp.</td>
                            <td class="txt-right"><?= number_format(
                                                        $d["kwintansi"]["harga_bulanan"],
                                                        2,
                                                        ".",
                                                        ","
                                                    ) ?></td>
                        </tr>
                        <tr style="font-size:14px;">
                            <td>PPh</td>
                            <td>Rp.</td>
                            <td class="txt-right"><?= number_format(
                                                        $d["kwintansi"]["pph"],
                                                        2,
                                                        ".",
                                                        ","
                                                    ) ?></td>
                        </tr>
                        <tr style="font-size:14px;">
                            <td>Dibayarkan</td>
                            <td>Rp.</td>
                            <td class="txt-right"><?= number_format(
                                                        $d["kwintansi"]["harga_bulanan_pph"],
                                                        2,
                                                        ".",
                                                        ","
                                                    ) ?></td>
                        </tr>
                    </table>

                    <div class="w-100" style="margin-top: -20px;text-align:center;">
                        <div style="text-align: right;">
                            <div>Medan, <?= date(
                                            "d-m-Y",
                                            strtotime($d["tanggal"])
                                        ) ?></div>
                            <div>yang Menerima</div><br><br><br>
                            <div class="mt-2">(<?= $d["kwintansi"]["supplier"]["name"] ?>)</div>
                        </div>
                    </div>

                    <!-- Tambahkan garis hanya jika bukan invoice pertama -->
                    <div class="dotted-line"></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

</body>

</html>
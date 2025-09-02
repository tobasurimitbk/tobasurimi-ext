<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak kwintansi TB All</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
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
            width: 20px;
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

                    <div style="text-align: right;">
                        <u>Tanggal: <?= date(
                                        "d-m-Y",
                                        strtotime($d["tanggal"])
                                    ) ?></u>
                        <br><br>
                        KWITANSI BULANAN <br>
                        No. Nota : <?= $d["noKwitansi"] ?>
                    </div>

                    <div class="w-50 d-flex content-between" style="margin-top: 0px;">
                        <div style="width: 60%;padding: 0.5rem;">
                            <?= $d["company"]["holding_company"] ?> (<?= $d["company"]["company"] ?>)
                        </div>
                    </div>

                    <table class="w-100 mt-2">
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

                    <table class="mt-1" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double;">
                        <tr>
                            <td>Bruto</td>
                            <td>Rp.</td>
                            <td class="txt-right"><?= number_format(
                                                        $d["kwintansi"]["harga_bulanan"],
                                                        2,
                                                        ".",
                                                        ","
                                                    ) ?></td>
                        </tr>
                        <tr>
                            <td>PPh</td>
                            <td>Rp.</td>
                            <td class="txt-right"><?= number_format(
                                                        $d["kwintansi"]["pph"],
                                                        2,
                                                        ".",
                                                        ","
                                                    ) ?></td>
                        </tr>
                        <tr>
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumarry Jumlah Upah dan Jam Kerja</title>
    <style>
        body {
            height: 100%;
            font-family: 'Times New Roman', Times, serif;
            letter-spacing: 1px;
            font-size: 10px;
        }

        @page {
            margin: 100px 10px 10px 10px;
        }

        h4 {
            font-weight: normal;
            font-size: 15px;
            margin-bottom: 10px;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
        }

        #dashed-border-table {
            border-collapse: collapse;
        }

        #dashed-border-table th,
        #dashed-border-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
        }

        thead {
            font-weight: bold;
        }

        .content {
            /* margin-top tdk terlalu kecil, karena margin @page sudah besar */
            margin-top: 0;
        }

        .page-number:before {
            content: counter(page);
        }
    </style>
</head>

<body>
    <div class="header">
        <table width="100%" style="line-height: 0.7;">
            <tr align="left">
                <td>Hal : <span class="page-number"></span></td>
            </tr>
            <!-- <tr align="left">
                <td>Tgl : <?= date('d/m/Y') ?></td>
            </tr> -->
            <tr align="center">
                <td style="font-size:12px;">PT TOBA SURIMI</td>
            </tr>
            <tr align="center" style="font-weight: bold; font-size:15px">
                <td>SUMMARY JUMLAH UPAH & JUMLAH JAM KERJA</td>
            </tr>
            <tr align="center" style=" font-size:12px">
                <td> Bulan <?= convertMonthIndo($month) ?> Tahun <?= $year ?> Periode 1</td>
            </tr>
            <tr align="center" style=" font-size:12px">
                <td>Pembayaran dari tanggal <?= $startDate ?> s/d tanggal <?= $endDate ?> </td>
            </tr>
            <tr align="center" style=" font-size:12px">
                <td>Unit <?= $data['unit']['company'] ?> - Departemen <?= $data['divisi']['divisi'] ?> - Tipe / Gol : <?= $tipe ?></td>
            </tr>
        </table>
    </div>

    <div class="content">
        <table width="100%" style="margin-top: 20px;" border="1" id="dashed-border-table">
            <thead>
                <tr align="center">
                    <td>Bagian</td>
                    <td>Orang</td>
                    <td>Upah Pokok<br>(Rp)</td>
                    <td>Tunj+Cad<br>(Rp)</td>
                    <td>Lembur<br>(Rp)</td>
                    <!-- <td>Tunj.Ns/Mbl/Srg<br>(Rp)</td> -->
                    <td>Total Upah<br>(Rp)</td>
                    <td>Potongan <br>(Rp)</td>
                    <td>Upah Bersih <br>(Rp)</td>
                    <td>Jam Kerja <br>(Jam)</td>
                    <td>Jam Lembur <br>(Jam)</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['res'] as $d) : ?>
                    <?php if ($d['payrollTotal'][0]['totalEmployee'] != 0): ?>
                        <tr>
                            <td><?= $d['bagian'] ?></td>
                            <td><?= $d['payrollTotal'][0]['totalEmployee'] ?></td>
                            <td><?= number_format($d['payrollTotal'][0]['upahPokok'], 2, ',', '.')  ?></td>
                            <td><?= number_format(($d['payrollTotal'][0]['skala_upah']), 2, ',', '.')  ?></td>
                            <td><?= number_format(($d['payrollTotal'][0]['lembur']), 2, ',', '.')  ?></td>
                            <!-- <td><?= number_format(0, 2, ',', '.')  ?></td> -->
                            <td><?= number_format($d['payrollTotal'][0]['total_upah'], 2, ',', '.')  ?></td>
                            <td><?= number_format($d['payrollTotal'][0]['potongan'], 2, ',', '.')  ?></td>
                            <td><?= number_format($d['payrollTotal'][0]['upahBersih'], 2, ',', '.')  ?></td>
                            <td><?= number_format($d['totalJamKerja'], 2, ',', '.') ?></td>
                            <td><?= number_format($d['totalJamLembur'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr>
                    <td>Total</td>
                    <td><?= $data['orangTotal'] ?></td>
                    <td><?= number_format($data['upahPokokTotal'], 2, ',', '.')  ?></td>
                    <td><?= number_format($data['tunjanganPlusSkalaUpah'], 2, ',', '.')  ?></td>
                    <td><?= number_format($data['lemburTotal'], 2, ',', '.')  ?></td>
                    <!-- <td><?= number_format(0, 2, ',', '.') ?></td> -->
                    <td><?= number_format($data['totalUpah'], 2, ',', '.') ?></td>
                    <td><?= number_format($data['potongan'], 2, ',', '.')  ?></td>
                    <td><?= number_format($data['upahBersih'], 2, ',', '.') ?></td>
                    <td><?= number_format($data['jamKerja'], 2, ',', '.')  ?></td>
                    <td><?= number_format($data['lembur'], 2, ',', '.') ?></td>
                </tr>
            </tbody>

        </table>
    </div>

</body>

</html>
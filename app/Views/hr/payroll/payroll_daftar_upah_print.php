<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Upah Karyawan</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10px;
        }

        @page {
            margin: 100px 10px 10px 10px;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        tr {
            page-break-inside: avoid;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
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
                <td>DAFTAR UPAH KARYAWAN</td>
            </tr>
            <tr align="center" style=" font-size:12px">
                <td> Bulan <?= convertMonthIndo($month) ?> Tahun <?= $year ?> Periode 1</td>
            </tr>
            <tr align="center" style=" font-size:12px">
                <td>Pembayaran dari tanggal <?= $startDate ?> s/d tanggal <?= $endDate ?> </td>
            </tr>
        </table>
    </div>
    <div class="content">
        <?php foreach ($payrollData['dataPayroll'] as $i => $bagian): ?>

            <table style="page-break-before: <?= $i > 0 ? 'always' : 'auto' ?>" id="dashed-border-table">
                <thead>
                    <tr>
                        <td colspan="11" style="font-weight: bold; text-align:left;">
                            BAGIAN : <?= $bagian['namaBagian'] ?>
                        </td>
                    </tr>
                    <tr align="center">
                        <th>No</th>
                        <th>Kode</th>
                        <th>Karyawan</th>
                        <th>J.Hr</th>
                        <!-- <th>Total Upah <br> (Rp)</th> -->
                        <th>Upah Pokok <br> (Rp)</th>
                        <th>Tunj. Tdk Tetap <br> (Rp)</th>
                        <th>Lembur Kerja <br> (Rp)</th>
                        <th>Tunj.Ksjh <br> (Rp)</th>
                        <th>Potongan <br> (Rp)</th>
                        <th>Jumlah Upah <br> (Rp)</th>
                        <th>Tanda Tangan</th>
                    </tr>
                </thead>
                <?php $no = 1; ?>

                <?php foreach ($bagian['employees'] as $b) : ?>
                    <tr align="center">
                        <td><?= $no++ ?></td>
                        <td><?= $b['nip'] ?></td>
                        <td><?= $b['name'] ?></td>
                        <td><?= $b['hariKerja'] ?></td>
                        <!-- <td><?= number_format($b['totalUpah'], 2, ',', '.') ?></td> -->
                        <td><?= number_format($b['upahPokok'], 2, ',', '.') ?></td>
                        <td><?= number_format($b['tunjanganTidakTetap'], 2, ',', '.') ?></td>
                        <td><?= number_format($b['lemburKerja'], 2, ',', '.') ?></td>
                        <td><?= number_format($b['tunjanganKesejahteraan'], 2, ',', '.') ?></td>
                        <td><?= number_format($b['potongan'], 2, ',', '.') ?></td>
                        <td><?= number_format($b['jumlahUpah'], 2, ',', '.') ?></td>
                        <td style="padding: 25px;">

                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr>
                    <td colspan="4" style="text-align: right;">
                        Total
                    </td>
                    <!-- <td><?= number_format($bagian['totalTotalUpah'], 2, ',', '.')  ?></td> -->
                    <td><?= number_format($bagian['totalUpahPokok'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalTunjanganTidakTetap'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalLemburKerja'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalTunjanganKesejahteraan'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalPotongan'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalJumlahUpah'], 2, ',', '.')  ?></td>
                    <td></td>
                </tr>

                <!-- <tr>
                <td colspan="4" style="text-align: right;">
                    Total Keseluruhan
                </td>
                <td><?= number_format($payrollData['total']['subTotalUpah'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['total']['subTotalUangMakan'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['total']['subTotalUpahPokok'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['total']['subTotalLemburKerja'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['total']['subTotalTunjanganKesejahteraan'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['total']['subTotalPotongan'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['total']['subTotalJumlahUpah'], 2, ',', '.') ?></td>
                <td></td>
            </tr> -->
            </table>
        <?php endforeach; ?>
    </div>
</body>

</html>
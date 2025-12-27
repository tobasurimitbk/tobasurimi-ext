<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Upah Karyawan</title>
    <style>
        body {
            height: 100%;
            font-family: 'Times New Roman', Times, serif;
            letter-spacing: 1px;
            font-size: 8;
        }

        @page {
            size: 9.5in 11in landscape;
            margin: 100px 25px 25px 25px;
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
            border: 1px dashed #000;
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
            <tr align="left">
                <td>Tgl : <?= date('d/m/Y') ?></td>
            </tr>
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
        <table width="100%" style="margin-top: 10px;" border="1" id="dashed-border-table">
            <?php foreach ($payrollData['dataPayroll'] as $bagian): ?>
                <tr>
                    <td colspan="12" style="font-weight: bold; text-align:left;">
                        BAGIAN : <?= $bagian['namaBagian'] ?>
                    </td>
                </tr>
                <tr align="center">
                    <td>No</td>
                    <td>Kode</td>
                    <td>Karyawan</td>
                    <td>J.Hr</td>
                    <td>Total Upah <br> (Rp)</td>
                    <td>Uang Makan <br> (Rp)</td>
                    <td>Upah Pokok <br> (Rp)</td>
                    <td>Lembur Kerja <br> (Rp)</td>
                    <td>Tunj.Ksjh <br> (Rp)</td>
                    <td>Potongan <br> (Rp)</td>
                    <td>Jumlah Upah <br> (Rp)</td>
                    <td>Tanda Tangan</td>
                </tr>
                <?php $no = 1; ?>

                <?php foreach ($bagian['employees'] as $b) : ?>
                    <tr align="center">
                        <td><?= $no++ ?></td>
                        <td><?= $b['nip'] ?></td>
                        <td><?= $b['name'] ?></td>
                        <td><?= $b['hariKerja'] ?></td>
                        <td><?= number_format($b['totalUpah'], 2, ',', '.') ?></td>
                        <td><?= number_format($b['uangMakan'], 2, ',', '.') ?></td>
                        <td><?= number_format($b['upahPokok'], 2, ',', '.') ?></td>
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
                    <td><?= number_format($bagian['totalTotalUpah'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalUangMakan'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalUpahPokok'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalLemburKerja'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalTunjanganKesejahteraan'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalPotongan'], 2, ',', '.')  ?></td>
                    <td><?= number_format($bagian['totalJumlahUpah'], 2, ',', '.')  ?></td>
                    <td></td>
                </tr>

            <?php endforeach; ?>
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

        <table width="100%" style="margin-top: 20px;">
            <tr align="left" style="font-size:12px;">
                <td>PERINCIAN</td>
            </tr>
        </table>
        <br>

        <table>
            <tbody>
                <?php $tunjanganDisplay = ["Tunjangan Seragam", "Tunjangan Makan Malam", "Tunjangan Transport"]; ?>
                <?php $tunjanganTotalDisplay = 0; ?>
                <?php $gajiDivisiModel = new \App\Models\GajiDivisiModel(); ?>
                <?php foreach ($tunjanganDisplay as  $t) : ?>
                    <?php $nominalTunjDisplay = $gajiDivisiModel->getNominalByKomponenName($divisi['id'], $t); ?>
                    <?php $tunjanganTotalDisplay += $nominalTunjDisplay; ?>
                    <tr>
                        <td><?= $t; ?></td>
                        <td>:</td>
                        <td>Rp <?= number_format($nominalTunjDisplay, 2, ',', '.')  ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td>Jumlah Tunjangan</td>
                    <td>:</td>
                    <td>Rp <?= $tunjanganTotalDisplay; ?></td>
                </tr>
            </tbody>
        </table>

        <table width="100%" style="margin-top: 10px;">
            <tr align="left" style="font-size:12px;">
                <td>KETERANGAN</td>
            </tr>
        </table>
        <br>
        <?php $resultKeterangan = App\Models\PayrollsModel::convertionIDRMoneyTotal($payrollData['total']['subTotalJumlahUpah']); ?>
        <table>
            <?php foreach ($resultKeterangan as $i => $r) : ?>
                <?php if ($i % 3 == 0) : ?>
                    <tr>
                    <?php endif; ?>
                    <td>
                        <?= $r['lembar'] ?>
                    </td>
                    <td>:</td>
                    <td style="width:50px">
                        <?= $r['totalLembar'] ?>
                    </td>
                    <?php if (($i + 1) % 3 == 0) : ?>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>

    </div>




</body>


</html>
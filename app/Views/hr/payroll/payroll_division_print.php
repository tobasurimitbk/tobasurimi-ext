<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Upah Karyawan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 10px;
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
    </style>
</head>

<body>
    <table border="0" width="100%">
        <tr>
            <td width="210">
                <table border="0">
                    <tr>
                        <td>Hal</td>
                        <td>:</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Tgl</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime(date('Y-m-d'))) ?></td>
                    </tr>
                </table>
            </td>
            <td>

            </td>
        </tr>
    </table>
    <table width="100%">
        <tr align="center">
            <td colspan="3" style="text-align: center; font-size:14px;">PT TOBA SURIMI</td>
        </tr>
    </table>
    <table width="100%">
        <tr align="center" style="font-weight: bold; font-size:15px">
            <td>DAFTAR UPAH KARYAWAN</td>
        </tr>
        <tr align="center" style=" font-size:12px">
            <td> Bulan <?= date('M', strtotime("{$year}-{$month}-01")) ?> Tahun <?= $year ?> Periode 1</td>
        </tr>
        <tr align="center" style=" font-size:12px">
            <td>Pembayaran dari tanggal <?= $startDate ?> s/d tanggal <?= $endDate ?> </td>
        </tr>
    </table>

    <table border="0" style="margin-top: 30px;">
        <tr>
            <td>Departemen</td>
            <td>:</td>
            <td><?= $divisi['divisi'] ?></td>
        </tr>
    </table>

    <table width="100%" style="margin-top: 10px;" border="1" id="dashed-border-table">
        <thead>
            <tr align="center">
                <td>NO</td>
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
        </thead>
        <tbody>
            <?php
            $uangMakanTotal = 0;
            $tunjanganKesejahteraanTotal = 0; ?>
            <?php foreach ($payrollData['dataPayroll'] as $p) : ?>
                <?php
                $gajiConjunctionModel = new \App\Models\GajiConjunctionModel();
                $uangMakanNominal = $gajiConjunctionModel->getNominalByKomponenName(
                    "Uang Makan",
                    $p['employee_id']
                );
                $tunjanganKesejahteraanNominal = $gajiConjunctionModel->getNominalByKomponenName(
                    "Tunjangan Kesejahteraan",
                    $p['employee_id']
                );
                $uangMakanTotal += $uangMakanNominal;
                $tunjanganKesejahteraanTotal += $tunjanganKesejahteraanNominal;
                ?>
                <tr align="center">
                    <td><?= $p['no'] ?></td>
                    <td><?= $p['id'] ?></td>
                    <td><?= $p['name'] ?></td>
                    <td><?= $p['hariKerja'] ?></td>
                    <td><?= $p['jumlahUpah'] ?></td>
                    <td><?= number_format($uangMakanNominal, 2, ',', '.') ?></td>
                    <td><?= $p['upahPokok'] ?></td>
                    <td><?= $p['upahLembur'] ?></td>
                    <td><?= number_format($tunjanganKesejahteraanNominal, 2, ',', '.')   ?></td>
                    <td><?= $p['potongan'] ?></td>
                    <td><?= $p['jumlahUpah'] ?></td>
                    <td style="padding: 25px;">

                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" style="text-align: right;">
                    Total
                </td>
                <td><?= number_format($payrollData['total']['jumlahUpah'], 2, ',', '.')  ?></td>
                <td><?= number_format($uangMakanTotal, 2, ',', '.')  ?></td>
                <td><?= number_format($payrollData['total']['upahPokok'], 2, ',', '.')  ?></td>
                <td><?= number_format($payrollData['total']['upahLembur'], 2, ',', '.')  ?></td>
                <td><?= number_format($tunjanganKesejahteraanTotal, 2, ',', '.')  ?></td>
                <td><?= number_format($payrollData['total']['potongan'], 2, ',', '.')  ?></td>
                <td><?= number_format($payrollData['total']['jumlahUpah'], 2, ',', '.')  ?></td>
                <td></td>
            </tr>
        </tbody>
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
    <?php $resultKeterangan = App\Models\PayrollsModel::convertionIDRMoneyTotal($payrollData['total']['jumlahUpah']); ?>
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



</body>


</html>
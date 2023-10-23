<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumarry Jumlah Upah dan Jam Kerja</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 10px;
        }

        h4 {
            font-weight: normal;
            font-size: 15px;
            margin-bottom: 10px;
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
            <td>SUMMARY JUMLAH UPAH & JUMLAH JAM KERJA</td>
        </tr>
        <tr align="center" style=" font-size:12px">
            <td> Bulan <?= date('M', strtotime("{$year}-{$month}-01")) ?> Tahun <?= $year ?> Periode 1</td>
        </tr>
        <tr align="center" style=" font-size:12px">
            <td>Pembayaran dari tanggal <?= $startDate ?> s/d tanggal <?= $endDate ?> </td>
        </tr>
    </table>

    <table width="100%" style="margin-top: 20px;" border="1" id="dashed-border-table">
        <thead>
            <tr align="center">
                <td>Bagian</td>
                <td>Orang</td>
                <td>Upah Pokok<br>(Rp)</td>
                <td>Tunj+Cad<br>(Rp)</td>
                <td>Lembur<br>(Rp)</td>
                <td>Tunj.Ns/Mbl/Srg<br>(Rp)</td>
                <td>Total Upah<br>(Rp)</td>
                <td>Potongan <br>(Rp)</td>
                <td>Upah Bersih <br>(Rp)</td>
                <td>Jam Kerja <br>(Jam)</td>
                <td>Jam Lembur <br>(Jam)</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['res'] as $d) : ?>
                <tr>
                    <td><?= $d['divisi'] ?></td>
                    <td><?= $d['payrollTotal'][0]['totalEmployee'] ?></td>
                    <td><?= number_format($d['payrollTotal'][0]['upahBersih'], 2, ',', '.')  ?></td>
                    <td><?= number_format(($d['payrollTotal'][0]['tunjangan'] + $d['payrollTotal'][0]['cadangan']), 2, ',', '.')  ?></td>
                    <td><?= number_format(($d['payrollTotal'][0]['lembur']), 2, ',', '.')  ?></td>
                    <td><?= number_format(0, 2, ',', '.')  ?></td>
                    <td><?= number_format(($d['payrollTotal'][0]['upahBersih'] + $d['payrollTotal'][0]['tunjangan'] + $d['payrollTotal'][0]['cadangan']), 2, ',', '.')  ?></td>
                    <td><?= number_format($d['payrollTotal'][0]['potongan'], 2, ',', '.')  ?></td>
                    <td><?= number_format(($d['payrollTotal'][0]['upahBersih'] + $d['payrollTotal'][0]['tunjangan'] + $d['payrollTotal'][0]['cadangan'] - $d['payrollTotal'][0]['potongan']), 2, ',', '.')  ?></td>
                    <td><?= number_format($d['totalJamKerja'], 2, ',', '.') ?></td>
                    <td><?= number_format($d['totalJamLembur'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td>Total</td>
                <td><?= $data['orangTotal'] ?></td>
                <td><?= number_format($data['upahPokokTotal'], 2, ',', '.')  ?></td>
                <td><?= number_format($data['tunjanganPlusCadangan'], 2, ',', '.')  ?></td>
                <td><?= number_format($data['lemburTotal'], 2, ',', '.')  ?></td>
                <td><?= number_format(0, 2, ',', '.') ?></td>
                <td><?= number_format($data['totalUpah'], 2, ',', '.') ?></td>
                <td><?= number_format($data['potongan'], 2, ',', '.')  ?></td>
                <td><?= number_format($data['upahBersih'], 2, ',', '.') ?></td>
                <td><?= number_format($data['jamKerja'], 2, ',', '.')  ?></td>
                <td><?= number_format($data['lembur'], 2, ',', '.') ?></td>
            </tr>
        </tbody>

    </table>
</body>

</html>
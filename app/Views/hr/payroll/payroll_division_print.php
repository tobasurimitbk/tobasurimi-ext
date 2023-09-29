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
                <table border="0">
                    <tr>
                        <td colspan="3" style="text-align: center; font-size:14px;">PT TOBA SURIMI</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr align="center" style="font-weight: bold; font-size:15px">
            <td>DAFTAR UPAH KARYAWAN</td>
        </tr>
        <tr align="center" style=" font-size:12px">
            <td> Bulan <?= $month ?> Tahun <?= $year ?> Periode 1</td>
        </tr>
    </table>

    <table border="0">
        <tr>
            <td>Divisi</td>
            <td>:</td>
            <td><?= $divisi['divisi'] ?></td>
        </tr>
    </table>

    <table width="100%" style="margin-top: 10px;" border="1" id="dashed-border-table">
        <thead>
            <tr align="center">
                <td>NO</td>
                <td>Karyawan</td>
                <td>J.Hr</td>
                <td>Upah Pokok <br> (Rp)</td>
                <td>Upah Lembur <br> (Rp)</td>
                <td>Total Upah <br> (Rp)</td>
                <td>Potongan <br> (Rp)</td>
                <td>Jumlah Upah <br> (Rp)</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payrollData['dataPayroll'] as $p) : ?>
                <tr align="center">
                    <td><?= $p['no'] ?></td>
                    <td><?= $p['name'] ?></td>
                    <td><?= $p['hariKerja'] ?></td>
                    <td><?= $p['upahPokok'] ?></td>
                    <td><?= $p['upahLembur'] ?></td>
                    <td><?= $p['totalUpah'] ?></td>
                    <td><?= $p['potongan'] ?></td>
                    <td><?= $p['jumlahUpah'] ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align: right;">
                    Total
                </td>
                <td><?= number_format($payrollData['total']['upahPokok'], 2, ',', '.')  ?></td>
                <td><?= number_format($payrollData['total']['upahLembur'], 2, ',', '.')  ?></td>
                <td><?= number_format($payrollData['total']['totalUpah'], 2, ',', '.')  ?></td>
                <td><?= number_format($payrollData['total']['potongan'], 2, ',', '.')  ?></td>
                <td><?= number_format($payrollData['total']['jumlahUpah'], 2, ',', '.')  ?></td>
            </tr>
        </tbody>
    </table>

    <table width="100%" style="margin-top: 30px;">
        <tr align="left" style="font-size:12px;">
            <td>PERINCIAN KOMPONEN GAJI</td>
        </tr>
    </table>
    <br>

    <table>
        <tbody>
            <?php foreach ($komponenGaji as  $k) : ?>
                <tr>
                    <td><?= $k['name'] ?> <?= $k['tipe'] == "PLUS" ? "(+)" : "(-)"  ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table width="100%" style="margin-top: 30px;">
        <tr align="left" style="font-size:12px;">
            <td>KETERANGAN</td>
        </tr>
    </table>
    <br>
    <?php $resultKeterangan = App\Models\PayrollsModel::convertionIDRMoneyTotal($payrollData['total']['jumlahUpah']); ?>
    <table>
        <?php foreach ($resultKeterangan as $r) : ?>
            <tr>
                <td>
                    <?= $r['lembar'] ?>
                </td>
                <td>:</td>
                <td>
                    <?= $r['totalLembar'] ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>


</html>
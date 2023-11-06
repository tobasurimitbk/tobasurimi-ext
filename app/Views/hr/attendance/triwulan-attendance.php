<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi Triwulan PDF</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 9px;
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
            <td>DATA ABSENSI KARYAWAN</td>
        </tr>
        <tr align="center" style=" font-size:12px">
            <td>Unit <?= $unit['company'] ?> - Departemen <?= $divisi['divisi'] ?></td>
        </tr>
    </table>
    <table width="100%" style="margin-top: 20px;" border="1" id="dashed-border-table">
        <thead>
            <tr align="center">
                <td>No</td>
                <td>Kode</td>
                <td>Nama Karyawan</td>
                <td><?= convertToIndonesianMonth($startMonth)  ?></td>
                <td><?= convertToIndonesianMonth($middleMonth)  ?></td>
                <td><?= convertToIndonesianMonth($endMonth)  ?></td>
                <td>Jumlah Absensi<br>A+P+H</td>
                <td>Total Kupon<br>(LBR)</td>
                <td>Tanda Tangan</td>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php if (count($data['res']) == 0) : ?>
                <tr>
                    <td colspan="9" style="text-align: center;">
                        Tidak ada karyawan perempuan
                    </td>
                </tr>
            <?php else : ?>
                <?php $totalKupon = 0; ?>
                <?php foreach ($data['res'] as $d) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $d['id']; ?></td>
                        <td><?= $d['name']; ?></td>
                        <td>
                            <?php foreach ($d['kehadiran'] as $k) : ?>
                                <?php if ($k['yearMonth'] == $startMonth) : ?>
                                    0 A
                                    <?= $k['P'] ?> P
                                    0 H
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php foreach ($d['kehadiran'] as $k) : ?>
                                <?php if ($k['yearMonth'] == $middleMonth) : ?>
                                    0 A
                                    <?= $k['P'] ?> P
                                    0 H
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php foreach ($d['kehadiran'] as $k) : ?>
                                <?php if ($k['yearMonth'] == $endMonth) : ?>
                                    0 A
                                    <?= $k['P'] ?> P
                                    0 H
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?= $d['totalAPH'] ?>
                        </td>
                        <td><?= $d['totalKupon']  ?></td>
                        <td style="padding: 20px;"></td>
                    </tr>
                    <?php $totalKupon += $d['totalKupon']; ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="7" style="text-align: right;">Total</td>
                    <td><?= $totalKupon; ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>

        </tbody>

    </table>
</body>

</html>
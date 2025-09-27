<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Potongan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 7.5px;
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
            <td>DAFTAR POTONGAN</td>
        </tr>
        <tr align="center" style=" font-size:12px">
            <td> Bulan <?= date('M', strtotime("{$year}-{$month}-01")) ?> Tahun <?= $year ?> Periode 1</td>
        </tr>
        <tr align="center" style=" font-size:12px">
            <td>Unit <?= $payrollData['unit']['company'] ?> Departemen <?= $payrollData['divisi']['divisi'] ?></td>
        </tr>
    </table>

    <table width="100%" border="1" id="dashed-border-table" style="margin-top: 30px;">
        <?php foreach ($payrollData['res'] as $payroll) : ?>
            <tr>
                <td colspan="13">
                    Bagian : <?= $payroll['bagian'] ?>
                </td>
            <tr>
                <td>No</td>
                <td>Kode</td>
                <td>Kode Kop</td>
                <td>Nama Karyawan</td>
                <td>Pot.Iuran Koperasi</td>
                <td>Potongan STM</td>
                <td>Potongan ASTEK</td>
                <td>Potongan SPM</td>
                <td>Potongan Perlengkapan Kerja</td>
                <td>Potongan Denda</td>
                <td>Pot. Bon Koperasi</td>
                <td>Potongan Pinjaman</td>
                <td>Total Potongan</td>
            </tr>
            </tr>
            <?php $no = 1; ?>
            <?php foreach ($payroll['detail'] as $i => $pd) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $pd['employee']['nip'] ?></td>
                    <td></td>
                    <td><?= $pd['employee']['name'] ?></td>
                    <td><?= number_format($pd['potIuranKoperasi'], 2, ',', '.')  ?></td>
                    <td><?= number_format($pd['potStm'], 2, ',', '.')  ?></td>
                    <td><?= number_format($pd['potAstek'], 2, ',', '.')  ?></td>
                    <td><?= number_format($pd['potSpm'], 2, ',', '.')  ?></td>
                    <td><?= number_format($pd['potPerlengkapanKerja'], 2, ',', '.')  ?></td>
                    <td><?= number_format($pd['potDenda'], 2, ',', '.')  ?></td>
                    <td><?= number_format($pd['potBonKoperasi'], 2, ',', '.')  ?></td>
                    <td><?= number_format($pd['potPinjaman'], 2, ',', '.')  ?></td>
                    <td><?= number_format($pd['totPotongan'], 2, ',', '.')  ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" style="text-align: right;">Total</td>
                <td><?= number_format($payroll['totPotonganSingle']['totPotIuranKoperasi'], 2, ',', '.') ?></td>
                <td><?= number_format($payroll['totPotonganSingle']['totPotStm'], 2, ',', '.') ?></td>
                <td><?= number_format($payroll['totPotonganSingle']['totPotAstek'], 2, ',', '.') ?></td>
                <td><?= number_format($payroll['totPotonganSingle']['totPotSpm'], 2, ',', '.') ?></td>
                <td><?= number_format($payroll['totPotonganSingle']['totPerlengkapanKerja'], 2, ',', '.') ?></td>
                <td><?= number_format($payroll['totPotonganSingle']['totPotDenda'], 2, ',', '.') ?></td>
                <td><?= number_format($payroll['totPotonganSingle']['totPotBonKoperasi'], 2, ',', '.') ?></td>
                <td><?= number_format($payroll['totPotonganSingle']['totPotPinjaman'], 2, ',', '.') ?></td>
                <td><?= number_format($payroll['totPotonganSingle']['totPotongan'], 2, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4" style="text-align: right;">
                Total Keseluruhan
            </td>
            <td><?= number_format($payrollData['potAll']['totPotIuranKoperasi'], 2, ',', '.') ?></td>
            <td><?= number_format($payrollData['potAll']['totPotStm'], 2, ',', '.') ?></td>
            <td><?= number_format($payrollData['potAll']['totPotAstek'], 2, ',', '.') ?></td>
            <td><?= number_format($payrollData['potAll']['totPotSpm'], 2, ',', '.') ?></td>
            <td><?= number_format($payrollData['potAll']['totPerlengkapanKerja'], 2, ',', '.') ?></td>
            <td><?= number_format($payrollData['potAll']['totPotDenda'], 2, ',', '.') ?></td>
            <td><?= number_format($payrollData['potAll']['totPotBonKoperasi'], 2, ',', '.') ?></td>
            <td><?= number_format($payrollData['potAll']['totPotPinjaman'], 2, ',', '.') ?></td>
            <td><?= number_format($payrollData['potAll']['totPotongan'], 2, ',', '.') ?></td>
        </tr>
    </table>
</body>

</html>
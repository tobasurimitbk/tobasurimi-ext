<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Potongan</title>
    <style>
        body {
            height: 100%;
            font-family: 'Times New Roman', Times, serif;
            letter-spacing: 1px;
            font-size: 7;
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
    <div class="header" style="margin-left:50px;">
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
            <tr align="center" style="font-weight:bold;font-size:15px">
                <td>DAFTAR POTONGAN</td>
            </tr>
            <tr align="center" style="font-size:12px">
                <td>Bulan <?= convertMonthIndo($month) ?> Tahun <?= $year ?> Periode 1</td>
            </tr>
            <tr align="center" style="font-size:12px">
                <td>
                    Unit <?= $payrollData['unit']['company'] ?>
                    Departemen <?= $payrollData['divisi']['divisi'] ?>
                    Tipe/Gol : <?= $tipe ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        <table width="100%" border="1" id="dashed-border-table" style="margin-top: 30px;">
            <?php foreach ($payrollData['res'] as $payroll) : ?>
                <thead>
                    <tr>
                        <th colspan="19" style="font-weight:bold; text-align:left;">
                            BAGIAN : <?= $payroll['bagian'] ?>
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Kode Kop</th>
                        <th>Nama Karyawan</th>
                        <th>Pot. Iuran Koperasi</th>
                        <th>Pot. Stm</th>
                        <th>Pot. BPJS</th>
                        <th>Pot. Spm</th>
                        <th>Pot. Tutup Mulut</th>
                        <th>Pot. Baju Seragam</th>
                        <th>Pot. Sepatu, Celana, Topi</th>
                        <th>Pot. Denda</th>
                        <th>Pot. Kartu</th>
                        <th>Pot. Bon Koperasi</th>
                        <th>Pot. Pinjaman Koperasi</th>
                        <th>Pot. Pinjaman</th>
                        <th>Pot. Kantin</th>
                        <th>Pot. Pinjaman Lain-lain</th>
                        <th>Total Potongan</th>
                    </tr>
                </thead>
                <?php $no = 1; ?>
                <?php foreach ($payroll['detail'] as $i => $pd) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $pd['employee']['nip'] ?></td>
                        <td></td>
                        <td><?= $pd['employee']['name'] ?></td>
                        <td><?= number_format($pd['potIuranKoperasi'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potStm'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potBpjs'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potSpm'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potTutupMulut'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potBajuSeragam'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potSepatuCelanaTopi'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potDenda'], 2, ',', '.')  ?></td>
                        <td>0</td>
                        <td><?= number_format($pd['potBonKoperasi'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potPinjamanKoperasi'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potPinjaman'], 2, ',', '.')  ?></td>

                        <td><?= number_format($pd['potKantin'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['potPinjamanLainLain'], 2, ',', '.')  ?></td>
                        <td><?= number_format($pd['totPotongan'], 2, ',', '.')  ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" style="text-align: right;">Total</td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotIuranKoperasi'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotStm'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotBpjs'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotSpm'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotTutupMulut'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotBajuSeragam'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotSepatuCelanaTopi'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotDenda'], 2, ',', '.') ?></td>
                    <td>0</td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotBonKoperasi'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPinjamanKoperasi'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotPinjaman'], 2, ',', '.') ?></td>

                    <td><?= number_format($payroll['totPotonganSingle']['totPotKantin'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotPinjamanLainLain'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotongan'], 2, ',', '.') ?></td>

                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" style="text-align: right;">
                    Total Keseluruhan
                </td>
                <td><?= number_format($payrollData['potAll']['totPotIuranKoperasi'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotStm'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotBpjs'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotSpm'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotTutupMulut'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotBajuSeragam'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotSepatuCelanaTopi'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotDenda'], 2, ',', '.') ?></td>
                <td>0</td>
                <td><?= number_format($payrollData['potAll']['totPotBonKoperasi'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPinjamanKoperasi'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotPinjaman'], 2, ',', '.') ?></td>

                <td><?= number_format($payrollData['potAll']['totPotKantin'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotPinjamanLainLain'], 2, ',', '.') ?></td>
                <td><?= number_format($payrollData['potAll']['totPotongan'], 2, ',', '.') ?></td>

            </tr>
        </table>
    </div>
</body>

</html>
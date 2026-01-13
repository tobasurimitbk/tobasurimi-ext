<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar Potongan</title>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10px;
        }

        /* ================= PAGE ================= */
        @page {
            margin: 100px 10px 10px 10px;
        }

        /* ================= HEADER ================= */
        .header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
        }

        .page-number:before {
            content: counter(page);
        }

        /* ================= TABLE ================= */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .bagian-title {
            text-align: left;
            font-weight: bold;
        }

        .total-row td {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- ================= HEADER HALAMAN ================= -->
    <div class="header">
        <table width="100%" style="line-height: 0.5;">
            <tr>
                <td style="border:none; text-align:left;">
                    Hal : <span class="page-number"></span>
                </td>

            </tr>
            <tr>
                <td style="border:none; text-align:left;">
                    Tgl : <?= date('d/m/Y') ?>
                </td>
            </tr>
            <tr>
                <td style="border:none; text-align:center; font-size:12px;">
                    PT TOBA SURIMI
                </td>
            </tr>
            <tr>
                <td style="border:none; text-align:center; font-size:15px; font-weight:bold;">
                    DAFTAR POTONGAN
                </td>
            </tr>
            <tr>
                <td style="border:none; text-align:center; font-size:12px;">
                    Bulan <?= convertMonthIndo($month) ?> Tahun <?= $year ?> Periode 1
                </td>
            </tr>
            <tr>
                <td style="border:none; text-align:center; font-size:12px;">
                    Unit <?= $payrollData['unit']['company'] ?>
                    Departemen <?= $payrollData['divisi']['divisi'] ?>
                    Tipe/Gol : <?= $tipe ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= CONTENT ================= -->
    <?php foreach ($payrollData['res'] as $i => $payroll): ?>

        <table style="page-break-before: <?= $i > 0 ? 'always' : 'auto' ?>">

            <thead>
                <tr>
                    <th colspan="19" class="bagian-title">
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

            <tbody>
                <?php $no = 1;
                foreach ($payroll['detail'] as $pd): ?>
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
                        <td>0,00</td>
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
                    <td>0,00</td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotBonKoperasi'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPinjamanKoperasi'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotPinjaman'], 2, ',', '.') ?></td>

                    <td><?= number_format($payroll['totPotonganSingle']['totPotKantin'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotPinjamanLainLain'], 2, ',', '.') ?></td>
                    <td><?= number_format($payroll['totPotonganSingle']['totPotongan'], 2, ',', '.') ?></td>

                </tr>

            </tbody>
            <!-- <tfoot>
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
            </tfoot> -->

        </table>

    <?php endforeach; ?>

</body>

</html>
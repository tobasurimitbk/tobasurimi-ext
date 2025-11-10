<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            height: 100%;
            font-family: 'Times New Roman', Times, serif;
            letter-spacing: 1px;
            font-size: 8;
        }

        @page {
            size: 9.5in 11in landscape;
            margin: 25px;
            padding: 25px;
        }

        .page-break {
            page-break-after: always;
        }

        .karyawan-row {
            border-bottom: 1px dashed #000;
            margin-bottom: 10px;
        }

        .slip-container {
            width: 100%;
            border-collapse: collapse;
        }

        .slip {
            width: 100%;
            /* padding: 8px 10px; */
            border-right: 1px dashed #000;
        }

        .slip:last-child {
            border-right: none;
        }

        h4 {
            margin-left: 3px;
            margin-bottom: 3px;
            font-size: 12px;
            text-align: left;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 3px;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .signature {
            text-align: center;
            padding-top: 1px;
        }

        .copy-label {
            text-align: center;
            font-size: 7x;
            font-style: italic;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <?php foreach (array_chunk($payrollData['data'], 2) as $chunk): ?>
        <div class="page-break">
            <?php foreach ($chunk as $p): ?>
                <div class="karyawan-row" style="margin-top: -30px;">
                    <table class="slip-container">
                        <tr>
                            <?php foreach (['Karyawan', 'Perusahaan'] as $tipeSlip): ?>
                                <td style="width:50%; vertical-align:top;">
                                    <div class="slip">
                                        <br>
                                        <h4>TANDA TERIMA UPAH</h4>
                                        <table style="line-height: 8px;">
                                            <tr>
                                                <td colspan="3">Tenaga Kerja Harian Tetap</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 120px;">Kode Karyawan</td>
                                                <td style="width: 10px;">:</td>
                                                <td><?= $p['employee']['nip'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Tahun / Bulan</td>
                                                <td>:</td>
                                                <td>Tahun : <?= explode('-', $p['payroll']['year_month'])[0] ?> Bulan : <?= explode('-', $p['payroll']['year_month'])[1] ?> Periode : 1</td>
                                            </tr>
                                            <tr>
                                                <td>No. Induk</td>
                                                <td>:</td>
                                                <td>
                                                    <?= $p['employee']['nip'] ?>
                                                    Bagian : <?= $p['employee']['nama_bagian'] ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nama</td>
                                                <td>:</td>
                                                <td><?= $p['employee']['name'] ?></td>
                                            </tr>
                                        </table>

                                        <hr style="border: 0.5px dashed #000;">

                                        <table style="line-height: 8px;" border="0">
                                            <tr>
                                                <td style="width:260px;">Hari Kerja</td>
                                                <td style="width: 5px; text-align:right;">:</td>
                                                <td><?= $p['payroll']['hadir_final'] ?> Hari</td>
                                            </tr>
                                            <tr>
                                                <td>Tambahan Hari Libur Tidak Kerja</td>
                                                <td>:</td>
                                                <td><?= $p['payroll']['libur'] ?> Hari</td>
                                            </tr>
                                            <tr>
                                                <td>Tambahan Hari Libur Resmi</td>
                                                <td>:</td>
                                                <td>0 Hari</td>
                                            </tr>
                                            <tr>
                                                <td><?= $tunjanganGajiPokok == null ? "" : ucfirst(strtolower($tunjanganGajiPokok['name'])) ?></td>
                                                <td>:</td>
                                                <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_harian'], 2, ',', '.') ?>/Hari</td>
                                            </tr>
                                            <tr>
                                                <td><?= $tunjanganCadangan == null ? "" : ucfirst(strtolower($tunjanganCadangan['name'])) ?></td>
                                                <td>:</td>
                                                <td><?= "Rp " . number_format($p['payroll']['nominal_cadangan'], 2, ',', '.') ?>/Hari</td>
                                            </tr>
                                            <tr>
                                                <td>Total Gaji</td>
                                                <td>:</td>
                                                <td>
                                                    <?= "Rp " . number_format($p['payroll']['total_gaji_harian_plus_cadangan'], 2, ',', '.') ?>/Hari : <?= "Rp. " . number_format($p['payroll']['nominal_uang_gaji'], 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Lembur I</td>
                                                <td>:</td>
                                                <td><?= $p['totalLemburJamPertama'] ?> Jam</td>
                                            </tr>
                                            <tr>
                                                <td>Lembur II</td>
                                                <td>:</td>
                                                <td><?= $p['totalLemburJamKedua'] ?> Jam</td>
                                            </tr>
                                            <tr>
                                                <td>Total Uang Lembur I & II</td>
                                                <td>:</td>
                                                <td><?= "Rp " . number_format($p['payroll']['nominal_uang_lembur'], 2, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td>Total Gaji & Lembur</td>
                                                <td>:</td>
                                                <td><?= "Rp " . number_format($p['payroll']['nominal_uang_gaji'] + $p['payroll']['nominal_uang_lembur'], 2, ',', '.') ?></td>
                                            </tr>
                                        </table>

                                        <hr style="border: 0.5px dashed #000;">

                                        <table style="line-height: 8px;" border="0">
                                            <?php foreach ($p['perhitunganGaji'] as $g): ?>
                                                <tr>
                                                    <td style="width:260px;"><?= ucfirst(strtolower($g['name'])) ?></td>
                                                    <td style="width: 5px;">:</td>
                                                    <td><?= "Rp " . number_format($g['nominal'], 2, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>

                                        </table>

                                        <hr style="border: 0.5px dashed #000;">
                                        <table style="line-height: 8px; font-size:12px;" border="0">
                                            <tr>
                                                <td style="width:260px;">Sisa Diterima</td>
                                                <td style="width: 5px;">:</td>
                                                <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_diterima'], 2, ',', '.') ?></td>
                                            </tr>

                                        </table>
                                        <hr style="border: 0.5px dashed #000;">


                                        <table width="100%">
                                            <tr>
                                                <td class="signature">JURU BAYAR<br><br><br>(__________________)</td>
                                                <td class="signature">PENERIMA<br><br><br>(__________________)</td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</body>

</html>
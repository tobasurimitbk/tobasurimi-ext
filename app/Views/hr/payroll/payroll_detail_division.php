<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Upah Detail</title>
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

        @page {
            size: 9.44in 10.00in landscape;
            margin: 29px;
            padding: 29px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <?php foreach ($payrollData['data'] as $i => $p) : ?>
        <?php if ($i == 0) : ?>
            <table border="0" width="100%">
                <tr>
                    <td>
                        <h4>
                            TANDA TERIMA UPAH
                        </h4>
                        <div class="header-text">
                            <table>
                                <tr>
                                    <td colspan="3">Tenaga Kerja Harian Tetap</td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td>Company Name </td>
                                    <td>:</td>
                                    <td><?= $payrollData['company']['company'] ?></td>
                                </tr>
                                <tr>
                                    <td>Kode Karyawan </td>
                                    <td>:</td>
                                    <td><?= $p['employee']['id'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tahun / Bulan / Periode </td>
                                    <td>:</td>
                                    <td><?= $payrollData['year'] ?> / <?= $payrollData['month'] ?> / 1</td>
                                </tr>
                                <tr>
                                    <td>No Induk / Divisi</td>
                                    <td>:</td>
                                    <td> <?= $p['employee']['nip'] ?> / <?= $payrollData['divisi']['divisi'] ?></td>
                                </tr>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td> <?= $p['employee']['name'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>

                    <td>
                        <h4>
                            TANDA TERIMA UPAH
                        </h4>
                        <div class="header-text">
                            <table>
                                <tr>
                                    <td colspan="3">Tenaga Kerja Harian Tetap</td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td>Company Name </td>
                                    <td>:</td>
                                    <td><?= $payrollData['company']['company'] ?></td>
                                </tr>
                                <tr>
                                    <td>Kode Karyawan </td>
                                    <td>:</td>
                                    <td><?= $p['employee']['id'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tahun / Bulan / Periode </td>
                                    <td>:</td>
                                    <td><?= $payrollData['year'] ?> / <?= $payrollData['month'] ?> / 1</td>
                                </tr>
                                <tr>
                                    <td>No Induk / Divisi</td>
                                    <td>:</td>
                                    <td> <?= $p['employee']['nip'] ?> / <?= $payrollData['divisi']['divisi'] ?></td>
                                </tr>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td> <?= $p['employee']['name'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </td>

                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>Hari Kerja</td>
                                <td>:</td>
                                <td><?= $p['payroll']['hadir_final'] ?> Hari</td>
                            </tr>
                            <tr>
                                <td>Tambahan Hari Libur Kerja</td>
                                <td>:</td>
                                <td><?= $p['payroll']['libur'] ?> Hari</td>
                            </tr>
                            <tr>
                                <td>Tambahan Hari Libur Resmi</td>
                                <td>:</td>
                                <td>0 Hari</td>
                            </tr>
                            <tr>
                                <td>Gaji</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_harian'], 2, ',', '.') ?>/Hari</td>
                            </tr>
                            <tr>
                                <td>Cadangan</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_cadangan'], 2, ',', '.') ?>/Hari</td>
                            </tr>
                            <tr>
                                <td>Total Gaji</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format(($p['payroll']['nominal_gaji_harian'] + $p['payroll']['nominal_cadangan']), 2, ',', '.') ?>/Hari</td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_uang_gaji'], 2, ',', '.') ?></td>
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
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td>Hari Kerja</td>
                                <td>:</td>
                                <td><?= $p['payroll']['hadir_final'] ?> Hari</td>
                            </tr>
                            <tr>
                                <td>Tambahan Hari Libur Kerja</td>
                                <td>:</td>
                                <td><?= $p['payroll']['libur'] ?> Hari</td>
                            </tr>
                            <tr>
                                <td>Tambahan Hari Libur Resmi</td>
                                <td>:</td>
                                <td>0 Hari</td>
                            </tr>
                            <tr>
                                <td>Gaji</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_harian'], 2, ',', '.') ?>/Hari</td>
                            </tr>
                            <tr>
                                <td>Cadangan</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_cadangan'], 2, ',', '.') ?>/Hari</td>
                            </tr>
                            <tr>
                                <td>Total Gaji</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format(($p['payroll']['nominal_gaji_harian'] + $p['payroll']['nominal_cadangan']), 2, ',', '.') ?>/Hari</td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_uang_gaji'], 2, ',', '.') ?></td>
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
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>Total Gaji & Lembur</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_uang_gaji'] + $p['payroll']['nominal_uang_lembur'], 2, ',', '.') ?></td>
                            </tr>
                            <?php foreach ($p['perhitunganGaji'] as  $pg) : ?>
                                <?php if ($pg['tipe'] == "MINUS") : ?>
                                    <tr>
                                        <td><?= $pg['name'] ?></td>
                                        <td>:</td>
                                        <td><?= "Rp " . number_format($pg['nominal'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <tr>
                                <td>Potongan Lain-Lain</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format($p['totalNominalKeterlambatanPresensi'] + $p['totalNominalRekapPerizinanNotApproved'], 2, ',', '.') ?></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td>Total Gaji & Lembur</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_uang_gaji'] + $p['payroll']['nominal_uang_lembur'], 2, ',', '.') ?></td>
                            </tr>
                            <?php foreach ($p['perhitunganGaji'] as  $pg) : ?>
                                <?php if ($pg['tipe'] == "MINUS") : ?>
                                    <tr>
                                        <td><?= $pg['name'] ?></td>
                                        <td>:</td>
                                        <td><?= "Rp " . number_format($pg['nominal'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <tr>
                                <td>Potongan Lain-Lain</td>
                                <td>:</td>
                                <td><?= "Rp " . number_format($p['totalNominalKeterlambatanPresensi'] + $p['totalNominalRekapPerizinanNotApproved'], 2, ',', '.') ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <?php foreach ($p['perhitunganGaji'] as  $pg) : ?>
                                <?php if ($pg['tipe'] == "PLUS") : ?>
                                    <tr>
                                        <td><?= $pg['name'] ?></td>
                                        <td>:</td>
                                        <td><?= "Rp " . number_format($pg['nominal'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </table>
                    </td>
                    <td>
                        <table>
                            <?php foreach ($p['perhitunganGaji'] as  $pg) : ?>
                                <?php if ($pg['tipe'] == "PLUS") : ?>
                                    <tr>
                                        <td><?= $pg['name'] ?></td>
                                        <td>:</td>
                                        <td><?= "Rp " . number_format($pg['nominal'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </table>
                    </td>
                </tr> -->
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td colspan="2">Sisa Diterima</td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_diterima'], 2, ',', '.') ?></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td colspan="2">Sisa Diterima</td>
                                <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_diterima'], 2, ',', '.') ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td>
                        <center style="margin-top: 20px; margin-bottom:20px;">
                            TANDA TANGAN
                        </center>
                    </td>
                </tr>
                <tr align="center">
                    <td>
                        <table align="center">
                            <tr align="center">
                                <td>
                                    JURU BAYAR <br><br><br><br><br><br><br>

                                    (______________________) <br>
                                </td>
                                <td>
                                    PENERIMA <br><br><br><br><br><br><br>

                                    (______________________) <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        PENERIMA <br><br><br><br><br><br><br>

                        (______________________) <br>
                    </td>
                </tr>
            </table>
        <?php else : ?>
            <div class="page-break">
                <table border="0" width="100%">
                    <tr>
                        <td>
                            <h4>
                                TANDA TERIMA UPAH
                            </h4>
                            <div class="header-text">
                                <table>
                                    <tr>
                                        <td colspan="3">Tenaga Kerja Harian Tetap</td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td>Company Name </td>
                                        <td>:</td>
                                        <td><?= $payrollData['company']['company'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Kode Karyawan </td>
                                        <td>:</td>
                                        <td><?= $p['employee']['id'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Tahun / Bulan / Periode </td>
                                        <td>:</td>
                                        <td><?= $payrollData['year'] ?> / <?= $payrollData['month'] ?> / 1</td>
                                    </tr>
                                    <tr>
                                        <td>No Induk / Divisi</td>
                                        <td>:</td>
                                        <td> <?= $p['employee']['nip'] ?> / <?= $payrollData['divisi']['divisi'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Nama</td>
                                        <td>:</td>
                                        <td> <?= $p['employee']['name'] ?></td>
                                    </tr>
                                </table>
                            </div>
                        </td>

                        <td>
                            <h4>
                                TANDA TERIMA UPAH
                            </h4>
                            <div class="header-text">
                                <table>
                                    <tr>
                                        <td colspan="3">Tenaga Kerja Harian Tetap</td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td>Company Name </td>
                                        <td>:</td>
                                        <td><?= $payrollData['company']['company'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Kode Karyawan </td>
                                        <td>:</td>
                                        <td><?= $p['employee']['id'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Tahun / Bulan / Periode </td>
                                        <td>:</td>
                                        <td><?= $payrollData['year'] ?> / <?= $payrollData['month'] ?> / 1</td>
                                    </tr>
                                    <tr>
                                        <td>No Induk / Divisi</td>
                                        <td>:</td>
                                        <td> <?= $p['employee']['nip'] ?> / <?= $payrollData['divisi']['divisi'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Nama</td>
                                        <td>:</td>
                                        <td> <?= $p['employee']['name'] ?></td>
                                    </tr>
                                </table>
                            </div>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td>Hari Kerja</td>
                                    <td>:</td>
                                    <td><?= $p['payroll']['hadir_final'] ?> Hari</td>
                                </tr>
                                <tr>
                                    <td>Tambahan Hari Libur Kerja</td>
                                    <td>:</td>
                                    <td><?= $p['payroll']['libur'] ?> Hari</td>
                                </tr>
                                <tr>
                                    <td>Tambahan Hari Libur Resmi</td>
                                    <td>:</td>
                                    <td>0 Hari</td>
                                </tr>
                                <tr>
                                    <td>Gaji</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_harian'], 2, ',', '.') ?>/Hari</td>
                                </tr>
                                <tr>
                                    <td>Cadangan</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_cadangan'], 2, ',', '.') ?>/Hari</td>
                                </tr>
                                <tr>
                                    <td>Total Gaji</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format(($p['payroll']['nominal_gaji_harian'] + $p['payroll']['nominal_cadangan']), 2, ',', '.') ?>/Hari</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_uang_gaji'], 2, ',', '.') ?></td>
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
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>Hari Kerja</td>
                                    <td>:</td>
                                    <td><?= $p['payroll']['hadir_final'] ?> Hari</td>
                                </tr>
                                <tr>
                                    <td>Tambahan Hari Libur Kerja</td>
                                    <td>:</td>
                                    <td><?= $p['payroll']['libur'] ?> Hari</td>
                                </tr>
                                <tr>
                                    <td>Tambahan Hari Libur Resmi</td>
                                    <td>:</td>
                                    <td>0 Hari</td>
                                </tr>
                                <tr>
                                    <td>Gaji</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_harian'], 2, ',', '.') ?>/Hari</td>
                                </tr>
                                <tr>
                                    <td>Cadangan</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_cadangan'], 2, ',', '.') ?>/Hari</td>
                                </tr>
                                <tr>
                                    <td>Total Gaji</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format(($p['payroll']['nominal_gaji_harian'] + $p['payroll']['nominal_cadangan']), 2, ',', '.') ?>/Hari</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_uang_gaji'], 2, ',', '.') ?></td>
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
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td>Total Gaji & Lembur</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_uang_gaji'] + $p['payroll']['nominal_uang_lembur'], 2, ',', '.') ?></td>
                                </tr>
                                <?php foreach ($p['perhitunganGaji'] as  $pg) : ?>
                                    <?php if ($pg['tipe'] == "MINUS") : ?>
                                        <tr>
                                            <td><?= $pg['name'] ?></td>
                                            <td>:</td>
                                            <td><?= "Rp " . number_format($pg['nominal'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <tr>
                                    <td>Potongan Lain-Lain</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format($p['totalNominalKeterlambatanPresensi'] + $p['totalNominalRekapPerizinanNotApproved'], 2, ',', '.') ?></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>Total Gaji & Lembur</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_uang_gaji'] + $p['payroll']['nominal_uang_lembur'], 2, ',', '.') ?></td>
                                </tr>
                                <?php foreach ($p['perhitunganGaji'] as  $pg) : ?>
                                    <?php if ($pg['tipe'] == "MINUS") : ?>
                                        <tr>
                                            <td><?= $pg['name'] ?></td>
                                            <td>:</td>
                                            <td><?= "Rp " . number_format($pg['nominal'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <tr>
                                    <td>Potongan Lain-Lain</td>
                                    <td>:</td>
                                    <td><?= "Rp " . number_format($p['totalNominalKeterlambatanPresensi'] + $p['totalNominalRekapPerizinanNotApproved'], 2, ',', '.') ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <?php foreach ($p['perhitunganGaji'] as  $pg) : ?>
                                    <?php if ($pg['tipe'] == "PLUS") : ?>
                                        <tr>
                                            <td><?= $pg['name'] ?></td>
                                            <td>:</td>
                                            <td><?= "Rp " . number_format($pg['nominal'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </table>
                        </td>
                        <td>
                            <table>
                                <?php foreach ($p['perhitunganGaji'] as  $pg) : ?>
                                    <?php if ($pg['tipe'] == "PLUS") : ?>
                                        <tr>
                                            <td><?= $pg['name'] ?></td>
                                            <td>:</td>
                                            <td><?= "Rp " . number_format($pg['nominal'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </table>
                        </td>
                    </tr> -->
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td colspan="2">Sisa Diterima</td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_diterima'], 2, ',', '.') ?></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td colspan="2">Sisa Diterima</td>
                                    <td><?= "Rp " . number_format($p['payroll']['nominal_gaji_diterima'], 2, ',', '.') ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <center style="margin-top: 20px; margin-bottom:20px;">
                                TANDA TANGAN
                            </center>
                        </td>
                    </tr>
                    <tr align="center">
                        <td>
                            <table align="center">
                                <tr align="center">
                                    <td>
                                        JURU BAYAR <br><br><br><br><br><br><br>

                                        (______________________) <br>
                                    </td>
                                    <td>
                                        PENERIMA <br><br><br><br><br><br><br>

                                        (______________________) <br>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            PENERIMA <br><br><br><br><br><br><br>

                            (______________________) <br>
                        </td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Upah</title>
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
    </style>
</head>

<body>
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
                            <td>Unit </td>
                            <td>:</td>
                            <td><?= $company['company'] ?></td>
                        </tr>
                        <tr>
                            <td>Kode Karyawan </td>
                            <td>:</td>
                            <td><?= $employee['id'] ?></td>
                        </tr>
                        <tr>
                            <td>Tahun / Bulan / Periode </td>
                            <td>:</td>
                            <td><?= $year ?> / <?= $month ?> / 1</td>
                        </tr>
                        <tr>
                            <td>No Induk / Departemen</td>
                            <td>:</td>
                            <td> <?= $employee['nip'] ?> / <?= $employee['divisi'] ?></td>
                        </tr>
                        <tr>
                            <td>Bagian</td>
                            <td>:</td>
                            <td> <?= $employee['nama_bagian'] ?> </td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td> <?= $employee['name'] ?></td>
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
                            <td>Unit </td>
                            <td>:</td>
                            <td><?= $company['company'] ?></td>
                        </tr>
                        <tr>
                            <td>Kode Karyawan </td>
                            <td>:</td>
                            <td><?= $employee['id'] ?></td>
                        </tr>
                        <tr>
                            <td>Tahun / Bulan / Periode </td>
                            <td>:</td>
                            <td><?= $year ?> / <?= $month ?> / 1</td>
                        </tr>
                        <tr>
                            <td>No Induk / Departemen</td>
                            <td>:</td>
                            <td> <?= $employee['nip'] ?> / <?= $employee['divisi'] ?></td>
                        </tr>
                        <tr>
                            <td>Bagian</td>
                            <td>:</td>
                            <td> <?= $employee['nama_bagian'] ?> </td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td> <?= $employee['name'] ?></td>
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
                        <td style="width: 248px;">Hari Kerja</td>
                        <td>:</td>
                        <td><?= $payroll['hadir_final'] ?> Hari</td>
                    </tr>
                    <tr>
                        <td>Tambahan Hari Libur Kerja</td>
                        <td>:</td>
                        <td><?= $payroll['libur'] ?> Hari</td>
                    </tr>
                    <tr>
                        <td>Gaji</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format($payroll['nominal_gaji_harian'], 2, ',', '.') ?>/Hari</td>
                    </tr>
                    <tr>
                        <td>Cadangan</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format($payroll['nominal_cadangan'], 2, ',', '.') ?>/Hari</td>
                    </tr>
                    <tr>
                        <td>Total Gaji</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format(($payroll['nominal_gaji_harian'] + $payroll['nominal_cadangan']), 2, ',', '.') ?>/Hari</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td><?= "Rp " . number_format($payroll['nominal_uang_gaji'], 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Lembur I</td>
                        <td>:</td>
                        <td><?= $totalLemburJamPertama ?> Jam</td>
                    </tr>
                    <tr>
                        <td>Lembur II</td>
                        <td>:</td>
                        <td><?= $totalLemburJamKedua ?> Jam</td>
                    </tr>
                    <tr>
                        <td>Total Uang Lembur I & II</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format($payroll['nominal_uang_lembur'], 2, ',', '.') ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td style="width: 248px;">Hari Kerja</td>
                        <td>:</td>
                        <td><?= $payroll['hadir_final'] ?> Hari</td>
                    </tr>
                    <tr>
                        <td>Tambahan Hari Libur Kerja</td>
                        <td>:</td>
                        <td><?= $payroll['libur'] ?> Hari</td>
                    </tr>
                    <tr>
                        <td>Gaji</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format($payroll['nominal_gaji_harian'], 2, ',', '.') ?>/Hari</td>
                    </tr>
                    <tr>
                        <td>Cadangan</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format($payroll['nominal_cadangan'], 2, ',', '.') ?>/Hari</td>
                    </tr>
                    <tr>
                        <td>Total Gaji</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format(($payroll['nominal_gaji_harian'] + $payroll['nominal_cadangan']), 2, ',', '.') ?>/Hari</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td><?= "Rp " . number_format($payroll['nominal_uang_gaji'], 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Lembur I</td>
                        <td>:</td>
                        <td><?= $totalLemburJamPertama ?> Jam</td>
                    </tr>
                    <tr>
                        <td>Lembur II</td>
                        <td>:</td>
                        <td><?= $totalLemburJamKedua ?> Jam</td>
                    </tr>
                    <tr>
                        <td>Total Uang Lembur I & II</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format($payroll['nominal_uang_lembur'], 2, ',', '.') ?></td>
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
                        <td style="width: 248px;">Total Gaji & Lembur</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format($payroll['nominal_uang_gaji'] + $payroll['nominal_uang_lembur'], 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Pinjaman</td>
                        <td>:</td>
                        <td> <?= "Rp " . number_format($payroll['nominal_pinjaman_karyawan'], 2, ',', '.') ?></td>
                    </tr>
                    <?php $potIuranPinjamanBon = 0; ?>
                    <?php foreach ($perhitunganGaji as  $p) : ?>
                        <?php if ($p['tipe'] == "MINUS") : ?>
                            <?php if (in_array($p['name'], ["Potongan Iuran Koperasi", "Potongan Pinjaman Koperasi", "Potongan Bon Koperasi"])) : ?>
                                <?php $potIuranPinjamanBon += $p['nominal']; ?>
                            <?php endif; ?>
                            <?php if (!in_array($p['name'], ["Potongan Iuran Koperasi", "Potongan Pinjaman Koperasi", "Potongan Bon Koperasi"])) : ?>
                                <tr>
                                    <td><?= $p['name'] ?></td>
                                    <td>:</td>
                                    <td> <?= "Rp " . number_format($p['nominal'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td>Potongan Iuran/Pinjaman/Bon Koperasi</td>
                        <td>:</td>
                        <td> <?= "Rp " . number_format($potIuranPinjamanBon, 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Lain-Lain</td>
                        <td>:</td>
                        <td> <?= "Rp " . number_format($totalNominalKeterlambatanPresensi, 2, ',', '.') ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td style="width: 248px;">Total Gaji & Lembur</td>
                        <td>:</td>
                        <td><?= "Rp " . number_format($payroll['nominal_uang_gaji'] + $payroll['nominal_uang_lembur'], 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Pinjaman</td>
                        <td>:</td>
                        <td> <?= "Rp " . number_format($payroll['nominal_pinjaman_karyawan'], 2, ',', '.') ?></td>
                    </tr>
                    <?php $potIuranPinjamanBon = 0; ?>
                    <?php foreach ($perhitunganGaji as  $p) : ?>
                        <?php if ($p['tipe'] == "MINUS") : ?>
                            <?php if (in_array($p['name'], ["Potongan Iuran Koperasi", "Potongan Pinjaman Koperasi", "Potongan Bon Koperasi"])) : ?>
                                <?php $potIuranPinjamanBon += $p['nominal']; ?>
                            <?php endif; ?>
                            <?php if (!in_array($p['name'], ["Potongan Iuran Koperasi", "Potongan Pinjaman Koperasi", "Potongan Bon Koperasi"])) : ?>
                                <tr>
                                    <td><?= $p['name'] ?></td>
                                    <td>:</td>
                                    <td> <?= "Rp " . number_format($p['nominal'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td>Potongan Iuran/Pinjaman/Bon Koperasi</td>
                        <td>:</td>
                        <td> <?= "Rp " . number_format($potIuranPinjamanBon, 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Potongan Lain-Lain</td>
                        <td>:</td>
                        <td> <?= "Rp " . number_format($totalNominalKeterlambatanPresensi, 2, ',', '.') ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan=" 2">
                <hr>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td colspan="2">Sisa Diterima</td>
                        <td><?= "Rp " . number_format($payroll['nominal_gaji_diterima'], 2, ',', '.') ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td colspan="2">Sisa Diterima</td>
                        <td><?= "Rp " . number_format($payroll['nominal_gaji_diterima'], 2, ',', '.') ?></td>
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
</body>

</html>
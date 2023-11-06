<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=AbsensiFinal_" . $yearMonth . ".xls");
?>
<b>
    Unit: <?= $company['company'] ?><br>
    Departemen: <?= $divisi != null ? $divisi['divisi'] : "Semua Departemen" ?><br>
    Bulan: <?= date('F - Y', strtotime($yearMonth)) ?> <br>
    Periode: <?= $startDate ?> s.d <?= $endDate ?> <br>
    Golongan: <?= $golongan != null ? $golongan['golongan_name'] : "Semua Golongan" ?>
</b>
<br>
<b>
    Rekap Data Absensi Final
</b>
<br>
<table border="1">
    <thead>
        <tr align="center" style="font-weight: bold;">
            <td rowspan="2" style="text-align: center;">No</td>
            <td rowspan="2" style="text-align: center;">Karyawan</td>
            <td rowspan="2" style="text-align: center;">Departemen</td>
            <td rowspan="2" style="text-align: center;">Bagian</td>
            <td colspan="<?= $startMonth['totalDay']  ?>" style="text-align: center;"><?= $startMonth['firstMonthName'] ?></td>
            <td colspan="<?= $endMonth['totalDay']  ?>" style="text-align: center;"><?= $endMonth['secondMonthName'] ?></td>
        <tr>
            <?php
            foreach ($allDates as $a) :
                if (date("N", strtotime($a)) == 7) :
                    echo "<td align=center style=\"vertical-align:middle;\" ><font color='red'>" . date('d', strtotime($a)) . "</font></td>";
                else :
                    echo "<td align=center style=\"vertical-align:middle;\">" . date('d', strtotime($a)) . "</td>";
                endif;
            endforeach;
            ?>
        </tr>
        </tr>
    </thead>
    <tbody>
        <?php $nomor = 1; ?>
        <?php $attandanceModel = new \App\Models\AttendancesModel(); ?>
        <?php foreach ($employeesData as $i => $e) : ?>
            <?php $libur = 0; ?>
            <tr align="center; font-weight:bold; color:white;">
                <td style="color: black; font-weight:normal;"><?= $nomor++; ?></td>
                <td style="color: black; font-weight:normal;">
                    &nbsp; <?= $e['name']; ?>
                </td>
                <td style="color: black; font-weight:normal;">
                    &nbsp;<?= $e["divisi"]; ?></td>
                </td>
                <td style="color: black; font-weight:normal;">
                    &nbsp;<?= $e["namaBagian"]; ?></td>
                </td>
                <?php $j = 1; ?>
                <?php foreach ($allDates as $a) : ?>
                    <?php $no = (strlen($j) == 1) ? ("0" . $j) : $j; ?>
                    <?php $attandance = $attandanceModel->getAttendances($a, $e['id']); ?>
                    <?php if ($attandance == null) : ?>
                        <!-- Null -->
                        <td align=center style='background-color:#e7323a; color:white;'>
                            A
                        </td>
                    <?php else : ?>
                        <?php $statusKode = explode("_", $attandance->status)[1]; ?>
                        <?php if ($statusKode == "A") : ?>
                            <!-- Employe Tidak Hadir -->
                            <td align=center style='background-color:#e7323a; color:white;'>
                                A
                            </td>
                        <?php elseif ($statusKode == "H") : ?>
                            <!-- Employe Hadir -->
                            <td align=center style="background-color:#304de2; color:white;vertical-align: middle;">
                                <b>H</b>
                            </td>
                        <?php elseif ($statusKode == "I") : ?>
                            <!-- Employe Ijin -->
                            <td align=center style='background-color:#17a2b8; color:white;'>
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($statusKode == "CT") : ?>
                            <!-- Employe Cuti Tahunan -->
                            <td align=center style='background-color:#ffc107; color:white;'>
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($statusKode == "CHD") : ?>
                            <!-- Employe Cuti Haid -->
                            <td align=center style='background-color:#242120; color:white;'>
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($statusKode == "CHL") : ?>
                            <!-- Employe Cuti Hamil -->
                            <td align=center style='background-color:#C34A36; color:white;'>
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($statusKode == "CM") : ?>
                            <!-- Employe Cuti Melahirkan -->
                            <td align=center style='background-color:#C34A36; color:white;'>
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($statusKode == "S") : ?>
                            <!-- Employe Sakit -->
                            <td align=center style='background-color:#28a745; color:white;'>
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($statusKode == "L") : ?>
                            <!-- LIBUR -->
                            <td style='background-color:#845EC2; color:white;'>
                                <b>L<b>
                            </td>
                        <?php elseif ($statusKode == "RL") : ?>
                            <!-- RL -->
                            <td align=center style="background-color:#ff7b00; vertical-align:middle; color:white;">
                                <b>RL</b>
                            </td>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br>
<b>
    Rekap Total Data Absensi Final
</b>
<table border="1">
    <thead>
        <tr align="center" style="font-weight: bold;">
            <td width="10" style="text-align: center;">No</td>
            <td style="text-align: center;">Karyawan</td>
            <td style="text-align: center;">Departemen</td>
            <td style="text-align: center;">Bagian</td>
            <?php foreach ($statusPerizinan as $s) : ?>
                <td width="20" align="center">
                    <b><?= explode("_", $s['value'])[1] ?></b>
                </td>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php $nomors = 1; ?>
        <?php foreach ($employeesData as $i => $e) : ?>
            <?php $status = $attandanceModel->getStatusAttendances($year, $month, $e['id']); ?>
            <tr align="center">
                <td><?= $nomors++; ?></td>
                <td>
                    &nbsp;<?= $e['name'] ?></td>
                <td>
                    &nbsp;<?= $e['divisi'] ?></td>
                </td>
                <td>
                    &nbsp;<?= $e["namaBagian"]; ?>
                </td>
                <?php foreach ($statusPerizinan as $s) : ?>
                    <td width="20" align="center">
                        <?= $status[$s['value']] ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br>
<b>
    Keterangan:
    <?php foreach ($statusPerizinan as $s) : ?>
        <?= explode("_", $s['value'])[0] ?> (<?= explode("_", $s['value'])[1]; ?>)
    <?php endforeach; ?>
</b>
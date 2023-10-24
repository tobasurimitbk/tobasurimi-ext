<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=LogAbsensi_" . $yearMonth . ".xls");
?>
<b>
    Unit: <?= $company['company'] ?><br>
    Departemen: <?= $divisi != null ? $divisi['divisi'] : "Semua Departemen" ?><br>
    Bulan: <?= date('F - Y', strtotime($yearMonth)) ?>
</b>
<br>
<b>
    Rekap Data Log Absensi
</b>
<br>
<table border="1">
    <thead>
        <tr align="center" style="font-weight: bold;">
            <td>No</td>
            <td>Karyawan</td>
            <td>Departemen</td>
            <td>Bagian</td>
            <?php
            $last_date = date("t", strtotime($yearMonth . "-01"));
            for ($i = 1; $i <= $last_date; $i++) :
                $temp = mktime(0, 0, 0, $month, $i, $year);
                $no = (strlen($i) == 1) ? ("0" . $i) : $i;
                echo "<td align=center style=\"vertical-align:middle;\">" . $i . "</td>";
            endfor;
            ?>
        </tr>
    </thead>
    <?php
    $kehadiran = array();
    $nomor = 1;
    ?>
    <tbody>
        <?php for ($i = 0; $i < count($res_user); $i++) : ?>
            <?php
            $hadir = 0;
            $alpha = 0;
            $libur = 0;
            ?>
            <tr align="center">
                <td><?= $nomor++; ?></td>
                <td style="vertical-align:middle;z-index:9999" nowrap>
                    <?= $res_user[$i]["employeeName"]; ?>
                </td>
                <td style="vertical-align:middle;z-index:9999" nowrap>
                    <?= $res_user[$i]["divisi"]; ?>
                </td>
                <td style="vertical-align:middle;z-index:9999" nowrap>
                    <?= $res_user[$i]["namaBagian"]; ?>
                </td>
                <?php
                for ($j = 1; $j <= $last_date; $j++) :
                    $no = (strlen($j) == 1) ? ("0" . $j) : $j;
                    $jam_masuk = "";
                    $jam_keluar = "";
                    $check = 0;

                    $dateFormat = ($year . "-" . $month . "-" . $no);
                    $formPerizinanModel = new \App\Models\FormPerijinanModel();
                    $hariBesarModel = new \App\Models\BigDaysModel();

                    $perizinanCheck = $formPerizinanModel
                        ->where('employee_id', $res_user[$i]['employeeID'])
                        ->where('periode', ($year . "-" . $month . "-" . $no))
                        ->first();

                    $hariBesarCheck = $hariBesarModel
                        ->where('date', $dateFormat)
                        ->first();

                    foreach ($res_user[$i]["list_attendance"] as $val) :
                        if ($val->periode == ($year . "-" . $month . "-" . $no)) :
                            $jam_masuk = $val->checkin;
                            $jam_keluar = $val->checkout;
                            if ($val->checkin != '')
                                $check = 1;
                            break;
                        endif;
                    endforeach;
                ?>
                    <?php if ($hariBesarCheck != null) : ?>
                        <?php $libur++; ?>
                        <td style='background-color:#845EC2;; color:white;'><b>L<b></td>
                    <?php elseif ($perizinanCheck != null) : ?>
                        <!-- Ada perizinan -->
                        <?php $statusKode = explode("_", $perizinanCheck['status'])[1]; ?>
                        <?php if ($perizinanCheck['status']  == "CUTI TAHUNAN_CT") : ?>
                            <!-- Ada perizinan Cuti Tahunan -->
                            <td style="background-color: #ffc107; color:white;">
                                <b><?= $statusKode  ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "CUTI HAID_CHD") : ?>
                            <!-- Ada perizinan Cuti Haid -->
                            <td style="background-color: #242120; color:white;">
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "CUTI HAMIL_CHL") : ?>
                            <!-- Ada perizinan Cuti Hamil -->
                            <td style="background-color: #C34A36; color:white;">
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "CUTI MELAHIRKAN_CM") : ?>
                            <!-- Ada perizinan Cuti Melahirkan -->
                            <td style="background-color: #4B4453; color:white;">
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "IJIN_I") : ?>
                            <!-- Ada ijin -->
                            <td style="background-color: #17a2b8; color:white;">
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "SAKIT_S") : ?>
                            <!-- Ada sakit -->
                            <td style="background-color: #28a745; color:white;">
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "RL_RL") : ?>
                            <!-- Ada RL -->
                            <td style="background-color: #ff7b00; color:white;">
                                <b><?= $statusKode ?></b>
                            </td>
                        <?php endif ?>
                    <?php else : ?>
                        <?php if ($check == 1) : ?>
                            <?php $hadir++; ?>
                            <td style="background-color: #304de2; color:white;">
                                <b>H</b>
                            </td>
                        <?php else : ?>
                            <?php
                            $temp = mktime(0, 0, 0, $month, $j, $year);
                            if (date("N", $temp) == 7) {
                                // Hari Minggu
                                $libur++;
                                echo "<td style='background-color:#845EC2;; color:white;'><b>L</b></td>";
                            } else {
                                // tidak absen = alpha
                                $alpha++;
                                echo "<td style='background-color:#e7323a; color:white;'><b>A</b></td>";
                            }
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endfor; ?>
            </tr>
            <?php
            $kehadiran[] = [
                'id' => $res_user[$i]['employeeID'],
                'hadir' => $hadir,
                'libur' => $libur,
                'alpha' => $alpha
            ];
            ?>
        <?php endfor; ?>
    </tbody>
</table>
<br>
<b>
    Rekap Data Kehadiran Total Log Absensi
</b>
<table border="1">
    <thead>
        <tr align="center" style="font-weight: bold;">
            <td width="10">No</td>
            <td>Karyawan</td>
            <td>Departemen</td>
            <td>Bagian</td>
            <?php foreach ($statusPerizinan as $s) : ?>
                <td>
                    <b><?= explode("_", $s['value'])[1] ?></b>
                </td>
            <?php endforeach; ?>
            <td>
                <b>L</b>
            </td>
            <td>
                <b>A</b>
            </td>
            <td>
                <b>H</b>
            </td>
        </tr>
    </thead>
    <tbody>
        <?php $nomors = 1; ?>
        <?php for ($i = 0; $i < count($res_user); $i++) : ?>
            <tr align="center">
                <td>
                    <?= $nomors++; ?>
                </td>
                <td>
                    <?= $res_user[$i]["employeeName"]; ?>
                </td>
                <td width="10">
                    <?= $res_user[$i]["divisi"]; ?>
                </td>
                <td width="10">
                    <?= $res_user[$i]["namaBagian"]; ?>
                </td>
                <?php foreach ($statusPerizinan as $s) : ?>
                    <td>
                        <?= $res_user[$i]['statusAttendances'][explode("_", $s['value'])[1]] ?>
                    </td>
                <?php endforeach; ?>
                <td>
                    <?php if ($kehadiran[$i]['id'] == $res_user[$i]['employeeID']) : ?>
                        <?= $kehadiran[$i]['libur']; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($kehadiran[$i]['id'] == $res_user[$i]['employeeID']) : ?>
                        <?= $kehadiran[$i]['alpha']; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($kehadiran[$i]['id'] == $res_user[$i]['employeeID']) : ?>
                        <?= $kehadiran[$i]['hadir']; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>
<br>
<b>
    Keterangan:
    <?php foreach ($statusPerizinanAll as $s) : ?>
        <?= ucfirst(strtolower(explode("_", $s['value'])[0])) ?> (<?= explode("_", $s['value'])[1]; ?>),
    <?php endforeach; ?>
    Libur (L)
</b>
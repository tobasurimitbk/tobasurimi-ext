<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Absensi <?= date('F - Y', strtotime($yearMonth)) ?></title>
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/img/favicon.png" type="image/png" />
    <style>
        /* Style tabel dasar Bootstrap */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
            padding: 5px;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 5px;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <h3 style="text-align: center;">REKAP DATA LOG ABSENSI</h3>

    <table class="mb-3" style="font-size: 12px;">
        <tbody>
            <tr>
                <td width="100px"><b>Company Name</b></td>
                <td width="10px">:</td>
                <td><?= $company['company'] ?></td>
            </tr>
            <tr>
                <td width="100px"><b>Divisi</b></td>
                <td width="10px">:</td>
                <td><?= $divisi != null ? $divisi['divisi'] : "Semua Divisi" ?></td>
            </tr>
            <tr>
                <td width="100px"><b>Bulan</b></td>
                <td width="10px">:</td>
                <td><?= date('F - Y', strtotime($yearMonth)) ?></td>
            </tr>
        </tbody>
    </table>

    <table class="table table-bordered" style="font-size:7px; margin-top:10px;" border="1">
        <thead>
            <tr>
                <td style="vertical-align:middle;z-index:9999" width="10">&nbsp;No</td>
                <td style="vertical-align:middle;z-index:9999">&nbsp;User</td>
                <?php
                $last_date = date("t", strtotime($yearMonth . "-01"));
                for ($i = 1; $i <= $last_date; $i++) {
                    $temp = mktime(0, 0, 0, $month, $i, $year);
                    $no = (strlen($i) == 1) ? ("0" . $i) : $i;

                    if (date("N", $temp) == 7) {
                        echo "<td align=center  style=\"vertical-align:middle;\"><font color='red'>I<br>" . $i . "</font></td>";
                        echo "<td align=center  style=\"vertical-align:middle;\"><font color='red'>O<br>" . $i . "</font></td>";
                    } else {
                        echo "<td align=center style=\"vertical-align:middle;\">I<br>" . $i . "</td>";
                        echo "<td align=center style=\"vertical-align:middle;\">O<br>" . $i . "</td>";
                    }
                ?>
                <?php
                }
                ?>
            </tr>
        </thead>
        <?php $kehadiran = array();
        $nomor = 1; ?>

        <?php for ($i = 0; $i < count($res_user); $i++) : ?>
            <?php
            $hadir = 0;
            $alpha = 0;
            $libur = 0;
            ?>
            <tr>
                <td><?= $nomor++; ?></td>
                <td style="vertical-align:middle;z-index:9999" nowrap>
                    &nbsp;<?php echo $res_user[$i]["employeeName"]; ?>
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
                        <td><b>L<b></td>
                        <td><b>L<b></td>
                    <?php elseif ($perizinanCheck != null) : ?>
                        <!-- Ada perizinan -->
                        <?php $statusKode = explode("_", $perizinanCheck['status'])[1]; ?>
                        <?php if ($perizinanCheck['status']  == "CUTI TAHUNAN_CT") : ?>
                            <!-- Ada perizinan Cuti Tahunan -->
                            <td>
                                <b><?= $statusKode  ?></b>
                            </td>
                            <td>
                                <b><?= $statusKode  ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "CUTI HAID_CHD") : ?>
                            <!-- Ada perizinan Cuti Haid -->
                            <td>
                                <b><?= $statusKode ?></b>
                            </td>
                            <td>
                                <b><?= $statusKode  ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "CUTI HAMIL_CHL") : ?>
                            <!-- Ada perizinan Cuti Hamil -->
                            <td>
                                <b><?= $statusKode ?></b>
                            </td>
                            <td>
                                <b><?= $statusKode  ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "CUTI MELAHIRKAN_CM") : ?>
                            <!-- Ada perizinan Cuti Melahirkan -->
                            <td>
                                <b><?= $statusKode ?></b>
                            </td>
                            <td>
                                <b><?= $statusKode  ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "IJIN_I") : ?>
                            <!-- Ada ijin -->
                            <td>
                                <b><?= $statusKode ?></b>
                            </td>
                            <td>
                                <b><?= $statusKode  ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "SAKIT_S") : ?>
                            <!-- Ada sakit -->
                            <td>
                                <b><?= $statusKode ?></b>
                            </td>
                            <td>
                                <b><?= $statusKode  ?></b>
                            </td>
                        <?php elseif ($perizinanCheck['status'] == "RL_RL") : ?>
                            <!-- Ada RL -->
                            <td>
                                <b><?= $statusKode ?></b>
                            </td>
                            <td>
                                <b><?= $statusKode  ?></b>
                            </td>
                        <?php endif ?>
                    <?php else : ?>
                        <?php if ($check == 1) : ?>
                            <?php $hadir++; ?>
                            <td>
                                <b><?= $jam_masuk != "" ? "H" : "A"; ?></b>
                            </td>
                            <td>
                                <b><?= $jam_keluar != "" ? "H" : "A"; ?></b>
                            </td>
                        <?php else : ?>
                            <?php
                            $temp = mktime(0, 0, 0, $month, $j, $year);
                            if (date("N", $temp) == 7) {
                                // Hari Minggu
                                $libur++;
                                echo "<td><b>L</b></td>";
                                echo "<td><b>L</b></td>";
                            } else {
                                // tidak absen = alpha
                                $alpha++;
                                echo "<td><b>A</b></td>";
                                echo "<td><b>A</b></td>";
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
    </table>

    <small style="font-size: 10px;">
        Keterangan:
        <?php foreach ($statusPerizinanAll as $s) : ?>
            <?= explode("_", $s['value'])[0] ?> (<?= explode("_", $s['value'])[1]; ?>),
        <?php endforeach; ?>
        LIBUR (L)
    </small>


    <div class="page-break">
        <h3 style="text-align: center;">REKAP DATA KEHADIRAN TOTAL LOG ABSENSI</h3>

        <table class="table table-bordered" style="font-size:12px; margin-top:10px;" border="1">
            <thead>
                <tr>
                    <td width="10">&nbsp;No</td>
                    <td>&nbsp;Karyawan</td>
                    <?php foreach ($statusPerizinan as $s) : ?>
                        <td align="center">
                            <b><?= explode("_", $s['value'])[1] ?></b>
                        </td>
                    <?php endforeach; ?>
                    <td align="center">
                        <b>L</b>
                    </td>
                    <td align="center">
                        <b>A</b>
                    </td>
                    <td align="center">
                        <b>H</b>
                    </td>
                </tr>
            </thead>
            <tbody>
                <?php $nomors = 1; ?>
                <?php for ($i = 0; $i < count($res_user); $i++) : ?>
                    <tr>
                        <td>
                            &nbsp;<?= $nomors++; ?>
                        </td>
                        <td>
                            &nbsp;<?php echo $res_user[$i]["employeeName"]; ?>
                        </td>
                        <?php foreach ($statusPerizinan as $s) : ?>
                            <td align="center">
                                <?= $res_user[$i]['statusAttendances'][explode("_", $s['value'])[1]] ?>
                            </td>
                        <?php endforeach; ?>
                        <td align="center">
                            <?php if ($kehadiran[$i]['id'] == $res_user[$i]['employeeID']) : ?>
                                <b><?= $kehadiran[$i]['libur']; ?></b>
                            <?php endif; ?>
                        </td>
                        <td align="center">
                            <?php if ($kehadiran[$i]['id'] == $res_user[$i]['employeeID']) : ?>
                                <b><?= $kehadiran[$i]['alpha']; ?></b>
                            <?php endif; ?>
                        </td>
                        <td align="center">
                            <?php if ($kehadiran[$i]['id'] == $res_user[$i]['employeeID']) : ?>
                                <b><?= $kehadiran[$i]['hadir']; ?></b>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <small style="font-size: 10px;">
            Keterangan:
            <?php foreach ($statusPerizinanAll as $s) : ?>
                <?= explode("_", $s['value'])[0] ?> (<?= explode("_", $s['value'])[1]; ?>),
            <?php endforeach; ?>
            LIBUR (L)
        </small>
    </div>
</body>

</html>
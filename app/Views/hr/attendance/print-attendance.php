<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi Final <?= date('F - Y', strtotime($yearMonth)) ?></title>
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
    <h5 style="text-align: center;">PT TOBA SURIMI</h5>
    <h3 style="text-align: center;">REKAP DATA ABSENSI FINAL</h3>
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

    <table class="table table-bordered" style="font-size:12px; margin-top:10px;" border="1">
        <thead>
            <tr align="center" style="font-weight: bold;">
                <td width="10">&nbsp;No</td>
                <td height="25">&nbsp;Karyawan</td>
                <td height="25">&nbsp;Divisi</td>
                <?php
                $lastDate = date("t", strtotime($year . "-" . $month . "-01"));
                for ($i = 1; $i <= $lastDate; $i++) :
                    $temp = mktime(0, 0, 0, $month, $i, $year);
                    echo "<td align=center style=\"vertical-align:middle;\">" . $i . "</td>";
                endfor;
                ?>
            </tr>
        </thead>
        <tbody>
            <?php $nomor = 1; ?>
            <?php foreach ($employeesData as $i => $e) : ?>
                <?php $attandanceModel = new \App\Models\AttendancesModel(); ?>
                <?php $libur = 0; ?>
                <tr align="center; font-weight:bold; color:white;">
                    <td style="color: black; font-weight:normal;"><?= $nomor++; ?></td>
                    <td style="color: black; font-weight:normal;">
                        &nbsp; <?= $e['name']; ?>
                    </td>
                    <td style="color: black; font-weight:normal;">
                        &nbsp;<?= $e["divisi"]; ?></td>
                    </td>
                    <?php for ($j = 1; $j <= $lastDate; $j++) : ?>
                        <?php
                        // create format date yyyy-mm-dd
                        $no = (strlen($j) == 1) ? ("0" . $j) : $j;
                        $dateFormat = ($year . "-" . $month . "-" . $no);
                        // get attendance by employee and date
                        $attandance = $attandanceModel->getAttendances($dateFormat, $e['id']);
                        ?>
                        <?php $temp = mktime(0, 0, 0, $month, $j, $year); ?>
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
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <small style="font-size: 10px;">
        Keterangan:
        <?php foreach ($statusPerizinan as $s) : ?>
            <?= explode("_", $s['value'])[0] ?> (<?= explode("_", $s['value'])[1]; ?>)
        <?php endforeach; ?>
    </small>

    <div class="page-break">
        <h5 style="text-align: center;">PT TOBA SURIMI</h5>
        <h3 style="text-align: center;">REKAP DATA KEHADIRAN ABSENSI FINAL</h3>
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
        <table class="table table-bordered" style="font-size:12px; margin-top:10px;" border="1">
            <thead>
                <tr align="center" style="font-weight: bold;">
                    <td width="10">&nbsp;No</td>
                    <td height="25">&nbsp;Karyawan</td>
                    <td height="25">&nbsp;Divisi</td>
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
                        <?php foreach ($statusPerizinan as $s) : ?>
                            <td width="20" align="center">
                                <?= $status[$s['value']] ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <small style="font-size: 10px;">
            Keterangan:
            <?php foreach ($statusPerizinan as $s) : ?>
                <?= explode("_", $s['value'])[0] ?> (<?= explode("_", $s['value'])[1]; ?>)
            <?php endforeach; ?>
        </small>

    </div>
</body>

</html>
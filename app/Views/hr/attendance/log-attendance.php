<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    th {
        background-color: white;
    }

    th:first-child,
    td:first-child {
        position: sticky;
        left: -12px;
        z-index: 1;
        /* Menetapkan z-index agar tidak terlindung oleh sel lain */
    }

    td:first-child {
        border: 1px solid #f2f2f2;
        box-sizing: border-box;
    }

    td:first-child::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: #fff;
        left: 0;
        top: 0;
        z-index: -1;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Log Absensi</h1>
        <div class="col-button-tambah-spp">
            <button class="btn btn-warning btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-print"></i> Print
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><a target="_blank" class="dropdown-item" href="<?= base_url('log-attendance/print/id/' . $year . '-' . $month . "?divisiID=" . @$_GET['divisiID'] . "&golongan=" .  @$_GET['golongan']) ?>">PDF</a></li>
                <li><a class="dropdown-item" href="<?= base_url('log-attendance/excel/id/' . $year . '-' . $month . "?divisiID=" . @$_GET['divisiID'] . "&golongan=" .  @$_GET['golongan']) ?>">Excel</a></li>
            </ul>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-page-list-attendance">
                <div class="col-6 mb-2">
                    <form id="search_form" name="search_form" class="kt-form kt-form--fit kt-margin-b-20" method="POST">
                        <select name="month" id="month">
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                $temp = (strlen($i) == 1) ? ("0" . $i) : $i;
                                $checked = ($month == $temp) ? "selected" : "";
                            ?>
                                <option value="<?php echo $temp; ?>" <?php echo $checked; ?>><?php echo $temp; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <select name="year" id="year">
                            <?php
                            for ($i = date("Y") - 2; $i <= date("Y") + 2; $i++) {
                                $checked = ($year == $i) ? "selected" : "";
                            ?>
                                <option value="<?php echo $i; ?>" <?php echo $checked; ?>><?php echo $i; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <button type="button" class="btn btn-primary btn-brand--icon" id="kt_search" onclick="printReport();">
                            <span>
                                <i class="la la-print"></i>
                                <span>Cari</span>
                            </span>
                        </button>

                    </form>
                </div>
                <div class="col-6 mb-2">
                    <!--begin: Datatable -->
                    <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                </div>
            </div>
            <hr>
            <div class="row row-col-page-list-attendance mt-4">
                <form action="#" method="get">
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <div class="form-floating">
                                <select class="form-select" name="divisiID" aria-label="Floating label select example">
                                    <option value="">
                                        Cari Departemen
                                    </option>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option <?= @$_GET['divisiID'] == $d['id'] ? 'selected' : '' ?> value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Cari Departemen</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-floating">
                                <select class="form-select" name="filterGolongan" aria-label="Floating label select example">
                                    <option value="">
                                        Cari Tipe / Golongan
                                    </option>
                                    <?php foreach ($golongan as $g) : ?>
                                        <option <?= @$_GET['golongan'] == $g['golongan_name'] ? "selected" : "" ?> value="<?= $g['golongan_name'] ?>">
                                            <?= $g['golongan_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Cari Tipe / Golongan</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-floating">
                                <select class="form-select" name="select2EmployeesName" aria-label="Floating label select example">
                                    <?php if ($employeeDetailFilter != null) : ?>
                                        <option value="<?= $employeeDetailFilter['id'] ?>">
                                            <?= $employeeDetailFilter['name']; ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                                <label for="floatingInput">Cari Karyawan</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <a href="<?= base_url("log-attendance?month=$month&year=$year") ?>" type="button" class="btn btn-primary btn_reset">
                                <i class="fa-solid fa-rotate-right"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <br><br><br><br><br>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped- table-bordered table-hover table-checkable" id="attendanceTable">
                        <thead>
                            <tr>
                                <td height="25" style="vertical-align:middle;z-index:1">&nbsp;Karyawan</td>
                                <td height="25" style="vertical-align:middle;z-index:1">&nbsp;Departemen</td>
                                <td height="25" style="vertical-align:middle;z-index:1">&nbsp;Bagian</td>
                                <?php
                                $last_date = date("t", strtotime($year . "-" . $month . "-01"));
                                for ($i = 1; $i <= $last_date; $i++) :
                                    $temp = mktime(0, 0, 0, $month, $i, $year);
                                    $no = (strlen($i) == 1) ? ("0" . $i) : $i;

                                    if (date("N", $temp) == 7) :
                                        echo "<td align=center  style=\"vertical-align:middle; min-width: 44px;\" height=\"25\"><font color='red'>IN<br> " . $i . "</font></td>";
                                        echo "<td align=center  style=\"vertical-align:middle; min-width: 44px;\" height=\"25\"><font color='red'>OUT<br> " . $i . "</font></td>";
                                    else :
                                        echo "<td align=center style=\"vertical-align:middle; min-width: 44px;\" height=\"25\">IN<br> " . $i . "</td>";
                                        echo "<td align=center style=\"vertical-align:middle; min-width: 44px;\" height=\"25\">OUT<br> " . $i . "</td>";
                                    endif;
                                endfor;
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $kehadiran = array(); ?>
                            <?php if (count($res_user) == 0) : ?>
                                <tr>
                                    <td colspan="66">Tidak ada data pegawai</td>
                                </tr>
                            <?php else : ?>
                                <?php for ($i = 0; $i < count($res_user); $i++) : ?>
                                    <?php
                                    $hadir = 0;
                                    $alpha = 0;
                                    $libur = 0;
                                    ?>
                                    <tr>
                                        <td style="vertical-align:middle;z-index:1" nowrap>
                                            &nbsp;<?php echo strtoupper($res_user[$i]["employeeName"]); ?></td>
                                        <td style="vertical-align:middle;z-index:1" nowrap>
                                            &nbsp;<?php echo strtoupper($res_user[$i]["divisi"]); ?></td>
                                        <td style="vertical-align:middle;z-index:1" nowrap>
                                            &nbsp;<?php echo strtoupper($res_user[$i]["namaBagian"]); ?></td>
                                        </td>
                                        <?php
                                        for ($j = 1; $j <= $last_date; $j++) :
                                            $no = (strlen($j) == 1) ? ("0" . $j) : $j;
                                            $jam_masuk = ""; // checkOut
                                            $jam_keluar = ""; // checkIN
                                            $check = 0; // cek apakah ada di log absen tidak

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
                                                <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style="vertical-align:middle; cursor:pointer;"><img src='assets/img/stop.png' width='25' height='25'></td>
                                                <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style="vertical-align:middle;cursor:pointer;"><img src='assets/img/stop.png' width='25' height='25'></td>
                                            <?php elseif ($perizinanCheck != null) : ?>
                                                <!-- Ada perizinan -->
                                                <?php $statusKode = explode("_", $perizinanCheck['status'])[1]; ?>
                                                <?php if ($perizinanCheck['status']  == "CUTI TAHUNAN_CT") : ?>
                                                    <!-- Ada perizinan Cuti Tahunan -->
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#ffc107; color:white; cursor:pointer;'>
                                                        <b><?= $statusKode ?></b>
                                                    </td>
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#ffc107; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode  ?></b>
                                                    </td>
                                                <?php elseif ($perizinanCheck['status'] == "CUTI HAID_CHD") : ?>
                                                    <!-- Ada perizinan Cuti Haid -->
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#242120; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode ?></b>
                                                    </td>
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#242120; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode  ?></b>
                                                    </td>
                                                <?php elseif ($perizinanCheck['status'] == "CUTI HAMIL_CHL") : ?>
                                                    <!-- Ada perizinan Cuti Hamil -->
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#C34A36; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode ?></b>
                                                    </td>
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#C34A36; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode  ?></b>
                                                    </td>
                                                <?php elseif ($perizinanCheck['status'] == "CUTI MELAHIRKAN_CM") : ?>
                                                    <!-- Ada perizinan Cuti Melahirkan -->
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#4B4453; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode ?></b>
                                                    </td>
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#4B4453; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode  ?></b>
                                                    </td>
                                                <?php elseif ($perizinanCheck['status'] == "IJIN_I") : ?>
                                                    <!-- Ada ijin -->
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#17a2b8; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode ?></b>
                                                    </td>
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#17a2b8; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode  ?></b>
                                                    </td>
                                                <?php elseif ($perizinanCheck['status'] == "SAKIT_S") : ?>
                                                    <!-- Ada sakit -->
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#28a745; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode ?></b>
                                                    </td>
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#28a745; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode  ?></b>
                                                    </td>
                                                <?php elseif ($perizinanCheck['status'] == "RL_RL") : ?>
                                                    <!-- Ada RL -->
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#ff7b00; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode ?></b>
                                                    </td>
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#ff7b00; color:white;cursor:pointer;'>
                                                        <b><?= $statusKode  ?></b>
                                                    </td>
                                                <?php endif ?>
                                            <?php else : ?>
                                                <?php if ($check == 1) : ?>
                                                    <?php $hadir++; ?>
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style="background-color:#304de2;" style='vertical-align: middle;cursor:pointer;'>
                                                        <font color="white"><b><?= $jam_masuk; ?></b></font>
                                                    </td>
                                                    <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style="background-color:#304de2" style='vertical-align: middle;cursor:pointer;'>
                                                        <font color="white"><b><?= $jam_keluar; ?></b></font>
                                                    </td>
                                                <?php else : ?>
                                                    <?php
                                                    $temp = mktime(0, 0, 0, $month, $j, $year);
                                                    if (date("N", $temp) == 7) {
                                                        // Hari Minggu
                                                        $libur++;
                                                        echo "<td class='detail' data-tanggal='" . $dateFormat . "' data-employee_id='" . $res_user[$i]["employeeID"] . "' width='25' align='center' style=\"vertical-align:middle;cursor:pointer;\"><img src='assets/img/stop.png' width='25' height='25'></td>";
                                                        echo "<td class='detail'  data-tanggal='" . $dateFormat . "' data-employee_id='" . $res_user[$i]["employeeID"] . "' width=25 align=center style=\"vertical-align:middle;cursor:pointer;\"><img src='assets/img/stop.png' width='25' height='25'></td>";
                                                    } else {
                                                        // tidak absen = alpha
                                                        $alpha++;
                                                        echo "<td class='detail'  data-tanggal='" . $dateFormat . "' data-employee_id='" . $res_user[$i]["employeeID"] . "' width=25 align=center style='background-color:#e7323a;;cursor:pointer;'></td>";
                                                        echo "<td class='detail'  data-tanggal='" . $dateFormat . "' data-employee_id='" . $res_user[$i]["employeeID"] . "' width=25 align=center style='background-color:#e7323a;cursor:pointer;'></td>";
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
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-5">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <td height="25">&nbsp;Karyawan</td>
                                <td height="25">&nbsp;Departemen</td>
                                <td height="25">&nbsp;Bagian</td>
                                <?php foreach ($statusPerizinan as $s) : ?>
                                    <td width="20" align="center">
                                        <b><?= explode("_", $s['value'])[1] ?></b>
                                    </td>
                                <?php endforeach; ?>
                                <td width="20" align="center">
                                    <b>L</b>
                                </td>
                                <td width="20" align="center">
                                    <b>A</b>
                                </td>
                                <td width="20" align="center">
                                    <b>H</b>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($res_user) == 0) : ?>
                                <tr>
                                    <td colspan="13">Tidak ada data pegawai</td>
                                </tr>
                            <?php else : ?>
                                <?php for ($i = 0; $i < count($res_user); $i++) : ?>
                                    <tr>
                                        <td width="150">
                                            &nbsp;<?php echo $res_user[$i]["employeeName"]; ?></td>
                                        <td width="110">
                                            &nbsp;<?php echo $res_user[$i]["divisi"]; ?></td>
                                        <td width="110">
                                            &nbsp;<?php echo $res_user[$i]["namaBagian"]; ?></td>
                                        </td>
                                        <?php foreach ($statusPerizinan as $s) : ?>
                                            <td width="90" align="center">
                                                <?= $res_user[$i]['statusAttendances'][explode("_", $s['value'])[1]] ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td width="90" align="center">
                                            <?php if ($kehadiran[$i]['id'] == $res_user[$i]['employeeID']) : ?>
                                                <b><?= $kehadiran[$i]['libur']; ?></b>
                                            <?php endif; ?>
                                        </td>
                                        <td width="90" align="center">
                                            <?php if ($kehadiran[$i]['id'] == $res_user[$i]['employeeID']) : ?>
                                                <b><?= $kehadiran[$i]['alpha']; ?></b>
                                            <?php endif; ?>
                                        </td>
                                        <td width="90" align="center">
                                            <?php if ($kehadiran[$i]['id'] == $res_user[$i]['employeeID']) : ?>
                                                <b><?= $kehadiran[$i]['hadir']; ?></b>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($pager->hasMore() || ($pager->getCurrentPage() < $pager->getPageCount()) || (count($employeesData) > $pager->getPerPage())) : ?>
                    <?= $pager->links('default', 'bootstrap4_pagination') ?>
                <?php endif ?>

                <div class="card-text mt-4">
                    <b class="text-black">Keterangan</b>
                </div>

                <div class="row mt-3">
                    <?php foreach ($statusPerizinanAll as $s) : ?>
                        <div class="col-sm-2 col-4 mt-2">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="p-3" style="width: 5px; height:5px; background-color:<?= $s['description'] ?>"></div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="card-text mt-1 text-black">
                                        <?= explode("_", $s['value'])[0] ?> (<?= explode("_", $s['value'])[1]; ?>)
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="col-sm-2 col-4 mt-2">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="text-center">
                                    <img src='assets/img/stop.png' width='27' height='27'>
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="card-text mt-1 text-black">
                                    LIBUR (L)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Detail Log Attendance</h5>
            </div>
            <form id="updateAttendanceForm" role="form" method="POST">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" class="form-control" id="employeeName" disabled>
                        <label for="employeeName">Employe Name</label>
                    </div>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" name="tanggal" class="form-control" id="tanggal" disabled>
                        <label for="tanggal">Tanggal</label>
                    </div>
                    <div class="input-group">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input type="text" name="jamKerjaName" class="form-control" id="jamKerjaName" disabled>
                            <label for="tanggal">Jam Kerja</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button class="btn btn-success jamKerjaDetail" id="jamKerjaDetail" type="button">
                                <i class="fas fa-calendar-week"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" name="statusKehadiran" class="form-control" id="statusKehadiran" disabled>
                        <label for="status">Status Kehadiran</label>
                    </div>

                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" name="keterangan" class="form-control" id="keterangan" disabled>
                        <label for="status">Keterangan Tambahan</label>
                    </div>

                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" name="jamTerlambat" class="form-control" id="jamTerlambat" disabled>
                        <label for="jamTerlambat">Jam Terlambat</label>
                    </div>

                    <div class="row mb-2" id="formInOut">
                        <div class="col-md-6">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <input type="text" class="form-control" id="checkIn" name="checkIn" disabled maxlength="30">
                                <label for="checkin">CheckIN</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <input type="text" class="form-control" id="checkOut" name="checkOut" disabled maxlength="30">
                                <label for="checkout">CheckOut</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard btn-discard-1 mr-3">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="detail2Modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Jam Kerja</h5>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input type="text" id="jamKerjaNameDetail" class="form-control jamKerjaNameDetail" disabled>
                            <label for="checkin">Jenis Jam Kerja</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input type="text" id="jamTerlambatDetail" class="form-control jamTerlambatDetail" disabled>
                            <label for="checkout">Jam Terlambat</label>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered nowrap table-striped table-hover-tobasurimi dataTable table-form-tts" id="dataTable2" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: center; width:10px;">No</th>
                            <th style="text-align: center;">Hari</th>
                            <th style="text-align: center;">Masuk</th>
                            <th style="text-align: center;">Mulai Istirahat</th>
                            <th style="text-align: center;">Selesai Istirahat</th>
                            <th style="text-align: center;">Pulang</th>
                        </tr>
                    </thead>
                    <tbody class="body-table">
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-2 mr-3">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
    function printReport() {
        document.location.href = 'log-attendance?month=' + document.getElementById('month').value + '&year=' + document.getElementById('year').value;
    }
    // Search employee
    $("select[name='select2EmployeesName']").select2({
        placeholder: "Cari Karyawan",
        theme: "bootstrap-5",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "<?= base_url('attendance/like-employees') ?>",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    employeesName: params.term,
                    divisiID: "<?= @$_GET['divisiID'] ?>",
                };
            },
            processResults: function(data) {
                var options = [];
                $.each(data.data, function(index, employee) {
                    options.push({
                        id: employee.id,
                        text: employee.name
                    });
                });
                return {
                    results: options
                };
            },
            cache: true
        }
    });

    $("select[name='divisiID']").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $("select[name='filterGolongan']").select2({
        placeholder: "Cari Tipe/Golongan Pegawai",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $("select[name='select2EmployeesName'], select[name='divisiID'], select[name='filterGolongan']").on("change", function() {
        var divisiID = $("select[name='divisiID']").val();
        var golongan = $("select[name='filterGolongan']").val();
        var employeesID = $("select[name='select2EmployeesName']").val();
        var link = "<?= base_url("log-attendance?month=$month&year=$year") ?>&divisiID=" + divisiID + "&employeesID=" + employeesID + "&golongan=" + golongan;
        window.location.href = link;
    });


    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.form-select')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $('.detail').click(function(e) {
        e.preventDefault();
        const csrfToken = '<?= csrf_token() ?>';

        var employeeID = $(this).data('employee_id');
        var tanggal = $(this).data('tanggal');

        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('employeeID', employeeID);
        formData.append('tanggal', tanggal);

        $.ajax({
            url: "<?= base_url("log-attendance/detail"); ?>",
            data: formData,
            beforeSend: function(xhr) {
                setLoading();
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            complete: function() {
                stopLoading();
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                var data = response.data;
                csrf.val(response.token);

                $('#employeeName').val(data.employee?.name);
                $('#tanggal').val(data.tanggal);
                $('#statusKehadiran').val(data.status);
                $('#keterangan').val(data.keterangan);
                $('#checkIn').val(data.checkIn);
                $('#checkOut').val(data.checkOut);
                $('#jamTerlambat').val(data.jamTerlambat);
                $('#jamKerjaName').val(data.jamKerja.jenis);

                // ASSIGN ATTR
                $('#jamKerjaDetail').data('jam_kerja_id', data.jamKerja.id);
                $('#jamKerjaDetail').data('jenis', data.jamKerja.jenis);
                $('#jamKerjaDetail').data('jam_terlambat', data.jamKerja.jam_terlambat);

                $('#detailModal').modal('show');
            },
            onError: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan pada sistem',
                    confirmButtonColor: '#4e73df',
                })
            }
        });
    });
    $('.jamKerjaDetail').click(function() {
        var element = $(this);
        var jamKerjaId = element.data('jam_kerja_id');
        var jenis = element.data('jenis');
        var jamTerlambat = element.data('jam_terlambat');
        // ASSIGN
        $('#jamKerjaNameDetail').val(jenis);
        $('#jamTerlambatDetail').val(jamTerlambat);
        // GET DETAIL JAM KERJA
        getListDetailJamKerja(jamKerjaId);

        $('#detail2Modal').modal('show');

    })

    $('.btn-discard-1').click(function() {
        $('#detailModal').modal('hide');
    });

    $('.btn-discard-2').click(function() {
        $('#detail2Modal').modal('hide');
    });

    function getListDetailJamKerja(jamKerjaId) {
        $.ajax({
            url: `<?= base_url('employee/get-jam-kerja-detail'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                jam_kerja_id: jamKerjaId,
            },
            dataType: "json",
            success: function(res) {
                var listData = res.data;
                var no = 1;

                const table = $('#dataTable2');
                table.find('tbody').empty();
                table.find('tfoot').empty();

                if (listData.length === 0) {
                    var newRow = $('<tr>');
                    newRow.append($('<td colspan="6" style="text-align:center">Tidak Ada Jam Kerja</td>'));
                    table.find('tfoot').append(newRow);
                } else {
                    $.each(listData, function(i, v) {
                        var newRow = $('<tr style="color:whitesmoke;">');
                        newRow.append($('<td style="text-align: center;">').html(
                            `
                            ${no++} 
                        `
                        ));
                        newRow.append($('<td style="text-align: center;">').text(v.hari));
                        newRow.append($('<td style="text-align: center;">').text(v.jam_masuk));
                        newRow.append($('<td style="text-align: center;">').text(v.jam_istirahat_mulai));
                        newRow.append($('<td style="text-align: center;">').text(v.jam_istirahat_selesai));
                        newRow.append($('<td style="text-align: center;">').text(v.jam_pulang));
                        table.find('tbody').append(newRow);
                    });
                }
            }
        });

    }
</script>


<?= $this->endSection(); ?>
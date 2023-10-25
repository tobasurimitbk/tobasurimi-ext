<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<!-- External CSS -->
<style>
    th {
        background-color: white;
    }

    th:first-child,
    td:first-child {
        position: sticky;
        left: -12px;

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
        <h1>Data Absensi</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right mr-2" data-bs-toggle="modal" data-bs-target="#generateModal" href="#">
                Generate
            </a>
            <?php if ($totalAttendances != 0) : ?>
                <input type="hidden" name="year" id="year" value="<?= $year ?>">
                <input type="hidden" name="month" id="month" value="<?= $month ?>">

                <button class="btn btn-warning btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-print"></i> Print
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <li><a target="_blank" class="dropdown-item" href="<?= base_url('list-attendance/print/id/' . $year . '-' . $month . "?divisiID=" . @$_GET['divisiID']) ?>">Bulanan PDF</a></li>
                    <li><a class="dropdown-item" href="#" id="triwulanBtnPDF">Triwulan PDF</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('list-attendance/excel/id/' . $year . '-' . $month . "?divisiID=" . @$_GET['divisiID']) ?>">Bulanan Excel</a></li>
                </ul>
            <?php endif ?>
            </form>
        </div>
    </div>
    <?= csrf_field() ?>
    <?php if (session()->has('error')) : ?>
        <div class="alert alert-danger">
            <?= session('error') ?>
        </div>
    <?php endif; ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-page-list-attendance">
                <div class="col-6 mb-2">
                    <form action="<?= base_url('list-attendance') ?>" class="kt-form kt-form--fit kt-margin-b-20" method="GET">
                        <select name="month" required id="month">
                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                <?php
                                $temp = (strlen($i) == 1) ? ("0" . $i) : $i;
                                $checked = ($month == $temp) ? "selected" : "";
                                ?>
                                <option value="<?= $temp; ?>" <?= $checked; ?>>
                                    <?= $temp; ?>
                                </option>
                            <?php endfor ?>
                        </select>
                        <select name="year" required id="year">
                            <?php
                            for ($i = date("Y") - 2; $i <= date("Y") + 2; $i++) :
                                $checked = ($year == $i) ? "selected" : "";
                            ?>
                                <option value="<?= $i; ?>" <?= $checked; ?>><?= $i; ?></option>
                            <?php endfor ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-brand--icon" id="kt_search" onclick="printReport();">
                            <span>
                                <i class="la la-print"></i>
                                <span>Cari</span>
                            </span>
                        </button>
                    </form>
                </div>
                <div class="col-6 mb-0">
                    <div class="clearfix" id="loadingSpinner">
                        <div class="spinner-border text-primary float-right" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hasil Preview Form Generate -->
            <hr>
            <div class="row row-col-page-list-attendance mt-4">
                <form action="#" method="get">
                    <div class="row mb-4">
                        <div class="col-sm-4">
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
                        <div class="col-sm-4">
                            <div class="form-floating">
                                <select class="form-select" name="select2EmployeesName" aria-label="Floating label select example">
                                    <?php if ($employeeDetailFilter != null) : ?>
                                        <option value="<?= $employeeDetailFilter['id'] ?>">
                                            <?= $employeeDetailFilter['name']; ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                                <label for="floatingInput">Cari Data Karyawan</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <a href="<?= base_url("list-attendance?month=$month&year=$year") ?>" type="button" class="btn btn-primary btn_reset">
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
                                <td height="25" rowspan="2" style="vertical-align:middle;z-index:1; text-align:center;">&nbsp;Karyawan</td>
                                <td height="25" rowspan="2" style="vertical-align:middle;z-index:1; text-align:center;">&nbsp;Departemen</td>
                                <td height="25" rowspan="2" style="vertical-align:middle;z-index:1; text-align:center;">&nbsp;Bagian</td>
                                <td colspan="<?= $startMonth['totalDay'] * 2 ?>" style="text-align: center;"><?= $startMonth['firstMonthName'] ?></td>
                                <td colspan="<?= $endMonth['totalDay'] * 2 ?>" style="text-align: center;"><?= $endMonth['secondMonthName'] ?></td>
                            <tr>
                                <?php
                                foreach ($allDates as $a) :
                                    if (date("N", strtotime($a)) == 7) :
                                        echo "<td align=center style=\"vertical-align:middle; min-width: 44px;\"height=\"25\" ><font color='red'>IN <br>" . date('d', strtotime($a)) . "</font></td>";
                                        echo "<td align=center style=\"vertical-align:middle; min-width: 44px;\" height=\"25\"><font color='red'>OUT <br>" . date('d', strtotime($a)) . "</font></td>";
                                    else :
                                        echo "<td align=center style=\"vertical-align:middle; min-width: 44px;\" height=\"25\">IN <br>" . date('d', strtotime($a)) . "</td>";
                                        echo "<td align=center style=\"vertical-align:middle; min-width: 44px;\"height=\"25\">OUT<br> " . date('d', strtotime($a)) . "</td>";
                                    endif;
                                endforeach;
                                ?>
                            </tr>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $attandanceModel = new \App\Models\AttendancesModel(); ?>
                            <!-- Cek apakah sudah digenerate apa belum -->
                            <?php if ($totalAttendances == 0) : ?>
                                <td colspan="<?= ($startMonth['totalDay'] * 2) + ($endMonth['totalDay'] * 2) + 2 ?>" align="left">
                                    Presensi Belum digenerate dari log absensi
                                </td>
                            <?php else : ?>
                                <?php foreach ($employeesData as $i => $e) : ?>
                                    <?php $libur = 0; ?>
                                    <?php $isGenerate = $attandanceModel->detectIfGenerate($year . "-" . $month, $e['id']); ?>
                                    <?php if ($isGenerate) : ?>
                                        <tr>
                                            <td style="vertical-align:middle;z-index:1; text-align:center;" nowrap>
                                                &nbsp; <?= $e['name']; ?>
                                            </td>
                                            <td style="vertical-align:middle;z-index:1; text-align:center;" nowrap>
                                                &nbsp;<?= $e["divisi"]; ?></td>
                                            </td>
                                            <td style="vertical-align:middle;z-index:1; text-align:center;" nowrap>
                                                &nbsp;<?= $e["nama_bagian"]; ?></td>
                                            </td>
                                            <?php $j = 1; ?>
                                            <?php foreach ($allDates as $a) : ?>
                                                <?php $no = (strlen($j) == 1) ? ("0" . $j) : $j; ?>
                                                <?php $attandance = $attandanceModel->getAttendances($a, $e['id']); ?>
                                                <?php if ($attandance == null) : ?>
                                                    <!-- Null -->
                                                    <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="vertical-align:middle; background-color:#a41fa6;"></td>
                                                    <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="vertical-align:middle; background-color:#a41fa6;"></td>
                                                <?php else : ?>
                                                    <?php $statusKode = explode("_", $attandance->status)[1]; ?>
                                                    <?php if ($statusKode == "A") : ?>
                                                        <!-- Employe Tidak Hadir -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#e7323a; color:white;'>
                                                        </td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#e7323a; color:white;'>
                                                        </td>
                                                    <?php elseif ($statusKode == "H") : ?>
                                                        <!-- Employe Hadir -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="background-color:#304de2;" style='vertical-align: middle;'>
                                                            <font color="white"><b><?= $attandance->checkin; ?></b></font>
                                                        </td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="background-color:#304de2;" style='vertical-align: middle;'>
                                                            <font color="white"><b><?= $attandance->checkout; ?></b></font>
                                                        </td>
                                                    <?php elseif ($statusKode == "I") : ?>
                                                        <!-- Employe Ijin -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#17a2b8; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#17a2b8; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                    <?php elseif ($statusKode == "CT") : ?>
                                                        <!-- Employe Cuti Tahunan -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#ffc107; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#ffc107; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                    <?php elseif ($statusKode == "CHD") : ?>
                                                        <!-- Employe Cuti Haid -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#242120; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#242120; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                    <?php elseif ($statusKode == "CHL") : ?>
                                                        <!-- Employe Cuti Hamil -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#C34A36; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#C34A36; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                    <?php elseif ($statusKode == "CM") : ?>
                                                        <!-- Employe Cuti Melahirkan -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#C34A36; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#C34A36; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                    <?php elseif ($statusKode == "S") : ?>
                                                        <!-- Employe Sakit -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#28a745; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#28a745; color:white;'>
                                                            <b><?= $statusKode ?></b>
                                                        </td>
                                                    <?php elseif ($statusKode == "L") : ?>
                                                        <!-- LIBUR -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="vertical-align:middle;"><img src='assets/img/stop.png' width='25' height='25'></td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="vertical-align:middle;"><img src='assets/img/stop.png' width='25' height='25'></td>
                                                    <?php elseif ($statusKode == "RL") : ?>
                                                        <!-- RL -->
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="background-color:#ff7b00; vertical-align:middle; color:white;"><b>RL</b></td>
                                                        <td class="update-attendance" data-tanggal="<?= $a ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="background-color:#ff7b00; vertical-align:middle; color:white;"><b>RL</b></td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php $j++; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalAttendances != 0) : ?>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <td height="25" style="vertical-align:middle;z-index:1">&nbsp;Karyawan</td>
                                    <td height="25" style="vertical-align:middle;z-index:1">&nbsp;Departemen</td>
                                    <td height="25" style="vertical-align:middle;z-index:1">&nbsp;Bagian</td>
                                    <?php foreach ($statusPerizinan as $s) : ?>
                                        <td width="20" align="center">
                                            <b><?= explode("_", $s['value'])[1] ?></b>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Cek apakah sudah digenerate apa belum -->
                                <?php if ($totalAttendances == 0) : ?>
                                    <td colspan="<?= count($statusPerizinan) + 2 ?>" align="left">
                                        Presensi Belum digenerate
                                    </td>
                                <?php else : ?>
                                    <?php $isGenerateTotal = 0; ?>
                                    <?php foreach ($employeesData as $i => $e) : ?>
                                        <?php $status = $attandanceModel->getStatusAttendances($year, $month, $e['id']); ?>
                                        <?php $isGenerate = $attandanceModel->detectIfGenerate($year . "-" . $month, $e['id']); ?>
                                        <?php if ($isGenerate) : ?>
                                            <?php $isGenerateTotal++; ?>
                                            <tr>
                                                <td width="150">
                                                    &nbsp;<?= $e['name'] ?></td>
                                                <td width="110">
                                                    &nbsp;<?= $e['divisi'] ?></td>
                                                </td>
                                                <td width="110">
                                                    &nbsp;<?= $e['nama_bagian'] ?></td>
                                                </td>
                                                <?php foreach ($statusPerizinan as $s) : ?>
                                                    <td width="20" align="center">
                                                        <?= $status[$s['value']] ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($totalAttendances > 0 && count($employeesData) > 10 && $isGenerateTotal > 10) : ?>
                    <?= $pager->links('default', 'bootstrap4_pagination') ?>
                <?php endif ?>


                <div class="card-text mt-4">
                    <b class="text-black">Keterangan</b>
                </div>

                <div class="row mt-3">
                    <?php foreach ($statusPerizinan as $s) : ?>
                        <?php if ($s['value'] == "LIBUR_L") : ?>
                            <div class="col-sm-2 col-4 mt-2">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="text-center">
                                            <img src='assets/img/stop.png' width='28' height='28'>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="card-text mt-1 text-black">
                                            LIBUR (L)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
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
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <div class="col-sm-2 col-4 mt-2">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="p-3" style="width: 5px; height:5px; background-color:#a41fa6;"></div>
                            </div>
                            <div class="col-sm-9">
                                <div class="card-text mt-1 text-black">
                                    BELUM GENERATE
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Generate Attendance</h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <ul class="nav nav-tabs" id="myTab" role="tablist" style="margin-top: -20px;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#global" type="button" role="tab" aria-controls="home" aria-selected="true">Global (Seluruh Karyawan)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab" aria-controls="profile" aria-selected="false">Personal (Per Karyawan)</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="global" role="tabpanel" aria-labelledby="home-tab">
                        <form id="formGenerateGlobalAttendance" role="form" method="POST">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="form-floating mt-1">
                                        <input value="<?= $year . '-' . $month ?>" autocomplete="one-time-code" name="monthYearGlobal" type="month" required class="form-control target">
                                        <label>Periode Absensi</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input readonly value="<?= $startDate ?>" autocomplete="one-time-code" name="startDateGlobal" type="text" required class="form-control target input-picker startDate">
                                        <label for="floatingInput">Tanggal Mulai Log Absen</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input readonly value="<?= $endDate ?>" autocomplete="one-time-code" name="finishDateGlobal" type="text" required class="form-control target input-picker endDate">
                                        <label for="floatingInput">Tanggal Selesai Log Absen</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-submit-form" id="globalGenerateBtn">Generate</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="single" role="tabpanel" aria-labelledby="profile-tab">
                        <form id="formGeneratePersonalAttendance">
                            <div class="row mb-2">
                                <div class="col-md-12 mt-3">
                                    <div class="form-floating mt-1">
                                        <input value="<?= $year . '-' . $month ?>" autocomplete="one-time-code" name="monthYearPersonal" type="month" required class="form-control target">
                                        <label>Periode Absensi</label>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="divisionID" name="filterDivisi" aria-label="Floating label select example">
                                            <option value="">
                                                Cari Departemen
                                            </option>
                                            <?php foreach ($divisi as $d) : ?>
                                                <option value="<?= $d['id'] ?>">
                                                    <?= $d['divisi']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput">Cari Departemen</label>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="employeeID" name="filterEmployee" aria-label="Floating label select example">
                                            <option value="">
                                                Cari Berdasarkan Nama Karyawan
                                            </option>
                                        </select>
                                        <label for="floatingInput">Cari Berdasarkan Nama Karyawan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="<?= $startDate ?>" readonly autocomplete="one-time-code" name="startDatePersonal" type="text" required class="form-control target input-picker startDate">
                                        <label for="floatingInput">Tanggal Mulai Log Absen</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="<?= $endDate ?>" readonly autocomplete="one-time-code" name="finishDatePersonal" type="text" required class="form-control target input-picker endDate">
                                        <label for="floatingInput">Tanggal Selesai Log Absen</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-submit-form" id="singleGenerateBtn">Generate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal" id="updateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Update Attendance</h5>
            </div>
            <form id="updateAttendanceForm" role="form" method="POST">
                <div class="modal-body">
                    <div class="alert bg-info text-white" style="margin-top: -10px;">
                        <div class="card-text">
                            Status Perizinan yang tidak disetujui akan dimasukkan kedalam perhitungan potongan pada Payroll
                        </div>
                    </div>
                    <input type="hidden" name="attendanceID" id="attendenceID" />
                    <?= csrf_field() ?>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" class="form-control" id="employeeName" disabled>
                        <label for="employeeName">Employe Name</label>
                    </div>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" name="tanggal" class="form-control" id="tanggal" disabled>
                        <label for="tanggal">Tanggal</label>
                    </div>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <select name="statusKehadiran" class="form-select" id="statusKehadiran">
                            <option selected>Pilih Status Kehadiran</option>
                            <?php foreach ($statusPerizinan as $sk) : ?>
                                <option value="<?= $sk['value']; ?>"><?= explode("_", $sk['value'])[0] . " (" . explode("_", $sk['value'])[1] . ")"; ?></option>
                            <?php endforeach ?>
                        </select>
                        <label for="status">Status Kehadiran</label>
                    </div>

                    <div class="form-floating mb-2" style="height: 50px;" id="reasonForm">
                        <input type="text" name="reason" class="form-control" id="reason">
                        <label for="floatingInput">Reason</label>
                    </div>

                    <div class="form-floating mb-2" style="height: 50px;" id="approvalForm">
                        <select name="isApproved" class="form-select" id="isApproved">
                            <?php $statusApproval = ["APPROVED", "NOT APPROVED"]; ?>
                            <?php foreach ($statusApproval as $sa) : ?>
                                <option value="<?= $sa == "APPROVED" ? '1' : '0' ?>"><?= $sa; ?></option>
                            <?php endforeach ?>
                        </select>
                        <label for="floatingInput">Status Approval</label>
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
                                <input type="text" class="form-control" id="checkin" name="checkIn" maxlength="30">
                                <label for="checkin">CheckIN</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <input type="text" class="form-control" id="checkout" name="checkOut" maxlength="30">
                                <label for="checkout">CheckOut</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3">Batal</button>
                    <button type="submit" class="btn btn-submit-form">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="triwulanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Print Triwulan Absensi</h5>
            </div>
            <form id="printTriwulanPDF" class="create-form" role="form" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-floating mb-2">
                                <select class="form-select" name="divisionID" aria-label="Floating label select example">
                                    <option value="">
                                        Cari Departemen
                                    </option>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Cari Departemen</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <input type="month" class="form-control" name="startMonth" id="startMonth">
                                <label for="startMonth">Mulai</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <input type="month" class="form-control" name="endMonth" id="endMonth">
                                <label for="endMonth">Selesai</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3">Batal</button>
                    <button type="submit" class="btn btn-submit-form" id="printBtnTriwulan">Print</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // hide loading
        $('#loadingSpinner').hide();
        // csrf
        const csrfToken = '<?= csrf_token() ?>';
        // hide modal
        $('.btn-discard').click(function() {
            $('#updateModal').hide();
        });
        // select2 divisi
        $("select[name='divisiID']").select2({
            placeholder: "Cari Departemen",
            theme: "bootstrap-5",
            allowClear: true,
        });
        // post generate global attendance
        $('#globalGenerateBtn').click(function(e) {
            e.preventDefault();
            // set variable
            const csrf = $(`[name="${csrfToken}"]`);
            var monthYearGlobal = $("input[name='monthYearGlobal']").val();
            var startDateGlobal = $("input[name='startDateGlobal']").val();
            var finishDateGlobal = $("input[name='finishDateGlobal']").val();

            if (monthYearGlobal == '') {
                Swal.fire({
                    icon: 'warning',
                    title: "Pilih periode absensi",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else if (startDateGlobal == '') {
                Swal.fire({
                    icon: 'warning',
                    title: "Tanggal mulai log absensi tidak boleh kosong",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else if (finishDateGlobal == '') {
                Swal.fire({
                    icon: 'warning',
                    title: "Tanggal selesai log absensi tidak boleh kosong",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Generate Global Presensi ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    // append to form
                    var formData = new FormData();
                    formData.append('monthYear', monthYearGlobal);
                    formData.append('startDate', startDateGlobal);
                    formData.append('finishDate', finishDateGlobal);

                    $.ajax({
                        url: "<?= base_url("generate-attendance/global"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            // show loading
                            $('#loadingSpinner').show();
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then((result) => {
                                    // update table
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                            $('#loadingSpinner').hide();
                        },
                        onError: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan pada sistem',
                                confirmButtonColor: '#4e73df',
                            });
                            $('#loadingSpinner').hide();
                        }
                    });
                });
            }
        });
        // post generate personal attendance
        $('#singleGenerateBtn').click(function(e) {
            e.preventDefault();
            // set variable
            const csrf = $(`[name="${csrfToken}"]`);
            var monthYearPersonal = $("input[name='monthYearPersonal']").val();
            var startDatePersonal = $("input[name='startDatePersonal']").val();
            var finishDatePersonal = $("input[name='finishDatePersonal']").val();
            var employeeID = $('#employeeID').val();
            var divisionID = $('#divisionID').val();

            if (monthYearPersonal == '') {
                Swal.fire({
                    icon: 'warning',
                    title: "Pilih periode absensi",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else if (divisionID == '') {
                Swal.fire({
                    icon: 'warning',
                    title: "Pilih divisi",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else if (employeeID == null) {
                Swal.fire({
                    icon: 'warning',
                    title: "Pilih karyawan",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else if (startDatePersonal == '') {
                Swal.fire({
                    icon: 'warning',
                    title: "Tanggal mulai log absensi tidak boleh kosong",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else if (finishDatePersonal == '') {
                Swal.fire({
                    icon: 'warning',
                    title: "Tanggal selesai log absensi tidak boleh kosong",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Generate Personal Presensi ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    // append to form
                    var formData = new FormData();
                    formData.append('monthYear', monthYearPersonal);
                    formData.append('startDate', startDatePersonal);
                    formData.append('finishDate', finishDatePersonal);
                    formData.append('employeeID', employeeID);

                    $.ajax({
                        url: "<?= base_url("generate-attendance/personal"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            // show loading
                            $('#loadingSpinner').show();
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then((result) => {
                                    // update table
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                            $('#loadingSpinner').hide();
                        },
                        onError: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan pada sistem',
                                confirmButtonColor: '#4e73df',
                            });
                            $('#loadingSpinner').hide();
                        }
                    });
                });
            }
        });
        // Display Modal Change Status Attendence 
        $('.update-attendance').click(function(e) {
            e.preventDefault();

            // get var
            var employeeID = $(this).data('employee_id');
            var tanggal = $(this).data('tanggal');
            const csrf = $(`[name="${csrfToken}"]`);
            // append to form
            var formData = new FormData();
            formData.append('employeeID', employeeID);
            formData.append('tanggal', tanggal);
            // Ajax Get Detail Status By Tanggal and EmployeeID
            $.ajax({
                url: "<?= base_url("get-attendance"); ?>",
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    var attendance = response.data.attendance;
                    var employee = response.data.employee;

                    $('#reason').val(null);
                    $('#attendenceID').val(attendance.id);
                    $('#employeeName').val(employee.name);
                    $('#tanggal').val(response.data.tanggal);
                    $('#statusKehadiran').val(attendance.status);
                    $('#keterangan').val(response.data.keterangan);
                    $('#jamTerlambat').val(response.data.jamTerlambat);
                    $('#isApproved').val(attendance.isApproved);

                    if (attendance.status == 'HADIR_H') {
                        // hadir
                        $('#reasonForm').hide();
                        $('#formInOut').show();
                        $('#approvalForm').hide();
                        // set form
                        $('#checkout').val(attendance.checkout);
                        $('#checkin').val(attendance.checkin);
                    } else if (attendance.status == "ALPHA_A" || attendance.status == "LIBUR_L" || attendance.status == "RL_RL") {
                        $('#approvalForm').hide();
                    } else {
                        // ada perizinan
                        $('#reasonForm').show();
                        $('#formInOut').hide();
                        $('#approvalForm').show();
                        $('#reason').val(attendance.reason);
                    }

                    $('#updateModal').show();
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
        // if on change divisi
        $('#employeeID').attr('disabled', true);
        $("#divisionID").on('change', function() {
            $("#employeeID").empty();
            if ($(this).val() == "") {
                $('#employeeID').attr('disabled', true);
            } else {
                $('#employeeID').attr('disabled', false);
            }
        });

        // on change status kehadiran
        $('#statusKehadiran').change(function(e) {
            e.preventDefault();
            if ($(this).val() == "HADIR_H") {
                // hadir
                $("input[name='checkIn']").attr('required', true);
                $("input[name='checkOut']").attr('required', true);
                $('#reasonForm').hide();
                $('#approvalForm').hide();
                $('#formInOut').show();
            } else if ($(this).val() == "ALPHA_A" || $(this).val() == "LIBUR_L" || $(this).val() == "RL_RL") {
                $('#approvalForm').hide();
            } else {
                // izin
                $("input[name='checkIn']").attr('required', false);
                $("input[name='checkOut']").attr('required', false);
                $('#reasonForm').show();
                $('#approvalForm').show();
                $('#formInOut').hide();
            }

        });
        // datepicker checkin dan checkout
        $(function() {
            $('#checkin').datetimepicker({
                format: 'HH:mm:ss',
                icons: {
                    up: 'fas fa-chevron-up',
                    down: 'fas fa-chevron-down'
                },
            });
            $('#checkout').datetimepicker({
                format: 'HH:mm:ss',
                icons: {
                    up: 'fas fa-chevron-up',
                    down: 'fas fa-chevron-down'
                },
            });
        });
        // update attendance
        $('#updateAttendanceForm').submit(function(e) {
            e.preventDefault();
            // set variable
            const csrf = $(`[name="${csrfToken}"]`);
            var attendenceID = $('#attendenceID').val();
            var statusKehadiran = $('#statusKehadiran').val();
            var reason = $('#reason').val();
            var checkIn = $('#checkin').val();
            var checkOut = $('#checkout').val();
            var isApproved = $('#isApproved').val();
            // append to form
            var formData = new FormData();
            formData.append('attendenceID', attendenceID);
            formData.append('statusKehadiran', statusKehadiran);
            formData.append("reason", reason);
            formData.append("reason", reason);
            formData.append("checkIn", checkIn);
            formData.append("checkOut", checkOut);
            formData.append("isApproved", isApproved);

            $.ajax({
                url: "<?= base_url("update-attendance"); ?>",
                data: formData,
                method: "POST",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    }).then((result) => {
                        location.reload();
                    });;
                    stopLoading()
                },
                onError: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan pada sistem',
                        confirmButtonColor: '#4e73df',
                    });
                    stopLoading()
                }
            });
        });
        // Search employee
        $("select[name='select2EmployeesName']").select2({
            placeholder: "Cari Nama Karyawan",
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
        // otomatis generate start dan end 
        $('input[name="monthYearGlobal"], input[name="monthYearPersonal"]').on('change', function() {
            var inputValue = $(this).val();
            var dateParts = inputValue.split('-');
            var year = parseInt(dateParts[0]);
            var month = parseInt(dateParts[1]);

            var startDate = new Date(year, month - 2, 23);
            var endDate = new Date(year, month - 1, 21);

            var formattedStartDate = startDate.getDate() + '/' + (startDate.getMonth() + 1) + '/' + startDate.getFullYear();
            var formattedEndDate = endDate.getDate() + '/' + (endDate.getMonth() + 1) + '/' + endDate.getFullYear();

            $('.startDate').val(formattedStartDate).attr('readonly', true);
            $('.endDate').val(formattedEndDate).attr('readonly', true);
        });
        // filter table by employee
        $("select[name='select2EmployeesName']").on("change", function() {
            var selectedValue = $(this).val();
            window.location.href = "<?= base_url("list-attendance?month=$month&year=$year") ?>&employeesID=" + selectedValue;
        });
        // filter htable by division
        $("select[name='divisiID']").on("change", function() {
            var selectedValue = $(this).val();
            window.location.href = "<?= base_url("list-attendance?month=$month&year=$year") ?>&divisiID=" + selectedValue;
        });
        // filter select2 init
        $("select[name='filterDivisi']").select2({
            placeholder: "Cari Departemen",
            theme: "bootstrap-5",
            allowClear: true,
        });
        // select2 filter employee ajax
        $("select[name='filterEmployee']").select2({
            placeholder: "Cari Nama Karyawan",
            theme: "bootstrap-5",
            allowClear: true,
            minimumInputLength: 2,
            dropdownParent: $('#generateModal'),
            ajax: {
                url: "<?= base_url('attendance/like-employees') ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        employeesName: params.term,
                        divisiID: $("select[name='filterDivisi']").val(),
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
        $('#triwulanBtnPDF').click(function() {
            $('#triwulanModal').modal('show');
        });

        $('#btn-discard').click(function() {
            $('#triwulanModal').modal('hide');
        });

        var validator = $(".create-form").validate({
            rules: {
                divisionID: {
                    required: true
                },
                startMonth: {
                    required: true
                },
                endMonth: {
                    required: true
                },
            },
            messages: {
                divisionID: {
                    required: "Pilih Departemen"
                },
                startMonth: {
                    required: "Mulai bulan wajib diisi"
                },
                endMonth: {
                    required: "Selesai bulan wajib diisi"
                },
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $('#printBtnTriwulan').click(function(e) {
            e.preventDefault();
            if ($('.create-form').valid()) {
                var startMonth = $('#startMonth').val();
                var endMonth = $('#endMonth').val();
                var divisionID = $('select[name="divisionID"]').val();
                window.open("<?= base_url('list-attendance/triwulan/id') ?>" + '/' + startMonth + '/' + endMonth + '/' + divisionID, "_blank");
            }
        })

        // style helper
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
    });
</script>

<?= $this->endSection(); ?>
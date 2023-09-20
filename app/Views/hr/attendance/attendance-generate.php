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
        <h1>Attendance</h1>
        <div class="col-button-tambah-spp">
            <?php if ($totalAttendances == 0) : ?>
                <a class="btn btn-hide-form btn-discard float-right" data-bs-toggle="modal" data-bs-target="#generateModal" href="#">
                    Generate
                </a>
            <?php endif; ?>
            <?php if ($totalAttendances != 0) : ?>
                <form id="formPosting" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="year" id="year" value="<?= $year ?>">
                    <input type="hidden" name="month" id="month" value="<?= $month ?>">
                    <?php if ($isPostingPayroll != 0) : ?>
                        <input type="hidden" name="statusPosting" id="statusPosting" value="0">
                        <button disabled class="btn btn-show-form btn-save float-right btn-submit">
                            <i class="fa-solid fa-check mr-1 fa-lg"></i> Sudah Posting
                        </button>
                    <?php else : ?>
                        <?php if ($isPosting == 0 && $totalAttendances != 0) : ?>
                            <a class="btn btn-show-form btn-discard float-right btn-generate-ulang" data-bs-toggle="modal" data-bs-target="#generateModal" href="#">
                                Generate Ulang
                            </a>
                            <input type="hidden" name="statusPosting" id="statusPosting" value="1">
                            <button type="submit" class="btn btn-show-form btn-save float-right btn-submit">
                                Posting
                            </button>
                            <button class="btn btn-warning btn-print float-right" onclick="alert('Fitur print belum tersedia')">
                                <i class="fa-solid fa-print"></i> Print
                            </button>
                        <?php else : ?>
                            <input type="hidden" id="statusPosting" name="statusPosting" value="0">
                            <button class="btn btn-show-form btn-save float-right btn-submit">
                                Batalkan Posting
                            </button>
                        <?php endif ?>
                    <?php endif; ?>
                </form>
            <?php endif ?>
        </div>
    </div>
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
                <div class="col-6 mb-2">
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
                                        Cari Berdasarkan Divisi
                                    </option>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option <?= @$_GET['divisiID'] == $d['id'] ? 'selected' : '' ?> value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Cari Berdasarkan Divisi</label>
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
                                <td height="25" style="vertical-align:middle;z-index:9999">&nbsp;Karyawan</td>
                                <td height="25" style="vertical-align:middle;z-index:9999">&nbsp;Divisi</td>
                                <?php
                                $lastDate = date("t", strtotime($year . "-" . $month . "-01"));
                                for ($i = 1; $i <= $lastDate; $i++) :
                                    $temp = mktime(0, 0, 0, $month, $i, $year);

                                    if (date("N", $temp) == 7) {
                                        echo "<td align=center  style=\"vertical-align:middle;\" width=\"25\" height=\"25\"><font color='red'>Masuk " . $i . "</font></td>";
                                        echo "<td align=center  style=\"vertical-align:middle;\" width=\"25\" height=\"25\"><font color='red'>Keluar " . $i . "</font></td>";
                                    } else {
                                        echo "<td align=center style=\"vertical-align:middle;\" width=\"25\" height=\"25\">Masuk " . $i . "</td>";
                                        echo "<td align=center style=\"vertical-align:middle;\" width=\"25\" height=\"25\">Keluar<br> " . $i . "</td>";
                                    }

                                endfor
                                ?>
                                <td>Hadir</td>
                                <td>Ijin</td>
                                <td>Alpha</td>
                                <td>Cuti</td>
                                <td>Sakit</td>
                                <td>Libur</td>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Cek apakah sudah digenerate apa belum -->
                            <?php if ($totalAttendances == 0) : ?>
                                <td colspan="<?= $temp ?>" align="left">
                                    Presensi Belum digenerate
                                </td>
                            <?php else : ?>
                                <?php foreach ($employeesData as $i => $e) : ?>
                                    <?php
                                    // declare model
                                    $attandanceModel = new \App\Models\AttendancesModel();
                                    $status = $attandanceModel->getStatusAttendances($year, $month, $e['id']);
                                    ?>
                                    <?php $libur = 0; ?>
                                    <tr>
                                        <td style="vertical-align:middle;z-index:9999" nowrap>
                                            &nbsp; <?= $e['name']; ?>
                                        </td>
                                        <td style="vertical-align:middle;z-index:9999" nowrap>
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
                                                <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="vertical-align:middle;"><img src='assets/img/stop.png' width='25' height='25'></td>
                                                <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="vertical-align:middle;"><img src='assets/img/stop.png' width='25' height='25'></td>
                                            <?php else : ?>
                                                <?php if ($attandance->status == "ALPHA") : ?>
                                                    <!-- Employe Tidak Hadir -->
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#e7323a; color:white;'>
                                                    </td>
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#e7323a; color:white;'>
                                                    </td>
                                                <?php elseif ($attandance->status == "HADIR") : ?>
                                                    <!-- Employe Hadir -->
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="background-color:#304de2" style='vertical-align: middle;'>
                                                        <font color="white"><b><?= $attandance->checkin; ?></b></font>
                                                    </td>
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="background-color:#304de2" style='vertical-align: middle;'>
                                                        <font color="white"><b><?= $attandance->checkout; ?></b></font>
                                                    </td>
                                                <?php elseif ($attandance->status == "IJIN") : ?>
                                                    <!-- Employe Ijin -->
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#17a2b8; color:white;'>
                                                        <b><?= $attandance->status ?></b>
                                                    </td>
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#17a2b8; color:white;'>
                                                        <b><?= $attandance->status ?></b>
                                                    </td>
                                                <?php elseif ($attandance->status == "CUTI") : ?>
                                                    <!-- Employe Cuti -->
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#ffc107; color:white;'>
                                                        <b><?= $attandance->status ?></b>
                                                    </td>
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#ffc107; color:white;'>
                                                        <b><?= $attandance->status ?></b>
                                                    </td>
                                                <?php elseif ($attandance->status == "SAKIT") : ?>
                                                    <!-- Employe Sakit -->
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#28a745; color:white;'>
                                                        <b><?= $attandance->status ?></b>
                                                    </td>
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style='background-color:#28a745; color:white;'>
                                                        <b><?= $attandance->status ?></b>
                                                    </td>
                                                <?php elseif ($attandance->status == "LIBUR") : ?>
                                                    <!-- LIBUR -->
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="vertical-align:middle;"><img src='assets/img/stop.png' width='25' height='25'></td>
                                                    <td class="update-attendance" data-tanggal="<?= $dateFormat ?>" data-employee_id="<?= $e['id'] ?>" width=25 align=center style="vertical-align:middle;"><img src='assets/img/stop.png' width='25' height='25'></td>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <td align="center"><b><?= $status['HADIR'] ?></b></td>
                                        <td align="center"><b><?= $status['IJIN'] ?></b></td>
                                        <td align="center"><b><?= $status['ALPHA'] - $libur ?></b></td>
                                        <td align="center"><b><?= $status['CUTI'] ?></b></td>
                                        <td align="center"><b><?= $status['SAKIT'] ?></b></td>
                                        <td align="center"><b><?= $status['LIBUR'] ?></b></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalAttendances != 0) : ?>
                    <?php if ($pager->hasMore() || ($pager->getCurrentPage() < $pager->getPageCount()) || (count($employeesData) > $pager->getPerPage())) : ?>
                        <?= $pager->links('default', 'bootstrap4_pagination') ?>
                    <?php endif ?>
                <?php endif ?>

                <div class="card-text mt-4">
                    <b class="text-black">Keterangan</b>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-2 col-4">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="p-3" style="width: 5px; height:5px; background-color:#304de2"></div>
                            </div>
                            <div class="col-sm-9">
                                <div class="card-text mt-1 text-black">
                                    HADIR
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 col-4">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="p-3" style="width: 5px; height:5px; background-color:#e7323a"></div>
                            </div>
                            <div class="col-sm-9">
                                <div class="card-text mt-1 text-black">
                                    ALPHA
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 col-4">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="p-3" style="width: 5px; height:5px; background-color:#17a2b8;"></div>
                            </div>
                            <div class="col-sm-9">
                                <div class="card-text mt-1 text-black">
                                    IZIN
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 col-4">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="p-3" style="width: 5px; height:5px; background-color:#ffc107;"></div>
                            </div>
                            <div class="col-sm-9">
                                <div class="card-text mt-1 text-black">
                                    CUTI
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2 col-4">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="p-3" style="width: 5px; height:5px; background-color:#28a745;"></div>
                            </div>
                            <div class="col-sm-9">
                                <div class="card-text mt-1 text-black">
                                    SAKIT
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
            <form id="formGenerateAttendance" role="form" method="POST">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="row mb-2" id="formCheckInOut">
                        <div class="col-md-6">
                            <div class="form-floating mt-1">
                                <select name="month" class="form-select" id="labelMonthFilter" aria-label="Pilih Filter Bulan">
                                    <option selected>Pilih Bulan</option>
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
                                <label for="labelMonthFilter">Month</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mt-1">
                                <select name="year" class="form-select" id="labelYearFilter" aria-label="Pilih Filter Tahun">
                                    <option selected>Pilih Tahun</option>
                                    <?php
                                    for ($i = date("Y") - 2; $i <= date("Y") + 2; $i++) :
                                        $checked = ($year == $i) ? "selected" : "";
                                    ?>
                                        <option value="<?= $i; ?>" <?= $checked; ?>><?= $i; ?></option>
                                    <?php endfor ?>
                                </select>
                                <label for="labelYearFilter">Year</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                    <?php if ($isPosting == 0) : ?>
                        <button type="submit" class="btn btn-submit-form">Generate</button>
                    <?php else : ?>
                        <button type="button" disabled class="btn btn-submit-form">Sudah Posting</button>
                    <?php endif ?>
                </div>
            </form>
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
                            <?php $statusKehadiran = ["HADIR", "IJIN", "ALPHA", "CUTI", "SAKIT"]; ?>
                            <?php foreach ($statusKehadiran as $sk) : ?>
                                <option value="<?= $sk; ?>"><?= $sk; ?></option>
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


<script>
    $(document).ready(function() {
        // csrf
        const csrfToken = '<?= csrf_token() ?>';
        // hide modal
        $('.btn-discard').click(function() {
            $('#updateModal').hide();
        });
        // select2 divisi
        $("select[name='divisiID']").select2({
            placeholder: "Cari Berdasarkan Divisi",
            theme: "bootstrap-5",
            allowClear: true,
        });
        // post generate attendance
        $('#formGenerateAttendance').submit(function(e) {
            e.preventDefault();
            setLoading()
            // set variable
            const csrf = $(`[name="${csrfToken}"]`);
            var month = $("select[name='month']").val();
            var year = $("select[name='year']").val();
            // append to form
            var formData = new FormData();
            formData.append('month', month);
            formData.append('year', year);

            $.ajax({
                url: "<?= base_url("generate-attendance"); ?>",
                data: formData,
                method: "POST",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.code == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        });
                        // update table
                        location.reload();
                    } else if (response.code == 422) {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        });
                    }
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
        // Display Modal Change Status Attendence 
        $('.update-attendance').click(function(e) {
            e.preventDefault();
            if ($('#statusPosting').val() == 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Presensi sudah diposting",
                    confirmButtonColor: '#4e73df',
                });
            } else {
                setLoading();
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

                        $('#attendenceID').val(attendance.id);
                        $('#employeeName').val(employee.name);
                        $('#tanggal').val(response.data.tanggal);
                        $('#statusKehadiran').val(attendance.status);
                        $('#keterangan').val(response.data.keterangan);
                        $('#jamTerlambat').val(response.data.jamTerlambat);
                        $('#isApproved').val(attendance.isApproved);

                        console.log(response.data);

                        if (attendance.status == 'HADIR') {
                            // hadir
                            $('#reasonForm').hide();
                            $('#formInOut').show();
                            $('#approvalForm').hide();
                            // set form
                            $('#checkout').val(attendance.checkout);
                            $('#checkin').val(attendance.checkin);
                        } else {
                            // ada perizinan
                            $('#reasonForm').show();
                            $('#formInOut').hide();
                            $('#approvalForm').show();
                            $('#reason').val(attendance.reason);
                        }

                        $('#updateModal').show();
                        stopLoading()
                    },
                    onError: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan pada sistem',
                            confirmButtonColor: '#4e73df',
                        })
                        stopLoading()
                    }
                });
            }

        });
        // Hari Libur
        $('.hari-libur').click(function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tanggal ' + $(this).data('tanggal') + ' adalah hari libur',
                confirmButtonColor: '#4e73df',
            });
        });
        // on change status kehadiran
        $('#statusKehadiran').change(function(e) {
            e.preventDefault();
            if ($(this).val() == "HADIR") {
                // hadir
                $("input[name='checkIn']").attr('required', true);
                $("input[name='checkOut']").attr('required', true);
                $('#reasonForm').hide();
                $('#approvalForm').hide();
                $('#formInOut').show();
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
        // update status posting
        $('#formPosting').submit(function(e) {
            e.preventDefault();
            // set variable
            const csrf = $(`[name="${csrfToken}"]`);
            var statusPosting = $('#statusPosting').val();
            var year = $('#year').val();
            var month = $('#month').val();
            // alert
            Swal.fire({
                icon: 'question',
                title: statusPosting == 1 ? "Posting Presensi ?" : "Batalkan Posting Presensi ?",
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    setLoading();

                    // append to form
                    var formData = new FormData();
                    formData.append('statusPosting', statusPosting);
                    formData.append('year', year);
                    formData.append("month", month);

                    $.ajax({
                        url: "<?= base_url("posting-unposting-attendance"); ?>",
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
                            });
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
        $("select[name='select2EmployeesName']").on("change", function() {
            var selectedValue = $(this).val();
            window.location.href = "<?= base_url("list-attendance?month=$month&year=$year") ?>&employeesID=" + selectedValue;
        });
        $("select[name='divisiID']").on("change", function() {
            var selectedValue = $(this).val();
            window.location.href = "<?= base_url("list-attendance?month=$month&year=$year") ?>&divisiID=" + selectedValue;
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
    });
</script>

<?= $this->endSection(); ?>
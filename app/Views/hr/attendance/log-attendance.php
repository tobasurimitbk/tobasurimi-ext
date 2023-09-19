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
        <h1>Log Attendance</h1>
        <div class="col-button-tambah-spp">
            <button class="btn btn-warning btn-print float-right" onclick="alert('Fitur print belum tersedia')">
                <i class="fa-solid fa-print"></i> Print
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-page-list-attendance">
                <div class="col-6 mb-2">
                    <form id="search_form" name="search_form" class="kt-form kt-form--fit kt-margin-b-20" method="POST">

                        <!-- <?php echo "Attendance " . date("F Y", strtotime($year . "-" . $month . "-01")); ?> -->
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
                                <label for="floatingInput">Cari Berdasarkan Karyawan</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
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
                                <td height="25" style="vertical-align:middle;z-index:9999">&nbsp;Karyawan</td>
                                <td height="25" style="vertical-align:middle;z-index:9999">&nbsp;Divisi</td>
                                <?php
                                $last_date = date("t", strtotime($year . "-" . $month . "-01"));
                                for ($i = 1; $i <= $last_date; $i++) :
                                    $temp = mktime(0, 0, 0, $month, $i, $year);
                                    $no = (strlen($i) == 1) ? ("0" . $i) : $i;

                                    if (date("N", $temp) == 7) :
                                        echo "<td align=center  style=\"vertical-align:middle;\" width=\"25\" height=\"25\"><font color='red'>Masuk " . $i . "</font></td>";
                                        echo "<td align=center  style=\"vertical-align:middle;\" width=\"25\" height=\"25\"><font color='red'>Keluar " . $i . "</font></td>";
                                    else :
                                        echo "<td align=center style=\"vertical-align:middle;\" width=\"25\" height=\"25\">Masuk " . $i . "</td>";
                                        echo "<td align=center style=\"vertical-align:middle;\" width=\"25\" height=\"25\">Keluar " . $i . "</td>";
                                    endif;
                                endfor;
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
                            <?php for ($i = 0; $i < count($res_user); $i++) : ?>
                                <?php
                                $hadir = 0;
                                $alpha = 0;
                                $libur = 0;
                                ?>
                                <tr>
                                    <td style="vertical-align:middle;z-index:9999" nowrap>
                                        &nbsp;<?php echo $res_user[$i]["employeeName"]; ?></td>
                                    <td style="vertical-align:middle;z-index:9999" nowrap>
                                        &nbsp;<?php echo $res_user[$i]["divisi"]; ?></td>
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
                                            <?php if ($perizinanCheck['status']  == "IJIN") : ?>
                                                <!-- Ada perizinan ijin -->
                                                <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#17a2b8; color:white;cursor:pointer;'>
                                                    <b><?= $perizinanCheck['status'] ?></b>
                                                </td>
                                                <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#17a2b8; color:white;cursor:pointer;'>
                                                    <b><?= $perizinanCheck['status']  ?></b>
                                                </td>
                                            <?php elseif ($perizinanCheck['status'] == "CUTI") : ?>
                                                <!-- Ada perizinan cuti -->
                                                <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#ffc107; color:white;cursor:pointer;'>
                                                    <b><?= $perizinanCheck['status'] ?></b>
                                                </td>
                                                <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#ffc107; color:white;cursor:pointer;'>
                                                    <b><?= $perizinanCheck['status']  ?></b>
                                                </td>
                                            <?php elseif ($perizinanCheck['status'] == "SAKIT") : ?>
                                                <!-- Ada perizinan sakit -->
                                                <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#28a745; color:white;cursor:pointer;'>
                                                    <b><?= $perizinanCheck['status'] ?></b>
                                                </td>
                                                <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style='background-color:#28a745; color:white;cursor:pointer;'>
                                                    <b><?= $perizinanCheck['status']  ?></b>
                                                </td>
                                            <?php endif ?>
                                        <?php else : ?>
                                            <?php if ($check == 1) : ?>
                                                <?php $hadir++; ?>
                                                <td class="detail" data-tanggal="<?= $dateFormat; ?>" data-employee_id="<?= $res_user[$i]['employeeID'] ?>" width=25 align=center style="background-color:#304de2" style='vertical-align: middle;cursor:pointer;'>
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
                                                    echo "<td class='detail'  data-tanggal='" . $dateFormat . "' data-employee_id='" . $res_user[$i]["employeeID"] . "' width=25 align=center style='background-color:#e7323a;cursor:pointer;'></td>";
                                                    echo "<td class='detail'  data-tanggal='" . $dateFormat . "' data-employee_id='" . $res_user[$i]["employeeID"] . "' width=25 align=center style='background-color:#e7323a;cursor:pointer;'></td>";
                                                }

                                                ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <td>
                                        <b><?= $hadir ?></b>
                                    </td>
                                    <td align=center>
                                        <b><?= $res_user[$i]['statusAttendances']['IJIN']; ?></b>
                                    </td>
                                    <td align=center>
                                        <b><?= $alpha ?></b>
                                    </td>
                                    <td align=center>
                                        <b><?= $res_user[$i]['statusAttendances']['CUTI']; ?></b>
                                    </td>
                                    <td align=center>
                                        <b><?= $res_user[$i]['statusAttendances']['SAKIT']; ?></b>
                                    </td>
                                    <td align=center>
                                        <b><?= $libur ?></b>
                                    </td>
                                </tr>
                            <?php endfor; ?>
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
                        <button type="button" class="btn btn-hide-form btn-discard mr-3">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    function printReport() {
        document.location.href = 'log-attendance?month=' + document.getElementById('month').value + '&year=' + document.getElementById('year').value;
    }
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

    $("select[name='divisiID']").select2({
        placeholder: "Cari Berdasarkan Divisi",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $("select[name='select2EmployeesName']").on("change", function() {
        var selectedValue = $(this).val();
        window.location.href = "<?= base_url("log-attendance?month=$month&year=$year") ?>&employeesID=" + selectedValue;
    });
    $("select[name='divisiID']").on("change", function() {
        var selectedValue = $(this).val();
        window.location.href = "<?= base_url("log-attendance?month=$month&year=$year") ?>&divisiID=" + selectedValue;
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

        console.log(tanggal);
        console.log(employeeID);

        $.ajax({
            url: "<?= base_url("log-attendance/detail"); ?>",
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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

                $('#detailModal').show();
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
    $('.btn-discard').click(function() {
        $('#detailModal').hide();
    });
</script>


<?= $this->endSection(); ?>
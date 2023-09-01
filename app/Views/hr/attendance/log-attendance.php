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
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-page-list-attendance">
                <div class="col-6 mb-4">
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
                <div class="col-6 mb-4">
                    <!--begin: Datatable -->
                    <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                </div>
            </div>
            <div class="row row-col-page-list-attendance">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                        <thead>
                            <tr>
                                <td height="25" style="vertical-align:middle;z-index:9999">&nbsp;User</td>
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
                                ?>
                                <tr>
                                    <td style="vertical-align:middle;z-index:9999" nowrap>
                                        &nbsp;<?php echo $res_user[$i]["employeeName"]; ?></td>
                                    <?php
                                    for ($j = 1; $j <= $last_date; $j++) :
                                        $no = (strlen($j) == 1) ? ("0" . $j) : $j;
                                        $jam_masuk = ""; // checkOut
                                        $jam_keluar = ""; // checkIN
                                        $check = 0; // cek apakah ada di log absen tidak

                                        $dateFormat = ($year . "-" . $month . "-" . $no);
                                        $formPerizinanModel = new \App\Models\FormPerijinanModel();

                                        $perizinanCheck = $formPerizinanModel
                                            ->where('employee_id', $res_user[$i]['employeeID'])
                                            ->where('periode', ($year . "-" . $month . "-" . $no))
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
                                        <?php if ($perizinanCheck != null) : ?>
                                            <!-- Ada perizinan -->
                                            <?php if ($perizinanCheck['status']  != "ALPHA") : ?>
                                                <!-- Ada perizinan bukan alpha -->
                                                <td width=25 align=center style='background-color:#d6bc27; color:white;'>
                                                    <b><?= $perizinanCheck['status'] ?></b>
                                                </td>
                                                <td width=25 align=center style='background-color:#d6bc27; color:white;'>
                                                    <b><?= $perizinanCheck['status']  ?></b>
                                                </td>
                                            <?php else : ?>
                                                <!-- Ada perizinan dengan status alpha -->
                                                <?php $alpha++; ?>
                                                <td width=25 align=center style='background-color:#e7323a'></td>
                                                <td width=25 align=center style='background-color:#e7323a'></td>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <?php if ($check == 1) : ?>
                                                <?php $hadir++; ?>
                                                <td width=25 align=center style="background-color:#304de2" style='vertical-align: middle;'>
                                                    <font color="white"><b><?= $jam_masuk; ?></b></font>
                                                </td>
                                                <td width=25 align=center style="background-color:#304de2" style='vertical-align: middle;'>
                                                    <font color="white"><b><?= $jam_keluar; ?></b></font>
                                                </td>
                                            <?php else : ?>

                                                <?php
                                                $temp = mktime(0, 0, 0, $month, $j, $year);
                                                if (date("N", $temp) == 7) {
                                                    // Hari Minggu
                                                    echo "<td width=25 align=center style=\"vertical-align:middle;\"><img src='assets/img/stop.png' width='25' height='25'></td>";
                                                    echo "<td width=25 align=center style=\"vertical-align:middle;\"><img src='assets/img/stop.png' width='25' height='25'></td>";
                                                } else {
                                                    // tidak absen = alpha
                                                    $alpha++;
                                                    echo "<td width=25 align=center style='background-color:#e7323a'></td>";
                                                    echo "<td width=25 align=center style='background-color:#e7323a'></td>";
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
                                        <b><?= $res_user[$i]['statusAttendances']['LIBUR']; ?></b>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function printReport() {
        document.location.href = 'log-attendance?month=' + document.getElementById('month').value + '&year=' + document.getElementById('year').value;
    }
</script>


<?= $this->endSection(); ?>
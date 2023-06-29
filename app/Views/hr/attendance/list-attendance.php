<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>List Attendance</h1>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-12">
                    <form id="search_form" name="search_form" class="kt-form kt-form--fit kt-margin-b-20" method="POST">

                        <?php echo "Attendance " . date("F Y", strtotime($year . "-" . $month . "-01")); ?>
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
                                <span>Process</span>
                            </span>
                        </button>

                    </form>
                </div>
            </div>
            <div class="row">
                <!--begin: Datatable -->
                <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                <table>
                    <tr>
                        <td nowrap><img src='<?= base_url() ?>/assets/img/blue.png' width='25' height='25'>&nbsp;Hadir&nbsp;&nbsp;</td>
                        <td></td>
                        <td nowrap><img src='<?= base_url() ?>/assets/img/red.png' width='25' height='25'>&nbsp;Tidak Hadir</td>
                </table>
                <style>
                    th {
                        background-color: white;
                    }

                    th:first-child,
                    td:first-child {
                        position: sticky;
                        left: 0px;

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

                <div class="table-responsive">
                    <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                        <thead>
                            <tr>
                                <td height="25" style="vertical-align:middle;z-index:9999">&nbsp;User</th>
                                    <?php
                                    $last_date = date("t", strtotime($year . "-" . $month . "-01"));
                                    for ($i = 1; $i <= $last_date; $i++) {
                                        $temp = mktime(0, 0, 0, $month, $i, $year);
                                        $no = (strlen($i) == 1) ? ("0" . $i) : $i;

                                        if (date("N", $temp) == 6 || date("N", $temp) == 7) {
                                            echo "<td align=center  style=\"vertical-align:middle;\" width=\"25\" height=\"25\"><font color='red'>Masuk " . $i . "</font></th>";
                                            echo "<td align=center  style=\"vertical-align:middle;\" width=\"25\" height=\"25\"><font color='red'>Keluar " . $i . "</font></th>";
                                        } else {
                                            echo "<td align=center style=\"vertical-align:middle;\" width=\"25\" height=\"25\">Masuk " . $i . "</th>";
                                            echo "<td align=center style=\"vertical-align:middle;\" width=\"25\" height=\"25\">Keluar " . $i . "</th>";
                                        }
                                    ?>

                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        </thead>

                        <?php
                        for ($i = 0; $i < count($res_user); $i++) {
                        ?>
                            <tr>
                                <td style="vertical-align:middle;z-index:9999" nowrap>
                                    &nbsp;<?php echo $res_user[$i]["employeeName"]; ?></td>
                                <?php
                                for ($j = 1; $j <= $last_date; $j++) {
                                    $no = (strlen($j) == 1) ? ("0" . $j) : $j;
                                    $jam_masuk = "";
                                    $jam_keluar = "";
                                    $check = 0;
                                    for ($k = 0; $k < count($res_user[$i]["list_attendance"]); $k++) {

                                        if ($res_user[$i]["list_attendance"][$k]["periode"] == ($year . "-" . $month . "-" . $no)) {
                                            $jam_masuk = $res_user[$i]["list_attendance"][$k]["checkin"];
                                            $jam_keluar = $res_user[$i]["list_attendance"][$k]["checkout"];
                                            if ($res_user[$i]["list_attendance"][$k]["checkin"] != '')
                                                $check = 1;
                                            break;
                                        }
                                    }
                                    if ($check == 1) {
                                ?>
                                        <td width=25 align=center style="background-color:#304de2" style='vertical-align: middle;'>

                                            <font color="black"><?php echo $jam_masuk; ?></font>


                                        </td>
                                        <td width=25 align=center style="background-color:#304de2" style='vertical-align: middle;'>
                                            <font color="black"><?php echo $jam_keluar; ?></font>
                                        </td>

                                <?php
                                    } else {
                                        $temp = mktime(0, 0, 0, $month, $j, $year);
                                        if (date("N", $temp) == 7) {
                                            echo "<td width=25 align=center style=\"vertical-align:middle;\"><img src='assets/img/stop.png' width='25' height='25'></td>";
                                            echo "<td width=25 align=center style=\"vertical-align:middle;\"><img src='assets/img/stop.png' width='25' height='25'></td>";
                                        } else {
                                            echo "<td width=25 align=center style='background-color:#ff0000'></td>";
                                            echo "<td width=25 align=center style='background-color:#ff0000'></td>";
                                            //echo "<td bgcolor=\"red\" width=40>&nbsp;</td>";
                                        }
                                    }
                                }
                                ?>
                            </tr>

                        <?php

                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function printReport() {
        document.location.href = 'list-attendance?month=' + document.getElementById('month').value + '&year=' + document.getElementById('year').value;


    }
</script>


<?= $this->endSection(); ?>
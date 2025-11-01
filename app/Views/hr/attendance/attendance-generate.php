<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    th {
        background-color: white;
    }

    /* Row biasa lebih pendek */
    #attendanceTable,
    #attendanceTotalTable td {
        padding: 4px 8px;
        font-size: 13px;
        line-height: 1.2;
    }

    /* Header lebih tinggi & agak tebal */
    #attendanceTable thead th {
        padding: 8px 8px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.4;
        /* biar beda dikit */
    }

    #attendanceTable th,
    #attendanceTable td {
        padding: 4px 8px;
        /* lebih kecil dari default Bootstrap */
        font-size: 13px;
        /* biar lebih rapih */
        line-height: 1.2;
    }

    /* Kolom No */
    #attendanceTable th:nth-child(1),
    #attendanceTable td:nth-child(1) {
        position: sticky;
        left: 0;
        z-index: 3;
        min-width: 45px !important;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
    }

    /* Kolom NIP */
    #attendanceTable th:nth-child(2),
    #attendanceTable td:nth-child(2) {
        position: sticky;
        left: 50px;
        /* geser setelah kolom No */
        z-index: 3;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
        min-width: 120px !important;
    }

    /* Kolom Karyawan */
    #attendanceTable th:nth-child(3),
    #attendanceTable td:nth-child(3) {
        position: sticky;
        left: 170px;
        /* 50 (No) + 120 (NIP) */
        z-index: 3;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
        min-width: 200px !important;
        white-space: nowrap;
    }

    /* Kolom Dept */
    #attendanceTable th:nth-child(4),
    #attendanceTable td:nth-child(4) {
        position: sticky;
        left: 370px;
        /* 50 + 120 + 200 */
        z-index: 3;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
        min-width: 120px !important;
    }

    /* Kolom Bagian */
    #attendanceTable th:nth-child(5),
    #attendanceTable td:nth-child(5) {
        position: sticky;
        left: 490px;
        /* 50 + 120 + 200 + 120 */
        z-index: 3;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
        min-width: 150px !important;
    }

    #attendanceTable td:nth-child(n+6):nth-child(-n+70),
    #attendanceTable th:nth-child(n+6):nth-child(-n+70) {
        min-width: 100px !important;
        cursor: pointer;
        max-width: 100px !important;
        width: 100px !important;
    }

    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Data Absensi</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right mr-2" data-bs-toggle="modal" data-bs-target="#generateModal" href="#" onclick="resetFormGenerateLog()">
                <i class="fa-solid fa-clock-rotate-left"></i> Generate
            </a>
            <?php if (can('Personalia', 'Data Absensi', 'p')): ?>
                <button class="btn btn-warning btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-download"></i> Export
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <li><a class="dropdown-item" href="#" id="btnShowExportHarianModal">Lap. Harian</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportExcelBulan()">Lap. Bulanan</a></li>
                    <li><a class="dropdown-item" href="#" id="btnTriwulanModal">Lap. Triwulan</a></li>
                </ul>
            <?php endif; ?>
        </div>

    </div>
    <div class="card">
        <div class="card-body">

            <div class="row row-col-page-list-attendance mt-4">
                <form action="#" method="get">
                    <div class="row mb-3">
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-floating" style="height: 50px;">
                                    <input placeholder="" value="<?= date('Y-m') ?>" class="form-control month" id="month" name="month" />
                                    <label style="z-index: 1;" style="z-index: 1;">Pilih Bulan</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-floating">
                                <select class="form-select" name="divisi_id" id="divisi_id">
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
                        <div class="col-md-2">
                            <div class="form-floating" style="height: 50px;">
                                <select class="form-select bagian_id" name="bagian_id" id="bagian_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Cari Bagian </label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-floating">
                                <select class="form-select" name="tipe" id="tipe">
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
                        <div class="col-sm-4">
                            <div class="form-floating">
                                <select class="form-select" id="employee_id" name="employee_id">

                                </select>
                                <label for="floatingInput">Cari Karyawan</label>
                            </div>
                        </div>

                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-form-tts" id="attendanceTable">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-5">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="attendanceTotalTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th style="width: 100px;">Nip</th>
                                <th>Karyawan</th>
                                <th style="width: 100px;">Dept</th>
                                <th style="width: 100px;">Bagian</th>
                                <?php foreach ($statusPerizinanAll as $s): ?>
                                    <th width="20" align="center">
                                        <b><?= explode('_', $s['value'])[1] ?></b>
                                    </th>
                                <?php endforeach; ?>
                                <th width="20" align="center">
                                    <b>L</b>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="card-text mt-4">
                    <b class="">Keterangan</b>
                </div>

                <div class="row mt-3">
                    <?php foreach ($statusPerizinanAll as $s) : ?>
                        <div class="col-sm-2 col-4 mt-2">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="p-3" style="width: 5px; height:5px; background-color:<?= $s['description'] ?>"></div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="card-text mt-1">
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
                                <div class="card-text mt-1">
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

<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Generate Absensi Dari Log</h5>
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
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="form-floating" style="height: 50px;">
                                            <input placeholder="" value="<?= date('Y-m') ?>" class="form-control month_year_global" id="month_year_global" name="month_year_global" />
                                            <label style="z-index: 1;" style="z-index: 1;">Periode Absensi</label>
                                        </div>
                                        <div class="input-group-append" style="height:50px;">
                                            <button disabled class="btn btn-secondary" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="" autocomplete="one-time-code" name="start_date_global" id="start_date_global" type="text" required class="form-control target input-picker" placeholder="Tanggal Mulai Log Absen">
                                        <label for="floatingInput">Tanggal Mulai Log Absen</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="" autocomplete="one-time-code" name="finish_date_global" id="finish_date_global" type="text" required class="form-control target input-picker" placeholder="Tanggal Selesai Log Absen">
                                        <label for="floatingInput">Tanggal Selesai Log Absen</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
                                <button type="submit" class="btn btn-submit-form" id="globalGenerateBtn">Generate</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="single" role="tabpanel" aria-labelledby="profile-tab">
                        <form id="formGeneratePersonalAttendance">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="form-floating" style="height: 50px;">
                                            <input placeholder="" value="<?= date('Y-m') ?>" class="form-control month_year_personal" id="month_year_personal" name="month_year_personal" />
                                            <label style="z-index: 1;" style="z-index: 1;">Periode Absensi</label>
                                        </div>
                                        <div class="input-group-append" style="height:50px;">
                                            <button disabled class="btn btn-secondary" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="employee_id_filter" name="employee_id_filter">
                                        </select>
                                        <label for="floatingInput">Cari Karyawan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="" autocomplete="one-time-code" name="start_date_personal" id="start_date_personal" type="text" required class="form-control target input-picker startDate" placeholder="Tanggal Mulai Log Absen">
                                        <label for="floatingInput">Tanggal Mulai Log Absen</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="" autocomplete="one-time-code" name="finish_date_personal" id="finish_date_personal" type="text" required class="form-control target input-picker endDate" placeholder="Tanggal Selesai Log Absen">
                                        <label for="floatingInput">Tanggal Selesai Log Absen</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Update Absensi</h5>
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
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control" id="employeeName" disabled>
                        <label for="employeeName">Karyawan</label>
                    </div>
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" name="tanggal" class="form-control" id="tanggal" disabled>
                        <label for="tanggal">Tanggal</label>
                    </div>
                    <div class="input-group">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" name="jamKerjaName" class="form-control" id="jamKerjaName" disabled>
                            <label for="tanggal">Jam Kerja</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button class="btn btn-success jamKerjaDetail" id="jamKerjaDetail" type="button">
                                <i class="fas fa-calendar-week"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select name="statusKehadiran" class="form-select" id="statusKehadiran">
                                    <?php foreach ($statusPerizinanAll as $sk) : ?>
                                        <option value="<?= $sk['value']; ?>"><?= explode("_", $sk['value'])[0] . " (" . explode("_", $sk['value'])[1] . ")"; ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="status">Status Kehadiran</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-floating mb-3" style="height: 50px;" id="approvalForm">
                                <select name="isApproved" class="form-select" id="isApproved">
                                    <?php $statusApproval = ["APPROVED", "NOT APPROVED"]; ?>
                                    <?php foreach ($statusApproval as $sa) : ?>
                                        <option value="<?= $sa == "APPROVED" ? '1' : '0' ?>"><?= $sa; ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="floatingInput">Status Approval</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input placeholder="Nominal Uang Makan (Opsional)" type="text" name="nominal_uang_makan" class="form-control" id="nominal_uang_makan" oninput="this.value = greatFormatRupiah(this.value)">
                                <label for="status">Nominal Uang Makan (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input placeholder="Nominal Denda Keterlambatan (Opsional)" type="text" name="nominal_denda_keterlambatan" class="form-control" id="nominal_denda_keterlambatan" oninput="this.value = greatFormatRupiah(this.value)">
                                <label for="status">Nominal Denda Keterlambatan (Opsional)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm">
                            <div class="form-floating mb-3" style="height: 50px;" id="reasonForm">
                                <input type="text" name="reason" class="form-control" id="reason" placeholder="Reason">
                                <label for="floatingInput">Keterangan Tambahan (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <label class="form-label font-weight-bold modal-sub-title" style="font-size: 14px;">Abaikan Dari Sync Log Absensi</label>
                            <div class="form-control border-0 custom-toggle-switch" style="margin-top: -15px;">
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input" type="checkbox" name="abaikan_sync_log" id="abaikan_sync_log">
                                    <label class="form-check-label" for="abaikan_sync_log"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" name="keterangan" class="form-control" id="keterangan" disabled>
                        <label for="status">Keterangan Tambahan</label>
                    </div>

                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" name="jamTerlambat" class="form-control" id="jamTerlambat" disabled>
                        <label for="jamTerlambat">Jam Terlambat</label>
                    </div>

                    <div class="row mb-3" id="formInOut">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control" id="checkin" name="checkIn" maxlength="30" placeholder="CheckIn">
                                <label for="checkin">CheckIN</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control" id="checkout" name="checkOut" maxlength="30" placeholder="CheckOut">
                                <label for="checkout">CheckOut</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard btn-discard-update-absensi mr-3">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="updateAbsensi">Simpan</button>
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
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" id="jamKerjaNameDetail" class="form-control jamKerjaNameDetail" disabled>
                            <label for="checkin">Jenis Jam Kerja</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
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
                <button type="button" class="btn btn-hide-form btn-discard mr-3" id="btn-discard-2">Kembali</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="triwulanModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Triwulan Absensi</h5>
            </div>
            <form id="exportTriwulanForm" class="create-form" role="form" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-floating mb-2">
                                <select class="form-select" name="divisi_id_triwulan" id="divisi_id_triwulan">
                                    <?php foreach ($divisi as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Pilih Departemen</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="form-floating mb-2" style="height: 50px;">
                                    <input type="text" class="form-control" placeholder="Bulan Mulai" name="start_month" id="start_month">
                                    <label for="start_month">Bulan Mulai</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="form-floating mb-2" style="height: 50px;">
                                    <input type="text" placeholder="Bulan Selesai" class="form-control" name="end_month" id="end_month">
                                    <label for="end_month">Bulan Selesai</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" id="hideModalTriwulan">Kembali</button>
                    <button type="submit" class="btn btn-submit-form" id="btnExportTriwulan">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="lapHarianModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Laporan Harian Presensi</h5>
            </div>
            <form id="lapLogAbsensiForm" role="form" method="POST">
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-2" style="height: 50px;">
                                    <input type="text" id="start_date" name="start_date" class="form-control start_date" placeholder="Tanggal Mulai Log Absensi">
                                    <label for="start_date">Tanggal Mulai Absensi</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-2" style="height: 50px;">
                                    <input type="text" id="end_date" name="end_date" class="form-control end_date" placeholder="Tanggal Selesai Log Absensi">
                                    <label for="end_date">Tanggal Selesai Absensi</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" id="btnHideExportHarianModal">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnExportLapHarian">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    let csrfToken = '<?= csrf_token() ?>';
    let attendanceTable;

    // Inisiasi Kolom Dinamis
    refreshStructureColoumn();

    let attendanceTotalTable = $('#attendanceTotalTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: true,
        paging: true,
        autoWidth: true,
        pageLength: 25, // 🔹 default 25 baris per halaman
        ajax: {
            url: "<?= base_url('list-attendance/all-total') ?>",
            type: "POST",
            data: function(d) {
                d.month = $('#month').val();
                d.year = $('#year').val();
                d.divisi_id = $('#divisi_id').val();
                d.tipe = $('#tipe').val();
                d.employee_id = $('#employee_id').val();
                d.bagian_id = $('#bagian_id').val();
            },
            dataSrc: 'data'
        },
        columns: [{
                data: "no",
                className: "text-center",
            }, {
                data: "nip",
                className: "text-left"
            }, {
                data: "name",
                className: "text-left"
            }, {
                data: "divisi",
                className: "text-left",
            }, {
                data: "bagian",
                className: "text-left",
            }, {
                data: "total_hadir",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_cuti_tahunan",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_cuti_haid",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_cuti_hamil",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_cuti_melahirkan",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_ijin",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_sakit",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_rl",
                className: "text-left",
                sortable: false,
            },
            {
                data: "total_alpha",
                className: "text-left",
                sortable: false,
            },
            {
                data: "total_dinas",
                className: "text-left",
                sortable: false,
            },
            {
                data: "total_cuti_keguguran",
                className: "text-left",
                sortable: false,
            },
            {
                data: "total_libur",
                className: "text-left",
                sortable: false,
            },

        ],
    });

    function refreshStructureColoumn() {
        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
            $('#attendanceTable').DataTable().clear().destroy();
        }

        $.ajax({
            url: "<?= base_url('list-attendance/all') ?>",
            type: "POST",
            data: {
                month: $('#month').val(),
                year: $('#year').val(),
                divisi_id: $('#divisi_id').val(),
                tipe: $('#tipe').val(),
                employee_id: $('#employee_id').val()
            },
            success: function(json) {
                // buat header <th> sesuai response columns
                let thead = '<tr>';
                json.columns.forEach(col => thead += `<th>${col.title}</th>`);
                thead += '</tr>';
                $('#attendanceTable thead').html(thead);

                // init DataTable (sekali saja)
                attendanceTable = $('#attendanceTable').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    ordering: true,
                    paging: true,
                    autoWidth: true,
                    pageLength: 25, // 🔹 default 25 baris per halaman
                    ajax: {
                        url: "<?= base_url('list-attendance/all') ?>",
                        type: "POST",
                        data: function(d) {
                            d.month = $('#month').val();
                            d.year = $('#year').val();
                            d.divisi_id = $('#divisi_id').val();
                            d.tipe = $('#tipe').val();
                            d.employee_id = $('#employee_id').val();
                            d.bagian_id = $('#bagian_id').val();
                        },
                        dataSrc: 'data' // penting, biar DataTables ngerti
                    },
                    columnDefs: [{
                        targets: "_all",
                        render: function(data, type, row, meta) {
                            let colName = meta.settings.aoColumns[meta.col].data; // contoh: "day_1_in"
                            let colClass = row[colName + "_class"] || ""; // contoh: row.day_1_in_class

                            if (colClass.includes('bg-libur')) {
                                return `<img src="<?= base_url('assets/img/stop.png') ?>" 
                        width="16" height="16" alt="Stop">`;
                            }


                            if (colClass.includes('bg-alpha')) {
                                return renderCell(data, '#e7323a');
                            }

                            if (colClass.includes('bg-cuti-tahunan')) {
                                return renderCell(data, '#ffc107');
                            }

                            if (colClass.includes('bg-cuti-haid')) {
                                return renderCell(data, '#242120');
                            }

                            if (colClass.includes('bg-cuti-hamil')) {
                                return renderCell(data, '#C34A36');
                            }

                            if (colClass.includes('bg-cuti-melahirkan')) {
                                return renderCell(data, '#4B4453');
                            }

                            if (colClass.includes('bg-ijin')) {
                                return renderCell(data, '#17a2b8');
                            }

                            if (colClass.includes('bg-sakit')) {
                                return renderCell(data, '#28a745');
                            }

                            if (colClass.includes('bg-rl')) {
                                return renderCell(data, '#ff7b00');
                            }

                            if (colClass.includes('bg-hadir')) {
                                return renderCell(data, '#304de2');
                            }

                            if (colClass.includes('bg-dinas')) {
                                return renderCell(data, '#ad53a9');
                            }

                            if (colClass.includes('bg-cuti-keguguran')) {
                                return renderCell(data, '#75321a');
                            }

                            function renderCell(data, bgColor) {
                                return `<div style="
                                width:100%; 
                                height:100%; 
                                background-color:${bgColor}; 
                                color:#fff; 
                                display:flex; 
                                align-items:center; 
                                justify-content:center; 
                                font-weight:bold;
                            ">
                                ${data ?? ''}
                            </div>`;
                            }

                            return data ?? '';
                        }
                    }],

                    columns: json.columns,
                });
            }
        });
    }


    $('#attendanceTable tbody').on('click', 'td', function() {
        let cell = attendanceTable.cell(this);
        let colIndex = cell.index().column;

        // hanya mulai dari kolom ke-6
        if (colIndex >= 5) {
            let colName = attendanceTable.settings().init().columns[colIndex].data;
            // contoh: "day_12_in"
            let csrf = $(`[name="${csrfToken}"]`);

            // ambil tanggal dari field "_date"
            let rowData = attendanceTable.row(this.closest('tr')).data();

            // ganti _in/_out/_class jadi _date
            let baseName = colName.replace(/_(in|out|class)$/, '');
            let tanggal = rowData[baseName + '_date'];
            let employeeId = rowData.id;
            let formData = new FormData();
            formData.set('tanggal', tanggal);
            formData.set('employee_id', employeeId);

            $.ajax({
                url: "<?= base_url("list-attendance/get-attendance"); ?>",
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
                    if (response.status == false) {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        });
                        return;
                    } else {
                        var attendance = response.data.attendance;
                        var employee = response.data.employee;
                        var uangMakan = response.data.uangMakanHarian;
                        var dendaAbsenHarian = response.data.dendaAbsenHarian;

                        $('#reason').val(null);
                        $('#attendenceID').val(attendance.id);
                        $('#employeeName').val(employee.name);
                        $('#tanggal').val(response.data.tanggal);
                        $('#statusKehadiran').val(attendance.status).change();
                        $('#keterangan').val(response.data.keterangan);
                        $('#jamTerlambat').val(response.data.jamTerlambat);
                        $('#isApproved').val(attendance.isApproved).change();

                        if (attendance.status == 'HADIR_H') {
                            // hadir
                            $('#reasonForm').show();
                            $('#formInOut').show();
                            $('#approvalForm').show();
                            // set form
                            $('#checkout').val(attendance.checkout);
                            $('#checkin').val(attendance.checkin);
                            $('#reason').val(attendance.reason);
                        } else if (attendance.status == "ALPHA_A" || attendance.status == "LIBUR_L" || attendance.status == "RL_RL") {
                            $('#approvalForm').hide();
                        } else {
                            // ada perizinan
                            $('#reasonForm').show();
                            $('#formInOut').hide();
                            $('#approvalForm').show();
                            $('#reason').val(attendance.reason);
                        }

                        $('#jamKerjaName').val(response.data.jamKerja.jenis);
                        // Uang Makan Harian
                        if (uangMakan == null) {
                            $('#nominal_uang_makan').val(null).keyup();
                        } else {
                            $('#nominal_uang_makan').val(greatFormatRupiah(uangMakan.nominal));
                        }
                        // Denda Absen Harian
                        if (dendaAbsenHarian == null) {
                            $('#nominal_denda_keterlambatan').val(null).keyup();
                        } else {
                            $('#nominal_denda_keterlambatan').val(greatFormatRupiah(dendaAbsenHarian.nominal)).keyup();
                        }
                        // Abaikan sync log
                        var abaikan_sync_log = attendance.abaikan_sync_log;
                        if (abaikan_sync_log == "yes") {
                            // Ya
                            $('#abaikan_sync_log').attr('checked', true).change();
                        } else {
                            // Gak
                            $('#abaikan_sync_log').attr('checked', false).change();
                        }
                        // Assign Attr
                        $('#jamKerjaDetail').data('jam_kerja_id', response.data.jamKerja.id);
                        $('#jamKerjaDetail').data('jenis', response.data.jamKerja.jenis);
                        $('#jamKerjaDetail').data('jam_terlambat', response.data.jamKerja.jam_terlambat);

                        $('#updateModal').modal('show');
                    }

                }
            });

        }
    });

    $('.btn-discard-update-absensi').click(function(e) {
        e.preventDefault();
        $('#updateModal').modal('hide');
    });

    var validatorGlobal = $("#formGenerateGlobalAttendance").validate({
        rules: {
            month_year_global: {
                required: true
            },
            start_date_global: {
                required: true
            },
            finish_date_global: {
                required: true
            },
        },
        messages: {
            month_year_global: {
                required: "Pilih periode"
            },
            start_date_global: {
                required: "Tgl mulai wajib diisi"
            },
            finish_date_global: {
                required: "Tgl akhir wajib diisi"
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

    var validatorPersonal = $("#formGeneratePersonalAttendance").validate({
        rules: {
            month_year_personal: {
                required: true
            },
            employee_id_filter: {
                required: true
            },
            start_date_personal: {
                required: true
            },
            finish_date_personal: {
                required: true
            },
        },
        messages: {
            month_year_personal: {
                required: "Periode wajib diisi"
            },
            employee_id_filter: {
                required: "Karyawan wajib diisi"
            },
            start_date_personal: {
                required: "Tanggal mulai wajib diisi"
            },
            finish_date_personal: {
                required: "Tanggal selesai wajib diisi"
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


    var validatorUpdateAttendance = $("#updateAttendanceForm").validate({
        rules: {
            statusKehadiran: {
                required: true
            },
            isApproved: {
                required: true
            },
        },
        messages: {
            statusKehadiran: {
                required: "Pilih status kehadiran"
            },
            isApproved: {
                required: "Pilih status approval"
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

    var validatorExportTriwulan = $("#exportTriwulanForm").validate({
        rules: {
            divisi_id_triwulan: {
                required: true
            },
            start_month: {
                required: true
            },
            end_month: {
                required: true
            },
        },
        messages: {
            divisi_id_triwulan: {
                required: "Departemen wajib diisi"
            },
            start_month: {
                required: "Pilih bulan awal"
            },
            end_month: {
                required: "Pilih bulan akhir"
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


    $('#globalGenerateBtn').click(function(e) {
        e.preventDefault();
        if ($('#formGenerateGlobalAttendance').valid()) {
            var month_year_global = $('#month_year_global').val();
            var start_date_global = $('#start_date_global').val();
            var finish_date_global = $('#finish_date_global').val();

            const formData = new FormData();
            const csrf = $(`[name="${csrfToken}"]`);

            formData.set('month_year_global', month_year_global);
            formData.set('start_date_global', start_date_global);
            formData.set('finish_date_global', finish_date_global);

            $.ajax({
                url: "<?= base_url("list-attendance/generate-global"); ?>",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                data: formData,
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    if (response.status) {
                        Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            .then(() => {
                                $('#generateModal').modal('hide');
                                attendanceTable.ajax.reload(null, false);
                                attendanceTotalTable.ajax.reload(null, false);
                            })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            });
        }
    });

    $('#singleGenerateBtn').click(function(e) {
        e.preventDefault();
        if ($('#formGeneratePersonalAttendance').valid()) {
            var month_year_personal = $('#month_year_personal').val();
            var employee_id_filter = $('#employee_id_filter').val();
            var start_date_personal = $('#start_date_personal').val();
            var finish_date_personal = $('#finish_date_personal').val();

            const formData = new FormData();
            const csrf = $(`[name="${csrfToken}"]`);

            formData.set('month_year_personal', month_year_personal);
            formData.set('employee_id_filter', employee_id_filter);
            formData.set('start_date_personal', start_date_personal);
            formData.set('finish_date_personal', finish_date_personal);

            $.ajax({
                url: "<?= base_url("list-attendance/generate-personal"); ?>",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                data: formData,
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    if (response.status) {
                        Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            .then(() => {
                                $('#generateModal').modal('hide');
                                attendanceTable.ajax.reload(null, false);
                                attendanceTotalTable.ajax.reload(null, false);
                            })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            });
        }
    });

    $('#updateAbsensi').click(function(e) {
        e.preventDefault();
        if ($('#updateAttendanceForm').valid()) {
            // set variable
            const csrf = $(`[name="${csrfToken}"]`);
            var attendenceID = $('#attendenceID').val();
            var statusKehadiran = $('#statusKehadiran').val();
            var reason = $('#reason').val();
            var checkIn = $('#checkin').val();
            var checkOut = $('#checkout').val();
            var isApproved = $('#isApproved').val();
            var nominalUangMakan = destroyFormatRupiah($('#nominal_uang_makan').val());
            var nominalDendaKeterlambatan = destroyFormatRupiah($('#nominal_denda_keterlambatan').val());
            var abaikanSyncLog_form = $('#abaikan_sync_log').prop('checked');
            var abaikanSyncLog = abaikanSyncLog_form == true ? "yes" : "no";
            // append to form
            var formData = new FormData();
            formData.append('attendenceID', attendenceID);
            formData.append('statusKehadiran', statusKehadiran);
            formData.append("reason", reason);
            formData.append("checkIn", checkIn);
            formData.append("checkOut", checkOut);
            formData.append("isApproved", isApproved);
            formData.append("abaikan_sync_log", abaikanSyncLog);
            formData.set('nominal_uang_makan', nominalUangMakan);
            formData.set('nominal_denda_keterlambatan', nominalDendaKeterlambatan);

            $.ajax({
                url: "<?= base_url("list-attendance/update-attendance"); ?>",
                data: formData,
                method: "POST",
                dataType: "json",
                beforeSend: function(xhr) {
                    setLoading();
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                complete: function() {
                    stopLoading();
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
                            attendanceTable.ajax.reload(null, false);
                            attendanceTotalTable.ajax.reload(null, false);

                            $('#updateModal').modal('hide');
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Pilih bulan dahulu',
                            confirmButtonColor: '#4e73df',
                        });
                        return;
                    }
                },
            });
        }
    })

    // Search employee
    $("#employee_id").select2({
        placeholder: "Cari Karyawan",
        theme: "bootstrap-5",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "<?= base_url('list-attendance/like-employees') ?>",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    employeesName: params.term,
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

    $("#divisi_id").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    }).change(function(e) {
        e.preventDefault();
        let csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('divisionID', $(this).val());
        $.ajax({
            url: `<?= base_url("list-attendance/get-bagian"); ?>`,
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(result) {
                csrf.val(result.token);
                $("select[name='bagian_id']").empty()
                $("select[name='bagian_id']").append(`<option value=""></option>`)
                result.data.forEach(function(item) {
                    $("select[name='bagian_id']").append(`<option value="${item.id}">${item.kode_bagian.toUpperCase()} - ${item.nama_bagian.toUpperCase()}</option>`)
                });
            }
        });
        attendanceTable.ajax.reload(null, false);
        attendanceTotalTable.ajax.reload(null, false);
    });
    $('#bagian_id').select2({
        placeholder: "Cari Bagian",
        theme: "bootstrap-5",
        allowClear: true,
    }).change(function(e) {
        e.preventDefault();
        attendanceTable.ajax.reload(null, false);
        attendanceTotalTable.ajax.reload(null, false);
    });
    $('#divisi_id_triwulan').select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#exportTriwulanForm')
    });

    $("#statusKehadiran").select2({
        placeholder: "Pilih Status Kehadiran",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#updateModal')
    });

    $('#isApproved').select2({
        placeholder: "Pilih Status Approval",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#updateModal')
    });

    $("#start_date").datepicker({
        placeholder: "Pilih Tanggal Mulai Absensi",
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("#end_date").datepicker({
        placeholder: " Tanggal Selesai Absensi",
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#employee_id_filter').select2({
        placeholder: "Cari Karyawan",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#generateModal'),
        minimumInputLength: 2,
        ajax: {
            url: "<?= base_url('list-attendance/like-employees') ?>",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    employeesName: params.term,
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
    $("#tipe").select2({
        placeholder: "Cari Tipe/Golongan Pegawai",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $(".month,#month_year_personal,#month_year_global,#start_month,#end_month").datepicker({
        format: "yyyy-mm",
        startView: "months", // langsung tampilin bulan
        minViewMode: "months", // cuma bisa pilih bulan
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
    });

    $("#start_date_global, #finish_date_global,#finish_date_personal,#start_date_personal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#divisi_id,#tipe,#employee_id').change(function(e) {
        e.preventDefault();
        if (attendanceTable) {
            attendanceTable.ajax.reload(null, false);
            attendanceTotalTable.ajax.reload(null, false);
        }
    });

    $('#month').change(function(e) {
        e.preventDefault();
        refreshStructureColoumn();
    });

    $('#btnShowExportHarianModal').click(function(e) {
        e.preventDefault();
        $('#start_date,#end_date').val(null);
        $('#lapHarianModal').modal('show');
    });

    $('#btnHideExportHarianModal').click(function(e) {
        e.preventDefault();
        $('#lapHarianModal').modal('hide');
    });

    $('#btnTriwulanModal').click(function(e) {
        e.preventDefault();
        resetFormTriwulan();
        $('#triwulanModal').modal('show');
    });

    $('#hideModalTriwulan').click(function(e) {
        e.preventDefault();
        $('#triwulanModal').modal('hide');
    });

    $('#btnExportTriwulan').click(function(e) {
        e.preventDefault();
        if ($('#exportTriwulanForm').valid()) {
            var divisiId = $('#divisi_id_triwulan option:selected').val();
            var startMonth = $('#start_month').val();
            var endMonth = $('#end_month').val();
            var url = "<?= base_url('list-attendance/export-triwulan') ?>" + '?divisi_id=' + divisiId + '&start_month=' + startMonth + '&end_month=' + endMonth;
            window.open(url, '_blank');
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

    // on change status kehadiran
    $('#statusKehadiran').change(function(e) {
        e.preventDefault();
        if ($(this).val() == "HADIR_H") {
            // hadir
            $("input[name='checkIn']").attr('required', true);
            $("input[name='checkOut']").attr('required', true);
            // $('#reasonForm').hide();
            $('#approvalForm').show();
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

    $('#btnExportLapHarian').click(function(e) {
        e.preventDefault();
        if ($('#lapLogAbsensiForm').valid()) {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            var divisiId = $('#divisi_id').val();
            var tipe = $('#tipe').val();
            var bagianId = $('#bagian_id').val();

            var url = "<?= base_url('list-attendance/export-harian') ?>" + "?start_date=" + startDate + "&end_date=" + endDate + "&divisi_id=" + divisiId + "&tipe=" + tipe + "&bagian_id=" + bagianId;
            window.location.href = url;
        }
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

    // Detail jam kerja modal show
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

    });

    $('#btn-discard-2').click(function(e) {
        e.preventDefault();
        $('#detail2Modal').modal('hide');
    })

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


    function resetFormGenerateLog() {
        $('#start_date_global,#finish_date_global,#divisi_id_filter,#employee_id_filter,#start_date_personal,#finish_date_personal').val(null).change();
    }

    function resetFormTriwulan() {
        $('#divisi_id_triwulan,#start_month,#end_month').val(null).change();
    }

    function exportExcelBulan() {
        var month = $('#month').val();
        var divisiId = $('#divisi_id').val();
        var bagianId = $('#bagian_id').val();
        var tipe = $('#tipe').val();

        if (month == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih bulan dahulu',
                confirmButtonColor: '#4e73df',
            });
            return;
        }

        var url = "<?= base_url('list-attendance/export-bulanan') ?>?month=" + month + "&divisi_id=" + divisiId + "&tipe=" + tipe + "&bagian_id=" + bagianId;
        window.location.href = url;
    }
</script>

<?php if (session()->getFlashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: '<?= session()->getFlashdata('error') ?>',
            confirmButtonColor: '#4e73df',
        });
    </script>
<?php endif; ?>


<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name">Detail Payroll</h1>
        <div class="col-button-tambah-spp">
            <?php $splitMonthYear = explode("-", $payrollDetail['year_month']); ?>
            <a class="btn btn-warning btn-print float-right" target="_blank" href="<?= base_url("payroll/print/single/" . encrypt($payrollDetail['id'])); ?>">
                <i class="fa-solid fa-print"></i> Print
            </a>
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("payroll?year=" . $splitMonthYear[0] . "&month=" . $splitMonthYear[1]); ?>">
                Kembali
            </a>
        </div>
    </div>
    <?= csrf_field() ?>

    <div class="card">
        <div class="card-body">
            <label class="form-label font-weight-bold lable-title mt-2">
                Detail Karyawan
            </label>
            <div class="row mt-2">
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" class="form-control target input-picker" value="<?= date('M-Y', strtotime($payrollDetail['year_month']))  ?>">
                        <label for="floatingInput">Bulan / Periode</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" class="form-control target input-picker" value="<?= date('d/m/Y', strtotime($payrollDetail['start_date']))  ?>">
                        <label for="floatingInput">Mulai Absen</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" class="form-control target input-picker" value="<?= date('d/m/Y', strtotime($payrollDetail['end_date']))  ?>">
                        <label for="floatingInput">Selesai Absen</label>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" id="nip" class="form-control target input-picker" value="<?= $payrollDetail['nip'] ?>">
                        <label for="floatingInput">NIP</label>
                    </div>
                </div>
                <div class="col-sm-3 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" id="employeeName" class="form-control target" value="<?= $payrollDetail['employeeName'] ?>">
                        <label for="floatingInput">Nama Karyawan</label>
                    </div>
                </div>
                <div class="col-sm-3 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" id="divisi" class="form-control target input-picker" value="<?= $payrollDetail['divisi'] ?>">
                        <label for="floatingInput">Departemen</label>
                    </div>
                </div>
                <div class="col-sm-3 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" id="bagian" class="form-control target input-picker" value="<?= $payrollDetail['nama_bagian'] ?>">
                        <label for="floatingInput">Bagian</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Detail & Komponen Payroll
            </label>
            <!-- <div class="row mt-2">
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" id="hariKerja" class="form-control target input-picker" value="<?= $payrollDetail['hadir'] ?> Hari">
                        <label for="floatingInput">Hari Kerja (Total Masuk)</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" id="tambahanHariLiburTidakKerja" class="form-control target input-picker" value="0 Hari">
                        <label for="floatingInput">Tambahan Hari Libur Tidak Bekerja</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalHariLibur" id="totalHariLibur" class="form-control target input-picker" value="<?= $payrollDetail['libur'] ?? 0 ?> Hari">
                        <label for="floatingInput">Total Hari Libur (Termasuk Hari Besar)</label>
                    </div>
                </div>
            </div> -->
            <div class="row mt-2">
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalAlpha" id="totalAlpha" class="form-control target input-picker" value="<?= $payrollDetail['alpha'] ?? 0 ?> Kali">
                        <label for="floatingInput">Alpha</label>
                    </div>
                </div>
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalIzin" id="totalIzin" class="form-control target input-picker" value="<?= $payrollDetail['izin'] ?? 0 ?> Kali">
                        <label for="floatingInput">Izin</label>
                    </div>
                </div>
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalSakit" id="totalSakit" class="form-control target input-picker" value="<?= $payrollDetail['sakit'] ?? 0 ?> Kali">
                        <label for="floatingInput">Sakit</label>
                    </div>
                </div>
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalCutiTahunan" id="totalCutiTahunan" class="form-control target input-picker" value="<?= $payrollDetail['cuti_tahunan'] ?? 0 ?> Kali">
                        <label for="floatingInput">Cuti Tahunan</label>
                    </div>
                </div>
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalCutiHaid" id="totalCutiHaid" class="form-control target input-picker" value="<?= $payrollDetail['cuti_haid'] ?? 0 ?> Kali">
                        <label for="floatingInput">Cuti Haid</label>
                    </div>
                </div>
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalCutiHamil" id="totalCutiHamil" class="form-control target input-picker" value="<?= $payrollDetail['cuti_hamil'] ?? 0 ?> Kali">
                        <label for="floatingInput">Cuti Hamil</label>
                    </div>
                </div>
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalCutiMelahirkan" id="totalCutiMelahirkan" class="form-control target input-picker" value="<?= $payrollDetail['cuti_melahirkan'] ?? 0 ?> Kali">
                        <label for="floatingInput">Cuti Melahirkan</label>
                    </div>
                </div>
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalRL" id="totalRL" class="form-control target input-picker" value="<?= $payrollDetail['rl'] ?? 0 ?> Kali">
                        <label for="floatingInput">RL</label>
                    </div>
                </div>
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalLibur" id="totalLibur" class="form-control target input-picker" value="<?= $payrollDetail['libur'] ?? 0 ?> Kali">
                        <label for="floatingInput">Libur</label>
                    </div>
                </div>
                <div class="col-sm-2 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalHadir" id="totalHadir" class="form-control target input-picker" value="<?= $payrollDetail['hadir'] ?? 0 ?> Kali">
                        <label for="floatingInput">Hadir</label>
                    </div>
                </div>

                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" required name="totalUangLembur" id="totalUangLembur" class="form-control target input-picker" value="<?= "Rp " . number_format($payrollDetail['nominal_uang_lembur'],  2, ',', '.') ?>">
                        <label for="floatingInput">Total Uang Lembur Dalam Sebulan</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);" type="text" id="gajiPerHari" class="form-control target input-picker" value="<?= "Rp " . number_format($payrollDetail['nominal_gaji_harian'], 2, ',', '.') ?>">
                        <label for="floatingInput">Gaji (Per Hari)</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);" type="text" id="cadanganPerHari" class="form-control target input-picker" value="<?= "Rp " . number_format($payrollDetail['nominal_cadangan'], 2, ',', '.') ?>">
                        <label for="floatingInput">Cadangan (Per Hari)</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" id="hariKerja" class="form-control target input-picker" value="<?= $payrollDetail['hadir_final'] ?> Hari">
                        <label for="floatingInput">Hari Kerja (Total Masuk + Perizinan Approved)</label>
                    </div>
                </div>
            </div>
            <div class="tabelKomponen">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= (@$_GET['location'] == "nilaiKomponenGaji" || empty(@$_GET['location'])) ? 'active' : '' ?>" id="home-tab" data-toggle="tab" data-target="#perhitunganGaji" type="button" role="tab" aria-controls="home" aria-selected="true">Nilai Komponen Gaji</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= (@$_GET['location'] == "rekapKeterlambatanPresensi") ? 'active' : '' ?>" id="profile-tab" data-toggle="tab" data-target="#keterlambatanPresensi" type="button" role="tab" aria-selected="false">Rekap Keterlambatan Presensi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-toggle="tab" data-target="#rekapLembur" type="button" role="tab" aria-selected="false">Rekap Lembur</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= (@$_GET['location'] == "rekapPerizinanTidakDisetujui") ? 'active' : '' ?>" id="contact-tab" data-toggle="tab" data-target="#perizinanNotApproved" type="button" role="tab" aria-selected="false">Rekap Perizinan Tidak Disetujui</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= (@$_GET['location'] == "pinjamanKaryawan") ? 'active' : '' ?>" id="contact-tab" data-toggle="tab" data-target="#pinjamanKaryawan" type="button" role="tab" aria-selected="false">Pinjaman Karyawan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= (@$_GET['location'] == "rekapGajiHarian") ? 'active' : '' ?>" id="contact-tab" data-toggle="tab" data-target="#rekapGajiHarian" type="button" role="tab" aria-selected="false">Rekap Gaji Harian</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade <?= (@$_GET['location'] == "nilaiKomponenGaji" || empty(@$_GET['location'])) ? 'show active' : '' ?> " id="perhitunganGaji" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table nowrap table-hover-tobasurimi dataTable" id="perhitunganGajiTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 10px;" class="sort">No</th>
                                        <th class="sort">Komponen Gaji</th>
                                        <th class="sort">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table" id="body-table">
                                    <?php $no = 1; ?>
                                    <?php foreach ($perhitunganGaji as  $p) : ?>
                                        <tr class="perhitunganGajis" data-id="<?= $p['id'] ?>" data-komponen_gaji="<?= $p['name'] ?>" data-nominal="<?= $p['nominal'] ?>" data-tipe="<?= $p['tipe'] == "PLUS" ? "+" : "-"  ?>">
                                            <td><?= $no++; ?></td>
                                            <td><b> <?= $p['name'] ?></b></td>
                                            <td style="color: <?= $p['tipe'] == "PLUS" ? "green" : "red"  ?>; font-weight:bold;"><b> <?= $p['tipe'] == "PLUS" ? "(+)" : "(-)"  ?> <?= "Rp " . number_format($p['nominal'],  2, ',', '.') ?></b></td>
                                        </tr>
                                    <?php endforeach ?>
                                    <tr class="bg-secondary">
                                        <td colspan="2" align="right"><b>Tunjangan Diterima</b></td>
                                        <td><b><?= "Rp " . number_format($totalPerhitunganGaji,  2, ',', '.') ?></b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade <?= (@$_GET['location'] == "rekapKeterlambatanPresensi") ? 'show active' : '' ?>" id="keterlambatanPresensi" role="tabpanel">
                        <table class="table nowrap table-hover-tobasurimi dataTable" id="keterlambatanPresensiTabel" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;" class="sort">No</th>
                                    <th class="sort">Tanggal</th>
                                    <th class="sort">Waktu Masuk</th>
                                    <th class="sort">Total Jam Keterlambatan</th>
                                    <th class="sort">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table" style="cursor: pointer;">
                                <?php $no = 1; ?>
                                <?php if (count($rekapKeterlambatanPresensi) == 0) : ?>
                                    <td colspan="5" class="text-center">Karyawan tidak pernah datang terlambat </td>
                                <?php else : ?>
                                    <?php foreach ($rekapKeterlambatanPresensi as  $r) : ?>
                                        <tr class="rekapKeterlambatanPresensiTable" data-id="<?= $r['id'] ?>" data-tanggal="<?= date('d/m/Y', strtotime($r['periode'])) ?>" data-checkin="<?= $r['checkin'] ?>" data-total_jam_keterlambatan="<?= $r['total_jam_keterlambatan'] ?>" data-nominal="<?= $r['nominal_pengurangan'] ?>">
                                            <td><?= $no++; ?></td>
                                            <td><?= date('d/m/Y', strtotime($r['periode'])) ?></td>
                                            <td><?= $r['checkin'] ?></td>
                                            <td><?= $r['total_jam_keterlambatan'] ?></td>
                                            <td class="text-danger" style="font-weight:bold;"><b>(-) <?= "Rp " . number_format($r['nominal_pengurangan'],  2, ',', '.') ?></b></td>
                                        </tr>
                                    <?php endforeach ?>
                                    <tr class="bg-secondary">
                                        <td colspan="4" align="right"><b>Denda Keterlambatan Presensi</b></td>
                                        <td><b class="text-danger"> (-) <?= "Rp " . number_format($totalNominalKeterlambatanPresensi,  2, ',', '.') ?></b></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="rekapLembur" role="tabpanel">
                        <table class="table nowrap table-hover-tobasurimi dataTable" id="rekapLemburTabel" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;" class="sort">No</th>
                                    <th class="sort">Tanggal Lembur</th>
                                    <th class="sort">Total Jam Lembur</th>
                                    <th class="sort">Jam Mulai Lembur</th>
                                    <th class="sort">Jam Selesai Lembur</th>
                                    <th class="sort">Uang Lembur</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table">
                                <?php $no = 1; ?>
                                <?php if (count($rekapLembur) == 0) : ?>
                                    <td colspan="6" class="text-center">Karyawan tidak pernah lembur </td>
                                <?php else : ?>
                                    <?php foreach ($rekapLembur as  $r) : ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= date('d/m/Y', strtotime($r['periode'])) ?></td>
                                            <td><?= $r['total_jam_lembur'] ?> Jam</td>
                                            <td><?= $r['jam_mulai_lembur'] ?></td>
                                            <td><?= $r['jam_selesai_lembur'] ?></td>
                                            <td style="font-weight:bold;" class="text-success"><b>(+) <?= "Rp " . number_format($r['total_uang_lembur'],  2, ',', '.') ?></b></td>
                                        </tr>
                                    <?php endforeach ?>
                                    <tr class="bg-secondary">
                                        <td colspan="5" align="right"><b>Total Uang Lembur Selama Sebulan</b></td>
                                        <td><b class="text-success">(+) <?= "Rp " . number_format($payrollDetail['nominal_uang_lembur'],  2, ',', '.') ?></b></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade <?= (@$_GET['location'] == "rekapPerizinanTidakDisetujui") ? 'show active' : '' ?>" id="perizinanNotApproved" role="tabpanel">
                        <table class="table nowrap table-hover-tobasurimi dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;" class="sort">No</th>
                                    <th class="sort">Jenis Perizinan</th>
                                    <th class="sort">Tanggal</th>
                                    <th class="sort">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table">
                                <?php $no = 1; ?>
                                <?php if (count($rekapPerizinanNotApproved) == 0) : ?>
                                    <td colspan=" 4" class="text-center">Tidak ada perizinan yang tidak disetujui</td>
                                <?php else : ?>
                                    <?php foreach ($rekapPerizinanNotApproved as  $r) : ?>
                                        <tr class="rekapPerizinanTidakDisetujuiTabel" data-id="<?= $r['id'] ?>" data-tanggal="<?= date('d/m/Y', strtotime($r['periode'])) ?>" data-jenis="<?= $r['status'] ?>" data-nominal="<?= $r['nominal_pengurangan'] ?>">
                                            <td><?= $no++; ?></td>
                                            <td><?= explode("_", $r['status'])[0] ?></td>
                                            <td><?= date('d/m/Y', strtotime($r['periode'])) ?></td>
                                            <td style="font-weight:bold;" class="text-danger"><b>(-) <?= "Rp " . number_format($r['nominal_pengurangan'],  2, ',', '.') ?></b></td>
                                        </tr>
                                    <?php endforeach ?>
                                    <tr class="bg-secondary">
                                        <td colspan="3" align="right"><b>Pengurangan Gaji Harian</b></td>
                                        <td><b class="text-danger">(-) <?= "Rp " . number_format($totalNominalRekapPerizinanNotApproved,  2, ',', '.') ?></b></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade <?= (@$_GET['location'] == "pinjamanKaryawan") ? 'show active' : '' ?>" id="pinjamanKaryawan" role="tabpanel">
                        <table class="table nowrap table-hover-tobasurimi dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px; text-align:center;" class="sort">No</th>
                                    <th style="text-align: center;" class="sort">Tanggal Ambil Pinjaman</th>
                                    <th class="sort">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table">
                                <?php $no = 1;
                                $totalPinjaman = 0; ?>
                                <?php if ($rekapPinjaman == null) : ?>
                                    <td colspan="4" class="text-center">Karyawan tidak pernah mengambil pinjaman bulanan</td>
                                <?php else : ?>
                                    <?php $totalPinjaman = $rekapPinjaman['nominal']; ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td style="text-align: center;"><?= date('d/m/Y', strtotime($rekapPinjaman['updatedAt'])) ?></td>
                                        <td style="font-weight:bold;" class="text-danger"><b>(-) <?= "Rp " . number_format($rekapPinjaman['nominal'],  2, ',', '.') ?></b></td>
                                    </tr>
                                    <tr class="bg-secondary">
                                        <td colspan="2" align="right"><b>Total Pinjaman Karyawan</b></td>
                                        <td><b class="text-danger">(-) <?= "Rp " . number_format($totalPinjaman,  2, ',', '.') ?></b></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade <?= (@$_GET['location'] == "rekapGajiHarian") ? 'show active' : '' ?>" id="rekapGajiHarian" role="tabpanel">
                        <table class="table nowrap table-hover-tobasurimi dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px; text-align:center;" class="sort">No</th>
                                    <th style="text-align: center;" class="sort">Tanggal</th>
                                    <th class="sort">Jam Kerja</th>
                                    <th class="sort">CheckIn</th>
                                    <th class="sort">Mulai Istirahat</th>
                                    <th class="sort">Selesai Istirahat</th>
                                    <th class="sort">CheckOut</th>
                                    <th class="sort">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Rincan Perhitungan Final
            </label>
            <table class="table mt-3 p-3" width="100%" cellspacing="0">
                <tbody class="body-table" style="color:black;">
                    <tr>
                        <td style="width: 10px;">1</td>
                        <td>Total Gaji & Lembur</td>
                        <td>=</td>
                        <td align="right"><b id="gajiLemburTabel"><?= "Rp " . number_format($payrollDetail['nominal_uang_gaji'] + $payrollDetail['nominal_uang_lembur'], 2, ',', '.') ?></b></td>
                    </tr>
                    <tr>
                        <td style="width: 10px;">2</td>
                        <td>Penambahan Gaji</td>
                        <td>=</td>
                        <td align="right"><b id="penambahanGajiTabel" class="text-success">(+) <?= "Rp " . number_format($payrollDetail['nominal_penambahan_gaji'], 2, ',', '.') ?></b></td>
                    </tr>
                    <tr>
                        <td style="width: 10px;">3</td>
                        <td>Pengurangan Gaji</td>
                        <td>=</td>
                        <td align="right"><b id="penguranganGajiTabel" class="text-danger">(-) <?= "Rp " . number_format($payrollDetail['nominal_pengurangan_gaji'], 2, ',', '.') ?></b></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="right"><b>Total Uang Diterima</b></td>
                        <td align="right"><b id="gajiDiterimaTabel"><?= "Rp " . number_format($payrollDetail['nominal_gaji_diterima'], 2, ',', '.') ?></b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    </div>
</section>

<div class="modal fade" id="perhitunganGajiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name">Update Nominal Komponen Gaji</label></h5>
            </div>
            <form id="formUpdateKomponenGaji" role="form" method="POST">
                <input type="hidden" name="komponenGajiID">
                <input type="hidden" value="<?= $payrollDetail['id'] ?>" name="payrollID">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" id="namaKomponenGaji" class="form-control target input-picker" value="">
                                <label for="floatingInput">Nama Komponen Gaji</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input name="nominal" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);" autocomplete="one-time-code" type="text" id="nominalKomponenGaji" class="form-control target input-picker" value="">
                                <label for="nominalKomponenGaji" id="nominalGajiModal">Nominal</label>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-submit-form" id="submitFormUpdateKomponenGaji">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rekapKeterlambatanPresensiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name">Update Nominal Denda Keterlambatan Presensi</label></h5>
            </div>
            <form id="formUpdateKeterlambatanPresensi" role="form" method="POST">
                <input type="hidden" name="rekapKeterlambatanPresensiID">
                <input type="hidden" value="<?= $payrollDetail['id'] ?>" name="payrollID">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" id="tanggalKeterlambatan" class="form-control target input-picker" value="">
                                <label for="floatingInput">Tanggal Keterlambatan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" id="waktuMasuk" class="form-control target input-picker" value="">
                                <label for="floatingInput">Waktu Masuk</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" id="totalJamKeterlambatan" class="form-control target input-picker" value="">
                                <label for="floatingInput">Total Jam Keterlambatan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input name="nominal" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);" autocomplete="one-time-code" type="text" id="nominalDendaKeterlambatan" class="form-control target input-picker" value="">
                                <label for="nominalDendaKeterlambatan" id="nominalDendaKeterlambatan">Nominal</label>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-submit-form" id="submitFormKeterlambatanPresensi">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    $('.perhitunganGaji').click(function() {
        var id = $(this).data('id');
        var komponenGaji = $(this).data('komponen_gaji');
        var nominal = $(this).data('nominal');
        var tipe = $(this).data('tipe');

        $('#nominalGajiModal').text("Nominal (" + tipe + ")");
        $('#namaKomponenGaji').val(komponenGaji);
        $('#nominalKomponenGaji').val(formatRupiah(nominal));
        $("input[name='komponenGajiID']").val(id);

        $('#perhitunganGajiModal').modal('show');
    });
    var validatorNilaiKomponenGaji = $("#formUpdateKomponenGaji").validate({
        rules: {
            nominal: {
                required: true
            }
        },
        messages: {
            nominal: {
                required: "Nominal wajib diisi"
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
    $('#submitFormUpdateKomponenGaji').click(function(e) {
        e.preventDefault();
        if ($("#formUpdateKomponenGaji").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let data = new FormData(document.querySelector("#formUpdateKomponenGaji"));
                    $.ajax({
                        url: "<?= base_url("payroll/update/nominal-komponen-gaji"); ?>",
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                location.replace(`<?= base_url("payroll/id"); ?>/${response.id}?location=${response.location}`);
                            });
                        },
                        onError: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            });
                        }
                    });

                }
            })
        }
    });
</script>

<script>
    $('.rekapKeterlambatanPresensiTable').click(function() {
        var id = $(this).data('id');
        var tanggal = $(this).data('tanggal');
        var checkin = $(this).data('checkin');
        var total_jam_keterlambatan = $(this).data('total_jam_keterlambatan');
        var nominal = $(this).data('nominal');

        $("input[name='rekapKeterlambatanPresensiID']").val(id);
        $('#tanggalKeterlambatan').val(tanggal);
        $('#waktuMasuk').val(checkin);
        $('#totalJamKeterlambatan').val(total_jam_keterlambatan);
        $('#nominalDendaKeterlambatan').val(formatRupiah(nominal));

        $('#rekapKeterlambatanPresensiModal').modal('show');
    });
    var validatorRekapKeterlambatanPresensi = $("#formUpdateKeterlambatanPresensi").validate({
        rules: {
            nominal: {
                required: true
            }
        },
        messages: {
            nominal: {
                required: "Nominal wajib diisi"
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
    $('#submitFormKeterlambatanPresensi').click(function(e) {
        e.preventDefault();
        if ($("#formUpdateKeterlambatanPresensi").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let data = new FormData(document.querySelector("#formUpdateKeterlambatanPresensi"));
                    $.ajax({
                        url: "<?= base_url("payroll/update/nominal-keterlambatan-presensi"); ?>",
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                location.replace(`<?= base_url("payroll/id"); ?>/${response.id}?location=${response.location}`);
                            });
                        },
                        onError: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            });
                        }
                    });

                }
            })
        }
    });
</script>

<script>
    $("#gajiPerHari, #cadanganPerHari").change(function() {
        const csrf = $(`[name="${csrfToken}"]`);

        var gajiPerHari = $("#gajiPerHari").val() || 0;
        var cadanganPerHari = $("#cadanganPerHari").val() || 0;

        var formData = new FormData();
        formData.append('nominalGajiPerHari', gajiPerHari);
        formData.append('nominalCadangan', cadanganPerHari);
        formData.append("payrollID", "<?= $payrollDetail['id'] ?>");
        formData.append("gajiPerHariID", "<?= $payrollDetail['nominal_gaji_harian'] ?>");
        formData.append("cadanganID", "<?= $payrollDetail['nominal_cadangan'] ?>");

        $.ajax({
            url: "<?= base_url("payroll/update/nominal-gaji-cadangan"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);
                location.reload();
            },
            onError: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan pada sistem',
                    confirmButtonColor: '#4e73df',
                });
            }
        });
    });
</script>

<script>
    function formatRupiah(angka) {
        if (angka === null) {
            angka = 0;
        }

        angka = angka.toString();
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return 'Rp. ' + ribuanFormatted + ',' + desimal;
    }
</script>
<?= $this->endSection(); ?>
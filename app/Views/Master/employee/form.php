<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 id="title"><?= $title ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("employee"); ?>">
                Kembali
            </a>
            <?php if (!empty($data)) : ?>
                <?php if (can('Personalia', 'Karyawan', 'd')) : ?>
                    <button class="btn btn-hapus delete-parent float-right root-form-view">
                        Hapus
                    </button>
                <?php endif; ?>
                <?php if (can('Personalia', 'Karyawan', 'u')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <form id="create-form" class="create-form">
            <div class="card-body">
                <input type="hidden" id="id" class="id" value="<?= !empty($data) ? encrypt($data['id']) : '' ?>" name="id">
                <div class="row">
                    <div class="col-sm-4 mt-3">
                        <?= csrf_field() ?>
                        <div class="text-center">
                            <img class="preview-photo" width="130" id="preview_photo" src="<?= empty($data) ? "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAACCCAMAAAC93eDPAAAAMFBMVEXk5ueutLfn6eqyt7qrsbTh4+TDx8q2u77Z3N3Jzc/U19nO0dPq7Oy8wcSnrrHd4OEuWFw9AAADAUlEQVR4nO2a23LjIAxAjQAbsIH//9vFTjpNUhckRyI7u5ynTF84lSVuYpoGg8FgMBgMBoPB4H8DYNs2gE+NvkEOfo4xzj7YD2jAFKIy3+jZ9bUA67Ux6hFjlnXqJwH+Zfy7hQ5bJwF3KrCTou0SCP+bwBEIJ+8Ac/rdYJcI0g6wVGJw+xirrAPElkGJg6gDzG0D2W8BK8agOIgZTBlnoNQiFQZYkAZiKQmhXo5PWBGFSeMNzCwRBmwu3h0kwoDPhEPBC4TBUYJQ4F80cbPSN4l/vdpoMZBISEuoyIOF24BWDwfsNTFTDZLjVohkBfZJmjA13uDPR7KCYlcgG6jIrUAuiH9SgWzAr/D5iqAt1YcC+3JNn5q4t/JQO0meK2ReA9re9UAzGxSoCpHdYCPmo8TJkrhhSOwCZdtECwL7xDRR968m8BsQDrU7MgdbShgEtvAHFq0gkgk7+F10EjpY73MDzkHyxgtXmCYKXsIC5mxr2M9RZActfAkMoeFgBFbIVwenahJi5fjkYCt1IXK5csLm07lEUrlXUwTKNu7nDiZ164jcJCa/mIdYGKNi6NgXukvkNepbh0wtc8gfaRXCBpPN2drjV+/BYf+fc3YuFJxz2d7/1mf0MnbwcdFafTUqjzlxieVr2EncA8D5qFM6bxKmpEtWCFrAZp/L4JziIdO5BcheN4f/LlF2C4C9Q40b/8tiWRlXTID1tUONslAz02QF00oLwINEmjkiAUFfFLhFwr8bCciI3miVsni95bCdvxIgSsTrgYCM3LS3uNy+hXA1DX86XLt928i3SzWHK8fc1jsFqoOmJ8S7lfAT4r4S806BiKEdcQQMFO0aEDxrHnxBOGw2D22XHdBHrSwjoPCXkdiLjCvgbiPpF94UFtSBS9IA1SKgduipIKqCdMF5gXZGSgehTFDNLyEsUMLQaqdLzUoPCo29g8zi8OJQr0uQN2g9bMgi69Mz9SuxC89FLlBtnMmX5E5939AhGxvPISHqDtTf+kAXqh9iMBgMBn8LfwAfLCKVi1nppAAAAABJRU5ErkJggg==" : ($data['employee_img'] == null ? 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAACCCAMAAAC93eDPAAAAMFBMVEXk5ueutLfn6eqyt7qrsbTh4+TDx8q2u77Z3N3Jzc/U19nO0dPq7Oy8wcSnrrHd4OEuWFw9AAADAUlEQVR4nO2a23LjIAxAjQAbsIH//9vFTjpNUhckRyI7u5ynTF84lSVuYpoGg8FgMBgMBoPB4H8DYNs2gE+NvkEOfo4xzj7YD2jAFKIy3+jZ9bUA67Ux6hFjlnXqJwH+Zfy7hQ5bJwF3KrCTou0SCP+bwBEIJ+8Ac/rdYJcI0g6wVGJw+xirrAPElkGJg6gDzG0D2W8BK8agOIgZTBlnoNQiFQZYkAZiKQmhXo5PWBGFSeMNzCwRBmwu3h0kwoDPhEPBC4TBUYJQ4F80cbPSN4l/vdpoMZBISEuoyIOF24BWDwfsNTFTDZLjVohkBfZJmjA13uDPR7KCYlcgG6jIrUAuiH9SgWzAr/D5iqAt1YcC+3JNn5q4t/JQO0meK2ReA9re9UAzGxSoCpHdYCPmo8TJkrhhSOwCZdtECwL7xDRR968m8BsQDrU7MgdbShgEtvAHFq0gkgk7+F10EjpY73MDzkHyxgtXmCYKXsIC5mxr2M9RZActfAkMoeFgBFbIVwenahJi5fjkYCt1IXK5csLm07lEUrlXUwTKNu7nDiZ164jcJCa/mIdYGKNi6NgXukvkNepbh0wtc8gfaRXCBpPN2drjV+/BYf+fc3YuFJxz2d7/1mf0MnbwcdFafTUqjzlxieVr2EncA8D5qFM6bxKmpEtWCFrAZp/L4JziIdO5BcheN4f/LlF2C4C9Q40b/8tiWRlXTID1tUONslAz02QF00oLwINEmjkiAUFfFLhFwr8bCciI3miVsni95bCdvxIgSsTrgYCM3LS3uNy+hXA1DX86XLt928i3SzWHK8fc1jsFqoOmJ8S7lfAT4r4S806BiKEdcQQMFO0aEDxrHnxBOGw2D22XHdBHrSwjoPCXkdiLjCvgbiPpF94UFtSBS9IA1SKgduipIKqCdMF5gXZGSgehTFDNLyEsUMLQaqdLzUoPCo29g8zi8OJQr0uQN2g9bMgi69Mz9SuxC89FLlBtnMmX5E5939AhGxvPISHqDtTf+kAXqh9iMBgMBn8LfwAfLCKVi1nppAAAAABJRU5ErkJggg==' : $data['employee_img']) ?>" />
                        </div>
                        <div class="form-floating mt-3" style="height: 50px;">
                            <input autocomplete="one-time-code" onchange="previewPhoto();" type="file" class="form-control input-image employeeImg" id="employeeImg" name="employeeImg" accept="image/png, image/jpg, image/jpeg">
                        </div>
                        <?php if (!empty($data)) : ?>
                            <label class="form-label font-weight-bold lable-title mb-3 mt-4">
                                ID Finger : <?= !empty($data) ? $data['id'] : '' ?>
                            </label> <br>
                            <label class="form-label font-weight-bold lable-title">
                                Riwayat Payroll
                            </label>
                            <div class="row">
                                <div class="col-sm-12 mt-3">
                                    <div class="input-group">
                                        <div class="form-floating" style="height: 50px;">
                                            <input placeholder="" value="<?= date('Y') ?>" class="form-control year" id="year" name="year" />
                                            <label style="z-index: 1;" style="z-index: 1;">Pilih Tahun</label>
                                        </div>
                                        <div class="input-group-append" style="height:50px;">
                                            <button disabled class="btn btn-secondary" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mt-2 mb-3">
                                <table class="table table-borderd nowrap table-hover-tobasurimi" width="100%" cellspacing="0" id="tabel-riwayat-payroll">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="10" style="text-align: center;">No</th>
                                            <th onclick="changeSort('payrolls.year_month')">Periode</th>
                                            <th onclick="changeSort('payrolls.nominal_gaji_diterima')">Gaji Diterima (THP)</th>
                                            <th>Slip</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-detail-table" id="body-detail-table">
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-8 mt-3">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Identitas Karyawan
                        </label>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Full Name" value="<?= !empty($data) ? $data['name'] : '' ?>">
                            <label for="floatingInput">Nama Lengkap </label>
                        </div>
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" name="join_date" type="text" placeholder="" class="form-control join_date" id="join_date" value="<?= !empty($data) ? date('d/m/Y', strtotime($data['join_date'])) : date('d/m/Y', strtotime(date('Y-m-d'))) ?>">
                                <label>Tanggal Bergabung</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($data) ? $data['nip'] : '' ?>" autocomplete="one-time-code" type="text" minlength="6" maxlength="18" class="form-control nip" id="nip" name="nip" placeholder="NIP" maxlength="30">
                            <label for="floatingInput">NIP </label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select gender" name="gender" id="gender" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= !empty($data) ? ($data['gender'] == "Pria" ? 'selected' : '') : '' ?> value="Pria">PRIA</option>
                                <option <?= !empty($data) ? ($data['gender'] == "Wanita" ? 'selected' : '') : '' ?> value="Wanita">WANITA</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Jenis Kelamin </label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select id="jabatan_id" class="form-select jabatan_id" name="jabatan_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($jabatan as $j) : ?>
                                    <option <?= !empty($data) ? ($data['jabatan_id'] == $j['id'] ? 'selected' : '') : '' ?> value="<?= encrypt($j['id']) ?>"><?= strtoupper($j['jabatan_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Jabatan </label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select division_id" name="division_id" id="division_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($data) ? ($data['division_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= encrypt($d['id']) ?>"><?= strtoupper($d['divisi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen </label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bagian_id" name="bagian_id" id="bagian_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($bagianList)) : ?>
                                    <?php foreach ($bagianList as $b) : ?>
                                        <option <?= !empty($data) ? ($data['bagian_id'] == $b['id'] ? 'selected' : '') : '' ?> value="<?= $b['id'] ?>"><?= strtoupper($b['kode_bagian']) . " - " . strtoupper($b['nama_bagian']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Bagian </label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select tipe" name="tipe" id="tipe" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($tipeEmployee as $t) : ?>
                                    <option <?= !empty($data) ? ($data['tipe'] == $t['golongan_name'] ? 'selected' : '') : '' ?> value="<?= $t['golongan_name'] ?>">
                                        <?= strtoupper($t['golongan_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe / Golongan </label>
                        </div>
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input value="<?= !empty($data) ? ($data['dob'] != "0000-00-00" ? date('d/m/Y', strtotime($data['dob'])) : '')  : '' ?>" autocomplete="one-time-code" name="dob" type="text" placeholder="" class="form-control dob" id="dob">
                                <label>Tanggal Lahir (Opsional)</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select marriage_id" name="marriage_id" id="marriage_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataPernikahan as $d) : ?>
                                    <option <?= !empty($data) ? ($data['marriage_id'] == $d->id ? 'selected' : '') : '' ?> value="<?= encrypt($d->id) ?>"><?= strtoupper($d->value) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Status Kawin (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($data) ? $data['child'] : '' ?>" autocomplete="one-time-code" type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control child" id="child" name="child" placeholder="Jumlah Anak">
                            <label for="floatingInput">Jumlah Anak (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($data) ? $data['nik'] : '' ?>" autocomplete="one-time-code" type="text" minlength="16" maxlength="16" class="form-control nik" id="nik" name="nik" placeholder="NIK">
                            <label for="floatingInput">NIK (Opsional)</label>
                        </div>
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Informasi Alamat
                        </label>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select province_id" name="province_id" id="province_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataProvinces as $province) : ?>
                                    <option <?= !empty($data) ? ($data['province_id'] == $province['id'] ? 'selected' : '') : '' ?> value="<?= encrypt($province["id"]); ?>"><?= strtoupper($province["province_name"]); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Provinsi (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select city_id" name="city_id" id="city_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($data)) : ?>
                                    <?php foreach ($cityList as $c) : ?>
                                        <option data-code="<?= $c['postal_code'] ?>" <?= $c['id'] == $data['city_id'] ? 'selected' : '' ?> value="<?= $c['id'] ?>"><?= strtoupper($c['city_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kota (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($data) ? $data['postal_code'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control zip_code" id="zip_code" name="zip_code" placeholder="Zip Code">
                            <label for="floatingInput">Kode Pos (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($data) ? $data['address'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control address" id="address" name="address" placeholder="Address">
                            <label for="floatingInput">Alamat Lengkap (Opsional)</label>
                        </div>
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Informasi Kontak & Pendidikan Terakhir
                        </label>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($data) ? $data['email'] : '' ?>" autocomplete="one-time-code" type="email" class="form-control email" id="email" name="email" placeholder="Email (Opsional)" maxlength="30">
                            <label for="floatingInput">Email (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select religion_id" name="religion_id" id="religion_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataAgama as $d) : ?>
                                    <option <?= !empty($data) ? ($data['religion_id'] == $d->id ? 'selected' : '') : '' ?> value="<?= encrypt($d->id) ?>"><?= strtoupper($d->value) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Agama (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select pendidikan" name="pendidikan" id="pendidikan" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataPendidikan as $d) : ?>
                                    <option <?= !empty($data) ? ($data['pendidikan'] == $d->id ? 'selected' : '') : '' ?> value="<?= encrypt($d->id) ?>"><?= strtoupper($d->value) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pendidikan (Opsional)</label>
                        </div>
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Informasi Rekening & Payroll
                        </label>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bank_name" id="bank_name" name="bank_name" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= !empty($data) ? ($data['bank_name'] == "BCA" ? 'selected' : '') : '' ?> value="BCA">BCA</option>
                                <option <?= !empty($data) ? ($data['bank_name'] == "BNI" ? 'selected' : '') : '' ?> value="BNI">BNI</option>
                                <option <?= !empty($data) ? ($data['bank_name'] == "BRI" ? 'selected' : '') : '' ?> value="BRI">BRI</option>
                                <option <?= !empty($data) ? ($data['bank_name'] == "MANDIRI" ? 'selected' : '') : '' ?> value="MANDIRI">MANDIRI</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Nama Bank (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($data) ? $data['acc_no'] : '' ?>" autocomplete="one-time-code" type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control acc_no" id="acc_no" name="acc_no" placeholder="No. Rekening" maxlength="30">
                            <label for="floatingInput">No. Rekening (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($data) ? $data['owner_name'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control owner_name" id="owner_name" name="owner_name" placeholder="Nama Pemilik Rekening" maxlength="100">
                            <label for="floatingInput">Nama Pemilik Rekening (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($data) ? $data['pin'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control pin" id="pin" name="pin" oninput="this.value=this.value.replace(/[^0-9]/g,'');" placeholder="PIN (Opsional)">
                            <label for="floatingInput">PIN (Opsional)</label>
                        </div>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select id="status" class="form-select status" name="status" aria-label="Floating label select example">
                                <option <?= !empty($data) ? ($data['status'] == "Aktif" ? 'selected' : '') : '' ?> value="Aktif">AKTIF</option>
                                <option <?= !empty($data) ? ($data['status'] == "Tidak Aktif" ? 'selected' : '') : '' ?> value="Tidak Aktif">TIDAK AKTIF</option>
                                <option <?= !empty($data) ? ($data['status'] == "Resign" ? 'selected' : '') : '' ?> value="Resign">RESIGN</option>
                                <option <?= !empty($data) ? ($data['status'] == "Pensiun" ? 'selected' : '') : '' ?> value="Pensiun">PENSIUN</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Status Karyawan</label>
                        </div>
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Komponen Gaji
                        </label>
                        <div class="table-responsive mt-2 mb-3">
                            <table class="table table-borderd nowrap table-hover-tobasurimi" width="100%" cellspacing="0" id="tabel-komponen-gaji">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Komponen Gaji</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="body-detail-table" id="body-detail-table">
                                    <tr>
                                        <td colspan="3" class="text-center">Komponen Gaji Menyesuaikan Departemen</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</section>
<?php if (!empty($data)) : ?>
    <script>
        let csrfTokens = '<?= csrf_token() ?>';
        let csrfs = $(`[name="${csrfTokens}"]`);
        var divisiID = "<?= encrypt($data['division_id']) ?>";
        var employeeID = "<?= encrypt($data['id']) ?>";

        var formData = new FormData();
        formData.append('divisi_id', divisiID);
        formData.append('employee_id', employeeID);

        $.ajax({
            url: "<?= base_url("employee/getKomponenGaji"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfs.val());
            },
            complete: function() {},
            processData: false,
            contentType: false,
            success: function(response) {

                if (response.status) {
                    var table = $('#tabel-komponen-gaji');
                    table.find('tbody').empty();
                    var komponenGaji = response.komponenGaji;

                    if (komponenGaji.length == 0 && response.divisi.divisi != null) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ups, Divisi ' + response.divisi.divisi + ' komponen gajinya belum diatur :)',
                            confirmButtonColor: '#4e73df',
                        });
                    } else {

                        $.each(komponenGaji, function(index, data) {
                            var newRow = $('<tr class="text-dark font-weight-bold">');
                            var indexNumber = index + 1;
                            var inputText = $('<div class="form-floating">').append(
                                $('<input>').attr({
                                    'type': 'text',
                                    'name': data.id,
                                    'class': 'form-control komponen-gaji',
                                    'value': (data.nominal == null) ?
                                        "0,00" : greatFormatRupiah(data.nominal),
                                    'onkeyup': "this.value = greatFormatRupiah(this.value)",
                                })
                            ).append(
                                $('<label>').attr('for', 'floatingInput').text(data.name)
                            );

                            var komponenGajiName = "<div style='font-weight: bold; color: " + (data.tipe == "PLUS" ? "green" : "red") + ";'>" + (data.tipe == "PLUS" ? "(+) " : "(-) ") + data.name + "</div>";

                            newRow.append($('<td>').text(indexNumber));
                            newRow.append($('<td>').append(komponenGajiName));
                            newRow.append($('<td>').append(inputText));
                            table.append(newRow);
                        });
                    }

                }

            },
        });


        var sort = "payrolls.year_month";
        var sortType = "desc";
        var tabelRiwayatPayroll = $('#tabel-riwayat-payroll').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            order: [
                [1, 'asc']
            ],
            fixedHeader: true,
            lengthMenu: [
                [12],
                [12],
            ],
            pageLength: 12,
            ajax: {
                url: "<?= base_url("employee/all-riwayat-payroll"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.employee_id = "<?= encrypt($data['id']) ?>";
                    data.year = $('#year').val();
                    data.sort = sort;
                    data.sortType = sortType;
                }
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                    data: "no",
                    className: "text-center",
                    sortable: false,
                    width: "3%"
                },
                {
                    data: "year_month",
                    className: "text-left"
                },
                {
                    data: "nominal_gaji_diterima",
                    className: "text-right",
                    render: function(data) {
                        return greatFormatRupiah(data);
                    }
                },
                {
                    data: "id",
                    className: "text-center actions",
                    searchable: false,
                    sortable: false,
                    render: function(data, type, row) {
                        let id = row.id;
                        let res = '';

                        res += `
                            <?php if (can('Personalia', 'Karyawan', 'p')): ?>
                                <a target="_blank" data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" href='<?= base_url("payroll/print/single/"); ?>${id}' style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </a>
                            <?php endif ?>
                        `;

                        return res;
                    }
                }
            ],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            "drawCallback": function(settings) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            },
            language: {
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        $('.year').change(function(e) {
            e.preventDefault();
            tabelRiwayatPayroll.ajax.reload();
        });

        function changeSort(val) {
            if (sort !== val) {
                sortType = "asc";
                sort = val;
            } else {
                sortType = sortType === "asc" ? "desc" : "asc";
            }
        }
    </script>
<?php endif; ?>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('#gender').select2({
        placeholder: "Pilih Jenis Kelamin",
        theme: "bootstrap-5",
    });
    $('#jabatan_id').select2({
        placeholder: "Pilih Jabatan",
        theme: "bootstrap-5",
    });
    $('#division_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
    });
    $('#bagian_id').select2({
        placeholder: "Pilih Bagian",
        theme: "bootstrap-5",
    });
    $('#tipe').select2({
        placeholder: "Pilih Tipe/Golongan",
        theme: "bootstrap-5",
    });
    $('#marriage_id').select2({
        placeholder: "Pilih Status Kawin (Opsional)",
        theme: "bootstrap-5",
    });
    $('#province_id').select2({
        placeholder: "Pilih Provinsi (Opsional)",
        theme: "bootstrap-5",
    });
    $('#city_id').select2({
        placeholder: "Pilih Kota (Opsional)",
        theme: "bootstrap-5",
    });
    $('#religion_id').select2({
        placeholder: "Pilih Agama (Opsional)",
        theme: "bootstrap-5",
    });
    $('#pendidikan').select2({
        placeholder: "Pilih Pendidikan (Opsional)",
        theme: "bootstrap-5",
    });
    $('#bank_name').select2({
        placeholder: "Pilih Nama Bank (Opsional)",
        theme: "bootstrap-5",
    });
    $('#status').select2({
        placeholder: "Pilih Status Karyawan",
        theme: "bootstrap-5",
    });
    $("#year").datepicker({
        format: "yyyy",
        startView: "years",
        minViewMode: "years",
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
    });
    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px').css('height', ' calc(3.5rem + 2px)');

    var today = new Date();
    var yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);

    $("#dob,#join_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        endDate: yesterday
    });

    $('select[name="division_id"]').on('change', function(e) {
        e.preventDefault();
        let csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('divisionID', $(this).val());
        $.ajax({
            url: `<?= base_url("employee/get-bagian"); ?>`,
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
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
        generateKomponenGaji();
    });


    $('select[name="province_id"]').on('change', function() {
        let id = $(this).val();
        $.ajax({
            url: `<?= base_url("city"); ?>/${id}`,
            method: "GET",
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                $(".city_id").empty()
                $(".city_id").val("").change()
                $(".city_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".city_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name.toUpperCase()}</option>`)
                })
            }
        })
    });

    $('#city_id').change(function() {
        $(".zip_code").val($(".city_id option:selected").attr("data-code"))
    });

    var validator = $("#create-form").validate({
        rules: {
            nip: {
                required: true,
                minlength: 6,
                maxlength: 18
            },
            nik: {
                minlength: 16,
                maxlength: 16
            },
            name: {
                required: true
            },
            gender: {
                required: true
            },
            division_id: {
                required: true,
            },
            bagian_id: {
                required: true,
            },
            tipe: {
                required: true,
            },
            pin: {
                minlength: 6,
                maxlength: 6
            },
            jabatan_id: {
                required: true,
            },
            join_date: {
                required: true,
            },
        },
        messages: {
            nip: {
                required: "NIP wajib diisi",
                minlength: "NIP Minimal 6 Digit",
                maxlength: "NIP Maksimal 18 Digit"
            },
            nik: {
                minlength: "NIK Minimal 16 Digit",
                maxlength: "NIK Maksimal 16 Digit"
            },
            name: {
                required: "Nama Lengkap wajib diisi"
            },
            gender: {
                required: "Jenis Kelamin wajib diisi"
            },
            division_id: {
                required: "Divisi wajib diisi"
            },
            bagian_id: {
                required: "Bagian wajib diisi"
            },
            tipe: {
                required: "Tipe/Golongan wajib diisi"
            },
            pin: {
                minlength: "Minimal dan Maksimal 6 Karakter",
                maxlength: "Minimal dan Maksimal 6 Karakter"
            },
            jabatan_id: {
                required: "Jabatan wajib diisi"
            },
            join_date: {
                required: "Tanggal Bergabung wajib diisi"
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

    $(".btn-submit-parent").click(function() {
        if ($(".create-form").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $(".komponen-gaji").each(function() {
                        $(this).val(destroyFormatRupiah($(this).val()));
                    });

                    const csrf = $(`[name="${csrfToken}"]`);
                    let data = new FormData(document.querySelector("#create-form"));
                    let id = $(".id").val();
                    let url = id == '' ? '<?= base_url("employee/save") ?>' : '<?= base_url("employee/update"); ?>';

                    $.ajax({
                        url: url,
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
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
                                        if (id == '') {
                                            Swal.fire({
                                                icon: 'question',
                                                title: 'Input Karyawan Lagi ?',
                                                confirmButtonColor: '#4e73df',
                                                cancelButtonColor: '#d33',
                                                showCancelButton: true,
                                                reverseButtons: true,
                                                confirmButtonText: 'Ya',
                                                cancelButtonText: 'Tidak',
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url('employee/create') ?>";
                                                } else {
                                                    window.location.href = "<?= base_url('employee') ?>";
                                                }
                                            })
                                        } else {
                                            window.location.href = "<?= base_url('employee') ?>";
                                        }
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        },
                    });
                }
            })
        }
    });

    $(".delete-parent").click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Karyawan?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                let id = $(".id").val();
                $.ajax({
                    url: "<?= base_url("employee/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading()
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.replace("<?= base_url('employee') ?>")
                                })
                        }
                    },

                });
            }
        })
    })

    function generateKomponenGaji() {
        const csrf = $(`[name="${csrfToken}"]`);
        var divisiID = $('select[name="division_id"]').val();
        var employeeID = $('input[name="id"]').val();

        var formData = new FormData();
        formData.append('divisi_id', divisiID);
        formData.append('employee_id', employeeID);

        $.ajax({
            url: "<?= base_url("employee/getKomponenGaji"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            processData: false,
            contentType: false,
            success: function(response) {

                if (response.status) {
                    var table = $('#tabel-komponen-gaji');
                    table.find('tbody').empty();
                    var komponenGaji = response.komponenGaji;

                    if (komponenGaji.length == 0 && response.divisi.divisi != null) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ups, Divisi ' + response.divisi.divisi + ' komponen gajinya belum diatur :)',
                            confirmButtonColor: '#4e73df',
                        });
                    } else {

                        $.each(komponenGaji, function(index, data) {
                            var newRow = $('<tr class="text-dark font-weight-bold">');
                            var indexNumber = index + 1;
                            var inputText = $('<div class="form-floating">').append(
                                $('<input>').attr({
                                    'type': 'text',
                                    'name': data.id,
                                    'class': 'form-control komponen-gaji',
                                    'value': (data.nominal == null) ?
                                        "0,00" : greatFormatRupiah(data.nominal),
                                    'onkeyup': 'this.value = greatFormatRupiah(this.value);'
                                })
                            ).append(
                                $('<label>').attr('for', 'floatingInput').text(data.name)
                            );

                            var komponenGajiName = "<div style='font-weight: bold; color: " + (data.tipe == "PLUS" ? "green" : "red") + ";'>" + (data.tipe == "PLUS" ? "(+) " : "(-) ") + data.name + "</div>";

                            newRow.append($('<td>').text(indexNumber));
                            newRow.append($('<td>').append(komponenGajiName));
                            newRow.append($('<td>').append(inputText));
                            table.append(newRow);
                        });
                    }

                }

            },
        });
    }

    function previewPhoto() {
        let file = document.getElementById("employeeImg").files[0];
        document.getElementById("preview_photo").src = window.URL.createObjectURL(file);
    }
</script>

<?= $this->endSection(); ?>
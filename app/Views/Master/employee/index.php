<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Karyawan</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-3 mb-3 view-cols-image">
                            <img class="preview-photo" id="preview_photo" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAACCCAMAAAC93eDPAAAAMFBMVEXk5ueutLfn6eqyt7qrsbTh4+TDx8q2u77Z3N3Jzc/U19nO0dPq7Oy8wcSnrrHd4OEuWFw9AAADAUlEQVR4nO2a23LjIAxAjQAbsIH//9vFTjpNUhckRyI7u5ynTF84lSVuYpoGg8FgMBgMBoPB4H8DYNs2gE+NvkEOfo4xzj7YD2jAFKIy3+jZ9bUA67Ux6hFjlnXqJwH+Zfy7hQ5bJwF3KrCTou0SCP+bwBEIJ+8Ac/rdYJcI0g6wVGJw+xirrAPElkGJg6gDzG0D2W8BK8agOIgZTBlnoNQiFQZYkAZiKQmhXo5PWBGFSeMNzCwRBmwu3h0kwoDPhEPBC4TBUYJQ4F80cbPSN4l/vdpoMZBISEuoyIOF24BWDwfsNTFTDZLjVohkBfZJmjA13uDPR7KCYlcgG6jIrUAuiH9SgWzAr/D5iqAt1YcC+3JNn5q4t/JQO0meK2ReA9re9UAzGxSoCpHdYCPmo8TJkrhhSOwCZdtECwL7xDRR968m8BsQDrU7MgdbShgEtvAHFq0gkgk7+F10EjpY73MDzkHyxgtXmCYKXsIC5mxr2M9RZActfAkMoeFgBFbIVwenahJi5fjkYCt1IXK5csLm07lEUrlXUwTKNu7nDiZ164jcJCa/mIdYGKNi6NgXukvkNepbh0wtc8gfaRXCBpPN2drjV+/BYf+fc3YuFJxz2d7/1mf0MnbwcdFafTUqjzlxieVr2EncA8D5qFM6bxKmpEtWCFrAZp/L4JziIdO5BcheN4f/LlF2C4C9Q40b/8tiWRlXTID1tUONslAz02QF00oLwINEmjkiAUFfFLhFwr8bCciI3miVsni95bCdvxIgSsTrgYCM3LS3uNy+hXA1DX86XLt928i3SzWHK8fc1jsFqoOmJ8S7lfAT4r4S806BiKEdcQQMFO0aEDxrHnxBOGw2D22XHdBHrSwjoPCXkdiLjCvgbiPpF94UFtSBS9IA1SKgduipIKqCdMF5gXZGSgehTFDNLyEsUMLQaqdLzUoPCo29g8zi8OJQr0uQN2g9bMgi69Mz9SuxC89FLlBtnMmX5E5939AhGxvPISHqDtTf+kAXqh9iMBgMBn8LfwAfLCKVi1nppAAAAABJRU5ErkJggg==" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-input-files-img" style="height: 50px;">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onchange="previewPhoto();" type="file" class="form-control input-image employeeImg" id="employeeImg" name="employeeImg" accept="image/png, image/jpg, image/jpeg">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control name" id="name" name="name" placeholder="Full Name" maxlength="30">
                                <label for="floatingInput">Nama Lengkap</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" minlength="16" maxlength="16" class="form-control nik" id="nik" name="nik" placeholder="NIK" maxlength="30">
                                <label for="floatingInput">NIK</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nip" id="nip" name="nip" placeholder="NIP" maxlength="30">
                                <label for="floatingInput">NIP</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control address" id="address" name="address" placeholder="Address">
                                <label for="floatingInput">Alamat</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input class="form-control input-picker dob" id="dob" name="dob" placeholder="Date of Birth">
                                        <label for="floatingInput">Tanggal Lahir</label>
                                    </div>
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                            <i class="fa fa-calendar icon-dob icon-form"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select onchange="getCity()" class="form-select province_id" name="province_id" id="province_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataProvinces)) {
                                        foreach ($dataProvinces as $province) {
                                    ?>
                                            <option value="<?= $province["id"]; ?>"><?= $province["province_name"]; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Provinsi</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select city_id" name="city_id" id="city_id" aria-label="Floating label select example" onchange="getZipCode()">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Kota</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control zip_code" id="zip_code" name="zip_code" placeholder="Zip Code">
                                <label for="floatingInput">Kode Pos</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control phone_no" id="phone_no" name="phone_no" placeholder="Phone (Optional)" maxlength="30">
                                <label for="floatingInput">Nomor Telepon (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="email" class="form-control email" id="email" name="email" placeholder="Email (Optional)" maxlength="30">
                                <label for="floatingInput">Email (Optional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select gender" name="gender" id="floatingSelect" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="Pria">Pria</option>
                                    <option value="Wanita">Wanita</option>
                                </select>
                                <label for="floatingInput">Jenis Kelamin</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select religion_id" name="religion_id" id="religion_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Agama</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select marriage_id" name="marriage_id" id="marriage_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Status Kawin</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control child" id="child" name="child" placeholder="Jumlah Anak">
                                <label for="floatingInput">Jumlah Anak</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select division_id" name="division_id" id="division_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Divisi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select jabatan_id" name="jabatan_id" id="floatingSelect" aria-label="Floating label select example">
                                    <option value=""></option>

                                </select>
                                <label for="floatingInput">Jabatan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input class="form-control input-picker join_date" id="join_date" name="join_date" placeholder="Tanggal Bergabung">
                                        <label for="floatingInput">Tanggal Bergabung</label>
                                    </div>
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                            <i class="fa fa-calendar icon-join-date icon-form"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select bank_name" name="bank_name" id="floatingSelect" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="BCA">BCA</option>
                                    <option value="BNI">BNI</option>
                                    <option value="BRI">BRI</option>
                                    <option value="MANDIRI">MANDIRI</option>
                                </select>
                                <label for="floatingInput">Nama Bank</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control acc_no" id="acc_no" name="acc_no" placeholder="No. Rekening" maxlength="30">
                                <label for="floatingInput">No. Rekening</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control owner_name" id="owner_name" name="owner_name" placeholder="Nama Pemilik Rekening" maxlength="100">
                                <label for="floatingInput">Nama Pemilik Rekening</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-pin">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control pin" id="pin" name="pin" oninput="this.value=this.value.replace(/[^0-9]/g,'');" placeholder="PIN (Optional)">
                                <label for="floatingInput">PIN (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select status" name="status" id="floatingSelect" aria-label="Floating label select example">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                    <option value="Resign">Resign</option>
                                    <option value="Pensiun">Pensiun</option>
                                </select>
                                <label for="floatingInput">Status</label>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col">
                            <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal" style="width: 106px;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-2 mb-3">
                    <table class="table-inside table-borderd nowrap table-hover-tobasurimi" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Komponen Gaji</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Komponen Gaji</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tunjangan_id" name="tunjangan_id" id="tunjangan_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Tunjangan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nominal" name="nominal" id="nominal" placeholder="Nominal">
                                <label for="floatingInput">Nominal</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <button type="button" class="btn btn-discard delete-detail delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Employee</h1>
        <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-2">
                    <input class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('nip')" class="sort">NIP</th>
                                <th onclick="changeSort('name')" class="sort">Nama Lengkap</th>
                                <th onclick="changeSort('divisionName')" class="sort">Divisi</th>
                                <th onclick="changeSort('email')" class="sort">Email</th>
                                <th onclick="changeSort('phone_no')" class="sort">No. Telepon</th>
                                <th onclick="changeSort('dob')" class="sort">Tanggal Lahir</th>
                                <th onclick="changeSort('gender')" class="sort">Jenis Kelamin</th>
                                <th onclick="changeSort('status')" class="sort">Status</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "nip";
    let sortType = "asc";

    let komponen_gaji = [];
    var row = 0;

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("employee/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
            data: "no",
            className: "text-center",
            sortable: false,
            width: "5%"
        }, {
            data: "nip",
            className: "text-center"
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "divisionName",
            className: "text-center"
        }, {
            data: "email",
            className: "text-center"
        }, {
            data: "phone_no",
            className: "text-center"
        }, {
            data: "dob",
            className: "text-center"
        }, {
            data: "gender",
            className: "text-center"
        }, {
            data: "status",
            className: "text-center"
        }],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var validator_detail = $(".detail-form").validate({
        rules: {
            tunjangan_id: {
                required: true
            },
            nominal: {
                required: true
            }
        },
        messages: {
            tunjangan_id: {
                required: "Tunjangan wajib diisi"
            },
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

    $(document).ready(function() {
        $('.province_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        $('.city_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        $('.religion_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        $('.marriage_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        $('.jabatan_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        $('.tunjangan_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        $(".nik").mask("AAAAAAAAAAAAAAAA", {
            translation: {
                "A": {
                    pattern: /[0-9]/,
                }
            }
        })

        //CSS SELECT2 FLOATING LABEL
        $('.province_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.province_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.province_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        //CSS SELECT2 FLOATING LABEL
        $('.city_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.city_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.city_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        //CSS SELECT2 FLOATING LABEL
        $('.religion_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.religion_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.religion_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        //CSS SELECT2 FLOATING LABEL
        $('.marriage_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.marriage_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.marriage_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        //CSS SELECT2 FLOATING LABEL
        $('.jabatan_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.jabatan_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.jabatan_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".tunjangan_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".tunjangan_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".tunjangan_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $(".create-form").validate({
            rules: {
                nip: {
                    required: true
                },
                nik: {
                    required: true,
                    minlength: 16,
                    maxlength: 16
                },
                name: {
                    required: true
                },
                gender: {
                    required: true
                },
                dob: {
                    required: true
                },
                address: {
                    required: true,
                },
                email: {
                    email: true
                },
                acc_no: {
                    required: true
                },
                division_id: {
                    required: true,
                },
                // province_id: {
                //     required: true,
                // },
                // city_id: {
                //     required: true,
                // },
                religion_id: {
                    required: true,
                },
                marriage_id: {
                    required: true,
                },
                child: {
                    required: true,
                },
                jabatan_id: {
                    required: true,
                },
                join_date: {
                    required: true,
                },
                owner_name: {
                    required: true,
                },
                bank_name: {
                    required: true,
                }
            },
            messages: {
                nip: {
                    required: "NIP wajib diisi"
                },
                nik: {
                    required: "NIK wajib diisi",
                    minlength: "NIK Minimal 16 Digit",
                    maxlength: "NIK Maksimal 16 Digit"
                },
                name: {
                    required: "Nama Lengkap wajib diisi"
                },
                gender: {
                    required: "Jenis Kelamin wajib diisi"
                },
                dob: {
                    required: "Tanggal Lahir wajib diisi"
                },
                address: {
                    required: "Alamat wajib diisi"
                },
                email: {
                    email: "Email harus benar"
                },
                acc_no: {
                    required: "Nomor Rekening wajib diisi"
                },
                division_id: {
                    required: "Divisi wajib diisi"
                },
                // province_id: {
                //     required: "Provinsi wajib diisi"
                // },
                // city_id: {
                //     required: "Kota wajib diisi"
                // },
                religion_id: {
                    required: "Agama wajib diisi"
                },
                marriage_id: {
                    required: "Status Kawin wajib diisi"
                },
                child: {
                    required: "Anak wajib diisi"
                },
                pin: {
                    minlength: "Minimal dan Maksimal 6 Karakter",
                    maxlength: "Minimal dan Maksimal 6 Karakter"
                },
                jabatan: {
                    required: "Jabatan wajib diisi"
                },
                join_date: {
                    required: "Tanggal Bergabung wajib diisi"
                },
                owner_name: {
                    required: "Nama Pemilik Rekening wajib diisi"
                },
                bank_name: {
                    required: "Bank wajib diisi"
                }
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

        $(".dob").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".join_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $('.icon-dob').click(function() {
            $(".dob").focus();
        });

        $('.icon-join-date').click(function() {
            $(".dob").focus();
        });

        $(".phone_no").mask("0000000000000")

        $(".btn-show-detail").click(function() {
            $(".title-detail-name").text("Tambah")
            $(".delete-detail").css('display', 'none');
            $(".id_detail").val('')
            $(".nominal").val("");

            validator_detail.resetForm();
            validator_detail.reset();

            $.ajax({
                url: `<?= base_url("tunjangan/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".tunjangan_id").empty()
                    $(".jabatan_id").val("").change()
                    $(".tunjangan_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".tunjangan_id").append(`<option value="${item.id}">${item.name}</option>`)
                    })

                    $(".tunjangan_id").val('').change();
                    $(".detail-modal").modal("show")
                }
            })
        })

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".form-pin").css("display", "");
            $('.pin').rules('add', {
                minlength: 6,
                maxlength: 6
            });


            $(".province_id").val('').change()
            $(".city_id").val('').change()
            $(".city_id").empty()
            $(".city_id").append(`<option value=""></option>`)

            $(".title-name").text("Tambah");
            validator.resetForm();
            validator.reset();
            document.getElementById("preview_photo").src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAACCCAMAAAC93eDPAAAAMFBMVEXk5ueutLfn6eqyt7qrsbTh4+TDx8q2u77Z3N3Jzc/U19nO0dPq7Oy8wcSnrrHd4OEuWFw9AAADAUlEQVR4nO2a23LjIAxAjQAbsIH//9vFTjpNUhckRyI7u5ynTF84lSVuYpoGg8FgMBgMBoPB4H8DYNs2gE+NvkEOfo4xzj7YD2jAFKIy3+jZ9bUA67Ux6hFjlnXqJwH+Zfy7hQ5bJwF3KrCTou0SCP+bwBEIJ+8Ac/rdYJcI0g6wVGJw+xirrAPElkGJg6gDzG0D2W8BK8agOIgZTBlnoNQiFQZYkAZiKQmhXo5PWBGFSeMNzCwRBmwu3h0kwoDPhEPBC4TBUYJQ4F80cbPSN4l/vdpoMZBISEuoyIOF24BWDwfsNTFTDZLjVohkBfZJmjA13uDPR7KCYlcgG6jIrUAuiH9SgWzAr/D5iqAt1YcC+3JNn5q4t/JQO0meK2ReA9re9UAzGxSoCpHdYCPmo8TJkrhhSOwCZdtECwL7xDRR968m8BsQDrU7MgdbShgEtvAHFq0gkgk7+F10EjpY73MDzkHyxgtXmCYKXsIC5mxr2M9RZActfAkMoeFgBFbIVwenahJi5fjkYCt1IXK5csLm07lEUrlXUwTKNu7nDiZ164jcJCa/mIdYGKNi6NgXukvkNepbh0wtc8gfaRXCBpPN2drjV+/BYf+fc3YuFJxz2d7/1mf0MnbwcdFafTUqjzlxieVr2EncA8D5qFM6bxKmpEtWCFrAZp/L4JziIdO5BcheN4f/LlF2C4C9Q40b/8tiWRlXTID1tUONslAz02QF00oLwINEmjkiAUFfFLhFwr8bCciI3miVsni95bCdvxIgSsTrgYCM3LS3uNy+hXA1DX86XLt928i3SzWHK8fc1jsFqoOmJ8S7lfAT4r4S806BiKEdcQQMFO0aEDxrHnxBOGw2D22XHdBHrSwjoPCXkdiLjCvgbiPpF94UFtSBS9IA1SKgduipIKqCdMF5gXZGSgehTFDNLyEsUMLQaqdLzUoPCo29g8zi8OJQr0uQN2g9bMgi69Mz9SuxC89FLlBtnMmX5E5939AhGxvPISHqDtTf+kAXqh9iMBgMBn8LfwAfLCKVi1nppAAAAABJRU5ErkJggg==";
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');

            komponen_gaji = [];
            row = 0

            $(".body-detail-table").empty()

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'status_pernikahan'
                },
                dataType: "json",
                success: function(result) {
                    $(".marriage_id").empty()
                    $(".marriage_id").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".marriage_id").append(`<option value="${item.id}">${item.value}</option>`)
                    })

                    $(".marriage_id").val('').change();
                }
            })

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'religion'
                },
                dataType: "json",
                success: function(result) {
                    $(".religion_id").empty()
                    $(".religion_id").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".religion_id").append(`<option value="${item.id}">${item.value}</option>`)
                    })

                    $(".religion_id").val('').change();
                }
            })

            $.ajax({
                url: `<?= base_url("divisi/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".division_id").empty()
                    $(".division_id").val("").change()
                    $(".division_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".division_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                    })

                    $(".division_id").val('').change();
                    $(".add-modal").modal("show")
                }
            })

            $.ajax({
                url: `<?= base_url("jabatan/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".jabatan_id").empty()
                    $(".jabatan_id").val("").change()
                    $(".jabatan_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".jabatan_id").append(`<option value="${item.id}">${item.jabatan_name}</option>`)
                    })

                    $(".jabatan_id").val('').change();
                    $(".add-modal").modal("show")
                }
            })
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".btn-submit-detail").click(function() {
            let id = $(".id_detail").val();
            let tunjangan_id = $(".tunjangan_id option:selected").val();
            let tunjangan_name = $(".tunjangan_id option:selected").text();
            let nominal = $(".nominal").val();

            // update detail
            if (id) {
                let validate_exist = true;

                komponen_gaji.map(item => {
                    if (item.row != id) {
                        // if (item.tunjangan_id == tunjangan_id) {
                        //     validate_exist = false;
                        // }
                    }
                })

                if (validate_exist) {
                    if ($(".detail-form").valid()) {
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
                                let new_komponen_gaji = []
                                let tag_html = "";

                                row = 0;

                                $(".body-detail-table").empty()

                                komponen_gaji.map(item => {
                                    if (item.row == id) {
                                        tag_html += `<tr class="edit-table-detail" data-id ="${row + 1}" data-tunjanganid ="${tunjangan_id}" data-nominal ="${nominal}">`;
                                        tag_html += "<td>";
                                        tag_html += tunjangan_name;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += nominal;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_komponen_gaji.push({
                                            row: item.row,
                                            id: item.id,
                                            tunjangan_id: tunjangan_id,
                                            nominal: nominal,
                                            tunjangan_name: tunjangan_name,
                                        });
                                    } else {
                                        tag_html += `<tr class="edit-table-detail" data-row ="${row+1}" data-id ="${item.id}" data-tunjanganid ="${item.tunjangan_id}" data-nominal ="${item.nominal}">`;
                                        tag_html += "<td>";
                                        tag_html += item.tunjangan_name;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += item.nominal;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_komponen_gaji.push(item);
                                    }

                                    row = row + 1;
                                })

                                komponen_gaji = new_komponen_gaji;

                                $(".body-detail-table").append(tag_html)

                                $(".detail-modal").modal("hide")
                            }
                        })
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Komponen Already Exist',
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
            // create detail
            else {
                let validate_exist = true;

                komponen_gaji.map(item => {
                    // if (item.tunjangan_id == tunjangan_id) {
                    //     validate_exist = false;
                    // }
                })

                if (validate_exist) {
                    if ($(".detail-form").valid()) {
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
                                komponen_gaji.push({
                                    row: row + 1,
                                    id: id,
                                    tunjangan_id: tunjangan_id,
                                    nominal: nominal,
                                    tunjangan_name: tunjangan_name,
                                })

                                let tag_html = "";
                                tag_html += `<tr class="edit-table-detail" data-id ="${row + 1}" data-tunjanganid ="${tunjangan_id}" data-nominal ="${nominal}">`;
                                tag_html += "<td>";
                                tag_html += tunjangan_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += nominal;
                                tag_html += "</td>";
                                tag_html += "</tr>";
                                $(".body-detail-table").append(tag_html)
                                $(".detail-modal").modal("hide")
                                row = row + 1;
                            }
                        })
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Komponen Already Exist',
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
        })

        $(".btn-submit-form").click(function() {
            if ($(".create-form").valid()) {
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
                        setLoading()
                        let data = new FormData(document.querySelector(".create-form"));
                        let new_komponen_gaji = [];
                        let id = $(".id").val();
                        komponen_gaji.forEach((item) => {
                            new_komponen_gaji.push({
                                "id": item.id,
                                "employee_id": item.employee_id,
                                "tunjangan_id": item.tunjangan_id,
                                "nominal": item.nominal
                            })
                        })
                        data.append("komponen_gaji", JSON.stringify(new_komponen_gaji))
                        // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("employee/update"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        stopLoading()
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                table.ajax.reload()
                                                $(".add-modal").modal("hide")
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        }
                        // CREATE
                        else {
                            $.ajax({
                                url: "<?= base_url("employee/save"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        stopLoading()
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                table.ajax.reload()
                                                $(".add-modal").modal("hide")
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        }
                    }
                })
            }
        })

        $(".delete-btn").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("employee/delete"); ?>",
                        data: {
                            id: id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        table.ajax.reload()
                                        $(".add-modal").modal("hide")
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".form-pin").css("display", "none");
            $('.pin').rules('remove', 'minlength');
            $('.pin').rules('remove', 'maxlength');
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            validator.resetForm();
            validator.reset();

            komponen_gaji = [];
            row = 0

            $(".body-detail-table").empty()

            $.ajax({
                url: "<?= base_url("employee/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".join_date").val(res?.data?.join_date);
                        $(".address").val(res?.data?.address);
                        $(".acc_no").val(res?.data?.acc_no);
                        $(".bank_name").val(res?.data?.bank_name);
                        $(".owner_name").val(res?.data?.owner_name);
                        $(".dob").val(res?.data?.dob);
                        $(".email").val(res?.data?.email);
                        $(".gender").val(res?.data?.gender);
                        $(".name").val(res?.data?.name);
                        $(".nip").val(res?.data?.nip);
                        $(".nik").val(res?.data?.nik);
                        $(".phone_no").val(res?.data?.phone_no);
                        $(".status").val(res?.data?.status);
                        $(".jabatan_id").val(res?.data?.jabatan_id);
                        $(".zip_code").val(res?.data?.postal_code);
                        $(".province_id").val(res?.data?.province_id).change();
                        $(".child").val(res?.data?.child).change();
                        $(".komponen_gaji").val(res?.data?.komponen_gaji).change();
                        document.getElementById("preview_photo").src = res?.data?.employee_img;

                        let tag_html = "";

                        res?.data?.komponen_gaji.map((item) => {
                            tag_html += `<tr class="edit-table-detail" data-row ="${row + 1}" data-id ="${item.id}" data-employeeid = "${item.employee_id}" data-tunjanganid ="${item.tunjangan_id}" data-roleid ="${item.nominal}">`;
                            tag_html += "<td>";
                            tag_html += item.tunjangan_name;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += item.nominal;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            komponen_gaji.push({
                                ...item,
                                row: row + 1
                            });

                            row = row + 1;
                        })

                        $(".body-detail-table").append(tag_html)

                        // AJAX GET CITY
                        $.ajax({
                            url: `<?= base_url("city"); ?>/${res?.data?.province_id}`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".city_id").empty()
                                $(".city_id").val("").change()
                                $(".city_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".city_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                                })

                                $(".city_id").val(res?.data?.city_id).change();
                                //$(".zip_code").val(res?.data?.postalCode);
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                name: 'status_pernikahan'
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".marriage_id").empty()
                                $(".marriage_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".marriage_id").append(`<option value="${item.id}">${item.value}</option>`)
                                })

                                $(".marriage_id").val(res?.data?.marriage_id).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                name: 'religion'
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".religion_id").empty()
                                $(".religion_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".religion_id").append(`<option value="${item.id}">${item.value}</option>`)
                                })

                                $(".religion_id").val(res?.data?.religion_id).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("divisi/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".division_id").empty()
                                $(".division_id").val("").change()
                                $(".divisionid").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".division_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                                })

                                $(".division_id").val(res?.data?.division_id);
                                $(".add-modal").modal("show")
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("jabatan/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".jabatan_id").empty()
                                $(".jabatan_id").val("").change()
                                $(".jabatanid").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".jabatan_id").append(`<option value="${item.id}">${item.jabatan_name}</option>`)
                                })

                                $(".jabatan_id").val(res?.data?.jabatan_id);
                                $(".add-modal").modal("show")
                            }
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })
    })

    $(document).on('show.bs.modal', '.detail-modal', function() {
        document.getElementById("add_modal").style = "display: block; z-index: 999 !important";
    })

    $(document).on('hide.bs.modal', '.detail-modal', function() {
        document.getElementById("add_modal").style = "display: block;";
        $(".add-modal").css("overflow-y", "auto");
    })

    $(document).on('click', '.delete-detail', function() {
        let id = $(".id_detail").val()
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                let new_komponen_gaji = []
                let tag_html = "";

                row = 0;

                $(".body-detail-table").empty()

                komponen_gaji.map(item => {
                    if (item.row != id) {
                        tag_html += `<tr class="edit-table-detail" data-row ="${row + 1}" data-id ="${item.id}" data-tunjanganid ="${item.tunjangan_id}" data-roleid ="${item.nominal}">`;
                        tag_html += "<td>";
                        tag_html += item.tunjangan_name;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.nominal;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_komponen_gaji.push({
                            ...item,
                            row: row + 1
                        });

                        row = row + 1;
                    }
                })

                komponen_gaji = new_komponen_gaji;

                $(".body-detail-table").append(tag_html)
                $(".detail-modal").modal("hide")

                $(".detail-modal").modal("hide")
            }
        })
    })

    $(document).on('click', '.edit-table-detail', function() {
        $(".title-detail-name").text("Update")
        $(".delete-detail").css('display', '');
        let tunjangan_id = $(this).data('tunjanganid')
        let nominal = $(this).data('nominal')
        let id = $(this).data('id')

        $(".id_detail").val(id)

        validator_detail.resetForm();
        validator_detail.reset();

        $.ajax({
            url: `<?= base_url("tunjangan/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".tunjangan_id").empty()
                $(".jabatan_id").val("").change()
                $(".tunjangan_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".tunjangan_id").append(`<option value="${item.id}">${item.name}</option>`)
                })

                $(".tunjangan_id").val(tunjangan_id).change();
                $(".detail-modal").modal("show")
            }
        })
    })
    //change picture
    const previewPhoto = function() {
        let file = document.getElementById("employeeImg").files[0];
        document.getElementById("preview_photo").src = window.URL.createObjectURL(file);
    }

    const getCity = function() {
        const id = $(".province_id option:selected").val()

        if (id) {
            $.ajax({
                url: `<?= base_url("city"); ?>/${id}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".city_id").empty()
                    $(".city_id").val("").change()
                    $(".city_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".city_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                    })
                }
            })
        }
    }

    const getZipCode = function() {
        //        $(".zip_code").val($(".city_id option:selected").attr("data-code"))
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>
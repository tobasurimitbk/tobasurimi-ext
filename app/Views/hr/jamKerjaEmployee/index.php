<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 id="title">Data Jam Kerja Karyawan</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("employee"); ?>">
                Kembali
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mt-0">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Karyawan</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled value="<?= $employee['nip'] ?>" class="form-control " id="" name="" aria-label="Floating label select example" />
                        <label for="floatingInput" style="z-index: 1;">NIP</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled value="<?= $employee['name'] ?>" class="form-control " id="" name="" aria-label="Floating label select example" />
                        <label for="floatingInput" style="z-index: 1;">Nama Karyawan</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled value="<?= $employee['divisi'] ?>" class="form-control" id="" name="" aria-label="Floating label select example" />
                        <label for="floatingInput" style="z-index: 1;">Departemen</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled value="<?= $employee['nama_bagian'] ?>" class="form-control" id="" name="" aria-label="Floating label select example" />
                        <label for="floatingInput" style="z-index: 1;">Bagian</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled value="<?= strtoupper($employee['tipe']) ?>" class="form-control " id="" name="" aria-label="Floating label select example" />
                        <label for="floatingInput" style="z-index: 1;">Tipe Karyawan</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled value="<?= strtoupper($employee['jabatan_name']) ?>" class="form-control " id="" name="" aria-label="Floating label select example" />
                        <label for="floatingInput" style="z-index: 1;">Jabatan Karyawan</label>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Jam Kerja Karyawan Per Bulan</label>
                </div>
            </div>

            <div class="row justify-content-end mb-3">
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" id="bulan" type="month" placeholder="Search" value="<?= @$_GET['bulan'] ?>" />
                </div>
            </div>

            <div class="table-responsive mt-1" style="margin-top: -10px;">
                <table class="table table-bordered nowrap table-striped table-hover-tobasurimi dataTable table-form-tts" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: center; width:10px;">No</th>
                            <th style="text-align: center;">Tanggal</th>
                            <th style="text-align: center;">Jam Kerja</th>
                            <th style="text-align: center;">Masuk</th>
                            <!-- <th style="text-align: center;">Mulai Istirahat</th>
                            <th style="text-align: center;">Selesai Istirahat</th> -->
                            <th style="text-align: center;">Pulang</th>
                            <th style="text-align: center; width:10px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table">
                        <?php $i = 1; ?>
                        <?php foreach ($detail as $d) : ?>
                            <tr style="color:whitesmoke;">
                                <td style="text-align: center;"><?= $i++ ?></td>
                                <td style="text-align: center;"><?= $d['tanggal_text'] ?></td>
                                <td style="text-align: center;"><?= @$d['jam_kerja']['jenis'] ?></td>
                                <td style="text-align: center;"><?= @$d['jam_kerja']['jam_masuk'] ?></td>
                                <!-- <td style="text-align: center;"><?= @$d['jam_kerja']['jam_istirahat_mulai'] ?></td>
                                <td style="text-align: center;"><?= @$d['jam_kerja']['jam_istirahat_selesai'] ?></td> -->
                                <td style="text-align: center;"><?= @$d['jam_kerja']['jam_pulang'] ?></td>
                                <td style="text-align: center;">
                                    <a href="#" data-jam_kerja_id="<?= @$d['jam_kerja']['jam_kerja_id'] ?>" data-tanggal_text="<?= $d['tanggal_text'] ?>" data-tanggal="<?= $d['tanggal'] ?>" data-toggle="tooltip" title="Atur Jam Kerja" class="btn btn-primary btn-update-modal">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if ($detail == null) : ?>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="8" style="text-align: center;">
                                    Pilih Bulan Terlebih Dahulu
                                </td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- MODAL UPDATE -->
<div class="modal add-modal" id="update_jam_kerja_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Jam Kerja</h5>
            </div>
            <div class="modal-body">
                <form class="form-update-jam-kerja" role="form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" class="tanggal" name="tanggal" id="tanggal">
                    <input type="hidden" value="<?= $employee['id'] ?>" class="employee_id" name="employee_id" id="employee_id">
                    <input type="hidden" value="<?= $employee['division_id'] ?>" class="divisi_id" name="divisi_id" id="divisi_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control tanggal_text" id="tanggal_text" name="tanggal_text" placeholder="Kode Barang">
                                <label for="floatingInput">Tanggal</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select jam_kerja_id" id="jam_kerja_id" name="jam_kerja_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($jamKerja as $j) : ?>
                                        <option value="<?= $j['id'] ?>">
                                            <?= $j['jenis'] . " - " . $j['shift'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                </select>
                                <label style="z-index: 1;">Pilih Jam Kerja</label>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered nowrap table-striped table-hover-tobasurimi dataTable table-form-tts" id="dataTable2" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px;">No</th>
                                <th style="text-align: center;">Hari</th>
                                <th style="text-align: center;">Masuk</th>
                                <!-- <th style="text-align: center;">Mulai Istirahat</th>
                                <th style="text-align: center;">Selesai Istirahat</th> -->
                                <th style="text-align: center;">Pulang</th>
                            </tr>
                        </thead>
                        <tbody class="body-table">
                        </tbody>
                        <tfoot></tfoot>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-modal mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('.btn-update-modal').click(function(e) {
        e.preventDefault();
        var selected = $(this);
        $('#tanggal').val(selected.data('tanggal'));
        $('#tanggal_text').val(selected.data('tanggal_text'));
        $('#jam_kerja_id').val(selected.data('jam_kerja_id')).change();
        getListDetailJamKerja();
        $('#update_jam_kerja_modal').modal('show');

    });

    $('.btn-discard-modal').click(function(e) {
        e.preventDefault();
        $('#update_jam_kerja_modal').modal('hide');
    });


    $('#jam_kerja_id').select2({
        placeholder: "Pilih Jam Kerja",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListDetailJamKerja();
    });

    $("#jam_kerja_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#bulan').change(function(e) {
        e.preventDefault();
        var bulan = $(this).val();
        console.log(bulan);
        if (bulan) {
            window.location.href = "<?= base_url('employee/jam-kerja/' . encrypt($employee['id'])) ?>" + "?bulan=" +
                bulan
        }
    });

    var validator = $(".form-update-jam-kerja").validate({
        rules: {
            jam_kerja_id: {
                required: true
            },
        },
        messages: {
            jam_kerja_id: {
                required: "Jam Kerja wajib diisi"
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


    $('.btn-submit-detail').click(function(e) {
        e.preventDefault();
        if ($('.form-update-jam-kerja').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Update Jam Kerja ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                var data = new FormData(document.querySelector(".form-update-jam-kerja"));
                $.ajax({
                    url: "<?= base_url("employee/update-jam-kerja"); ?>",
                    data: data,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function() {
                        stopLoading()
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        }
                    },
                });
            })
        }
    });

    function getListDetailJamKerja() {
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
                jam_kerja_id: $(".jam_kerja_id option:selected").val(),
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
                        // newRow.append($('<td style="text-align: center;">').text(v.jam_istirahat_mulai));
                        // newRow.append($('<td style="text-align: center;">').text(v.jam_istirahat_selesai));
                        newRow.append($('<td style="text-align: center;">').text(v.jam_pulang));
                        table.find('tbody').append(newRow);
                    });
                }
            }
        });

    }
</script>

<?= $this->endSection(); ?>
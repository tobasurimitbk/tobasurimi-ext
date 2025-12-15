<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Update" : "Tambah"; ?> Custom Gaji Harian</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("custom-gaji-harian"); ?>">
                Kembali
            </a>
            <?php if (!empty($customGajiHarian)) : ?>
                <?php if (can('Personalia', 'Custom Gaji Harian', 'u')): ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit">
                        Update
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <button class="btn btn-show-form btn-save float-right btn-submit">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" class="id" name="id" id="id" value="<?= !empty($customGajiHarian) ? encrypt($customGajiHarian['id']) : "" ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select employee_id" name="employee_id" id="employee_id">
                                <option value="">
                                    Cari Nama Karyawan
                                </option>
                                <?php if (!empty($customGajiHarian)): ?>
                                    <option value="<?= $customGajiHarian['employee_id'] ?>" selected>
                                        (<?= $customGajiHarian['nip'] ?>) <?= $customGajiHarian['employee_name'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Cari Nama Karyawan</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input value="<?= (!empty($customGajiHarian)) ? date('d/m/Y', strtotime($customGajiHarian['tanggal'])) : "" ?>" type="text" class="form-control tanggal" id="tanggal" name="tanggal" placeholder="Tanggal">
                                <label for="floatingInput">Tanggal</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button disabled class="btn btn-secondary" type="button">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-2">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($customGajiHarian)) ? $customGajiHarian['checkin'] : "" ?>" type="text" class="form-control checkin" id="checkin" name="checkin" placeholder="Check In" readonly>
                            <label for="floatingInput">Check In</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($customGajiHarian)) ? $customGajiHarian['checkout'] : "" ?>" type="text" class="form-control checkout" id="checkout" name="checkout" placeholder="Check Out" readonly>
                            <label for="floatingInput">Check Out</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($customGajiHarian)) ? $customGajiHarian['total_jam'] : "" ?>" type="text" class="form-control total_jam" id="total_jam" name="total_jam" placeholder="Total Jam" readonly>
                            <label for="floatingInput">Total Jam</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($customGajiHarian)) ? number_format($customGajiHarian['nominal'], 2) : "" ?>" type="text" class="form-control nominal" oninput="this.value = greatFormatRupiah(this.value)" id="nominal" name="nominal" placeholder="Nominal">
                            <label for="floatingInput">Nominal</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea style="height: 90px;" class="form-control keterangan text-area-all" id="keterangan" name="keterangan" placeholder="Keterangan"><?= (!empty($customGajiHarian) ? $customGajiHarian['keterangan'] : "") ?></textarea>
                            <label for="floatingInput">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <br>
        </div>
    </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let csrf = $(`[name="${csrfToken}"]`);
    $('.employee_id').select2({
        placeholder: "Cari Karyawan",
        theme: "bootstrap-5",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "<?= base_url('custom-gaji-harian/like-employees') ?>",
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


    $('.employee_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.employee_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    var validator = $(".create-form").validate({
        rules: {
            employee_id: {
                required: true
            },
            tanggal: {
                required: true
            },
            checkin: {
                required: true
            },
            // checkout: {
            //     required: true
            // },
            nominal: {
                required: true
            },
        },
        messages: {
            employee_id: {
                required: "Nama Karyawan wajib diisi"
            },
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            checkin: {
                required: "Checkin wajib diisi"
            },
            // checkout: {
            //     required: "Checkout wajib diisi"
            // },
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

    $(".btn-submit").click(function() {
        if ($(".create-form").valid()) {
            let id = $(".id").val();
            let data = new FormData(document.querySelector(".create-form"));
            let url = id == '' ? "<?= base_url("custom-gaji-harian/save"); ?>" : "<?= base_url("custom-gaji-harian/update"); ?>";
            let nominal = destroyFormatRupiah($('#nominal').val());
            data.set('nominal', nominal);

            $.ajax({
                url: url,
                data: data,
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
                    csrf.val(response.token);
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showCancelButton: true,
                            showDenyButton: true,
                            confirmButtonText: 'Buat Lagi',
                            denyButtonText: 'Tidak Buat Lagi',
                            confirmButtonColor: '#4e73df',
                            denyButtonColor: '#dc3545',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Tidak Buat Lagi Reset Form
                                $('#employee_id').val(null).change();
                                $('#tanggal').val(null);
                                $('#nominal').val(null);
                                $('#checkin').val(null);
                                $('#checkout').val(null);
                                $('#total_jam').val(null);
                                $('#keterangan').val(null);
                            } else if (result.isDenied) {
                                // Buat Baru Lagi
                                window.location.href = "<?= base_url("custom-gaji-harian"); ?>";
                            }
                        });
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
    });

    $('#tanggal').change(function(e) {
        e.preventDefault();
        let tanggal = $(this).val();
        let employeeId = $('#employee_id option:selected').val();

        if (tanggal == '') {
            Swal.fire({
                icon: 'error',
                title: 'isi tanggal dulu',
                confirmButtonColor: '#4e73df',
            });
            return;
        } else if (employeeId == '') {
            Swal.fire({
                icon: 'error',
                title: 'pilih karyawan dulu',
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            $.ajax({
                url: "<?= base_url("custom-gaji-harian/get-attendance"); ?>",
                data: {
                    employee_id: employeeId,
                    tanggal: tanggal
                },
                method: "GET",
                success: function(response) {
                    if (response.status == false) {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        });
                        $('#checkout').val(null);
                        $('#checkin').val(null);
                        $('#total_jam').val(null);

                        return;
                    } else {
                        var data = response.data;
                        var totalJam = parseFloat(data.total_jam).toFixed(2);
                        $('#checkout').val(data.checkout);
                        $('#checkin').val(data.checkin);
                        $('#total_jam').val(totalJam);
                        $('#nominal').val(greatFormatRupiah(data.nominal));
                    }

                }
            });
        }


    });
</script>

<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include('header.php') ?>
    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold;">
                BC 4.0 - PEMBERITAHUAN PEMASUKAN BARANG ASAL TEMPAT LAIN DALAM DAERAH PABEAN KE TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-entitas">
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pengusaha TPB / Pengusaha Kena Pajak
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengusaha_tpb_npwp" id="pengusaha_tpb_npwp" name="pengusaha_tpb_npwp" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($pengusahaTPB as $p) : ?>
                                            <option <?= $bcEntitas != null ? ($p['npwp'] == $bcEntitas['nomor_identitas'] ? 'selected' : '')  : '' ?> value="<?= $p['npwp'] ?>" data-nama_pengusaha="<?= $p['nama_pengusaha'] ?>" data-alamat="<?= $p['alamat'] ?>" data-nib="<?= $p['nib'] ?>" data-id="<?= $p['id'] ?>">
                                                <?= $p['npwp'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">NPWP</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="pengusaha_tpb_nitku" value="<?= $bcEntitas == null ? "" : ($bcEntitas['nitku_entitas'] != null ? $bcEntitas['nitku_entitas'] : "")  ?>" name="pengusaha_tpb_nitku" type="text" class="form-control pengusaha_tpb_nitku" placeholder="">
                                    <label>NITKU</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="pengusaha_tpb_nama" value="<?= $bcEntitas == null ? "" : ($bcEntitas['nama_entitas'] != null ? $bcEntitas['nama_entitas'] : "")  ?>" name="pengusaha_tpb_nama" type="text" class="form-control pengusaha_tpb_nama" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="pengusaha_tpb_alamat" id="pengusaha_tpb_alamat" class="form-control pengusaha_tpb_alamat" style="height: 100px;"><?= ($bcEntitas == null) ? "" : ($bcEntitas['alamat_entitas'] != null ? $bcEntitas['alamat_entitas'] : "") ?></textarea>
                                    <label>Alamat</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengusaha_tpb_nomor_ijin_tpb" id="pengusaha_tpb_nomor_ijin_tpb" name="pengusaha_tpb_nomor_ijin_tpb" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php if (!empty($bcEntitas)) : ?>
                                            <option value="<?= $bcEntitas['nomor_ijin_entitas'] ?>" selected>
                                                <?= $bcEntitas['nomor_ijin_entitas'] ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                    <label style="z-index: 1;">Nomor Izin TPB</label>
                                </div>
                            </div>
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input disabled readonly value="<?= $bcEntitas == null ? '' : ($bcEntitas['tanggal_ijin_entitas'] != null ? date('d/m/Y', strtotime($bcEntitas['tanggal_ijin_entitas'])) : '') ?>" autocomplete="one-time-code" name="pengusaha_tpb_tanggal_skep_tpb" type="text" placeholder="" class="form-control pengusaha_tpb_tanggal_skep_tpb" id="pengusaha_tpb_tanggal_skep_tpb">
                                    <label>Tanggal Skep TPB</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="pengusaha_tpb_nib" value="<?= $bcEntitas == null ? '' : ($bcEntitas['nib_entitas'] != null ? $bcEntitas['nib_entitas'] : '') ?>" name="pengusaha_tpb_nib" type="text" class="form-control pengusaha_tpb_nib" placeholder="">
                                <label>NIB</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pengirim
                            </label>
                            <div class="mt-1">
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <input id="pengirim_npwp" value="<?= $bcEntitas != null ? $bcEntitas['npwp_pemasok'] : $bc40['no_ktp'] ?>" name="pengirim_npwp" type="number" class="form-control pengirim_npwp" placeholder="">
                                        <label>NPWP / NO KTP</label>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <input id="pengirim_nitku" value="<?= $bcEntitas == null ? (!empty($bc40['no_ktp']) ? $bc40['no_ktp'] . "000000" : "") : ($bcEntitas['nitku_pemasok'] != null ? $bcEntitas['nitku_pemasok'] : "")  ?>" name="pengirim_nitku" type="text" class="form-control pengirim_nitku" placeholder="">
                                        <label>NITKU</label>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <input id="pengirim_nama" value="<?= $bcEntitas != null ? $bcEntitas['nama_pemasok'] : $bc40['name']  ?>" name="pengirim_nama" type="text" class="form-control pengirim_nama" placeholder="">
                                        <label>Nama</label>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <textarea name="pengirim_alamat" id="pengirim_alamat" class="form-control pengirim_alamat" style="height: 100px;"><?= $bcEntitas != null ? $bcEntitas['alamat_pemasok'] : $bc40['address']  ?></textarea>
                                        <label>Alamat</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-4">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pemilik Barang
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="pemilik_barang_npwp" value="<?= $bcEntitas == null ? '' : ($bcEntitas['npwp_pemilik_barang'] == null ? '' : $bcEntitas['npwp_pemilik_barang'])  ?>" name="pemilik_barang_npwp" type="number" class="form-control pemilik_barang_npwp" placeholder="">
                                    <label>NPWP</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="pemilik_barang_nitku" value="<?= $bcEntitas == null ? "" : ($bcEntitas['nitku_pemilik_barang'] != null ? $bcEntitas['nitku_pemilik_barang'] : "")  ?>" name="pemilik_barang_nitku" type="text" class="form-control pemilik_barang_nitku" placeholder="">
                                    <label>NITKU</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="pemilik_barang_nama" value="<?= $bcEntitas == null ? '' : ($bcEntitas['nama_pemilik_barang'] == null ? '' : $bcEntitas['nama_pemilik_barang']) ?>" name="pemilik_barang_nama" type="text" class="form-control pemilik_barang_nama" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="pemilik_barang_alamat" id="pemilik_barang_alamat" class="form-control pemilik_barang_alamat" style="height: 100px;"><?= ($bcEntitas == null) ? '' : ($bcEntitas['alamat_pemilik_barang'] == null ? '' : $bcEntitas['alamat_pemilik_barang']) ?></textarea>
                                    <label>Alamat</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-primary" id="btn-simpan-perubahan" style="float: right;">
                        Simpan Perubahan
                    </a>
                    <button class="btn btn-primary" type="button" disabled id="btn-loading" style="float: right;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading
                    </button>
                </form>
            </div>
        </div>
    </div>

</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);


    $('#pengusaha_tpb_nomor_ijin_tpb').select2({
        placeholder: "Pilih No Ijin TPB",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $(this).find('option:selected');
        $('#pengusaha_tpb_tanggal_skep_tpb').val(selected.data('tanggal_skep_tpb'));
        $('#pemilik_barang_alamat').val(selected.data('alamat_pemilik_barang'));
    });

    $('#pengusaha_tpb_npwp').select2({
        placeholder: "Pilih No NPWP Perusahaan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $(this).find('option:selected');
        var npwpPengusaha = $(this).val();
        var namaPengusaha = selected.data('nama_pengusaha');
        var alamatPengusaha = selected.data('alamat');
        var nibDefault = selected.data('nib');

        $('#pengusaha_tpb_nama').val(namaPengusaha);
        $('#pengusaha_tpb_alamat').val(alamatPengusaha);
        $('#pengusaha_tpb_nib').val(nibDefault)
        $('#pemilik_barang_npwp').val(npwpPengusaha)
        $('#pemilik_barang_nama').val(namaPengusaha);
        $('#pemilik_barang_alamat').val(alamatPengusaha);

        // UPDATE NITKU
        // NITKU NPWP + 6 DIGIT 0
        var nol = "000000";
        $('#pengusaha_tpb_nitku').val(npwpPengusaha + '' + nol);
        $('#pemilik_barang_nitku').val(npwpPengusaha + '' + nol);

        // DROPDOPWN NOMOR IZIN TPB
        getListNoIjinTPB();

    });


    $("#pengusaha_tpb_tanggal_skep_tpb").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var validatorEntitas = $("#form-entitas").validate({
        rules: {
            pengusaha_tpb_npwp: {
                required: true
            },
            pengusaha_tpb_nama: {
                required: true
            },
            pengusaha_tpb_alamat: {
                required: true
            },
            pengusaha_tpb_nomor_ijin_tpb: {
                required: true
            },
            pengusaha_tpb_tanggal_skep_tpb: {
                required: true
            },
            pengusaha_tpb_nib: {
                required: true
            },
            pengirim_npwp: {
                required: true
            },
            pengirim_nama: {
                required: true
            },
            pengirim_alamat: {
                required: true
            },
            pemilik_barang_npwp: {
                required: true
            },
            pemilik_barang_nama: {
                required: true
            },
            pemilik_barang_alamat: {
                required: true
            }
        },
        messages: {
            pengusaha_tpb_npwp: {
                required: "Npwp pengusaha TPB wajib diisi"
            },
            pengusaha_tpb_nama: {
                required: "Nama pengusaha TPB wajib diisi"
            },
            pengusaha_tpb_alamat: {
                required: "Alamat pengusaha TPB wajib diisi"
            },
            pengusaha_tpb_nomor_ijin_tpb: {
                required: "Nomor izin TPB pengusaha wajib diisi"
            },
            pengusaha_tpb_tanggal_skep_tpb: {
                required: "Tanggal SKEP TPB pengusaha wajib diisi"
            },
            pengusaha_tpb_nib: {
                required: "NIB pengusaha TPB wajib diisi"
            },
            pengirim_npwp: {
                required: "Npwp / no ktp pengirim wajib diisi"
            },
            pengirim_nama: {
                required: "Nama pengirim wajib diisi"
            },
            pengirim_alamat: {
                required: "Alamat pengirim wajib diisi"
            },
            pemilik_barang_npwp: {
                required: "Npwp pemilik barang wajib diisi"
            },
            pemilik_barang_nama: {
                required: "Nama pemilik barang wajib diisi"
            },
            pemilik_barang_alamat: {
                required: "Alamat pemilik barang wajib diisi"
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

    $('#btn-loading').hide();

    $('#btn-simpan-perubahan').click(function() {
        if ($('#form-entitas').valid()) {
            var formData = new FormData(document.querySelector("#form-entitas"));
            formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
            formData.append("pengusaha_tpb_tanggal_skep_tpb", $('#pengusaha_tpb_tanggal_skep_tpb').val());
            $.ajax({
                url: "<?= base_url("bea-cukai-bc-40/id/entitas"); ?>",
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    $('#btn-loading').show();
                    $('#btn-simpan-perubahan').hide();
                },
                complete: function() {
                    $('#btn-loading').hide();
                    $('#btn-simpan-perubahan').show();
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

        }
    });

    function getListNoIjinTPB() {

        $.ajax({
            url: `<?= base_url('bea-cukai-bc-40/list-no-ijin-tpb'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                pengusaha_tpb_id: $(".pengusaha_tpb_npwp option:selected").data('id'),
            },
            dataType: "json",
            success: function(res) {
                $(".pengusaha_tpb_nomor_ijin_tpb").empty()
                $(".pengusaha_tpb_nomor_ijin_tpb").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".pengusaha_tpb_nomor_ijin_tpb").append(`<option data-alamat_pemilik_barang="${item.alamat_pemilik_barang}" data-tanggal_skep_tpb="${item.tanggal_skep_tpb}"  value="${item.no_ijin_tpb}">${item.no_ijin_tpb}</option>`)
                })
                $(".pengusaha_tpb_nomor_ijin_tpb").val();
            }
        });
    }
</script>


<?= $this->endSection(); ?>
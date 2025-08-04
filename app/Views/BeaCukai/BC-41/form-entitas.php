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
                BC 4.1 - PEMBERITAHUAN PENGELUARAN KEMBALI BARANG ASAL TEMPAT LAIN DALAM DAERAH PABEAN DARI TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-entitas">
                    <input type="hidden" name="id" value="<?= encrypt($bc41['id']) ?>">
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Penyelenggara/Pengusaha TPB/Pengusaha Kena Pajak
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select entitas_npwp_pengusaha" id="entitas_npwp_pengusaha" name="entitas_npwp_pengusaha" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($pengusahaTPB as $p) : ?>
                                            <option <?= count($payload->entitas) != 0 ? ($payload->entitas[0]->nomorIdentitas == $p['npwp'] ? 'selected' : '') : '' ?> value="<?= $p['npwp'] ?>" data-nama_pengusaha="<?= $p['nama_pengusaha'] ?>" data-alamat="<?= $p['alamat'] ?>" data-nib="<?= $p['nib'] ?>" data-id="<?= $p['id'] ?>">
                                                <?= $p['npwp'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">NPWP</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_nama_pengusaha" value="<?= count($payload->entitas) != 0 ? $payload->entitas[0]->namaEntitas : '' ?>" name="entitas_nama_pengusaha" type="text" class="form-control entitas_nama_pengusaha" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="entitas_alamat_pengusaha" id="entitas_alamat_pengusaha" class="form-control entitas_alamat_pengusaha" style="height: 100px;"><?= count($payload->entitas) != 0 ? $payload->entitas[0]->alamatEntitas : '' ?></textarea>
                                    <label>Alamat</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select entitas_nomor_ijin_tpb" id="entitas_nomor_ijin_tpb" name="entitas_nomor_ijin_tpb" aria-label="Floating label select example">
                                        <option selected value="<?= count($payload->entitas) != 0 ? $payload->entitas[0]->nomorIjinEntitas : '' ?>"><?= count($payload->entitas) != 0 ? $payload->entitas[0]->nomorIjinEntitas : '' ?></option>
                                    </select>
                                    <label style="z-index: 1;">Nomor Izin TPB</label>
                                </div>
                            </div>
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input disabled readonly value="<?= count($payload->entitas) == 0 ? "" : date('d/m/Y', strtotime(count($payload->entitas) == 0 ? $payload->entitas[0]->tanggalIjinEntitas : '')) ?>" autocomplete="one-time-code" name="entitas_tanggal_skep_tpb" type="text" placeholder="" class="form-control entitas_tanggal_skep_tpb" id="entitas_tanggal_skep_tpb">
                                    <label>Tanggal Skep TPB</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="entitas_nib" value="<?= count($payload->entitas) == 0 ? "" : $payload->entitas[0]->nibEntitas ?>" name="entitas_nib" type="text" class="form-control entitas_nib" placeholder="">
                                <label>NIB</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Penerima Barang/Pembeli Barang Kena Pajak/Penerima Jasa Kena Pajak
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_npwp_penerima_barang" value="<?= count($payload->entitas) == 0 ? "" : $payload->entitas[2]->nomorIdentitas ?>" name="entitas_npwp_penerima_barang" type="number" class="form-control entitas_npwp_penerima_barang" placeholder="">
                                    <label>NPWP</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_nama_penerima_barang" value="<?= count($payload->entitas) == 0 ? "" : $payload->entitas[2]->namaEntitas ?>" name="entitas_nama_penerima_barang" type="text" class="form-control entitas_nama_penerima_barang" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="entitas_alamat_penerima_barang" id="entitas_alamat_penerima_barang" class="form-control entitas_alamat_penerima_barang" style="height: 100px;"><?= count($payload->entitas) == 0 ? "" : $payload->entitas[2]->alamatEntitas ?></textarea>
                                    <label>Alamat</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pemilik Barang
                            </label>
                            <div class="mt-1">
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <input id="entitas_npwp_pemilik_barang" value="<?= count($payload->entitas) == 0 ? "" : $payload->entitas[1]->nomorIdentitas ?>" name="entitas_npwp_pemilik_barang" type="number" class="form-control entitas_npwp_pemilik_barang" placeholder="">
                                        <label>NPWP</label>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <input id="entitas_nama_pemilik_barang" value="<?= count($payload->entitas) == 0 ? "" : $payload->entitas[1]->namaEntitas ?>" name="entitas_nama_pemilik_barang" type="text" class="form-control entitas_nama_pemilik_barang" placeholder="">
                                        <label>Nama</label>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <textarea name="entitas_alamat_pemilik_barang" id="entitas_alamat_pemilik_barang" class="form-control entitas_alamat_pemilik_barang" style="height: 100px;"><?= count($payload->entitas) == 0 ? "" : $payload->entitas[1]->alamatEntitas ?></textarea>
                                        <label>Alamat</label>
                                    </div>
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


    $('#entitas_nomor_ijin_tpb').select2({
        placeholder: "Pilih No Ijin TPB",
        theme: "bootstrap-5",
    }).change(function() {
        var selected = $(this).find('option:selected');
        $('#entitas_tanggal_skep_tpb').val(selected.data('tanggal_skep_tpb'));
        $('#entitas_alamat_pemilik_barang').val(selected.data('alamat_pemilik_barang'));
    });

    $("#entitas_tanggal_skep_tpb").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#entitas_npwp_pengusaha').select2({
        placeholder: "Pilih No NPWP Perusahaan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $(this).find('option:selected');
        var npwpPengusaha = $(this).val();
        var namaPengusaha = selected.data('nama_pengusaha');
        var alamatPengusaha = selected.data('alamat');
        var nibDefault = selected.data('nib');

        $('#entitas_nama_pengusaha').val(namaPengusaha);
        $('#entitas_alamat_pengusaha').val(alamatPengusaha);
        $('#entitas_npwp_pemilik_barang').val(npwpPengusaha)
        $('#entitas_nama_pemilik_barang').val(namaPengusaha);
        $('#entitas_alamat_pemilik_barang').val(alamatPengusaha);
        $('#entitas_nib').val(nibDefault);
        // DROPDOPWN NOMOR IZIN TPB
        getListNoIjinTPB();

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
            entitas_npwp_pengusaha: {
                required: true
            },
            entitas_nama_pengusaha: {
                required: true
            },
            entitas_alamat_pengusaha: {
                required: true
            },
            entitas_nomor_ijin_tpb: {
                required: true
            },
            entitas_tanggal_skep_tpb: {
                required: true
            },
            entitas_nib: {
                required: true
            },
            entitas_npwp_pemilik_barang: {
                required: true
            },
            entitas_nama_pemilik_barang: {
                required: true
            },
            entitas_alamat_pemilik_barang: {
                required: true
            },
            entitas_npwp_penerima_barang: {
                required: true
            },
            entitas_nama_penerima_barang: {
                required: true
            },
            entitas_alamat_penerima_barang: {
                required: true
            }
        },
        messages: {
            entitas_npwp_pengusaha: {
                required: "Npwp pengusaha wajib diisi"
            },
            entitas_nama_pengusaha: {
                required: "Nama pengusaha wajib diisi"
            },
            entitas_alamat_pengusaha: {
                required: "Alamat wajib diisi"
            },
            entitas_nomor_ijin_tpb: {
                required: "Nomor ijin TPB wajib diisi"
            },
            entitas_tanggal_skep_tpb: {
                required: "Tanggal skep TPB wajib diisi"
            },
            entitas_nib: {
                required: "NIB wajib diisi"
            },
            entitas_npwp_pemilik_barang: {
                required: "NPWP pemilik Wajib diisi"
            },
            entitas_nama_pemilik_barang: {
                required: "Nama pemilik barang wajib diisi"
            },
            entitas_alamat_pemilik_barang: {
                required: "Alamat pemilik barang wajib diisi"
            },
            entitas_npwp_penerima_barang: {
                required: "NPWP penerima barang wajib diisi"
            },
            entitas_nama_penerima_barang: {
                required: "Nama penerima barang wajib diisi"
            },
            entitas_alamat_penerima_barang: {
                required: "Alamat penerima barang wajib diisi"
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
            Swal.fire({
                icon: 'question',
                title: 'Simpan Entitas ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-entitas"));
                    formData.append("entitas_tanggal_skep_tpb", $('#entitas_tanggal_skep_tpb').val());
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-41/id/entitas"); ?>",
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
            })

        }
    });

    function getListNoIjinTPB() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-23/list-no-ijin-tpb'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                pengusaha_tpb_id: $(".entitas_npwp_pengusaha option:selected").data('id'),
            },
            dataType: "json",
            success: function(res) {
                $(".entitas_nomor_ijin_tpb").empty()
                $(".entitas_nomor_ijin_tpb").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".entitas_nomor_ijin_tpb").append(`<option data-alamat_pemilik_barang="${item.alamat_pemilik_barang}" data-tanggal_skep_tpb="${item.tanggal_skep_tpb}"  value="${item.no_ijin_tpb}">${item.no_ijin_tpb}</option>`)
                })
                $(".entitas_nomor_ijin_tpb").val();
            }
        });
    }
</script>


<?= $this->endSection(); ?>
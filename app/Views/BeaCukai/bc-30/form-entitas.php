<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>
<!-- modal tambah pemilik barang -->
<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Tambah Pemilik</h5>
            </div>
            <div class="modal-body">
                <form id="entitas-pemilik-form" class="entitas-pemilik-form">
                    <div class="mt-1">


                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select tambah_pemilik_kode_jenis_entitas" id="tambah_pemilik_kode_jenis_entitas" name="tambah_pemilik_kode_jenis_entitas" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="0">0 - NPWP 12 DIGIT</option>
                                        <option value="1">1 - NPWP 10 digit</option>
                                        <option value="2">2 - PASSPOR</option>
                                        <option value="3">3 - KTP</option>
                                        <option value="4">4 - LAINNYA</option>
                                        <option value="5">5 - NPWP 15 DIGIT</option>
                                    </select>
                                    <label style="z-index: 1;">Kode Identitas </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input id="tambah_nomor_pemilik_barang" value="" name="tambah_nomor_pemilik_barang" type="text" class="tambah_nomor_pemilik_barang form-control" placeholder="">
                                    <label>Nomor Identitas</label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="tambah_nama_pemilik_barang" value="" name="tambah_nama_pemilik_barang" type="text" class="form-control tambah_nama_pemilik_barang" placeholder="">
                            <label>Nama</label>
                        </div>
                    </div>
                    <div class="mt-1">

                        <div class="form-floating mb-3">
                            <textarea name="tambah_alamat_pemilik_barang" id="tambah_alamat_pemilik_barang" class="form-control tambah_alamat_pemilik_barang" style="height: 100px;"></textarea>
                            <label>Alamat</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btn-tambah-data-pemilik-barang" class="btn btn-submit-form btn-submit-parent">Tambah</button>
            </div>
        </div>
    </div>
</div>


<section class="section section-form">
    <?php include('header.php') ?>
    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 3.0 - PEMBERITAHUAN EKSPOR BARANG </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <div class="mt-3">
                    <a href="#" class="btn btn-primary" id="btn-simpan-perubahan" style="float: right;">
                        Simpan Perubahan
                    </a>
                </div>
                <form id="form-entitas">
                    <input type="hidden" name="id" value="<?= encrypt($bc30['id']) ?>">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                EKSPORTIR
                            </label>
                            <div class="mt-1">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select entitas_kode_jenis_identitas_eksportir" id="entitas_kode_jenis_identitas_eksportir" name="entitas_kode_jenis_identitas_eksportir" aria-label="Floating label select example">
                                                <option value=""></option>
                                                <option value="0" <?= $payload->entitas[0]->kodeJenisIdentitas == 0 ? 'selected' : '' ?>>0 - NPWP 12 DIGIT</option>
                                                <option value="1" <?= $payload->entitas[0]->kodeJenisIdentitas == 1 ? 'selected' : '' ?>>1 - NPWP 10 digit</option>
                                                <option value="2" <?= $payload->entitas[0]->kodeJenisIdentitas == 2 ? 'selected' : '' ?>>2 - PASSPOR</option>
                                                <option value="3" <?= $payload->entitas[0]->kodeJenisIdentitas == 3 ? 'selected' : '' ?>>3 - KTP</option>
                                                <option value="4" <?= $payload->entitas[0]->kodeJenisIdentitas == 4 ? 'selected' : '' ?>>4 - LAINNYA</option>
                                                <option value="5" <?= $payload->entitas[0]->kodeJenisIdentitas == 5 ? 'selected' : '' ?>>5 - NPWP 15 DIGIT</option>
                                            </select>
                                            <label style="z-index: 1;">Kode Identitas </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">

                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select entitas_nomor_eksportir" id="entitas_nomor_eksportir" name="entitas_nomor_eksportir" aria-label="Floating label select example">
                                                <option value=""></option>
                                                <?php foreach ($pengusahaTPB as $p) : ?>
                                                    <option <?= $payload->entitas[0]->nomorIdentitas != null ? ($payload->entitas[0]->nomorIdentitas ? 'selected' : '')  : '' ?> value="<?= $p['npwp'] ?>" data-nama_pengusaha="<?= $p['nama_pengusaha'] ?>" data-alamat="<?= $p['alamat'] ?>" data-nib="<?= $p['nib'] ?>" data-id="<?= $p['id'] ?>">
                                                        <?= $p['npwp'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label style="z-index: 1;">Nomor Identitas</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_nama_eksportir" value="<?= count($payload->entitas) != 0 ? $payload->entitas[0]->namaEntitas : '' ?>" name="entitas_nama_eksportir" type="text" class="form-control entitas_nama_eksportir" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="entitas_alamat_eksportir" id="entitas_alamat_eksportir" class="form-control entitas_alamat_eksportir" style="height: 100px;"><?= count($payload->entitas) != 0 ? $payload->entitas[0]->alamatEntitas : '' ?></textarea>
                                    <label>Alamat</label>
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-6">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Penerima
                            </label>

                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_nama_penerima" value="<?= count($payload->entitas) == 0 ? "" : $payload->entitas[$indexPenerima]->namaEntitas ?>" name="entitas_nama_penerima" type="text" class="form-control entitas_nama_penerima" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="entitas_alamat_penerima" id="entitas_alamat_penerima" class="form-control entitas_alamat_penerima" style="height: 100px;"><?= count($payload->entitas) == 0 ? "" : $payload->entitas[$indexPenerima]->alamatEntitas ?></textarea>
                                    <label>Alamat</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select entitas_kode_negara_penerima" id="entitas_kode_negara_penerima" name="entitas_kode_negara_penerima" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeNegaraAsal as $k) : ?>
                                            <option <?= !empty($payload->entitas[$indexPenerima])  ? ($payload->entitas[$indexPenerima]->kodeNegara == $k['code'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['code']) ?>">
                                                <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Negara</label>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pembeli
                            </label>

                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_nama_pembeli" value="<?= count($payload->entitas) == 0 ? "" : $payload->entitas[$indexPembeli]->namaEntitas ?>" name="entitas_nama_pembeli" type="text" class="form-control entitas_nama_pembeli" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="entitas_alamat_pembeli" id="entitas_alamat_pembeli" class="form-control entitas_alamat_pembeli" style="height: 100px;"><?= count($payload->entitas) == 0 ? "" : $payload->entitas[$indexPembeli]->alamatEntitas ?></textarea>
                                    <label>Alamat</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select entitas_kode_negara_pembeli" id="entitas_kode_negara_pembeli" name="entitas_kode_negara_pembeli" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeNegaraAsal as $k) : ?>
                                            <option <?= !empty($payload->entitas[$indexPembeli])  ? ($payload->entitas[$indexPembeli]->kodeNegara == $k['code'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['code']) ?>">
                                                <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Negara</label>
                                </div>
                            </div>

                        </div>


                    </div>
                    <div class="row">
                        <div class="section-header">

                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pemilik Barang
                            </label>
                            <button class="btn btn-show-form btn-add float-right" id="btn-display-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>

                        <div class="table-responsive  mt-3">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-pemilik" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">Seri</th>
                                        <th style="text-align: center;">Nomor Identitas</th>
                                        <th style="text-align: center;">Alamat</th>
                                        <th style="text-align: center;">Nama</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($pemilik) == 0) : ?>
                                        <tr style="color: white; text-align:center;">
                                            <td colspan="5">Tidak ada Entitas Pemilik</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php $length = count($pemilik); ?>
                                        <?php foreach ($pemilik as $i => $p) : ?>
                                            <tr style="color: white; text-align:center;">
                                                <td><?= $p->nomor ?></td>
                                                <td><?= $p->nomorIdentitas ?></td>
                                                <td><?= $p->alamatEntitas ?></td>
                                                <td><?= $p->namaEntitas ?></td>
                                                <td>
                                                    <?php if ($i == $length - 1) : ?>
                                                        <button type="button" class="btn btn-danger" onclick="removeData(<?= $i ?>)"><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                                                    <?php else : ?>

                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>


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
    <?php if (count($payload->entitas) < 3) : ?>
        $('#btn-display-modal').css('display', 'none');
    <?php elseif (count($payload->entitas) > 3) : ?>
        $('#btn-display-modal').css('display', '');
    <?php endif; ?>

    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var no = 1;




    var validatorEntitasPemilik = $('#entitas-pemilik-form').validate({
        rules: {
            tambah_pemilik_kode_jenis_entitas: {
                required: true
            },
            tambah_nomor_pemilik_barang: {
                required: true
            },
            tambah_nama_pemilik_barang: {
                required: true
            },
            tambah_alamat_pemilik_barang: {
                required: true
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
    })

    var validatorEntitas = $("#form-entitas").validate({
        rules: {
            entitas_kode_jenis_identitas_eksportir: {
                required: true
            },
            entitas_nomor_eksportir: {
                required: true
            },
            entitas_nama_eksportir: {
                required: true
            },
            entitas_alamat_eksportir: {
                required: true
            },
            entitas_nama_penerima: {
                required: true
            },
            entitas_alamat_penerima: {
                required: true
            },
            entitas_kode_negara_penerima: {
                required: true
            },
            entitas_nama_pembeli: {
                required: true
            },
            entitas_alamat_pembeli: {
                required: true
            },
            entitas_kode_negara_pembeli: {
                required: true
            },
        },
        messages: {
            entitas_kode_jenis_identitas_eksportir: {
                required: "Kode Jenis Identitas Eksportir Wajib diisi"
            },
            entitas_nomor_eksportir: {
                required: "Nomor Eksportir wajib diisi"
            },
            entitas_nama_eksportir: {
                required: "Nama Eksportir wajib diisi"
            },
            entitas_alamat_eksportir: {
                required: "Alamat Eksportir wajib diisi"
            },
            entitas_nama_penerima: {
                required: "Nama Penerima wajib diisi"
            },
            entitas_alamat_penerima: {
                required: "Alamat Penerima wajib diisi"
            },
            entitas_kode_negara_penerima: {
                required: "Kode Negara Penerima wajib diisi"
            },
            entitas_nama_pembeli: {
                required: "Nama Pembeli wajib diisi"
            },
            entitas_alamat_pembeli: {
                required: "Alamat pembeli wajib diisi"
            },
            entitas_kode_negara_pembeli: {
                required: "Kode Negara pembeli wajib diisi"
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
    // DISPLAY MODAL
    $('#btn-display-modal').click(function(event) {
        event.preventDefault();
        $('.add-modal').modal('show');
    })

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
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/entitas"); ?>",
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
    $('.btn-submit-form').click(function() {
        if ($('#entitas-pemilik-form').valid()) {
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
                    var formData = new FormData(document.querySelector("#entitas-pemilik-form"));
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/entitas/pemilik"); ?>",
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


    function removeData(index_delete) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Entitas Pemilik ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("bea-cukai-bc-30/id/entitas/pemilik-delete"); ?>",
                    data: {
                        id: "<?= encrypt($bc30['id']) ?>",
                        index_delete: index_delete
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    },
                });
            }
        })

    }

    $('#entitas_kode_jenis_identitas_eksportir').select2({
        placeholder: "Pilih Jenis Identitas Eksportir",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#entitas_kode_negara_penerima').select2({
        placeholder: "Pilih Kode Negara",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#entitas_kode_negara_pembeli').select2({
        placeholder: "Pilih Kode Negara",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#tambah_pemilik_kode_jenis_entitas').select2({
        placeholder: "Pilih Kode  Jenis Identitas Pemilik",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#entitas_nomor_eksportir').select2({
        placeholder: "Pilih No NPWP",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $(this).find('option:selected');
        var npwpPengusaha = $(this).val();
        var namaPengusaha = selected.data('nama_pengusaha');
        var alamatPengusaha = selected.data('alamat');
        var nibDefault = selected.data('nib');
        $('#entitas_nama_eksportir').val(namaPengusaha);
        $('#entitas_alamat_eksportir').val(alamatPengusaha);

        // DROPDOPWN NOMOR IZIN TPB
        getListNoIjinTPB();
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
                pengusaha_tpb_id: $("#entitas_nomor_eksportir option:selected").data('id'),
            },
            dataType: "json",
            success: function(res) {

            }
        });
    }

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');
</script>


<?= $this->endSection(); ?>
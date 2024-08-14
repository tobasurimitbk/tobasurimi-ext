<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<!-- modal tambah bank devisa -->
<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Tambah Bank Devisa</h5>
            </div>
            <div class="modal-body">
                <form id="form-bank-devisa" class="form-bank-devisa">

                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="tambah-kode-bank-devisa" value="" name="tambah-kode-bank-devisa" type="text" class="form-control tambah-kode-bank-devisa" placeholder="">
                            <label>Kode Bank</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btn-tambah-bank-devisa" class="btn btn-submit-form btn-submit-parent">Tambah</button>
            </div>
        </div>
    </div>
</div>


<section class="section section-form">
    <?php include('header.php') ?>
    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            BC 3.0 - PEMBERITAHUAN EKSPOR BARANG
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <form id="form-transaksi">
                <div class="row mt-1">
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Harga
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <select class="form-select harga_kode_valuta" id="harga_kode_valuta" name="harga_kode_valuta" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeValuta as $k) : ?>
                                        <option <?= $payload->kodeValuta == $k['value'] ? 'selected' : '' ?> data-id_encrypt="<?= encrypt($k['value']) ?>" value="<?= $k['value'] ?>">
                                            <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Valuta</label>
                            </div>
                        </div>

                        <a href="#" id="btn-sesuai-valuta-terbaru" class="btn btn-warning" style="float: right;">
                            Sesuai Valuta Terbaru
                        </a>

                        <button class="btn btn-warning" type="button" disabled id="btn-sesuai-valuta-terbaru-loading" style="float: right;">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading
                        </button>

                        <br><br>

                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_ndpbm" value="<?= $payload->ndpbm == "" ? "0" : formatRupiah($payload->ndpbm) ?>" name="harga_ndpbm" type="text" class="form-control harga_ndpbm" onchange="this.value = formatRupiah(this.value)">
                                <label>NDPBM</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select transaksi_kode_incoterm" id="transaksi_kode_incoterm" name="transaksi_kode_incoterm" aria-label="Floating label select example">
                                    <option selected value=""></option>
                                    <?php foreach ($kodeIncoterm as $i) : ?>
                                        <option <?= $payload->kodeIncoterm == $i['value'] ? 'selected' : '' ?> data-id_encrypt="<?= encrypt($i['value']) ?>" value="<?= $i['value'] ?>">
                                            <?= strtoupper($i['value']) . " - " . strtoupper($i['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Cara Penyerahan</label>
                            </div>
                        </div>

                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="harga_cif" name="harga_cif" value="<?= $payload->cif  == "" ? "0" : formatRupiah($payload->cif)  ?>" type="text" class="harga_cif form-control" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Cif</label>
                            </div>
                        </div>

                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="freight" name="freight" value="<?= $payload->cif  == "" ? "0" : formatRupiah($payload->cif)  ?>" type="text" class="freight form-control" onchange="this.value = formatRupiah(this.value)">
                                <label>Frieght</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select  " id="transaksi_kode_asuransi" name="transaksi_kode_asuransi" aria-label="Floating label select example">
                                            <option selected value=""></option>
                                            <?php foreach ($kodeAsuransi as $a) : ?>
                                                <option <?= $payload->kodeAsuransi == $a['value'] ? 'selected' : '' ?> data-id_encrypt="<?= encrypt($a['value']) ?>" value="<?= $a['value'] ?>">
                                                    <?= strtoupper($a['value']) . " - " . strtoupper($a['description']) . "" ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label style="z-index: 1;">Asuransi </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input id="tambah_nomor_pemilik_barang" value="" name="tambah_nomor_pemilik_barang" type="number" class="tambah_nomor_pemilik_barang form-control" placeholder="">
                                        <label>Asuransi</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            <span style="color: white;">hidden</span>
                        </label>

                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="nilai_maklon" value="0" name="nilai_maklon" type="text" class="form-control nilai_maklon" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Maklon</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input readonly id="pajak_dikson" value="0" name="pajak_dikson" type="text" class="form-control pajak_dikson" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Bea Keluar</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select status_pph" name="status_pph" id="status_pph">
                                    <option value="1">PPH 2.5 %</option>
                                    <option value="0">TIDAK ADA</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Status PPH</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="nilai_pungutan_sawit" value="0" name="nilai_pungutan_sawit" type="text" class="form-control nilai_pungutan_sawit" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Pungutan Sawit</label>
                            </div>
                        </div>


                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Berat
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input readonly id="berat_netto" value="<?= $payload->netto == "" ? "0" : formatRupiah($payload->netto) ?>" name="berat_netto" type="text" class="form-control berat_netto" onchange="this.value = formatRupiah(this.value)">
                                <label>Berat Bersih/Netto (KGM)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="berat_bruto" name="berat_bruto" value="<?= $payload->bruto == "" ? "0" : formatRupiah($payload->bruto) ?>" type="text" class="form-control berat_bruto" onchange="this.value = formatRupiah(this.value)">
                                <label>Berat Kotor/Bruto (KGM)</label>
                            </div>
                        </div>


                    </div>
                </div>
            </form>
            <div class="row">
                <div class="section-header">
                    <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                        Bank Devisa
                    </label>
                    <button class="btn btn-show-form btn-add float-right" id="btn-display-modal">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                    </button>
                </div>

                <div class="table-responsive  mt-3">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-bank-devisa" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">Seri</th>
                                <th style="text-align: center;">Kode Bank</th>
                                <th style="text-align: center;">Nama Bank</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($payload->bankDevisa) == 0) : ?>
                                <tr style="color: white; text-align:center;">
                                    <td colspan="4">Tidak ada Bank Devisa</td>
                                </tr>
                            <?php else : ?>
                                <?php $length = count($payload->bankDevisa); ?>
                                <?php foreach ($payload->bankDevisa as $i => $p) : ?>
                                    <tr style="color: white; text-align:center;">
                                        <td><?= $p->seriBank ?></td>
                                        <td><?= $p->kodeBank ?></td>
                                        <td><?= $p->namaBank ?></td>
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

            <a href="#" class="btn btn-primary mt-4" id="btn-simpan-perubahan" style="float: right;">
                Simpan Perubahan
            </a>
            <button class="btn btn-primary" type="button" disabled id="btn-loading" style="float: right;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Loading
            </button>
        </div>

    </div>

</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    $('#harga_kode_valuta').select2({
        placeholder: "Pilih Valuta",
        theme: "bootstrap-5",
    });
    $('#transaksi_kode_asuransi').select2({
        placeholder: "Pilih Kode Asuransi",
        theme: "bootstrap-5",
    });
    $('#transaksi_kode_incoterm').select2({
        placeholder: "Pilih Kode Incoterm",
        theme: "bootstrap-5",
    });

    // DISPLAY MODAL
    $('#btn-display-modal').click(function(event) {
        event.preventDefault();
        $('.add-modal').modal('show');
    })

    $('#btn-sesuai-valuta-terbaru-loading').hide();

    $('#btn-sesuai-valuta-terbaru').click(function(e) {
        e.preventDefault();
        if ($('#harga_kode_valuta').val()) {
            $.ajax({
                url: `<?= base_url("bea-cukai-bc-23/api/valuta"); ?>`,
                method: "GET",
                data: {
                    harga_kode_valuta: $('#harga_kode_valuta option:selected').data('id_encrypt')
                },
                beforeSend: function() {
                    $('#btn-sesuai-valuta-terbaru-loading').show();
                    $('#btn-sesuai-valuta-terbaru').hide();
                },
                complete: function() {
                    $('#btn-sesuai-valuta-terbaru').show();
                    $('#btn-sesuai-valuta-terbaru-loading').hide();
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        csrf.val(res.token);
                        if (res.data.status) {
                            $("#harga_ndpbm").val((formatRupiah(res.data.data)));
                            $("#harga_cif").val((formatRupiah(res.data.data)));
                        }
                    }

                    if (res.data.status === false) {
                        Swal.fire({
                            icon: 'error',
                            title: res.data.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                    }
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: "Pilih valuta dahulu",
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        }
    });

    $('#harga_ndpbm').keyup(function() {
        var ndpbm = convertRupiahToNumber($(this).val()) || 0;
        var hargaBarang = convertRupiahToNumber($("#harga_nilai_penyerahan").val()) || 0;
        var hargaPabean = (Number(ndpbm) * Number(hargaBarang));

        $('#harga_cif').val(formatRupiah(ndpbm));
        $('#harga_nilai_pabean').val(formatRupiah(hargaPabean))
    });

    $('#harga_nilai_penyerahan').keyup(function() {
        var ndpbm = convertRupiahToNumber($("#harga_ndpbm").val()) || 0;
        var hargaBarang = convertRupiahToNumber($(this).val()) || 0;
        var hargaPabean = (Number(ndpbm) * Number(hargaBarang));

        $('#harga_nilai_pabean').val(formatRupiah(hargaPabean))
    });


    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var validatorTransaksi = $("#form-transaksi").validate({
        rules: {
            harga_kode_valuta: {
                required: true
            },
            harga_ndpbm: {
                required: true
            },
            harga_cif: {
                required: true
            },
            harga_nilai_pabean: {
                required: true
            },
            harga_nilai_penyerahan: {
                required: true
            },
            pajak_uang_muka: {
                required: true
            },
            pajak_dikson: {
                required: true
            },
            pajak_ppn_pajak: {
                required: true
            },
            pajak_tarif_ppn_pajak: {
                required: true
            },
            pajak_ppnbm_pajak: {
                required: true
            },
            pajak_tarif_ppnbm_pajak: {
                required: true
            },
            berat_bruto: {
                required: true
            },
            berat_netto: {
                required: true
            },
        },
        messages: {
            harga_kode_valuta: {
                required: "Pilih kode valuta"
            },
            harga_ndpbm: {
                required: "NDPBM wajib diisi"
            },
            harga_cif: {
                required: "CIF wajib diisi"
            },
            harga_nilai_pabean: {
                required: true
            },
            harga_nilai_penyerahan: {
                required: true
            },
            pajak_uang_muka: {
                required: true
            },
            pajak_dikson: {
                required: true
            },
            pajak_ppn_pajak: {
                required: "PPN wajib diisi"
            },
            pajak_tarif_ppn_pajak: {
                required: true
            },
            pajak_ppnbm_pajak: {
                required: "PPnBM wajib diisi"
            },
            pajak_tarif_ppnbm_pajak: {
                required: true
            },
            berat_bruto: {
                required: "Bruto wajib diisi"
            },
            berat_netto: {
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
    });

    // init loading
    $('#btn-loading').hide();

    $('#btn-simpan-perubahan').click(function() {
        if ($('#form-transaksi').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Transaksi ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-transaksi"));
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/transaksi"); ?>",
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
                                csrf.val(response.token);
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
    $('#btn-tambah-bank-devisa').click(function() {
        if ($('#form-bank-devisa').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Bank Devisa ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-bank-devisa"));
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/transaksi/bankDevisa"); ?>",
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
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
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
            title: 'Hapus Bank Devisa ?',
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
                    url: "<?= base_url("bea-cukai-bc-30/id/transaksi/bank-devisa-delete"); ?>",
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

    function formatRupiah(angka) {
        var formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });
        var parsedNumber = parseFloat(angka);
        if (isNaN(parsedNumber)) {
            return "0";
        }
        return formatter.format(parsedNumber).replace('Rp', '').trim();
    }

    function convertRupiahToNumber(rupiah) {
        var withoutDot = rupiah.replace(/\./g, '');
        var numberWithDot = withoutDot.replace(',', '.');
        return parseFloat(numberWithDot);
    }
</script>


<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <div class="section-header">
        <h1 class="title-name">Dokumen BC 2.3</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view">
                Simpan
            </button>
        </div>
    </div>

    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
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
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Valuta</label>
                            </div>
                        </div>

                        <a href="#" id="btn-sesuai-valuta-terbaru" class="btn btn-primary" style="float: right;">
                            Sesuai Valuta Terbaru
                        </a>

                        <button class="btn btn-primary" type="button" disabled id="btn-sesuai-valuta-terbaru-loading" style="float: right;">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading
                        </button>

                        <br><br>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_ndpbm" value="0" name="harga_ndpbm" type="text" class="form-control harga_ndpbm" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                                <label>NDPBM</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select harga_kode_harga_barang" id="harga_kode_harga_barang" name="harga_kode_harga_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeIncoterm as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Harga Barang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="harga_nilai_barang" name="harga_nilai_barang" type="text" class="harga_nilai_barang form-control" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                                <label>Harga Barang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="harga_cif" name="harga_cif" readonly type="text" class="harga_cif form-control" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                                <label>Harga Cif</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_nilai_pabean" readonly name="harga_nilai_pabean" type="text" class="form-control harga_nilai_pabean" placeholder="">
                                <label>Harga Barang Pabean</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Harga Lainnya
                        </label>
                        <div class="mt-1">

                        </div>

                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Berat
                        </label>
                        <div class="mt-1">

                        </div>

                    </div>
                </div>
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

    $('#btn-sesuai-valuta-terbaru-loading').hide();

    $('#btn-sesuai-valuta-terbaru').click(function(e) {
        e.preventDefault();
        if ($('#harga_kode_valuta').val()) {
            $.ajax({
                url: `<?= base_url("bea-cukai-bc-23/api/valuta"); ?>`,
                method: "GET",
                data: {
                    harga_kode_valuta: $('#harga_kode_valuta').val()
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
                            $("#harga_ndpbm").val(formatRupiah(res?.data?.data));
                            $("#harga_cif").val(formatRupiah(res?.data?.data));
                        }
                    }

                    if (res.data.status === false) {
                        console.log(res.data.message);
                        Swal.fire({
                            icon: 'warning',
                            title: res.data.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                    }

                }
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: "Pilih valuta dahulu",
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        }
    });

    $('#harga_nilai_barang').keyup(function() {
        var ndpbm = $("#harga_ndpbm").val() || 0;
        var hargaBarang = $(this).val() || 0;

        $('#harga_nilai_pabean').val(formatRupiah(Number(repairRupiah(ndpbm)) * Number(repairRupiah(hargaBarang))))
    });

    $('#harga_kode_harga_barang').select2({
        placeholder: "Pilih Kode Harga",
        theme: "bootstrap-5",
    });

    $('#kontainer_jenis').select2({
        placeholder: "Pilih Jenis Peti Kemas",
        theme: "bootstrap-5",
    });

    $('#kontainer_tipe').select2({
        placeholder: "Pilih Tipe Peti Kemas",
        theme: "bootstrap-5",
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var tableListInformasiKemasan = $('.table-list-informasi-kemasan').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        lengthChange: true,
        info: false,
        paging: false,
        searching: false,
        ordering: false,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });
    var tableListPetiKemas = $('.table-list-informasi-peti-kemas').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        lengthChange: true,
        info: false,
        paging: false,
        searching: false,
        ordering: false,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    function formatRupiah(angka) {
        angka = angka || 0;
        angka = angka.toString().replace(/\./g, '').replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuanFormatted = parts[0].split('').reverse().join('').match(/\d{1,3}/g).join('.').split('').reverse().join('');
        var desimal = parts[1] || '00';
        return ribuanFormatted + ',' + desimal;
    }

    function repairRupiah(rupiahString) {
        var cleanedString = rupiahString.replace(/[^\d,]/g, '');
        var parts = cleanedString.split(',');
        var ribuan = parts[0].replace(/\./g, '');
        var repairedNumber = ribuan + '.' + (parts[1] || '00');
        var result = parseFloat(repairedNumber);
        return isNaN(result) ? 0 : result;
    }
</script>


<?= $this->endSection(); ?>
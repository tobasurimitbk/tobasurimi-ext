<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($jasaVendorOut) ? "Tambah Rasio" : "Update Rasio" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("rasio"); ?>">
                Batal
            </a>
            <button class="btn btn-hapus delete-parent float-right" onclick="remove('')">
                Hapus
            </button>
            <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('')">
                Posting
            </button>
            <button class="btn btn-warning btn-print float-right" onclick="print('')">
                Print
            </button>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Rasio</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTable" width="100%" border="1" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;" rowspan="2">No</th>
                                        <th style="text-align: center;" colspan="5">Data Pembelian</th>
                                        <th style="text-align: center;" colspan="4">Data Penerimaan</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center;">Spesifikasi</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">Harga Total</th>
                                        <th style="text-align: center;">Harga Satuan</th>
                                        <th style="text-align: center;">Satuan</th>

                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">Harga Total</th>
                                        <th style="text-align: center;">Harga Satuan</th>
                                        <th style="text-align: center;">Satuan</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                                <tfoot style="background: #ffffff !important;" class="foot-detail-table" id="foot-detail-table">
                                    <tr>
                                        <td colspan="12" style="text-align: center;">
                                            Tidak Ada Barang
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Total Pembelian Barang</label>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly placeholder="Qty" value="" class="form-control qtyTotalPembelian" id="qtyTotalPembelian" name="qtyTotalPembelian" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Qty</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly placeholder="Harga Total" value="" class="form-control hargaTotalPembelian" id="hargaTotalPembelian" name="hargaTotalPembelian" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Harga Total</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly placeholder="Rata-rata Harga Satuan" value="" class="form-control hargaSatuanPembelian" id="hargaSatuanPembelian" name="hargaSatuanPembelian" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Rata-rata Harga Satuan</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Total Penerimaan Barang</label>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly placeholder="Qty" value="" class="form-control qtyTotalPenerimaan" id="qtyTotalPenerimaan" name="qtyTotalPenerimaan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Qty</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly placeholder="Harga Total" value="" class="form-control hargaTotalPenerimaan" id="hargaTotalPenerimaan" name="hargaTotalPenerimaan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Harga Total</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly placeholder="Rata-rata Harga Satuan" value="" class="form-control hargaSatuanPenerimaan" id="hargaSatuanPenerimaan" name="hargaSatuanPenerimaan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Rata-rata Harga Satuan</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Biaya Tambahan</label>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-floating " style="height: 50px;">
                            <select class="form-select akun_coa_subsidi" name="akun_coa_subsidi" id="akun_coa_subsidi">
                                <option value=""></option>
                                <?php
                                if (!empty($subAkuns)) {
                                    foreach ($subAkuns as $sub) {
                                ?>
                                        <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Akun COA Subsidi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select akun_coa_biaya" name="akun_coa_biaya" id="akun_coa_biaya">
                                <option value=""></option>
                                <?php
                                if (!empty($subAkuns)) {
                                    foreach ($subAkuns as $sub) {
                                ?>
                                        <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Akun COA BIaya Lain-lain</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select akun_coa_kopek" name="akun_coa_kopek" id="akun_coa_kopek">
                                <option value=""></option>
                                <?php
                                if (!empty($subAkuns)) {
                                    foreach ($subAkuns as $sub) {
                                ?>
                                        <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Akun COA Kopek</label>
                        </div>
                    </div>
                </div>
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Rasio Barang Jadi</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableRasio" width="100%" border="1" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Kode Barang</th>
                                        <th style="text-align: center;">Nama Barang</th>
                                        <th style="text-align: center;">Satuan</th>
                                        <th style="text-align: center;">Jumlah Barang</th>
                                        <th style="text-align: center;">Rasio</th>
                                        <th style="text-align: center;">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table-rasio">
                                </tbody>
                                <tfoot style="background: #ffffff !important;" class="tfoot-rasio" id="tfoot-rasio">
                                    <tr>
                                        <td colspan="7" style="text-align: center;">
                                            Tidak Ada Barang
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    const csrfToken = '<?= csrf_token() ?>';

    let list_items_barang_jadi = [];
    let list_items_barang_digunakan = [];

    $(".tanggal").datepicker({
        todayHighlight: true,
        format: "mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        startView: "months",
        minViewMode: 1
    }).change(function() {
        getData()
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getData()
    });

    $("#divisi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    // Akun AR
    $('#akun_coa_subsidi, #akun_coa_biaya, #akun_coa_kopek').select2({
        placeholder: "Pilih Akun COA",
        theme: "bootstrap-5",
        allowClear: true
    })

    //CSS SELECT2 FLOATING LABEL
    $('#akun_coa_subsidi, #akun_coa_biaya, #akun_coa_kopek')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const getData = function() {
        var department_id = $('#divisi_id').val();
        var bulan = $('#tanggal').val();
        if (department_id && bulan) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-barang-jadi'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_barang_jadi = [];
                        let no = 0;
                        // Iterate over each item in the response data
                        res.data.forEach(function(item) {

                            list_items_barang_jadi.push(item);
                        });
                        drawTableRasio();
                        console.log(list_items_barang_jadi);
                    } else {
                        stopLoading()
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Produksi Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                },
            });
            $.ajax({
                url: `<?= base_url('rasio/get-barang-digunakan'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_barang_digunakan = [];
                        let no = 0;
                        // Iterate over each item in the response data
                        res.data.forEach(function(item) {
                            list_items_barang_digunakan.push(item);
                        });
                        // drawTableDigunakan();
                        console.log(list_items_barang_digunakan);
                    } else {
                        stopLoading()
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Produksi Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                },
            });
        }
    }

    const drawTableRasio = function() {
        $('.body-table-rasio').empty();
        $('.tfoot-rasio').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        if (list_items_barang_jadi.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio').append(row);
        } else {
            var rasio = 0;
            var rasioTotal = 0;
            var total = 0;
            list_items_barang_jadi.map((item, index) => {
                total = item.totalQtyAll // hitung total barang
                rasio = (item.qtyTotal / item.totalQtyAll) * 100; // Perhitungan rasio
                rasioTotal += rasio; // Perhitungan rasio
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>'; // Nomor urut menggunakan index + 1
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + item.kode_satuan + '</td>';
                row += '<td>' +
                    '<input class="form-control jumlah-barang text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + item.qtyTotal + '">' +
                    '</td>';
                row += '<td>' +
                    '<input class="form-control rasio text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + rasio.toFixed(2) + '%">' + // Ubah nilai rasio menjadi persentase dengan dua angka di belakang koma
                    '</td>';
                row += '<td>' +
                    '<input class="form-control harga text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="">' +
                    '</td>';
                row += '</tr>';
                no++;
            });
            rowFooter += '<tr>';
            rowFooter += '<td colspan="4"></td>';
            rowFooter += '<td>' +
                '<input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + total + '">' +
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control rasio-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + rasioTotal.toFixed(2) + '%">' + // Ubah nilai rasio menjadi persentase dengan dua angka di belakang koma
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control harga-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="">' +
                '</td>';
            rowFooter += '</tr>';
            $('.tfoot-rasio').append(rowFooter);
            $('.body-table-rasio').append(row);
        }
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }
</script>

<?= $this->endSection(); ?>
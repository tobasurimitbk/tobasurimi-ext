<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-add-spp .form-floating .form-floating-custom .select2 .selection .select2-selection {
        height: 90px !important;
    }

    .form-add-spp .form-floating .form-floating-custom .select2 .selection .select2-selection__rendered {
        height: 60px !important;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Tambah Dokumen BC 2.5</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-25"); ?>">
                Kembali
            </a>
            <a class="btn btn-info btn-print float-right text-white" href="<?= base_url('bea-cukai-bc-25/id/header/' . encrypt($bc25['id'])) ?>">
                Form Ceisa
            </a>
            <?php if ($bc25['status_posting']): ?>
                <button class="btn btn-show-form btn-save float-right" id="btn_update_no_aju_no_daftar">
                    Ubah No Aju & No Daftar
                </button>
            <?php endif; ?>
            <?php if ($bc25['status_posting'] == 0) : ?>
                <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                    Hapus
                </button>
                <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting()">
                    Posting
                </button>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header" style="font-weight: bold; <?= session()->get('theme') == 'dark' ? 'color:white;' : 'color:black;' ?>">
            PILIH INVENTORI YANG AKAN KELUAR
        </div>
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select class="form-select jenis_pengeluaran" id="jenis_pengeluaran" name="jenis_pengeluaran" aria-label="Floating label select example">
                                <option value=""></option>
                                <option value="ORDER FORM LOKAL">ORDER FORM LOKAL</option>

                            </select>
                            <label style="z-index: 1;">Pilih Pengeluaran</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select class="form-select reference_penerima_id" id="reference_penerima_id" name="reference_penerima_id" aria-label="Floating label select example">
                                <option value=""></option>

                            </select>
                            <label style="z-index: 1;">Pilih Penerima (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 90px;">
                            <div class="form-floating-custom">
                                <select class="form-select multiple_reference_id" id="multiple_reference_id" multiple name="multiple_reference_id[]" aria-label="Floating label select example">
                                    <option value=""></option>

                                </select>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">Daftar Barang Yang Akan Keluar</label>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-show-detail btn-add btn-block float-right" type="button" id="btnDetailStockModal">
                            <i class="fa-solid fa-magnifying-glass"></i> Inventori
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: center; width:5px;">No</th>
                            <th>Kode Barang</th>
                            <th>Barang</th>
                            <th>Spesifikasi</th>
                            <th>Qty</th>
                            <th>Harga Satuan</th>
                            <th>Satuan</th>
                            <th>Sub Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table">
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</section>



<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var listData = [];
    var listDataSelected = [];

    $('#jenis_pengeluaran').select2({
        placeholder: "Pilih Jenis Pengeluaran",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        dropdownReferensiPenerima();

    });

    $('#reference_penerima_id').select2({
        placeholder: "Pilih Penerima (Opsional)",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        dropdownReferensiPengeluaran();
    });


    $('.multiple_reference_id').select2({
        placeholder: "Pilih Referensi Pengeluaran (Opsional)",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {});

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    function dropdownReferensiPenerima() {
        var jenis_pengeluaran = $('#jenis_pengeluaran option:selected').val();
        $.ajax({
            url: "<?= base_url("bea-cukai-bc-25/get-referensi-penerima"); ?>",
            data: {
                jenis_pengeluaran: jenis_pengeluaran,
            },
            method: "GET",
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    $("#multiple_reference_id").empty()
                    $("#multiple_reference_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $("#multiple_reference_id").append(`<option value="${item.id}">${item.reference_no}</option>`)
                    })
                    $("#multiple_reference_id").val();
                }
            }
        });
    }

    function dropdownReferensiPengeluaran() {
        var jenis_pengeluaran = $('#jenis_pengeluaran option:selected').val();
        $.ajax({
            url: "<?= base_url("bea-cukai-bc-25/get-referensi"); ?>",
            data: {
                jenis_pengeluaran: jenis_pengeluaran,
            },
            method: "GET",
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    $("#multiple_reference_id").empty()
                    $("#multiple_reference_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $("#multiple_reference_id").append(`<option value="${item.id}">${item.reference_no}</option>`)
                    })
                    $("#multiple_reference_id").val();
                }
            }
        });
    }
</script>

<?= $this->endSection(); ?>
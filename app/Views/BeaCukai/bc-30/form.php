<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section section-form">

    <div class="section-header">
        <h1 class="title-name"><?= !empty($bc30) ? "Update Dokumen BC 3.0" : "Tambah Dokumen BC 3.0" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-30"); ?>">
                Batal
            </a>
            <?php if (!empty($bc30)) : ?>
                <?php if ($bc30['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 3.0', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc30['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 3.0', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="postingAction()">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc30['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 3.0', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php if (can('Bea Cukai', 'BC 3.0', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            DATA BARANG UNTUK PEMBUATAN DOKUMEN BEA CUKAI 3.0
        </div>
        <div class="card-body">
            <form class="create-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id" class="id" value="<?= !empty($bc30) ? encrypt($bc30['id']) : '' ?>">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select tipe_sales_order" id="tipe_sales_order" name="tipe_sales_order" aria-label="Floating label select example">
                                <option value=""></option>
                                <option value="INTERNASIONAL">SALES ORDER EKSPOR</option>
                                <option value="LOKAL">SALES ORDER LOKAL</option>
                            </select>
                            </select>
                            <label style="z-index: 1;">Pilih Tipe Sales Order</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select sales_order_id" id="sales_order_id" name="sales_order_id" aria-label="Floating label select example">
                                <option value=""></option>

                            </select>
                            </select>
                            <label style="z-index: 1;">Pilih Order Form</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= !empty($bc30) ? $bc30['divisi'] : '' ?>" class="form-control divisi_asal_name" id="divisi_asal_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Nama Customer</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Negara" value="<?= !empty($bc30) ? $bc30['divisi'] : '' ?>" class="form-control divisi_asal_name" id="divisi_asal_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Negara</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= !empty($bc30) ? $bc30['warehouse_name'] : '' ?>" class="form-control warehouse_asal_name" id="warehouse_asal_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Alamat</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($bc30) ? $bc30['no_aju'] : "" ?>" name="no_aju" readonly type="text" id="no_aju" class="form-control no_aju" placeholder="">
                                <label>Nomor Pengajuan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button" onclick="noAjuShowModal()">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc30) ? $bc30['no_daftar'] : "" ?>" autocomplete="one-time-code" type="number" class="form-control no_daftar" id="no_daftar" name="no_daftar" placeholder="No Daftar">
                            <label for="floatingInput">Nomor Daftar</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mt-2">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Di Ekspor</label>
                </div>
                <div class="col-md-12 col-table-button-tts">


                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tipe Barang</th>
                                    <th style="text-align: center;">Dokumen Asal</th>
                                    <th style="text-align: center;">Kode Barang (Internal)</th>
                                    <th style="text-align: center;">Barang - Spesifikasi (Internal)</th>
                                    <th style="text-align: center;">Kode Barang (Sales)</th>
                                    <th style="text-align: center;">Barang - Spesifikasi (Sales)</th>
                                    <th style="text-align: center;">No Stuffing / Pengeluaran Barang</th>
                                    <th style="text-align: center;">Departemen</th>
                                    <th style="text-align: center;">Warehouse</th>
                                    <th style="text-align: center;">Qty Keluar</th>
                                    <th style="text-align: center;">Satuan</th>
                                    <th style="text-align: center;">Harga</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="13" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var listData = [];

    $('#tipe_sales_order').select2({
        placeholder: "Pilih Tipe Sales Order",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $('#sales_order_id').select2({
        placeholder: "Pilih Order Form",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $("#sales_order_id,#tipe_sales_order")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    function getListMutasiGobal() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-30/list-sales-order'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                tipe_sales_order: $(".tipe_sales_order option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".sales_order_id").empty()
                $(".sales_order_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".sales_order_id").append(`<option 

                        value="${item.id}">${item.no_mutasi}
                    </option>`)
                })
                $(".sales_order_id").val();
            }
        });
    }
</script>

<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .disabled-option {
        color: gray !important;
        background-color: #f0f0f0 !important;
    }
</style>
<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataSPP) ? "Update SPP" : "Tambah SPP" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("spp"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataSPP)) { ?>
                <?php if ($dataSPP->is_posted === "0") { ?>
                    <?php if (can('Pembelian', 'SPP', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php } ?>

                <button class="btn btn-warning btn-print float-right" onclick="print('<?= encrypt($dataSPP->id) ?>')">
                    Print
                </button>
                <?php if ($dataSPP->is_posted == "0") : ?>
                    <?php if (can('Pembelian', 'SPP', 'a')) : ?>
                        <button onclick="updateStatusPosting('1')" class="btn btn-success posting-spp float-right posting-spp">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Pembelian', 'SPP', 'ua') && $dataSPP->request_status != "finished") : ?>
                        <button onclick="updateStatusPosting('0')" class="btn btn-success un-posting-spp float-right posting-spp">
                            Un Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

            <?php } ?>

            <?php if (!empty($dataSPP)) {
                if ($dataSPP->is_posted === "0") { ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php }
            } else { ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data SPP</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($dataSPP) ? encrypt($dataSPP->id) : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataSPP) ? ($dataSPP->is_posted == "1" ? 'readonly' : '') : '' ?> autocomplete="one-time-code" <?= !empty($dataSPP) ? ($dataSPP->is_posted === "1" ? 'readonly=true' : '') : ''; ?> type="text" class="form-control spp_no" id="spp_no" name="spp_no" placeholder="No. SPP" value="<?= !empty($dataSPP) ? $dataSPP->spp_no : ""; ?>">
                                    <label for="floatingInput">No. SPP</label>
                                </div>
                                <div style="<?= !empty($dataSPP) ? "display:none;" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select onchange="changeDepartment()" <?= !empty($dataSPP) ? ($dataSPP->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option <?= !empty($dataSPP) ? ($dataSPP->divisi_id == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataSPP) ? ($dataSPP->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-control input-picker request_date" value="<?= !empty($dataSPP) ? ($dataSPP->request_date ? date("d/m/Y", strtotime($dataSPP->request_date)) : "") : $today; ?>" id="request_date" name="request_date" placeholder="Tanggal Order">
                                    <label for="floatingInput">Tanggal Order</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-request-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select onchange="changeTipeSPP()" <?= !empty($dataSPP) ? ($dataSPP->is_posted === "1" ? 'disabled=true' : '')  : ''; ?> class="form-select spp_type" name="spp_type" id="spp_type" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataSppType as $d) : ?>
                                    <option <?= (!empty($dataSPP) ? ($dataSPP->spp_type == trim($d['value']) ? 'selected' : '') : (trim($d['value']) == "Lokal BP" ? 'selected' : '')) ?> value="<?= trim($d['value']) ?>"><?= strtoupper($d['value']) ?></option>
                                <?php endforeach; ?>
                            </select>

                            <label for="floatingInput">Tipe SPP</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataSPP) ? $dataSPP->note : ""; ?>" <?= !empty($dataSPP) ? ($dataSPP->is_posted === "1" ? 'readonly=true' : '') : ''; ?> type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
                            <label for="floatingInput">Catatan (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>
                    <div class="col-md-6">
                        <?php if (!empty($dataSPP)) {
                            if ($dataSPP->is_posted === "0") { ?>
                                <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                                </button>
                            <?php }
                        } else { ?>
                            <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Qty</th>
                                <th>Keterangan</th>
                                <th style="width:80px;<?= !empty($dataSPP) ? ($dataSPP->is_posted === "1" ? "display: none;" : "") : ""; ?>">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        <tfoot class="tfoot">
                            <tr>
                                <td colspan="7" class="text-center">Data Barang Tidak Ada</td>
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary" id="title-detail-name">Tambah Barang</h5>
                <!-- <button class="btn btn-show-form btn-add-barang float-right">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah Barang
                </button> -->
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="barang_spesifikasi_id" class="barang_spesifikasi_id" id="barang_spesifikasi_id">
                    <input autocomplete="one-time-code" type="hidden" class="barang_detail_id" name="barang_detail_id" id="barang_detail_id" />
                    <input autocomplete="one-time-code" type="hidden" class="barang_id" name="barang_id" id="barang_id" />
                    <input autocomplete="one-time-code" type="hidden" class="header_barang_name" name="header_barang_name" id="header_barang_name" />

                    <input autocomplete="one-time-code" type="hidden" class="spp_type_bypass" name="spp_type_bypass" id="spp_type_bypass" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="hidden" class="kode" name="kode" id="kode" />
                                    <select class="form-select kode_barang" name="kode_barang" id="kode_barang" aria-label="Floating label select example">
                                        <option data-barang_id="" data-nama="" data-satuan="" value=""></option>
                                    </select>
                                    <label for="floatingInput">Kode Barang</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button class="btn btn-success btn-add-barang" data-toggle="modal" type="button">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating" style="height: 50px;">
                                <select class="form-select satuan_id" name="satuan_id" id="satuan_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($satuanBarang as $s) : ?>
                                        <option data-kode_satuan="<?= $s['kode_satuan'] ?>" value="<?= $s['id'] ?>">
                                            <?= $s['kode_satuan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan</label>
                            </div>
                            <small class="mb-4 mt-1">
                                <i>
                                    Jika ingin menggunakan satuan yang lain, pastikan anda sudah mengatur satuannya di menu master barang
                                </i>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="preventNegativeInput(this)" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <textarea autocomplete="one-time-code" class="form-control keterangan text-area-all" name="keterangan" id="keterangan" placeholder="Keterangan (Opsional)"></textarea>
                                <label for="floatingInput">Keterangan (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail" onclick="submitDetailForm()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal add-modal m-t-bahan-baku" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang</h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form class="create-form-barang" role="form" method="POST">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="type" id="type">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select" name="parent_type_id" id="parent_type_id">
                                    <option value=""></option>
                                    <?php foreach ($kelompokBarang as $kb) : ?>
                                        <option value="<?= encrypt($kb['id']) ?>"><?= $kb['parent_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Kategori Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control" name="kode_barang" placeholder="Kode Barang">
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                    <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                        <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" id="generate_new_code" name="generate_new_code" type="checkbox" onchange="generateNewCode()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" name="barang_name" id="barang_name">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                </form>
                <form class="spek-form" role="form">
                    <div class="col-subtitle-modal">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title">Tambah Spesifikasi</label>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-add float-right" onclick="resetFormDetail()" style="background: orange;">
                                    <i class="fa fa-refresh fa-sm mr-2" aria-hidden="true"></i>Reset
                                </button>
                                <button type="button" class="btn btn-add float-right mr-1 title-detail-name-spek" onclick="submitSpekForm()">
                                    <i class="title-detail-icon-spek fa fa-plus fa-sm mr-2" aria-hidden="true"></i><span class="btn-text">Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <input autocomplete="one-time-code" type="hidden" class="spek_id" name="spek_id" id="spek_id" />
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" name="spek" id="spek">
                                <label for="floatingInput">Spesifikasi</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <select class="form-select" name="satuan1_id" id="satuan1_id" title="Satuan terkecil dari produk. Cth: PCS" onchange="changeSpanText()">
                                    <option value=""></option>
                                    <?php foreach ($satuanBarang as $sb) : ?>
                                        <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan 1</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-floating">
                                <select class="form-select" name="satuan2_id" id="satuan2_id" title="Satuan yang lebih besar dari Satuan 1. Cth: LUSIN (12 Pcs)" onchange="checkSatuan2()">
                                    <option value=""></option>
                                    <?php foreach ($satuanBarang as $sb) : ?>
                                        <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan 2</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group ">
                                <input type="text" name="konversi_satuan_2" onchange="checkValueKonversi()" id="konversi_satuan_2" class="form-control" onkeypress="return isNumberKey(event)">
                                <div class="input-group-append">
                                    <span class="input-group-text satuan1">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-floating">
                                <select class="form-select" name="satuan3_id" id="satuan3_id" title="Satuan terbesar dari produk. Cth: DUS (konversi 48 PCS)" onchange="checkSatuan3()">
                                    <option value=""></option>
                                    <?php foreach ($satuanBarang as $sb) : ?>
                                        <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan 3</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group ">
                                <input type="text" name="konversi_satuan_3" onchange="checkValueKonversi()" id="konversi_satuan_3" class="form-control" onkeypress="return isNumberKey(event)">
                                <div class="input-group-append">
                                    <span class="input-group-text satuan1">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12 mt-1" style="font-size: 12px;">
                        Note : <br>
                        <ul>
                            <li style="height: 15px;">PCS, Lusin 12 PCS, Dus 24 PCS</li>
                            <li style="height: 15px;">Satuan 1 adalah satuan terkecil</li>
                            <li style="height: 15px;">Satuan 2 harus lebih besar daripada satuan 1</li>
                            <li style="height: 15px;">Saturan 3 harus lebih besar dari satuan 2</li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Spesifikasi</th>
                                            <th scope="col">Satuan 1</th>
                                            <th scope="col">Satuan 2</th>
                                            <th scope="col">Satuan 3</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-detail-table-spek" id="body-detail-table-spek" id="tbody2" style="cursor: pointer;">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form btn-submit-form-barang" id="btn-submit-form-barang">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    let list_items = [];
    let list_items_spek = [];

    <?php if (empty($dataSPP)) : ?>
        $(document).ready(function() {
            changeStatus();
        });
    <?php endif; ?>

    const customMatcher = function(params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }

        if (typeof data.text === 'undefined') {
            return null;
        }

        let term = params.term.toLowerCase().split(" ");
        let text = data.text.toLowerCase();

        let isMatch = term.every(t => text.includes(t));

        return isMatch ? data : null;
    }


    var validator_detail = $(".detail-form").validate({
        rules: {
            kode_barang: {
                required: true
            },
            nama_barang: {
                required: true
            },
            qty: {
                required: true
            },
            harga: {
                required: true
            },
            satuan_id: {
                required: true
            }
        },
        messages: {
            kode_barang: {
                required: "Kode wajib diisi"
            },
            nama_barang: {
                required: "Nama wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            harga: {
                required: "Harga wajib diisi"
            },
            satuan_id: {
                required: "Satuan wajib diisi"
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

    var validator = $(".create-form").validate({
        rules: {
            request_date: {
                required: true
            },
            spp_no: {
                required: true
            },
            divisi_id: {
                required: true,
            },
            spp_type: {
                required: true,
            }
        },
        messages: {
            request_date: {
                required: "Tanggal Order wajib diisi"
            },
            spp_no: {
                required: "No. SPP wajib diisi"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            spp_type: {
                required: "Pilih Tipe SPP"
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

    $(".request_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    // DIVISI
    $('.divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5"
    });

    // PILIH TIPE SPP
    $('.spp_type').select2({
        placeholder: "Pilih SPP",
        theme: "bootstrap-5"
    });


$('.kode_barang').select2({
    placeholder: "Pilih Kode Barang",
    theme: "bootstrap-5",
    dropdownParent: $(".detail-modal .modal-content"),
    ajax: {
        url: '<?= base_url("barang/dropdown/type-server") ?>',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                q: params.term, 
                type: getTypeSPP()
            };
        },
        processResults: function(data) {
            // Pastikan server mengembalikan data dengan struktur yang lengkap
            return {
                results: $.map(data.results, function(item) {
                    return {
                        id: item.id,
                        text: item.text,
                        // Tambahkan semua data attributes yang diperlukan
                        satuan_1: item.satuan_1,
                        satuan_2: item.satuan_2,
                        satuan_3: item.satuan_3,
                        barang_name_master: item.barang_name_master,
                        barang_spesifikasi_id: item.barang_spesifikasi_id,
                        barang_id: item.barang_id,
                        nama: item.nama,
                        satuan_id: item.satuan_id,
                        kode_barang: item.kode_barang,
                        satuan: item.satuan
                    };
                })
            };
        },
        cache: true
    },
    minimumInputLength: 1
});
    

    $('.satuan_id').select2({
        placeholder: "Pilih Kode Satuan",
        theme: "bootstrap-5",
        dropdownParent: $(".detail-modal .modal-content"),
        tags: false,
        allowClear: true
    })


    //CSS SELECT2 FLOATING LABEL
    $('.divisi_id,.spp_type,.kode_barang,.satuan_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.divisi_id,.spp_type,.kode_barang,.satuan_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.divisi_id,.spp_type,.kode_barang,.satuan_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $('.icon-request-date').click(function() {
        $(".request_date").focus();
    });


    $(".btn-submit-parent").click(function() {
        $(".detail-modal").modal("hide")
        if (list_items.length === 0) {
            Swal.fire({
                icon: 'error',
                title: "Barang masih kosong",
                confirmButtonColor: '#4e73df',
            })
        } else {
            if ($(".create-form").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let id = $(".id").val();
                        let formData = new FormData(document.querySelector('.create-form'));
                        formData.append("items", JSON.stringify(list_items));

                        if (id) {
                            // UPDATE
                            <?php if (can('Pembelian', 'SPP', 'u')) : ?>
                                $.ajax({
                                    url: "<?= base_url("spp/update"); ?>",
                                    data: formData,
                                    method: "POST",
                                    dataType: "json",
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading();
                                    },
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        if (response.status) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                reverseButtons: true,
                                                confirmButtonText: 'Oke',
                                            }).then((result) => {
                                                window.location.replace("<?= base_url('spp') ?>");
                                            })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            });
                                            csrf.val(response.token);
                                        }

                                    }
                                });
                            <?php else : ?>
                                Swal.fire({
                                    icon: 'error',
                                    title: "Anda tidak memiliki akses update",
                                    confirmButtonColor: '#4e73df',
                                })
                            <?php endif; ?>
                        } else {
                            // CREATE
                            $.ajax({
                                url: "<?= base_url("spp/save"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        }).then((result) => {
                                            window.location.replace("<?= base_url('spp') ?>");
                                        })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        });
                                        csrf.val(response.token);
                                    }
                                }
                            });
                        }
                    }
                })
            }
        }

    })

    $(".btn-show-detail").click(function() {

        $("#title-detail-name").text("Tambah Barang");
        $(".barang_detail_id").val('');

        $(".kode").val('')
        $(".nama_barang").val('')
        $(".qty").val('')
        $(".harga").val('')
        $(".total").val('')
        $(".keterangan").val('')

        validator_detail.resetForm();
        validator_detail.reset();

        var spp_type = $('.spp_type').val().trim();
        var type = "";

        if (spp_type === "Import BB" || spp_type === "Lokal BB") {
            type = "bahan_baku";
        } else {
            type = "bahan_penolong";
        }
        $(".spp_type_bypass").val(type)

        if(spp_type == ""){
            Swal.fire({
                icon: 'error',
                title: "Pilih Tipe SPP Dahulu",
                confirmButtonColor: '#4e73df',
            });
            return;
        }else{
            $(".kode_barang").val(null).change();
            $(".detail-modal").modal("show");
        }


    });



    $(".btn-hide-detail").click(function() {
        $(".detail-modal").modal("hide")
    });

    $('.kode_barang').on('select2:select', function (e) {
        var data = e.params.data;
        
        // Set data attributes ke elemen option yang dipilih
        var selectedOption = $(this).find('option:selected');
        
        // Set semua data attributes
        selectedOption.data('satuan_1', data.satuan_1);
        selectedOption.data('satuan_2', data.satuan_2);
        selectedOption.data('satuan_3', data.satuan_3);
        selectedOption.data('barang_name_master', data.barang_name_master);
        selectedOption.data('barang_spesifikasi_id', data.barang_spesifikasi_id);
        selectedOption.data('barang_id', data.barang_id);
        selectedOption.data('nama', data.nama);
        selectedOption.data('satuan_id', data.satuan_id);
        selectedOption.data('kode_barang', data.kode_barang);
        selectedOption.data('satuan', data.satuan);
        
        // Trigger change event manual
        $(this).trigger('change');
    });

    $(".kode_barang").on('change', function(e) {
         
        if ($(".kode_barang option:selected").val()) {
            console.log($('.kode_barang option:selected').val());
            let nama = $(".kode_barang option:selected").data("nama") ? $(".kode_barang option:selected").data("nama") : "";
            let satuan = $(".kode_barang option:selected").data("satuan") ? $(".kode_barang option:selected").data("satuan") : "";
            let satuan_1 = $(".kode_barang option:selected").data("satuan_1") ? $(".kode_barang option:selected").data("satuan_1") : "";
            let satuan_2 = $(".kode_barang option:selected").data("satuan_2") ? $(".kode_barang option:selected").data("satuan_2") : "";
            let satuan_3 = $(".kode_barang option:selected").data("satuan_3") ? $(".kode_barang option:selected").data("satuan_3") : "";
            let barang_id = $(".kode_barang option:selected").data("barang_id") ? $(".kode_barang option:selected").data("barang_id") : "";
            let barang_spesifikasi_id = $(".kode_barang option:selected").data("barang_spesifikasi_id") ? $(".kode_barang option:selected").data("barang_spesifikasi_id") : "";
            let barang_name_master = $(".kode_barang option:selected").data("barang_name_master");
            let spp_type = $('.spp_type').val().trim();


            if (list_items.length === 0 && spp_type === "Lokal BB") {
                $('.header_barang_name').val(barang_name_master);
            }

            $(".kode").val($(".kode_barang option:selected").val());
            console.log(nama)
            $(".nama_barang").val(nama);
            $(".barang_id").val(barang_id);
            $(".barang_spesifikasi_id").val(barang_spesifikasi_id);

            // SET ACTIVE FIELD
            activeFieldSatuanId(
                satuan_1,
                satuan_2,
                satuan_3
            );

            $(".satuan_id").val(satuan_1).change();

        } else {
            $(".kode").val("");
            $(".nama_barang").val("");
            $(".barang_id").val("");
            $(".barang_spesifikasi_id").val("");
            $(".satuan_id").val(null).change();
        }
    })

    // delete
    $(".delete-parent").click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                let id = $(".id").val();
                $.ajax({
                    url: "<?= base_url("spp/delete"); ?>",
                    data: {
                        id: id,
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            stopLoading()
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url("spp"); ?>"
                                })
                        }
                    },

                });
            }
        })
    })

    $(document).on('click', '.edit-table-detail', function(evt) {
        $("#title-detail-name").text("Update Barang")
        validator_detail.resetForm();
        validator_detail.reset();
        let barang_id = "";
        let barang_spesifikasi_id = "";
        let barang_detail_id = $(this).data('barang_detail_id');

        $.each(list_items, function(i, v) {
            if (v.barang_detail_id === barang_detail_id) {
                $(".barang_detail_id").val(v.barang_detail_id);
                $(".barang_id").val(v.barang_id);
                $(".barang_spesifikasi_id").val(v.barang_spesifikasi_id);
                $(".kode_barang").val(v.kode_barang);
                $(".nama_barang").val(v.nama_barang);
                $(".satuan_id").val(v.satuan_id).change();
                $(".qty").val(v.qty);
                $(".keterangan").val(v.keterangan);

                barang_id = v.barang_id;
                barang_spesifikasi_id = v.barang_spesifikasi_id;
            }
        });

        var spp_type = $('.spp_type').val().trim();
        var type = "";
        if (spp_type == "Import BB" || spp_type == "Lokal BB") {
            type = "bahan_baku";
        } else {
            type = "bahan_penolong";
        }

        $.ajax({
            url: `<?= base_url("barang/dropdown/type-server-first"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                type: type,
                barang_spesifikasi_id: barang_spesifikasi_id
            },
            success: function(res) {
                $(".kode_barang").empty();
                $(".kode_barang").append(`<option data-barang_name_master=""  data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
                res.data.forEach(function(item) {
                    console.log(item, barang_id, barang_spesifikasi_id);
                    if (barang_id == item.barang_id && barang_spesifikasi_id == item.barang_spesifikasi_id) {
                        $(".kode_barang").append(`<option selected 
                        data-satuan_1="${item.satuan_1}" 
                        data-satuan_2="${item.satuan_2}" 
                        data-satuan_3="${item.satuan_3}" 
                        data-barang_name_master="${item.barang_name_master}" 
                        data-barang_spesifikasi_id="${item.barang_spesifikasi_id}" 
                        data-barang_id="${item.id}" 
                        data-nama="${item.nama}" 
                        data-satuan_id="${item.satuan_1}" 
                        data-satuan="${item.nama_satuan}" 
                        value="${item.kode_barang}">
                        ${item.kode_barang} - ${item.nama}
                        </option>`);
                    }
                })

                var kode_barang_selected = $(".kode_barang option:selected").change();
                activeFieldSatuanId(
                    kode_barang_selected.data('satuan_1'),
                    kode_barang_selected.data('satuan_2'),
                    kode_barang_selected.data('satuan_3')
                );

                $(".detail-modal").modal("show");
            }
        })
    });

    $('.btn-hide-detail').click(function() {
        $('.detail-modal').modal('hide');
        resetFormDetail();
    });

    const submitDetailForm = function() {
        let barang_detail_id = $(".barang_detail_id").val();
        let barang_id = $(".barang_id").val()
        let barang_spesifikasi_id = $(".barang_spesifikasi_id").val();
        let kode_barang = $(".kode_barang option:selected").data('kode_barang');
        let nama_barang = $(".nama_barang").val()
        let nama_satuan = $(".satuan_id option:selected").data('kode_satuan')
        let satuan_id = $(".satuan_id option:selected").val()
        let qty = $(".qty").val()
        let keterangan = $(".keterangan").val() ? $(".keterangan").val() : '-'

        let header_barang_name = $('.header_barang_name').val();
        let spp_type = $('.spp_type').val().trim();

        let validate_same = false;
        let validate_bahan_baku = false;

        if (barang_detail_id === '') {
            list_items.map(item => {
                if (item.barang_id === barang_id && item.barang_spesifikasi_id === barang_spesifikasi_id) {
                    validate_same = true
                }
            });
        }

        if (list_items.length >= 1 && spp_type === "Lokal BB") {
            list_items.map(item => {
                if (item.barang_id !== barang_id) {
                    validate_bahan_baku = true;
                }
            });
        }

        if (validate_bahan_baku) {
            Swal.fire({
                icon: 'error',
                title: 'Barang harus sejenis',
                // title: "Header Barang Wajib " + header_barang_name.toUpperCase() + " (Karena ini merupakan PO Lokal Bahan Baku)",
                confirmButtonColor: '#4e73df',
            })
        } else

            // if (validate_same) {
            //     Swal.fire({
            //         icon: 'error',
            //         title: "Barang Sudah Ada",
            //         confirmButtonColor: '#4e73df',
            //     })
            // } else {

            if (barang_detail_id) {
                if ($(".detail-form").valid()) {
                    // UPDATE DETAIL
                    $.each(list_items, function(i, v) {
                        if (v.barang_detail_id === barang_detail_id) {
                            list_items[i].barang_id = barang_id;
                            list_items[i].barang_spesifikasi_id = barang_spesifikasi_id;
                            list_items[i].kode_barang = kode_barang;
                            list_items[i].nama_barang = nama_barang;
                            list_items[i].nama_satuan = nama_satuan;
                            list_items[i].satuan_id = satuan_id;
                            list_items[i].qty = qty;
                            list_items[i].keterangan = keterangan;
                        }
                    });
                    drawTableDetail();
                    $(".detail-modal").modal("hide");
                }
            } else {
                if ($(".detail-form").valid()) {
                    // CREATE DETAIL
                    if (barang_id !== "") {
                        list_items.push({
                            'barang_detail_id': getID(),
                            'barang_id': barang_id,
                            'barang_spesifikasi_id': barang_spesifikasi_id,
                            'kode_barang': kode_barang,
                            'nama_barang': nama_barang,
                            'nama_satuan': nama_satuan,
                            'satuan_id': satuan_id,
                            'qty': qty,
                            'keterangan': keterangan
                        });
                    }
                    resetFormDetail();
                    drawTableDetail();
                    $(".detail-modal").modal("hide");
                }
            }

    }
    // }



    const updateStatusPosting = function(status) {
        Swal.fire({
            icon: 'question',
            title: status == '0' ? 'Unposting SPP ?' : 'Posting SPP ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: status == '0' ? 'Unposting' : 'Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("spp/update-status"); ?>",
                    data: {
                        id: "<?= !empty($dataSPP) ? encrypt($dataSPP->id) : '0' ?>",
                        status: status
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.replace("<?= base_url('spp') ?>");
                                })
                        }
                    },

                });
            }
        })
    }

    const changeTipeSPP = function() {
        list_items = [];
        drawTableDetail();
        drawTable();
    }

    const drawTable = function() {
        $('.body-detail-table-spek').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items_spek.length === 0) {
            row += `
                    <tr>
                        <td colspan="7" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.tfoot').append(row);
        } else {
            list_items_spek.map(item => {
                row += '<tr style="color:whitesmoke;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.spesifikasi + '</td>';
                row += '<td>' + item.satuan_1_text + '</td>';
                row += '<td>' + item.satuan_2_text + '(' + item.konversi_satuan_2 + ' ' + item.satuan_1_text + ')</td>';
                row += '<td>' + item.satuan_3_text + '(' + item.konversi_satuan_3 + ' ' + item.satuan_1_text + ')</td>';

                row += '<td>' + `
                    <button class="btn btn-warning edit-table-detail-spek mr-1" data-spek_id="${item.spek_id}">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button><button class="btn btn-danger" onclick="deleteRowSpek('${item.spek_id}', '${item.spesifikasi_id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                    '</td>';

                no++;
            });
            $('.body-detail-table-spek').append(row);
        }
    }

    $(document).on('click', '.edit-table-detail-spek', function(evt) {
        resetFormDetail();
        $(".title-detail-name-spek .btn-text").text("Update");
        $(".title-detail-icon-spek").removeClass("fa-plus").addClass("fa-exchange");

        validator_spek.resetForm();
        validator_spek.reset();
        let spek_id = $(this).data('spek_id');

        $.each(list_items_spek, function(i, v) {
            if (v.spek_id === spek_id) {
                $("#spek_id").val(v.spek_id);
                $("#spesifikasi_id").val(v.id);
                $("#spek").val(v.spesifikasi);
                $("#satuan1_id").val(v.satuan_1).change();
                $("#satuan2_id").val(v.satuan_2).change();
                $("#konversi_satuan_2").val(v.konversi_satuan_2);
                $("#satuan3_id").val(v.satuan_3).change();
                $("#konversi_satuan_3").val(v.konversi_satuan_3);
            }
        });
    });


    const deleteRowSpek = function(id, spesifikasi_id) {
        if (spesifikasi_id) {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data Spesifikasi?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("barang-master/delete-spek"); ?>",
                        data: {
                            id: spesifikasi_id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        $('.dataTable').DataTable().ajax.reload();
                                        $(".add-modal").modal("hide")
                                    });
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    });
                }
            })
        } else {
            const indexToRemove = list_items_spek.findIndex(item => item.spek_id === id);
            if (indexToRemove !== -1) {
                list_items_spek.splice(indexToRemove, 1);
            }
        }
        drawTable();
    }

    const deleteRowDetail = function(id, detail_id) {
        if (detail_id != 'undefined') {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data Detail?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("spp/delete-detail"); ?>",
                        data: {
                            id: detail_id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        location.reload();
                                    });
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    });
                }
            })
        } else {
            const indexToRemove = list_items.findIndex(item => item.barang_detail_id === id);
            if (indexToRemove !== -1) {
                list_items.splice(indexToRemove, 1);
            }
        }
        drawTableDetail();
    }

    const changeDepartment = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".spp_no").attr("readonly", true);

            $.ajax({
                url: `<?= base_url("spp/generate"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    divisi_name: $(".divisi_id option:selected").text()
                },
                success: function(res) {
                    $(".spp_no").val(res.data);
                }
            })
        }
    }

    $('#request_date,#divisi_id,#spp_type').change(function() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            changeStatus();
        }
    })

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".spp_no").attr("readonly", true);

            $.ajax({
                url: `<?= base_url("spp/generate"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    divisi_name: $(".divisi_id option:selected").text(),
                    request_date: $('#request_date').val(),
                    spp_type: $('.spp_type').val()
                },
                success: function(res) {
                    $(".spp_no").val(res.data);
                }
            })
        } else {
            $(".spp_no").attr("readonly", false);
            $(".spp_no").val("");
        }
    }
    const resetFormDetail = function() {
        $(".title-detail-name-spek .btn-text").text("Tambah");
        $(".title-detail-icon-spek").removeClass("fa-update").addClass("fa-plus");
        $("#spek_id").val('');
        $("#spek").val('');
        $("#satuan1_id").val('').change()
        $("#satuan2_id").val('').change()
        $("#konversi_satuan_2").val('');
        $("#satuan3_id").val('').change()
        $("#konversi_satuan_3").val('');
        $('#satuan_id').val(null).change();
    }

    const print = function(id) {
        location.replace("<?= base_url('spp/print/') ?>" + id)
    }

    const getID = function() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };


    var validator_spek = $(".spek-form").validate({
        rules: {
            spek: {
                required: true
            },
            satuan1_id: {
                required: true
            }
        },
        messages: {
            spek: {
                required: "Spesifikasi wajib diisi"
            },
            satuan1_id: {
                required: "Satuan 1 wajib diisi"
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

    var validator_barang = $(".create-form-barang").validate({
        rules: {
            parent_type_id: {
                required: true
            },
            barang_name: {
                required: true
            },
            kode_barang: {
                required: true
            }
        },
        messages: {
            parent_type_id: {
                required: "Kategori Barang Wajib Diisi"
            },
            barang_name: {
                required: "Nama Barang Wajib Diisi"
            },
            kode_barang: {
                required: "Kode Barang Wajib Diisi"
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
    $(document).ready(function() {
        $('.btn-add-barang').click(function() {
            $(".detail-modal").modal("hide")
            // $('.title-name').text("Tambah Bahan Baku");
            $(".create-form-barang :input:not([name='type'])").val('');
            var spp_type = $('.spp_type_bypass').val();
            $("#type").val(spp_type);
            if (spp_type) {
                $.ajax({
                    url: `<?= base_url("stock-list/kategori-barang"); ?>`,
                    method: "GET",
                    dataType: "json",
                    data: {
                        parent_type: spp_type
                    },
                    success: function(res) {
                        $("#parent_type_id").empty();
                        $("#parent_type_id").append(`<option value=""></option>`);
                        res.data.forEach(function(item) {
                            $("#parent_type_id").append(`<option value="${item.id}">${item.parent_name}</option>`);
                        })
                        // $(".parent_type_id").val("").change();
                    }
                })
            }

            $('.delete-btn').hide();
            $('input[name="kode_barang"]').attr('readonly', false);
            $('#generate_new_code').prop('checked', true).change().show();
            $('.add-modal').modal('show');
            $('#tbody2').empty();

            list_items_spek.splice(0, list_items_spek.length);
            drawTable();
            drawTableDetail();
            validator_spek.resetForm();
            validator_spek.reset();
            validator_barang.resetForm();
            validator_barang.reset();
        });

        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });

        var validator = $(".create-form-barang").validate({
            rules: {
                parent_type_id: {
                    required: true
                },
                barang_name: {
                    required: true
                },
                kode_barang: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                minimum_stock: {
                    required: true
                },
            },
            messages: {
                parent_type_id: {
                    required: "Kategori Barang Wajib Diisi"
                },
                barang_name: {
                    required: "Nama Barang Wajib Diisi"
                },
                kode_barang: {
                    required: "Kode Barang Wajib Diisi"
                },
                satuan_id: {
                    required: "Satuan Barang Wajib Diisi"
                },
                minimum_stock: {
                    required: "Minimal stock harus diisi",
                    number: "Masukkan angka valid"
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
        $('#btn-submit-form-barang').click(function(e) {
            e.preventDefault();
            if (list_items_spek.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Masukkan Spesifikasi Barang Dahulu',
                    confirmButtonColor: '#4e73df',
                });
            } else {
                if ($(".create-form-barang").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('input[name="id"]').val();
                            let csrf = $(`[name="${csrfToken}"]`);
                            let data = new FormData(document.querySelector(".create-form-barang"));
                            data.append("items", JSON.stringify(list_items_spek));
                            $.ajax({
                                url: "<?= base_url("barang-master/save"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                $("#add_modal").modal("hide");
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {

                                        });
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            });
                        }
                    })
                }
            }
        });
    });

    var counter = 2; // Counter variable for rowspan

    function addRow(tableID) {
        var table = document.getElementById(tableID);
        var row = table.insertRow();
        var row2 = table.insertRow();

        // Create cells with appropriate colspan
        row.innerHTML = `
    <td rowspan="2" style="padding:0px!important;text-align:center;">
        <span id="nomber">${counter}</span>
    </td>
    <td colspan="3">
        <div class="row">
            <div class="col-sm-12" style="padding:0px!important;">
                <div class="form-floating">
                    <input type="text" name="spek[]" id="spek" class="form-control">
                    <label for="floatingInput">Spesifikasi</label>
                </div>
            </div>
        </div>
    </td>
    <td rowspan="2" style="padding:0px!important;text-align:center;">
        <button type="button" class="btn btn-primary" onclick="addRow('tbody2')"><i class="fas fa-plus"></i></button>
        <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')"><i class="far fa-trash-alt"></i></button>
    </td>`;
        row2.innerHTML = `
    <td style="width: 15%;">
        <div class="row">
            <div class="col-sm-12" style="padding:0px!important;">
                <div class="form-floating">
                    <select class="form-select" name="satuan1_id[]" id="satuan1_id_${counter}" title="Satuan terkecil dari produk. Cth: PCS" onchange="changeSpanText(${counter})">
                        <option value=""></option>
                        <?php foreach ($satuanBarang as $sb) : ?>
                            <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="floatingInput">Satuan 1</label>
                </div>
            </div>
        </div>
    </td>
    <td style="width: 30%;">
        <div class="row">
            <div class="col-sm-6" style="padding:0px!important;">
                <div class="form-floating">
                    <select class="form-select" name="satuan2_id[]" id="satuan2_id_${counter}" title="Satuan yang lebih besar dari Satuan 1. Cth: LUSIN (12 Pcs)" onchange="checkSatuan2(${counter})">
                        <option value=""></option>
                        <?php foreach ($satuanBarang as $sb) : ?>
                            <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="floatingInput">Satuan 2</label>
                </div>
            </div>
            <div class="col-sm-6" style="padding:0px!important;">
                <div class="input-group ">
                    <input type="text" name="konversi_satuan_2[]" id="konversi_satuan_2_${counter}" onchange="checkValueKonversi(${counter})" class="form-control" onkeypress="return isNumberKey(event)">
                    <div class="input-group-append">
                        <span class="input-group-text satuan_${counter}">-</span>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td style="width: 30%;">
        <div class="row">
            <div class="col-sm-6" style="padding:0px!important;">
                <div class="form-floating">
                    <select class="form-select" name="satuan3_id[]" id="satuan3_id_${counter}" title="Satuan terbesar dari produk. Cth: DUS (konversi 48 PCS)" onchange="checkSatuan3(${counter})">
                        <option value=""></option>
                        <?php foreach ($satuanBarang as $sb) : ?>
                            <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="floatingInput">Satuan 3</label>
                </div>
            </div>
            <div class="col-sm-6" style="padding:0px!important;">
                <div class="input-group ">
                    <input type="text" name="konversi_satuan_3[]" id="konversi_satuan_3_${counter}" onchange="checkValueKonversi(${counter})" class="form-control" onkeypress="return isNumberKey(event)">
                    <div class="input-group-append">
                        <span class="input-group-text satuan_${counter}">-</span>
                    </div>
                </div>
            </div>
        </div>
    </td>`;
        $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`)
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`)
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`)
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(`#satuan_id_${counter}, #satuan1_id_${counter}, #satuan2_id_${counter}, #satuan3_id_${counter}`).select2({
            theme: "bootstrap-5",
            allowClear: true,
            placeholder: 'Pilih Satuan',
            dropdownParent: $(".add-modal .modal-content")
        });

        counter++;
        // rows++;
    }

    function deleteRow(tableID, counters = null) {
        try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
            let csrfToken = '<?= csrf_token() ?>';

            // Variable to track whether any checkbox is checked
            var isChecked = false;

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                var chkbox = row.cells[0].childNodes[0];

                if (null != chkbox && true == chkbox.checked) {
                    isChecked = true;
                    table.deleteRow(i);
                    table.deleteRow(i - 1); // Remove the previous row as well
                    rowCount -= 2; // Reduce rowCount by 2
                    i--;
                }
            }

            // If no checkbox is checked, remove the last two rows
            if (!isChecked && rowCount > 2) {
                if (counters) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Hapus Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            let idSpek = document.getElementById('id_spek_' + counters).value;
                            setLoading()
                            $.ajax({
                                url: "<?= base_url("barang-master/delete-spek"); ?>",
                                data: {
                                    id: idSpek
                                },
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        stopLoading()
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                $('.dataTable').DataTable().ajax.reload()
                                                $("#add_modal").modal("hide")
                                            });
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            });
                        }
                    })

                } else {
                    table.deleteRow(rowCount - 1);
                    table.deleteRow(rowCount - 2);
                    rowCount -= 2;
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Baris terakhir tidak boleh dihapus!!!',
                    confirmButtonColor: '#4e73df',
                })
            }
            var lastRow = table.rows[rowCount - 2];
            var currentCount = parseInt(lastRow.querySelector('#nomber').innerText);
            counter = currentCount + 1;

        } catch (e) {
            alert(e);
        }
    }

    const generateNewCode = function() {
        let value = document.getElementById('generate_new_code').checked ? true : false;

        var spp_type = $('.spp_type_bypass').val();
        if (value) {
            $("input[name='kode_barang']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("barang-master/generate-new-code"); ?>`,
                data: {
                    type: spp_type
                },
                method: "GET",
                success: function(res) {
                    csrf.val(res.token);
                    $("input[name='kode_barang']").attr("readonly", true);
                    $("input[name='kode_barang']").val(res.codeNew);
                }
            })
        } else {
            $("input[name='kode_barang']").attr("readonly", false);
            $("input[name='kode_barang']").val("");
        }
    }

    function changeSpanText(counter = null) {
        var selectedText, selectedSatuan1Val, selectedSatuan2Val, selectedSatuan3Val, spanText;

        if (counter) {
            selectedText = $(`#satuan1_id_${counter}`).find('option:selected').text();
            selectedSatuan1Val = $(`#satuan1_id_${counter}`).val();
            selectedSatuan2Val = $(`#satuan2_id_${counter}`).val();
            selectedSatuan3Val = $(`#satuan3_id_${counter}`).val();
            spanText = $(`.satuan_${counter}`);
        } else {
            selectedText = $('#satuan1_id').find('option:selected').text();
            selectedSatuan1Val = $('#satuan1_id').val();
            selectedSatuan2Val = $('#satuan2_id').val();
            selectedSatuan3Val = $('#satuan3_id').val();
            spanText = $('.satuan1');
        }

        if (selectedSatuan1Val === selectedSatuan2Val && selectedSatuan1Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 2 tidak boleh sama dengan satuan 1',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#satuan2_id_${counter}`).val('').change();
                    $(`#satuan3_id_${counter}`).val('').change();
                } else {
                    $('#satuan2_id').val('').change();
                    $('#satuan3_id').val('').change();
                }
            });
        } else if (selectedSatuan1Val === selectedSatuan3Val && selectedSatuan1Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 3 tidak boleh sama dengan satuan 1',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#satuan3_id_${counter}`).val('').change();
                } else {
                    $('#satuan3_id').val('').change();
                }
            });
        } else if (selectedSatuan2Val === selectedSatuan3Val && selectedSatuan2Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 3 tidak boleh sama dengan satuan 2',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {

                    $(`#satuan3_id_${counter}`).val('').change();
                } else {
                    $('#satuan3_id').val('').change();
                }
            });
        } else {
            if (selectedSatuan1Val == "") {
                if (counter) {

                    $('#satuan2_id_' + counter + '').val('').change();
                    $(`#konversi_satuan_2_${counter}`).val('');
                    $(`#satuan3_id_${counter}`).val('').change();
                    $(`#konversi_satuan_3_${counter}`).val('');
                } else {
                    $('#satuan2_id').val('').change();
                    $(`#konversi_satuan_2`).val('');
                    $('#satuan3_id').val('').change();
                    $(`#konversi_satuan_3`).val('');
                }
            }
        }
        // Ubah konten span sesuai dengan nilai yang dipilih
        spanText.text(selectedText ? selectedText : '-');
    }

    function checkSatuan2(counter = null) {
        var selectedText, selectedSatuan1Val, selectedSatuan2Val, selectedSatuan3Val, spanText;

        if (counter) {
            selectedSatuan1Val = $(`#satuan1_id_${counter}`).val();
            selectedSatuan2Val = $(`#satuan2_id_${counter}`).val();
            selectedSatuan3Val = $(`#satuan3_id_${counter}`).val();
            spanText = $(`.satuan_${counter}`);
        } else {
            selectedSatuan1Val = $('#satuan1_id').val();
            selectedSatuan2Val = $('#satuan2_id').val();
            selectedSatuan3Val = $('#satuan3_id').val();
            spanText = $('.satuan1');
        }

        if (selectedSatuan2Val === selectedSatuan1Val && selectedSatuan2Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 2 tidak boleh sama dengan satuan 1',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#satuan2_id_${counter}`).val('').change();
                } else {
                    $('#satuan2_id').val('').change();
                }
            });
        } else if (selectedSatuan2Val === selectedSatuan3Val && selectedSatuan2Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 2 tidak boleh sama dengan satuan 3',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#satuan2_id_${counter}`).val('').change();
                } else {
                    $('#satuan2_id').val('').change();
                }
            });
        } else {
            if (selectedSatuan2Val == "") {
                if (counter) {
                    $(`#konversi_satuan_2_${counter}`).val('');
                    $(`#satuan3_id_${counter}`).val('').change();
                    $(`#konversi_satuan_3_${counter}`).val('');
                } else {
                    $(`#konversi_satuan_2`).val('');
                    $('#satuan3_id').val('').change();
                    $(`#konversi_satuan_3`).val('');
                }
            }
        }
    }

    function checkSatuan3(counter = null) {
        var selectedText, selectedSatuan1Val, selectedSatuan2Val, selectedSatuan3Val, spanText;

        if (counter) {
            selectedSatuan1Val = $(`#satuan1_id_${counter}`).val();
            selectedSatuan2Val = $(`#satuan2_id_${counter}`).val();
            selectedSatuan3Val = $(`#satuan3_id_${counter}`).val();
            spanText = $(`.satuan_${counter}`);
        } else {
            selectedSatuan1Val = $('#satuan1_id').val();
            selectedSatuan2Val = $('#satuan2_id').val();
            selectedSatuan3Val = $('#satuan3_id').val();
            spanText = $('.satuan1');
        }

        if (selectedSatuan3Val === selectedSatuan1Val && selectedSatuan3Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 3 tidak boleh sama dengan satuan 1',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#satuan3_id_${counter}`).val('').change();
                } else {
                    $('#satuan3_id').val('').change();
                }
            });
        } else if (selectedSatuan3Val === selectedSatuan2Val && selectedSatuan3Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 3 tidak boleh sama dengan satuan 2',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#satuan3_id_${counter}`).val('').change();
                } else {
                    $('#satuan3_id').val('').change();
                }
            });
        }
    }

    function checkValueKonversi(counter = null) {
        var selectedSatuan2Val, selectedSatuan3Val;

        if (counter) {
            selectedSatuan2Val = $(`#konversi_satuan_2_${counter}`).val();
            selectedSatuan3Val = $(`#konversi_satuan_3_${counter}`).val();
        } else {
            selectedSatuan2Val = $('#konversi_satuan_2').val();
            selectedSatuan3Val = $('#konversi_satuan_3').val();
        }

        if (Number(selectedSatuan3Val) <= Number(selectedSatuan2Val) && selectedSatuan3Val !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 3 harus lebih besar dari satuan 2',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#konversi_satuan_3_${counter}`).val('').change();
                } else {
                    $('#konversi_satuan_3').val('').change();
                }
            });
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

    const submitSpekForm = function() {
        let spek = $("#spek").val().trim();
        let satuan1_id = $("#satuan1_id").val();
        let satuan1_text = $("#satuan1_id option:selected").text(); // Mendapatkan teks dari opsi yang dipilih
        let satuan2_id = $("#satuan2_id").val();
        let satuan2_text = $("#satuan2_id option:selected").text(); // Mendapatkan teks dari opsi yang dipilih
        let konversi_satuan_2 = $("#konversi_satuan_2").val();
        let satuan3_id = $("#satuan3_id").val();
        let satuan3_text = $("#satuan3_id option:selected").text(); // Mendapatkan teks dari opsi yang dipilih
        let konversi_satuan_3 = $("#konversi_satuan_3").val();
        let spek_id = $("#spek_id").val();
        let spesifikasi_id = $("#spesifikasi_id").val();

        if ($(".create-form-barang").valid()) {
            if ($(".spek-form").valid()) {
                if (spek_id) {
                    $.each(list_items_spek, function(i, v) {
                        if (v.spek_id === spek_id) {
                            list_items_spek[i].spesifikasi = spek;
                            list_items_spek[i].satuan_1 = satuan1_id;
                            list_items_spek[i].satuan_1_text = satuan1_text;
                            list_items_spek[i].satuan_2 = satuan2_id;
                            list_items_spek[i].satuan_2_text = satuan2_text;
                            list_items_spek[i].konversi_satuan_2 = konversi_satuan_2;
                            list_items_spek[i].satuan_3 = satuan3_id;
                            list_items_spek[i].satuan_3_text = satuan3_text;
                            list_items_spek[i].konversi_satuan_3 = konversi_satuan_3;
                        }
                    });
                    resetFormDetail();
                    drawTable();
                } else {
                    let isDuplicate = list_items_spek.some(function(item) {
                        return item.spesifikasi.toUpperCase() === spek.toUpperCase();
                    });

                    if (!isDuplicate) {
                        list_items_spek.push({
                            'spek_id': getID(),
                            'spesifikasi_id': "",
                            'spesifikasi': spek.toUpperCase(),
                            'satuan_1': satuan1_id,
                            'satuan_1_text': satuan1_text,
                            'satuan_2': satuan2_id,
                            'satuan_2_text': satuan2_text,
                            'konversi_satuan_2': konversi_satuan_2,
                            'satuan_3': satuan3_id,
                            'satuan_3_text': satuan3_text,
                            'konversi_satuan_3': konversi_satuan_3
                        });
                        resetFormDetail();
                        drawTable();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Spesifikasi sudah ada!',
                            confirmButtonColor: 'red',
                        })
                    }
                }
            }
        }
    }

    const drawTableDetail = function() {
        $('.body-detail-table').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items.length === 0) {
            row += `
                    <tr>
                        <td colspan="7" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.tfoot').append(row);
        } else {
            list_items.map(item => {
                row += '<tr style="color:whitesmoke;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + item.nama_satuan + '</td>';
                row += '<td>' + item.qty + '</td>';
                row += '<td>' + item.keterangan + '</td>';
                <?php if (!empty($dataSPP)) : ?>
                    <?php if ($dataSPP->is_posted == '0') : ?>
                        row += '<td>' + `
                    <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-barang_detail_id="${item.barang_detail_id}" >
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button><button class="btn btn-danger" onclick="deleteRowDetail('${item.barang_detail_id}', '${item.purchase_detail_id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                            '</td>';
                    <?php endif; ?>
                <?php else : ?>
                    row += '<td>' + `
                    <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-barang_detail_id="${item.barang_detail_id}" >
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button><button class="btn btn-danger" onclick="deleteRowDetail('${item.barang_detail_id}', '${item.purchase_detail_id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                        '</td>';
                <?php endif; ?>

                no++;
            });
            $('.body-detail-table').append(row);
        }
    }

    // Update
    <?php if (!empty($dataSPP)) : ?>
        <?php foreach ($dataSPPDetail as $i => $d) : ?>
            list_items.push({
                'barang_detail_id': getID(),
                'purchase_detail_id': "<?= encrypt($d->id) ?>",
                'barang_id': "<?= encrypt($d->barang1_id) ?>",
                'barang_spesifikasi_id': "<?= encrypt($d->barang2_id) ?>",
                'kode_barang': "<?= $d->kode_barang ?>",
                'nama_barang': <?= json_encode($d->barang_name . " " . $d->spesifikasi) ?>,
                'nama_satuan': "<?= $d->kode_satuan ?>",
                'satuan_id': "<?= $d->unit ?>",
                'qty': "<?= $d->qty ?>",
                'keterangan': "<?= str_replace('"', '\"', $d->note)  ?>"
            });
        <?php endforeach; ?>
        drawTableDetail();
    <?php endif; ?>

    // $(document).keydown(function(e) {
    //     if (e.keyCode === 116) {
    //         e.preventDefault();
    //         var spp_type = $('.spp_type').val().trim();
    //         var type = "";

    //         if (spp_type === "Import BB" || spp_type === "Lokal BB") {
    //             type = "bahan_baku";
    //         } else {
    //             type = "bahan_penolong";
    //         }
    //         $(".spp_type_bypass").val(type)
    //         if (spp_type) {
    //             $.ajax({
    //                 url: `<?= base_url("barang/dropdown/type"); ?>`,
    //                 method: "GET",
    //                 dataType: "json",
    //                 data: {
    //                     type: type
    //                 },
    //                 success: function(res) {
    //                     $(".kode_barang").empty();
    //                     $(".kode_barang").append(`<option data-barang_name_master="" data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
    //                     res.data.forEach(function(item) {
    //                         $(".kode_barang").append(`<option data-barang_name_master="${item.barang_name_master}" data-barang_spesifikasi_id="${item.barang_spesifikasi_id}" data-barang_id="${item.id}" data-nama="${item.barang_name}" data-satuan_id="${item.satuan_1}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
    //                     })
    //                     $(".kode_barang").val("").change();
    //                     $(".detail-modal").modal("show");
    //                 }
    //             })
    //         } else {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: "Pilih Tipe SPP Dahulu",
    //                 confirmButtonColor: '#4e73df',
    //             })
    //         }
    //     }
    // });

    function activeFieldSatuanId(satuan_1, satuan_2, satuan_3) {
        const allowedValues = [String(satuan_1), String(satuan_2), String(satuan_3)];
        $('#satuan_id').on('select2:open', function() {
            $('#satuan_id option').each(function() {
                if (!allowedValues.includes(String($(this).val()))) {
                    $(this).attr('disabled', true).addClass('disabled-option');
                } else {
                    $(this).attr('disabled', false).removeClass('disabled-option');
                }
            });
        });
    }

    function getTypeSPP(){
        var spp_type = $('.spp_type').val().trim();
        var type = "";

        if (spp_type === "Import BB" || spp_type === "Lokal BB") {
            type = "bahan_baku";
        } else {
            type = "bahan_penolong";
        }

        console.log(type);
        console.log($('.spp_type').val().trim());
        return type;
    }
</script>
<script>
    $("select[name='parent_type_id']").select2({
        placeholder: "Pilih Kategori Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content")
    });


    $("#satuan1_id, #satuan2_id, #satuan3_id").select2({
        theme: "bootstrap-5",
        allowClear: true,
        placeholder: 'Pilih Satuan',
        dropdownParent: $(".add-modal .modal-content")
    });

    $("#satuan1_id, #satuan2_id, #satuan3_id")
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $("select[name='parent_type_id'],#satuan1_id, #satuan2_id, #satuan3_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $("select[name='parent_type_id'],#satuan1_id, #satuan2_id, #satuan3_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $("select[name='parent_type_id'],#satuan1_id, #satuan2_id, #satuan3_id")
        .parent('div')
        .find('label')
        .css('z-index', '1');
</script>
<?= $this->endSection(); ?>
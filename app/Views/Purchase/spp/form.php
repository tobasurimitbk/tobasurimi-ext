<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah SPP</h1>
        <div class="col-button-tambah-spp">
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
                    <?php if (can('Pembelian', 'SPP', 'ua')) : ?>
                        <button onclick="updateStatusPosting('0')" class="btn btn-success un-posting-spp float-right posting-spp">
                            Un Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

            <?php } ?>

            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("spp"); ?>">
                Batal
            </a>

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
                                    <input <?= !empty($dataSPP) ? 'readonly' : '' ?> autocomplete="one-time-code" <?= !empty($dataSPP) ? ($dataSPP->is_posted === "1" ? 'readonly=true' : '') : ''; ?> type="text" class="form-control spp_no" id="spp_no" name="spp_no" placeholder="No. SPP" value="<?= !empty($dataSPP) ? $dataSPP->spp_no : ""; ?>">
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
                            <select onchange="changeTipeSPP()" <?= !empty($dataSPP) ? 'disabled="true"' : ''; ?> class="form-select spp_type" name="spp_type" id="spp_type" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataSppType as $d) : ?>
                                    <option <?= (!empty($dataSPP) ? ($dataSPP->spp_type == $d['value'] ? 'selected' : '') : '') ?> value="<?= $d['value'] ?>"><?= $d['value'] ?></option>
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
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="barang_spesifikasi_id" class="barang_spesifikasi_id" id="barang_spesifikasi_id">
                    <input autocomplete="one-time-code" type="hidden" class="barang_detail_id" name="barang_detail_id" id="barang_detail_id" />
                    <input autocomplete="one-time-code" type="hidden" class="barang_id" name="barang_id" id="barang_id" />
                    <input autocomplete="one-time-code" type="hidden" class="header_barang_name" name="header_barang_name" id="header_barang_name" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="hidden" class="kode" name="kode" id="kode" />
                                <select class="form-select kode_barang" name="kode_barang" id="kode_barang" aria-label="Floating label select example">
                                    <option data-barang_id="" data-nama="" data-satuan="" value=""></option>
                                </select>
                                <label for="floatingInput">Kode Barang</label>
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
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="hidden" class="satuan_id" name="satuan_id">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" readonly="true" type="text" class="form-control satuan" id="satuan" name="satuan" placeholder="Satuan">
                                    <label for="floatingInput">Satuan</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" class="form-control qty" name="qty" id="qty" placeholder="Qty">
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
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail" onclick="submitDetailForm()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    let list_items = [];

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
            satuan: {
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
            satuan: {
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

    //CSS SELECT2 FLOATING LABEL
    $('.divisi_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.divisi_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.divisi_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // PILIH TIPE SPP
    $('.spp_type').select2({
        placeholder: "Pilih SPP",
        theme: "bootstrap-5"
    });

    $('.spp_type')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.spp_type')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.spp_type')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // KODE BARANG
    $('.kode_barang').select2({
        placeholder: "Pilih Kode Barang",
        theme: "bootstrap-5",
        dropdownParent: $(".detail-modal .modal-content"),
        tags: false,
        allowClear: true
    })

    //CSS SELECT2 FLOATING LABEL
    $('.kode_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.kode_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.kode_barang')
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
                    cancelButtonText: 'Batal',
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
                                                location.reload();
                                            })
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
                                            window.location.replace("<?= base_url('spp/id/') ?>" + response.id);
                                        })
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

        $(".title-detail-name").text("Tambah");
        $(".barang_detail_id").val('');

        $(".kode").val('')
        $(".nama_barang").val('')
        $(".qty").val('')
        $(".satuan").val('')
        $(".satuan_id").val('')
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
        if (spp_type) {
            $.ajax({
                url: `<?= base_url("barang/dropdown/type"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    type: type
                },
                success: function(res) {
                    $(".kode_barang").empty();
                    $(".kode_barang").append(`<option data-barang_name_master="" data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
                    res.data.forEach(function(item) {
                        $(".kode_barang").append(`<option data-barang_name_master="${item.barang_name_master}" data-barang_spesifikasi_id="${item.barang_master_spesifikasi_id}" data-barang_id="${item.id}" data-nama="${item.barang_name}" data-satuan_id="${item.satuan_1}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
                    })
                    $(".kode_barang").val("").change();
                    $(".detail-modal").modal("show");
                }
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: "Pilih Tipe SPP Dahulu",
                confirmButtonColor: '#4e73df',
            })
        }

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".kode_barang").change(function() {
            if ($(".kode_barang option:selected").val()) {
                let nama = $(".kode_barang option:selected").data("nama") ? $(".kode_barang option:selected").data("nama") : "";
                let satuan = $(".kode_barang option:selected").data("satuan") ? $(".kode_barang option:selected").data("satuan") : "";
                let satuan_id = $(".kode_barang option:selected").data("satuan_id") ? $(".kode_barang option:selected").data("satuan_id") : "";
                let barang_id = $(".kode_barang option:selected").data("barang_id") ? $(".kode_barang option:selected").data("barang_id") : "";
                let barang_spesifikasi_id = $(".kode_barang option:selected").data("barang_spesifikasi_id") ? $(".kode_barang option:selected").data("barang_spesifikasi_id") : "";
                let barang_name_master = $(".kode_barang option:selected").data("barang_name_master");
                let spp_type = $('.spp_type').val().trim();


                if (list_items.length === 0 && spp_type === "Lokal BB") {
                    $('.header_barang_name').val(barang_name_master);
                }

                $(".kode").val($(".kode_barang option:selected").val());
                $(".nama_barang").val(nama);
                $(".barang_id").val(barang_id);
                $(".barang_spesifikasi_id").val(barang_spesifikasi_id);
                $(".satuan").val(satuan);
                $(".satuan_id").val(satuan_id);
            } else {
                $(".kode").val("");
                $(".nama_barang").val("");
                $(".barang_id").val("");
                $(".barang_spesifikasi_id").val("");
                $(".satuan").val("");
                $(".satuan_id").val("");
            }
        })
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
            cancelButtonText: 'Batal',
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
        $(".title-detail-name").text("Update")

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
                $(".nama_satuan").val(v.nama_satuan);
                $(".satuan_id").val(v.satuan_id);
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
            url: `<?= base_url("barang/dropdown/type"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                type: type
            },
            success: function(res) {
                $(".kode_barang").empty();
                $(".kode_barang").append(`<option data-barang_name_master=""  data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
                res.data.forEach(function(item) {
                    if (barang_id === item.id && barang_spesifikasi_id === item.barang_master_spesifikasi_id) {
                        $(".satuan_id").val(item.satuan_1);
                        $(".satuan").val(item.nama_satuan);
                        $(".kode_barang").append(`<option selected data-barang_name_master="${item.barang_name_master}" data-barang_spesifikasi_id="${item.barang_master_spesifikasi_id}" data-barang_id="${item.id}" data-nama="${item.barang_name}" data-satuan_id="${item.satuan_1}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
                    } else {
                        $(".kode_barang").append(`<option data-barang_name_master="${item.barang_name_master}" data-barang_spesifikasi_id="${item.barang_master_spesifikasi_id}" data-barang_id="${item.id}" data-nama="${item.barang_name}" data-satuan_id="${item.satuan_1}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
                    }
                })

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
        let kode_barang = $(".kode_barang").val()
        let nama_barang = $(".nama_barang").val()
        let nama_satuan = $(".satuan").val()
        let satuan_id = $(".satuan_id").val()
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

        validator_detail.resetForm();
        validator_detail.reset();

        if (validate_bahan_baku) {
            Swal.fire({
                icon: 'error',
                title: "Header Barang Wajib " + header_barang_name.toUpperCase() + " (Karena ini merupakan PO Lokal Bahan Baku)",
                confirmButtonColor: '#4e73df',
            })
        } else

        if (validate_same) {
            Swal.fire({
                icon: 'error',
                title: "Barang Sudah Ada",
                confirmButtonColor: '#4e73df',
            })
        } else {

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
                    drawTable();
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
                    drawTable();
                    $(".detail-modal").modal("hide");
                }
            }

        }
    }

    const updateStatusPosting = function(status) {
        Swal.fire({
            icon: 'question',
            title: status == '0' ? 'Unposting SPP ?' : 'Posting SPP ?',
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
                                    location.reload()
                                })
                        }
                    },

                });
            }
        })
    }

    const changeTipeSPP = function() {
        list_items = [];
        drawTable();
    }

    const drawTable = function() {
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
                    </button><button class="btn btn-danger" onclick="deleteRowDetail('${item.barang_detail_id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                            '</td>';
                    <?php endif; ?>
                <?php else : ?>
                    row += '<td>' + `
                    <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-barang_detail_id="${item.barang_detail_id}" >
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button><button class="btn btn-danger" onclick="deleteRowDetail('${item.barang_detail_id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                        '</td>';
                <?php endif; ?>

                no++;
            });
            $('.body-detail-table').append(row);
        }
    }

    const deleteRowDetail = function(id) {
        const indexToRemove = list_items.findIndex(item => item.barang_detail_id === id);
        if (indexToRemove !== -1) {
            list_items.splice(indexToRemove, 1);
        }
        drawTable();
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
                    $(".spp_no").val(res?.data);
                }
            })
        }
    }

    const changeStatus = function() {
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
                    $(".spp_no").val(res?.data);
                }
            })
        } else {
            $(".spp_no").attr("readonly", false);
            $(".spp_no").val("");
        }
    }
    const resetFormDetail = function() {
        $(".barang_detail_id").val('');
        $(".barang_id").val('').val(null).change()
        $(".barang_spesifikasi_id").val('');
        $(".kode").val(null).change()
        $(".nama_barang").val('')
        $(".satuan").val('')
        $(".satuan_id").val('')
        $(".qty").val('')
        $(".keterangan").val('')
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

    // Update
    <?php if (!empty($dataSPP)) : ?>
        <?php foreach ($dataSPPDetail as $i => $d) : ?>
            list_items.push({
                'barang_detail_id': getID(),
                'barang_id': "<?= encrypt($d->barang1_id) ?>",
                'barang_spesifikasi_id': "<?= encrypt($d->barang2_id) ?>",
                'kode_barang': "<?= $d->kode_barang ?>",
                'nama_barang': "<?= $d->nama_barang ?>",
                'nama_satuan': "<?= $d->nama_satuan ?>",
                'satuan_id': "<?= $d->unit ?>",
                'qty': "<?= $d->qty ?>",
                'keterangan': "<?= $d->note ?>"
            });
        <?php endforeach; ?>
        drawTable();
    <?php endif; ?>
</script>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataWorkOrders) ? "Detail Work Order" : "Tambah Work Order"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("work-order"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-form">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" value="<?= !empty($dataWorkOrders) ? $dataWorkOrders->id : ""; ?>" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataWorkOrders) ? 'readonly' : '' ?> autocomplete="one-time-code" type="text" value="<?= !empty($dataWorkOrders) ? $dataWorkOrders->wo_no : "AUTO GENERATE"; ?>" class="form-control wo_no" id="wo_no" name="wo_no" placeholder="Kode Produksi" readonly>
                                    <label for="floatingInput">Kode Produksi</label>
                                </div>
                                <!-- <div style="<?= !empty($dataWorkOrders) ? "display:none;" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control user_production" name="user_production" id="user_production" placeholder="Pembuat Dokumen" value="<?= session()->get("login")->name; ?>" readonly>
                            <label for="floatingInput">Pembuat Dokumen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataWorkOrders) ? ($dataWorkOrders->request_date ? date("d/m/Y", strtotime($dataWorkOrders->request_date)) : "") : ""; ?>" type="text" class="form-control date_production" name="date_production" id="date_production" placeholder="Tanggal Pembuatan Dokumen">
                            <label for="floatingInput">Tanggal Pembuatan Dokumen</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select department_id" name="department_id" id="department_id" <?= !empty($dataWorkOrders) ? 'disabled' : '' ?>>
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $dataDivisis) : ?>
                                    <option value="<?= $dataDivisis->id ?>" <?= !empty($dataWorkOrders) ? $dataWorkOrders->divisi_id == $dataDivisis->id ? "selected" : "" : ""; ?>><?= $dataDivisis->divisi ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Department</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select warehouse_id" name="warehouse_id" id="warehouse_id" disabled>
                                <option value=""></option>
                                <?php if (!empty($dataWorkOrders)) : ?>
                                    <?php foreach ($dataWarehouse ?? [] as $dataWarehouse) : ?>
                                        <option value="<?= $dataWarehouse->id ?>" <?= !empty($dataWorkOrders) ? $dataWorkOrders->warehouse_id == $dataWarehouse->id ? "selected" : "" : ""; ?>><?= $dataWarehouse->warehouse_name ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Warehouse</label>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataWorkOrders) ? 'readonly' : '' ?> autocomplete="one-time-code" type="text" value="<?= !empty($dataWorkOrders) ? $dataWorkOrders->standart_production : ""; ?>" class="form-control standart_production" name="standart_production" id="standart_production" placeholder="Jumlah Standart Produksi" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');">
                            <label for="floatingInput">Jumlah Standart Produksi</label>
                        </div>
                    </div> -->
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Hasil Barang Jadi</label>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </button>
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
                                <!-- <th>Satuan</th> -->
                                <th>Qty Target</th>
                                <th>Qty Hasil</th>
                                <th>Keterangan</th>
                                <th style="width:80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        <tfoot class="tfoot">
                            <tr>
                                <td colspan="7" class="text-center">Tidak Ada Data</td>
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
                <!-- <button class="btn btn-show-form btn-add-barang float-right">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah Barang
                </button> -->
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="barang_detail_id" name="barang_detail_id" id="barang_detail_id" />
                    <input autocomplete="one-time-code" type="hidden" class="barang_id" name="barang_id" id="barang_id" />
                    <input autocomplete="one-time-code" type="hidden" class="header_barang_name" name="header_barang_name" id="header_barang_name" />

                    <input autocomplete="one-time-code" type="hidden" class="spp_type_bypass" name="spp_type_bypass" id="spp_type_bypass" />
                    <input autocomplete="one-time-code" type="hidden" class="work_order_detail_id" name="work_order_detail_id" id="work_order_detail_id" />

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="hidden" class="kode" name="kode" id="kode" />
                                <select class="form-select kode_barang" name="kode_barang" id="kode_barang" aria-label="Floating label select example">
                                    <option data-barang_id="" data-nama="" data-satuan="" value=""></option>
                                </select>
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty Target</label>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row">
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
                                <label for="floatingInput">Qty Target</label>
                            </div>
                        </div>
                    </div> -->
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

    $(document).ready(function() {
        // Departemen
        $('.department_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        });

        //CSS SELECT2 FLOATING LABEL
        $('.department_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.department_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.department_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PILIH TIPE Warehouse
        $('.warehouse_id').select2({
            placeholder: "Pilih Warehouse",
            theme: "bootstrap-5"
        });

        $('.warehouse_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.warehouse_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.warehouse_id')
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
        }).change(function() {
            changeKodeBarang();
        });


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
        // Mengatur default value ke hari ini

        // Mengaktifkan datepicker
        $('#date_production').datepicker({
            autoclose: true,
            todayHighlight: true,
            enableOnReadonly: false,
            format: 'dd/mm/yyyy'
        });

        <?php if (!empty($dataWorkOrders)) : ?>
            // Membuat input readonly
            $('#date_production').prop('readonly', true);
        <?php endif; ?>

        $(".btn-show-detail").click(function() {

            $(".title-detail-name").text("Tambah");
            $(".barang_detail_id").val('');

            $(".kode").val('')
            $(".nama_barang").val('')
            $(".qty").val('')
            $(".satuan").val('')
            // $(".satuan_id").val('')
            $(".harga").val('')
            $(".total").val('')
            $(".keterangan").val('')

            validator_detail.resetForm();
            validator_detail.reset();
            var type = "bahan_jadi";
            $(".spp_type_bypass").val(type)
            if (type) {
                $.ajax({
                    url: `<?= base_url("barang/dropdown/type-nospec"); ?>`,
                    method: "GET",
                    dataType: "json",
                    data: {
                        type: type
                    },
                    success: function(res) {
                        $(".kode_barang").empty();
                        $(".kode_barang").append(`<option data-barang_name_master="" data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
                        res.data.forEach(function(item) {
                            $(".kode_barang").append(`<option data-barang_name_master="${item.barang_name_master}" data-barang_id="${item.id}" data-nama="${item.barang_name}" data-satuan_id="${item.satuan_1}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
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
        })

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
                wo_no: {
                    required: true
                },
                user_production: {
                    required: true
                },
                date_production: {
                    required: true
                },
                department_id: {
                    required: true
                },
                warehouse_id: {
                    required: true
                }
            },
            messages: {
                wo_no: {
                    required: "Kode produksi wajib diisi"
                },
                user_production: {
                    required: "Pembuat dokumen wajib diisi"
                },
                date_production: {
                    required: "Tanggal pembuatan dokumen wajib diisi"
                },
                department_id: {
                    required: "Department wajib diisi"
                },
                warehouse_id: {
                    required: "Warehouse wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                console.log(elem);
                if (elem.hasClass("multiple_po_id")) {
                    element = $(".select2-selection--multiple").parent();
                    error.insertAfter(element);
                } else if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.col-md-6').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.col-md-6').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $(".btn-save").click(function() {
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
                            $('#warehouse_id').prop('disabled', false);
                            $('#department_id').prop('disabled', false);
                            let id = $(".id").val();
                            let formData = new FormData(document.querySelector('.create-form'));
                            formData.append("items", JSON.stringify(list_items));

                            if (id) {
                                // UPDATE
                                <?php if (can('Produksi', 'Work Order', 'u')) : ?>
                                    $.ajax({
                                        url: "<?= base_url("work-order/update"); ?>",
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
                                    url: "<?= base_url("work-order/save"); ?>",
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
                                                window.location.replace("<?= base_url('work-order/details/') ?>" + response.id);
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

        $('#department_id').on('change', function() {
            var departmentId = $(this).val();
            $.ajax({
                url: `<?= base_url("warehouse/dropdown/divisi/"); ?>/${departmentId}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $("#warehouse_id").empty();
                    $("#warehouse_id").append(`<option value=""></option>`);
                    res.data.forEach(function(item) {
                        $("#warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`);
                    })
                    $("#warehouse_id").prop('disabled', false);
                    $("#warehouse_id").val("").change();
                }
            })
        });

        $(document).on('click', '.edit-table-detail', function(evt) {
            $(".title-detail-name").text("Update")

            validator_detail.resetForm();
            validator_detail.reset();
            let barang_id = "";
            let barang_detail_id = $(this).data('barang_detail_id');

            $.each(list_items, function(i, v) {
                if (v.barang_detail_id === barang_detail_id) {
                    $(".barang_detail_id").val(v.barang_detail_id);
                    $(".barang_id").val(v.barang_id);
                    $(".kode_barang").val(v.kode_barang);
                    $(".nama_barang").val(v.nama_barang);
                    $(".work_order_detail_id").val(v.work_order_detail_id);
                    $(".satuan_id").val(v.satuan_id);
                    $(".qty").val(v.qty);
                    $(".keterangan").val(v.keterangan);

                    barang_id = v.barang_id;
                }
            });
            type = "bahan_jadi";

            $.ajax({
                url: `<?= base_url("barang/dropdown/type-nospec"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    type: type
                },
                success: function(res) {
                    $(".kode_barang").empty();
                    $(".kode_barang").append(`<option data-barang_name_master=""  data-barang_id="" data-nama="" data-satuan_id="" data-satuan="" value=""></option>`);
                    res.data.forEach(function(item) {
                        if (barang_id === item.id) {
                            $(".kode_barang").append(`<option selected data-barang_name_master="${item.barang_name_master}" data-barang_id="${item.id}" data-nama="${item.barang_name}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
                        } else {
                            $(".kode_barang").append(`<option data-barang_name_master="${item.barang_name_master}" data-barang_id="${item.id}" data-nama="${item.barang_name}" value="${item.kode_barang}">${item.kode_barang} - ${item.barang_name}</option>`);
                        }
                    })

                    $(".detail-modal").modal("show");
                }
            })
        });
    });

    $('.btn-hide-detail').click(function() {
        $('.detail-modal').modal('hide');
        resetFormDetail();
    });

    const submitDetailForm = function() {
        let barang_detail_id = $(".barang_detail_id").val();
        let barang_id = $(".barang_id").val()
        let work_order_detail_id = $(".work_order_detail_id").val()
        let kode_barang = $(".kode_barang").val()
        let nama_barang = $(".nama_barang").val()
        let satuan_id = $(".satuan_id").val()
        let qty = $(".qty").val()
        let keterangan = $(".keterangan").val() ? $(".keterangan").val() : '-'

        let header_barang_name = $('.header_barang_name').val();

        let validate_same = false;
        let validate_bahan_baku = false;
        if (barang_detail_id) {
            if ($(".detail-form").valid()) {
                // UPDATE DETAIL
                $.each(list_items, function(i, v) {
                    if (v.barang_detail_id === barang_detail_id) {
                        list_items[i].barang_id = barang_id;
                        list_items[i].work_order_detail_id = work_order_detail_id;
                        list_items[i].kode_barang = kode_barang;
                        list_items[i].nama_barang = nama_barang;
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
                        'work_order_detail_id': "",
                        'barang_id': barang_id,
                        'kode_barang': kode_barang,
                        'nama_barang': nama_barang,
                        'qty': qty,
                        'qty_hasil': 0,
                        'keterangan': keterangan
                    });
                }

                resetFormDetail();
                drawTable();
                $(".detail-modal").modal("hide");
                $(".btn-show-detail").css("display", "none");
            }
        }
    }

    // const changeStatus = function() {
    //     let value = document.getElementById('auto_generate').checked ? true : false;

    //     if (value) {
    //         $(".wo_no").attr("readonly", true);
    //         $(".wo_no").val("AUTO GENERATE");
    //     } else {
    //         $(".wo_no").attr("readonly", false);
    //         $(".wo_no").val("");
    //     }
    // }
    const getID = function() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    }
    const resetFormDetail = function() {
        $(".barang_detail_id").val('');
        $(".barang_id").val('').val(null).change();
        $(".kode").val(null).change()
        $(".nama_barang").val('')
        $(".satuan").val('')
        $(".satuan_id").val('')
        $(".qty").val('')
        $(".keterangan").val('')
    }
    const changeKodeBarang = function() {
        if ($(".kode_barang option:selected").val()) {
            let nama = $(".kode_barang option:selected").data("nama") ? $(".kode_barang option:selected").data("nama") : "";
            let barang_id = $(".kode_barang option:selected").data("barang_id") ? $(".kode_barang option:selected").data("barang_id") : "";
            let barang_name_master = $(".kode_barang option:selected").data("barang_name_master");

            console.log('masuk');
            console.log(nama);
            $(".kode").val($(".kode_barang option:selected").val()).change();
            $(".nama_barang").val(nama).change();
            $(".barang_id").val(barang_id).change();
        } else {
            $(".kode").val("");
            $(".nama_barang").val("");
            $(".barang_id").val("");
        }
    }
    const drawTable = function() {
        $('.body-detail-table').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items.length === 0) {
            row += `
                    <tr>
                        <td colspan="8" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.tfoot').append(row);
        } else {
            list_items.map(item => {
                row += '<tr style="color:whitesmoke;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.nama_barang + '</td>';
                row += '<td>' + item.qty + '</td>';
                row += '<td>' + item.qty_hasil + '</td>';
                row += '<td>' + item.keterangan + '</td>';
                <?php if (!empty($dataWorkOrders)) : ?>
                    <?php if ($dataWorkOrders->is_posted == '0') : ?>
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
            // Check if there's work_order_detail_id
            if (list_items[indexToRemove].work_order_detail_id) {
                // If work_order_detail_id exists, execute Ajax
                Swal.fire({
                    icon: 'question',
                    title: 'Yakin akan di hapus?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url("work-order/delete-detail"); ?>",
                            data: {
                                id: list_items[indexToRemove].work_order_detail_id,
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
                                csrf.val(response.token);
                                if (response.status) {
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            location.reload();
                                        })
                                }
                            },
                        });
                    }
                });
            } else {
                // If work_order_detail_id does not exist, simply remove the item from the list
                list_items.splice(indexToRemove, 1);
                drawTable();
                $(".btn-show-detail").css("display", "");
            }
        }
    }

    // Update
    <?php if (!empty($dataWorkOrders)) : ?>
        <?php foreach ($dataWorkOrderDetails as $i => $d) : ?>
            list_items.push({
                'barang_detail_id': getID(),
                'work_order_detail_id': "<?= encrypt($d->id) ?>",
                'barang_id': "<?= encrypt($d->barang1_id) ?>",
                'kode_barang': "<?= $d->kode_barang ?>",
                'nama_barang': "<?= $d->nama_barang ?>",
                'qty': "<?= $d->qty ?>",
                'qty_hasil': "<?= $d->qty_hasil ?>",
                'keterangan': "<?= $d->note ?>"
            });
        <?php endforeach; ?>
        console.log(list_items);
        drawTable();
        <?php if (!empty($dataWorkOrderDetails)) : ?>
            $(".btn-show-detail").css("display", "none");
        <?php endif; ?>
        <?php if (empty($dataWorkOrderDetails)) : ?>
            $(".btn-show-detail").css("display", "");
        <?php endif; ?>
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>
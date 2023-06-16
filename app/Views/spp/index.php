<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section list">
<div class="section-header">
    <h1>Surat Permintaan Pembelian</h1>
    <button class="btn btn-show-form btn-add float-right">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </button>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end mb-3">
            <div class="col-md-2">
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <input class="form-control search form-out-search" placeholder="Search" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Tipe SPP</th>
                            <th>No. SPP</th> 
                            <th>Departemen</th> 
                            <th>Jenis Order</th> 
                            <th>Total Harga</th> 
                            <th>Tanggal Order</th> 
                            <th>Status</th> 
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table" style="cursor: pointer;">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</section>

<section class="section add" style="display: none;">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <button class="btn btn-hide-form">
        Batal
    </button>
    <button class="btn btn-submit-form btn-submit-parent">
        Simpan
    </button>
</div>
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label font-weight-bold">Data SPP</label>
            </div>
        </div>
        <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
            <input type="hidden" class="id" name="id" id="id" />
            <?= csrf_field() ?>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input class="form-control input-picker request_date" id="request_date" name="request_date" placeholder="Tanggal Order">
                                <label for="floatingInput">Tanggal Order</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-request-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" readonly="true" class="form-control" placeholder="Order Oleh" value="<?= session()->get("login")->name; ?>">
                        <label for="floatingInput">Order Oleh</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select spp_type" name="spp_type" id="spp_type" aria-label="Floating label select example">
                            <option value="lokal">Lokal</option>
                            <option value="import">Import</option>
                        </select>
                        <label for="floatingInput">Tipe SPP</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control spp_no" id="spp_no" name="spp_no" placeholder="No. SPP">
                                <label for="floatingInput">No. SPP</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select order_type" id="order_type" name="order_type" aria-label="Floating label select example">
                            <option value=""></option>
                        </select>
                        <label for="floatingInput">Jenis Order</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                            <option value=""></option>
                        </select>
                        <label for="floatingInput">Departemen</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
                        <label for="floatingInput">Catatan (Opsional)</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3" style="height: 50px;">
                        <label for="floatingInput">Status</label>
                        <div>
                            <label class="switch">
                            <input class="is_posted" name="is_posted" id="is_posted" type="checkbox">
                            <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label font-weight-bold">List Barang</label>
            </div>
            <div class="col-md-6">
                <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </button>
            </div>
        </div>
        <div class="table-responsive mt-2">
            <table class="table-inside nowrap table-hover-tobasurimi" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Spesifikasi</th>
                        <th>Harga Barang</th>
                        <th>Qty</th>
                        <th>Total Harga</th>
                        <th>Keterangan</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                </tbody>
            </table>
        </div>
    </div>
</div>
</section>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body" style="height: 380px !important; max-height: 380px !important;">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kode_barang" name="kode_barang" id="kode_barang" aria-label="Floating label select example">
                                    <option data-nama="" data-satuan="" data-stok="" value=""></option>
                                </select>
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan" name="satuan" id="satuan" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control spesifikasi" name="spesifikasi" id="spesifikasi" placeholder="Spesifikasi">
                                <label for="floatingInput">Spesitifikasi</label>
                            </div>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Barang">
                                <label for="floatingInput">Harga Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total Harga">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan">
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard delete-detail">Hapus</button>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_items = [];
    let list_delete = [];
    var row = 0;

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[1, 'asc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("spp/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
            }
        },
        // scrollX: true,
        "initComplete": function (settings, json) {    
            $('.dataTables_length').empty();    
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>"); 
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
            data: "no",
            className: "text-center",
            orderable: false
        },
        {
            data: "spp_type",
            className: "text-center"
        },
        {
            data: "spp_no",
            className: "text-center"
        },
        {
            data: "warehouseName",
            className: "text-center"
        },
        {
            data: "order_type",
            className: "text-center"
        },
        {
            data: "total",
            className: "text-center"
        },
        {
            data: "request_date",
            className: "text-center"
        },
        {
            data: "request_status",
            className: "text-center",
            orderable: false
        }],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });
    
    var validator_detail = $(".detail-form").validate({
        rules: {
            kode_barang: {
                required: true
            },
            qty: {
                required: true
            },
            satuan: {
                required: true
            },
            spesifikasi: {
                required: true
            },
            harga: {
                required: true
            }
        },
        messages: {
            kode_barang: {
                required: "Address wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            satuan: {
                required: "Satuan wajib diisi"
            },
            spesifikasi: {
                required: "Spesifikasi wajib diisi"
            },
            harga: {
                required: "Harga wajib diisi"
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
        $(".dateStart").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".dateEnd").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".request_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // SPP TYPE
        $('.order_type').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.order_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.order_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.order_type')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // WAREHOUSE
        $('.warehouse_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
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
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
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

        // SATUAN
        $('.satuan').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.satuan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.satuan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.icon-dateStart').click(function() {
            $(".dateStart").focus();
        });

        $('.icon-dateEnd').click(function() {
            $(".dateEnd").focus();
        });

        $('.icon-request-date').click(function() {
            $(".request_date").focus();
        });

        var validator = $(".create-form").validate({
            rules: {
                request_date: {
                    required: true
                },
                order_type: {
                    required: true
                },
                spp_no: {
                    required: true
                },
                order_type: {
                    required: true
                },
                warehouse_id: {
                    required: true,
                }
            },
            messages: {
                request_date: {
                    required: "Tanggal Order wajib diisi"
                },
                order_type: {
                    required: "Jenis Order wajib diisi"
                },
                spp_no: {
                    required: "No. SPP wajib diisi"
                },
                order_type: {
                    required: "Tipe SPP wajib diisi"
                },
                warehouse_id: {
                    required: "Departemen wajib diisi"
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
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');                      

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');   
            },
        });

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG
            if(list_items.length === 0)
            {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            }
            else
            {
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
                            const csrf = $(`[name="${csrfToken}"]`);
                            setLoading()
                            let data = new FormData(document.querySelector(".create-form"));

                            let update_list_items = [];
                            
                            list_items.map(obj => {
                                if (obj.id) {
                                    update_list_items.push(
                                        {
                                            id: obj.id,
                                            item_code: obj.kode_barang,
                                            qty: obj.qty,
                                            unit: obj.satuan,
                                            price: obj.harga.replaceAll(",", ""),
                                            note: obj.keterangan,
                                            spec: obj.spesifikasi
                                        }
                                    )
                                }
                                else
                                {
                                    update_list_items.push(
                                        {
                                            item_code: obj.kode_barang,
                                            qty: obj.qty,
                                            unit: obj.satuan,
                                            price: obj.harga.replaceAll(",", ""),
                                            note: obj.keterangan,
                                            spec: obj.spesifikasi
                                        }
                                    )
                                }
                            })

                            data.append("items", JSON.stringify(update_list_items))

                            let id = $(".id").val();
                            // UPDATE
                            if(id)
                            {
                                $.ajax({
                                    url: "<?= base_url("spp/update"); ?>",
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
                                            stopLoading()
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                table.ajax.reload()
                                                $(".list").css("display", "");
                                                $(".add").css("display", "none");
                                            })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                            // CREATE
                            else
                            {
                                $.ajax({
                                    url: "<?= base_url("spp/save"); ?>",
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
                                            stopLoading()
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                table.ajax.reload()
                                                $(".list").css("display", "");
                                                $(".add").css("display", "none");
                                            })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                        }
                    })
                }
            }
        })

        $(".btn-show-detail").click(function() {
            $(".delete-detail").css('display', 'none');

            $(".title-detail-name").text("Tambah");
            $(".id_detail").val('');
            
            $(".nama_barang").val('')
            $(".qty").val('')
            $(".satuan").val('')
            $(".spesifikasi").val('')
            $(".harga").val('')
            $(".total").val('')
            $(".keterangan").val('')

            validator_detail.resetForm();
            validator_detail.reset();

            $.ajax({
                url: `<?= base_url("barang/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".kode_barang").empty();

                    $(".kode_barang").append(`<option data-nama="" data-satuan="" data-stok="" value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".kode_barang").append(`<option data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    })

                    $(".kode_barang").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("satuan/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".satuan").empty();

                    $(".satuan").append(`<option value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".satuan").append(`<option value="${item.id}">${item.nama_satuan}</option>`);
                    })

                    $(".satuan").val("").change();
                    $(".detail-modal").modal("show");
                }
            })
        })

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah");

            $(".spp_no").attr("readonly", false);
            $(".input-generate").css("display", "");
            $(".spp_no").val('').change()
            $(".order_type").val('').change()
            $(".warehouse_id").val('').change()

            row = 0;

            list_items = [];

            validator.resetForm();
            validator.reset();

            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".body-detail-table").empty()

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'tipe_po'
                },
                dataType: "json",
                success: function(result) {
                    $(".order_type").empty()
                    $(".order_type").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".order_type").append(`<option value="${item.id}">${item.value}</option>`)
                    })

                    $(".order_type").val('').change();
                }
            })

            $.ajax({
                url: `<?= base_url("warehouse/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".warehouse_id").empty()
                    $(".warehouse_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                    })

                    $(".warehouse_id").val('').change();

                    $(".list").css("display", "none");
                    $(".add").css("display", "");
                }
            })
        })

        $(".kode_barang").change(function() {
            let nama = $(".kode_barang option:selected").data("nama");
            let satuan = $(".kode_barang option:selected").data("satuan");
            let stok = $(".kode_barang option:selected").data("stok");

            $(".nama_barang").val(nama);
            $(".satuan").val(satuan).change();
            $(".qty").val(stok);
        })

        $(".btn-hide-form").click(function() {
            $(".list").css("display", "");
            $(".add").css("display", "none");
        })

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function () {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd").change(function () {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $(".spp_no").attr("readonly", true);
            $(".input-generate").css("display", "none");

            validator.resetForm();
            validator.reset();

            $.ajax({
                url: "<?= base_url("spp/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    console.log(res)
                    if (res.status) {
                        $(".id").val(id);
                        $(".request_date").val(res?.data?.request_date);
                        $(".spp_type").val(res?.data?.spp_type);
                        $(".spp_no").val(res?.data?.spp_no);
                        $(".warehouse_id").val(res?.data?.warehouse_id).change();
                        $(".is_posted").prop( "checked", res?.data?.is_posted);
                        $(".note").val(res?.data?.note);

                        $(".list").css("display", "none");
                        $(".add").css("display", "");
                    }
                    else
                    {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $(".harga, .qty").keyup(function () {
            let harga = $(".harga").val() ? $(".harga").val().replaceAll(",", "") : 0;
            let qty = $(".qty").val() ? parseInt($(".qty").val()) : 0;

            let total = (harga * qty).toLocaleString();
            $(".total").val(total);
        })

        // delete
        $(".delete-btn").click(function() {
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
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("spp/delete"); ?>",
                        data: {
                            id: id
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
                                        table.ajax.reload()
                                        $(".list").css("display", "");
                                        $(".add").css("display", "none");
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val();
            let kode_barang = $(".kode_barang option:selected").val()
            let nama_barang = $(".nama_barang").val()
            let nama_satuan = $(".satuan option:selected").text()
            let satuan = $(".satuan option:selected").val()
            let spesifikasi = $(".spesifikasi").val()
            let harga = $(".harga").val()
            let qty = $(".qty").val()
            let total = $(".total").val()
            let keterangan = $(".keterangan").val()

            // update detail
            if(row_detail)
            {
                if ($(".detail-form").valid()) {
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
                            console.log(id)
                            let new_list_items = []
                            let tag_html = "";

                            row = 0;

                            $(".body-detail-table").empty()

                            list_items.map(item => {
                                if(item.row == row_detail)
                                {
                                    tag_html += `<tr class="edit-table-detail" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += "<td>";
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += kode_barang;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += nama_barang;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += spesifikasi;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += harga;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += qty;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += total;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += keterangan;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    new_list_items.push({
                                        id: item.id,
                                        row: row + 1,
                                        kode_barang: kode_barang,
                                        nama_barang: nama_barang,
                                        nama_satuan: nama_satuan,
                                        satuan: satuan,
                                        spesifikasi: spesifikasi,
                                        harga: harga,
                                        qty: qty,
                                        total: total,
                                        keterangan: keterangan
                                    });

                                    row = row + 1;
                                }
                                else
                                {
                                    tag_html += `<tr class="edit-table-detail" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += "<td>";
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += item.kode_barang;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += item.nama_barang;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += item.nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += item.spesifikasi;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += item.harga;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += item.qty;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += item.total;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += item.keterangan;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    new_list_items.push(item);

                                    row = row + 1;
                                }
                            })

                            list_items = [];

                            list_items = new_list_items;

                            $(".body-detail-table").append(tag_html)

                            $(".detail-modal").modal("hide")
                        }
                    })
                }
            }
            // create detail
            else
            {
                if ($(".detail-form").valid()) {
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
                            list_items.push({
                                id: '',
                                row: row + 1,
                                kode_barang: kode_barang,
                                nama_barang: nama_barang,
                                nama_satuan: nama_satuan,
                                satuan: satuan,
                                spesifikasi: spesifikasi,
                                harga: harga,
                                qty: qty,
                                total: total,
                                keterangan: keterangan
                            })

                            let tag_html = "";
                            tag_html += `<tr class="edit-table-detail" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += "<td>";
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += kode_barang;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += nama_barang;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += nama_satuan;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += spesifikasi;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += harga;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += qty;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += total;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += keterangan;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                            tag_html += "</td>";
                            tag_html += "</tr>";
                            $(".body-detail-table").append(tag_html)
                            $(".detail-modal").modal("hide")
                            row = row + 1;
                        }
                    })
                }
            }
        })
    })

    const deleteRow = function(id) {
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
                console.log(id)
                let new_list_items = []
                let tag_html = "";

                $(".body-detail-table").empty()

                row = 0;

                console.log(list_items)

                list_items.map(item => {
                    if(item.row != id)
                    {
                        tag_html += `<tr class="edit-table-detail" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += "<td>";
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.nama_satuan;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.spesifikasi;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.total;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({...item, row: row + 1});

                        row = row + 1;
                    }
                    else
                    {
                        // sent parameter isDelete if have customer id and id
                        if(item.id)
                        {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".detail-modal").modal("hide")
            }
        })
    }

    $(document).on('click', '.delete-detail', function() {
        let id = $(".id_detail").val()
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
                console.log(id)
                let new_list_items = []
                let tag_html = "";

                $(".body-detail-table").empty()

                row = 0;

                console.log(list_items)

                list_items.map(item => {
                    if(item.row != id)
                    {
                        tag_html += `<tr class="edit-table-detail" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += "<td>";
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.nama_satuan;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.spesifikasi;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.total;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({...item, row: row + 1});

                        row = row + 1;
                    }
                    else
                    {
                        // sent parameter isDelete if have customer id and id
                        if(item.id)
                        {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".detail-modal").modal("hide")
            }
        })
    })

    $(document).on('click', '.edit-table-detail', function(evt) {
        $(".title-detail-name").text("Update")
        $(".delete-detail").css('display', '');
        let kode_barang = $(this).data('kode_barang')
        let nama_barang = $(this).data('nama_barang')
        let satuan = $(this).data('satuan')
        let spesifikasi = $(this).data('spesifikasi')
        let harga = $(this).data('harga')
        let qty = $(this).data('qty')
        let keterangan = $(this).data('keterangan')
        let rowid = $(this).data('row')
        let id = $(this).data('id')

        validator_detail.resetForm();
        validator_detail.reset();

        $(".id_detail").val(rowid)
        $(".kode_barang").val(kode_barang).change()
        $(".nama_barang").val(nama_barang)
        $(".satuan").val(satuan).change()
        $(".spesifikasi").val(spesifikasi)
        $(".harga").val(harga)
        $(".qty").val(qty)
        $(".total").val((harga.replaceAll(",", "") * parseInt(qty)).toLocaleString())
        $(".keterangan").val(keterangan)

        $(".detail-modal").modal("show")
    })

    const changeStatus = function()
    {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if(value)
        {
            $(".spp_no").attr("readonly", true);
            $(".spp_no").val("AUTO GENERATE");
        }
        else
        {
            $(".spp_no").attr("readonly", false);
            $(".spp_no").val("");
        }
    }
</script>
<?= $this->endSection(); ?>
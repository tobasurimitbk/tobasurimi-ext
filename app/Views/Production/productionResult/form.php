<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah Hasil Produksi</h1>
    <div class="col-button-tambah-spp">
        <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("production-result"); ?>">
            Batal
        </a>
        <button class="btn btn-show-form btn-save float-right btn-submit-form">
            Simpan
        </button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form class="create-form form-add-spp form-hp" role="form" method="POST" enctype="multipart/form-data">
            <input type="hidden" value="<?= $data->id ?? ""; ?>" class="id" name="id" id="id" />
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" value="<?= $data->pr_no ?? ""; ?>" class="form-control" id="pr_no" name="pr_no" placeholder="Kode Produksi" disabled>
                                <label for="floatingInput">Kode Produksi</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select barang_id" name="work_order" id="work_order" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach($workOrders ?? [] as $workOrder): ?>
                            <option value="<?= $workOrder->id ?>" <?= !empty($data) && $data->work_order_id == $workOrder->id ? 'selected' : '' ?>><?= $workOrder->wo_no ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Work Order</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select barang_id" name="warehouse" id="warehouse" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach($warehouses ?? [] as $warehouse): ?>
                            <option value="<?= $warehouse->id ?>" <?= !empty($data) && $data->warehouse_id == $warehouse->id ? 'selected' : '' ?>><?= $warehouse->warehouse_name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Warehouse</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= $data->receive_date ?? '' ?>"  class="form-control target input-picker" name="receive_date" id="receive_date" placeholder="Target">
                        <label for="floatingInput">Receive Date</label>
                    </div>
                </div>
            </div>

            <!-- details -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-jadi" type="button" role="tab" aria-controls="nav-barang-jadi" aria-selected="true">Barang Jadi</button>
                            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-barang-setengah-jadi" type="button" role="tab" aria-controls="nav-barang-setengah-jadi" aria-selected="false">Barang Setengah Jadi</button>
                            <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-scrap" type="button" role="tab" aria-controls="nav-scrap" aria-selected="false">Scrap</button>
                            <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-material-return" type="button" role="tab" aria-controls="nav-material-return" aria-selected="false">Material Return</button>
                        </div>
                    </nav>
                    <div class="tab-content mt-3" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-barang-jadi" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                    <input type="text" class="form-control target input-picker" id="barang_jadi_unit" placeholder="Target" value="<?= $barangJadi->nama_barang ?? '' ?>" disabled>
                                        <label for="floatingInput">Nama Barang</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" class="form-control target input-picker" id="barang_jadi_code" value="<?= $barangJadi->kode_barang ?? '' ?>" disabled>
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" class="form-control target input-picker" id="barang_jadi_unit" value="<?= $barangJadi->nama_satuan ?? '' ?>" disabled>
                                        <label for="floatingInput">Satuan</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" value="<?= $barangJadi->qty ?? '' ?>"  oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" name="barang_jadi_qty" id="barangJadiQty">
                                        <label for="floatingInput">Qty</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-barang-setengah-jadi" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="" id="barang_setengah_jadi">
                                            <option value="" selected disabled></option>
                                            <?php foreach($barangData as $barang): ?>
                                            <option data-code="<?= $barang->kode_barang ?>" data-unit="<?= $barang->unit ?>" value="<?= $barang->id ?>"><?= $barang->nama_barang ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput">Nama Barang</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" class="form-control target input-picker" id="setengah_jadi_barang_code" placeholder="Target" disabled>
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" class="form-control target input-picker" id="setengah_jadi_barang_unit" placeholder="Target" disabled>
                                        <label for="floatingInput">Satuan</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" value="<?= !empty($dataWorkOrders) ? formatter($dataWorkOrders->target, "STR_TO_INT") : ""; ?>"  oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" id="barangSetengahJadiQty">
                                        <label for="floatingInput">Qty</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <button type="button" class="btn btn-primary" id="addBarangSetengahJadi">Pilih</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table nowrap table-hover-tobasurimi" id="barangSetengahJadiDataTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No.</th>
                                            <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                            <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                            <th onclick="changeSort('nama_satuan')" class="sort">Nama Satuan</th>
                                            <th onclick="changeSort('target')" class="sort">Jumlah</th>
                                            <th onclick="changeSort('target')" class="sort">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-table" id="body-table" style="cursor: pointer;">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-scrap" role="tabpanel" aria-labelledby="nav-contact-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="" id="scrap">
                                            <option value="" selected disabled></option>
                                            <?php foreach($barangData as $barang): ?>
                                            <option data-code="<?= $barang->kode_barang ?>" data-unit="<?= $barang->unit ?>" value="<?= $barang->id ?>"><?= $barang->nama_barang ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput">Nama Barang</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" class="form-control target input-picker" id="scrap_code" placeholder="Target" disabled>
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" class="form-control target input-picker" id="scrap_unit" placeholder="Target" disabled>
                                        <label for="floatingInput">Satuan</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" value="<?= !empty($dataWorkOrders) ? formatter($dataWorkOrders->target, "STR_TO_INT") : ""; ?>"  oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" name="" id="scrapQty">
                                        <label for="floatingInput">Qty</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <button type="button" class="btn btn-primary" id="addScrap">Pilih</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table nowrap table-hover-tobasurimi" id="scrapDataTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No.</th>
                                            <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                            <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                            <th onclick="changeSort('nama_satuan')" class="sort">Nama Satuan</th>
                                            <th onclick="changeSort('target')" class="sort">Jumlah</th>
                                            <th onclick="changeSort('target')" class="sort">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-table" id="body-table" style="cursor: pointer;">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-material-return" role="tabpanel" aria-labelledby="nav-contact-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="" id="materialReturn">
                                            <option value="" selected disabled></option>
                                            <?php foreach($barangData as $barang): ?>
                                            <option data-code="<?= $barang->kode_barang ?>" data-unit="<?= $barang->unit ?>" value="<?= $barang->id ?>"><?= $barang->nama_barang ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput">Nama Barang</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" class="form-control target input-picker" id="materialReturnCode" placeholder="Target" disabled>
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" class="form-control target input-picker" id="materialReturnUnit" placeholder="Target" disabled>
                                        <label for="floatingInput">Satuan</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input type="text" value="<?= !empty($dataWorkOrders) ? formatter($dataWorkOrders->target, "STR_TO_INT") : ""; ?>"  oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" name="" id="materialReturnQty">
                                        <label for="floatingInput">Qty</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <button type="button" class="btn btn-primary" id="addMaterialReturn">Pilih</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table nowrap table-hover-tobasurimi" id="materialReturnDataTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No.</th>
                                            <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                            <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                            <th onclick="changeSort('nama_satuan')" class="sort">Nama Satuan</th>
                                            <th onclick="changeSort('target')" class="sort">Jumlah</th>
                                            <th onclick="changeSort('target')" class="sort">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-table" id="body-table" style="cursor: pointer;">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- details -->

        </form>
    </div>
</div>
</section>

<script>
const csrfToken = '<?= csrf_token() ?>';

$(document).ready(function() {
    $('#work_order, #warehouse, #barang_setengah_jadi, #scrap').select2({
        placeholder: "",
        theme: "bootstrap-5",
    });

    $("#receive_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    //CSS SELECT2 FLOATING LABEL
    $('.barang_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.barang_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.barang_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $('.satuan_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
    })

    //CSS SELECT2 FLOATING LABEL
    $('.satuan_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.satuan_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.satuan_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    var validator = $(".create-form").validate({
        rules: {
            barang_id: {
                required: true
            },
            production_amt: {
                required: true
            },
            satuan_id: {
                required: true
            },
            target: {
                required: true
            }
        },
        messages: {
            barang_id: {
                required: "Barang wajib diisi"
            },
            production_amt: {
                required: "Hasil wajib diisi"
            },
            satuan_id: {
                required: "Satuan wajib diisi"
            },
            target: {
                required: "Target wajib diisi"
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

    $(".btn-submit-form").click(function() {
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
                    const data = new FormData(document.querySelector(".create-form"));
                    const id = $(".id").val();

                    const barangSetengahJadiItems = barangSetengahJadiDataTable.rows().data().toArray();
                    data.append("barang_setengah_jadi", JSON.stringify(barangSetengahJadiItems));

                    const scrapItems = scrapDataTable.rows().data().toArray();
                    data.append("scrap", JSON.stringify(scrapItems));

                    const materialReturnItems = materialReturnDataTable.rows().data().toArray();
                    data.append("material_return", JSON.stringify(materialReturnItems));

                    // UPDATE
                    if(id)
                    {
                        $.ajax({
                            url: "<?= base_url("production-result/update"); ?>",
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
                                        window.location.href = "<?= base_url("production-result/"); ?>" + id;
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
                    else
                    {
                        $.ajax({
                            url: "<?= base_url("production-result/create"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            },
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                console.log(response)
                                csrf.val(response.token);
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        window.location.href = "<?= base_url("production-result/"); ?>" + response.id;
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
    });

    // barang setengah jadi
    const barangSetengahJadiDataTable = $('#barangSetengahJadiDataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        // serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],

        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        // scrollX: true,
        "initComplete": function(settings, json) {
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
                data: "kode_barang",
                className: "text-center"
            },
            {
                data: "nama_barang",
                className: "text-center"
            },
            {
                data: "nama_satuan",
                className: "text-center"
            },
            {
                data: "jumlah",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    return `
                        <div class="mt-0">
                            <button class="btn btn-danger" data-action="delete-itemSetengahJadi">
                                Delete
                            </button>
                        </div>
                    `
                }
            }
        ],
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

    $('#barang_setengah_jadi').change(function() {
        const selectedEl = $(this).find('option:selected');
        const data = selectedEl.data();
        
        $('#setengah_jadi_barang_code').val(data.code);
        $('#setengah_jadi_barang_unit').val(data.unit);
    });

    $('#addBarangSetengahJadi').click(function() {

        const itemId = $('#barang_setengah_jadi').val();
        const itemQty = $('#barangSetengahJadiQty').val();

        if (itemId && itemQty > 0) {
            const itemName = $('#barang_setengah_jadi option:selected').text();
            const itemCode = $('#setengah_jadi_barang_code').val();
            const itemUnit = $('#setengah_jadi_barang_unit').val();
            const itemData = {
                id: itemId,
                kode_barang: itemCode,
                nama_barang: itemName,
                nama_satuan: itemUnit,
                jumlah: itemQty
            };
            barangSetengahJadiDataTable.row.add(itemData).draw(false);

            // clear input
            $('#barang_setengah_jadi').val('').trigger('change');
            $('#barangSetengahJadiQty').val('');
        }
        
    });

    $('#barangSetengahJadiDataTable').on('click', '[data-action="delete-itemSetengahJadi"]', function() {
        barangSetengahJadiDataTable.row($(this).parent().parent()).remove().draw();
    });
    // barang setengah jadi end

    // scrap
    const scrapDataTable = $('#scrapDataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        // serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],

        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        // scrollX: true,
        "initComplete": function(settings, json) {
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
                data: "kode_barang",
                className: "text-center"
            },
            {
                data: "nama_barang",
                className: "text-center"
            },
            {
                data: "nama_satuan",
                className: "text-center"
            },
            {
                data: "jumlah",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    return `
                        <div class="mt-0">
                            <button class="btn btn-danger" data-action="delete-scrap">
                                Delete
                            </button>
                        </div>
                    `
                }
            }
        ],
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

    $('#scrap').change(function() {
        const selectedEl = $(this).find('option:selected');
        const data = selectedEl.data();
        
        $('#scrap_code').val(data.code);
        $('#scrap_unit').val(data.unit);
    });

    $('#addScrap').click(function() {

        const itemId = $('#scrap').val();
        const itemQty = $('#scrapQty').val();

        if (itemId && itemQty > 0) {
            const itemName = $('#scrap option:selected').text();
            const itemCode = $('#scrap_code').val();
            const itemUnit = $('#scrap_unit').val();
            const itemData = {
                id: itemId,
                kode_barang: itemCode,
                nama_barang: itemName,
                nama_satuan: itemUnit,
                jumlah: itemQty
            };
            scrapDataTable.row.add(itemData).draw(false);

            // clear input
            $('#scrap').val('').trigger('change');
            $('#scrapQty').val('');
        }
        
    });

    $('#scrapDataTable').on('click', '[data-action="delete-scrap"]', function() {
        scrapDataTable.row($(this).parent().parent()).remove().draw();
    });
    // scrap end

    // material return
    const materialReturnDataTable = $('#materialReturnDataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        // serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],

        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        // scrollX: true,
        "initComplete": function(settings, json) {
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
                data: "kode_barang",
                className: "text-center"
            },
            {
                data: "nama_barang",
                className: "text-center"
            },
            {
                data: "nama_satuan",
                className: "text-center"
            },
            {
                data: "jumlah",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    return `
                        <div class="mt-0">
                            <button class="btn btn-danger" data-action="delete-material-return">
                                Delete
                            </button>
                        </div>
                    `
                }
            }
        ],
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

    $('#materialReturn').change(function() {
        const selectedEl = $(this).find('option:selected');
        const data = selectedEl.data();
        
        $('#materialReturnCode').val(data.code);
        $('#materialReturnUnit').val(data.unit);
    });

    $('#addMaterialReturn').click(function() {

        const itemId = $('#materialReturn').val();
        const itemQty = $('#materialReturnQty').val();

        if (itemId && itemQty > 0) {
            const itemName = $('#materialReturn option:selected').text();
            const itemCode = $('#materialReturnCode').val();
            const itemUnit = $('#materialReturnUnit').val();
            const itemData = {
                id: itemId,
                kode_barang: itemCode,
                nama_barang: itemName,
                nama_satuan: itemUnit,
                jumlah: itemQty
            };
            materialReturnDataTable.row.add(itemData).draw(false);

            // clear input
            $('#materialReturn').val('').trigger('change');
            $('#materialReturnQty').val('');
        }
        
    });

    $('#materialReturnDataTable').on('click', '[data-action="delete-material-return"]', function() {
        materialReturnDataTable.row($(this).parent().parent()).remove().draw();
    });
    // material return end


    if (id) {
        const barangSetengahJadiData = <?= json_encode($barangSetengahJadi ?? []) ?>;
        barangSetengahJadiDataTable.rows.add(barangSetengahJadiData).draw(false);

        const scrapData = <?= json_encode($scrap ?? []); ?>;
        scrapDataTable.rows.add(scrapData).draw(false);

        const materialReturnData = <?= json_encode($materialReturn ?? []); ?>;
        materialReturnDataTable.rows.add(materialReturnData).draw(false);
    }
});

const changeStatus = function()
{
    let value = document.getElementById('auto_generate').checked ? true : false;

    if(value)
    {
        $(".wo_no").attr("readonly", true);
        $(".wo_no").val("AUTO GENERATE");
    }
    else
    {
        $(".wo_no").attr("readonly", false);
        $(".wo_no").val("");
    }
}
</script>

<?= $this->endSection(); ?>
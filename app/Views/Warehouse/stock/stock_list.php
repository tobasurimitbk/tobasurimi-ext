<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Stock List</h1>
        <?php if (can('Inventori', 'Stok List', 'p')) : ?>
            <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 30px;">
                Import / Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item btn-upload-excel">Import Excel</button></li>
                <li><button class="dropdown-item" onclick="excel('<?= base_url("stock-list/export-excel"); ?>')">Export Excel</button></li>
            </ul>
        <?php endif; ?>
        <?php if (can("Inventori", "Stok List", "c")) : ?>
            <a href="<?= base_url('stock-list/create') ?>" type="button" class="btn btn-show-form btn-add btn-add-barang float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Inisiasi Stok
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select" name="parent_type" id="parent_type" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($tipeBarang as $t) : ?>
                                <option value="<?= $t['description'] ?>">
                                    <?= strtoupper($t['value']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Tipe Barang</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select parent_name" id="parent_name" name="parent_name" aria-label="Floating label select example">
                            <option value=""></option>
                        </select>
                        <label style="z-index: 1;">Kategori Barang</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($dataDivisi as $divisi) : ?>
                                <option value="<?= $divisi["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['divisi_id'] === $divisi["id"] ? "selected" : "") : ""; ?>><?= strtoupper($divisi["divisi"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Departemen</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">

                        </select>
                        <label style="z-index: 1;">Warehouse</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select status_stok" id="status_stok" name="status_stok" aria-label="Floating label select example">
                            <option value="ALL">SEMUA</option>
                            <option value="1">ADA</option>
                            <option value="0">HABIS</option>
                        </select>
                        <label style="z-index: 1;">Status Stok</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Kode / Nama Barang </label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th onclick="changeSort('parent_barang.parent_type')">Tipe Barang</th>
                            <th onclick="changeSort('parent_barang.parent_name')">Kategori</th>
                            <th onclick="changeSort('barang_master.kode_barang')">Kode</th>
                            <th onclick="changeSort('barang_master.barang_name')">Barang</th>
                            <th onclick="changeSort('divisis.divisi')">Departemen</th>
                            <th onclick="changeSort('warehouses.warehouse_name')">Warehouse</th>
                            <th onclick="changeSort('stock.qty')">Qty Satuan 1</th>
                            <th>Qty Satuan 2</th>
                            <th>Qty Satuan 3</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>
<div class="modal" id="import_excel_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Inventori</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary text-black" role="alert">
                    UNDUH TEMPLEATE EXCEL <a href="<?= base_url('assets/import/IMPORT_EXCEL_INVENTORI.xlsx') ?>" style="text-decoration: none;"><b style="color: black;">DISINI</b></a>
                </div>
                <form class="form-excel" method="post">
                    <div class="form-floating" style="height: 50px;">
                        <input type="file" name="file" id="file" accept=".xlsx" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-import-excel mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-excel">Simpan</button>
            </div>
        </div>
    </div>
</div>


<script>
    let sort = "createdAt";
    let sortType = "desc";


    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.parent_type = $("#parent_type option:selected").val();
                data.parent_name = $("#parent_name option:selected").val();
                data.divisi_id = $("#divisi_id option:selected").val();
                data.warehouse_id = $("#warehouse_id option:selected").val();
                data.status_stok = $("#status_stok option:selected").val();
                data.sort = sort;
                data.sortType = sortType;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                orderable: false
            },
            {
                data: "parent_type",
                className: "text-center"
            },
            {
                data: "parent_name",
                className: "text-center"
            },
            {
                data: "kode_barang",
                className: "text-center"
            },
            {
                data: "barang",
                className: "text-center"
            },
            {
                data: "divisi",
                className: "text-center",

            },
            {
                data: "warehouse",
                className: "text-center"
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    return `
                        <div class="mt-0 actions">
                            <a href="<?= base_url('stock-list/id/') ?>${id}'" data-toggle="tooltip" title="Detail Stok" class="btn btn-success posting-spp actions">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    `
                }
            }
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    $('#parent_type').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET KATEGORI BARANG
        $.ajax({
            url: `<?= base_url('stock-list/kategori-barang'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                parent_type: $(this).val(),
            },
            dataType: "json",
            success: function(res) {
                $(".parent_name").empty()
                $(".parent_name").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".parent_name").append(`<option value="${item.id}">${item.parent_name}</option>`)
                })
                $(".parent_name").val();
            }
        });
        table.ajax.reload();

    });

    $('#parent_name').select2({
        placeholder: "Pilih Kategori Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('stock-list/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_id").val();
            }
        });
        table.ajax.reload();
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $('#status_stok').select2({
        placeholder: "Pilih Status Stok",
        theme: "bootstrap-5",
    }).change(function() {
        table.ajax.reload();
    });

    $('.search').change(function() {
        table.ajax.reload();
    });

    // upload excel
    $('.btn-upload-excel').click(function() {
        $('#file').val(null);
        $('#import_excel_modal').modal('show');

    });

    $('.btn-discard-import-excel').click(function() {
        $('#import_excel_modal').modal('hide');
    });

    $('.btn-submit-excel').click(function() {
        if ($('.form-excel').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Import Excel?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrf = $(`[name="${csrfToken}"]`);
                    let formData = new FormData(document.querySelector(".form-excel"));
                    $.ajax({
                        url: "<?= base_url("stock-list/import"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
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
                                }).then(() => {
                                    location.reload();
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


    var validator_excel = $(".form-excel").validate({
        rules: {
            file: {
                required: true
            },
        },
        messages: {
            file: {
                required: "File wajib diisi"
            },
        },
    });

    $("#parent_type,#divisi_id,#warehouse_id,#parent_name,#status_stok")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const stokDetail = function(id) {
        location.replace("<?= base_url('stock-list/id/') ?>" + id, "");
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    //export excel
    const excel = function(url) {
        let search = $(".search").val();
        let parent_type = $("#parent_type option:selected").val();
        let parent_name = $("#parent_name option:selected").val();
        let divisi_id = $("#divisi_id option:selected").val();
        let warehouse_id = $("#warehouse_id").val();
        let status_stok = $("#status_stok option:selected").val();

        window.open(url + `?search=${search}&parent_type=${parent_type}&parent_name=${parent_name}&divisi_id=${divisi_id}&warehouse_id=${warehouse_id}&status_stok=${status_stok}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>
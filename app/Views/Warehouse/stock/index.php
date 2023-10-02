<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select" name="barang" id="barang">
                                    <option value=""></option>
                                    <?php foreach ($barangData ?? [] as $barang): ?>
                                    <option value="<?= $barang->id ?>"><?= "$barang->kode_barang - $barang->nama_barang" ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select" name="warehouse" id="warehouse">
                                    <option value=""></option>
                                    <?php foreach ($warehouseData ?? [] as $warehouse): ?>
                                    <option value="<?= $warehouse->id ?>"><?= $warehouse->warehouse_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Warehouse</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="stock_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Stock Detail</h5>
                <button type="button" class="close" style="display: block;" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table" id="stockTable">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Stock Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Stock</h1>
        <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp row-form-select-master-barang-index">
                <div class="col mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Nama Barang / Kode Barang" value="" />
                </div>
                <div class="col mb-3">
                    <select class="form-select kategori form-out-search" name="kategori" id="kategori" aria-label="Floating label select example">
                        <option value="">Kategori: All</option>
                        <option value="Bahan Baku">Bahan Baku</option>
                        <option value="Bahan Penolong">Bahan Penolong</option>
                        <option value="Jadi">Barang Jadi</option>
                        <option value="Scrap">Barang Scrap</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('parent_barang')" class="sort">Parent Barang</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('type')" class="sort">Tipe Supplier</th>
                                <th onclick="changeSort('kode_satuan')" class="sort">Satuan</th>
                                <th onclick="changeSort('warehouse')" class="sort">Warehouse</th>
                                <th onclick="changeSort('kategori')" class="sort">Kategori</th>
                                <th onclick="changeSort('stok')" class="sort">Stok</th>
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "parent_barang";
    let sortType = "asc";
    let list_spek = [];
    var row_detail = 0;
    let changeParent = true;

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
            url: "<?= base_url("stock/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.kategori = $(".kategori").val();
                data.sort = sort;
                data.sortType = sortType;
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
            sortable: false
        }, 
        {
            data: "parent_barang",
            className: "text-center"
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
            data: "type",
            className: "text-center"
        },
        {
            data: "kode_satuan",
            className: "text-center"
        },
        {
            data: 'warehouse',
            className: "text-center"
        },
        {
            data: "kategori",
            className: "text-center"
        },
        {
            data: "qty",
            className: "text-center",
            render: function(data, type, row) {
                return `
                <div class="text-danger">
                ${data}
                </div>
                `
            }
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

    $(document).ready(function() {
        $('.create-form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $('.kategori').select2({
            placeholder: "Kategori: All",
            theme: "bootstrap-5",
            allowClear: true
        })

         $('#barang, #warehouse').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $("#barang, #warehouse")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $("#barang, #warehouse")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $("#barang, #warehouse")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KATEGORI
        $('.kategori_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kategori_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kategori_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kategori_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $(".create-form").validate({
            rules: {
                nama_barang: {
                    required: true
                },
                spesifikasi: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                harga_barang: {
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
                harga_barang: {
                    required: "Harga wajib diisi"
                },
                satuan_id: {
                    required: "Satuan wajib diisi"
                },
                stok: {
                    required: "Stok wajib diisi"
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

        $(".btn-show-form").click(function() {
            validator.resetForm();
            validator.reset();

            $(".add-modal").modal("show")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            const id = data.id;
            const warehouseId = data.warehouseId;

            // clear table body
            $('#stockTable tbody').empty();

            $.ajax({
                url: `<?= base_url("stock/"); ?>${id}/${warehouseId}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {

                        for (const stock of res.stockData) {
                            const el = `<tr>
                                            <td>${stock.no}</td>
                                            <td>${stock.qty}</td>
                                            <td>${stock.stockDate}</td>
                                        </tr>`;
                            $('#stockTable tbody').append(el);
                        }

                        $('#stock_modal').modal('show');

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $(".search").keyup(function () {
            table.ajax.reload();
        })

        $(".kategori").change(function () {
            table.ajax.reload();
        })

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

                        $.ajax({
                            url: "<?= base_url("stock/save"); ?>",
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
                                        $(".add-modal").modal("hide")
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
            }
        })

    })

    const changeSort = function(val) {
        if(sort !== val)
        {
            sortType = "ASC";
            sort = val;
        }
        else
        {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>
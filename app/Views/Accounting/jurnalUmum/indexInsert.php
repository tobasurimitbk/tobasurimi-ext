<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header d-flex justify-content-end">
        <h1 class="me-auto">Tambah Nilai Barang</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row row-col-spp mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select kode_department" name="kode_department" id="kode_department">
                            <option value="" data-code=""></option>
                        </select>
                        <label for="floatingInput">Pilih Department</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select kode_warehouse" name="kode_warehouse" id="kode_warehouse">
                            <option value="" data-code=""></option>
                        </select>
                        <label for="floatingInput">Pilih Warehouse</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select type_barang" name="type_barang" id="type_barang">
                            <option value="" data-code=""></option>
                            <option value="bahan_penolong" data-code="">Bahan Penolong</option>
                            <option value="bahan_jadi" data-code="">Bahan Jadi</option>
                            <option value="bahan_baku" data-code="">Bahan Baku</option>
                            <option value="bahan_scrap" data-code="">Bahan Scrap</option>
                            <option value="bahan_modal" data-code="">Bahan Modal</option>
                            <option value="bahan_setengah_jadi" data-code="">Bahan Setengah Jadi</option>
                        </select>
                        <label for="floatingInput">Pilih Type Barang</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select kode_barang" name="kode_barang" id="kode_barang">
                            <option value="" data-code=""></option>
                        </select>
                        <label for="floatingInput">Pilih Barang</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary w-100" id="search">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('barang_master_spesifikasi.spesifikasi')" class="sort">Nama Spesifikasi</th>
                                <th>Akun Persedian/Pembelian</th>
                                <th>Saldo Akun</th>
                                <th>Akun Penjualan</th>
                                <th>Saldo Akun</th>
                                <th>Akun Pemakaian</th>
                                <th>Saldo Akun</th>
                                <th>Action</th>
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

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <input type="hidden" name="divisi_id" class="divisi_id" id="divisi_id">
                    <input type="hidden" name="barang_id" class="barang_id" id="barang_id">
                    <input type="hidden" name="type" id="type" value="">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled autocomplete="one-time-code" type="text" class="form-control" placeholder="Nama Barang" id="parentName" name="parentName">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled autocomplete="one-time-code" type="text" class="form-control divisis_name" placeholder="Departemen" id="divisi_name" name="divisi_name">
                                <label for="floatingInput">Departemen</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_ap_id" name="akun_ap_id" id="akun_ap_id">
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
                                <label for="floatingInput">Akun Persediaan/Pembelian</label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_ar_id" name="akun_ar_id" id="akun_ar_id">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub_ar) {
                                    ?>
                                            <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Penjualan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_pemakaian_id" name="akun_pemakaian_id" id="akun_pemakaian_id">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub_ar) {
                                    ?>
                                            <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Pemakaian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kategori" name="kategori" id="kategori">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($kategoriBarangAkun)) {
                                        foreach ($kategoriBarangAkun as $kategoriBarang) {
                                    ?>
                                            <option value="<?= $kategoriBarang->id; ?>"><?= $kategoriBarang->description; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Kategori Barang</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-submit-form">Simpan</button>
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "createdAt";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({

        processing: true,
        serverSide: true,
        ordering: true,
        deferLoading: 0,
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
            url: "<?= base_url("jurnal/get-data"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.kode_department = $("#start_date").val();
                data.kode_warehouse = $("#end_date").val();
                data.type_transaksi = $('#type_barang').val();
                data.kode_barang = $("#kode_barang").val();
                data.sort = sort;
                data.sortType = sortType;
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
                data: null,
                className: "text-center",
                orderable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: "spesifikasi",
                className: "text-left"
            },
            {
                data: "no_sub_pembelian",
                className: "text-left"
            },
            {
                data: "no_sub_pembelian",
                className: "text-left",
            },
            {
                data: "no_sub_penjualan",
                className: "text-left",
            },
            {
                data: "no_sub_penjualan",
                className: "text-left",
            },
            {
                data: "no_sub_pemakaian",
                className: "text-left"
            },
            {
                data: "no_sub_pemakaian",
                className: "text-left",
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
                        <div class="mt-0">
                            <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit/Add Account COA" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="detail('${id}')" data-toggle="tooltip" title="Insert Saldo Barang" class="btn btn-success posting-spp actions">
                                <i class="fas fa-plus"></i>
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

    function edit(id) {
        let rowData = table.rows().data().toArray();
        let selectedData = rowData.find(row => row.id == id);
        let account_barang_id = selectedData.account_barang_id;
        let parentName = selectedData.spesifikasi;
        let ap_id = selectedData.ap_id;
        let ar_id = selectedData.ar_id;
        let pemakaian_id = selectedData.pemakaian_id;
        let kategori_id = selectedData.kategori_id;
        let divisiId = $("#kode_department").val();
        let divisiName = $("#kode_department option:selected").text();
        let typeBarang = $("#type_barang").val();

        $('#id').val(account_barang_id);
        $('#divisi_id').val(divisiId);
        $('#barang_id').val(id);
        $('#type').val(typeBarang);
        $("#parentName").val(parentName);
        $('#divisi_name').val(divisiName);
        $("#akun_ap_id").val(ap_id).change();
        $('#akun_ar_id').val(ar_id).change();
        $("#akun_pemakaian_id").val(pemakaian_id).change();
        $('#kategori').val(kategori_id).change();

        $('.add-modal').modal('show');
    }

    function detail(id) {
        const width = 800;
        const height = 600;
        const left = window.innerWidth / 2 - width / 2;
        const top = window.innerHeight / 2 - height / 2;

        window.open(
            "<?= base_url('jurnal/detail/') ?>" + id,
            "_blank",
            `width=${width},height=${height},top=${top},left=${left},resizable=yes`
        );

    }

    $(document).ready(function() {

        // ==========================
        // INIT SELECT2 (tanpa AJAX)
        // ==========================
        $('.kode_department').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        $('.kode_warehouse').select2({
            placeholder: "Pilih Warehouse",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        $('.type_barang').select2({
            placeholder: "Pilih Type Barang",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        $('.kode_barang').select2({
            placeholder: "Pilih Barang",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        //CSS SELECT2 FLOATING LABEL
        $('.akun_ar_id, .akun_ap_id, .kategori, .akun_pemakaian_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.akun_ar_id, .akun_ap_id, .kategori, .akun_pemakaian_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.akun_ar_id, .akun_ap_id, .kategori, .akun_pemakaian_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Akun AR
        $('.akun_ar_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        $('.akun_pemakaian_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // Akun AR
        $('.kategori').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // Akun AP
        $('.akun_ap_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // ==========================
        // AJAX FUNCTIONS
        // ==========================
        function loadDivisi() {
            return $.ajax({
                url: '<?= base_url("jurnal/get-divisi"); ?>',
                dataType: 'json'
            });
        }

        function loadWarehouse(deptID) {
            return $.ajax({
                url: '<?= base_url("jurnal/get-warehouse"); ?>',
                data: {
                    department_id: deptID
                },
                dataType: 'json'
            });
        }

        function loadBarang(typeBarang) {
            return $.ajax({
                url: '<?= base_url("jurnal/get-barang"); ?>',
                data: {
                    type_barang: typeBarang
                },
                dataType: 'json'
            });
        }

        // ==========================
        // POPULATE FUNCTIONS
        // ==========================
        function populateDivisi(data) {
            let $el = $('.kode_department');

            $el.empty().append(`<option value=""></option>`);

            data.data.forEach(item => {
                $el.append(`<option value="${item.id}">${item.divisi}</option>`);
            });

            $el.trigger('change.select2');
        }

        function populateWarehouse(data) {
            let $el = $('.kode_warehouse');

            $el.empty().append(`<option value=""></option>`);

            data.data.forEach(item => {
                $el.append(`<option value="${item.id}">${item.warehouse_name}</option>`);
            });

            $el.trigger('change.select2');
        }

        function populateBarang(data) {
            let $el = $('.kode_barang');

            $el.empty().append(`<option value=""></option>`);

            data.data.forEach(item => {
                $el.append(`<option value="${item.id}">(${item.kode_barang}) ${item.barang_name}</option>`);
            });

            $el.trigger('change.select2');
        }

        // ==========================
        // LOAD DIVISI (awal)
        // ==========================
        loadDivisi().then(res => {
            populateDivisi(res);
        });

        // ==========================
        // ON CHANGE DEPARTMENT → LOAD WAREHOUSE
        // ==========================
        $('.kode_department').on('change', function() {

            let deptID = $(this).val();

            $('.kode_warehouse').empty().append(`<option value=""></option>`).trigger('change');

            if (!deptID) return;

            loadWarehouse(deptID).then(res => {
                populateWarehouse(res);
                $('.kode_warehouse').select2('open'); // auto open
            });
        });

        // ==========================
        // ON CHANGE TYPE BARANG → LOAD BARANG
        // ==========================
        $('.type_barang').on('change', function() {

            let typeBarang = $(this).val();

            $('.kode_barang').empty().append(`<option value=""></option>`).trigger('change');

            if (!typeBarang) return;

            loadBarang(typeBarang).then(res => {
                populateBarang(res);
                $('.kode_barang').select2('open'); // auto open
            });
        });


        // ==========================
        // FIX HEIGHT & LABEL
        // ==========================
        $('.kode_department, .kode_warehouse, .type_barang, .kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_department, .kode_warehouse, .type_barang, .kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px').css('z-index', '1');

        $('.kode_department, .kode_warehouse, .type_barang, .kode_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $("#search").on('click', function() {
            let departmentId = $('#kode_department').val();
            let warehouseId = $('#kode_warehouse').val();
            let typeBarang = $('#type_barang').val();
            let kodeBarang = $('#kode_barang').val();
            if (!departmentId && !warehouseId && !typeBarang && !kodeBarang) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih minimal satu filter untuk pencarian!',
                    confirmButtonText: 'Oke'
                });
                return;
            } else {
                table.ajax.reload();
            }
        })

        // hide modal
        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });

        // action save or update
        $('.btn-submit-form').click(function(e) {
            e.preventDefault();
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
                        let id = $('input[name="id"]').val();
                        let divisi_id = $('#divisi_id').val();
                        let csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.querySelector(".create-form"));
                        data.append("divisi_id", divisi_id);
                        $.ajax({
                            url: "<?= base_url("tipe-barang/save"); ?>",
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
                                            table.ajax.reload();
                                            $('#parentName').val(null);
                                            $(".add-modal").modal("hide")
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        $('#parentName').val(null);
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
                                }).then(() => {
                                    $(".add-modal").modal("hide")
                                });
                            }
                        });
                    }
                })

            }
        });
    });
</script>
<?= $this->endSection(); ?>
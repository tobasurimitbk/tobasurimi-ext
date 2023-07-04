<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control kode_barang" name="kode_barang" id="kode_barang" placeholder="Kode Barang">
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nama_barang" name="nama_barang" id="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_id" name="satuan_id" id="satuan_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Satuan Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" onkeyup="formatNumber(this)" class="form-control harga_barang" name="harga_barang" id="harga_barang" placeholder="Harga Barang">
                                <label for="floatingInput">Harga Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kategori_id" name="kategori_id" id="kategori_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Kategori</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select hs_id" name="hs_id" id="hs_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Kode HS</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select ap_id" name="ap_id" id="ap_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Akun Pembelian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select ar_id" name="ar_id" id="ar_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Akun Penjualan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control stok" name="stok" id="stok">
                                <label for="floatingInput">Stok</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" style="height: 50px;">
                                <label for="floatingInput" class="label-modal-master-barang">Status</label>
                                <div>
                                    <label class="switch">
                                    <input class="status" name="status" id="status" type="checkbox" checked>
                                    <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5 class="modal-sub-title">Spesifikasi</h5>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-add-row btn-add btn-block float-right" style="width: 106px;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table-inside nowrap table-hover-tobasurimi table-add-modal-master-barang" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Spesifikasi</th>
                                <th>Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-spek" id="body-detail-spek" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form">Simpan</button>
                    <button type="button" class="btn btn-discard delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Barang</h1>
    <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </button>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end row-col-spp row-form-select-master-barang-index">
            <div class="col mb-3">
                <input class="form-control search form-out-search" placeholder="Search" value="" />
            </div>
            <div class="col mb-3">
                <select class="form-select kategori form-out-search" name="kategori" id="kategori" aria-label="Floating label select example">
                    <option value="">Kategori: All</option>
                    <?php
                    if (!empty($dataKategori)) {
                        foreach ($dataKategori as $kategori) {
                    ?>
                            <option value="<?= $kategori->id; ?>"><?= $kategori->value; ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col mb-3">
                <select class="form-select filter_status" name="filter_status" id="filter_status" aria-label="Floating label select example">
                    <option value="Aktif">Status: Aktif</option>
                    <option value="Tidak Aktif">Status: Tidak Aktif</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                            <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                            <th onclick="changeSort('harga_barang')" class="sort">Harga Barang</th>
                            <th onclick="changeSort('kode_satuan')" class="sort">Satuan</th>
                            <th onclick="changeSort('kategori')" class="sort">Kategori</th>
                            <th onclick="changeSort('code_hs')" class="sort">Kode HS</th>
                            <th onclick="changeSort('sub_akun_ap')" class="sort">Akun Pembelian</th>
                            <th onclick="changeSort('sub_akun_ar')" class="sort">Akun Penjualan</th>
                            <th onclick="changeSort('stok')" class="sort">Stok</th>
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "kode_barang";
    let sortType = "asc";
    let list_spek = [];
    var row_detail = 0;

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[0, 'asc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("barang/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.kategori = $(".kategori").val();
                data.status = $(".filter_status").val();
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
            data: "kode_barang",
            className: "text-center"
        },
        {
            data: "nama_barang",
            className: "text-center"
        },
        {
            data: "harga_barang",
            className: "text-center"
        },
        {
            data: "kode_satuan",
            className: "text-center"
        },
        {
            data: "kategori",
            className: "text-center"
        },
        {
            data: "code_hs",
            className: "text-center"
        },
        {
            data: "sub_akun_ap",
            className: "text-center"
        },
        {
            data: "sub_akun_ar",
            className: "text-center"
        },
        {
            data: "stok",
            className: "text-center",
            render: function(data, type, row) {
                return `
                <div class="text-danger">
                ${data}
                </div>
                `
            }
        },
        {
            data: "status",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row?.id;
                return `
                <div class="mt-2">
                <label class="switch">
                <input class="status_table" id=${"status_table_" + id} onchange="changeStatus('${id}')" name="status_table" id="status_table" type="checkbox" ${data === "Aktif" ? 'checked' : ''}>
                <span class="slider round"></span>
                </label>
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
        $('.kategori').select2({
            placeholder: "Kategori: All",
            theme: "bootstrap-5",
            allowClear: true
        })

        // SATUAN BARANG
        $('.satuan_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".satuan_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".satuan_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $(".satuan_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SATUAN HS
        $('.satuan_hs').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.satuan_hs')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.satuan_hs')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan_hs')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // AKUN PEMBELIAN 1
        $('.ap_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.ap_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.ap_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.ap_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // AKUN PEMBELIAN 2
        $('.ar_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.ar_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.ar_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.ar_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KATEGORI
        $('.kategori_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
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

        // KODE HS
        $('.hs_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.hs_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.hs_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.hs_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $(".create-form").validate({
            rules: {
                kode_barang: {
                    required: true
                },
                nama_barang: {
                    required: true
                },
                harga_barang: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                kategori_id: {
                    required: true
                },
                hs_id: {
                    required: true
                },
                ap_id: {
                    required: true
                },
                ar_id: {
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
                kategori_id: {
                    required: "Kategori wajib diisi"
                },
                hs_id: {
                    required: "Kode HS wajib diisi"
                },
                ap_id: {
                    required: "Akun Pembelian wajib diisi"
                },
                ar_id: {
                    required: "Akun Pembelian wajib diisi"
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
            $(".body-detail-spek").empty()
            list_spek = [];
            row_detail = 0;
            $('.stok').rules('add', {
                required: true
            });
            $(".id").val("");
            $(".title-name").text("Tambah");
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".stok").attr("readonly", false);
            $(".kode_barang").attr("readonly", false);

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'kategori_barang'
                },
                dataType: "json",
                success: function(res) {
                    $(".kategori_id").empty()
                    $(".kategori_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".kategori_id").append(`<option value="${item.id}">${item.value}</option>`)
                    })

                    $(".kategori_id").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("sub-account/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".ap_id").empty()
                    $(".ar_id").empty()

                    $(".ap_id").append(`<option value=""></option>`)
                    $(".ar_id").append(`<option value=""></option>`)

                    res.data.forEach(function(item) {
                        $(".ap_id").append(`<option value="${item.id}">${item.nama_sub}</option>`)
                        $(".ar_id").append(`<option value="${item.id}">${item.nama_sub}</option>`)
                    })

                    $(".ap_id").val("").change();
                    $(".ar_id").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("satuan/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".satuan_id").empty()

                    $(".satuan_id").append(`<option value=""></option>`)

                    res.data.forEach(function(item) {
                        $(".satuan_id").append(`<option value="${item.id}">${item.nama_satuan}</option>`)
                    })

                    $(".satuan_id").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("hs-code/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".hs_id").empty()

                    $(".hs_id").append(`<option value=""></option>`)

                    res.data.forEach(function(item) {
                        $(".hs_id").append(`<option value="${item.id}">${item.code}</option>`)
                    })

                    $(".hs_id").val("").change();
                    $(".add-modal").modal("show")
                }
            })
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $('.stok').rules('remove', 'required');
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");
            $(".stok").attr("readonly", true);
            $(".kode_barang").attr("readonly", true);
            $(".body-detail-spek").empty()
            row_detail = 0;
            list_spek = [];

            $.ajax({
                url: "<?= base_url("barang/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        let new_spek = res?.data?.spek;
                        let tag_html = "";

                        new_spek.forEach((item) => {
                            row_detail++;
                            list_spek.push(
                            {
                                row: row_detail,
                                display: "",
                            })
                            tag_html += `<tr class="table_${row_detail}">`;
                            tag_html += `<td>`;
                            tag_html += `<input type="text" onkeypress="return lettersOnly(event)" value="${item}" class="form-control spek_${row_detail}" id="spek_${row_detail}" name="spek_${row_detail}">`;
                            tag_html += `</td>`;
                            tag_html += `<td>`;
                            tag_html += `<button onclick='deleteChildRow(${row_detail})'>X</button>`;
                            tag_html += `</td>`;
                            tag_html += `</tr>`;
                        })

                        $(".body-detail-spek").append(tag_html)

                        $(".id").val(id);
                        $(".kode_barang").val(res?.data?.kode_barang);
                        $(".nama_barang").val(res?.data?.nama_barang);
                        $(".harga_barang").val(res?.data?.harga_barang);
                        
                        validator.resetForm();
                        validator.reset();

                        $.ajax({
                            url: `<?= base_url("metadata/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                name: 'kategori_barang'
                            },
                            dataType: "json",
                            success: function(result) {
                                $(".kategori_id").empty()
                                $(".kategori_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".kategori_id").append(`<option value="${item.id}">${item.value}</option>`)
                                })

                                $(".kategori_id").val(res?.data?.kategori_id).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("sub-account/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".ap_id").empty()
                                $(".ar_id").empty()

                                $(".ap_id").append(`<option value=""></option>`)
                                $(".ar_id").append(`<option value=""></option>`)

                                result.data.forEach(function(item) {
                                    $(".ap_id").append(`<option value="${item.id}">${item.nama_sub}</option>`)
                                    $(".ar_id").append(`<option value="${item.id}">${item.nama_sub}</option>`)
                                })

                                $(".ap_id").val(res?.data?.ap_id).change();
                                $(".ar_id").val(res?.data?.ar_id).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("satuan/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".satuan_id").empty()

                                $(".satuan_id").append(`<option value=""></option>`)

                                result.data.forEach(function(item) {
                                    $(".satuan_id").append(`<option value="${item.id}">${item.nama_satuan}</option>`)
                                })

                                $(".satuan_id").val(res?.data?.satuan_id).change();
                            }
                        })

                        $.ajax({
                            url: `<?= base_url("hs-code/dropdown"); ?>`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".hs_id").empty()

                                $(".hs_id").append(`<option value=""></option>`)

                                result.data.forEach(function(item) {
                                    $(".hs_id").append(`<option value="${item.id}">${item.code}</option>`)
                                })

                                $(".hs_id").val(res?.data?.hs_id).change();
                                $(".add-modal").modal("show")
                            }
                        })
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

        $(".kategori, .filter_status").change(function () {
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
                        let data = new FormData(document.querySelector(".create-form"));

                        let id = $(".id").val();

                        let update_list_spek = [];
                        console.log(list_spek)
                        list_spek.forEach((item) => {
                            if(item.display != "none")
                            {
                                update_list_spek.push($(".spek_" + item.row).val());
                            }
                        })

                        data.append("spek", JSON.stringify(update_list_spek));

                        // UPDATE
                        if(id)
                        {
                            $.ajax({
                                url: "<?= base_url("barang/update"); ?>",
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
                        // CREATE
                        else
                        {
                            $.ajax({
                                url: "<?= base_url("barang/save"); ?>",
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
                    }
                })
            }
        })

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
                        url: "<?= base_url("barang/delete"); ?>",
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
        })
    })

    const changeStatus = function(id)
    {
        const csrf = $(`[name="${csrfToken}"]`);
        let value = document.getElementById('status_table_' + id).checked ? true : false;

        let data = {
            id: id
        }

        if(value)
        {
            data["status"] = true;
        }

        $.ajax({
            url: "<?= base_url("barang/update-status"); ?>",
            data: data,
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
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    })
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

    $(".btn-add-row").click(function() {
        row_detail++;
        list_spek.push(
        {
            row: row_detail,
            display: "",
        })
        
        let tag_html = "";
        tag_html += `<tr class="table_${row_detail}">`;
        tag_html += `<td>`;
        tag_html += `<input type="text" onkeypress="return lettersOnly(event)" class="form-control spek_${row_detail}" id="spek_${row_detail}" name="spek_${row_detail}">`;
        tag_html += `</td>`;
        tag_html += `<td>`;
        tag_html += `<button onclick='deleteChildRow(${row_detail})'>X</button>`;
        tag_html += `</td>`;
        tag_html += `</tr>`;

        $(".body-detail-spek").append(tag_html)
    })

    const deleteChildRow = function(id) {
        $(".table_" + id).css("display", "none")
        let new_list_spek = []
        list_spek.forEach((item) => {
            if(item.row !== id)
            {
                new_list_spek.push(item)
            }
            else
            {
                new_list_spek.push({row: id, display: "none"})
            }
        })

        list_spek = new_list_spek;
    }

    const changeSort = function(val) {
        if(sort !== val)
        {
            sortType = "asc";
            sort = val;
        }
        else
        {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>
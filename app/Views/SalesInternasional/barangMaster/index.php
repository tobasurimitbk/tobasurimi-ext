<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Master Barang Sales Ekspor</h1>
        <?php if (can('Penjualan Ekspor', 'Master Barang', 'p')) : ?>
            <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 20px;">
                Import / Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item btn-upload-excel-master-barang-ekspor">Import Excel</button></li>
                <li><button class="dropdown-item" onclick="exportExcel()">Export Excel</button></li>
            </ul>
        <?php endif; ?>
        <?php if (can('Penjualan Ekspor', 'Master Barang', 'c')): ?>
            <button class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp row-form-select-master-barang-index">

                <div class="col-md-3 col mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Nama Barang / Kode Barang" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('type_barang')" class="sort">Tipe Barang</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('barang_name')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('satuan_id')" class="sort">Satuan</th>
                                <!-- <th onclick="changeSort('harga_pokok')" class="sort">Harga Pokok</th> -->
                                <th onclick="changeSort('harga_jual')" class="sort">Harga Jual</th>
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

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form class="create-form" role="form" method="POST">
                    <input type="hidden" class="id" name="id" id="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" id="kode_barang" class="form-control kode_barang" name="kode_barang" placeholder="Kode Barang">
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                    <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                        <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 5px; margin-left: -30px;" id="generate_new_code" name="generate_new_code" type="checkbox" onchange="generateNewCode()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_barang" name="type_barang" id="type_barang">
                                    <option value="">Pilih Tipe Barang</option>
                                    <option value="bahan_jadi">BARANG JADI</option>
                                    <option value="kemasan">KEMASAN</option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select divisi_id" name="divisi_id" id="divisi_id">
                                    <option value=""></option>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option value="<?= ($d['id']) ?>"><?= $d['divisi'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Department</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control barang_name" name="barang_name" id="barang_name" placeholder="Nama Kemasan">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <!-- <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input oninput="preventNegativeInput(this)" onkeyup="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control harga_pokok" name="harga_pokok" id="harga_pokok" placeholder="Harga Pokok">
                                <label for="floatingInput">Harga Pokok</label>
                            </div>
                        </div> -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_id" name="satuan_id" id="satuan_id">
                                    <option value=""></option>
                                    <?php foreach ($satuan as $kb) : ?>
                                        <option value="<?= ($kb['id']) ?>"><?= $kb['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input oninput="preventNegativeInput(this)" onkeyup="this.value=greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control harga_jual" name="harga_jual" id="harga_jual" placeholder="Harga Jual">
                                <label for="floatingInput">Harga Jual</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form" id="btn-submit-form">Simpan</button>
                <?php if (can('Penjualan Ekspor', 'Master Barang', 'd')) : ?>
                    <button type="button" class="btn btn-discard delete-btn">Hapus</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="import_master_barang_ekspor" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Master Barang</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary text-black" role="alert">
                    UNDUH TEMPLEATE EXCEL <a href="<?= base_url('assets/import/IMPORT_MASTER_BARANG_SALES_EKSPOR.xlsx') ?>" style="text-decoration: none;"><b style="color: black;">DISINI</b></a>
                </div>
                <form class="form-excel-master-barang-ekspor" method="post">
                    <input type="hidden" name="type_barang_sales" value="EKSPOR">
                    <div class="form-floating" style="height: 50px;">
                        <input type="file" name="file" id="file" accept=".xlsx" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2" id="btn-discard-import-excel-master-barang-ekspor">Kembali</button>
                <button type="submit" class="btn btn-submit-form" id="btn-submit-excel-master-barang-ekspor">Simpan</button>
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

        processing: true,
        serverSide: true,
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
        ajax: {
            url: "<?= base_url("master-barang-internasional/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            },
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
                sortable: false,
                width: "5%"
            },
            {
                data: "type_barang",
                className: "text-center",
            },
            {
                data: "kode_barang",
                className: "text-center",
            },
            {
                data: "barang_name",
                className: "text-center",
            },
            {
                data: "kode_satuan",
                className: "text-center",
            },
            // {
            //     data: "harga_pokok",
            //     className: "text-center",
            //     render: function(data) {
            //         return greatFormatRupiah(data);
            //     }
            // },
            {
                data: "harga_jual",
                className: "text-center",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            },
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Master Barang Sales Masih Kosong",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('.btn-upload-excel-master-barang-ekspor').click(function() {
        $('#import_master_barang_ekspor').modal('show');
    });

    $('#btn-discard-import-excel-master-barang-ekspor').click(function() {
        $('#import_master_barang_ekspor').modal('hide');
    });

    $('#btn-submit-excel-master-barang-ekspor').click(function() {
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
                let formData = new FormData(document.querySelector(".form-excel-master-barang-ekspor"));
                $.ajax({
                    url: "<?= base_url("master-barang-internasional/import-excel"); ?>",
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
                                table.ajax.reload();
                                $('#import_master_barang_ekspor').modal('hide');
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
    });

    const exportExcel = function() {
        var url = "<?= base_url('master-barang-internasional/export-excel') ?>";
        window.open(url + `?sort=${sort}&sortType=${sortType}&`, "_blank");
    }


    var validator = $(".create-form").validate({
        rules: {
            kode_barang: {
                required: true
            },
            barang_name: {
                required: true
            },
            type_barang: {
                required: true
            },
            satuan_id: {
                required: true
            },
            // harga_pokok: {
            //     required: true
            // },
            harga_jual: {
                required: true
            }
        },
        messages: {
            kode_barang: {
                required: "Kode barang wajib diisi"
            },
            barang_name: {
                required: "Nama barang wajib diisi"
            },
            type_barang: {
                required: "Tipe barang wajib diisi"
            },
            satuan_id: {
                required: "Satuan wajib diisi"
            },
            // harga_pokok: {
            //     required: "Harga pokok wajib diisi"
            // },
            harga_jual: {
                required: "Harga jual wajib diisi"
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

    $('#btn-submit-form').click(function() {
        if ($('.create-form').valid()) {
            let id = $('.id').val();
            let data = new FormData(document.querySelector('.create-form'));
            // let hargaPokok = destroyFormatRupiah($('#harga_pokok').val());
            let hargaJual = destroyFormatRupiah($('#harga_jual').val());
            data.set('harga_jual', hargaJual);
            // data.set('harga_pokok', hargaPokok);

            if (id) {
                <?php if (can('Penjualan Ekspor', 'Master Barang', 'u')) : ?>
                    Swal.fire({
                        icon: 'question',
                        title: 'Update Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "<?= base_url("master-barang-internasional/update"); ?>",
                                data: data,
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
                                            table.ajax.reload();
                                            $('#add_modal').modal('hide');
                                            resetForm();
                                        })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            cancelButtonColor: '#d33',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        })
                                    }
                                }
                            });
                        }
                    })
                <?php else : ?>
                    Swal.fire({
                        icon: 'error',
                        title: "Anda tidak punya hak akses update",
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        reverseButtons: true,
                        confirmButtonText: 'Oke',
                    })
                <?php endif; ?>
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url("master-barang-internasional/save"); ?>",
                            data: data,
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
                                        table.ajax.reload();
                                        $('#add_modal').modal('hide');
                                        resetForm();
                                    })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                        cancelButtonColor: '#d33',
                                        reverseButtons: true,
                                        confirmButtonText: 'Oke',
                                    })
                                }
                            }
                        });
                    }
                })
            }

        }
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        resetForm();
        const data = table.row(this).data();
        let id = data.id;
        let formData = new FormData();
        formData.append("id", id);

        $.ajax({
            url: "<?= base_url("master-barang-internasional/get"); ?>",
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
            success: function(res) {
                csrf.val();
                $('.id').val(res.data.id);
                $('.kode_barang').val(res.data.kode_barang);
                $('.barang_name').val(res.data.barang_name);
                $('.type_barang').val(res.data.type_barang).change();
                $('.satuan_id').val(res.data.satuan_id).change();
                  $('.divisi_id').val(res.data.divisi_id).change();
                $('.harga_jual').val(greatFormatRupiah(res.data.harga_jual));

                $('.input-generate').hide();
                $('.kode_barang').attr('readonly', true);
                $('.type_barang').attr('disabled', true);
                $('#add_modal').modal('show');
                $('.delete-btn').show();
                $('.title-name').text('Update Barang')
            }
        })
    });

    $(".delete-btn").click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Barang ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                let csrf = $(`[name="${csrfToken}"]`);
                let id = $('input[name="id"]').val();
                $.ajax({
                    url: "<?= base_url("master-barang-internasional/delete"); ?>",
                    data: {
                        id: id
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
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            reverseButtons: true,
                            confirmButtonText: 'Oke',
                        }).then((result) => {
                            table.ajax.reload();
                            $('#add_modal').modal('hide');
                            resetForm();
                        })

                    },
                });
            }
        })
    });

    $('.btn-add').click(function() {
        resetForm();
        $('.input-generate').show();
        $('.type_barang').attr('disabled', false); // Enable the select element
        $('#add_modal').modal('show');
        $('.delete-btn').hide();
        $('.title-name').text('Tambah Barang')
    });

    $('.btn-hide-form').click(function() {
        resetForm();
        $('.add-modal').modal('hide');
    });

    $('.search').keyup(function() {
        table.ajax.reload();
    });

    // Initialize select2 for type_barang
    $("#type_barang").select2({
        theme: "bootstrap-5",
        placeholder: 'Pilih Tipe Barang',
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content")
    }).change(function() {
        let value = document.getElementById('generate_new_code').checked ? true : false;
        if (value) {
            generateNewCode();
        }
        
        // Enable/disable divisi_id based on type_barang selection
        if ($(this).val()) {
            $("#divisi_id").prop('disabled', false);
        } else {
            $("#divisi_id").prop('disabled', true).val(null).trigger('change');
            $("#barang_name").prop('disabled', true).val('');
        }
    });

    // Initialize select2 for divisi_id (initially disabled)
    $("#divisi_id").select2({
        theme: "bootstrap-5",
        placeholder: 'Pilih Divisi',
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content"),
        disabled: true // Initially disabled
    }).change(function() {
        let divisiId = $(this).val();
        fetchBarangData(divisiId);
        
        $("#barang_name").prop('disabled', false);
    });

    // // Function to fetch barang data
    function fetchBarangData(divisiId) {
        let csrfToken = '<?= csrf_token() ?>';
        let csrf = $(`[name="${csrfToken}"]`);
        
        $.ajax({
            url: 'master-barang-internasional/get-barang-jadi-master',
            method: 'POST',
            data: { divisi_id: divisiId },
            dataType: 'json',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                $("#barang_name").prop('disabled', true).html('');
            },
            success: function(response) {
                // Replace with select element
                $("#barang_name").replaceWith(`
                    <select class="form-select barang_name" name="barang_name" id="barang_name" placeholder="Nama Barang">
                        <option value=""></option>
                        ${response.data.map(item => `<option value="${item.id}">${item.barang_name}</option>`).join('')}
                    </select>
                `);
                
                // Initialize select2 with tags option for editable selection
                $("#barang_name").select2({
                    theme: "bootstrap-5",
                    placeholder: 'Pilih Barang',
                    allowClear: true,
                    dropdownParent: $(".add-modal .modal-content"),
                    tags: true, // Allow custom entries
                    createTag: function(params) {
                        // Don't create tags for empty input
                        if (params.term === '') {
                            return null;
                        }
                        return {
                            id: params.term,
                            text: params.term,
                            newTag: true // Add additional parameter
                        };
                    },
                    insertTag: function(data, tag) {
                        // Insert the tag at the end of the results
                        data.push(tag);
                    }
                });
                
                // Enable typing in the search box even after selection
                $("#barang_name").on('select2:select', function(e) {
                    if (e.params.data.newTag) {
                        // This is a newly created tag (custom entry)
                        // You can handle the new value here if needed
                        console.log("New custom entry:", e.params.data.text);
                    }
                });
                
                $("label[for='barang_name']").text('Nama Barang');
            },
            error: function(xhr, status, error) {
                console.error('Error fetching barang data:', error);
                // Fallback to regular input if AJAX fails
                $("#barang_name").replaceWith(`
                    <input autocomplete="one-time-code" type="text" class="form-control barang_name" 
                        name="barang_name" id="barang_name" placeholder="Nama Barang">
                `);
                $("#barang_name").prop('disabled', false);
                $("label[for='barang_name']").text('Nama Barang');
            }
        });
    }


    $("#satuan_id").select2({
        theme: "bootstrap-5",
        placeholder: 'Pilih Satuan',
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content")
    });

    function resetForm() {
        $(".id").val(null);
        $(".type_barang").val(null).change();
        $('.barang_name').val(null);
        // $('.harga_pokok').val(null);
        $('.harga_jual').val(null);
        $('.satuan_id').val(null).change();
        $("#generate_new_code").prop('checked', false).change();

    }

    function generateNewCode() {
        let csrfToken = '<?= csrf_token() ?>';
        let value = document.getElementById('generate_new_code').checked ? true : false;
        let csrf = $(`[name="${csrfToken}"]`);
        let type_barang = $(".type_barang").val() == "" ? "bahan_jadi" : $(".type_barang").val();
        if (value && type_barang) {
            $("input[name='kode_barang']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("master-barang-internasional/generate-new-code"); ?>`,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                data: {
                    type_barang: type_barang
                },
                method: "POST",
                success: function(res) {
                    csrf.val(res.token);
                    let id = $('.id').val();
                    if (id == "") {
                        $("input[name='kode_barang']").attr("readonly", true);
                        $("input[name='kode_barang']").val(res.codeNew);
                    }
                }
            })
        } else {
            let id = $('.id').val();
            if (id == "") {
                $("input[name='kode_barang']").attr("readonly", false);
                $("input[name='kode_barang']").val("");
            }
        }
    }

    // sort
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function formatRupiah(angka) {
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return '' + ribuanFormatted + ',' + desimal;
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
</script>

<?= $this->endSection(); ?>
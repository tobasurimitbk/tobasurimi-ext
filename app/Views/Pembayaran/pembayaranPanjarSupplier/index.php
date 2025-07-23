<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 1200px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form class="create-form" role="form" method="POST">
                    <!-- Parent Form (Header) -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5>Informasi Panjar & Pinjaman</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input type="text" style="display: none;" class="form-control hidden" name="id" id="id">

                                <div class="col-md-6">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control name" id="no_transaksi" name="no_transaksi" placeholder="No Transaksi">
                                            <label for="no_transaksi">No Transaksi</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                            <option value=""></option>
                                            <?php foreach ($divisi as $d) : ?>
                                                <option value="<?= $d['id'] ?>">
                                                    <?= $d['divisi']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Departemen</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                        <select class="form-select" name="bank_id" id="bank_id">
                                                <option disabled selected value=""></option>
                                            <?php foreach ($bankList as $b) : ?>
                                                <option value="<?= $b['id'] ?>"><?= strtoupper($b['kode_bank']) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Kode Bank (Opsional)</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="jenis" id="jenis">
                                            <option value="PUTIH">PUTIH</option>
                                            <option value="MERAH">MERAH</option>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Jenis </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select tipe_supplier" name="tipe_supplier" id="tipe_supplier">
                                            <option value="">Pilih Supplier</option>
                                            <option value="INTERNASIONAL">INTERNASIONAL</option>
                                            <option value="BAHAN PENOLONG">BAHAN PENOLONG</option>
                                            <option value="BAHAN BAKU">BAHAN BAKU</option>
                                        </select>
                                        <label for="tipe_supplier">Tipe Supplier</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select supplier_id" name="supplier_id" id="supplier_id"></select>
                                        <label for="supplier_id">Supplier</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select " name="payment_method" id="payment_method">
                                            <option disabled selected value="">Pilih Metode Pembayaran</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan" value="">
                                        <label for="floatingInput">Keterangan</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Child Form (Details) -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5>Detail Panjar & Pinjaman</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="akun_kas" id="akun_kas">
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Debit</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="akun_selisih" id="akun_selisih">
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Kredit</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select jenis_transaksi" name="jenis_transaksi" id="jenis_transaksi">
                                            <option value=""></option>
                                            <option value="PANJAR">PANJAR</option>
                                            <option value="PANJAR_TB">PANJAR TB</option>
                                            <option value="PINJAMAN">PINJAMAN</option>
                                        </select>
                                        <label for="jenis_transaksi">Jenis Transaksi</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input onkeyup="this.value = greatFormatRupiah(this.value)" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" class="form-control nominal_pembayaran" name="nominal_pembayaran" id="nominal_pembayaran" placeholder="Nominal Pembayaran">
                                        <label for="floatingInput">Nominal Pembayaran</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Jatuh Tempo">
                                            <label for="floatingInput">Tanggal Pembayaran</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 6px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control keterangan_detail" name="keterangan_detail" id="keterangan_detail" placeholder="Keterangan" value="">
                                        <label for="floatingInput">Keterangan</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <button type="button" class="btn btn-success btn-update-detail" style="display:none;">Update Detail</button>
                                    <button type="button" class="btn btn-primary btn-add-detail">Tambah Detail</button>
                                </div>
                            </div>

                            <!-- Table for showing added details -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <table class="table table-bordered" id="detail-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis Transaksi</th>
                                                <th>Nominal</th>
                                                <th>Akun Debit</th>
                                                <th>Akun Kredit</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Details will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form">Simpan Semua</button>
            </div>
        </div>
    </div>
</div>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Panjar & Pinjaman Supplier</h1>
        <button class="btn btn-show-form btn-add float-right" id="btn-display-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col mb-4">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating spp-ptspp mb-3" style="height: 50px;">
                        <select class="form-select kategori panjar_status form-out-search" name="panjar_status" id="panjar_status" aria-label="Floating label select example">
                            <option value="ALL">STATUS: SEMUA</option>
                            <option value="NOT_POSTING">STATUS: BELUM POSTING</option>
                            <option value="POSTING">STATUS: SUDAH POSTING</option>

                        </select>
                        <label for="floatingInput" class="l-spp-ptspp">Status Posting</label>
                    </div>
                </div>
                <div class="col mb-4">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('no_panjar')">No. Transaksi</th>
                                <th onclick="changeSort('jenis_panjar')">Jenis</th>
                                <th onclick="changeSort('supplier_id')">Supplier</th>
                                <th>Nominal</th>
                                <th>Tanggal Di Buat</th>
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

<!-- history modal -->
<div class="modal fade" id="historiModal" tabindex="-1" role="dialog" aria-labelledby="historiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historiModalLabel">Histori Pembayaran Panjar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control nomor_panjar" name="nomor_panjar" id="nomor_panjar">
                            <label for="floatingInput">No Panjar</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control supplier_name" name="supplier_name" id="supplier_name">
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                    <!-- <div class="col-sm-12">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control lpb_no" name="lpb_no" id="lpb_no">
                            <label for="floatingInput">Nomor LPB</label>
                        </div>
                    </div> -->
                </div>
                <div class="table-responsive">
                    <table class="table table-inside table-borderd nowrap table-hover-tobasurimi dataTable2" style="width: 100%;" id="tableHistori">
                        <thead>
                            <tr>
                                <td style="width: 10px;text-align: center;color:#E7323A;font-weight:bold;">No</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;" class="nomor">Nomor PO</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;">Jenis</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;">Total Panjar</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;">Payment Date</td>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "no_panjar";
    let sortType = "desc";
    let trigger = true;
    let details = [];
    let editingIndex = -1;
    let isEditMode = false;
    let initialValues = {};


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
            url: "<?= base_url("panjar-supplier/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.status = $('.panjar_status option:selected').val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
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
                sortable: false,
                width: "5%"
            },
            {
                data: "no_transaction",
                className: "text-center",
                // width: "10%"
            },
            {
                data: "type",
                className: "text-center"
            },
            {
                data: "supplier",
                className: "text-center"
            },
            {
                data: "nominal",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            },
            {
                data: "createdAt",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,

                render: function(data, type, row) {
                    let id = row.id;
                    let is_posted = row.is_posted;

                    if (is_posted == '0') {
                        return `
                        <div class="btn-group" role="group">
                           
                                <button data-toggle="tooltip" 
                                        title="Posting" 
                                        onclick="postPanjar('${id}')" 
                                        class="btn btn-success posting-panjar-supplier">
                                    <i class="fa fa-paper-plane fa-sm"></i>
                                </button>

                                <button data-toggle="tooltip" 
                                        title="Hapus" 
                                        onclick="confirmDelete('${id}')" 
                                        class="btn btn-danger">
                                    <i class="fa fa-trash fa-sm"></i>
                                </button>
                        </div>
                        `;
                    } else {
                        return `
                        <div class="btn-group" role="group">
                                <button data-toggle="tooltip" 
                                        title="Unpost" 
                                        onclick="unpostPanjar('${id}')" 
                                        class="btn btn-warning">
                                    <i class="fa fa-undo fa-sm"></i>
                                </button>
                        </div>
                        `;
                    }


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

    // INIT VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_transaksi: {
                required: true
            },
            jenis: {
                required: true
            },
            tipe_supplier: {
                required: true
            },
            supplier_id: {
                required: true
            },
            keterangan: {
                required: true
            }
        },
        messages: {
            no_transaksi: {
                required: "Nomor transaksi wajib diisi"
            },
            jenis: {
                required: "Jenis (PUTIH/MERAH) wajib diisi"
            },
            tipe_supplier: {
                required: "Tipe supplier wajib diisi"
            },
            supplier_id: {
                required: "Supplier wajib dipilih"
            },
            keterangan: {
                required: "Keterangan wajib dipilih"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            // Handle Select2 elements
            if (elem.hasClass("select2-hidden-accessible")) {
                element = elem.next().find(".select2-selection");
                error.insertAfter(element);
            }
            // Handle datepicker
            else if (elem.hasClass("input-picker")) {
                error.insertAfter(elem.closest('.input-group'));
            }
            // Handle other elements
            else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-floating').addClass('has-error');
            $(element).addClass('is-invalid');

            // Special handling for Select2
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element).next().find('.select2-selection').addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            $(element).closest('.form-floating').removeClass('has-error');
            $(element).removeClass('is-invalid');

            // Special handling for Select2
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element).next().find('.select2-selection').removeClass('is-invalid');
            }
        },
        submitHandler: function(form) {
            // This will be handled by our custom submit button click handler
            return false;
        }
    });

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di hapus?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("panjar-supplier/delete"); ?>",
                    data: {
                        id: id,
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
                                    table.ajax.reload()
                                })
                        }
                    },
                });
            }
        })
    }

    const deleteForm = function() {
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
                setLoading()
                $.ajax({
                    url: "<?= base_url("panjar-supplier/delete"); ?>",
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
    }

    //delete form by form
    $('.delete-form').click(deleteForm);


    // HIDE MODAL
    $('.btn-discard').click(function() {
        // Reset form validation

        // Clear input fields
        $('.add-modal input').val('');
        $('.add-modal textarea').val('');
        $('.add-modal select').val('').trigger('change');

        // // Reset other cached data or state
        $('.add-modal .error-message').text('');
        $('.add-modal .preview-image').attr('src', '');
        // Hide modal
        $('.add-modal').modal('hide');
    });

    // Modal close handler - Reset semua state
    $('#add_modal').on('hidden.bs.modal', function() {
        // 1. Reset form utama
        isEditMode = false;
        initialValues = {};
        $('.create-form')[0].reset();
        
        // 2. Reset select2
        $('.form-select').val('').trigger('change');
        
        // 3. Clear detail table
        $('#detail-table tbody').empty();
        
        // 4. Reset array details
        details = [];
        
        // 5. Reset editing state
        editingIndex = -1;
        
        // 6. Reset tombol
        $('.btn-add-detail').show();
        $('.btn-update-detail').hide();
        
        // 7. Reset validasi
        $('.is-invalid').removeClass('is-invalid');
        $('.has-error').removeClass('has-error');
        $('.text-danger').remove();
        
        // 8. Reset field khusus
        $('#auto_generate').show();
        $('#no_transaksi').val('').prop('disabled', false);
        
        // 9. Reset title dan tombol delete
        $(".title-name").text("Tambah Data Panjar & Pinjaman");
        $(".delete-btn").hide();
        
        // 10. Reset ID jika ada
        $("#id").val('');
    });

    // Fungsi reset tambahan yang bisa dipanggil manual
    function resetAllForm() {
        $('#add_modal').modal('hide'); // Ini akan trigger event hidden.bs.modal
    }

    // Contoh implementasi tombol cancel/batal
    $('.btn-cancel-form').click(function() {
        resetAllForm();
    });

    // $('#tipe_supplier').select2({
    //     placeholder: "Pilih Tipe Supplier",
    //     theme: "bootstrap-5",
    //     dropdownParent: $(".add-modal .modal-content")
    // });

    $('#tipe').select2({
        placeholder: "Pilih Tipe Panjar",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });

    $('#jenis_transaksi').select2({
        placeholder: "Pilih Jenis Transaksi",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });


    function initSelect2() {
        $('#jenis').select2({
            placeholder: "Pilih Jenis",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        }).on('change', function() {
            handleFieldChange(this);
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
            dropdownParent: $('#add_modal .modal-content')
        }).on('change', function() {
            handleFieldChange(this);
        });

        $('#bank_id').select2({
            placeholder: "Pilih Bank",
            theme: "bootstrap-5",
            dropdownParent: $('#add_modal .modal-content')
        }).on('change', function() {
            handleFieldChange(this);
        });

        $('#payment_method').select2({
            placeholder: "Pilih Metode Pembayaran",
            theme: "bootstrap-5",
            dropdownParent: $('#add_modal .modal-content')
        }).on('change', function() {
            handleFieldChange(this);
        });
    }

    

    $("#tipe_supplier, #supplier_id, #tipe, #jenis_transaksi, #jenis")
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        language: 'id',
        todayBtn: "linked"
    }).on('changeDate', function(e) {
        $(this).valid();
    });


    $(document).on('click', '.btn-edit-detail', function(e) {
        e.preventDefault();
        const index = $(this).data('index');
        editDetail(index);
    });

    function handleFieldChange(element) {
        if (!isEditMode) {
            generatePaymentNumber();
            return;
        }
        
        // Untuk mode edit, hanya generate jika nilai berubah
        let currentField = $(element).attr('id');
        let currentValue = $(element).val();
        
        if (initialValues[currentField] !== currentValue) {
            generatePaymentNumber(true);
        }
    }

    function editDetail(index) {
        const detail = details[index];
        
        // Isi field biasa
        $('#tanggal').val(detail.tanggal);
        $('#jenis_transaksi').val(detail.jenis_transaksi).trigger('change');
        $('#nominal_pembayaran').val(detail.nominal_pembayaran);
        $('#keterangan_detail').val(detail.keterangan);
        
        // Inisialisasi Select2 untuk akun_kas
        const akunKasOption = new Option(detail.akun_kas_name, detail.akun_kas, true, true);
        $('#akun_kas').append(akunKasOption).trigger('change');
        
        // Inisialisasi Select2 untuk akun_selisih
        const akunSelisihOption = new Option(detail.akun_selisih_name, detail.akun_selisih, true, true);
        $('#akun_selisih').append(akunSelisihOption).trigger('change');
        
        // Set index yang sedang diedit
        editingIndex = index;
        
        // Ubah tampilan tombol
        $('.btn-add-detail').hide();
        $('.btn-update-detail').show();
    }


    // Refresh details table
    function refreshDetailsTable() {
        const tbody = $('#detail-table tbody');
        tbody.empty();

        details.forEach((detail, index) => {
            tbody.append(`
                    <tr data-detail-id="${detail.id || ''}">
                        <td>${detail.tanggal}</td>
                        <td>${detail.jenis_transaksi}</td>
                        <td>${detail.nominal_pembayaran}</td>
                        <td>${detail.akun_kas_name || detail.akun_kas}</td>
                        <td>${detail.akun_selisih_name || detail.akun_selisih}</td>
                        <td>${detail.keterangan}</td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm btn-edit-detail" data-index="${index}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm btn-remove-detail" data-index="${index}">Hapus</button>
                        </td>
                    </tr>
                `);
        });
    }

    function generatePaymentNumber(forceGenerate = false) {
        // Jika di mode edit dan bukan force generate, skip
        if (isEditMode && !forceGenerate) return;
        
        // Get current values
        let currentValues = {
            divisi_id: $("#divisi_id").val(),
            bank_id: $("#bank_id").val(),
            payment_method: $("#payment_method").val(),
            jenis: $("#jenis").val()
        };
        
        // Jika nilai sama dengan initial values, skip
        if (isEditMode && JSON.stringify(currentValues) === JSON.stringify(initialValues)) return;
        
        // Proses generate nomor
        let jenisPembayaran = $("#jenis option:selected").text();
        let divisiId = $("#divisi_id option:selected").text();
        let paymentMethod = $("#payment_method option:selected").text();
        let bankId = $("#bank_id option:selected").val();
        
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        
        let url = "<?= base_url('panjar-supplier/generate-no-panjar'); ?>";
        url += `?jenisPembayaran=${encodeURIComponent(jenisPembayaran)}&divisiId=${encodeURIComponent(divisiId)}&paymentMethod=${encodeURIComponent(paymentMethod)}&bankId=${encodeURIComponent(bankId)}`;
        
        $("#no_transaksi").attr("readonly", true);
        
        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            success: function(response) {
                csrf.val(response.token);
                $("#no_transaksi").val(response.paymentNo);
            },
            error: function() {
                $("#no_transaksi").attr("readonly", false);
                Swal.fire("Error", "Gagal generate nomor", "error");
            }
        });
    }


    let selectedSupplierId = null; // Global variable untuk simpan supplier ID saat edit

    // Event saat klik baris table untuk edit data
    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        const modal = $(".add-modal");
        $(".title-name").text("Edit Data Panjar & Pinjaman");

        modal.modal("show");
        $('#auto_generate').hide();

        // AJAX get detail data
        $.ajax({
            url: "panjar-supplier/id/" + data.id,
            method: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    modal.find('.modal-body').html($('#modal-template').html());
                    isEditMode = true;

                    initialValues = {
                        divisi_id: res.data.transaction.divisi_id,
                        bank_id: res.data.transaction.bank_id,
                        payment_method: res.data.transaction.payment_method,
                        jenis: res.data.transaction.type
                    };

                    details = [];

                    // Populate form
                    $('#id').val(res.data.transaction.id);
                    $('#no_transaksi').val(res.data.transaction.no_transaction);
                    $('#divisi_id').val(res.data.transaction.divisi_id).trigger('change');
                    $('#payment_method').val(res.data.transaction.payment_method).trigger('change');
                    $('#bank_id').val(res.data.transaction.bank_id).trigger('change');
                    $('#jenis').val(res.data.transaction.type).trigger('change');
                    $('#tipe_supplier').val(res.data.supplier?.type || '');

                    $('#keterangan').val(res.data.transaction.keterangan || '');

                    // Simpan supplier ID untuk keperluan re-select setelah append dropdown
                    if (res.data.supplier) {
                        selectedSupplierId = res.data.supplier.id;
                    }

                    // Populate detail data
                    res.data.details.forEach(detail => {
                        const newDetail = {
                            id: detail.id,
                            tanggal: detail.payment_date,
                            jenis_transaksi: detail.jenis_transaksi,
                            nominal_pembayaran: greatFormatRupiah(detail.nominal_pembayaran),
                            akun_kas: detail.akun_kas.id,
                            akun_kas_name: detail.akun_kas.name || $('#akun_kas option[value="' + detail.akun_kas + '"]').text(),
                            akun_selisih: detail.akun_selisih.id,
                            akun_selisih_name: detail.akun_selisih.name || $('#akun_selisih option[value="' + detail.akun_selisih + '"]').text(),
                            keterangan: detail.keterangan,
                        }
                        details.push(newDetail);
                    });

                    // Refresh tabel detail
                    refreshDetailsTable();

                    // Handle post status
                    if (res.data.transaction.is_posted == 1) {
                        $(".delete-form, .btn-submit-form").hide();
                        $(".create-form input, .create-form select, .btn-add-detail").prop("disabled", true);
                    } else {
                        $(".delete-form, .btn-submit-form").show();
                        $(".create-form input, .create-form select, .btn-add-detail").prop("disabled", false);
                    }

                    // Trigger supplier list update agar dropdown di-refresh berdasarkan tipe
                    $('#tipe_supplier').trigger('change');

                } else {
                    modal.modal("hide");
                    Swal.fire("Error", res.message, "error");
                }
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                modal.modal("hide");
                Swal.fire("Error", "Gagal memuat data", "error");
            }
        });
    });

    // GET SUPPLIER BY TYPE (triggered saat tipe supplier berubah)
    $('#tipe_supplier').change(function() {
        var typeSupplier = $('#tipe_supplier option:selected').val();
        if (typeSupplier != '') {
            $.ajax({
                url: `<?= base_url('panjar-supplier/list-supplier'); ?>`,
                method: "GET",
                data: {
                    type_supplier: typeSupplier,
                },
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(res) {
                    appendDropdownSupplier(res.data); // akan handle selected ID juga
                }
            });
        }
    });

    // APPEND DATA SUPPLIER BY TYPE
    function appendDropdownSupplier(data) {
        $(".supplier_id").empty();
        $(".supplier_id").append(`<option value="">-- Pilih Supplier --</option>`);
        data.forEach(function(item) {
            const selected = (item.id == selectedSupplierId) ? 'selected' : '';
            $(".supplier_id").append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
        });
    }



    $(document).ready(function() {

        $("#akun_kas, #akun_selisih").select2({
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content"),
            placeholder: "Pilih Akun",
            allowClear: true,
            ajax: {
                url: "<?= base_url('panjar-supplier/list-akunCoa'); ?>",
                dataType: "json",
                delay: 250, // Hindari spam request
                data: function(params) {
                    return {
                        search: params.term // Kirim kata kunci pencarian
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id,
                                text: item.no_sub + ' ' + item.nama_sub
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 3
        });

         // Fungsi untuk update detail yang sedang diedit
        function updateDetail() {
            if (!validateDetails()) {
                refreshValidation();
                return;
            }

            const detail = {
                tanggal: $('#tanggal').val(),
                jenis_transaksi: $('#jenis_transaksi').val(),
                nominal_pembayaran: $('#nominal_pembayaran').val(),
                akun_kas: $('#akun_kas').val(),
                akun_selisih: $('#akun_selisih').val(),
                keterangan: $('#keterangan_detail').val()
            };

            // Update data di array details
            details[editingIndex] = detail;
            
            // Refresh tabel
            refreshDetailsTable();
            
            // Reset form dan editing state
            clearDetailForm();
            editingIndex = -1;
            $('.btn-update-detail').hide();
            $('.btn-add-detail').show();
        }   


        // Event handler untuk tombol update
        $(document).on('click', '.btn-update-detail', function(e) {
            e.preventDefault();
            updateDetail();
        });

        $('.btn-add-detail').click(function() {
            if (!validateDetails()) {
                refreshValidation();
                return;
            }

            const jenisTransaksi = $('#jenis_transaksi').val();

            const detail = {
                tanggal: $('#tanggal').val(),
                jenis_transaksi: jenisTransaksi,
                nominal_pembayaran: $('#nominal_pembayaran').val(),
                akun_kas: $('#akun_kas').val(),
                akun_kas_name: $('#akun_kas option:selected').text(),
                akun_selisih: $('#akun_selisih').val(),
                akun_selisih_name: $('#akun_selisih option:selected').text(),
                keterangan: $('#keterangan_detail').val(),
            };

            details.push(detail);
            refreshDetailsTable();
            clearDetailForm();
            refreshValidation();
        });


        // Remove detail
        $(document).on('click', '.btn-remove-detail', function() {
            const index = $(this).data('index');
            details.splice(index, 1);
            refreshDetailsTable();
            refreshValidation();
        });

        function clearDetailForm() {
            $('#tanggal, #nominal_pembayaran, #keterangan_detail').val('');
            $('#jenis_transaksi, #akun_kas, #akun_selisih').val('').trigger('change');
        }



        // Custom validation for details
        function validateDetails() {
            let isValid = true;
            const nominalRaw = $('#nominal_pembayaran').val();
            const nominalClean = nominalRaw ? destroyFormatRupiah(nominalRaw) : 0;

            // Check if fields are empty or nominal is 0
            if (
                !$('#tanggal').val() ||
                !$('#jenis_transaksi').val() ||
                !nominalClean ||
                nominalClean <= 0 ||
                !$('#akun_kas').val() ||
                !$('#akun_selisih').val()
            ) {
                isValid = false;

                // Highlight all empty detail fields
                $('#tanggal, #jenis_transaksi, #nominal_pembayaran, #akun_kas, #akun_selisih').each(function() {
                    const value = $(this).val();
                    const fieldId = $(this).attr('id');

                    if (
                        (fieldId === 'nominal_pembayaran' && (!nominalClean || nominalClean <= 0)) ||
                        (fieldId !== 'nominal_pembayaran' && !value)
                    ) {
                        $(this).closest('.form-floating').addClass('has-error');
                        $(this).addClass('is-invalid');
                    }
                });
            }

            return isValid;
        }

        // Update validation when adding/removing details
        function refreshValidation() {
            // Trigger validation on all fields

            // Special handling for detail fields
            const hasDetails = $("#detail-table tbody tr").length > 0;
            const detailFields = ['#tanggal', '#jenis_transaksi', '#nominal_pembayaran', '#akun_kas', '#akun_selisih', '#keterangan_detail'];

            detailFields.forEach(field => {
                const element = $(field);
                if (hasDetails) {
                    // If details exist, remove error styling
                    element.closest('.form-floating').removeClass('has-error');
                    element.removeClass('is-invalid');
                    element.next('span.text-danger').remove();
                } else {
                    // If no details, validate these fields
                    element.valid();
                }
            });
        }

        // Handle final submission
        $('.btn-submit-form').click(function() {
            if (details.length === 0) {
                alert('Tambahkan setidaknya satu detail pembayaran');
                return;
            }

            const formData = {
                no_transaksi: $('#no_transaksi').val(),
                jenis: $('#jenis').val(),
                tipe_supplier: $('#tipe_supplier').val(),
                supplier_id: $('#supplier_id').val(),
                keterangan: $('#keterangan').val(),
                details: details
            };

            // Submit via AJAX or form submission
            // Here you would typically make an AJAX call to submit the data
        });

        // Modal close handler
        $('#add_modal').on('hidden.bs.modal', function() {
            // Reset all form fields
            $('#auto_generate').show();

            // Clear the details table
            $('#detail-table tbody').empty();

            // Clear any hidden fields
            $('.hidden').val('');

            // Reset select2 elements
            $('.create-form .form-select').val('').trigger('change');

            // Clear details array
            details = [];
        });

        // Show form handler
        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah Data Panjar & Pinjaman");
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show");
            initSelect2();
            $(".btn-submit-form").show();
            $(".create-form input, .create-form select, .btn-add-detail").prop("disabled", false);

            selectedSupplierId = null; // Reset supplier ID saat tambah baru
        });

        // Handle submit form
        $(".btn-submit-form").click(function() {
            if ($(".create-form").valid()) {
                // Validate if there are details
                if (details.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tambahkan setidaknya satu detail transaksi',
                        confirmButtonColor: '#4e73df',
                    });
                    return;
                }

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
                        const csrf = $('meta[name="csrf-token"]').attr('content');
                        let data = new FormData();

                        // Add main form data
                        data.append('no_transaksi', $('#no_transaksi').val());
                        data.append('payment_method', $('#payment_method option:selected').val());
                        data.append('jenis', $('#jenis').val());
                        data.append('bank_id', $('#bank_id').val());
                        data.append('divisi_id', $('#divisi_id').val());
                        data.append('tipe_supplier', $('#tipe_supplier').val());
                        data.append('supplier_id', $('#supplier_id').val());
                        data.append('keterangan', $('#keterangan').val());

                        // Add details
                        details.forEach((detail, index) => {
                            data.append(`details[${index}][tanggal]`, detail.tanggal);
                            data.append(`details[${index}][jenis_transaksi]`, detail.jenis_transaksi);
                            data.append(`details[${index}][nominal_pembayaran]`, destroyFormatRupiah(detail.nominal_pembayaran));
                            data.append(`details[${index}][akun_kas]`, detail.akun_kas);
                            data.append(`details[${index}][akun_selisih]`, detail.akun_selisih);
                            data.append(`details[${index}][keterangan]`, detail.keterangan);
                        });

                        let id = $("#id").val();
                        let endpoint = id ? "panjar-supplier/update" : "panjar-supplier/save";

                        if (id) {
                            data.append('id', id);
                        }

                        $.ajax({
                            url: endpoint,
                            data: data,
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            headers: {
                                "X-CSRF-TOKEN": csrf
                            },
                            beforeSend: function() {
                                startLoading();
                            },
                            success: function(response) {
                                if (response.status) {
                                    stopLoading();
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        $(".add-modal").modal("hide");
                                        table.ajax.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                    stopLoading();
                                }
                            },
                            error: function(response) {
                                stopLoading();
                                if (response.responseJSON) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.responseJSON.message || 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Terjadi kesalahan pada server',
                                        confirmButtonColor: '#4e73df',
                                    });
                                }
                            }
                        });
                    }
                });
            }
        });

        // Helper functions
        function startLoading() {
            $('.btn-submit-form').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
        }

        function stopLoading() {
            $('.btn-submit-form').prop('disabled', false).html('Simpan Semua');
        }
    });



    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }


    // Post function
    function postPanjar(id) {
        Swal.fire({
            title: 'Post Panjar?',
            text: "Anda akan memposting transaksi ini",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Ya, Posting!'
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(id, 1);
            }
        });
    }

    // Unpost function
    function unpostPanjar(id) {
        Swal.fire({
            title: 'Unpost Panjar?',
            text: "Anda akan membatalkan posting transaksi ini",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'Ya, Unpost!'
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(id, 0);
            }
        });
    }

    // Delete function
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                remove(id);
            }
        });
    }


    const updateStatus = function(id, status) {
        Swal.fire({
            icon: 'question',
            title: status == '1' ? 'Yakin akan diposting ?' : 'Batalkan Posting ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("panjar-supplier/update-status"); ?>",
                    data: {
                        id: id,
                        status: status
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
                        console.log("Response:", response); // Debugging
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    table.ajax.reload()
                                })
                        }
                    },
                });
            }
        })

    }

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

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    $(".panjar_status").change(function() {
        table.ajax.reload();
    })

    $(".dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    $(".dataTable_info").addClass("pt-0");

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

    function displayHistory(id) {
        // console.log(id);
        $.ajax({
            url: "<?= base_url("/panjar-supplier/history-pembayaran"); ?>",
            data: {
                id: id
            },
            method: "GET",
            success: function(response) {
                console.log(response);
                $('#nomor_panjar').val(response.panjar_detail.no_panjar);
                $('#supplier_name').val(response.panjar_detail.name);
                const table = $('#tableHistori');
                var no = 1;

                table.find('tbody').empty();
                if (response.data.length > 0) {

                    $.each(response.data, function(i, v) {
                        var newRow = $('<tr>');
                        newRow.append($('<td style="text-align:center;">').text(no++));
                        newRow.append($('<td style="text-align:center;">').text(v.po_no));
                        newRow.append($('<td style="text-align:center;">').text(v.jenis_panjar));
                        newRow.append($('<td style="text-align:center;">').text(v.total_panjar));
                        newRow.append($('<td style="text-align:center;">').text(v.payment_date));
                        table.find('tbody').append(newRow);
                    });
                } else {
                    var newRow = $('<tr>');
                    newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Pembayaran</td>'));
                    table.find('tbody').append(newRow);
                }
                if (response.panjar_detail.type === 'BAHAN PENOLONG') {
                    $(".nomor").text("Nomor Tanda Terima Faktur");
                } else {
                    $(".nomor").text("Nomor PO");
                }
                $('#historiModal').modal('show');

            },
        });
    }

    function formatRupiah(angka) {
        var formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });
        var parsedNumber = parseFloat(angka);
        if (isNaN(parsedNumber)) {
            return "0,00";
        }
        return formatter.format(parsedNumber).replace('Rp', '').trim();
    }

    function convertRupiahToNumber(rupiah) {
        if (rupiah == "") {
            return 0;
        } else {
            var withoutDot = rupiah.replace(/\./g, '');
            var numberWithDot = withoutDot.replace(',', '.');
            return parseFloat(numberWithDot);
        }
    }

</script>


<?= $this->endSection(); ?>
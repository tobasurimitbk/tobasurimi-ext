<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<div class="modal choice-modal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Jenis Transaksi</h5>
            </div>
            <div class="modal-body text-center">
                <button class="btn btn-primary btn-panjar mb-2" style="width: 200px;">Panjar Supplier</button><br>
                <button class="btn btn-success btn-pinjaman" style="width: 200px;">Pinjaman Supplier</button>
            </div>
        </div>
    </div>
</div>


<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> <span id="form-title">Panjar Supplier</span></h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="form-type" value="panjar"> <!-- Tambahkan ini -->
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id">
                    <?= csrf_field() ?>
                    
                    <!-- Field-field yang sama -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control name" id="no_transaksi" name="no_transaksi" placeholder="No Transaksi">
                                    <label for="no_transaksi">No Transaksi</label>
                                </div>
                                <div style="" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 5; margin-bottom: -8px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="date" class="form-control name" id="payment_date" name="payment_date" placeholder="Tanggal">
                                <label for="payment_date">Tanggal Transaksi</label>
                            </div>
                        </div>

                        <!-- Field-field spesifik akan ditampilkan berdasarkan pilihan -->
                        <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select jenis_panjar" name="jenis_panjar" id="jenis_panjar">
                                        <option value=""></option>
                                        <option value="PANJAR">PANJAR</option>
                                        <option value="PANJAR_TB">PANJAR TB</option>
                                    </select>
                                    <label for="jenis_panjar">Jenis Panjar</label>
                                </div>
                        </div>

                        <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select tipe" name="tipe" id="tipe">
                                        <option value=""></option>    
                                        <option value="MERAH">Merah</option>
                                        <option value="PUTIH">Putih</option>
                                    </select>
                                    <label for="tipe" id="tipe-label" data-panjar="Tipe Panjar" data-pinjaman="Tipe Pinjaman">Tipe Panjar</label>
                                </div>
                        </div>

                        <!-- Field yang sama untuk kedua form -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tipe_supplier" name="tipe_supplier" id="tipe_supplier">
                                    <option value=""></option>
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

                        <div class="col-md-6">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_kas" id="akun_kas"></select>
                                <label for="akun_kas" style="z-index: 1;">Debit (Opsional)</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_selisih" id="akun_selisih"></select>
                                <label for="akun_selisih" style="z-index: 1;">Kredit</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keterangan" id="keterangan" name="keterangan">
                                <label for="keterangan">Keterangan</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="total" name="total" oninput="preventNegativeInput(this)" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="total">Total</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                <button type="button" class="btn btn-discard delete-form delete-btn">Hapus</button>
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
                            <option value="ALL">STATUS POSTING:SEMUA</option>
                            <option value="NOT_POSTING">STATUS POSTING:BELUM POSTING</option>
                            <option value="POSTING">STATUS POSTING:SUDAH POSTING</option>

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
                                <th onclick="changeSort('no_panjar')">No. Panjar</th>
                                <th onclick="changeSort('jenis_panjar')">Jenis</th>
                                <th onclick="changeSort('supplier_id')">Supplier</th>
                                <th>Akun Kas</th>
                                <th>Akun Selisih</th>
                                <th onclick="changeSort('payment_date')">Payment Date</th>
                                <th onclick="changeSort('payment_amount')">Total Panjar</th>
                                <th onclick="changeSort('payment_amt_left')">Sisa Panjar</th>
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
                data.status = $(".is_posted").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.panjar_status = $('.panjar_status option:selected').val();
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
                data: "no_panjar",
                className: "text-center",
                // width: "10%"
            },
            {
                data: "jenis_panjar",
                className: "text-center"
            },
            {
                data: "supplier",
                className: "text-center"
            },
            {
                data: "akun_kas_nama",
                className: "text-center"
            },
            {
                data: "akun_selisih_nama",
                className: "text-center"
            },
            {
                data: "payment_date",
                className: "text-center"
            },
            {
                data: "total_panjar",
                className: "text-center"
            },
            {
                data: "sisa_panjar",
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

                    if (is_posted === '0') {

                        return `
                        <div class="mt-0">
        
                            <button data-toggle="tooltip" title="Posting" onclick="updateStatus('${id}', 1)" class="btn btn-success posting-panjar-supplier">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>

                            <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>

                        <div>
                        `
                    }
                    if (is_posted === '1') {
                        return `

                            <div class="mt-0">
                                <button  data-toggle="tooltip" title="Histori LPB" onclick="displayHistory('${id}')" class="btn btn-success posting-spp">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </button>
                            <div>
                        `

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
            total: {
                required: true,
            },
            tipe: {
                required: true,
            },
            jenis_panjar: {
                required: true,
            },
            payment_date: {
                required: true
            },
            name: {
                required: true
            },
            akun_kas: {
                required: true
            },
            akun_selisih: {
                required: true
            },
            keterangan: {
                required: true
            }
        },
        messages: {
            akun_kas: {
                required: "akun kas wajib diisi",

            },
            akun_selisih: {
                required: "akun selisih wajib diisi",

            },
            total: {
                required: "total panjar wajib diisi",

            },
            tipe: {
                required: "tipe panjar wajib diisi",

            },
            jenis_panjar: {
                required: "jenis panjar wajib diisi",

            },
            payment_date: {
                required: "date have to be selected"
            },
            keterangan: {
                required: "Keterangan wajib diisi"
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
        validator.resetForm();
        validator.reset();

        // Clear input fields
        $('.add-modal input').val('');
        $('.add-modal textarea').val('');
        $('.add-modal select').val('').trigger('change');

        // Reset other cached data or state
        $('.add-modal .error-message').text('');
        $('.add-modal .preview-image').attr('src', '');

        // Hide modal
        $('.add-modal').modal('hide');
    });

    $('#tipe_supplier').select2({
        placeholder: "Pilih Tipe Supplier",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });

    $('#tipe').select2({
        placeholder: "Pilih Tipe Panjar",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });

    $('#jenis_panjar').select2({
        placeholder: "Pilih Jenis Panjar",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });


    $("#tipe_supplier, #supplier_id, #tipe, #jenis_panjar")
        .parent('div')
        .find('label')
        .css('z-index', '1');


    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        const isPinjaman = data.type === 'pinjaman';
        
        // Set form type dan title berdasarkan data
        $("#form-type").val(isPinjaman ? "pinjaman" : "panjar");
        $("#form-title").text(isPinjaman ? "Pinjaman Supplier" : "Panjar Supplier");
        
        // Tampilkan/sembunyikan field sesuai jenis
        if (isPinjaman) {
            $(".jenis_panjar").closest('.col-md-6').hide();
            $("#tipe-label").text($("#tipe-label").data('pinjaman'));
            $(".create-form").attr("action", "pinjaman-supplier/update");
        } else {
            $(".jenis_panjar").closest('.col-md-6').show();
            $("#tipe-label").text($("#tipe-label").data('panjar'));
            $(".create-form").attr("action", "panjar-supplier/update");
        }
        
        // Tampilkan modal SEBELUM AJAX
        $(".add-modal").modal("show");
        
        // AJAX untuk ambil detail
        $.ajax({
            url: (isPinjaman ? "pinjaman-supplier/id/" : "panjar-supplier/id/") + data.id,
            method: "GET",
            dataType: "json",
            success: function(res) {
                console.log(res)
                if (res.status) {
                    // Isi data umum
                    $("#id").val(res.data.id);
                    $("#no_transaksi").val(isPinjaman ? res.data.no_pinjaman : res.data.no_panjar);
                    $("#payment_date").val(res.data.payment_date);
                    $("#keterangan").val(res.data.keterangan || "");
                    $("#total").val(formatRupiah(isPinjaman ? res.data.total_pinjaman : res.data.total_panjar));
                    $("#tipe_supplier").val(res.data.type || "").change();
                    $("#tipe").val(isPinjaman ? res.data.type_pinjaman : res.data.type_panjar).change();
                    // Handle supplier
                    if (res.supplier) {
                        appendDropdownSupplier(res.supplier);
                        $("#supplier_id").val(res.data.supplier_id).trigger('change');
                    }
                    
                    // Handle dropdown khusus
                    if (!isPinjaman) {
                        $("#jenis_panjar").val(res.data.jenis_panjar || "").change();
                    }
                    
                    // Handle akun kas dan selisih
                    if (res.data.akun_kas) {
                        const akunKasText = res.data.akun_kas_name || (isPinjaman ? "Kas Pinjaman" : "Kas Panjar");
                        $("#akun_kas").append(new Option(akunKasText, res.data.akun_kas, true, true)).trigger('change');
                    }
                    
                    if (res.data.akun_selisih) {
                        const akunSelisihText = res.data.akun_selisih_name || "Akun Selisih";
                        $("#akun_selisih").append(new Option(akunSelisihText, res.data.akun_selisih, true, true)).trigger('change');
                    }
                    
                    if (res.data.no_panjar || res.data.no_pinjaman) {
                        $('#auto_generate').prop('checked', false).prop('disabled', true);
                    } else {
                        $('#auto_generate').prop('checked', true).prop('disabled', false);
                        if ($("#id").val() === '' && $('#auto_generate').is(':checked')) {
                            changeStatus();
                        }
                    }
                    
                    // Handle status posting
                    if (res.data.is_posted == 1) {
                        $(".delete-form, .btn-submit-form").hide();
                        $(".create-form input, .create-form select").prop("disabled", true);
                    } else {
                        $(".delete-form, .btn-submit-form").show();
                        $(".create-form input, .create-form select").prop("disabled", false);
                    }
                    
                    // Tampilkan modal setelah semua data terisi
                    $(".add-modal").modal("show");
                    
                } else {
                    Swal.fire("Error", res.message, "error");
                    $(".add-modal").modal("hide");
                }
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                Swal.fire("Error", "Gagal memuat data", "error");
                $(".add-modal").modal("hide");
            }
        });
    });


    // GET SUPPLIER BY TYPE
    $('#tipe_supplier').change(function() {
        var typeSupplier = $('#tipe_supplier option:selected').val();
        $.ajax({
            url: `<?= base_url('panjar-supplier/list-supplier'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_supplier: typeSupplier,
            },
            dataType: "json",
            success: function(res) {
                // APPEND TO DROPDOWN
                appendDropdownSupplier(res.data);
            }
        });
    });


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
                                text: item.nama_sub
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 3
        });


        
            // Tampilkan modal pilihan saat tombol tambah diklik
            $("#btn-display-modal").click(function() {
                $(".choice-modal").modal("show");
            });

            // Handle pilihan panjar
            $(".btn-panjar").click(function() {
                $("#form-type").val("panjar");
                $("#form-title").text("Panjar Supplier");
                $(".choice-modal").modal("hide");
                
                // Tampilkan field khusus panjar
                $(".jenis_panjar").closest('.col-md-6').show(); 
                $("#tipe-label").text($("#tipe-label").data('panjar'));
                
                // Set action form
                $(".create-form").attr("action", "panjar-supplier/save");
                
                $(".add-modal").modal("show");
            });

            // Handle pilihan pinjaman
            $(".btn-pinjaman").click(function() {
                $("#form-type").val("pinjaman");
                $("#form-title").text("Pinjaman Supplier");
                $(".choice-modal").modal("hide");
                
                // Sembunyikan seluruh kolom yang berisi field panjar
                $(".jenis_panjar").closest('.col-md-6').hide(); // Sembunyikan div parent-nya
                $("#tipe-label").text($("#tipe-label").data('pinjaman'));

                // Ubah placeholder Select2 untuk pinjaman
                $('#tipe').select2({
                    placeholder: "Pilih Tipe Pinjaman",
                    theme: "bootstrap-5",
                    dropdownParent: $(".add-modal .modal-content")
                });


                // Set action form  
                $(".create-form").attr("action", "pinjaman-supplier/save");
                
                $(".add-modal").modal("show");
            });


            $('.add-modal').on('hidden.bs.modal', function() {
                $('.create-form')[0].reset();
    
                // Reset nilai khusus
                $('#id').val('');
                $('#form-type').val('panjar'); // Kembalikan ke default
                $('#form-title').text('Panjar Supplier');
                
                // Reset field-field select
                $('.form-select').val('').trigger('change');
                
                // Reset checkbox auto generate
                $('#auto_generate').prop('checked', false);
                
                // Tampilkan semua field yang mungkin dihide
                $('.jenis_panjar').closest('.col-md-6').show();
                $('.tipe').closest('.col-md-6').show();
                
                // Reset label tipe
                $('#tipe-label').text($('#tipe-label').data('panjar'));
                
                // Hapus validasi error (jika menggunakan plugin validation)
                if ($('.create-form').validate) {
                    $('.create-form').validate().resetForm();
                }
                
                // Hapus class error dari input
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });

            // Handle submit form
            $(".btn-submit-parent").click(function() {
                if ($(".create-form").valid()) {
                    const formType = $("#form-type").val();
                    const isPanjar = formType === "panjar";
                    
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
                            const csrf = $(`[name="${csrfToken}"]`);
                            let data = new FormData(document.querySelector(".create-form"));
                            
                            // Format total sesuai jenis form
                            let total = destroyFormatRupiah($('#total').val());
                            data.set(isPanjar ? 'total_panjar' : 'total_pinjaman', total);
                            
                            // Set field no transaksi sesuai jenis form
                            let noTransaksi = $('#no_transaksi').val();
                            data.set(isPanjar ? 'no_panjar' : 'no_pinjaman', noTransaksi);
                            
                            let id = $(".id").val();
                            if (id) {
                                // Mode edit
                                endpoint = isPanjar ? "panjar-supplier/update" : "pinjaman-supplier/update";
                            } else {
                                // Mode create
                                endpoint = isPanjar ? "panjar-supplier/save" : "pinjaman-supplier/save";
                            }

                            $.ajax({
                                url: endpoint,
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
                                    csrf.val(response.responseJSON.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    });
                                    stopLoading();
                                }
                            });
                        }
                    });
                }
            });

    });


    // APPEND DATA SUPPLIER BY TYPE
    function appendDropdownSupplier(data) {
        // $(".supplier_id").empty()
        $(".supplier_id").append(`<option value=""></option>`)
        data.forEach(function(item) {
            $(".supplier_id").append(`<option value="${item.id}">${item.name}</option>`)
        })
    }

    function changeStatus() {
        const isPinjaman = $("#form-type").val() === "pinjaman";
        const checkbox = document.getElementById('auto_generate');
        const noTransaksiField = $("#no_transaksi");
        
        if (checkbox.checked) {
            // Tentukan endpoint berdasarkan jenis form
            const endpoint = isPinjaman 
                ? "<?= base_url('/pinjaman-supplier/generate-no-pinjaman'); ?>" 
                : "<?= base_url('/panjar-supplier/generate-no-panjar'); ?>";
            
            $.ajax({
                url: endpoint,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res) {
                        noTransaksiField.val(res);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message || 'Gagal generate nomor transaksi',
                            confirmButtonColor: '#4e73df',
                        });
                        checkbox.checked = false;
                        noTransaksiField.val("");
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat generate nomor',
                        confirmButtonColor: '#4e73df',
                    });
                    checkbox.checked = false;
                    noTransaksiField.val("");
                }
            });
        } else {
            noTransaksiField.val("");
        }
    }


    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
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
                console.log("CSRF Token:", csrf.val()); // Debugging
                console.log("ID:", id); // Debugging
                console.log("Status:", status); // Debugging
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
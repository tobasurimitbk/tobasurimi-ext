<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">

<!-- Modal Form -->
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
                                <input type="hidden" class="form-control hidden" name="id" id="id">

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control name" id="no_transaksi" name="no_transaksi" placeholder="No Transaksi">
                                        <label for="no_transaksi">No Transaksi</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select divisi_id" id="divisi_id" name="divisi_id">
                                            <option value=""></option>
                                            <?php foreach ($divisi as $d) : ?>
                                                <option value="<?= $d['id'] ?>"><?= $d['divisi']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="divisi_id">Departemen</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" name="bank_id" id="bank_id">
                                            <option disabled selected value=""></option>
                                            <?php foreach ($bankList as $b) : ?>
                                                <option value="<?= $b['id'] ?>"><?= strtoupper($b['kode_bank']) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <label for="bank_id">Kode Bank (Opsional)</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" name="jenis" id="jenis">
                                            <option value="PUTIH">PUTIH</option>
                                            <option value="MERAH">MERAH</option>
                                        </select>
                                        <label for="jenis">Jenis</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
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
                                    <div class="form-floating mb-3">
                                        <select class="form-select supplier_id" name="supplier_id" id="supplier_id"></select>
                                        <label for="supplier_id">Supplier</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" name="payment_method" id="payment_method">
                                            <option disabled selected value="">Pilih Metode Pembayaran</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                        </select>
                                        <label for="payment_method">Metode Pembayaran</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan">
                                        <label for="keterangan">Keterangan</label>
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
                                    <div class="form-floating mb-3">
                                        <select class="form-select" name="akun_kas" id="akun_kas"></select>
                                        <label for="akun_kas">Debit</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" name="akun_selisih" id="akun_selisih"></select>
                                        <label for="akun_selisih">Kredit</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
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
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control nominal_pembayaran" name="nominal_pembayaran" id="nominal_pembayaran" placeholder="Nominal Pembayaran">
                                        <label for="nominal_pembayaran">Nominal Pembayaran</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="form-floating mb-3">
                                            <input class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Pembayaran">
                                            <label for="tanggal">Tanggal Pembayaran</label>
                                        </div>
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control keterangan_detail" name="keterangan_detail" id="keterangan_detail" placeholder="Keterangan">
                                        <label for="keterangan_detail">Keterangan</label>
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

<!-- Main Page Content -->
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
                    <div class="input-group">
                        <input class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="input-group">
                        <input class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select panjar_status form-out-search" name="panjar_status" id="panjar_status">
                            <option value="ALL">STATUS: SEMUA</option>
                            <option value="NOT_POSTING">STATUS: BELUM POSTING</option>
                            <option value="POSTING">STATUS: SUDAH POSTING</option>
                        </select>
                        <label for="panjar_status">Status Posting</label>
                    </div>
                </div>
                <div class="col mb-4">
                    <input class="form-control search form-out-search" placeholder="Search" />
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
                        <tbody class="body-table" id="body-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- History Modal -->
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
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control nomor_panjar" name="nomor_panjar" id="nomor_panjar" readonly>
                            <label for="nomor_panjar">No Panjar</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control supplier_name" name="supplier_name" id="supplier_name" readonly>
                            <label for="supplier_name">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-inside table-borderd nowrap table-hover-tobasurimi dataTable2" style="width: 100%;" id="tableHistori">
                        <thead>
                            <tr>
                                <th style="width: 10px;text-align: center;">No</th>
                                <th style="text-align: center;" class="nomor">Nomor PO</th>
                                <th style="text-align: center;">Jenis</th>
                                <th style="text-align: center;">Total Panjar</th>
                                <th style="text-align: center;">Payment Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
    // Global Variables
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "no_panjar";
    let sortType = "desc";
    let details = [];
    let editingIndex = -1;
    let isEditMode = false;
    let initialValues = {};

    // Initialize DataTable
    const table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[1, 'asc']],
        fixedHeader: true,
        lengthMenu: [[25], [25]],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("panjar-supplier/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                return {
                    search: $(".search").val(),
                    status: $(".is_posted").val(),
                    dateStart: $(".dateStart").val(),
                    dateEnd: $(".dateEnd").val(),
                    panjar_status: $('.panjar_status').val(),
                    sort: sort,
                    sortType: sortType
                };
            }
        },
        initComplete: function() {
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [
            { data: "no", className: "text-center", sortable: false, width: "5%" },
            { data: "no_transaction", className: "text-center" },
            { data: "type", className: "text-center" },
            { data: "supplier", className: "text-center" },
            { 
                data: "nominal", 
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data) {
                    return formatRupiah(data);
                }
            },
            { data: "createdAt", className: "text-center" },
            { 
                data: "id", 
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let buttons = '<div class="btn-group" role="group">';
                    
                    if (row.is_posted == '0') {
                        buttons += `
                            <button data-toggle="tooltip" 
                                    title="Posting" 
                                    onclick="postPanjar('${row.id}')" 
                                    class="btn btn-success posting-panjar-supplier">
                                <i class="fa fa-paper-plane fa-sm"></i>
                            </button>
                            <button data-toggle="tooltip" 
                                    title="Hapus" 
                                    onclick="confirmDelete('${row.id}')" 
                                    class="btn btn-danger">
                                <i class="fa fa-trash fa-sm"></i>
                            </button>
                        `;
                    } else {
                        buttons += `
                            <button data-toggle="tooltip" 
                                    title="Unpost" 
                                    onclick="unpostPanjar('${row.id}')" 
                                    class="btn btn-warning">
                                <i class="fa fa-undo fa-sm"></i>
                            </button>
                        `;
                    }
                    
                    buttons += '</div>';
                    return buttons;
                }
            }
        ],
        columnDefs: [{ defaultContent: "-", targets: "_all" }],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // Form Validation
    var validator = $(".create-form").validate({
        rules: {
            no_transaksi: { required: true },
            jenis: { required: true },
            tipe_supplier: { required: true },
            supplier_id: { required: true },
            keterangan: { required: true }
        },
        messages: {
            no_transaksi: { required: "Nomor transaksi wajib diisi" },
            jenis: { required: "Jenis (PUTIH/MERAH) wajib diisi" },
            tipe_supplier: { required: "Tipe supplier wajib diisi" },
            supplier_id: { required: "Supplier wajib dipilih" },
            keterangan: { required: "Keterangan wajib dipilih" }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                error.insertAfter(element.next().find(".select2-selection"));
            } else if (element.hasClass("input-picker")) {
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-floating').addClass('has-error');
            $(element).addClass('is-invalid');
            
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element).next().find('.select2-selection').addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            $(element).closest('.form-floating').removeClass('has-error');
            $(element).removeClass('is-invalid');
            
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element).next().find('.select2-selection').removeClass('is-invalid');
            }
        },
        submitHandler: function(form) {
            return false; // Handled by custom submit
        }
    });

    // Initialize Select2 Elements
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

        $('#tipe_supplier').select2({
            placeholder: "Pilih Tipe Supplier",
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

        $("#akun_kas, #akun_selisih").select2({
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content"),
            placeholder: "Pilih Akun",
            allowClear: true,
            ajax: {
                url: "<?= base_url('panjar-supplier/list-akunCoa'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return { search: params.term };
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
    }

    // Initialize Datepicker
    function initDatepicker() {
        $("#tanggal").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true,
            language: 'id',
            todayBtn: "linked"
        }).on('changeDate', function() {
            $(this).valid();
        });

        $(".dateStart, .dateEnd").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });
    }

    // Handle Field Changes for Auto-generate
    function handleFieldChange(element) {
        if (!isEditMode) {
            generatePaymentNumber();
            return;
        }
        
        let currentField = $(element).attr('id');
        let currentValue = $(element).val();
        
        if (initialValues[currentField] !== currentValue) {
            generatePaymentNumber(true);
        }
    }

    // Generate Payment Number
    function generatePaymentNumber(forceGenerate = false) {
        if (isEditMode && !forceGenerate) return;
        
        let currentValues = {
            divisi_id: $("#divisi_id").val(),
            bank_id: $("#bank_id").val(),
            payment_method: $("#payment_method").val(),
            jenis: $("#jenis").val()
        };
        
        if (isEditMode && JSON.stringify(currentValues) === JSON.stringify(initialValues)) return;
        
        let jenisPembayaran = $("#jenis option:selected").text();
        let divisiId = $("#divisi_id option:selected").text();
        let paymentMethod = $("#payment_method option:selected").text();
        let bankId = $("#bank_id option:selected").val();
        
        let url = "<?= base_url('panjar-supplier/generate-no-panjar'); ?>";
        url += `?jenisPembayaran=${encodeURIComponent(jenisPembayaran)}&divisiId=${encodeURIComponent(divisiId)}&paymentMethod=${encodeURIComponent(paymentMethod)}&bankId=${encodeURIComponent(bankId)}`;
        
        $("#no_transaksi").prop("readonly", true);
        
        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
            },
            success: function(response) {
                $('meta[name="csrf-token"]').attr('content', response.token);
                $("#no_transaksi").val(response.paymentNo);
            },
            error: function() {
                $("#no_transaksi").prop("readonly", false);
                showAlert("Error", "Gagal generate nomor", "error");
            }
        });
    }

    // Get Supplier by Type
    $('#tipe_supplier').change(function() {
        var typeSupplier = $(this).val();
        if (typeSupplier) {
            $.ajax({
                url: "<?= base_url('panjar-supplier/list-supplier'); ?>",
                method: "GET",
                beforeSend: setLoading,
                complete: stopLoading,
                data: { type_supplier: typeSupplier },
                dataType: "json",
                success: function(res) {
                    appendDropdownSupplier(res.data);
                }
            });
        }
    });

    // Append Supplier to Dropdown
    function appendDropdownSupplier(data) {
        $("#supplier_id").empty().append('<option value=""></option>');
        data.forEach(function(item) {
            $("#supplier_id").append(`<option value="${item.id}">${item.name}</option>`);
        });
    }

    // Add Detail
    $('.btn-add-detail').click(function() {
        if (!validateDetails()) {
            refreshValidation();
            return;
        }

        const detail = {
            tanggal: $('#tanggal').val(),
            jenis_transaksi: $('#jenis_transaksi').val(),
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

    // Edit Detail
    $(document).on('click', '.btn-edit-detail', function(e) {
        e.preventDefault();
        const index = $(this).data('index');
        editDetail(index);
    });

    function editDetail(index) {
        const detail = details[index];
        
        $('#tanggal').val(detail.tanggal);
        $('#jenis_transaksi').val(detail.jenis_transaksi).trigger('change');
        $('#nominal_pembayaran').val(detail.nominal_pembayaran);
        $('#keterangan_detail').val(detail.keterangan);
        
        // Initialize Select2 for accounts
        if (detail.akun_kas) {
            const akunKasOption = new Option(detail.akun_kas_name, detail.akun_kas, true, true);
            $('#akun_kas').append(akunKasOption).trigger('change');
        }
        
        if (detail.akun_selisih) {
            const akunSelisihOption = new Option(detail.akun_selisih_name, detail.akun_selisih, true, true);
            $('#akun_selisih').append(akunSelisihOption).trigger('change');
        }
        
        editingIndex = index;
        $('.btn-add-detail').hide();
        $('.btn-update-detail').show();
    }

    // Update Detail
    $(document).on('click', '.btn-update-detail', function(e) {
        e.preventDefault();
        updateDetail();
    });

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
            akun_kas_name: $('#akun_kas option:selected').text(),
            akun_selisih: $('#akun_selisih').val(),
            akun_selisih_name: $('#akun_selisih option:selected').text(),
            keterangan: $('#keterangan_detail').val()
        };

        details[editingIndex] = detail;
        refreshDetailsTable();
        clearDetailForm();
        editingIndex = -1;
        $('.btn-update-detail').hide();
        $('.btn-add-detail').show();
    }

    // Remove Detail
    $(document).on('click', '.btn-remove-detail', function() {
        const index = $(this).data('index');
        details.splice(index, 1);
        refreshDetailsTable();
        refreshValidation();
    });

    // Refresh Details Table
    function refreshDetailsTable() {
        const tbody = $('#detail-table tbody');
        tbody.empty();

        details.forEach((detail, index) => {
            tbody.append(`
                <tr>
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

    // Clear Detail Form
    function clearDetailForm() {
        $('#tanggal, #nominal_pembayaran, #keterangan_detail').val('');
        $('#jenis_transaksi, #akun_kas, #akun_selisih').val('').trigger('change');
    }

    // Validate Details
    function validateDetails() {
        let isValid = true;
        const nominalRaw = $('#nominal_pembayaran').val();
        const nominalClean = nominalRaw ? destroyFormatRupiah(nominalRaw) : 0;

        if (
            !$('#tanggal').val() ||
            !$('#jenis_transaksi').val() ||
            !nominalClean ||
            nominalClean <= 0 ||
            !$('#akun_kas').val() ||
            !$('#akun_selisih').val()
        ) {
            isValid = false;

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

    // Refresh Validation
    function refreshValidation() {
        const hasDetails = $("#detail-table tbody tr").length > 0;
        const detailFields = ['#tanggal', '#jenis_transaksi', '#nominal_pembayaran', '#akun_kas', '#akun_selisih', '#keterangan_detail'];

        detailFields.forEach(field => {
            const element = $(field);
            if (hasDetails) {
                element.closest('.form-floating').removeClass('has-error');
                element.removeClass('is-invalid');
                element.next('span.text-danger').remove();
            } else {
                element.valid();
            }
        });
    }

    // Show Form Handler
    $(".btn-show-form").click(function() {
        $("#id").val("");
        $(".title-name").text("Tambah Data Panjar & Pinjaman");
        $(".delete-btn").hide();
        $(".add-modal").modal("show");
        $(".btn-submit-form").show();
        $(".create-form input, .create-form select, .btn-add-detail").prop("disabled", false);
    });

    $('.add-modal').on('shown.bs.modal', function () {
        initSelect2(); // Jalankan saat modal benar-benar sudah tampil
    });


    // Submit Form
    $(".btn-submit-form").click(function() {
        if ($(".create-form").valid()) {
            if (details.length === 0) {
                showAlert('error', 'Tambahkan setidaknya satu detail transaksi');
                return;
            }

            confirmAction(
                'Simpan Data?',
                'Simpan',
                function() {
                    const csrf = $('meta[name="csrf-token"]').attr('content');
                    let data = new FormData();

                    // Add main form data
                    data.append('no_transaksi', $('#no_transaksi').val());
                    data.append('payment_method', $('#payment_method').val());
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
                        headers: { "X-CSRF-TOKEN": csrf },
                        beforeSend: startLoading,
                        success: function(response) {
                            stopLoading();
                            if (response.status) {
                                showAlert('success', response.message, function() {
                                    $(".add-modal").modal("hide");
                                    table.ajax.reload();
                                });
                            } else {
                                showAlert('error', response.message);
                            }
                        },
                        error: function(response) {
                            stopLoading();
                            if (response.responseJSON) {
                                showAlert('error', response.responseJSON.message || 'Data Gagal Disimpan, coba Lagi');
                            } else {
                                showAlert('error', 'Terjadi kesalahan pada server');
                            }
                        }
                    });
                }
            );
        }
    });

    // Load Data for Edit
    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        const modal = $(".add-modal");
        $(".title-name").text("Edit Data Panjar & Pinjaman");

        modal.modal("show");
        $('#auto_generate').hide();

        $.ajax({
            url: "panjar-supplier/id/" + data.id,
            method: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    isEditMode = true;
                    
                    // Save initial values
                    initialValues = {
                        divisi_id: res.data.transaction.divisi_id,
                        bank_id: res.data.transaction.bank_id,
                        payment_method: res.data.transaction.payment_method,
                        jenis: res.data.transaction.type
                    };

                    // Clear existing details
                    details = [];
                    
                    // Populate parent form
                    $('#id').val(res.data.transaction.id);
                    $('#no_transaksi').val(res.data.transaction.no_transaction);
                    $('#divisi_id').val(res.data.transaction.divisi_id).trigger('change');
                    $('#payment_method').val(res.data.transaction.payment_method).trigger('change');
                    $('#bank_id').val(res.data.transaction.bank_id).trigger('change');
                    $('#jenis').val(res.data.transaction.type).trigger('change');
                    $('#tipe_supplier').val(res.data.supplier?.type || '');
                    $('#keterangan').val(res.data.transaction.keterangan || '');

                    // Populate supplier dropdown
                    if (res.data.supplier) {
                        const supplierOption = `<option value="${res.data.supplier.id}" selected>${res.data.supplier.name}</option>`;
                        $('#supplier_id').html(supplierOption).trigger('change');
                    }

                    // Process details
                    res.data.details.forEach(detail => {
                        const newDetail = {
                            id: detail.id,
                            tanggal: detail.payment_date,
                            jenis_transaksi: detail.jenis_transaksi,
                            nominal_pembayaran: formatRupiah(detail.nominal_pembayaran),
                            akun_kas: detail.akun_kas.id,
                            akun_kas_name: detail.akun_kas.name,
                            akun_selisih: detail.akun_selisih.id,
                            akun_selisih_name: detail.akun_selisih.name,
                            keterangan: detail.keterangan,
                        };
                        details.push(newDetail);
                    });

                    refreshDetailsTable();

                    // Handle posting status
                    if (res.data.transaction.is_posted == 1) {
                        $(".delete-form, .btn-submit-form").hide();
                        $(".create-form input, .create-form select, .btn-add-detail").prop("disabled", true);
                    } else {
                        $(".delete-form, .btn-submit-form").show();
                        $(".create-form input, .create-form select, .btn-add-detail").prop("disabled", false);
                    }
                } else {
                    modal.modal("hide");
                    showAlert("error", res.message);
                }
            },
            error: function() {
                modal.modal("hide");
                showAlert("error", "Gagal memuat data");
            }
        });
    });

    // Post/Unpost Functions
    function postPanjar(id) {
        confirmAction(
            'Post Panjar?',
            'Anda akan memposting transaksi ini',
            'Ya, Posting!',
            '#28a745',
            function() {
                updateStatus(id, 1);
            }
        );
    }

    function unpostPanjar(id) {
        confirmAction(
            'Unpost Panjar?',
            'Anda akan membatalkan posting transaksi ini',
            'Ya, Unpost!',
            '#ffc107',
            function() {
                updateStatus(id, 0);
            }
        );
    }

    function confirmDelete(id) {
        confirmAction(
            'Hapus Data?',
            'Data yang dihapus tidak dapat dikembalikan!',
            'Ya, Hapus!',
            '#dc3545',
            function() {
                remove(id);
            }
        );
    }

    // Update Status (Post/Unpost)
    function updateStatus(id, status) {
        confirmAction(
            status == '1' ? 'Yakin akan diposting?' : 'Batalkan Posting?',
            null,
            'Simpan',
            '#4e73df',
            function() {
                $.ajax({
                    url: "<?= base_url("panjar-supplier/update-status"); ?>",
                    data: { id: id, status: status },
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    beforeSend: setLoading,
                    complete: stopLoading,
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        $('meta[name="csrf-token"]').attr('content', response.token);
                        if (response.status) {
                            showAlert('success', response.message, function() {
                                table.ajax.reload();
                            });
                        }
                    },
                });
            }
        );
    }

    // Delete Record
    function remove(id) {
        confirmAction(
            'Yakin akan di hapus?',
            null,
            'Hapus',
            '#4e73df',
            function() {
                $.ajax({
                    url: "<?= base_url("panjar-supplier/delete"); ?>",
                    data: { id: id },
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    beforeSend: setLoading,
                    complete: stopLoading,
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        $('meta[name="csrf-token"]').attr('content', response.token);
                        if (response.status) {
                            showAlert('success', response.message, function() {
                                table.ajax.reload();
                            });
                        }
                    },
                });
            }
        );
    }

    // Display History
    function displayHistory(id) {
        $.ajax({
            url: "<?= base_url("/panjar-supplier/history-pembayaran"); ?>",
            data: { id: id },
            method: "GET",
            success: function(response) {
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
                        newRow.append($('<td style="text-align:center;">').text(formatRupiah(v.total_panjar)));
                        newRow.append($('<td style="text-align:center;">').text(v.payment_date));
                        table.find('tbody').append(newRow);
                    });
                } else {
                    var newRow = $('<tr>');
                    newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Pembayaran</td>'));
                    table.find('tbody').append(newRow);
                }
                
                $(".nomor").text(response.panjar_detail.type === 'BAHAN PENOLONG' ? 
                    "Nomor Tanda Terima Faktur" : "Nomor PO");
                
                $('#historiModal').modal('show');
            },
        });
    }

    // Helper Functions
    function confirmAction(title, text, confirmText, confirmColor, callback) {
        Swal.fire({
            title: title,
            text: text,
            icon: text ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#d33',
            confirmButtonText: confirmText || 'Ya',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }

    function showAlert(icon, title, callback) {
        Swal.fire({
            icon: icon,
            title: title,
            confirmButtonColor: '#4e73df',
        }).then(() => {
            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function setLoading() {
        $('.btn-submit-form').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
    }

    function stopLoading() {
        $('.btn-submit-form').prop('disabled', false).html('Simpan Semua');
    }

    function changeSort(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
        table.ajax.reload();
    }

    // Format Rupiah
    function formatRupiah(angka) {
        if (!angka) return "0,00";
        
        const formatter = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        return formatter.format(parseFloat(angka));
    }

    function greatFormatRupiah(angka) {
        if (!angka) return "0,00";
        
        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        });
        
        return formatter.format(parseFloat(angka)).replace('Rp', '').trim();
    }

    function destroyFormatRupiah(rupiah) {
        if (!rupiah) return 0;
        return parseFloat(rupiah.replace(/\./g, '').replace(',', '.'));
    }

    function preventNegativeInput(inputElement) {
        let inputValue = inputElement.value;
        let numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }

    // Initialize on Document Ready
    $(document).ready(function() {
        initDatepicker();

        // Event Listeners
        $(".search").keyup(function() {
            table.ajax.reload();
        });

        $(".panjar_status, .dateStart, .dateEnd").change(function() {
            table.ajax.reload();
        });

        $('.icon-dateStart').click(function() {
            $(".dateStart").focus();
        });

        $('.icon-dateEnd').click(function() {
            $(".dateEnd").focus();
        });

        // Modal Close Handler
        $('#add_modal').on('hidden.bs.modal', function() {
            isEditMode = false;
            initialValues = {};
            $('.create-form')[0].reset();
            $('.form-select').val('').trigger('change');
            $('#detail-table tbody').empty();
            details = [];
            editingIndex = -1;
            $('.btn-add-detail').show();
            $('.btn-update-detail').hide();
            $('.is-invalid').removeClass('is-invalid');
            $('.has-error').removeClass('has-error');
            $('.text-danger').remove();
            $('#auto_generate').show();
            $('#no_transaksi').val('').prop('disabled', false);
            $(".title-name").text("Tambah Data Panjar & Pinjaman");
            $(".delete-btn").hide();
            $("#id").val('');
        });
    });
</script>

<?= $this->endSection(); ?>
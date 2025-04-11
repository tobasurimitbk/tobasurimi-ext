<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
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
                            <h5>Informasi Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <input type="text" style="display: none;" class="form-control hidden" name="id" id="id">

                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control no_pembayaran" name="no_pembayaran" id="no_pembayaran" placeholder="No Pembayaran">
                                        <label for="floatingInput">No Pembayaran</label>
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
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select valas" id="valas" name="valas" aria-label="Floating label select example">
                                            <option value=""></option>
                                            <?php foreach ($dataValuta as $valuta) : ?>
                                                <option value="<?= $valuta["id"]; ?>"><?= $valuta["value"]; ?> - <?= $valuta["description"]; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Valas</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control bayar_ke" name="bayar_ke" id="bayar_ke" placeholder="Pembayaran Ke">
                                        <label for="floatingInput">Pembayaran Kepada</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Child Form (Details) -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5>Detail Pembayaran</h5>
                        </div>
                        <div class="card-body">
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
                                        <select class="form-select" name="metode_pembayaran" id="metode_pembayaran">
                                            <option selected value="">Pilih Metode Pembayaran</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Cash">Cash</option>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input onkeyup="this.value = greatFormatRupiah(this.value)" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" class="form-control nominal_pembayaran" name="nominal_pembayaran" id="nominal_pembayaran" placeholder="Nominal Pembayaran">
                                        <label for="floatingInput">Nominal Pembayaran</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control pembayaran_oleh" name="pembayaran_oleh" id="pembayaran_oleh" placeholder="Pembayaran Oleh" value="<?= session()->get("login")->name; ?>">
                                        <label for="floatingInput">Pembayaran Oleh</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="akun_kas" id="akun_kas" required>
                                            <option disabled selected value=""></option>
                                            <?php foreach ($subsAkuns as $subs) : ?>
                                                <option value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Debit</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="akun_selisih" id="akun_selisih" required>
                                            <option disabled selected value=""></option>
                                            <?php foreach ($subsAkuns as $subs) : ?>
                                                <option value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Kredit</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan" value="">
                                        <label for="floatingInput">Keterangan (Opsional)</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 text-end">
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
                                                <th>Metode</th>
                                                <th>Nominal</th>
                                                <th>Pembayaran Oleh</th>
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

<section class="section">
    <div class="section-header">
        <h1>Pembayaran Lain - Lain</h1>
        <?php if (can('Pembayaran', 'Lain - Lain', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="#">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Mulai Tanggal Pembayaran">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Selesai Tanggal Pembayaran">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <select class="form-select status_posting" name="status_posting" id="status_posting" aria-label="Floating label select example">
                        <option value="ALL">STATUS : SEMUA</option>
                        <option value="SUDAH POSTING">STATUS : SUDAH POSTING</option>
                        <option value="BELUM POSTING">STATUS : BELUM POSTING</option>
                    </select>
                </div>
                <div class="col mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('no_pembayaran')">No. Pembayaran</th>
                                <th onclick="changeSort('divisi_id')">Departemen</th>
                                <th onclick="changeSort('valas')">Valas</th>
                                <th onclick="changeSort('nominal_pembayaran')">Nominal</th>
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

<script>
    let details = [];
    let sort = "id";
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
            url: "<?= base_url("pembayaran-lain/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.status_posting = $(".status_posting").val();
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
                orderable: false,
                width: "5%"
            },
            {
                data: "no_pembayaran",
                className: "text-center"
            },
            {
                data: "divisi",
                className: "text-center"
            },
            {
                data: "valas",
                className: "text-center"
            },
            {
                data: "nominal",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let form = '';
                    let status_posting = row.status_posting;

                    form += ` <div class="mt-0">`;
                    if (status_posting == '0') {
                        <?php if (can('Pembayaran', 'Lain - Lain', 'd')) : ?>
                            form += `
                            <button onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>                    
                        `;
                        <?php endif; ?>
                        form += `
                        <?php if (can('Pembayaran', 'Lain - Lain', 'a')) : ?>
                            <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                    `;
                    } else {
                        form += '-';
                    }
                    form += ` </div>`;
                    return form;
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

    $(document).ready(function() {
        var validator = $(".create-form").validate({
            rules: {
                // Parent section rules
                no_pembayaran: {
                    required: true
                },
                divisi_id: {
                    required: true
                },
                bayar_ke: {
                    required: true
                },
                valas: {
                    required: true
                },
                
                // Child/detail section rules
                tanggal: {
                    required: function() {
                        return $("#detail-table tbody tr").length === 0;
                    }
                },
                metode_pembayaran: {
                    required: function() {
                        return $("#detail-table tbody tr").length === 0;
                    }
                },
                nominal_pembayaran: {
                    required: function() {
                        return $("#detail-table tbody tr").length === 0;
                    },
                    min: 1
                },
                pembayaran_oleh: {
                    required: function() {
                        return $("#detail-table tbody tr").length === 0;
                    }
                },
                akun_kas: {
                    required: function() {
                        return $("#detail-table tbody tr").length === 0;
                    }
                },
                akun_selisih: {
                    required: function() {
                        return $("#detail-table tbody tr").length === 0;
                    }
                }
            },
            messages: {
                no_pembayaran: {
                    required: "No pembayaran wajib diisi"
                },
                divisi_id: {
                    required: "Departemen wajib diisi"
                },
                bayar_ke: {
                    required: "Pembayaran kepada wajib diisi"
                },
                valas: {
                    required: "Mata uang wajib diisi"
                },
                tanggal: {
                    required: "Tanggal pembayaran wajib diisi"
                },
                metode_pembayaran: {
                    required: "Metode pembayaran wajib diisi"
                },
                nominal_pembayaran: {
                    required: "Nominal pembayaran wajib diisi",
                    min: "Nominal harus lebih dari 0"
                },
                pembayaran_oleh: {
                    required: "Pembayaran oleh wajib diisi"
                },
                akun_kas: {
                    required: "Akun debit wajib diisi"
                },
                akun_selisih: {
                    required: "Akun kredit wajib diisi"
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
                $(element).closest('.form-floating').addClass('has-error');
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).closest('.form-floating').removeClass('has-error');
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                // Custom validation for at least one detail
                if ($("#detail-table tbody tr").length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tambahkan setidaknya satu detail pembayaran',
                        confirmButtonColor: '#4e73df',
                    });
                    return false;
                }
                return true;
            }
        });


        // Initialize Select2 for dropdowns
        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
            dropdownParent: $('#add_modal .modal-content') // Updated to match modal structure
        });

        $('#valas').select2({
            placeholder: "Pilih Mata Uang",
            theme: "bootstrap-5",
            dropdownParent: $('#add_modal .modal-content')
        });

        $('#metode_pembayaran').select2({
            placeholder: "Pilih Metode Pembayaran",
            theme: "bootstrap-5",
            dropdownParent: $('#add_modal .modal-content')
        });

        $('#akun_kas').select2({
            placeholder: "Pilih Debit",
            theme: "bootstrap-5",
            dropdownParent: $('#add_modal .modal-content')
        });

        $('#akun_selisih').select2({
            placeholder: "Pilih Kredit",
            theme: "bootstrap-5",
            dropdownParent: $('#add_modal .modal-content')
        });

        $("#tanggal").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true,
            language: 'id', // opsional untuk bahasa Indonesia
            todayBtn: "linked"
        }).on('changeDate', function(e) {
            $(this).valid(); // Trigger validasi saat tanggal berubah
        });

        // When adding a detail
        $('.btn-add-detail').click(function() {
            // ... your existing code ...
            refreshValidation();
        });

        // When removing a detail
        $(document).on('click', '.btn-remove-detail', function() {
            // ... your existing code ...
            refreshValidation();
        });
        
        function refreshDetailsTable() {
            const tbody = $('#detail-table tbody');
            tbody.empty();
            
            details.forEach((detail, index) => {
                tbody.append(`
                    <tr>
                        <td>${detail.tanggal}</td>
                        <td>${detail.metode_pembayaran}</td>
                        <td>${detail.nominal_pembayaran}</td>
                        <td>${detail.pembayaran_oleh}</td>
                        <td>${detail.keterangan}</td>
                        <td>
                            <button class="btn btn-danger btn-sm btn-remove-detail" data-index="${index}">Hapus</button>
                        </td>
                    </tr>
                `);
            });
        }
        
        // Remove detail
        $(document).on('click', '.btn-remove-detail', function() {
            const index = $(this).data('index');
            details.splice(index, 1);
            refreshDetailsTable();
        });
        
        // Clear detail form
        function clearDetailForm() {
            $('#tanggal, #nominal_pembayaran, #keterangan').val('');
            $('#metode_pembayaran, #akun_kas, #akun_selisih').val('').trigger('change');
        }
        
        // Handle final submission
        $('.btn-submit-form').click(function() {
            if (details.length === 0) {
                alert('Tambahkan setidaknya satu detail pembayaran');
                return;
            }
            
            const formData = {
                no_pembayaran: $('#no_pembayaran').val(),
                divisi_id: $('#divisi_id').val(),
                valas: $('#valas').val(),
                bayar_ke: $('#bayar_ke').val(),
                details: details
            };
            
            // Submit via AJAX or form submission
            // ...
        });

        // Custom validation for details
        function validateDetails() {
            let isValid = true;
            
            // Check if at least one detail exists
            if ($("#detail-table tbody tr").length === 0) {
                isValid = false;
                // Highlight all detail fields
                $('#tanggal, #metode_pembayaran, #nominal_pembayaran, #pembayaran_oleh, #akun_kas, #akun_selisih').each(function() {
                    $(this).closest('.form-floating').addClass('has-error');
                    $(this).addClass('is-invalid');
                });
            }
            
            return isValid;
        }

        // Update validation when adding/removing details
        function refreshValidation() {
            // Trigger validation on all fields
            $(".create-form").validate().form();
            
            // Special handling for detail fields
            const hasDetails = $("#detail-table tbody tr").length > 0;
            const detailFields = ['#tanggal', '#metode_pembayaran', '#nominal_pembayaran', '#pembayaran_oleh', '#akun_kas', '#akun_selisih'];
            
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


        

        $('#add_modal').on('hidden.bs.modal', function () {
            // Reset all form fields
            $('.create-form')[0].reset();
            
            // Clear the details table
            $('#detail-table tbody').empty();
            
            // Clear any hidden fields or special inputs
            $('.hidden').val('');
            
            // Reset any select2 elements if you're using them
            $('.form-select').val('').trigger('change');

            details = [];
        });

        
        $('.btn-add-detail').click(function () {
            // Jalankan validasi detail dulu
            if (!validateDetails()) {
                refreshValidation(); // buat update styling error
                return; // stop proses kalau gak valid
            }

            const detail = {
                tanggal: $('#tanggal').val(),
                metode_pembayaran: $('#metode_pembayaran').val(),
                nominal_pembayaran: $('#nominal_pembayaran').val(),
                pembayaran_oleh: $('#pembayaran_oleh').val(),
                akun_kas: $('#akun_kas').val(),
                akun_selisih: $('#akun_selisih').val(),
                keterangan: $('#keterangan').val()
            };

            details.push(detail);
            refreshDetailsTable();
            clearDetailForm();
            refreshValidation(); // bersihin styling error kalau sudah valid
        });

        
        // Refresh details table
        function refreshDetailsTable() {
            const tbody = $('#detail-table tbody');
            tbody.empty();
            
            details.forEach((detail, index) => {
                tbody.append(`
                    <tr>
                        <td>${detail.tanggal}</td>
                        <td>${detail.metode_pembayaran}</td>
                        <td>${detail.nominal_pembayaran}</td>
                        <td>${detail.pembayaran_oleh}</td>
                        <td>${detail.keterangan}</td>
                        <td>
                            <button class="btn btn-danger btn-sm btn-remove-detail" data-index="${index}">Hapus</button>
                        </td>
                    </tr>
                `);
            });
        }
        
        // Remove detail
        $(document).on('click', '.btn-remove-detail', function() {
            const index = $(this).data('index');
            details.splice(index, 1);
            refreshDetailsTable();
        });
        
        // Clear detail form
        function clearDetailForm() {
            $('#tanggal, #nominal_pembayaran, #keterangan').val('');
            $('#metode_pembayaran, #akun_kas, #akun_selisih').val('').trigger('change');
        }
        
        // Handle final submission
        $('.btn-submit-form').click(function() {
            if (details.length === 0) {
                alert('Tambahkan setidaknya satu detail pembayaran');
                return;
            }
            
            const formData = {
                no_pembayaran: $('#no_pembayaran').val(),
                divisi_id: $('#divisi_id').val(),
                valas: $('#valas').val(),
                bayar_ke: $('#bayar_ke').val(),
                details: details
            };
            
            // Submit via AJAX or form submission
            // ...
        });

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah Pembayaran Lain");
            validator.resetForm();
            validator.reset();
            resetForm();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd").change(function() {
            table.ajax.reload();
        })

        $(".status_posting").change(function() {
            table.ajax.reload();
        });

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update Pembayaran Lain");

            validator.resetForm();
            validator.reset();

            $.ajax({
                url: "<?= base_url("pembayaran-lain/get"); ?>",
                data: { id: id },
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        // Reset form and clear details
                        resetForm();
                        details = [];
                        
                        // Set parent data
                        const parent = res.data.parent;
                        $('#id').val(parent.id);
                        $("#no_pembayaran").val(parent.no_pembayaran);
                        $('#divisi_id').val(parent.divisi_id).change();
                        $('#bayar_ke').val(parent.bayar_ke);
                        $('#valas').val(parent.valas).change();
                        
                        // Disable fields if needed
                        if (parent.status_posting === "1") {
                            disabledForm();
                        } else {
                            $("#no_pembayaran").attr('disabled', true);
                        }
                        
                        // Set details data
                        if (res.data.details && res.data.details.length > 0) {
                            res.data.details.forEach(detail => {
                                details.push({
                                    id: detail.id,
                                    tanggal: detail.tanggal,
                                    metode_pembayaran: detail.metode_pembayaran,
                                    nominal_pembayaran: detail.nominal_pembayaran,
                                    pembayaran_oleh: detail.pembayaran_oleh,
                                    akun_kas: detail.akun_kas,
                                    akun_selisih: detail.akun_selisih,
                                    keterangan: detail.keterangan
                                });
                            });
                            refreshDetailsTable();
                        }
                        
                        $(".add-modal").modal("show");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to load data',
                        confirmButtonColor: '#4e73df',
                    });
                }
            });
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
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $('meta[name="csrf-token"]').attr('content');
                        let id = $('#id').val();
                        
                        // Prepare data with proper formatting
                        const requestData = {
                            id: id,
                            divisi_id: $('#divisi_id').val(),
                            no_pembayaran: $('#no_pembayaran').val(),
                            bayar_ke: $('#bayar_ke').val(),
                            valas: $('#valas').val(),
                            details: details.map(detail => ({
                                id: detail.id || '',
                                tanggal: detail.tanggal,
                                metode_pembayaran: detail.metode_pembayaran,
                                nominal_pembayaran: detail.nominal_pembayaran.toString().replace(/\./g, ''),
                                pembayaran_oleh: detail.pembayaran_oleh,
                                akun_kas: detail.akun_kas,
                                akun_selisih: detail.akun_selisih,
                                keterangan: detail.keterangan || ''
                            }))
                        };

                        // Determine URL based on whether it's an update or create
                        let url = id ? "<?= base_url('pembayaran-lain/update'); ?>" : "<?= base_url('pembayaran-lain/save'); ?>";
                        let method = id ? "POST" : "POST";
                        
                        $.ajax({
                            url: url,
                            data: JSON.stringify(requestData),
                            contentType: "application/json", 
                            headers: {
                                "X-CSRF-TOKEN": csrf
                            },
                            beforeSend: function() {
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                            },
                            method: method,
                            dataType: "json",
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        table.ajax.reload();
                                        $(".add-modal").modal("hide");
                                        resetForm();
                                        details = []; // Clear details array
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                let errorMessage = 'Silakan coba lagi';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.statusText) {
                                    errorMessage = xhr.statusText;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan',
                                    text: errorMessage,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                        });
                    }
                });
            }
        });
        
    });

    const remove = function(id) {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        Swal.fire({
            icon: 'question',
            title: 'Hapus Pembayaran Ini ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    url: "<?= base_url("pembayaran-lain/delete"); ?>",
                    data: formData,
                    method: "POST",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                            }).then((result) => {
                                // update table
                                table.ajax.reload();
                            });
                        }
                    },
                });
            }
        });
    }


    function disabledForm() {
        $("#no_pembayaran").attr('disabled', true);
        $('#tanggal').attr('disabled', true);
        $('#divisi_id').attr('disabled', true);
        $('#bayar_ke').attr('disabled', true);
        $('#valas').attr('disabled', true);
        $('#metode_pembayaran').attr('disabled', true);
        $('#nominal_pembayaran').attr('disabled', true);
        $('#pembayaran_oleh').attr('disabled', true);
        $('#akun_kas').attr('disabled', true);
        $('#akun_selisih').attr('disabled', true);
        $('#keterangan').attr('disabled', true);

        $('.btn-submit-form').hide();
    }

    function resetForm() {
        $("#no_pembayaran").attr('disabled', false);
        $('#tanggal').attr('disabled', false);
        $('#divisi_id').attr('disabled', false);
        $('#bayar_ke').attr('disabled', false);
        $('#valas').attr('disabled', false);
        $('#metode_pembayaran').attr('disabled', false);
        $('#nominal_pembayaran').attr('disabled', false);
        $('#pembayaran_oleh').attr('disabled', false);
        $('#akun_kas').attr('disabled', false);
        $('#akun_selisih').attr('disabled', false);
        $('#keterangan').attr('disabled', false);

        $("#id").val(null).change();
        $("#no_pembayaran").val(null).change();
        $('#tanggal').val(null).change();
        $('#divisi_id').val(null).change();
        $('#bayar_ke').val(null).change();
        $('#valas').val(null).change();
        $('#metode_pembayaran').val(null).change();
        $('#nominal_pembayaran').val(null).change();
        $('#pembayaran_oleh').val(null).change();
        $('#akun_kas').val(null).change();
        $('#akun_selisih').val(null).change();
        $('#keterangan').val(null).change();

        $('.btn-submit-form').show();
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Pembayaran ?',
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
                    url: "<?= base_url("pembayaran-lain/posting"); ?>",
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

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
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
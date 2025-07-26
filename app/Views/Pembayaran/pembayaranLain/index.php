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
                                    <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                        <select class="form-select" name="bank_id" id="bank_id">
                                                <option selected disabled value="">Pilih Bank</option>
                                            <?php foreach ($bankList as $b) : ?>
                                                <option value="<?= $b['id'] ?>"><?= strtoupper($b['kode_bank']) ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <label for="bank_id" style="z-index: 1;">Kode Bank (Opsional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="jenis_pembayaran" id="jenis_pembayaran" required>
                                            <option selected disabled value="">Pilih Bank</option>
                                            <option value="PUTIH">PUTIH</option>
                                            <option value="MERAH">MERAH</option>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Jenis Pembayaran</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
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
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control bayar_ke" name="bayar_ke" id="bayar_ke" placeholder="Pembayaran Ke">
                                        <label for="floatingInput">Pembayaran Kepada</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control total_all_amount" name="total_all_amount" id="total_all_amount" placeholder="Total Keseluruhan" readonly>
                                        <label for="floatingInput">Total Keseluruhan</label>
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
                                        <textarea autocomplete="one-time-code" style="height: 88px;" type="text" class="form-control keterangan_parent" name="keterangan_parent" id="keterangan_parent" placeholder="Keterangan"></textarea>
                                        <label for="floatingInput">Keterangan</label>
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
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control pembayaran_oleh" name="pembayaran_oleh" id="pembayaran_oleh" placeholder="Pembayaran Oleh" value="<?= session()->get("login")->name; ?>">
                                        <label for="floatingInput">Pembayaran Oleh</label>
                                    </div>
                                </div>
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
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan" value="">
                                        <label for="floatingInput">Keterangan</label>
                                    </div>
                                </div>
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
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control jumlah" onkeyup="this.value = greatFormatRupiah(this.value)" id="jumlah" name="jumlah" placeholder="Jumlah Transaksi">
                                        <label for="floatingInput">Jumlah Transaksi</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" value="1" class="form-control kurs" onkeyup="this.value = greatFormatRupiah(this.value)" id="kurs" name="kurs" placeholder="Kurs">
                                        <label for="floatingInput">Kurs</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" onkeyup="this.value = greatFormatRupiah(this.value)" class="form-control jumlah_idr" id="jumlah_idr" name="jumlah_idr" placeholder="Jumlah IDR">
                                            <label for="floatingInput">Nominal Pembayaran IDR</label>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <button type="button" class="btn btn-success btn-update-detail" style="display:none;">Update</button>
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
                                                <th>Akun Kredit</th>
                                                <th>Akun Debit</th>
                                                <th>Valas</th>
                                                <th>Jumlah Transaksi</th>
                                                <th>Kurs</th>
                                                <th>Nominal IDR</th>
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
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Mulai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Selesai Tanggal">
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
                                <th onclick="changeSort('bayar_ke')">Uraian</th>
                                <th>Nominal</th>
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
    let editingIndex = -1;
    let details = [];
    let sort = "id";
    let sortType = "desc";
    let isEditMode = false;
    let initialValues = {};
    let select2Initialized = false;

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
                data: "bayar_ke",
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
                    let status_posting = row.status_posting;
                    let buttons = '';
                    
                    buttons += `<div class="btn-group" role="group">`;
                    
                    if (status_posting == '0') {
                        // Unposted state - show delete and post buttons
                        <?php if (can('Pembayaran', 'Lain - Lain', 'd')) : ?>
                            buttons += `
                                <button onclick="remove('${id}')" 
                                        class="btn btn-danger"
                                        data-toggle="tooltip" title="Hapus">
                                    <i class="fa fa-trash fa-sm"></i>
                                </button>`;
                        <?php endif; ?>
                        
                        <?php if (can('Pembayaran', 'Lain - Lain', 'a')) : ?>
                            buttons += `
                                <button onclick="posting('${id}', '1')" 
                                        class="btn btn-success"
                                        data-toggle="tooltip" title="Posting">
                                    <i class="fa fa-paper-plane fa-sm"></i>
                                </button>`;
                        <?php endif; ?>
                    } else {
                        // Posted state - show unpost button
                        <?php if (can('Pembayaran', 'Lain - Lain', 'a')) : ?>
                            buttons += `
                                <button onclick="posting('${id}', '0')" 
                                        class="btn btn-warning"
                                        data-toggle="tooltip" title="Unpost">
                                    <i class="fa fa-undo fa-sm"></i>
                                </button>`;
                        <?php endif; ?>
                    }
                    
                    buttons += `</div>`;
                    return buttons || '-'; // Return '-' if no buttons are shown
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
    $(".dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
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
                metode_pembayaran: {
                    required: true
                },
                bayar_ke: {
                    required: true
                },
                akun_selisih: {
                    required: true
                },
                total_all_amount: {
                    required: true
                },

                // Child/detail section rules
                tanggal: {
                    required: function() {
                        return $("#detail-table tbody tr").length === 0;
                    }
                },
                jumlah_idr: {
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
            },
            messages: {
                no_pembayaran: {
                    required: "No pembayaran wajib diisi"
                },
                divisi_id: {
                    required: "Departemen wajib diisi"
                },
                total_all_amount: {
                    required: "Total wajib Terisi"
                },
                bayar_ke: {
                    required: "Pembayaran kepada wajib diisi"
                },
                tanggal: {
                    required: "Tanggal pembayaran wajib diisi"
                },
                metode_pembayaran: {
                    required: "Metode pembayaran wajib diisi"
                },
                jumlah_idr: {
                    required: "Nominal pembayaran wajib diisi",
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
        
        updateAccountLabels($('#jenis_pembayaran').val());

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
            // Jalankan validasi detail dulu
            const akunSelisih = $('#akun_selisih option:selected').val();
            const jenisPembayaran = $('#jenis_pembayaran option:selected').val();
            let messageErr = '';
            if (akunSelisih == '') {
                if (jenisPembayaran == 'PUTIH') {
                    messageErr = "Akun Kredit Wajib Diisi"
                } else {
                    messageErr = "Akun Debit Wajib Diisi";
                }
                alert(messageErr)
                return;
            }

            if (!validateDetails()) {
                refreshValidation(); // buat update styling error
                return; // stop proses kalau gak valid
            }

            const detail = {
                tanggal: $('#tanggal').val(),
                pembayaran_oleh: $('#pembayaran_oleh').val(),
                akun_kas: $('#akun_kas').val(),
                akun_selisih: $('#akun_selisih').val(),
                akun_kas_name: $('#akun_kas option:selected').text(),
                akun_selisih_name: $('#akun_selisih option:selected').text(),
                keterangan: $('#keterangan').val(),
                valas: $('#valas option:selected').text(),
                valas_id: $('#valas option:selected').val(),
                jumlah: $('#jumlah').val(),
                kurs: $('#kurs').val(),
                jumlah_idr: $('#jumlah_idr').val(),
                jenis_pembayaran: jenisPembayaran
            };

            details.push(detail);
            refreshDetailsTable();
            clearDetailForm();
            refreshValidation(); // bersihin styling error kalau sudah valid
            
        });

        $('#jumlah').keyup(function() {
            var jumlah = destroyFormatRupiah($('#jumlah').val() || 0);
            var kurs = destroyFormatRupiah($('#kurs').val() || 1);
            var jumlahIdr = destroyFormatRupiah($('#jumlah_idr').val() || 0);

            $('#jumlah_idr').val(greatFormatRupiah(jumlah * kurs));
        });

        $('#kurs').keyup(function() {
            var jumlah = destroyFormatRupiah($('#jumlah').val() || 0);
            var kurs = destroyFormatRupiah($('#kurs').val() || 1);
            var jumlahIdr = destroyFormatRupiah($('#jumlah_idr').val() || 0);

            $('#jumlah').val(greatFormatRupiah(jumlahIdr / kurs));
            $('#jumlah_idr').val(greatFormatRupiah(jumlah * kurs));

        });

        $('#jumlah_idr').keyup(function() {
            var jumlah = destroyFormatRupiah($('#jumlah').val() || 0);
            var kurs = destroyFormatRupiah($('#kurs').val() || 1);
            var jumlahIdr = destroyFormatRupiah($('#jumlah_idr').val() || 0);

            $('#jumlah').val(greatFormatRupiah(jumlahIdr / kurs));
        });

        function initSelect2(silent = false) {
            const commonOptions = {
                theme: "bootstrap-5",
                dropdownParent: $('#add_modal .modal-content'),
                minimumResultsForSearch: 10,
                allowClear: true, // Tambahkan opsi ini
            };

            // Daftar field select2
            const select2Fields = [
                { id: '#jenis', placeholder: "Pilih Jenis" },
                { id: '#divisi_id', placeholder: "Pilih Departemen" },
                { id: '#bank_id', placeholder: "Pilih Bank" },
                { id: '#jenis_pembayaran', placeholder: "Pilih Jenis Pembayaran" },
                { id: '#payment_method', placeholder: "Pilih Metode Pembayaran" },
                { id: '#valas', placeholder: "Pilih Mata Uang" },
                { id: '#akun_kas', placeholder: "Pilih Debit" },
                { id: '#akun_selisih', placeholder: "Pilih Kredit" }
            ];

            // Hapus semua event handler terkait
            $('#jenis, #divisi_id, #bank_id, #jenis_pembayaran, #payment_method, #valas').off('.select2-handlers');

            // Inisialisasi semua field Select2
            select2Fields.forEach(field => {
                if ($(field.id).length) {
                    $(field.id).select2({
                        ...commonOptions,
                        placeholder: field.placeholder
                    }).data('select2').$container.addClass('select2-custom-style');
                }
            });

            // Handler perubahan field (dengan debounce) hanya jika tidak silent
            if (!silent) {
                let changeTimeout;

                $('#jenis, #divisi_id, #bank_id, #jenis_pembayaran, #payment_method').on('change.select2-handlers', function() {
                    clearTimeout(changeTimeout);
                    const changedField = this;
                    const changedFieldId = $(changedField).attr('id'); // dapatkan ID field yang berubah

                    changeTimeout = setTimeout(() => {
                        if ($('#add_modal').is(':visible')) {
                            // Jika yang berubah adalah field jenis_pembayaran
                            if (changedFieldId === 'jenis_pembayaran') {
                                const jenisPembayaran = $(changedField).val();
                                updateAccountLabels(jenisPembayaran); // update label akun
                            }
                            // Jika yang berubah adalah field jenis
                            else if (changedFieldId === 'jenis') {
                                details = []; // reset details
                            }
                            
                            // Selalu panggil handleFieldChange
                            handleFieldChange(changedField);
                        }
                    }, 300); // debounce 300ms
                });

                // Handler khusus untuk valas
                $('#valas').on('change.select2-handlers', function() {
                    const id = $(this).val();
                    if (id && id != 30) {
                        getNilaiKurs(id);
                    } else {
                        $('#kurs').val(1);
                    }
                });
            }

            // Destroy select2 saat modal ditutup
            $('#add_modal').off('hidden.bs.modal.select2-cleanup').on('hidden.bs.modal.select2-cleanup', function () {
                select2Fields.forEach(field => {
                    if ($(field.id).data('select2')) {
                        $(field.id).select2('destroy');
                    }
                });
            });
        }

        function getNilaiKurs(id) {
            $.ajax({
                url: "/kurs/getNilaiKurs/" + id,
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
                        if (res.data && res.data.nilai_kurs) {
                            $('#kurs').val(greatFormatRupiah(res.data.nilai_kurs));
                        }
                    } else {
                        // Swal.fire({
                        //     icon: 'error',
                        //     title: res.message,
                        //     confirmButtonColor: '#4e73df',
                        // });
                        $('#kurs').val(greatFormatRupiah(1));
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
        }

        // When removing a detail
        $(document).on('click', '.btn-remove-detail', function() {
            // ... your existing code ...
            refreshValidation();
        });

        function updateDetail(index) {
            if (!validateDetails()) return;

            const jenisPembayaran = $('#jenis_pembayaran').val();
            
            // Update detail
            details[index] = {
                ...details[index],
                tanggal: $('#tanggal').val(),
                pembayaran_oleh: $('#pembayaran_oleh').val(),
                akun_kas: $('#akun_kas').val(),
                akun_kas_name: $('#akun_kas option:selected').text(),
                akun_selisih: $('#akun_selisih').val(),
                akun_selisih_name: $('#akun_selisih option:selected').text(),
                keterangan: $('#keterangan').val(),
                valas: $('#valas option:selected').text(),
                valas_id: $('#valas option:selected').val(),
                jumlah: $('#jumlah').val(),
                kurs: $('#kurs').val(),
                jumlah_idr: $('#jumlah_idr').val(),
                jenis_pembayaran: jenisPembayaran
            };

            refreshDetailsTable();
            clearDetailForm();
            $('.btn-update-detail').hide();
            $('.btn-add-detail').show();
        }
        
        $('.btn-update-detail').hide();

        // Event handler untuk tombol edit
        $(document).on('click', '.btn-edit-detail', function(e) {
            e.preventDefault(); // Ini yang paling penting
            e.stopPropagation();
            const index = $(this).data('index');
            editDetail(index);
        });

        // Event handler untuk tombol update
        $(document).on('click', '.btn-update-detail', function(e) {
            e.preventDefault(); // Ini yang paling penting
            e.stopPropagation();
            const index = $(this).data('index');
            updateDetail(index);
        });
        // Remove detail
        $(document).on('click', '.btn-remove-detail', function() {
            const index = $(this).data('index');
            details.splice(index, 1);
            refreshDetailsTable();
        });

        // Function to update labels based on payment type
        function updateAccountLabels(jenis) {
            if (jenis === 'PUTIH') {
                // Update label
                $('#akun_kas').parent().find('label').text('Debit');
                $('#akun_selisih').parent().find('label').text('Kredit');

                // Update placeholder Select2
                $('#akun_kas').select2({
                    placeholder: "Pilih Akun Debit",
                    theme: "bootstrap-5",
                    dropdownParent: $('#add_modal .modal-content')
                });

                $('#akun_selisih').select2({
                    placeholder: "Pilih Akun Kredit",
                    theme: "bootstrap-5",
                    dropdownParent: $('#add_modal .modal-content')
                });

            } else if (jenis === 'MERAH') {
                // Update label
                $('#akun_kas').parent().find('label').text('Kredit');
                $('#akun_selisih').parent().find('label').text('Debit');

                // Update placeholder Select2
                $('#akun_kas').select2({
                    placeholder: "Pilih Akun Kredit",
                    theme: "bootstrap-5",
                    dropdownParent: $('#add_modal .modal-content')
                });

                $('#akun_selisih').select2({
                    placeholder: "Pilih Akun Debit",
                    theme: "bootstrap-5",
                    dropdownParent: $('#add_modal .modal-content')
                });
            }

            refreshDetailsTable();
        }

        // Custom validation for details
        function validateDetails() {
            let isValid = true;
            const nominalRaw = $('#jumlah_idr').val();
            const nominalClean = destroyFormatRupiah(nominalRaw);

            // Cek apakah field kosong atau nominal 0
            if (
                !$('#tanggal').val() ||
                !nominalClean || // bisa tambah pengecekan nominal < 1 kalau mau
                !$('#pembayaran_oleh').val() ||
                !$('#akun_kas').val()
            ) {
                isValid = false;

                // Highlight all detail fields (khususnya yang kosong)
                $('#tanggal, #pembayaran_oleh, #akun_kas').each(function() {
                    const value = $(this).val();
                    const fieldId = $(this).attr('id');

                    // Untuk nominal, kita pakai hasil clean
                    if (
                        (fieldId === 'jumlah_idr' && !nominalClean) ||
                        (fieldId !== 'jumlah_idr' && !value)
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
            $(".create-form").validate().form();

            // Special handling for detail fields
            const hasDetails = $("#detail-table tbody tr").length > 0;
            const detailFields = ['#tanggal',
                '#pembayaran_oleh',
                '#valas',
                '#kurs',
                '#jumlah',
                '#jumlah_idr',
                '#akun_kas'
            ];

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

        $('#add_modal').on('hidden.bs.modal', function() {
            // Reset all form fields
            $('.create-form')[0].reset();

            // Clear the details table
            $('#detail-table tbody').empty();

            // Clear any hidden fields or special inputs
            $('.hidden').val('');

            // Reset any select2 elements if you're using them
            resetForm();
            details = [];
        });


        $('.btn-add-detail').click(function() {
            // Validasi
            const akunSelisih = $('#akun_selisih option:selected').val();
            const jenisPembayaran = $('#jenis_pembayaran option:selected').val();
            
            if (akunSelisih == '') {
                const messageErr = jenisPembayaran == 'PUTIH' 
                    ? "Akun Kredit Wajib Diisi" 
                    : "Akun Debit Wajib Diisi";
                alert(messageErr);
                return;
            }

            if (!validateDetails()) {
                refreshValidation();
                return;
            }

            // Prepare detail data - KONSISTEN dengan struktur BE
            const detail = {
                tanggal: $('#tanggal').val(),
                pembayaran_oleh: $('#pembayaran_oleh').val(),
                keterangan: $('#keterangan').val(),
                valas: $('#valas option:selected').text(),
                valas_id: $('#valas option:selected').val(),
                jumlah: $('#jumlah').val(),
                kurs: $('#kurs').val(),
                jumlah_idr: $('#jumlah_idr').val(),
                jenis_pembayaran: jenisPembayaran,
                
                // SELALU simpan akun_kas sebagai debit (sesuai pilihan user)
                akun_kas: $('#akun_kas').val(),
                akun_kas_name: $('#akun_kas option:selected').text(),
                
                // Untuk akun_selisih, sesuaikan dengan jenis pembayaran
                akun_selisih: jenisPembayaran === 'PUTIH' 
                    ? $('#akun_selisih').val()  // PUTIH: ambil dari parent
                    : $('#akun_kas').val(),     // MERAH: sama dengan akun_kas
                akun_selisih_name: jenisPembayaran === 'PUTIH'
                    ? $('#akun_selisih option:selected').text()
                    : $('#akun_kas option:selected').text()
            };

            details.push(detail);
            refreshDetailsTable();
            clearDetailForm();
            refreshValidation();
        });

        function refreshDetailsTable() {
            const tableBody = $('#detail-table tbody');
            tableBody.empty();
            
            let totalAllAmount = 0;
            
            details.forEach((detail, index) => {
                // Format nilai sesuai kebutuhan
                const jumlahIDR = formatRupiah(detail.jumlah_idr);
                totalAllAmount += parseFloat(destroyFormatRupiah(detail.jumlah_idr));
                
                // Tampilkan data sesuai jenis pembayaran
                const row = `
                    <tr>
                        <td>${detail.tanggal}</td>
                        <td>${detail.jenis_pembayaran === 'PUTIH' ? detail.akun_selisih_name : detail.akun_kas_name}</td>
                        <td>${detail.jenis_pembayaran === 'PUTIH' ? detail.akun_kas_name : detail.akun_selisih_name}</td>
                        <td>${detail.valas}</td>
                        <td>${detail.jumlah}</td>
                        <td>${detail.kurs}</td>
                        <td>${jumlahIDR}</td>
                        <td>${detail.pembayaran_oleh}</td>
                        <td>${detail.keterangan || ''}</td>
                        <td class="actions">
                            <button class="btn btn-sm btn-warning btn-edit-detail" data-index="${index}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete-detail" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tableBody.append(row);
            });
            
            // Update total amount
            $('#total_all_amount').val(formatRupiah(totalAllAmount.toString()));
        }

        // Remove detail
        $(document).on('click', '.btn-remove-detail', function() {
            const index = $(this).data('index');
            details.splice(index, -1);
            refreshDetailsTable();
        });

        // Clear detail form
        function clearDetailForm() {
            $('#tanggal, #keterangan, #kurs, #jumlah, #jumlah_idr').val('');
            $('#akun_kas, #valas').val('').trigger('change');
        }

        // Handle final submissiono
        $('.btn-submit-form').click(function() {
            if (details.length === 0) {
                alert('Tambahkan setidaknya satu detail pembayaran');
                return;
            }

            const formData = {
                no_pembayaran: $('#no_pembayaran').val(),
                divisi_id: $('#divisi_id').val(),
                bank_id: $('#bank_id').val(),
                valas: $('#valas').val(),
                bayar_ke: $('#bayar_ke').val(),
                details: details
            };
        });

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-form").click(function() {
            // Reset state
            isEditMode = false;
            initialValues = {};
            
            // Reset form
            $(".id").val("");
            $(".title-name").text("Tambah Pembayaran Lain");
            validator.resetForm();
            validator.reset();
            resetForm();
            $(".create-form")[0].reset();
            $(".delete-btn").css('display', 'none');
            
            // Initialize with silent mode first
            initSelect2();
            
            // Set default values WITHOUT triggering change events
            $('#divisi_id, #bank_id, #metode_pembayaran, #jenis_pembayaran').val(null);
            
            $(".add-modal").modal("show");
        });

        

        function handleFieldChange(element) {
            if (!isEditMode) {
                generatePaymentNumber();
                return;
            }
            
            let currentField = $(element).attr('id');
            let currentValue = $(element).val();
            
            if (initialValues[currentField] !== currentValue) {
                console.log('Field value changed - generating payment number');
                generatePaymentNumber(true);
            }
        }

        $('.btn-discard').click(function() {

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
            $(".create-form")[0].reset();
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update Pembayaran Lain");

            validator.resetForm();
            validator.reset();

            $.ajax({
                url: "<?= base_url("pembayaran-lain/get"); ?>",
                data: { id: id },
                beforeSend: function() { setLoading(); },
                complete: function() { stopLoading(); },
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        // Reset form and clear details
                        resetForm();
                        details = [];
                        
                        // Store initial values BEFORE populating form
                        const parent = res.data.parent;
                        initialValues = {
                            divisi_id: parent.divisi_id,
                            bank_id: parent.bank_id,
                            metode_pembayaran: parent.metode_pembayaran,
                            jenis_pembayaran: parent.jenis_pembayaran
                        };

                        // Update account labels based on jenis_pembayaran FIRST
                        updateAccountLabels(parent.jenis_pembayaran);

                        // Now initialize with proper handlers
                        initSelect2();
                        
                        // Set parent data WITHOUT triggering change events
                        $('#id').val(parent.id);
                        $("#no_pembayaran").val(parent.no_pembayaran);
                        
                        // Use Select2's internal trigger
                        $('#divisi_id').val(parent.divisi_id).trigger('change.select2');
                        $('#bank_id').val(parent.bank_id).trigger('change.select2');
                        $('#metode_pembayaran').val(parent.metode_pembayaran).trigger('change.select2');
                        $('#jenis_pembayaran').val(parent.jenis_pembayaran).trigger('change.select2');
                        
                        // Set other fields
                        $('#bayar_ke').val(parent.bayar_ke);
                        $('#akun_selisih').val(parent.akun_selisih).trigger('change');
                        $('#keterangan_parent').val(parent.keterangan_parent);
                        $('#total_all_amount').val(parent.total_all_amount);

                        // Handle MERAH case
                        if (parent.jenis_pembayaran === 'MERAH' && parent.akun_kas) {
                            $('#akun_selisih').val(parent.akun_kas).trigger('change');
                        }

                        // Disable fields if posted
                        if (parent.status_posting === "1") {
                            $("#no_pembayaran").attr('disabled', true);
                            disabledForm();
                        }

                        // Set details data
                        if (res.data.details && res.data.details.length > 0) {
                            res.data.details.forEach(detail => {
                                details.push({
                                    id: detail.id,
                                    tanggal: detail.tanggal,
                                    pembayaran_oleh: detail.pembayaran_oleh,
                                    akun_kas: parent.jenis_pembayaran == "PUTIH" ? detail.akun_kas : detail.akun_selisih,
                                    akun_selisih: parent.jenis_pembayaran == "PUTIH" ? detail.akun_selisih : detail.akun_kas,
                                    akun_kas_name: detail.akun_kas_name,
                                    akun_selisih_name: detail.akun_selisih_name,
                                    jenis_pembayaran: parent.jenis_pembayaran,
                                    keterangan: detail.keterangan,
                                    valas: detail.valas,
                                    valas_id: detail.valas_id,
                                    kurs: detail.kurs,
                                    jumlah: detail.jumlah,
                                    jumlah_idr: detail.jumlah_idr,
                                    dataBE: true
                                });
                            });
                            refreshDetailsTable();
                        }

                        isEditMode = true;
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
                        const jenisPembayaran = $('#jenis_pembayaran').val();

                        // Prepare data with proper formatting
                        const requestData = {
                            id: id,
                            divisi_id: $('#divisi_id').val(),
                            bank_id: $('#bank_id').val(),
                            no_pembayaran: $('#no_pembayaran').val(),
                            metode_pembayaran: $('#metode_pembayaran').val(),
                            bayar_ke: $('#bayar_ke').val(),
                            akun_selisih: $('#akun_selisih').val(),
                            total_all_amount: destroyFormatRupiah($('#total_all_amount').val()),
                            keterangan_parent: $('#keterangan_parent').val(),
                            jenis_pembayaran: jenisPembayaran,
                            details: details.map(detail => ({
                                id: detail.id || '',
                                tanggal: detail.tanggal,
                                pembayaran_oleh: detail.pembayaran_oleh,
                                akun_kas: detail.akun_kas,
                                valas: detail.valas,
                                valas_id: detail.valas_id,
                                kurs: destroyFormatRupiah(detail.kurs),
                                jumlah: destroyFormatRupiah(detail.jumlah),
                                jumlah_idr: destroyFormatRupiah(detail.jumlah_idr),
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
        $('#bank_id').attr('disabled', true);
        $('#bayar_ke').attr('disabled', true);
        $('#valas').attr('disabled', true);
        $('#metode_pembayaran').attr('disabled', true);
        $('#pembayaran_oleh').attr('disabled', true);
        $('#akun_kas').attr('disabled', true);
        $('#akun_selisih').attr('disabled', true);
        $('#keterangan').attr('disabled', true);
        $('#jenis_pembayaran').attr('disabled', true);
        $('#keterangan_parent').attr('disabled', true);
        $('#jumlah').attr('disabled', true);
        $('#kurs').attr('disabled', true);
        $('#jumlah_idr').attr('disabled', true);

        $('.btn-submit-form').hide();
    }

    function resetForm() {
        $("#no_pembayaran").attr('disabled', false);
        $('#tanggal').attr('disabled', false);
        $('#divisi_id').attr('disabled', false);
        $('#bank_id').attr('disabled', false);
        $('#bayar_ke').attr('disabled', false);
        $('#valas').attr('disabled', false);
        $('#metode_pembayaran').attr('disabled', false);
        $('#pembayaran_oleh').attr('disabled', false);
        $('#akun_kas').attr('disabled', false);
        $('#akun_selisih').attr('disabled', false);
        $('#keterangan').attr('disabled', false);
        $('#jenis_pembayaran').attr('disabled', false);
        $('#keterangan_parent').attr('disabled', false);
        $('#jumlah').attr('disabled', false);
        $('#kurs').attr('disabled', false);
        $('#jumlah_idr').attr('disabled', false);

        $("#id").val(null).change();
        $("#no_pembayaran").val(null).change();
        $('#tanggal').val(null).change();
        $('#divisi_id').val(null).change();
        $('#bank_id').val(null).change();
        $('#bayar_ke').val(null).change();
        $('#valas').val(null).change();
        $('#metode_pembayaran').val(null).change();
        $('#pembayaran_oleh').val(null).change();
        $('#akun_kas').val(null).change();
        $('#akun_selisih').val(null).change();
        $('#keterangan').val(null).change();

        $('.btn-submit-form').show();
    }


    const posting = function(id, status) {
        const actionText = status == 1 ? 'Posting' : 'Unposting';

        Swal.fire({
            icon: 'question',
            title: `${actionText} Pembayaran ?`,
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
                        status: status, // kirim juga status ke server jika perlu
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
                                title: `${actionText} berhasil!`,
                                text: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then(() => {
                                table.ajax.reload();
                            });
                        }
                    },
                });
            }
        });
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

    function editDetail(index) {
        const detail = details[index];
        console.log(detail)
        // Populate all form fields
        $('#tanggal').val(detail.tanggal);
        $('#pembayaran_oleh').val(detail.pembayaran_oleh);
        $('#keterangan').val(detail.keterangan);
        $('#valas').val(detail.valas_id).trigger('change');
        $('#jumlah').val(detail.jumlah);
        $('#kurs').val(detail.kurs);
        $('#jumlah_idr').val(detail.jumlah_idr);
        $('#akun_selisih').val(detail.akun_selisih).trigger('change');
        $('#akun_kas').val(detail.akun_kas).trigger('change');
        
        // Update editing index
        editingIndex = index;
        
        // Change button state
        $('.btn-add-detail').hide();
        $('.btn-update-detail').show().data('index', index);
    }

    // Clear detail form
    function clearDetailForm() {
        $('#tanggal, #keterangan, #kurs, #jumlah, #jumlah_idr').val('');
        $('#akun_kas, #valas').val('').trigger('change');
    }

    function generatePaymentNumber(forceGenerate = false) {
        // Debugging: Log status generate
        console.log(`Generate called - Edit mode: ${isEditMode}, Force: ${forceGenerate}`);
        
        // Jika di mode edit dan bukan force generate, skip
        if (isEditMode && !forceGenerate) {
            console.log('Skipped generate in edit mode');
            return;
        }
        
        // Get current values
        let currentValues = {
            divisi_id: $("#divisi_id").val(),
            bank_id: $("#bank_id").val(),
            metode_pembayaran: $("#metode_pembayaran").val(),
            jenis_pembayaran: $("#jenis_pembayaran").val()
        };
        
        // Jika nilai sama dengan initial values, skip
        if (isEditMode && JSON.stringify(currentValues) === JSON.stringify(initialValues)) {
            console.log('Skipped generate - values unchanged');
            return;
        }
        
        // Proses generate nomor
        let jenisPembayaran = $("#jenis_pembayaran option:selected").text();
        let metodePembayaran = $("#metode_pembayaran option:selected").val();
        let divisiId = $("#divisi_id option:selected").text();
        let bankId = $("#bank_id option:selected").val();
        
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        
        let url = "<?= base_url('pembayaran-lain/generate-no-pembayaran'); ?>";
        url += `?jenisPembayaran=${encodeURIComponent(jenisPembayaran)}&divisiId=${encodeURIComponent(divisiId)}&metodePembayaran=${encodeURIComponent(metodePembayaran)}&bankId=${encodeURIComponent(bankId)}`;
        
        $(".no_pembayaran").attr("readonly", true);
        
        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            success: function(response) {
                csrf.val(response.token);
                $(".no_pembayaran").val(response.paymentNo);
                console.log('Generated payment number:', response.paymentNo);
            },
            error: function(xhr, status, error) {
                console.error('Error generating payment number:', error);
                $(".no_pembayaran").attr("readonly", false);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan pada sistem',
                    text: 'Gagal menghasilkan nomor pembayaran otomatis',
                    confirmButtonColor: '#4e73df',
                });
            }
        });
    }
        
</script>
<?= $this->endSection(); ?>
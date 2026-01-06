<?= $this->extend('layouts/template'); ?>
<?= $this->section('content'); ?>

<section class="section">
    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-fingerprint"></i> Data Presensi Fingerprint</h4>
            </div>
            
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <!-- Divisi Filter -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label>Divisi</label>
                            <select class="form-control select2" id="divisi_filter">
                                <option value="">Semua Divisi</option>
                                <?php foreach ($dataDivisi as $c): ?>
                                    <option value="<?= esc($c['id']) ?>">
                                        <?= esc($c['divisi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Perusahaan Filter -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label>Perusahaan</label>
                            <select class="form-control select2" id="company_filter">
                                <option value="">Semua Perusahaan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tanggal Filter -->
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" id="date_filter" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <!-- Jenis Gaji -->
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label>Jenis Gaji</label>
                            <select class="form-control" id="payroll_type">
                                 <option value=""></option>
                                 <?php foreach ($metadata as $v) : ?>
                                    <option value="<?= $v['id'] ?>">
                                        <?= strtoupper($v['value']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons Group -->
                    <div class="col-md-12">
                        <div class="card-action-buttons mt-3">
                            <div class="btn-toolbar justify-content-start" role="toolbar">
                                <div class="btn-group mr-2 mb-2" role="group">
                                    <button type="button" class="btn btn-info" id="load-btn">
                                        <i class="fas fa-search"></i> Load Data
                                    </button>
                                </div>
                                
                                <div class="btn-group mr-2 mb-2" role="group">
                                    <button type="button" class="btn btn-warning" id="clear-btn">
                                        <i class="fas fa-broom"></i> Clear Data
                                    </button>
                                </div>
                                
                                <div class="btn-group mr-2 mb-2" role="group">
                                    <button type="button" class="btn btn-success" id="pull-btn">
                                        <i class="fas fa-download"></i> Tarik Data
                                    </button>
                                </div>
                                
                                <div class="btn-group mr-2 mb-2" role="group">
                                    <button type="button" class="btn btn-primary" id="sync-btn" style="display: none;">
                                        <i class="fas fa-sync"></i> Sync to DB
                                    </button>
                                </div>
                                
                                <div class="btn-group mr-2 mb-2" role="group">
                                    <button type="button" class="btn btn-danger" id="print-btn" onclick="printPayrollPDF()">
                                        <i class="fas fa-file-pdf"></i> Print PDF
                                    </button>
                                </div>
                                
                                <div class="btn-group mb-2" role="group">
                                    <button type="button" class="btn btn-secondary" id="export-excel">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Alert -->
                <div class="alert" id="status-alert" style="display: none;"></div>

                <!-- Summary Stats -->
                <div class="row mb-4" id="summary-stats" style="display:none;">
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Karyawan</h4>
                                </div>
                                <div class="card-body" id="total-employees">
                                    0
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Check In</h4>
                                </div>
                                <div class="card-body" id="total-checkin">
                                    0
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Check Out</h4>
                                </div>
                                <div class="card-body" id="total-checkout">
                                    0
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Tidak Hadir</h4>
                                </div>
                                <div class="card-body" id="total-missing">
                                    0
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="attendanceTable">
                        <!-- Table Header -->
                        <thead class="thead-dark">
                            <tr>
                                <th width="50">No</th>
                                <th width="80">Badge</th>
                                <th>Nama Karyawan</th>
                                <th>Divisi</th>
                                <th width="100">Check In</th>
                                <th width="100">Check Out</th>
                                <th width="100">Jam Istirahat</th> <!-- TAMBAHAN -->
                                <th width="100">Total Jam</th>
                                <th width="100">Status</th>
                                <th width="100">Perusahaan</th>
                                <!-- <th width="150" class="text-center">Aksi</th> -->
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <!-- Data will load here -->
                            <tr id="no-data">
                                <td colspan="11" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-database fa-3x text-muted"></i>
                                        </div>
                                        <h2 class="mt-3">Data belum dimuat</h2>
                                        <p class="lead">
                                            Pilih filter dan klik "Load Data" untuk menampilkan data presensi
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="dataTables_info" id="data-info" role="status" aria-live="polite">
                            Menampilkan 0 dari 0 data
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dataTables_paginate paging_simple_numbers float-right" id="data-pagination">
                            <ul class="pagination">
                                <!-- Pagination akan diisi oleh JavaScript -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-info-circle"></i> Detail Presensi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detail-content">
                <!-- Detail akan diisi oleh JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="fas fa-edit"></i> Edit Presensi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-form">
                <div class="modal-body">
                    <input type="hidden" id="edit-user-id" name="user_id">
                    <input type="hidden" id="edit-date" name="date">
                    
                    <div class="form-group">
                        <label for="edit-checkin">Check In</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-sign-in-alt"></i>
                                </span>
                            </div>
                            <input type="datetime-local" class="form-control" id="edit-checkin" name="check_in_rounded">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-checkout">Check Out</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-sign-out-alt"></i>
                                </span>
                            </div>
                            <input type="datetime-local" class="form-control" id="edit-checkout" name="check_out_rounded">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-status">Status</label>
                        <select class="form-control" id="edit-status" name="status">
                            <option value="Hadir">Hadir</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Pulang Awal">Pulang Awal</option>
                            <option value="Tidak Masuk">Tidak Masuk</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Cuti">Cuti</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-keterangan">Keterangan</label>
                        <textarea class="form-control" id="edit-keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#divisi_filter').select2({
            placeholder: "Pilih Divisi",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        $('#payroll_type').select2({
            placeholder: "Pilih Tipe Karyawan",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });


        $('#print_period').select2({
            placeholder: "Pilih Periode",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        $('#print_type').select2({
            placeholder: "Pilih Tipe Karyawan",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });


        $('#company_filter').select2({
            placeholder: "Pilih Perusahaan",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        $('#date_filter').val(today);

        // =========================
        // LOAD COMPANY BY DIVISI
        // =========================
        $('#divisi_filter').on('change', function() {
            const divisiId = $(this).val();
            const $companySelect = $('#company_filter');
            
            $companySelect.html('<option value="">Loading...</option>').prop('disabled', true);
            
            if (!divisiId) {
                $companySelect.html('<option value="">Semua Perusahaan</option>').prop('disabled', false);
                return;
            }

            $.ajax({
                url: '<?= base_url("hr-outsourcing-attendance/listCompanyByDivisi") ?>/' + divisiId,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $companySelect.html('<option value="">Semua Perusahaan</option>');
                    
                    if (res && res.length > 0) {
                        $.each(res, function(i, item) {
                            $companySelect.append(`<option value="${item.id}">${item.name}</option>`);
                        });
                    }
                    
                    $companySelect.prop('disabled', false);
                },
                error: function() {
                    $companySelect.html('<option value="">Error loading</option>').prop('disabled', false);
                    showAlert('danger', 'Gagal memuat data perusahaan', 3000);
                }
            });
        });

        // =========================
        // BUTTON EVENT HANDLERS
        // =========================
        
        // Load Data Button
        $('#load-btn').on('click', function() {
            loadAttendanceData();
        });

        // Clear Data Button
        $('#clear-btn').on('click', function() {
            clearAttendanceData();
            showAlert('info', 'Data berhasil dibersihkan', 3000);
        });

        // Pull Data Button
        $('#pull-btn').on('click', function() {
            pullFromFingerprint();
        });

        // Export Excel Button
        $('#export-excel').on('click', function() {
            exportToExcel();
        });

        // Sync Button
        $('#sync-btn').on('click', function() {
            syncToDatabase();
        });

        // =========================
        // LOAD ATTENDANCE DATA
        // =========================
        function loadAttendanceData() {
            const companyId = $('#company_filter').val();
            const date = $('#date_filter').val();

            if (!companyId) {
                showAlert('warning', 'Pilih perusahaan terlebih dahulu!', 3000);
                return;
            }

            if (!date) {
                showAlert('warning', 'Pilih tanggal terlebih dahulu!', 3000);
                return;
            }

            // Show loading
            $('#load-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            $('#status-alert').hide();
            $('#summary-stats').hide();
            $('#sync-btn').hide();

            $.ajax({
                url: '<?= base_url("hr-outsourcing-attendance/all") ?>',
                type: 'POST',
                data: {
                    company_id: companyId,
                    date: date,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', `Berhasil memuat ${response.total} data presensi`, 5000);
                        renderTable(response.data);
                        updateSummaryStats(response.data);
                        $('#summary-stats').fadeIn();
                        $('#sync-btn').show();
                        updateDataInfo(response.total, response.total);
                    } else {
                        showAlert('danger', response.message || 'Gagal memuat data', 5000);
                        clearTable();
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('danger', 'Error: ' + (xhr.responseJSON?.message || error), 5000);
                    clearTable();
                },
                complete: function() {
                    $('#load-btn').prop('disabled', false).html('<i class="fas fa-search"></i> Load Data');
                }
            });
        }

        // =========================
        // CLEAR ATTENDANCE DATA
        // =========================
        function clearAttendanceData() {
            $('#table-body').html(`
                <tr id="no-data">
                    <td colspan="10" class="text-center py-5">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-database fa-3x text-muted"></i>
                            </div>
                            <h2 class="mt-3">Data belum dimuat</h2>
                            <p class="lead">
                                Pilih filter dan klik "Load Data" untuk menampilkan data presensi
                            </p>
                        </div>
                    </td>
                </tr>
            `);
            $('#summary-stats').hide();
            $('#sync-btn').hide();
            updateDataInfo(0, 0);
        }

        // =========================
        // PULL FROM FINGERPRINT
        // =========================
        function pullFromFingerprint() {
            const companyId = $('#company_filter').val();
            const date = $('#date_filter').val();

            if (!companyId) {
                showAlert('warning', 'Pilih perusahaan terlebih dahulu!', 3000);
                return;
            }

            if (!date) {
                showAlert('warning', 'Pilih tanggal terlebih dahulu!', 3000);
                return;
            }

            Swal.fire({
                title: 'Tarik Data dari Fingerprint',
                text: 'Apakah Anda yakin ingin menarik data dari mesin fingerprint?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tarik Data',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Menarik Data...',
                    text: 'Sedang mengambil data dari fingerprint',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: '<?= base_url("hr-outsourcing-attendance/pull-from-fingerprint") ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        company_id: companyId,
                        date: date,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function (response) {
                        Swal.close();

                        if (!response.success) {
                            showAlert('danger', response.message || 'Gagal menarik data', 5000);
                            return;
                        }

                        const fromMachine = response.from_machine ?? 0;
                        const inserted    = response.inserted ?? 0;

                        showAlert(
                            'success',
                            `Fingerprint: ${fromMachine} data • Tersimpan: ${inserted} data`,
                            6000
                        );

                        loadAttendanceData(); // reload table
                    },
                    error: function (xhr) {
                        Swal.close();

                        let msg = 'Error saat menarik data dari fingerprint';
                        if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }

                        showAlert('danger', msg, 5000);
                    }
                });
            });
        }

        // =========================
        // EXPORT TO EXCEL
        // =========================
        function exportToExcel() {
            const companyId = $('#company_filter').val();
            const date = $('#date_filter').val();

            if (!companyId) {
                showAlert('warning', 'Pilih perusahaan terlebih dahulu!', 3000);
                return;
            }

            if (!date) {
                showAlert('warning', 'Pilih tanggal terlebih dahulu!', 3000);
                return;
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url("hr-outsourcing-attendance/exportExcel") ?>';
            form.style.display = 'none';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '<?= csrf_token() ?>';
            csrfToken.value = '<?= csrf_hash() ?>';
            form.appendChild(csrfToken);

            const companyInput = document.createElement('input');
            companyInput.type = 'hidden';
            companyInput.name = 'company_id';
            companyInput.value = companyId;
            form.appendChild(companyInput);

            const dateInput = document.createElement('input');
            dateInput.type = 'hidden';
            dateInput.name = 'date';
            dateInput.value = date;
            form.appendChild(dateInput);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        // =========================
        // SYNC TO DATABASE
        // =========================
        function syncToDatabase() {
            const companyId = $('#company_filter').val();
            const date = $('#date_filter').val();

            if (!companyId || !date) {
                showAlert('warning', 'Pilih perusahaan dan tanggal terlebih dahulu!', 3000);
                return;
            }

            Swal.fire({
                title: 'Sinkronisasi ke Database',
                text: 'Apakah Anda yakin ingin menyinkronkan data ke database?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Sinkronkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#sync-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');

                    $.ajax({
                        url: '<?= base_url("hr-outsourcing-attendance/syncToDatabase") ?>',
                        type: 'POST',
                        data: {
                            company_id: companyId,
                            date: date,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', `Berhasil sinkronisasi ${response.synced || 0} data ke database`, 5000);
                            } else {
                                showAlert('danger', response.message || 'Gagal sinkronisasi data', 5000);
                            }
                        },
                        error: function() {
                            showAlert('danger', 'Error saat sinkronisasi data', 5000);
                        },
                        complete: function() {
                            $('#sync-btn').prop('disabled', false).html('<i class="fas fa-sync"></i> Sync to DB');
                        }
                    });
                }
            });
        }

        // =========================
        // RENDER TABLE FUNCTION (REVISED)
        // =========================
        function renderTable(data) {
            const tbody = $('#table-body');
            tbody.empty();

            if (!data || data.length === 0) {
                tbody.html(`
                    <tr id="no-data">
                        <td colspan="10" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-database fa-3x text-muted"></i>
                                </div>
                                <h2 class="mt-3">Tidak ada data</h2>
                                <p class="lead">
                                    Tidak ditemukan data presensi untuk filter yang dipilih
                                </p>
                            </div>
                        </td>
                    </tr>
                `);
                return;
            }

            let no = 1;
            let totalRows = 0; // Untuk menghitung baris yang benar-benar ditampilkan
            
            $.each(data, function(index, emp) {
                // Skip jika tidak ada check_in (tidak masuk)
                if (!emp.check_in) {
                    return; // continue ke data berikutnya
                }
                
                totalRows++; // Hitung baris yang ditampilkan

                // TAMBAHAN: Format Break Time Input
                const breakTimeValue = emp.breaktime || 0;
                const breakTimeDisplay = `
                    <div class="break-time-input-container">
                        <input type="text" 
                            class="form-control form-control-sm break-time-input" 
                            value="${breakTimeValue}"
                            data-user-id="${emp.user_id}"
                            data-original="${breakTimeValue}"
                            placeholder="0.5"
                            style="width: 80px; text-align: center;"
                            onchange="updateBreakTime(this, '${emp.user_id}')">
                        <div class="small text-muted mt-1">jam</div>
                        ${breakTimeValue > 0 ? `
                            <div class="text-info small">
                                <i class="fas fa-clock"></i> ${Math.round(breakTimeValue * 60)} menit
                            </div>
                        ` : ''}
                    </div>
                `;

                // Format Check In
                const checkInDisplay = emp.check_in_rounded 
                    ? `<div class="text-center">
                        <div class="time-display">${emp.check_in_rounded}</div>
                    </div>`
                    : '<span class="text-muted">-</span>';
                
                // Format Check Out
                const checkOutDisplay = emp.check_out_rounded 
                    ? `<div class="text-center">
                        <div class="time-display">${emp.check_out_rounded}</div>
                    </div>`
                    : '<span class="text-muted">-</span>';
                
                // Hitung jam kerja dalam menit
                let workHoursInMinutes = 0;
                let workHoursDisplay = '<span class="text-muted">-</span>';
                let workHoursClass = '';
                
                if (emp.work_hours && emp.work_hours !== '0:00') {
                    const [hours, minutes] = emp.work_hours.split(':').map(Number);
                    workHoursInMinutes = (hours * 60) + minutes;
                    
                    // Tentukan warna berdasarkan jam kerja (4 jam = 240 menit)
                    let workHoursColor = 'danger'; // default merah untuk < 4 jam
                    if (workHoursInMinutes >= 240) {
                        workHoursColor = 'success'; // hijau untuk ≥ 4 jam
                    }
                    
                    workHoursDisplay = `
                        <div class="text-center">
                            <div class="work-hours-display text-${workHoursColor}">
                                <i class="fas fa-clock mr-1"></i>${emp.work_hours}
                            </div>
                            <small class="text-muted">${hours} jam ${minutes} menit</small>
                        </div>
                    `;
                    
                    // Background color untuk row
                    if (workHoursInMinutes >= 240) {
                        workHoursClass = 'bg-success-light';
                    } else if (workHoursInMinutes > 0) {
                        workHoursClass = 'bg-danger-light';
                    }
                }

                // Tentukan Status sesuai permintaan user
                let status = '';
                let statusClass = '';
                
                if (emp.check_in_rounded) {
                    if (emp.check_out_rounded) {
                        // Ada check in dan check out
                        if (workHoursInMinutes >= 240) { // 4 jam atau lebih
                            status = 'Hadir';
                            statusClass = 'success'; // hijau
                        } else if (workHoursInMinutes > 0) {
                            status = 'Kurang Jam';
                            statusClass = 'danger'; // merah
                        } else {
                            status = 'Belum Checkout';
                            statusClass = 'warning'; // kuning
                        }
                    } else {
                        // Hanya check in (belum checkout)
                        status = 'Belum Checkout';
                        statusClass = 'warning'; // kuning
                    }
                } else {
                    // Tidak ada check in (tidak masuk)
                    status = 'Tidak Masuk';
                    statusClass = 'secondary'; // abu-abu (bisa diganti 'danger' jika mau merah)
                }
                
                tbody.append(`
                    <tr class="${workHoursClass}">
                        <td class="text-center">${no++}</td>
                        <td class="text-center">
                            <span class="badge badge-light border" style="font-size: 0.9em; padding: 5px 10px;">
                                <i class="fas fa-id-badge mr-1"></i>${emp.badge_no || '-'}
                            </span>
                        </td>
                        <td>
                            <strong>${emp.name}</strong>
                            <div class="small text-muted">ID: ${emp.user_id}</div>
                        </td>
                        <td>${emp.divisi_name || '-'}</td>
                        <td>${checkInDisplay}</td>
                        <td>${checkOutDisplay}</td>
                        <td class="text-center">${breakTimeDisplay}</td>
                        <td class="text-center">${workHoursDisplay}</td>
                        <td class="text-center">
                            <span class="badge badge-${statusClass}">
                                ${status}
                            </span>
                        </td>
                        <td>${emp.company_name}</td>
                    </tr>
                `);
            });
            
            // Jika tidak ada data yang ditampilkan (semua skip karena !emp.check_in)
            if (totalRows === 0) {
                tbody.html(`
                    <tr id="no-data">
                        <td colspan="10" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-user-slash fa-3x text-muted"></i>
                                </div>
                                <h2 class="mt-3">Semua Karyawan Tidak Masuk</h2>
                                <p class="lead">
                                    Tidak ada karyawan yang check in pada tanggal ini
                                </p>
                            </div>
                        </td>
                    </tr>
                `);
            }
        }

        // =========================
        // UPDATE SUMMARY STATS
        // =========================
        function updateSummaryStats(data) {
            if (!data || data.length === 0) {
                $('#total-employees').text('0');
                $('#total-checkin').text('0');
                $('#total-checkout').text('0');
                $('#total-missing').text('0');
                return;
            }

            const totalEmployees = data.length;
            const checkInCount = data.filter(item => item.check_in !== null).length;
            const checkOutCount = data.filter(item => item.check_out !== null).length;
            const missingCount = data.filter(item => 
                item.check_in === null && item.check_out === null
            ).length;

            $('#total-employees').text(totalEmployees);
            $('#total-checkin').text(checkInCount);
            $('#total-checkout').text(checkOutCount);
            $('#total-missing').text(missingCount);
        }

        // =========================
        // HELPER FUNCTIONS
        // =========================
        // =========================
        // HELPER FUNCTIONS
        // =========================
        function getStatusClass(status) {
            const statusMap = {
                'Hadir': 'success',          // hijau
                'Belum Checkout': 'warning', // kuning
                'Kurang Jam': 'danger',      // merah
                'Tidak Masuk': 'secondary',  // abu-abu (bisa diganti 'danger' jika mau merah)
                'Terlambat': 'warning',
                'Pulang Awal': 'info',
                'Izin': 'primary',
                'Sakit': 'info',
                'Cuti': 'warning'
            };
            return statusMap[status] || 'secondary';
        }

        function updateDataInfo(total, filtered) {
            $('#data-info').text(`Menampilkan ${filtered} dari ${total} data`);
        }

        function clearTable() {
            $('#table-body').html(`
                <tr id="no-data">
                    <td colspan="10" class="text-center py-5">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-database fa-3x text-muted"></i>
                            </div>
                            <h2 class="mt-3">Tidak ada data</h2>
                            <p class="lead">
                                Tidak ditemukan data presensi untuk filter yang dipilih
                            </p>
                        </div>
                    </td>
                </tr>
            `);
            $('#summary-stats').hide();
            $('#sync-btn').hide();
            updateDataInfo(0, 0);
        }
    });


    // Global function untuk update break time
    function updateBreakTime(inputElement, userId) {
            const input = $(inputElement);
            const breakTimeValue = parseFloat(input.val()) || 0;
            const originalValue = parseFloat(input.data('original')) || 0;
            
            // Validasi: maksimal 8 jam istirahat
            if (breakTimeValue > 8) {
                showAlert('warning', 'Jam istirahat maksimal 8 jam', 3000);
                input.val(originalValue);
                return;
            }
            
            // Jika tidak ada perubahan
            if (breakTimeValue === originalValue) {
                return;
            }
            
            // Tampilkan loading di input
            input.prop('disabled', true).addClass('bg-warning-light');
            
            // Ambil tanggal dari filter
            const date = $('#date_filter').val();
            const companyId = $('#company_filter').val();
            
            $.ajax({
                url: '<?= base_url("hr-outsourcing-attendance/updateBreakTime") ?>',
                type: 'POST',
                data: {
                    user_id: userId,
                    date: date,
                    company_id: companyId,
                    break_time: breakTimeValue,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update original value
                        input.data('original', breakTimeValue);
                        
                        // Update tampilan
                        const container = input.closest('.break-time-input-container');
                        const preview = container.find('.text-info');
                        
                        if (breakTimeValue > 0) {
                            if (preview.length) {
                                preview.html(`<i class="fas fa-clock"></i> ${Math.round(breakTimeValue * 60)} menit`);
                            } else {
                                container.append(`
                                    <div class="text-info small">
                                        <i class="fas fa-clock"></i> ${Math.round(breakTimeValue * 60)} menit
                                    </div>
                                `);
                            }
                        } else {
                            preview.remove();
                        }
                        
                        // Recalculate work hours
                        recalculateWorkHours(userId, breakTimeValue);
                        
                        // Tampilkan notifikasi sukses
                        showAlert('success', `Jam istirahat diperbarui: ${breakTimeValue} jam`, 2000);
                    } else {
                        // Kembalikan ke nilai semula
                        input.val(originalValue);
                        showAlert('danger', response.message || 'Gagal memperbarui jam istirahat', 3000);
                    }
                },
                error: function() {
                    // Kembalikan ke nilai semula
                    input.val(originalValue);
                    showAlert('danger', 'Error saat memperbarui jam istirahat', 3000);
                },
                complete: function() {
                    input.prop('disabled', false).removeClass('bg-warning-light');
                }
            });
    }

    // Global functions
    function showAlert(type, message, duration = 5000) {
        const alert = $('#status-alert');
        alert.removeClass().addClass(`alert alert-${type} alert-dismissible fade show`)
            .html(`
                <strong>${type === 'success' ? 'Success!' : type === 'danger' ? 'Error!' : 'Warning!'}</strong>
                ${message}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            `)
            .show();
        
        if (duration) {
            setTimeout(() => alert.alert('close'), duration);
        }
    }

    function printPayrollPDF() {
        const companyId = $('#company_filter').val();
        const date = $('#date_filter').val();
        
        if (!companyId) {
            showAlert('warning', 'Pilih perusahaan terlebih dahulu!', 3000);
            return;
        }

        if (!date) {
            showAlert('warning', 'Pilih tanggal terlebih dahulu!', 3000);
            return;
        }

        const payrollType = $('#payroll_type').val();
        
        Swal.fire({
            title: 'Generate Laporan Gaji PDF',
            html: `
                <div class="form-group">
                    <label>Periode:</label>
                    <select id="print_period" class="form-control">
                        <option value="1-15">1-15</option>
                        <option value="16-31">16-31</option>
                        <option value="full">Bulan Penuh</option>
                    </select>
                </div>
                <div class="form-group mt-3">
                    <label>Jenis Gaji:</label>
                    <select id="print_type" class="form-control">
                        <?php foreach ($metadata as $v) : ?>
                            <option value="<?= $v['id'] ?>">
                                <?= strtoupper($v['value']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Generate PDF',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            width: '500px',
            preConfirm: () => {
                return {
                    period: $('#print_period').val(),
                    type: $('#print_type').val()
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const params = result.value;
                
                // Show loading
                Swal.fire({
                    title: 'Generating PDF...',
                    text: 'Sedang memproses laporan gaji',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // AJAX request untuk generate PDF
                $.ajax({
                    url: '<?= base_url("hr-outsourcing-attendance/printPayrol") ?>',
                    type: 'POST',
                    data: {
                        company_id: companyId,
                        date: date,
                        period: params.period,
                        type: params.type,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        
                        if (response.success) {
                            downloadPDF(response.data, response.filename);
                            showAlert('success', 'PDF berhasil di-generate', 3000);
                        } else {
                            showAlert('danger', response.message || 'Gagal generate PDF', 5000);
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        showAlert('danger', 'Error: ' + error, 5000);
                    }
                });
            }
        });
    }

    function downloadPDF(base64Data, fileName) {
        try {
            // Decode base64 to blob
            const byteCharacters = atob(base64Data);
            const byteNumbers = new Array(byteCharacters.length);
            
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], { type: 'application/pdf' });
            
            // Create download link
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = fileName;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Clean up
            setTimeout(() => window.URL.revokeObjectURL(link.href), 100);
        } catch (error) {
            console.error('Error downloading PDF:', error);
            showAlert('danger', 'Error saat mendownload PDF', 5000);
        }
    }
</script>

<style>
/* Card Statistics */
.card-statistic-1 {
    border: 1px solid #e3e3e3;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.card-statistic-1 .card-icon {
    width: 60px;
    height: 60px;
    line-height: 60px;
    text-align: center;
    border-radius: 50%;
    float: left;
    margin-right: 15px;
}
.card-statistic-1 .card-icon i {
    font-size: 24px;
    color: white;
}
.card-statistic-1 .card-wrap {
    overflow: hidden;
}
.card-statistic-1 .card-header h4 {
    font-size: 13px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 5px;
}
.card-statistic-1 .card-body {
    font-size: 20px;
    font-weight: 700;
    color: #343a40;
}

/* Button Groups */
.card-action-buttons {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e3e3e3;
}
.btn-group {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.btn-group .btn {
    border-radius: 4px !important;
    padding: 8px 15px;
    font-weight: 500;
}
.btn-group .btn:not(:last-child) {
    border-right: 1px solid rgba(255,255,255,0.2);
}

/* Table Styles */
#attendanceTable th {
    background-color: #2c3e50;
    color: white;
    font-weight: 600;
    border: none;
}
#attendanceTable td {
    vertical-align: middle;
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* Work Hours Highlight */
.bg-success-light {
    background-color: rgba(40, 167, 69, 0.05) !important;
}
.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.05) !important;
}
.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.05) !important;
}

.work-hours-display {
    font-weight: 600;
    font-size: 14px;
    font-family: 'Courier New', monospace;
}
.work-hours-display.text-success {
    color: #28a745 !important;
}
.work-hours-display.text-warning {
    color: #ffc107 !important;
}
.work-hours-display.text-danger {
    color: #dc3545 !important;
}

/* Badge Styles */
.badge {
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 4px;
}
.verification-badge {
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 3px;
    display: inline-block;
    margin-top: 3px;
}

/* Time Display */
.time-display {
    font-weight: 600;
    font-size: 14px;
    font-family: 'Courier New', monospace;
}

/* Empty State */
.empty-state {
    padding: 40px 0;
}
.empty-state-icon {
    margin-bottom: 20px;
    opacity: 0.5;
}
.empty-state h2 {
    font-size: 24px;
    color: #6c757d;
    margin-bottom: 10px;
}
.empty-state .lead {
    font-size: 16px;
    color: #adb5bd;
    max-width: 500px;
    margin: 0 auto;
}

/* Modal Styles */
.modal-content {
    border-radius: 10px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.modal-header {
    border-radius: 10px 10px 0 0;
    padding: 15px 20px;
}
.modal-body {
    padding: 20px;
}
.modal-footer {
    border-top: 1px solid #e3e3e3;
    padding: 15px 20px;
}

/* Progress Bar */
.progress {
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}
.progress-bar {
    background-color: #28a745;
    transition: width 0.6s ease;
}

/* Detail Modal Work Summary */
.work-summary {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e3e3e3;
}
.work-summary h3 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

/* Tambahkan di bagian style */
.break-time-input-container {
    max-width: 100px;
    margin: 0 auto;
}

.break-time-input {
    font-size: 14px;
    font-weight: 500;
    padding: 4px 8px;
    height: 30px;
    border-radius: 4px;
    border: 1px solid #ced4da;
}

.break-time-input:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.break-time-input:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

/* Responsive untuk input break time */
@media (max-width: 768px) {
    .break-time-input-container {
        max-width: 70px;
    }
    
    .break-time-input {
        width: 60px;
        font-size: 12px;
        padding: 2px 4px;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .btn-toolbar {
        flex-direction: column;
    }
    .btn-group {
        width: 100%;
        margin-bottom: 5px;
    }
    .btn-group .btn {
        flex: 1;
        text-align: center;
    }
    .card-statistic-1 {
        margin-bottom: 15px;
    }
    #attendanceTable {
        font-size: 14px;
    }
    #attendanceTable th,
    #attendanceTable td {
        padding: 8px;
    }
}

@media (max-width: 576px) {
    .section-body {
        padding: 10px;
    }
    .card-body {
        padding: 15px;
    }
    .btn-group .btn {
        padding: 6px 10px;
        font-size: 13px;
    }
}
</style>

<?= $this->endSection(); ?>
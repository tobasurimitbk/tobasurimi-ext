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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Divisi</label>
                            <select class="form-control select2" id="divisi_filter">
                                <option value="">Semua Divisi</option>
                                <?php foreach ($dataDivisi as $c): ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= esc($c['divisi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Perusahaan</label>
                            <select class="form-control select2" id="company_filter">
                                <option value="">Semua Perusahaan</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" id="date_filter" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-block" id="load-btn">
                                <i class="fas fa-search"></i> Load
                            </button>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-success btn-block" id="sync-btn" style="display:none;">
                                <i class="fas fa-sync"></i> Sync to DB
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Alert -->
                <div class="alert" id="status-alert" style="display: none;"></div>

                <!-- Summary Stats -->
                <div class="row mb-3" id="summary-stats" style="display:none;">
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Karyawan</span>
                                <br>
                                <span class="info-box-number" id="total-employees">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-sign-in-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Check In</span>
                                <br>
                                <span class="info-box-number" id="total-checkin">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-sign-out-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Check Out</span>
                                <br>
                                <span class="info-box-number" id="total-checkout">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-user-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Ga Absen</span>
                                <br>
                                <span class="info-box-number" id="total-missing">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="attendanceTable">
                        <thead class="thead-dark">
                            <tr>
                                <th width="50">No</th>
                                <th width="80">Badge</th>
                                <th>Nama Karyawan</th>
                                <th width="120">Check In</th>
                                <th width="120">Check Out</th>
                                <th width="100">Status</th>
                                <th width="100">Perusahaan</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <!-- Data will load here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('#company_filter, #divisi_filter').select2();

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
            }
        });
    });

    // =========================
    // LOAD ATTENDANCE DATA
    // =========================
    $('#load-btn').on('click', function() {
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
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
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
                    showAlert('success', `Berhasil memuat ${response.total} data`, 5000);
                    renderTable(response.data);
                    updateSummaryStats(response.data);
                    $('#summary-stats').show();
                    $('#sync-btn').show();
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
                $('#load-btn').prop('disabled', false).html('<i class="fas fa-search"></i> Load');
            }
        });
    });

    // =========================
    // RENDER TABLE FUNCTION
    // =========================
    // =========================
    // RENDER TABLE FUNCTION - PERBAIKAN
    // =========================
    function renderTable(data) {
        const tbody = $('#table-body');
        tbody.empty();

        if (!data || data.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-database fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada data presensi</h5>
                        <p class="text-muted small">Coba pilih tanggal atau perusahaan lain</p>
                    </td>
                </tr>
            `);
            return;
        }

        // Render table rows langsung dari data (tidak perlu grouping lagi)
        let no = 1;
        $.each(data, function(index, emp) {
            // Format Check In dengan Verified badge
            const checkInDisplay = emp.check_in 
                ? `<div>
                    <strong class="text-success">${emp.check_in}</strong>
                    <div><small class="${emp.verified_in === 'Verified' ? 'text-success' : 'text-danger'}">
                        <i class="fas ${emp.verified_in === 'Verified' ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        ${emp.verified_in}
                    </small></div>
                </div>`
                : '<span class="text-muted">-</span>';
            
            // Format Check Out dengan Verified badge
            const checkOutDisplay = emp.check_out 
                ? `<div>
                    <strong class="text-warning">${emp.check_out}</strong>
                    <div><small class="${emp.verified_out === 'Verified' ? 'text-success' : 'text-danger'}">
                        <i class="fas ${emp.verified_out === 'Verified' ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        ${emp.verified_out}
                    </small></div>
                </div>`
                : '<span class="text-muted">-</span>';

            // Badge color berdasarkan verified status
            const badgeColor = emp.badge_no ? 'info' : 'secondary';
            const badgeText = emp.badge_no || '-';

            tbody.append(`
                <tr>
                    <td class="text-center">${no++}</td>
                    <td class="text-center">
                        <span class="badge badge-${badgeColor}" style="font-size: 1em; padding: 5px 10px;">
                            <i class="fas fa-id-badge mr-1"></i>${badgeText}
                        </span>
                    </td>
                    <td>
                        <strong>${emp.name}</strong>
                        <div class="small text-muted">User ID: ${emp.user_id}</div>
                    </td>
                    <td class="text-center">${checkInDisplay}</td>
                    <td class="text-center">${checkOutDisplay}</td>
                    <td class="text-center">
                        <span class="badge badge-${emp.status_class}">
                            ${emp.status}
                        </span>
                    </td>
                    <td>${emp.company_name}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-info view-btn" 
                                data-user-id="${emp.user_id}"
                                data-name="${emp.name}"
                                data-badge="${emp.badge_no}"
                                data-checkin="${emp.check_in || ''}"
                                data-checkout="${emp.check_out || ''}"
                                data-status="${emp.status}"
                                title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning edit-btn" 
                                data-user-id="${emp.user_id}"
                                data-date="${$('#date_filter').val()}"
                                title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });
    }

    // =========================
    // UPDATE SUMMARY STATS - PERBAIKAN
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
        
        // Count check in dan check out dari data baru
        const checkInCount = data.filter(item => item.check_in !== null).length;
        const checkOutCount = data.filter(item => item.check_out !== null).length;
        
        // Count status untuk missing
        const missingCount = data.filter(item => item.status === 'Belum Absen').length;

        $('#total-employees').text(totalEmployees);
        $('#total-checkin').text(checkInCount);
        $('#total-checkout').text(checkOutCount);
        $('#total-missing').text(missingCount);
    }

    // =========================
    // TAMBAHKAN EVENT UNTUK VIEW BUTTON
    // =========================
    $(document).on('click', '.view-btn', function() {
        const userId = $(this).data('user-id');
        const name = $(this).data('name');
        const badge = $(this).data('badge');
        const checkin = $(this).data('checkin');
        const checkout = $(this).data('checkout');
        const status = $(this).data('status');
        
        const content = `
            <div class="attendance-detail">
                <div class="row mb-3">
                    <div class="col-4"><strong>User ID:</strong></div>
                    <div class="col-8">${userId}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4"><strong>Nama:</strong></div>
                    <div class="col-8">${name}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4"><strong>Badge No:</strong></div>
                    <div class="col-8">${badge || '-'}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4"><strong>Check In:</strong></div>
                    <div class="col-8">${checkin || '<span class="text-muted">-</span>'}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4"><strong>Check Out:</strong></div>
                    <div class="col-8">${checkout || '<span class="text-muted">-</span>'}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4"><strong>Status:</strong></div>
                    <div class="col-8"><span class="badge badge-${getStatusClass(status)}">${status}</span></div>
                </div>
            </div>
        `;
        
        Swal.fire({
            title: 'Detail Presensi',
            html: content,
            icon: 'info',
            confirmButtonText: 'Tutup'
        });
    });

    function getStatusClass(status) {
        const statusMap = {
            'Complete': 'success',
            'Check In Only': 'warning',
            'Check Out Only': 'info',
            'Belum Absen': 'danger'
        };
        return statusMap[status] || 'secondary';
    }

    // =========================
    // SYNC TO DATABASE
    // =========================
    $('#sync-btn').on('click', function() {
        const companyId = $('#company_filter').val();
        const date = $('#date_filter').val();

        if (!confirm('Sync data presensi ke database?')) return;

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');

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
                    showAlert('success', `Berhasil sync ${response.synced || 0} data ke database`, 5000);
                } else {
                    showAlert('danger', response.message || 'Gagal sync data', 5000);
                }
            },
            error: function() {
                showAlert('danger', 'Error saat sync data', 5000);
            },
            complete: function() {
                $('#sync-btn').prop('disabled', false).html('<i class="fas fa-sync"></i> Sync to DB');
            }
        });
    });

    // =========================
    // HELPER FUNCTIONS
    // =========================
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

    function clearTable() {
        $('#table-body').empty();
        $('#summary-stats').hide();
        $('#sync-btn').hide();
    }
});
</script>

<style>
.info-box {
    color: white;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}
.info-box-icon {
    float: left;
    font-size: 2rem;
    padding: 0 10px;
}
.info-box-content {
    margin-left: 60px;
}
.info-box-text {
    font-size: 0.9rem;
    text-transform: uppercase;
}
.info-box-number {
    font-size: 1.8rem;
    font-weight: bold;
}

#attendanceTable th {
    background-color: #2c3e50;
    color: white;
}

.badge {
    font-size: 0.85em;
    padding: 5px 10px;
}
</style>

<?= $this->endSection(); ?>
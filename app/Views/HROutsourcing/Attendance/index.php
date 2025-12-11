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
                            <label>Pilih Company</label>
                            <select class="form-control" id="company_filter">
                                <option value="">-- Semua Perusahaan --</option>
                                <?php foreach ($company as $c): ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= esc($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" id="date_filter" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-block" id="load-btn">
                                <i class="fas fa-search"></i> Load Data
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Alert -->
                <div class="alert" id="status-alert" style="display: none;"></div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="attendanceTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User ID</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Tipe</th>
                                <th>Verified</th>
                                <th>Perusahaan</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info -->
                <div id="pagination-info" class="mt-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</section>


<script>
$(document).ready(function() {

    // =========================
    //  CLICK LOAD DATA BUTTON
    // =========================
    $('#load-btn').on('click', function() {
        loadData();
    });

     $('#company_filter').select2({
        placeholder: "Pilih Perusahaan",
        theme: "bootstrap-5",
    })

    // =========================
    //  AUTO LOAD IF COMPANY SELECTED
    // =========================
    if ($('#company_filter').val()) {
        loadData();
    }

    // =========================
    //  LOAD DATA FUNCTION
    // =========================
    function loadData() {
        const companyId = $('#company_filter').val();
        const date = $('#date_filter').val();

        $('#load-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        $('#status-alert').hide();

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
                    showAlert('success', `Data berhasil dimuat: ${response.total} record ditemukan`);
                    renderTable(response.data);
                    $('#pagination-info').html(`Total: ${response.total} data`).show();
                } else {
                    showAlert('danger', response.message || 'Gagal memuat data');
                    clearTable();
                }
            },
            error: function() {
                showAlert('danger', 'Terjadi kesalahan saat memuat data');
                clearTable();
            },
            complete: function() {
                $('#load-btn').prop('disabled', false).html('<i class="fas fa-search"></i> Load Data');
            }
        });
    }

    // =========================
    //  RENDER TABLE
    // =========================
    function renderTable(data) {
        const tbody = $('#table-body');
        tbody.empty();

        if (!data.length) {
            tbody.html(`
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        <i class="fas fa-database fa-2x mb-2"></i><br>
                        Tidak ada data ditemukan
                    </td>
                </tr>
            `);
            return;
        }

        let no = 1;
        $.each(data, function(index, row) {
            let typeClass = 'secondary';
            let typeIcon = '';

            if (row.type === 'Check In') {
                typeClass = 'success';
                typeIcon = '<i class="fas fa-sign-in-alt mr-1"></i>';
            } else if (row.type === 'Check Out') {
                typeClass = 'warning';
                typeIcon = '<i class="fas fa-sign-out-alt mr-1"></i>';
            } else if (row.type === 'Overtime In' || row.type === 'Overtime Out') {
                typeClass = 'danger';
                typeIcon = '<i class="fas fa-clock mr-1"></i>';
            }

            const verifiedBadge = row.verified === 'Verified'
                ? '<span class="badge badge-success">Verified</span>'
                : '<span class="badge badge-secondary">Not Verified</span>';

            tbody.append(`
                <tr>
                    <td class="text-center">${no + 1}</td>
                    <td class="text-center"><span class="badge badge-primary">${row.user_id}</span></td>
                    <td>${row.name || '-'}</td>
                    <td class="text-center">${row.date || '-'}</td>
                    <td class="text-center">${row.time || '-'}</td>
                    <td class="text-center">
                        <span class="badge badge-${typeClass}">
                            ${typeIcon}${row.type || '-'}
                        </span>
                    </td>
                    <td class="text-center">${verifiedBadge}</td>
                    <td>${row.company_name || '-'}</td>
                </tr>
            `);
        });
    }

    // =========================
    //  CLEAR TABLE
    // =========================
    function clearTable() {
        $('#table-body').html('');
        $('#pagination-info').hide();
    }

    // =========================
    //  ALERT HANDLER
    // =========================
    function showAlert(type, message) {
        const alert = $('#status-alert');
        alert.removeClass().addClass(`alert alert-${type}`).html(message).show();
        setTimeout(() => alert.fadeOut(), 5000);
    }

});
</script>

<style>
#attendanceTable {
    font-size: 0.9rem;
}

#attendanceTable th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.badge {
    font-size: 0.8em;
    padding: 0.3em 0.6em;
}
</style>



<?= $this->endSection(); ?>
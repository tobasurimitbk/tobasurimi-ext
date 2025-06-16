<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>HR Outsourcing Salary Payment</h1>
        <div class="section-header-breadcrumb">
            <a class="btn btn-secondary" href="#">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="karyawan-form" role="form" method="POST">
                <input type="hidden" name="id" id="id">
                
                <!-- Header Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Header Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departemen">Departemen</label>
                                    <select class="form-control select2" id="departemen" name="departemen" required>
                                        <option value="">Pilih Department</option>
                                        <?php foreach ($departement as $d): ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company">Perusahaan</label>
                                    <select class="form-control select2" id="company" name="company" required disabled>
                                        <option value="">Pilih Perusahaan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                 <div class="card mb-4">
                    <div class="card-header">
                        <h5>Detail Karyawan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="employee">Karyawan</label>
                                    <select class="form-control select2" id="employee" name="employee">
                                        <option value="">Pilih Karyawan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal_masuk_kerja">Tanggal Masuk Kerja</label>
                                    <input type="date" class="form-control" id="tanggal_masuk_kerja" name="tanggal_masuk_kerja">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions text-right">
                    <button type="button" class="btn btn-secondary mr-2" onclick="resetForm()">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                    <button type="button" class="btn btn-primary btn-submit">
                        <i class="fas fa-plus"></i> Add Data
                    </button>
                </div>
            </form>
            
            <!-- Data Table Section -->
            <div class="row mt-4">
                <div class="row justify-content-end mb-3">
                    <div class="col-md-4">
                        <input class="form-control search form-out-search" placeholder="Cari Nama / No Badge" />
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center" rowspan="3">NO</th>
                                <th class="text-center" rowspan="3">TMK</th>
                                <th class="text-center" rowspan="3">NO BADGE</th>
                                <th class="text-center" rowspan="3">NAMA</th>
                                <th class="text-center" colspan="4">UDANG</th>
                                <th class="text-center" colspan="4">KEPAH</th>
                                <th class="text-center" colspan="4">KPTG</th>
                                <th class="text-center" colspan="4">JAM KERJA</th>
                                <th class="text-center" rowspan="3">TOTAL KG</th>
                                <th class="text-center" rowspan="3">TOTAL JAM</th>
                                <th class="text-center" rowspan="3">JLH ORG</th>
                                <th class="text-center" rowspan="3">(Rp)</th>
                                <th class="text-center" rowspan="3">SUBSIDI RP.</th>
                                <th class="text-center" rowspan="3">BOR. / JAM (Rp)</th>
                                <th class="text-center" colspan="4">KG / JAM</th>
                            </tr>
                            <tr>
                                <th colspan="1">AC</th>
                                <th colspan="1">SK</th>
                                <th colspan="1">MB</th>
                                <th colspan="1">ML</th>
                                <th colspan="1">AC</th>
                                <th colspan="1">SK</th>
                                <th colspan="1">MB</th>
                                <th colspan="1">ML</th>
                                <th colspan="1">AC</th>
                                <th colspan="1">SK</th>
                                <th colspan="1">MB</th>
                                <th colspan="1">ML</th>
                                <th colspan="1">UDANG</th>
                                <th colspan="1">KEPAH</th>
                                <th colspan="1">KPTG</th>
                                <th colspan="1">ML</th>
                                <th colspan="1">AC</th>
                                <th colspan="1">SK</th>
                                <th colspan="1">MB</th>
                                <th colspan="1">ML</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table">
                            <!-- Data rows inserted here -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
// Global variables to store data
var headerData = {
    departemen: null,
    company: null,
};

// Initialize Select2 and event handlers
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: "bootstrap-5",
        allowClear: true
    });

    // Departemen change event
    $('#departemen').on('change', function() {
        var departemen_id = $(this).val();
        $('#company').val(null).trigger('change');
        $('#employee').val(null).trigger('change').prop('disabled', true);
        headerData.departemen = $(this).val();

        if(departemen_id) {
            $('#company').prop('disabled', false);
            
            $.ajax({
                url: '<?= base_url('/hr-outsourcing-sallary-payment/getHrCompanyOutSourcing') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    departemen_id: departemen_id,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    $('#company').empty().append('<option value="">Select Company</option>');
                    $.each(response.data, function(index, item) {
                        $('#company').append('<option value="'+item.id+'">'+item.name+'</option>');
                    });
                }
            });
        } else {
            $('#company').prop('disabled', true);
        }
    });

    // Company change event
    $('#company').on('change', function() {
        var company_id = $(this).val();
        $('#employee').val(null).trigger('change');
        headerData.company = $(this).val();

        if(company_id) {
            $('#employee').prop('disabled', false);
            
            $.ajax({
                url: '<?= base_url('/hr-outsourcing-sallary-payment/getHrEmployeeOutSourcing') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    company_id: company_id,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    $('#employee').empty().append('<option value="">Select Employee</option>');
                    $.each(response.data, function(index, item) {
                        $('#employee').append('<option data-badge="'+item.badge+'" data-tanggal_masuk_kerja="'+item.tanggal_masuk_kerja+'" value="'+item.id+'">'+item.nama+'</option>');
                    });
                }
            });
        } else {
            $('#employee').prop('disabled', true);
        }
    });

    // Employee change event
    $('#employee').on('change', function() {
        let tanggal_masuk_kerja = $(this).find(':selected').data('tanggal_masuk_kerja');
        $('#tanggal_masuk_kerja').val(tanggal_masuk_kerja);
    });

    // Submit button click event
    $('.btn-submit').on('click', function() {
        // Validate header data
        if (!headerData.departemen || !headerData.company) {
            alert('Harap lengkapi data header terlebih dahulu!');
            return;
        }

        // Add data to table
        addToTable();
    });
});

// Function to add data to table
function addToTable() {
    var employeeName = $('#employee option:selected').text();
    var employeeId = $('#employee').val();
    var tanggalMasuk = $('#tanggal_masuk_kerja').val();
    var badge = $('#employee').find(':selected').data('badge');

    var newRow = `
        <tr>
            <td>${$('#dataTable tbody tr').length + 1}</td>w
            <td><input type="text" class="form-control form-control-sm" style="min-width: 120px;" value="${tanggalMasuk}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" style="min-width: 120px;" value="${badge}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" style="min-width: 150px;" data-id="${employeeId}" value="${employeeName}" readonly></td>

            <!-- UDANG -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>

            <!-- KEPAH -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>

            <!-- KPTG -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>

            <!-- JAM KERJA -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>

            <!-- TOTAL & KALKULASI -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 120px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 120px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 120px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 120px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 120px;"></td>

            <!-- KG / JAM -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 90px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 90px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 90px;"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 90px;"></td>
        </tr>
    `;

    $('#dataTable tbody').append(newRow);
    resetForm();
}


// Reset form function
function resetForm() {
    $('#tanggal_masuk_kerja').val('');
    $('#employee').val(null).trigger('change');
}
</script>

<?= $this->endSection(); ?>
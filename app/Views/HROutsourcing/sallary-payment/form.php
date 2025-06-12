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
                        <h5>Employee Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departemen">Department</label>
                                    <select class="form-control select2" id="departemen" name="departemen" required>
                                        <option value="">Select Department</option>
                                        <?php foreach ($departement as $d): ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company">Company</label>
                                    <select class="form-control select2" id="company" name="company" required disabled>
                                        <option value="">Select Company</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="employee">Employee</label>
                                    <select class="form-control select2" id="employee" name="employee" required disabled>
                                        <option value="">Select Employee</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal_masuk_kerja">Employment Date</label>
                                    <input type="date" class="form-control" id="tanggal_masuk_kerja" name="tanggal_masuk_kerja" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Production Data Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Production Data</h5>
                    </div>
                    <div class="card-body">
                        <!-- Shrimp Section -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="udang_ac_2850">Shrimp AC 2,850</label>
                                    <input type="number" step="0.01" class="form-control" id="udang_ac_2850" name="udang_ac_2850">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="udang">Shrimp</label>
                                    <input type="number" step="0.01" class="form-control" id="udang" name="udang">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to_tau_udang">TO TAU Shrimp</label>
                                    <input type="number" step="0.01" class="form-control" id="to_tau_udang" name="to_tau_udang">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Clam Section -->
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kepahi_sk_1800">Clam SK 1,800</label>
                                    <input type="number" step="0.01" class="form-control" id="kepahi_sk_1800" name="kepahi_sk_1800">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kepahi">Clam</label>
                                    <input type="number" step="0.01" class="form-control" id="kepahi" name="kepahi">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to_tau_kepahi">TO TAU Clam</label>
                                    <input type="number" step="0.01" class="form-control" id="to_tau_kepahi" name="to_tau_kepahi">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kg_jan_kepahi">KG/JAN Clam</label>
                                    <input type="number" step="0.01" class="form-control" id="kg_jan_kepahi" name="kg_jan_kepahi">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Crab Section -->
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kptg_mb_10500">Crab MB 10,500</label>
                                    <input type="number" step="0.01" class="form-control" id="kptg_mb_10500" name="kptg_mb_10500">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kptg">Crab</label>
                                    <input type="number" step="0.01" class="form-control" id="kptg" name="kptg">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to_tau_kptg">TO TAU Crab</label>
                                    <input type="number" step="0.01" class="form-control" id="to_tau_kptg" name="to_tau_kptg">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kptg_2">Crab (2)</label>
                                    <input type="number" step="0.01" class="form-control" id="kptg_2" name="kptg_2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Work Summary Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Work Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jan_kerja">Work Days</label>
                                    <input type="number" step="0.01" class="form-control" id="jan_kerja" name="jan_kerja">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jlh">Total</label>
                                    <input type="number" step="0.01" class="form-control" id="jlh" name="jlh">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bp">BP</label>
                                    <input type="number" step="0.01" class="form-control" id="bp" name="bp">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="subsidi_bp_40000">BP Subsidy 40,000</label>
                                    <input type="number" step="0.01" class="form-control" id="subsidi_bp_40000" name="subsidi_bp_40000">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bor_jan_ro_10500">BOR/JAN RO 10,500</label>
                                    <input type="number" step="0.01" class="form-control" id="bor_jan_ro_10500" name="bor_jan_ro_10500">
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
                                <th>NO</th>
                                <th>TMK</th>
                                <th>NO BADGE</th>
                                <th>NAMA</th>
                                <th>UDANG AC 2850</th>
                                <th>KEPAHI SK 1,800</th>
                                <th>KPTG MB 10,500</th>
                                <th>UDANG</th>
                                <th>KEPAHI</th>
                                <th>KPTG</th>
                                <th>JAN KERJA</th>
                                <th>TO TAU UDANG</th>
                                <th>TO TAU KEPAHI</th>
                                <th>TO TAU KPTG</th>
                                <th>JLH</th>
                                <th>BP</th>
                                <th>SUBSIDI BP 40,000</th>
                                <th>BOR/JAN RO 10,500</th>
                                <th>KG/JAN KEPAHI</th>
                                <th>KPTG</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table">
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

var detailData = {
    employee: null,
    tanggal_masuk_kerja: null,
    udang: {},
    kepahi: {},
    kptg: {},
    lainnya: {}
};

// Initialize Select2 and event handlers
$(document).ready(function() {
    // Initialize Select2
    $('#departemen').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#company').select2({
        placeholder: "Pilih Perusahaan",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#employee').select2({
        placeholder: "Pilih Karyawan",
        theme: "bootstrap-5",
        allowClear: true
    });

    // Departemen change event
    $('#departemen').on('change', function() {
        var departemen_id = $(this).val();
        $('#company').val(null).trigger('change');
        $('#employee').val(null).trigger('change').prop('disabled', true);
        headerData.departemen = $(this).find('option:selected').text();

        if(departemen_id) {
            $('#company').prop('disabled', false);
            
            $('#company').on('select2:open', function() {
                var companySelect = $(this);
                if (!companySelect.hasClass('loaded')) {
                    $.ajax({
                        url: '<?= base_url('/hr-outsourcing-sallary-payment/getHrCompanyOutSourcing') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            departemen_id: departemen_id,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        success: function(response) {
                            var options = $.map(response.data, function(item) {
                                return new Option(item.name, item.id, false, false);
                            });
                            companySelect.empty().append(options).trigger('change');
                            companySelect.addClass('loaded');
                        }
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
        headerData.company = $(this).find('option:selected').text();

        if(company_id) {
            $('#employee').prop('disabled', false);
            
            $('#employee').on('select2:open', function() {
                var employeeSelect = $(this);
                if (!employeeSelect.hasClass('loaded')) {
                    $.ajax({
                        url: '<?= base_url('/hr-outsourcing-sallary-payment/getHrEmployeeOutSourcing') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            company_id: company_id,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                        },
                        success: function(response) {
                            var options = $.map(response.data, function(item) {
                                return new Option(item.nama, item.id, false, false);
                            });
                            employeeSelect.empty().append(options).trigger('change');
                            employeeSelect.addClass('loaded');
                        }
                    });
                }
            });
        } else {
            $('#employee').prop('disabled', true);
        }
    });

    // Employee change event
    $('#employee').on('change', function() {
        headerData.employee = $(this).find('option:selected').text();
    });

    // Tanggal Masuk Kerja change event
    $('#tanggal_masuk_kerja').on('change', function() {
        headerData.tanggal_masuk_kerja = $(this).val();
    });

    // Collect detail data when inputs change
    $('input[type="number"]').on('change', function() {
        var id = $(this).attr('id');
        var value = $(this).val();
        
        if (id.includes('udang')) {
            detailData.udang[id] = value;
        } else if (id.includes('kepahi')) {
            detailData.kepahi[id] = value;
        } else if (id.includes('kptg')) {
            detailData.kptg[id] = value;
        } else {
            detailData.lainnya[id] = value;
        }
    });

    // Submit button click event
    $('.btn-submit').on('click', function() {
        // Validate header data
        if (!headerData.departemen || !headerData.company || !headerData.employee || !headerData.tanggal_masuk_kerja) {
            alert('Harap lengkapi data header terlebih dahulu!');
            return;
        }

        // Add data to table
        addToTable();
    });

    // Reset loaded state when parent changes
    $('#departemen').on('change', function() {
        $('#company').removeClass('loaded');
    });

    $('#company').on('change', function() {
        $('#employee').removeClass('loaded');
    });
});

// Function to add data to table
function addToTable() {
    var table = $('#dataTable').DataTable();
    var rowData = [
        table.rows().count() + 1, // NO
        detailData.tanggal_masuk_kerja, // TMK
        $('#employee').val(), // NO BADGE (using employee ID)
        detailData.employee, // NAMA
        detailData.udang.udang_ac_2850 || '', // UDANG AC 2850
        detailData.kepahi.kepahi_sk_1800 || '', // KEPAHI SK 1,800
        detailData.kptg.kptg_mb_10500 || '', // KPTG MB 10,500
        detailData.udang.udang || '', // UDANG
        detailData.kepahi.kepahi || '', // KEPAHI
        detailData.kptg.kptg || '', // KPTG
        detailData.lainnya.jan_kerja || '', // JAN KERJA
        detailData.udang.to_tau_udang || '', // TO TAU UDANG
        detailData.kepahi.to_tau_kepahi || '', // TO TAU KEPAHI
        detailData.kptg.to_tau_kptg || '', // TO TAU KPTG
        detailData.lainnya.jlh || '', // JLH
        detailData.lainnya.bp || '', // BP
        detailData.lainnya.subsidi_bp_40000 || '', // SUBSIDI BP 40,000
        detailData.lainnya.bor_jan_ro_10500 || '', // BOR/JAN RO 10,500
        detailData.kepahi.kg_jan_kepahi || '', // KG/JAN KEPAHI
        detailData.kptg.kptg_2 || '' // KPTG
    ];

    // Add row to table
    table.row.add(rowData).draw();

    // Reset form after submission
    resetForm();
}

// Reset form function
function resetForm() {
    $('.karyawan-form').trigger('reset');
    $('.select2').val(null).trigger('change');
    $('#company, #employee').prop('disabled', true);
    
    // Reset global variables
    headerData = {
        departemen: null,
        company: null,
        employee: null,
        tanggal_masuk_kerja: null
    };
    
    detailData = {
        udang: {},
        kepahi: {},
        kptg: {},
        lainnya: {}
    };
}


    function resetForm() {
        document.querySelector('.karyawan-form').reset();
    }
</script>

<?= $this->endSection(); ?>
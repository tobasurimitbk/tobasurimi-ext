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
                                <th rowspan="3">NO</th>
                                <th rowspan="3">TMK/MASA wwSUBSIDI</th>
                                <th rowspan="3">BET</th>
                                <th rowspan="3">NAMA</th>
                                <th colspan="10">DATA PEKERJAAN (KG)</th>
                                <th rowspan="3">JLH/KG</th>
                                <th rowspan="3">JLH ORG</th>
                                <th rowspan="3">TTL JAM SRT</th>
                                <th rowspan="3">Rp</th>
                                <th rowspan="3">TOTAL Rp /ORG</th>
                                <th colspan="3">SUBSIDI</th>
                                <th colspan="1">Perjam</th>
                                <th rowspan="3">TOTAL KG</th>
                            </tr>
                            <tr>
                                <th rowspan="2">SJB</th>
                                <th rowspan="2">SJL</th>
                                <th rowspan="2">SJBMT</th>
                                <th rowspan="2">S.LEL</th>
                                <th rowspan="2">S.LIM</th>
                                <th rowspan="2">S.SPP</th>
                                <th rowspan="2">S.CM / S.CT</th>
                                <th rowspan="2">S.CCT</th>
                                <th rowspan="2">S.MH</th>
                                <th rowspan="2">S.LEL (Ulang)</th>
                                <th rowspan="2">Kilo 400</th>
                                <th rowspan="2">Kilo 600</th>
                                <th rowspan="2">TOTAL</th>
                                <th rowspan="2">10,500</th>
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

            if (departemen_id) {
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
                            $('#company').append('<option value="' + item.id + '">' + item.name + '</option>');
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

            if (company_id) {
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
                            $('#employee').append('<option data-badge="' + item.badge + '" data-tanggal_masuk_kerja="' + item.tanggal_masuk_kerja + '" value="' + item.id + '">' + item.nama + '</option>');
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
            <td>${$('#dataTable tbody tr').length + 1}</td>
            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${tanggalMasuk}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="22-0002" readonly></td>
            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${employeeName}" data-id="${employeeId}" readonly></td>

            <!-- SJB - S.LEL -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="sjb"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="sjl"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="sjbmt"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="slel"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="slim"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="sspp"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="scm"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="scct"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="smh"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="slel2"></td>

            <!-- JLH/KG, ORG, JAM, RP -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="jlhkg"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="jlh_org"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="ttl_jam"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="rp"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="total_rp_org"></td>

            <!-- Subsidi -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="subsidik400"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="subsidik600"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="subsiditotal"></td>

            <!-- Perjam, Total KG -->
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="perjam"></td>
            <td><input type="number" class="form-control form-control-sm" style="min-width: 100px;" name="total_kg"></td>
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
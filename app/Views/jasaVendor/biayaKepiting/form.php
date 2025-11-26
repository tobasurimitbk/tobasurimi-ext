<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($biayaKepiting) ? "Tambah Biaya Kepiting" : "Update Biaya Kepiting" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("biaya-kepiting"); ?>">
                Kembali
            </a>
            <?php if (!empty($biayaKepiting)) : ?>
                <?php if ($biayaKepiting['status_posting'] == "0") : ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($biayaKepiting['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($biayaKepiting['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("biaya-kepiting/print/"); ?><?= encrypt($biayaKepiting['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>

                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("biaya-kepiting/print/"); ?><?= encrypt($biayaKepiting['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Barang Masuk Vendor</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($biayaKepiting) ? encrypt($biayaKepiting['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($biayaKepiting) ? 'disabled=true' : ''; ?> value="<?= !empty($biayaKepiting) ? $biayaKepiting['no_pembayaran'] : "PAY-KPT/" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_pembayaran" id="no_pembayaran" name="no_pembayaran" placeholder="No. Rebus">
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                                <div style="<?= !empty($biayaKepiting) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <?php
                                        $isDisabled = !empty($biayaKepiting) ? 'disabled' : '';
                                        $tanggalValue = !empty($biayaKepiting)
                                            ? date('d/m/Y', strtotime($biayaKepiting['tanggal']))
                                            : date('d/m/Y');
                                    ?>
                                    <input 
                                        <?= $isDisabled ?> 
                                        autocomplete="one-time-code" 
                                        class="form-control input-picker tanggal" 
                                        id="tanggal" 
                                        name="tanggal" 
                                        placeholder="Tanggal Dibuat" 
                                        value="<?= $tanggalValue; ?>"
                                    >
                                    <label for="tanggal">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($biayaKepiting) ? 'disabled' : '' ?> class="form-select vendor_id" id="vendor_id" name="vendor_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($vendor as $v) : ?>
                                    <option <?= !empty($biayaKepiting) ? ($biayaKepiting['vendor_id'] == $v['id'] ? 'selected' : '') : '' ?> value="<?= $v['id'] ?>">
                                        <?= strtoupper($v['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Vendor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3 form-add-spp" style="height: 50px;">
                            <select class="form-select jasa_vendor_in_id" id="jasa_vendor_in_id" name="jasa_vendor_in_id[]" aria-label="Floating label select example">
                                   
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Nomor Penerimaan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($biayaKepiting) ? $jasaVendorInDetail['divisi'] : '' ?>" autocomplete="one-time-code" disabled type="text" class="form-control divisi" id="divisi" name="divisi" placeholder="Departemen">
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($biayaKepiting) ? ($biayaKepiting['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($biayaKepiting) ? $biayaKepiting['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">List Barang Masuk</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark" id="dynamicHeader">
                                <!-- Header akan diisi secara dinamis -->
                            </thead>
                            <tbody class="body-table">
                                <!-- Data akan diisi secara dinamis -->
                            </tbody>
                            <!-- HAPUS tfoot atau biarkan kosong -->
                            <tfoot style="display: none;">
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Perhitungan Perolehan Gaji</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi" id="dataTable2" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">JENIS</th>
                                    <th style="text-align: center;">JUMBO</th>
                                    <th style="text-align: center;">EX LUMP</th>
                                    <th style="text-align: center;">LUMP</th>
                                    <th style="text-align: center;">SPESIAL</th>
                                    <th style="text-align: center;">CLAW</th>
                                    <th style="text-align: center;">MH</th>
                                    <th style="text-align: center;">CF</th>
                                    <th style="text-align: center;">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="body-table-2">
                                <!-- Data akan diisi secara dinamis -->
                            </tbody>
                            <!-- HAPUS tfoot atau biarkan kosong -->
                            <tfoot style="display: none;">
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Perhitungan Bonus Khusus</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi" id="dataTable3" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="6" class="thead-bonus"><?= !empty($biayaKepiting) ? "Bonus Khusus Untuk Vendor " . $jasaVendorInDetail['name'] : "Bonus Khusus Untuk Vendor" ?></th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tanggal Masuk</th>
                                    <th style="text-align: center;">Barang </th>
                                    <th style="text-align: center;">Kg</th>
                                    <th style="text-align: center;">Bonus</th>
                                    <th style="text-align: center;">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="body-table-3">
                                <!-- Data akan diisi secara dinamis -->
                            </tbody>
                            <!-- HAPUS tfoot atau biarkan kosong -->
                            <tfoot style="display: none;">
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<script>
    // ==================== GLOBAL VARIABLES ====================
const csrfToken = '<?= csrf_token() ?>';
const csrf = $(`[name="${csrfToken}"]`);
let biayaKepiting = <?= json_encode($biayaKepiting); ?>;
var listBarang = [];
var listPerolehanGaji = [];
var listBonus = [];
var listDataVendor = [];

// ==================== INITIALIZATION ====================
$(document).ready(function() {
    initializeSelect2();
    initializeDatePicker();
    
    <?php if (!empty($biayaKepiting)) : ?>
        $('#vendor_id').val('<?= $biayaKepiting['vendor_id'] ?>').trigger('change');
        window.selectedSuratJalanId = biayaKepiting.jasa_vendor_in_kepiting_kukus_id;
        window.isEditMode = true;
        loadExistingData();
    <?php endif; ?>
});

function initializeSelect2() {
    $('#vendor_id').select2({
        placeholder: "Pilih Vendor",
        theme: "bootstrap-5",
        allowClear: true
    }).on('change', handleVendorChange);

    $('.jasa_vendor_in_id').select2({
        placeholder: "Pilih Nomor Penerimaan",
        theme: "bootstrap-5",
        multiple: true,
        allowClear: true,
    }).on('change', handleJasaVendorChange);
}

function initializeDatePicker() {
    $(".tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
}

// ==================== EVENT HANDLERS ====================
function handleVendorChange() {
    const vendorId = $(this).val();
    const $selectSuratJalan = $('.jasa_vendor_in_id');

    if (!vendorId) {
        $selectSuratJalan.empty();
        return;
    }

    $.ajax({
        url: `/biaya-kepiting/get-jasa-vendor-in/${vendorId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $selectSuratJalan.empty();
            
            if (response && response.length > 0) {
                response.forEach(item => {
                    const option = new Option(
                        `${item.no_penerimaan_surat_jalan} - ${item.tanggal}`,
                        item.id,
                        false,
                        false
                    );
                    $selectSuratJalan.append(option);
                });
            } else {
                $selectSuratJalan.append('<option value="">Tidak ada surat jalan</option>');
            }

            // === AUTO SELECT IF EDIT MODE ===
            if (isEditMode && selectedSuratJalanId) {
                $selectSuratJalan.val(selectedSuratJalanId).trigger('change');
            }

        },
        error: function() {
            $selectSuratJalan
                .empty()
                .append('<option value="">Gagal memuat data</option>');
        }
    });
}

function handleJasaVendorChange() {
    var selected = $('.jasa_vendor_in_id option:selected');
    
    if (selected.length > 0) {
        const firstSelected = selected.first();
        $('.vendor').val(firstSelected.data('vendor'));
        $('.divisi').val(firstSelected.data('divisi'));
        $('.thead-bonus').text("Bonus Khusus Untuk " + (firstSelected.data('vendor') || 'Vendor'));
        
        // Load data barang hanya jika ada jasa_vendor_in_id yang dipilih
        listDataBarang();
    } else {
        $('.vendor').val('');
        $('.divisi').val('');
        $('.thead-bonus').text("Bonus Khusus Untuk Vendor");
        
        // Kosongkan list barang jika tidak ada yang dipilih
        listBarang = [];
        drawTableBarang();
    }

    changeStatus();
}

// ==================== DATA LOADING ====================
function loadExistingData() {
    // Untuk mode edit, langsung load data tanpa bergantung jasa_vendor_in_id
    $.ajax({
        url: `<?= base_url('biaya-kepiting/list-barang'); ?>`,
        method: "GET",
        data: {
            id: $('.id').val()
        },
        dataType: "json",
        success: function(res) {
            csrf.val(res.token);
            
            // Reset data dulu
            listBarang = [];
            listPerolehanGaji = [];
            listDataVendor = [];
            listBonus = [];
            
            // Hanya set data jika response sukses
            if (res.status !== false) {
                listBarang = res.data || [];
                listPerolehanGaji = res.dataPerolehanGaji || [];
                listDataVendor = res.dataVendor || [];
                listBonus = res.dataBonus || [];

                drawAllTables();
            }
            
            // Set nilai select2 jika ada data jasa_vendor_in_kepiting_kukus_id
            if (res.biayaKepiting && res.biayaKepiting.jasa_vendor_in_kepiting_kukus_id) {
                const jasaVendorIds = res.biayaKepiting.jasa_vendor_in_kepiting_kukus_id.split(',');
                $('.jasa_vendor_in_id').val(jasaVendorIds).trigger('change');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error loading existing data:", error);
            // Set data kosong
            listBarang = [];
            listPerolehanGaji = [];
            listDataVendor = [];
            listBonus = [];
            alert("Terjadi kesalahan saat memuat data. Silakan refresh halaman.");
        }
    });
}

// ==================== TABLE FUNCTIONS ====================
function listDataBarang() {
    const jasaVendorInId = $(".jasa_vendor_in_id").val();
    
    // Reset data dulu
    listBarang = [];
    listPerolehanGaji = [];
    listDataVendor = [];
    listBonus = [];
    
    // Hanya load data barang jika ada jasa_vendor_in_id yang dipilih
    if (jasaVendorInId && jasaVendorInId.length > 0) {
        $.ajax({
            url: `<?= base_url('biaya-kepiting/list-barang'); ?>`,
            method: "GET",
            data: {
                jasa_vendor_in_id: jasaVendorInId,
                id: $('.id').val()
            },
            dataType: "json",
            success: function(res) {
                csrf.val(res.token);
                
                // Hanya set data jika response sukses
                if (res.status !== false) {
                    listBarang = res.data || [];
                    listPerolehanGaji = res.dataPerolehanGaji || [];
                    listDataVendor = res.dataVendor || [];
                    listBonus = res.dataBonus || [];

                     drawAllTables();
                }
            
            },
            error: function(xhr, status, error) {
                console.error("Error loading barang data:", error);
            }
        });
    }
}

// ==================== TABLE DRAWING ====================
function drawAllTables() {
    // Inisialisasi variabel
    if (!window.listBarang) window.listBarang = {};
    if (!listBarang.data) listBarang.data = [];
    if (!listBarang.thead) listBarang.thead = [];
    if (!window.listPerolehanGaji) window.listPerolehanGaji = [];
    if (!window.listDataVendor) window.listDataVendor = {};
    if (!window.listBonus) window.listBonus = [];

    // Hancurkan DataTables yang ada
    destroyDataTables();

    // Gambar semua tabel
    drawTable1();
    drawTable2();
    drawTable3();

    // Inisialisasi DataTables
    initializeDataTables();
}

function destroyDataTables() {
    ['#dataTable', '#dataTable2', '#dataTable3'].forEach(selector => {
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().destroy();
            $(selector).removeAttr('style');
        }
    });
}

function initializeDataTables() {
    const dtConfig = {
        scrollX: true,
        autoWidth: false,
        responsive: false,
        ordering: false,
        paging: false,
        searching: false,
        info: false,
        destroy: true,
        retrieve: true
    };

    $('#dataTable').DataTable(dtConfig);
    $('#dataTable2').DataTable(dtConfig);
    $('#dataTable3').DataTable(dtConfig);
}

function drawTable1() {
    const $table = $('#dataTable');
    const $tbody = $table.find('.body-table');
    $tbody.empty();

    // Build header
    const $thead = $('#dynamicHeader');
    $thead.empty();
    
    if (!listBarang.thead) listBarang.thead = [];
    const specCount = listBarang.thead.length || 7;

    $thead.append(`
        <tr>
            <th colspan="3"></th>
            <th colspan="2">Kg Bahan Baku</th>
            <th colspan="${specCount}">Hasil Kopek</th>
            <th colspan="1"></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Tanggal Masuk</th>
            <th>Supplier - Keterangan</th>
            <th>Qty Sebelum Kopek</th>
            <th>Rasio (%)</th>
            ${listBarang.thead.map(spec => `<th>${spec}</th>`).join('')}
            ${listBarang.thead.length === 0 ? `
                <th>JUMBO</th><th>EX LUMP</th><th>LUMP</th><th>SPESIAL</th>
                <th>CLAW</th><th>MH</th><th>CF</th>
            ` : ''}
            <th>TOTAL</th>
        </tr>
    `);

    if (!listBarang.data || listBarang.data.length === 0) {
        $tbody.append(`<tr><td colspan="${5 + specCount}" style="text-align: center;">Tidak Ada Barang</td></tr>`);
        return;
    }

    const totalPerSpek = {};
    let qtyKopekTotal = 0;
    let totalTotal = 0;

    // Init total per spec
    (listBarang.thead.length > 0 ? listBarang.thead : ['JUMBO','EX LUMP','LUMP','SPESIAL','CLAW','MH','CF']).forEach(spec => {
        totalPerSpek[spec] = 0;
    });

    // Process data
    listBarang.data.forEach((v, i) => {
        let total = 0;
        if (v.spek) {
            Object.values(v.spek).forEach(value => total += parseFloat(value || 0));
        }
        
        const rasio = total === 0 ? 0 : ((total / parseFloat(v.qty_sebelum_kopek)) * 100).toFixed(2);
        const specs = listBarang.thead.length > 0 ? listBarang.thead : ['JUMBO','EX LUMP','LUMP','SPESIAL','CLAW','MH','CF'];
        
        let rowHTML = `
            <tr>
                <td style="text-align: center;">${i + 1}</td>
                <td style="text-align: center;">${v.tanggal_masuk}</td>
                <td style="text-align: center;">${v.supplier || ''} - ${v.keterangan || ''}</td>
                <td style="text-align: center;">${parseFloat(v.qty_sebelum_kopek || 0).toFixed(2)}</td>
                <td style="text-align: center;">${rasio} %</td>
        `;

        specs.forEach(spec => {
            const value = v.spek && v.spek[spec] ? v.spek[spec] : 0;
            totalPerSpek[spec] += parseFloat(value || 0);
            
            rowHTML += `
                <td style="text-align: center;">
                    <input id="${i+'_'+spec}" 
                        style="height: 40px; padding: 5px; min-width: 100px;" 
                        class="form-control ${spec.toLowerCase().replace(/\s+/g, '_')}" 
                        oninput="preventNegativeInput(this); calculateAll()" 
                        autocomplete="off" 
                        type="text" 
                        value="${value}">
                </td>
            `;
        });

        rowHTML += `<td style="text-align: center;">${total.toFixed(3)}</td></tr>`;
        $tbody.append(rowHTML);

        totalTotal += total;
        qtyKopekTotal += parseFloat(v.qty_sebelum_kopek || 0);
    });

    // Grand total row
    const totalRasio = qtyKopekTotal === 0 ? 0 : ((totalTotal / qtyKopekTotal) * 100).toFixed(2);
    const specs = listBarang.thead.length > 0 ? listBarang.thead : ['JUMBO','EX LUMP','LUMP','SPESIAL','CLAW','MH','CF'];
    
    let totalRow = `
        <tr class="bg-total">
            <td style="text-align: center;"><b>TOTAL</b></td>
            <td></td><td></td>
            <td style="text-align: center;">${qtyKopekTotal.toFixed(2)}</td>
            <td style="text-align: center;">${totalRasio} %</td>
    `;

    specs.forEach(spec => {
        totalRow += `<td style="text-align: center;">${(totalPerSpek[spec] || 0).toFixed(2)}</td>`;
    });

    totalRow += `<td style="text-align: center;">${totalTotal.toFixed(3)}</td></tr>`;
    $tbody.append(totalRow);

    // Simpan data untuk perhitungan
    window.currentTotals = { totalPerSpek, totalTotal };
}

function drawTable2() {
    const $table = $('#dataTable2');
    const $tbody = $table.find('tbody');
    $tbody.empty();

    if (!listPerolehanGaji || listPerolehanGaji.length === 0) {
        $tbody.append('<tr><td colspan="9" style="text-align: center;">Tidak Ada Data</td></tr>');
        return;
    }

    // Column mapping yang benar
    const columns = ['jumbo', 'ex_lump', 'lump', 'special', 'claw', 'mh', 'cf'];
    const columnLabels = ['JUMBO', 'EX LUMP', 'LUMP', 'SPESIAL', 'CLAW', 'MH', 'CF'];

    // Draw rows untuk setiap jenis perolehan gaji
    listPerolehanGaji.forEach((item, index) => {
        let total = 0;
        let rowHTML = `<tr><td>${item.description || ''}</td>`;

        // Loop melalui setiap column
        columns.forEach(col => {
            const value = parseFloat(item[col] || 0);
            total += value;
            
            rowHTML += `
                <td style="text-align: center;">
                    <input id="gaji_${index}_${col}" 
                        class="form-control gaji-input ${col}" 
                        type="text" 
                        value="${value}"
                        oninput="calculateAll()">
                </td>
            `;
        });

        rowHTML += `<td>${formatRupiah(total.toFixed(2))}</td></tr>`;
        $tbody.append(rowHTML);
    });

    // Calculate totals setelah draw
    calculateTable2Totals();
}

function drawTable3() {
    const $tbody = $('#dataTable3 tbody');
    $tbody.empty();

    if (listBonus.length === 0) {
        $tbody.append('<tr><td colspan="6" style="text-align: center;">Tidak Ada Data Bonus</td></tr>');
        return;
    }

    const vendorData = listDataVendor || {};
    let totalBonusResult = 0;

    listBonus.forEach((v, i) => {
        // Set bonus nominal from vendor data
        if (v.spesifikasi === "JB") v.bonus_nominal = vendorData.bonus_karyawan_jb || v.bonus_nominal || 0;
        else if (v.spesifikasi === "SP LUMP") v.bonus_nominal = vendorData.bonus_karyawan_xl || v.bonus_nominal || 0;
        else if (v.spesifikasi === "BF") v.bonus_nominal = vendorData.bonus_karyawan_lp || v.bonus_nominal || 0;
        else if (v.spesifikasi === "SPL") v.bonus_nominal = vendorData.bonus_karyawan_sp || v.bonus_nominal || 0;
        else if (v.spesifikasi === "CLAW") v.bonus_nominal = vendorData.bonus_karyawan_cl || v.bonus_nominal || 0;
        else if (v.spesifikasi === "MH") v.bonus_nominal = vendorData.bonus_karyawan_mh || v.bonus_nominal || 0;
        else if (v.spesifikasi === "CF") v.bonus_nominal = vendorData.bonus_karyawan_cf || v.bonus_nominal || 0;
        
        const totalBonus = parseFloat(v.kg_bonus || 0) * parseFloat(v.bonus_nominal || 0);
        totalBonusResult += totalBonus;

        const rowHTML = `
            <tr>
                <td style="text-align: center;">${i + 1}</td>
                <td style="text-align: center;">${v.tanggal_masuk}</td>
                <td style="text-align: center;">${v.nama_barang || ''}</td>
                <td style="text-align: center;">
                    <input class="form-control kg_bonus" type="text" value="${v.kg_bonus || 0}" oninput="calculateBonus()">
                </td>
                <td style="text-align: center;">
                    <input class="form-control bonus_nominal" type="text" value="${v.bonus_nominal || 0}" oninput="calculateBonus()">
                </td>
                <td>${formatRupiah(totalBonus.toFixed(2))}</td>
            </tr>
        `;
        $tbody.append(rowHTML);
    });

    // Grand total row
    $tbody.append(`
        <tr style="background-color:#f2c996;">
            <td style="text-align:center;"><b>GRAND TOTAL</b></td>
            <td></td><td></td><td></td><td></td>
            <td>${formatRupiah(totalBonusResult.toFixed(2))}</td>
        </tr>
    `);
}

// ==================== CALCULATION FUNCTIONS ====================
function calculateAll() {
    updateTable1Totals();
    calculateTable2Totals();
}

function calculateTable2Totals() {
    if (!window.currentTotals) return;
    
    const { totalPerSpek, totalTotal } = window.currentTotals;
    const $tbody = $('#dataTable2 tbody');
    
    // Clear existing total rows
    $tbody.find('tr').filter(':contains("TOTAL")').remove();
    $tbody.find('tr').filter(':contains("PRESENTASE")').remove();
    $tbody.find('tr').filter(':contains("GRAND TOTAL")').remove();

    // Column mapping
    const columns = ['jumbo', 'ex_lump', 'lump', 'special', 'claw', 'mh', 'cf'];
    const columnLabels = ['JUMBO', 'EX LUMP', 'LUMP', 'SPESIAL', 'CLAW', 'MH', 'CF'];
    
    // Mapping antara nama kolom dan spek dari table 1
    const spekMapping = {
        'jumbo': 'JB',
        'ex_lump': 'SP LUMP', 
        'lump': 'LUMP',
        'special': 'SPL',
        'claw': 'CLAW',
        'mh': 'MH',
        'cf': 'CF'
    };

    // Get current values dari input
    const currentValues = {};
    listPerolehanGaji.forEach((item, index) => {
        currentValues[item.value] = {};
        columns.forEach(col => {
            const inputVal = $(`#gaji_${index}_${col}`).val();
            currentValues[item.value][col] = parseFloat(inputVal) || 0;
        });
    });

    // TOTAL PER KATEGORI
    Object.keys(currentValues).forEach(kategori => {
        const kategoriName = listPerolehanGaji.find(item => item.value === kategori)?.description || kategori;
        let totalKategori = 0;
        let rowHTML = `<tr style="background-color:#f2c996;"><td style="text-align: center;"><b>TOTAL ${kategoriName.toUpperCase()}</b></td>`;

        columns.forEach(col => {
            const harga = currentValues[kategori][col] || 0;
            const spekKey = spekMapping[col];
            const qty = totalPerSpek[spekKey] || 0;
            const totalSpec = harga * qty;
            totalKategori += totalSpec;
            rowHTML += `<td>${totalSpec !== 0 ? formatRupiah(totalSpec.toFixed(2)) : '0'}</td>`;
        });

        rowHTML += `<td>${formatRupiah(totalKategori.toFixed(2))}</td></tr>`;
        $tbody.append(rowHTML);
    });

    // PRESENTASE KOPEK
    let totalPresentase = 0;
    let presentaseRow = `<tr style="background-color:#f2c996;"><td style="text-align: center;"><b>PRESENTASE KOPEK</b></td>`;
    
    columnLabels.forEach((label, index) => {
        const spekKey = spekMapping[columns[index]];
        const qty = totalPerSpek[spekKey] || 0;
        const presentase = totalTotal ? (qty * 100 / totalTotal) : 0;
        totalPresentase += presentase;
        presentaseRow += `<td>${presentase.toFixed(2)} %</td>`;
    });
    
    presentaseRow += `<td>${totalPresentase.toFixed(2)} %</td></tr>`;
    $tbody.append(presentaseRow);

    // GRAND TOTAL
    const grandTotalPerCol = {};
    let grandTotalUpahKopek = 0;
    
    columns.forEach(col => grandTotalPerCol[col] = 0);
    
    Object.keys(currentValues).forEach(kategori => {
        columns.forEach(col => {
            const harga = currentValues[kategori][col] || 0;
            const spekKey = spekMapping[col];
            const qty = totalPerSpek[spekKey] || 0;
            grandTotalPerCol[col] += (harga * qty);
        });
    });
    
    grandTotalUpahKopek = Object.values(grandTotalPerCol).reduce((sum, val) => sum + val, 0);
    
    let grandTotalRow = `<tr style="background-color:#c7922f; font-weight:bold;"><td style="text-align:center;"><b>GRAND TOTAL UPAH KOPEK</b></td>`;
    
    columns.forEach(col => {
        grandTotalRow += `<td>${formatRupiah(grandTotalPerCol[col].toFixed(2))}</td>`;
    });
    
    grandTotalRow += `<td>${formatRupiah(grandTotalUpahKopek.toFixed(2))}</td></tr>`;
    $tbody.append(grandTotalRow);
}

function updateTable1Totals() {
    const $tbody = $('#dataTable tbody');
    let qtyKopekTotal = 0;
    let totalTotal = 0;
    const totalPerSpek = {};
    const specs = listBarang.thead.length > 0 ? listBarang.thead : ['JUMBO','EX LUMP','LUMP','SPESIAL','CLAW','MH','CF'];

    // Init totals
    specs.forEach(spec => totalPerSpek[spec] = 0);

    // Calculate new totals from inputs
    $tbody.find('tr').not('.bg-total').each(function() {
        const $row = $(this);
        if ($row.find('td').first().text().includes('Tidak Ada Barang')) return;

        const qty = parseFloat($row.find('td').eq(3).text()) || 0;
        qtyKopekTotal += qty;

        let rowTotal = 0;
        specs.forEach((spec, index) => {
            const inputVal = $row.find(`td:eq(${5 + index}) input`).val();
            const value = parseFloat(inputVal) || 0;
            totalPerSpek[spec] += value;
            rowTotal += value;
        });

        // Update row total
        $row.find('td:last').text(rowTotal.toFixed(3));
        totalTotal += rowTotal;
    });

    // Update grand total row
    const $totalRow = $tbody.find('.bg-total');
    const totalRasio = qtyKopekTotal === 0 ? 0 : ((totalTotal / qtyKopekTotal) * 100).toFixed(2);
    
    $totalRow.find('td').eq(3).text(qtyKopekTotal.toFixed(2));
    $totalRow.find('td').eq(4).text(totalRasio + ' %');
    
    specs.forEach((spec, index) => {
        $totalRow.find('td').eq(5 + index).text((totalPerSpek[spec] || 0).toFixed(2));
    });
    
    $totalRow.find('td:last').text(totalTotal.toFixed(3));

    // Save for table 2 calculations
    window.currentTotals = { totalPerSpek, totalTotal };
}

function calculateBonus() {
    let totalBonusResult = 0;
    const $tbody = $('#dataTable3 tbody');

    $tbody.find('tr').each(function() {
        const $row = $(this);
        if ($row.find('.kg_bonus').length > 0) {
            const kg = parseFloat($row.find('.kg_bonus').val()) || 0;
            const bonus = parseFloat($row.find('.bonus_nominal').val()) || 0;
            const total = kg * bonus;
            totalBonusResult += total;
            $row.find('td:last').text(formatRupiah(total.toFixed(2)));
        }
    });

    // Update grand total
    $tbody.find('tr:last td:last').text(formatRupiah(totalBonusResult.toFixed(2)));
}

// ==================== SUBMIT FUNCTIONS ====================
function submitBiayaKepiting() {
    if (!validateForm()) return;

    calculateAll();
    calculateBonus();

    const data = collectAllData();
    
    // Tentukan URL berdasarkan mode (create/update)
    const isEditMode = $('.id').val() !== '';
    const url = isEditMode ? '<?= base_url("biaya-kepiting/update"); ?>' : '<?= base_url("biaya-kepiting/save"); ?>';
    
    Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda yakin ingin ${isEditMode ? 'mengupdate' : 'menyimpan'} data biaya kepiting ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `Ya, ${isEditMode ? 'Update' : 'Simpan'}!`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            sendDataToServer(data, url);
        }
    });
}

function validateForm() {
    const noPembayaran = $('#no_pembayaran').val();
    const tanggal = $('#tanggal').val();
    const jasaVendorInId = $('#jasa_vendor_in_id').val();
    
    if (!noPembayaran) {
        Swal.fire('Peringatan', 'No. Pembayaran harus diisi', 'warning');
        return false;
    }
    
    if (!tanggal) {
        Swal.fire('Peringatan', 'Tanggal harus diisi', 'warning');
        return false;
    }
    
    if (!jasaVendorInId) {
        Swal.fire('Peringatan', 'Jasa Vendor harus dipilih', 'warning');
        return false;
    }
    
    if (!listBarang.data || listBarang.data.length === 0) {
        Swal.fire('Peringatan', 'Data barang tidak boleh kosong', 'warning');
        return false;
    }
    
    return true;
}

function collectAllData() {
    return {
        listBarang: collectBarangData(),
        listPerolehanGaji: collectGajiData(),
        listBonus: collectBonusData(),
        no_pembayaran: $('#no_pembayaran').val(),
        tanggal: $('#tanggal').val(),
        keterangan: $('#keterangan').val(),
        jasa_vendor_in_id: $('#jasa_vendor_in_id').val()
    };
}

function collectBarangData() {
    const data = [];
    const specs = listBarang.thead.length > 0 ? listBarang.thead : ['JUMBO','EX LUMP','LUMP','SPESIAL','CLAW','MH','CF'];
    
    $('#dataTable tbody tr').not('.bg-total').each(function() {
        const row = $(this);
        if (row.find('td').first().text().includes('Tidak Ada Barang')) return;
        
        const barangData = {
            tanggal_masuk: row.find('td:eq(1)').text(),
            supplier_keterangan: row.find('td:eq(2)').text(),
            supplier_id: row.data('supplier_id'),
            keterangan: row.data('keterangan'),
            master_barang_id: row.data('master_barang_id'),
            qty_sebelum_kopek: parseFloat(row.find('td:eq(3)').text()) || 0,
            rasio: parseFloat(row.find('td:eq(4)').text()) || 0,
            spek: {}
        };
        
        specs.forEach((spec, index) => {
            const inputValue = row.find(`td:eq(${5 + index}) input`).val();
            barangData.spek[spec] = parseFloat(inputValue) || 0;
        });
        
        const rowIndex = row.index();
        if (listBarang.data && listBarang.data[rowIndex]) {
            barangData.barang_master_id = listBarang.data[rowIndex].barang_master_id;
            barangData.barang_master_spesifikasi_id = listBarang.data[rowIndex].barang_master_spesifikasi_id;
        }
        
        data.push(barangData);
    });
    
    return data;
}

function collectGajiData() {
    const data = [];
    const columns = ['jumbo', 'ex_lump', 'lump', 'special', 'claw', 'mh', 'cf'];
    
    $('#dataTable2 tbody tr').each(function() {
        const row = $(this);
        const firstCell = row.find('td:first').text().trim();
        
        if (firstCell.includes('TOTAL') || firstCell.includes('PRESENTASE') || 
            firstCell.includes('GRAND TOTAL') || firstCell === 'Tidak Ada Data') return;
        
        const gajiType = listPerolehanGaji.find(item => 
            item.description === firstCell
        );
        
        if (!gajiType) return;
        
        const gajiData = {
            description: firstCell,
            value: gajiType.value,
            jumbo: 0, ex_lump: 0, lump: 0, special: 0, claw: 0, mh: 0, cf: 0
        };
        
        columns.forEach((col, index) => {
            const inputValue = row.find(`td:eq(${index + 1}) input`).val();
            const value = parseFloat(inputValue) || 0;
            gajiData[col] = value;
        });
        
        data.push(gajiData);
    });
    
    return data;
}

function collectBonusData() {
    const data = [];
    
    $('#dataTable3 tbody tr').not(':last').each(function() {
        const row = $(this);
        if (row.find('td').first().text().includes('Tidak Ada Data Bonus')) return;
        
        const bonusData = {
            tanggal_masuk: row.find('td:eq(1)').text(),
            nama_barang: row.find('td:eq(2)').text(),
            kg_bonus: parseFloat(row.find('td:eq(3) input').val()) || 0,
            bonus_nominal: parseFloat(row.find('td:eq(4) input').val()) || 0
        };
        
        const rowIndex = row.index();
        if (listBonus && listBonus[rowIndex]) {
            bonusData.barang_master_id = listBonus[rowIndex].barang_master_id;
            bonusData.barang_master_spesifikasi_id = listBonus[rowIndex].barang_master_spesifikasi_id;
            bonusData.spesifikasi = listBonus[rowIndex].spesifikasi;
        }
        
        data.push(bonusData);
    });
    
    return data;
}

// Ubah sendDataToServer untuk menerima URL parameter
function sendDataToServer(data, url) {
    Swal.fire({
        title: `${$('.id').val() ? 'Mengupdate' : 'Menyimpan'} Data`,
        text: 'Sedang memproses data, harap tunggu...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    // Pastikan jasa_vendor_in_id adalah array
    const jasaVendorInId = Array.isArray(data.jasa_vendor_in_id) 
        ? data.jasa_vendor_in_id 
        : [data.jasa_vendor_in_id];
    
    const formData = {
        listBarang: JSON.stringify(data.listBarang),
        listPerolehanGaji: JSON.stringify(data.listPerolehanGaji),
        listBonus: JSON.stringify(data.listBonus),
        no_pembayaran: data.no_pembayaran,
        tanggal: data.tanggal,
        keterangan: data.keterangan,
        jasa_vendor_in_id: jasaVendorInId
    };
    
    // Jika edit mode, tambahkan ID
    if ($('.id').val()) {
        formData.id = $('.id').val();
    }
    
    console.log('Data yang dikirim:', formData); // Debug
    
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        traditional: true,
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
        },
        data: formData,
        success: function(response) {
            Swal.close();
            
            if (response.status) {
                Swal.fire({
                    title: 'Sukses',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '/biaya-kepiting/';
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
        }
    });
}

// ==================== HELPER FUNCTIONS ====================
function getValueFromDescription(description) {
    const mapping = {
        'Upah Kopek': 'upah_kopek',
        'Komisi / Kg Daging': 'komisi_kg_daging',
        'Bonus / Kg Daging': 'bonus_kg_daging',
        'Tamb. Upah Kopek': 'tamb_upah_kopek'
    };
    return mapping[description] || description.toLowerCase().replace(/\s+/g, '_');
}

function formatRupiah(angka) {
    if (!angka) return 'Rp 0';
    const number_string = angka.toString().replace(/[^,\d]/g, '');
    const split = number_string.split(',');
    let rupiah = split[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return 'Rp ' + rupiah;
}

function preventNegativeInput(input) {
    if (parseFloat(input.value) < 0) input.value = 0;
}

function changeStatus() {
    let value = document.getElementById('auto_generate').checked ? true : false;
    if (value) {
        $(".no_pembayaran").attr("readonly", true);
        $.ajax({
            url: `<?= base_url("biaya-kepiting/get-no"); ?>`,
            method: "GET",
            data: { warehouse_id: $('#jasa_vendor_in_id option:selected').data('warehouse_id') },
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $(".no_pembayaran").val(res.data);
                } else {
                    Swal.fire({ icon: 'error', title: res.message, confirmButtonColor: '#4e73df' });
                    $(".no_pembayaran").attr("readonly", false);
                    $("#auto_generate").prop("checked", false);
                    $(".no_pembayaran").val("");
                }
            }
        })
    } else {
        $(".no_pembayaran").attr("readonly", false);
        $(".no_pembayaran").val("");
    }
}

// ==================== EVENT BINDINGS ====================
$(document).on('click', '.btn-submit-parent', function(e) {
    e.preventDefault();
    submitBiayaKepiting();
});
</script>

<?= $this->endSection(); ?>
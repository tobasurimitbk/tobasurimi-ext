<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Import Po Lokal BB</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-lokal-bahan-baku"); ?>">
                Kembali
            </a>
            <?php if (can("Pembelian", "PO Lokal BB", "c")) : ?>
                <button disabled class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Import Data
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="form-excel">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-12">
                        <input autocomplete="one-time-code" type="file" class="form-control file" id="file" name="file">

                    </div>
                </div>
            </form>
            <br>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Data</label>
                    </div>
                    <div class="col-md-6">

                        <button class="btn btn-show-detail btn-add btn-block btn-submit-preview float-right">
                            Preview
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Supplier</th>
                                <th rowspan="2">No PO</th>
                                <th rowspan="2">Tgl PO</th>
                                <th rowspan="2">Bahan Baku</th>
                                <th rowspan="2">Peti/Cong</th>
                                <th rowspan="2">Size</th>
                                <th rowspan="2">Department</th>
                                <th rowspan="2">Gudang</th>
                                <th rowspan="2">Qty</th>
                                <th rowspan="2">Satuan</th>
                                <th rowspan="2">Unit</th>
                                <th colspan="3" style="text-align: center;">Harian</th>
                                <th colspan="3" style="text-align: center;">Tambahan Harian</th>
                                <th colspan="3" style="text-align: center;">Tambahan Bulanan</th>
                                <th colspan="3" style="text-align: center;">Tambahan Langsung</th>
                                <th rowspan="2">Total</th>
                                <th rowspan="2">Status</th>
                                <th rowspan="2">Pesan Error</th>
                            </tr>
                            <tr>
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                            </tr>
                        </thead>

                        <tbody id="body-detail-table"></tbody>

                        <tfoot id="foot-detail-table">
                            <tr>
                                <td colspan="27" class="text-left">Tidak ada data</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var dataImported = [];


    $('.btn-submit-parent').click(function(e) {
        e.preventDefault();
        if (dataImported.length == 0) {
            Swal.fire({
                icon: 'error',
                title: "List yang akan di import masih kosong",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            Swal.fire({
                icon: 'question',
                title: 'Import Data ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var data = new FormData();
                    data.append("list_data", JSON.stringify(dataImported));
                    $.ajax({
                        url: "<?= base_url("po-lokal-bahan-baku/import-data"); ?>",
                        data: data,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
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
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                }).then((result) => {
                                    location.reload();
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                                return;
                            }
                        }
                    });
                }
            })
        }

    });

    $('.btn-submit-preview').click(function() {
        let file = $('#file').val();

        if (file == null || file == "") {
            Swal.fire({
                icon: 'error',
                title: "File input kosong",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            let csrf = $(`[name="${csrfToken}"]`);
            let formData = new FormData(document.querySelector(".form-excel"));
            $.ajax({
                url: "<?= base_url("po-lokal-bahan-baku/import-preview"); ?>",
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    if (response.status) {
                        dataImported = response.data.dataImported;
                        dataPreview = response.data.dataPreview;
                        drawTable(dataPreview);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        });
                        $('#file').val(null).change();
                    }
                },
            });
        }

    });

    function drawTable(dataPreview) {
        const tbody = $('#body-detail-table');
        const tfoot = $('#foot-detail-table');

        tbody.empty();
        tfoot.empty();

        let no = 1;
        let isOke = true;

        // =========================
        // TOTAL INIT
        // =========================
        let total = {
            dpp_umum: 0,
            pph_umum: 0,
            nilai_total_umum: 0,

            dpp_harian: 0,
            pph_harian: 0,
            nilai_total_harian: 0,

            dpp_bulanan: 0,
            pph_bulanan: 0,
            nilai_total_bulanan: 0,

            dpp_tambahan: 0,
            pph_tambahan: 0,
            nilai_total_tambahan: 0,

            grand_total: 0
        };

        if (!dataPreview || dataPreview.length === 0) {
            tfoot.append(`
            <tr>
                <td colspan="27" class="text-left">Tidak Ada Data</td>
            </tr>
        `);
            return;
        }

        $.each(dataPreview, function(i, v) {

            if (!v.status) isOke = false;

            let row = $('<tr>');

            if (!v.status) {
                row.addClass('table-danger');
            }

            row.append(`<td>${no++}</td>`);
            row.append(`<td>${v.supplier_name || '-'}</td>`);
            row.append(`<td>${v.po_no || '-'}</td>`);
            row.append(`<td>${v.po_date || '-'}</td>`);
            row.append(`<td>${v.barang_name || '-'}</td>`);
            row.append(`<td>${v.peti || '-'}</td>`);
            row.append(`<td>${v.size || '-'}</td>`);
            row.append(`<td>${v.divisi || '-'}</td>`);
            row.append(`<td>${v.warehouse_name || '-'}</td>`);
            row.append(`<td class="text-end">${greatFormatRupiah(v.qty)}</td>`);
            row.append(`<td>${v.kode_satuan || '-'}</td>`);

            // =========================
            // UNIT PRICE
            // =========================
            row.append(`<td class="text-end">${v.company_name}</td>`);

            // =========================
            // UMUM
            // =========================
            row.append(`<td class="text-end">${greatFormatRupiah(v.dpp_umum)}</td>`);
            row.append(`<td class="text-end">${greatFormatRupiah(v.pph_umum)}</td>`);
            row.append(`<td class="text-end">${greatFormatRupiah(v.nilai_total_umum)}</td>`);

            // =========================
            // HARIAN
            // =========================
            row.append(`<td class="text-end">${greatFormatRupiah(v.dpp_harian)}</td>`);
            row.append(`<td class="text-end">${greatFormatRupiah(v.pph_harian)}</td>`);
            row.append(`<td class="text-end">${greatFormatRupiah(v.nilai_total_harian)}</td>`);

            // =========================
            // BULANAN
            // =========================
            row.append(`<td class="text-end">${greatFormatRupiah(v.dpp_bulanan)}</td>`);
            row.append(`<td class="text-end">${greatFormatRupiah(v.pph_bulanan)}</td>`);
            row.append(`<td class="text-end">${greatFormatRupiah(v.nilai_total_bulanan)}</td>`);

            // =========================
            // TAMBAHAN
            // =========================
            row.append(`<td class="text-end">${greatFormatRupiah(v.dpp_tambahan)}</td>`);
            row.append(`<td class="text-end">${greatFormatRupiah(v.pph_tambahan)}</td>`);
            row.append(`<td class="text-end">${greatFormatRupiah(v.nilai_total_tambahan)}</td>`);

            // =========================
            // TOTAL
            // =========================
            row.append(`<td class="text-end fw-bold">${greatFormatRupiah(v.nilai_total.toFixed(2))}</td>`);

            // =========================
            // STATUS
            // =========================
            row.append(`
            <td class="text-center">
                ${v.status 
                    ? '<span class="text-success"><i class="fa fa-check"></i></span>' 
                    : '<span class="text-danger"><i class="fa fa-times"></i></span>'}
            </td>
        `);

            row.append(`<td>${v.message || ''}</td>`);

            tbody.append(row);

            // =========================
            // AKUMULASI TOTAL
            // =========================
            total.dpp_umum += v.dpp_umum || 0;
            total.pph_umum += v.pph_umum || 0;
            total.nilai_total_umum += v.nilai_total_umum || 0;

            total.dpp_harian += v.dpp_harian || 0;
            total.pph_harian += v.pph_harian || 0;
            total.nilai_total_harian += v.nilai_total_harian || 0;

            total.dpp_bulanan += v.dpp_bulanan || 0;
            total.pph_bulanan += v.pph_bulanan || 0;
            total.nilai_total_bulanan += v.nilai_total_bulanan || 0;

            total.dpp_tambahan += v.dpp_tambahan || 0;
            total.pph_tambahan += v.pph_tambahan || 0;
            total.nilai_total_tambahan += v.nilai_total_tambahan || 0;

            total.grand_total += v.nilai_total || 0;
        });

        // =========================
        // FOOTER TOTAL
        // =========================
        tfoot.append(`
        <tr class="table-secondary fw-bold">
            <td colspan="12" class="text-end">TOTAL</td>

            <td class="text-end">${greatFormatRupiah(total.dpp_umum.toFixed(2))}</td>
            <td class="text-end">${greatFormatRupiah(total.pph_umum.toFixed(2))}</td>
            <td class="text-end">${greatFormatRupiah(total.nilai_total_umum.toFixed(2))}</td>

            <td class="text-end">${greatFormatRupiah(total.dpp_harian.toFixed(2))}</td>
            <td class="text-end">${greatFormatRupiah(total.pph_harian.toFixed(2))}</td>
            <td class="text-end">${greatFormatRupiah(total.nilai_total_harian.toFixed(2))}</td>

            <td class="text-end">${greatFormatRupiah(total.dpp_bulanan.toFixed(2))}</td>
            <td class="text-end">${greatFormatRupiah(total.pph_bulanan.toFixed(2))}</td>
            <td class="text-end">${greatFormatRupiah(total.nilai_total_bulanan.toFixed(2))}</td>

            <td class="text-end">${greatFormatRupiah(total.dpp_tambahan.toFixed(2))}</td>
            <td class="text-end">${greatFormatRupiah(total.pph_tambahan.toFixed(2))}</td>
            <td class="text-end">${greatFormatRupiah(total.nilai_total_tambahan.toFixed(2))}</td>

            <td class="text-end">${greatFormatRupiah(total.grand_total.toFixed(2))}</td>

            <td colspan="2"></td>
        </tr>
    `);

        // =========================
        // BUTTON CONTROL
        // =========================
        $('.btn-submit-parent').prop('disabled', !isOke);
    }
</script>

<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Import Stok Inisiasi</h1>
        <?php if (can("Inventori", "Stok List", "c")) : ?>
            <div class="col-button-tambah-spp">
                <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-list/create"); ?>">
                    Kembali
                </a>
                <button disabled class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Import Data
                </button>
            </div>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="form-excel">
                <?= csrf_field() ?>
                <div class="form-floating mt-3" style="height: 50px;">
                    <input autocomplete="one-time-code" type="file" class="form-control file" id="file" name="file">
                </div>
            </form>
            <br>
            <a href="<?= base_url('assets/import/format_import_inventori.xls') ?>">Unduh Template Import</a>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Inisiasi Stok</label>
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
                                <th>No</th>
                                <th>Tipe Barang</th>
                                <th>Dept</th>
                                <th>Warehouse</th>
                                <th>Kode</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Tgl Stok</th>
                                <th>No Aju (Opsional)</th>
                                <th>No Daftar (Opsional)</th>
                                <th>Supplier (Opsional)</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td style="text-align: left;" colspan="15">
                                    Tidak ada data
                                </td>
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

    var listStock = [];

    $('.btn-submit-parent').click(function(e) {
        e.preventDefault();
        if (listStock.length == 0) {
            Swal.fire({
                icon: 'error',
                title: "List yang akan di import masih kosong",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            Swal.fire({
                icon: 'question',
                title: 'Import Stok ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var data = new FormData();
                    data.append("list_stock", JSON.stringify(listStock));
                    $.ajax({
                        url: "<?= base_url("stock-list/import"); ?>",
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
                                    window.location.href = "<?= base_url("stock-list"); ?>";
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
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
                title: "Inputan tidak boleh kosong",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            let csrf = $(`[name="${csrfToken}"]`);
            let formData = new FormData(document.querySelector(".form-excel"));
            $.ajax({
                url: "<?= base_url("stock-list/import-preview"); ?>",
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
                        listStock = response.data.dataResult;
                        listPreview = response.data.dataPreview;
                        drawTable(listPreview);
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

    function drawTable(listPreview) {
        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();
        var no = 1;
        if (listPreview.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td  colspan="15">').text("Tidak Ada Data"));
            table.find('tfoot').append(newRow);
        } else {
            var isOke = true;
            $.each(listPreview, function(i, v) {
                if (v.status == false) {
                    isOke = false;
                }

                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(v.no));
                newRow.append($('<td>').text(v.type_barang));
                newRow.append($('<td>').text(v.divisi));
                newRow.append($('<td>').text(v.warehouse_name));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang_name));
                newRow.append($('<td>').text(v.spesifikasi));
                newRow.append($('<td>').text(greatFormatRupiah(v.qty)));
                newRow.append($('<td>').text(v.kode_satuan));
                newRow.append($('<td>').text(v.tanggal));
                newRow.append($('<td>').text(v.no_aju));
                newRow.append($('<td>').text(v.no_daftar));
                newRow.append($('<td>').text(v.supplier_name));
                if (v.status == true) {
                    newRow.append($('<td>').html(`
                        <div class="text-success">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    `));
                } else {
                    newRow.append($('<td>').html(`
                        <div class="text-danger">
                            <i class="fa-solid fa-x"></i>
                        </div>
                    `));
                }
                newRow.append($('<td>').text(v.message));

                table.find('tbody').append(newRow);
            });

            if (!isOke) {
                $('.btn-submit-parent').attr('disabled', true);
            } else {
                $('.btn-submit-parent').attr('disabled', false);
            }
        }

    }
</script>

<?= $this->endSection(); ?>
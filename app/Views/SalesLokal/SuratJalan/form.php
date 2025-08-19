<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .id_so+.select2-container--bootstrap-5 .select2-selection__choice {
        font-size: 13px !important;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Update Surat Jalan" : "Tambah Surat Jalan"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("surat-jalan"); ?>">
                Kembali
            </a>

            <?php if (!empty($data)) : ?>
                <a class="btn btn-warning btn-print float-right" href="<?= base_url("surat-jalan/print/{$data->id}"); ?>" target="_blank">
                    Print
                </a>
            <?php endif; ?>

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp form-add-pinjaman-karyawan" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="No. Sales Order" required value="<?= $data->no_surat_jalan ?? ''; ?>" <?= (!empty($data) && $data->posting == 1 ? 'readonly' : '') ?>>
                                    <label for="floatingInput">No. Surat Jalan</label>
                                </div>
                                <?php if (empty($data)) : ?>
                                    <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                        <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 5px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()" <?= (!empty($data) && $data->posting == 1 ? 'disabled' : '') ?>>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_customer" name="id_customer" id="id_customer" <?= (!empty($data) && $data->posting == 1 ? 'disabled' : '') ?>>
                                <option value=""></option>
                                <?php foreach ($dataCustomers ?? [] as $customer) : ?>
                                    <option value="<?= $customer['id']; ?>" <?= !empty($data) && $data->id_customer === $customer['id'] ? "selected" : ""; ?>>
                                        <?= $customer['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_so" name="id_so[]" id="id_so[]" multiple <?= (!empty($data) && $data->posting == 1 ? 'disabled' : '') ?>>
                                <option value=""></option>
                                <?php foreach ($dataSo ?? [] as $so) : ?>
                                    <option value="<?= $so->id; ?>" <?= !empty($data) && in_array($so->id, $data->multiple_id_so) ? "selected" : ""; ?>>
                                        <?= $so->no_sales_order; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Order Form</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control" id="tagihan_ke" name="tagihan_ke" value="<?= $data->customerAddress ?? ''; ?>" <?= (!empty($data) && $data->posting == 1 ? 'readonly' : '') ?>>
                            <label for="floatingInput">Alamat Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control" id="no_telp" name="no_telp" value="<?= $data->customerPhone ?? ''; ?>" readonly>
                            <label for="floatingInput">No. Telp</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <!-- <input autocomplete="one-time-code" class="form-control" id="termin" name="termin" value="<?= $data->customerTermin ?? ''; ?>" readonly>
                            <label for="floatingInput">Termin</label> -->

                            <select class="form-select termin" id="termin" name="termin">
                                <?php if (!empty($termin)) : ?>
                                    <option value=""></option>
                                    <?php foreach ($termin as $row) : ?>
                                        <option value="<?= $row['id'] ?>" <?= ($data->terms == null ? $data->termin : $data->terms) == $row['id'] ? 'selected' : '' ?>><?= $row['value'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </select>
                            <label for="floatingInput">Termin</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control" id="salesName" name="salesName" value="<?= $data->customerSales ?? ''; ?>" readonly>
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" name="shipping_date" type="text" value="<?= !empty($data) ? $data->shipping_date : date('d/m/Y') ?>" class="form-control shipping_date" id="shipping_date" <?= (!empty($data) && $data->posting == 1 ? 'readonly' : '') ?>>
                                <label>Tanggal Pengiriman</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date" <?= (!empty($data) && $data->posting == 1 ? 'style="display:none;"' : '') ?>></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" class="form-control" id="no_po" name="no_po" placeholder="Nomor PO" value="<?= $data->no_po ?? ''; ?>" <?= (!empty($data) && $data->posting == 1 ? 'readonly' : '') ?>>
                            <label for="floatingInput">No. PO</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea autocomplete="one-time-code" class="form-control" id="note" name="note" placeholder="Keterangan" <?= (!empty($data) && $data->posting == 1 ? 'readonly' : '') ?>><?= $data->note ?? ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control jenis_penjualan" id="jenis_penjualan" name="jenis_penjualan" value="<?= $getJenisPenjualan ?? ''; ?>" <?= (!empty($data) && $data->posting == 1 ? 'readonly' : '') ?>>
                            <label for="floatingInput">Jenis Penjualan</label>
                        </div>
                    </div>
                    <div class="col-md-4 nama_ecommerce_div">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control nama_ecommerce" id="nama_ecommerce" name="nama_ecommerce" value="<?= $data->nama_ecommerce ?? ''; ?>" <?= (!empty($data) && $data->posting == 1 ? 'readonly' : '') ?>>
                            <label for="floatingInput">Nama Ecommerce</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php if (session()->get("login")->this_company_id != 16) { ?>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select company_id" name="company_id" id="company_id" <?= (!empty($data) && $data->posting == 1 ? 'disabled' : '') ?>>
                                    <option <?= !empty($data) ? ($data->id_company == "1" ? "selected" : "") : ""; ?> value="1">KIM 1</option>
                                    <option <?= !empty($data) ? ($data->id_company == "2" ? "selected" : "") : ""; ?> value="2">KIM 2</option>
                                    <option <?= !empty($data) ? ($data->id_company == "15" ? "selected" : "") : ""; ?> value="15">GLOBAL</option>
                                </select>
                                <label for="floatingInput">Pilih Company</label>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- List Barang -->
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                        </div>

                        <div class="col-md-6">
                            <button type="button" class="btn btn-show-detail btn-add btn-block float-right <?= (!empty($data) && $data->posting == 1) ? 'disabled' : '' ?>" data-btn="detail-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Harga Satuan</th>
                                    <th>Discount</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" style="text-align: right;">Total:</th>
                                    <th class="text-center" id="total-qty">0</th>
                                    <th></th>
                                    <th class="text-center" id="total-harga">Rp 0</th>
                                    <th class="text-center" id="total-disc">0</th>
                                    <th class="text-center" id="total-amount">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- modal barang -->
<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <!-- <input autocomplete="one-time-code" type="hidden" class="id_barang" name="id_barang" id="id_barang" /> -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select id_barang" name="id_barang" id="id_barang" aria-label="Floating label select example">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput">Nama Barang</label>
                                </div>
                                <?php if (can('Penjualan Lokal', 'Master Barang', 'c')) : ?>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-success btn-barang-add" id="btn-barang-add" data-toggle="modal" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control satuan" name="satuan" id="satuan" placeholder="Satuan">
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="this.value = greatFormatRupiah(this.value);" onchange="this.value = greatFormatRupiah(this.value);" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Barang">
                                <label for="floatingInput">Harga Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="this.value=this.value.replace(/[^0-9.]/g,'').replace(/\.(?=.*\.)/g, '');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control amount" name="amount" id="amount" placeholder="Total Harga" oninput="this.value=this.value.replace(/[^0-9.]/g,'').replace(/\.(?=.*\.)/g, '');">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <!-- <label for="discount_input" class="form-label">disc%</label> -->
                                <div class="input-group" style="height: 50px;">
                                    <input
                                        autocomplete="one-time-code"
                                        type="text"
                                        class="form-control discount_percentage"
                                        name="discount_percentage"
                                        id="discount_percentage"
                                        placeholder="Discount">
                                    <select class="form-select discount_unit" name="discount_unit" id="discount_unit" style="max-width: 100px;">
                                        <option value="percent" selected>%</option>
                                        <option value="rupiah">Rp</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" style="display: none;">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan">
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control keteranganppn" name="keteranganppn" id="keteranganppn" placeholder="keteranganppn">
                                <label for="floatingInput">Status PPN</label>
                                <input autocomplete="one-time-code" type="hidden" readonly="true" class="form-control statusppn" name="statusppn" id="statusppn" placeholder="statusppn">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <!-- <button type="button" class="btn btn-discard delete-btn delete-detail delete-form">Hapus</button> -->
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    let list_items = [];
    let list_delete = [];
    var row = 0;
    var total_harga_barang = 0;
    var total_qty = 0;
    var total_harga = 0;
    var priceEdit = 0;
    var totalPriceEdit = 0;
    let no = 0;

    $(document).ready(function() {
        <?php if (empty($termin)) : ?>
            getTerminList(this.value);
        <?php endif; ?>
        <?php if (empty($data)) : ?>
            $('#auto_generate').prop('checked', true).change();
        <?php endif; ?>
        // Deklarasikan variabel table di level yang lebih tinggi
        let table;

        // Fungsi untuk menghitung total
        function calculateTotals() {
            if (!table) return; // Pastikan tabel sudah diinisialisasi

            let totalQty = 0;
            let totalHarga = 0;
            let totalDisc = 0;
            let totalAmount = 0;

            // Ambil semua baris data
            const allData = table.rows().data().toArray();

            allData.forEach(row => {
                totalQty += parseFloat(row.qty) || 0;
                totalHarga += destroyFormatRupiah(row.harga_barang) || 0;
                totalDisc += parseFloat(row.disc) || 0;
                totalAmount += destroyFormatRupiah(row.amount) || 0;
            });

            // Update footer
            $('#total-qty').text(totalQty.toFixed(2));
            $('#total-harga').text(greatFormatRupiah(totalHarga));
            $('#total-disc').text(totalDisc.toFixed(2));
            $('#total-amount').text(greatFormatRupiah(totalAmount));
        }

        // Inisialisasi tabel
        table = $('.dataTable').DataTable({
            processing: true,
            info: false,
            paging: false,
            fixedHeader: true,
            display: "stripe",
            searching: false,
            ordering: false,
            columns: [{
                    data: "kode_barang",
                    className: "text-center"
                },
                {
                    data: "nama_barang",
                    className: "text-center"
                },
                {
                    data: "qty",
                    className: "text-center"
                },
                {
                    data: "satuan",
                    className: "text-center"
                },
                {
                    data: "harga_barang",
                    className: "text-center",
                    render: function(data, type, row) {
                        return greatFormatRupiah(destroyFormatRupiah(data));
                    }
                },
                {
                    data: "disc",
                    className: "text-center"
                },
                {
                    data: "amount",
                    className: "text-center",
                    render: function(data, type, row) {
                        return greatFormatRupiah(destroyFormatRupiah(data));
                    }
                },
                {
                    data: "status",
                    className: "text-center actions",
                    render: function(data, type, row) {
                        let id = row.id;
                        let disableButton = "";
                        return `
                            <div class="">
                                <button type="button" data-no="${row.no}" data-id="${row.id}" class="edit-table-detail" ${disableButton}><i class="fa fa-edit" aria-hidden="true"></i></button>
                                <button type="button" data-no="${row.no}" data-id="${row.id}" class="delete-button" ${disableButton}><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </div>
                        `;
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
            },
            footerCallback: function(row, data, start, end, display) {
                calculateTotals();
            }
        });

        $('.dataTable tbody').on('click', '.delete-button', function() {
            let rowData = table.row($(this).parents('tr')).data();
            let dataNo = $(this).data('no');
            let dataId = $(this).data('id');
            const csrf = $(`[name="${csrfToken}"]`);

            // console.log(dataId);


            if (dataId.length === 7 && /[a-zA-Z]/.test(dataId)) {
                // Hapus item dari array JavaScript dan gambar ulang tabel
                // console.log(list_items);
                // console.log(table);
                let indexToRemove = list_items.findIndex(item => item.no === dataNo);
                if (indexToRemove !== -1) {
                    list_items.splice(indexToRemove, 1);

                }
                table.clear().rows.add(list_items).draw();
                // console.log(list_items);
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Yakin akan di hapus?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url("order-form-lokal/delete-detail"); ?>",
                            data: {
                                id: dataId,
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
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            // Update the list_items array
                                            list_items = list_items.filter(item => item.id !== dataId);
                                            // Redraw the table with the updated list_items
                                            table.clear().rows.add(list_items).draw();
                                            reCountTotal();
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#e74a3b',
                                    });
                                }
                            },
                        });
                    }
                })
            }
            reCountTotal();
        });

        // getBarang();
        $('.dataTable tbody').on('click', '.edit-table-detail', function() {
            let rowData = table.row($(this).parents('tr')).data();
            let dataIdBarang = rowData.id_barang;
            let dataQty = rowData.qty;
            let dataHargaBarang = rowData.harga_barang;
            let dataDiscountPercentage = rowData.discount_percentage ?? rowData.disc;
            let dataDiscountUnit = rowData.discount_unit ?? rowData.discUnit;
            let dataKeterangan = rowData.keterangan;
            let dataIdDetail = rowData.id;

            $(".title-detail-name").text("Update");

            console.log(rowData);

            getBarangAsync(dataIdBarang).then(() => {
                $(".harga").val(dataHargaBarang).change();
                $(".qty").val(dataQty).change();
                $(".discount_percentage").val(dataDiscountPercentage).change();
                $(".discount_unit").val(dataDiscountUnit).change();
                $(".keterangan").val(dataKeterangan).change();
                $(".id_detail").val(dataIdDetail).change();
                $(".detail-modal").modal("show");
            }).catch((err) => {
                console.error("Gagal ambil data barang:", err);
            });
        });

        // Customer
        $('.id_customer').select2({
            placeholder: "Pilih Customer",
            theme: "bootstrap-5",
            allowClear: true
        })

        $('#company_id').select2({
            placeholder: "Pilih Company",
            theme: "bootstrap-5",
            allowClear: true
        })

        $('#termin').select2({
            placeholder: "Pilih Termin",
            theme: "bootstrap-5",
            allowClear: true
        })

        $('.id_barang').select2({
            placeholder: "Pilih Barang",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_customer, #company_id, #termin, .id_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_customer, #company_id, #termin, .id_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px')
            .css('margin-left', '-7px');

        $('.id_customer, #company_id, #termin, .id_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SO
        $('.id_so').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_so')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_so')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_so')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".shipping_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $(".id_customer").change(function() {
            if ($(".id_customer").val()) {
                let customerId = $(".id_customer").val();

                $.ajax({
                    url: "<?= base_url('/surat-jalan/sales-order'); ?>" + "/" + customerId,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        console.log(res);
                        $(".id_so").empty();
                        $(".id_So").append(`<option value=""></option>`);

                        // console.log(res.dataWarehouse)
                        res.soList.forEach(function(item) {
                            let jenis_penjualanan_name = "";

                            if (item.jenis_penjualan == 1) {
                                jenis_penjualanan_name = "By Sales";
                            } else if (item.jenis_penjualan == 2) {
                                jenis_penjualanan_name = "By Office";
                            } else if (item.jenis_penjualan == 3) {
                                jenis_penjualanan_name = "By Ecommerce";
                            } else {
                                jenis_penjualanan_name = "";
                            }
                            $(".id_so").append(`<option  value="${item.id}" data-jenis_penjualan="${jenis_penjualanan_name}"  data-no_po="${item.no_po}" data-nama_ecommerce="${item.nama_ecommerce}" data-termin="${item.termin}" data-sales="${item.salesName}" data-company="${item.id_company}" data-keterangan="${item.keterangan}" data-no_po="${item.no_po}">${item.no_sales_order}</option>`);
                        });

                        $('#tagihan_ke').val(res.customerData.address);
                        $('#salesName').val(res.customerData.salesName);
                        $('#termin').val(res.customerData.termin).change();
                        $('#no_telp').val(res.customerData.phone);
                    }
                })

            } else {
                $(".id_customer").attr("readonly", false)
                $(".id_so").val("");
            }
        });

        $(".id_so").change(function() {
            // Simpan konteks this ke dalam variabel
            let $this = $(this);

            let termin = $this.find("option:selected").data("termin");
            let sales = $this.find("option:selected").data("sales");
            let no_po = $this.find("option:selected").data("no_po");
            let company = $this.find("option:selected").data("company");

            let jenis_penjualan = $this.find("option:selected").data("jenis_penjualan");
            let nama_ecommerce = $this.find("option:selected").data("nama_ecommerce");

            if (termin) {
                $('#termin').val(termin);
            }
            if (sales) {
                $('#salesName').val(sales);
            }
            if (no_po) {
                $('#no_po').val(no_po);
            }
            if (company) {
                $('#company_id').val(company).change();
            }

            $('#jenis_penjualan').val(jenis_penjualan);
            $('#nama_ecommerce').val(nama_ecommerce);

            let selectedOptions = $(this).find("option:selected");
            let keteranganList = [];
            let noPoList = [];

            selectedOptions.each(function() {
                let ket = $(this).data("keterangan");
                let po = $(this).data("no_po");

                if (ket) keteranganList.push(ket);
                if (po) noPoList.push(po);
            });

            $('#note').val(keteranganList.filter(Boolean).join(', ')).change();
            $('#no_po').val(noPoList.filter(Boolean).join(', ')).change();

            $.ajax({
                url: `<?= base_url('/order-form-lokal/getItemList'); ?>`,
                method: "GET",
                data: {
                    ids: $this.val()
                },
                dataType: "json",
                success: function(res) {
                    let so = $this.val();

                    if (so.length != 0) {

                        res.map((row) => {
                            const amount = parseFloat(row.amount)
                            const disc = parseFloat(row.disc)
                            const discUnit = row.discUnit
                            console.log(row);
                            // row.amount = discUnit == 'percent' ? (amount * (100 - disc)) / 100 : amount - disc
                        })
                        table.clear();

                        table.rows.add(res).draw(false);
                        calculateTotals();
                    } else {
                        table.clear().draw(false);
                    }
                }
            })
        });

        calculateTotals();

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        });

        $(".btn-show-detail").click(function() {
            $(".title-detail-name").text("Tambah");

            $(".id_detail").val('')
            $(".id_barang").empty('')
            $(".id_barang").val('').change()
            $("#warehouse").val('').change()
            $("#warehouse").empty()
            $(".harga").val('')
            $(".qty").val('')
            $(".statusppn").val('')
            $(".amount").val('')
            $(".keterangan").val('')
            $(".discount_percentage").val('')
            $(".satuan").val('')
            $(".keteranganppn").val('')

            $(".id_barang").val('')

            getBarang();
            $(".detail-modal").modal("show");
        });

        $(".harga, .qty").keyup(function() {
            let harga = $(".harga").val() ? destroyFormatRupiah($(".harga").val()) : 0;
            let qty = $(".qty").val() ? parseFloat($(".qty").val()) : 0;

            let amount = harga * qty;
            $(".amount").val(greatFormatRupiah(amount));
        });

        $(".harga, .qty").change(function() {
            let harga = $(".harga").val() ? destroyFormatRupiah($(".harga").val()) : 0;
            let qty = $(".qty").val() ? parseFloat($(".qty").val()) : 0;

            let amount = harga * qty;
            $(".amount").val(greatFormatRupiah(amount));
        });

        $(".discount_percentage").keyup(function() {
            if ($(".discount_percentage").val()) {
                if ($(".discount_unit option:selected").val() == 'percent' && $(".discount_percentage").val() > 100) {
                    $(".discount_percentage").val(100)
                }
                if ($(".discount_unit option:selected").val() == 'percent' && $(".discount_percentage").val() < 0) {
                    $(".discount_percentage").val();
                }
            } else {
                $(".discount_percentage").val();
            }
        })

        $(".discount_unit").change(function() {
            let discVal = $(".discount_percentage").val();
            if (discVal) {
                if ($(".discount_unit option:selected").val() == 'percent' && discVal > 100) {
                    $(".discount_percentage").val(100)
                }
                if ($(".discount_unit option:selected").val() == 'percent' && discVal < 0) {
                    $(".discount_percentage").val();
                }
                if ($(".discount_unit option:selected").val() == 'rupiah') {
                    $(".discount_percentage").val(discVal)
                }
            } else {
                $(".discount_percentage").val();
            }
        })

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val() ? $(".id_detail").val() : 0;
            let id_barang = $(".id_barang option:selected").val()
            let nama_barang = $(".id_barang option:selected").text()
            const selectedData = $(".id_barang option:selected").data();
            let harga = destroyFormatRupiah($(".harga").val())
            let qty = $(".qty").val()
            let amount = destroyFormatRupiah($(".amount").val());
            let keterangan = $(".keterangan").val()
            let statusppn = $(".statusppn").val()
            const tax = selectedData.tax;
            let discountPercentage = $(".discount_percentage").val() || 0;
            let discountUnit = $(".discount_unit option:selected").val() || "percent";
            let dept = $(".dept").val()
            let warehouseId = $(".warehouse").val()
            let warhouseName = $(".warehouse").text()

            const discAmt = discountUnit == "percent" ? amount * (discountPercentage / 100) : discountPercentage;
            const discountedAmt = amount - discAmt;

            // console.log(list_items);


            const currentItemList = table.rows().data().toArray();
            let validate_same = currentItemList.findIndex((obj) => obj.id_barang == id_barang && obj.warehouse_id == warehouseId);

            if (validate_same >= 0 && row_detail == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang tidak boleh sama',
                    confirmButtonColor: '#4e73df',
                })

            } else {
                // update detail
                if (row_detail != 0) {
                    // console.log("Editing row with ID:", row_detail);

                    // 1. Cari index di list_items
                    const itemIndex = list_items.findIndex(item => item.id == row_detail);
                    // console.log("Found in list_items at index:", itemIndex);

                    if (itemIndex !== -1) {
                        // Hitung nilai diskon dan amount baru
                        const discAmt = discountUnit == "percent" ?
                            amount * (discountPercentage / 100) :
                            discountPercentage;

                        const discountedAmt = amount - discAmt;

                        // 2. Update item di list_items
                        const updatedItem = {
                            ...list_items[itemIndex],
                            id_barang: id_barang,
                            nama_barang: nama_barang,
                            harga_barang: harga,
                            qty: qty,
                            amount: amount,
                            discountedAmt: discountedAmt,
                            keterangan: keterangan,
                            statusppn: statusppn,
                            tax: tax,
                            taxAmt: amount * (tax / 100),
                            discount_percentage: discountPercentage,
                            dept: dept,
                            warehouse_id: warehouseId,
                            warhouse_name: warhouseName,
                            kode_barang: selectedData.code,
                            satuan: selectedData.satuan,
                            disc: discountPercentage,
                            discAmt: discAmt,
                            discUnit: discountUnit,
                            barangTotal: amount
                        };

                        list_items[itemIndex] = updatedItem;
                        // console.log("Updated list_items:", list_items[itemIndex]);

                        // 3. Update baris di tabel
                        let rowUpdated = false;
                        const rows = table.rows().indexes().toArray();

                        for (let i = 0; i < rows.length; i++) {
                            const rowData = table.row(rows[i]).data();
                            if (rowData.id == row_detail) {
                                const newData = {
                                    ...rowData,
                                    id_barang: id_barang,
                                    kode_barang: selectedData.code,
                                    nama_barang: nama_barang,
                                    qty: qty,
                                    satuan: selectedData.satuan,
                                    harga_barang: harga,
                                    barangTotal: amount,
                                    disc: discountPercentage,
                                    statusppn: statusppn,
                                    tax: tax,
                                    taxAmt: amount * (tax / 100),
                                    keterangan: keterangan,
                                    discAmt: discAmt,
                                    discUnit: discountUnit,
                                    amount: discountedAmt,
                                    dept: dept,
                                    warehouse_id: warehouseId,
                                    warehouse_name: warhouseName
                                };

                                table.row(rows[i]).data(newData).invalidate();
                                rowUpdated = true;
                                // console.log("Updated table row:", newData);
                                break;
                            }
                        }

                        if (rowUpdated) {
                            table.draw(); // Refresh tampilan tabel
                            // console.log("Table refreshed");
                        } else {
                            console.warn("Row not found in table with ID:", row_detail);
                        }

                        // 5. Reset form dan tutup modal
                        $(".detail-form")[0].reset();
                        $(".detail-modal").modal("hide");
                        // console.log("Modal closed");
                    } else {
                        console.warn("Item not found in list_items with ID:", row_detail);
                    }
                } else {
                    if ($(".detail-form").valid()) {
                        let id = generateRandomId();

                        no = no + 1;
                        list_items.push({
                            id: id,
                            no: no,
                            row: row + 1,
                            id_barang: id_barang,
                            nama_barang: nama_barang,
                            harga_barang: harga,
                            qty: qty,
                            amount: amount,
                            discountedAmt: discountedAmt,
                            keterangan: keterangan,
                            statusppn: statusppn,
                            tax: tax,
                            taxAmt: amount * (tax / 100),
                            discount_percentage: discountPercentage,
                            dept: dept,
                            warehouse_id: warehouseId,
                            warhouse_name: warhouseName,

                            kode_barang: selectedData.code,
                            satuan: selectedData.satuan,
                            disc: discountPercentage,
                            discAmt: discAmt,
                            discUnit: discountUnit,
                            isDeleted: false,

                            barangTotal: amount,
                        });

                        table.row.add({
                            id: id,
                            no: no,
                            id_barang: id_barang,
                            kode_barang: selectedData.code,
                            nama_barang: nama_barang,
                            qty: qty,
                            satuan: selectedData.satuan,
                            harga_barang: harga,
                            barangTotal: amount,
                            disc: discountPercentage,
                            statusppn: statusppn,
                            tax: tax,
                            taxAmt: amount * (tax / 100),
                            keterangan: keterangan,
                            discAmt: discAmt,
                            discUnit: discountUnit,
                            amount: discountedAmt,
                            dept: dept,
                            warehouse_id: warehouseId,
                            warehouse_name: warhouseName,
                            isDeleted: false
                        }).draw(false);

                        total_harga_barang = total_harga_barang + harga;
                        total_qty = total_qty + qty;
                        total_harga = total_harga + amount;

                        let tag_html = "";
                        let tag_total = "";

                        $(".foot-detail-table").empty()

                        tag_total += `<tr>`;
                        tag_total += "<td colspan='1'>";
                        tag_total += "</td>";
                        tag_total += "<td>";
                        tag_total += "<b>TOTAL</b>";
                        tag_total += "</td>";
                        tag_total += "<td>";
                        tag_total += `<b>${greatFormatRupiah(total_harga_barang)}</b>`;
                        tag_total += "</td>";
                        tag_total += "<td>";
                        tag_total += `<b>${total_qty}</b>`;
                        tag_total += "</td>";
                        tag_total += "<td>";
                        tag_total += `<b>${greatFormatRupiah(total_harga)}</b>`;
                        tag_total += "</td>";
                        tag_total += "<td colspan='3'>";
                        tag_total += "</td>";
                        tag_total += "</tr>";

                        $(".foot-detail-table").append(tag_total);

                        $(".detail-modal").modal("hide")
                        row = row + 1;
                    }

                }
            }
        });

        <?php if (!empty($data)) : ?>
            const itemList = <?= json_encode($data->itemList); ?>;
            console.log(itemList);

            table.rows.add(itemList).draw(false);
            calculateTotals();
        <?php endif; ?>

        function getTerminList() {
            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'termin'
                },
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(result) {
                    $(".termin").empty()
                    $(".termin").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".termin").append(`<option value="${item.id}">${item.value.toUpperCase()}</option>`)
                    })

                    $(".termin").val("").change();
                }
            })
        }

    })

    var validator = $(".create-form").validate({
        rules: {
            id_customer: {
                required: true
            },
            id_po: {
                required: true
            },
            'id_so[]': {
                required: true
            },
            no_surat_jalan: {
                required: true
            },
            shipping_date: {
                required: true
            },
        },
        messages: {
            id_customer: {
                required: "Customer wajib diisi"
            },
            id_po: {
                required: "PO wajib diisi"
            },
            'id_so[]': {
                required: "No Order wajib diisi"
            },
            no_surat_jalan: {
                required: "No Surat jalan wajib diisi"
            },
            shipping_date: {
                required: "Tanggal pengiriman wajib diisi"
            },
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
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    // MODAL
    var validator_detail = $(".detail-form").validate({
        rules: {
            id_barang: {
                required: true
            },
            harga: {
                required: true
            },
            qty: {
                required: true
            }
        },
        messages: {
            id_barang: {
                required: "Nama barang wajib diisi"
            },
            harga: {
                required: "Harga wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
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
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    $(".id_barang").change(function() {
        if ($(".id_barang").val()) {
            let nama = $(".id_barang option:selected").data("nama") ? $(".id_barang option:selected").data("nama") : "";
            let idBarang = $(".id_barang option:selected").data("id_item") ? $(".id_barang option:selected").data("id_item") : "";
            let satuan = $(".id_barang option:selected").data("satuan") ? $(".id_barang option:selected").data("satuan") : "";
            let statusppn = $(".id_barang option:selected").data("statusppn");
            let warehouseId = $(".id_barang option:selected").data("warehouse_id") ? $(".id_barang option:selected").data("warehouse_id") : "";
            let warehouseName = $(".id_barang option:selected").data("warehouse_name") ? $(".id_barang option:selected").data("warehouse_name") : "";
            let harga = $(".id_barang option:selected").data("harga") ? $(".id_barang option:selected").data("harga") : 0;

            // let stok = $(".id_barang option:selected").data("stok") ? $(".id_barang option:selected").data("stok") : "";

            $(".nama_barang").attr("readonly", nama ? true : false);
            if (statusppn == 1) {
                $(".keteranganppn").val("Barang PPN");

            } else {
                $(".keteranganppn").val("Barang Tidak PPN");
            }

            $(".statusppn").val(statusppn);
            $(".nama_barang").val(nama);
            $(".warehouse").on('change', function() {
                let idBarang = $(".id_barang option:selected").data("id_item") ? $(".id_barang option:selected").data("id_item") : "";
                let warehouseId = $(this).val();
                $(".stok").val("");
                $.ajax({
                    url: "<?= base_url('/order-form-lokal/stok'); ?>" + "/" + idBarang + "/" + warehouseId,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        //bug di penjualan lokal
                        // $(".warehouse").append(`<option value=""></option>`);
                        if (res.dataDetailStock.length > 0) {
                            // You can set the value of .stok based on the selected warehouse here
                            let selectedWarehouse = res.dataDetailStock; // Assuming you want the first item in the response
                            $(".stok").val(res.dataDetailStock);
                        }
                    }
                })
            });
            $(".satuan").val(satuan);
            $(".harga").val(greatFormatRupiah(harga));

        } else {
            $(".nama_barang").attr("readonly", false)
            // $(".harga").val("0");
            $(".qty").val("");
            $(".amount").val("0");
            $(".keterangan").val("").change();
            $(".statusppn").val("");
            $(".tax").val("");
            $(".discount_percentage").val();
            $(".dept").val("");
            $(".warehouse").val("");
            $(".id_warehouse").val("");
        }
    });

    $(".btn-submit").click(function() {
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
                    const csrf = $(`[name="${csrfToken}"]`);
                    setLoading()
                    let data = new FormData(document.querySelector(".create-form"));

                    let id = $(".id").val();

                    console.log(data.entries());
                    console.log(id)

                    if (id) {
                        $.ajax({
                            url: "<?= base_url("surat-jalan/update"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            },
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                csrf.val(response.token);
                                if (response.status) {
                                    stopLoading();
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        showCancelButton: true,
                                        showDenyButton: true,
                                        confirmButtonText: 'Cetak',
                                        denyButtonText: 'Baru',
                                        cancelButtonText: 'Tutup',
                                        confirmButtonColor: '#4e73df', // Biru
                                        denyButtonColor: '#28a745', // Hijau
                                        cancelButtonColor: '#dc3545', // Merah
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Cetak
                                            window.location.href = `<?= base_url("surat-jalan/print"); ?>/${response.id}`;
                                        } else if (result.isDenied) {
                                            // Buat baru
                                            window.location.reload();
                                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                            // Tutup - redirect to surat-jalan
                                            window.location.href = `<?= base_url("surat-jalan"); ?>`;
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                    stopLoading();
                                }
                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        });
                    }
                    // CREATE
                    else {
                        $.ajax({
                            url: "<?= base_url("surat-jalan/save"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            },
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                stopLoading();
                                csrf.val(response.token);
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        showCancelButton: true,
                                        showDenyButton: true,
                                        confirmButtonText: 'Cetak',
                                        denyButtonText: 'Baru',
                                        cancelButtonText: 'Tutup',
                                        confirmButtonColor: '#4e73df', // Biru
                                        denyButtonColor: '#28a745', // Hijau
                                        cancelButtonColor: '#dc3545', // Merah
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Cetak
                                            window.location.href = `<?= base_url("surat-jalan/print"); ?>/${response.id}`;
                                        } else if (result.isDenied) {
                                            // Buat baru
                                            window.location.reload();
                                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                            // Tutup - redirect to surat-jalan
                                            window.location.href = `<?= base_url("surat-jalan"); ?>`;
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                    stopLoading();
                                }
                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        });
                    }
                }
            })
        }
    });

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $.ajax({
                url: "<?= base_url("surat-jalan/generate-no-surat-jalan"); ?>",
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
                    csrf.val(response.token);
                    $(".no_surat_jalan").val(response.data);
                },

            });
            $(".no_surat_jalan").attr("readonly", true);
        } else {
            $(".no_surat_jalan").attr("readonly", false);
            // $(".no_surat_jalan").val("");
        }
    }

    function getBarang() {
        $.ajax({
            url: `<?= base_url("order-form-lokal/barangAll"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".id_barang").empty();

                $(".id_barang").append(`<option data-satuan="" data-warehouse_id="" data-harga="" data-statusppn="" data-warehouse_name="" data-id_item="" value=""></option>`);



                res.dataBarang.forEach(function(item) {
                    $(".id_barang").append(`<option data-code="${item.kode_barang}" data-harga="${item.harga_jual}" data-statusppn="${item.statusppn}" data-satuan="${item.nama_satuan}" data-warehouse_id="${item.warehouse_id}" data-harga="${item.harga_barang}" data-warehouse_name="${item.warehouse_name}" data-id_item="${item.id}" value="${item.id}">${item.nama_barang}</option>`);
                })
                // console.log(res.dataBarang);

                $(".id_barang").val("").change();
            }
        })
    }

    function getBarangAsync(selectedIdBarang = null) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `<?= base_url("order-form-lokal/barangAll"); ?>`,
                method: "GET",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(res) {
                    const $select = $(".id_barang");
                    $select.empty();
                    $select.append(`<option data-satuan="" data-warehouse_id="" data-harga="" data-statusppn="" data-warehouse_name="" data-id_item="" value=""></option>`);

                    res.dataBarang.forEach(function(item) {
                        $select.append(`
                            <option
                                data-code="${item.kode_barang}"
                                data-harga="${item.harga_jual}"
                                data-statusppn="${item.statusppn}"
                                data-satuan="${item.nama_satuan}"
                                data-warehouse_id="${item.warehouse_id}"
                                data-harga="${item.harga_barang}"
                                data-warehouse_name="${item.warehouse_name}"
                                data-id_item="${item.id}"
                                value="${item.id}"
                            >
                                ${item.nama_barang}
                            </option>
                        `);
                    });

                    // Pilih value jika disediakan
                    if (selectedIdBarang !== null) {
                        $select.val(selectedIdBarang).trigger("change");
                    }

                    resolve();
                },
                error: function(err) {
                    reject(err);
                }
            });
        });
    }

    function generateRandomId(length = 7) {
        const chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }
</script>

<?= $this->endSection(); ?>
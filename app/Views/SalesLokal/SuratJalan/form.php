<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


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
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
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
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


<script>
    const csrfToken = '<?= csrf_token() ?>';

    $(document).ready(function() {
        <?php if (empty($termin)) : ?>
            getTerminList(this.value);
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

        //CSS SELECT2 FLOATING LABEL
        $('.id_customer, #company_id, #termin')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_customer, #company_id, #termin')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_customer, #company_id, #termin')
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
</script>

<?= $this->endSection(); ?>
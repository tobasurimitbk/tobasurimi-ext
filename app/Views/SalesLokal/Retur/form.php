<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?> Return Barang</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("return-barang-sales"); ?>">Kembali</a>
            <?php if (!empty($data)) : ?>
                <button class="btn btn-hapus delete-parent float-right btn-delete">
                    Hapus
                </button>
            <?php endif; ?>
            <button class="btn btn-show-form btn-save float-right btn-submit">Simpan</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form " role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control no_surat_jalan" id="no_surat_retur" name="no_surat_retur" value="<?= !empty($data) ? $data->no_return : $noReturn; ?>" placeholder="Nomor surat Return">
                                <label for="floatingInput">Nomor Surat Return</label>
                            </div>
                            <div style="<?= !empty($data) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control input-picker shipping_date" id="return_date" name="return_date" value="<?= $data->return_date ?? ""; ?>" <?= !empty($data) ? 'readonly'  : ''; ?>>
                            <label for="floatingInput">Tanggal Return</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_customer" name="id_customer" id="id_customer">
                                <option value=""></option>
                                <?php foreach ($dataCustomers ?? [] as $customer) : ?>
                                    <option value="<?= $customer->id; ?>" data-address="<?= $customer->address; ?>" <?= !empty($data) ? ($data->customer_id === $customer->id ? "selected" : "") : ""; ?>><?= $customer->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Nama Customer</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control" id="customerAddress" name="customerAddress" disabled value="<?= $data->customerAddress ?? ''; ?>">
                            <label for="floatingInput">Alamat</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select sumber_select" name="sumber_select" id="sumber_select">
                                <option value="order_form" <?= !empty($data) ? ($data->sumber == "order_form" ? "selected" : "") : "selected"; ?>>Order Form</option>
                                <option value="surat_jalan" <?= !empty($data) ? ($data->sumber == "surat_jalan" ? "selected" : "") : ""; ?>>Surat Jalan</option>
                                <option value="invoice" <?= !empty($data) ? ($data->sumber == "invoice" ? "selected" : "") : ""; ?>>Invoice</option>
                            </select>
                            <label for="floatingInput">Sumber Data</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select reference_id" name="reference_id" id="reference_id">
                                <option value=""></option>
                                <?php foreach ($dataReference ?? [] as $value) : ?>
                                    <option value="<?= $value['id']; ?>" <?= !empty($data) ? ($data->id_invoice === $value['id'] ? "selected" : "") : ""; ?>><?= $value['no_reference']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">No Reference</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_warehouse" name="id_warehouse" id="id_warehouse">
                                <option value=""></option>
                                <?php foreach ($dataWarehouse ?? [] as $warehouse) : ?>
                                    <option value="<?= $warehouse->id; ?>" <?= !empty($data) ? ($data->id_warehouse === $warehouse->id ? "selected" : "") : ""; ?>><?= $warehouse->warehouse_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Warehouse Tujuan</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea autocomplete="one-time-code" class="form-control" id="note" name="note" placeholder="Keterangan"><?= $data->note ?? ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>

                <!-- list barang -->
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">List Barang Return</label>
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
                                    <th>Qty Invoice</th>
                                    <th>Qty Return</th>
                                    <th>Satuan</th>
                                    <th>Harga Satuan</th>
                                    <th>Discount</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</section>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    var itemList = [];

    const table = $('.dataTable').DataTable({
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
                className: "text-center",
            },
            {
                data: null,
                className: "text-center",
                render: function(data, type, row) {
                    var qty = row.qtyReturn ? row.qtyReturn : row.qty;
                    return `<input type="text" style="height: 40px; padding-bottom: 12px;" class="form-control" value="${qty}">`
                }
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
                className: "text-center",
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
        }
    });

    $(document).ready(function() {
        // getReferenceData();
        // getReferenceDataDetail();


        // Customer
        $('.id_customer').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            let address = $('.id_customer option:selected').data('address');
            $('#customerAddress').val(address).change();

            getReferenceData()
            getReferenceDataDetail()
        });

        // invoice
        $('.id_warehouse').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true
        });

        // invoice
        $('.reference_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            getReferenceDataDetail();
        });

        //CSS SELECT2 FLOATING LABEL
        $('.id_customer, .reference_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_customer, .reference_id, .id_warehouse')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_customer, .reference_id, .id_warehouse')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".shipping_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('.sumber_select').change(function() {
            getReferenceData();
            getReferenceDataDetail();
        })

        $(".btn-delete").click(function() {
            const csrf = $(`[name="${csrfToken}"]`);
            var dataId = $(".id").val();
            console.log(dataId);
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
                        url: "<?= base_url("return-barang-sales/delete"); ?>",
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
                                        window.location.href = "<?= base_url("return-barang-sales"); ?>";
                                    })
                            }
                        },
                    });
                }
            })
        });
        $(".btn-submit").click(function() {
            var isValid = true;
            var dataError = null;
            $(".id_customer").attr("disabled", false)
            $(".reference_id").attr("disabled", false)
            const dataTab = table.rows().every(function(rowIdx) {
                const inputVal = $(this.node()).first().find('input').val();
                const rowData = table.row(rowIdx).data();
                if (parseFloat(rowData.qty) < parseFloat(inputVal) && rowData.qty == 0) {
                    isValid = false;
                } else {
                    rowData.qtyReturn = inputVal;
                }

                table.row(rowIdx).data(rowData);
            });
            const newTableData = table.rows().data().toArray();
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Qty return tidak boleh lebih besar dari Qty Invoice',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else {
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
                            setLoading();

                            const id = $(".id").val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('returnedItems', JSON.stringify(newTableData));

                            // // UPDATE
                            if (id) {
                                $.ajax({
                                    url: "<?= base_url("return-barang-sales/update"); ?>",
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
                                            stopLoading()
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("return-barang-sales"); ?>";
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
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
                                    url: "<?= base_url("return-barang-sales/save"); ?>",
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
                                            stopLoading()
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("return-barang-sales"); ?>";
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
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
            }
        });

        <?php if (!empty($dataDetail)) : ?>
            <?php foreach ($dataDetail as $value) : ?>
                itemList.push(<?= json_encode($value); ?>);
            <?php endforeach; ?>
            table.rows.add(itemList).draw(false);
        <?php endif; ?>

    })

    var validator = $(".create-form").validate({
        rules: {
            id_customer: {
                required: true
            },
            shipping_date: {
                required: true
            },
            reference_id: {
                required: true
            },
            id_warehouse: {
                required: true
            },
            no_surat_retur: {
                required: true
            },
        },
        messages: {
            id_customer: {
                required: "Customer wajib diisi"
            },
            shipping_date: {
                required: "Tanggal return wajib diisi"
            },
            reference_id: {
                required: "No invoice wajib diisi"
            },
            id_warehouse: {
                required: "Warehouse wajib diisi"
            },
            no_surat_retur: {
                required: "No surat return wajib diisi"
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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $.ajax({
                url: `<?= base_url("/return-barang-sales/get-nomor-surat-return"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res) {
                        $("#no_surat_retur").val(res);
                        $("#no_surat_retur").attr("readonly", true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#no_surat_retur").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#no_surat_retur").val("");
                    }
                }
            })
        } else {
            $("#no_surat_retur").attr("readonly", false);
            // $("#no_surat_retur").val("");
        }
    }

    function getReferenceData() {
        let sumber = $('.sumber_select option:selected').val();
        let customer = $('.id_customer option:selected').val();
        if (sumber == "order_form" && customer) {
            getOrderForm(sumber, customer);
        } else if (sumber == "surat_jalan") {
            getSuratJalan(sumber, customer);
        } else {
            getInvoice(sumber, customer);
        }
    }

    function getOrderForm(sumber, customer) {
        if (sumber == "order_form" && customer) {
            $.ajax({
                url: `<?= base_url("/return-barang-sales/get-order-form/"); ?>${customer}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    const $select = $("#reference_id");
                    $select.empty(); // Hapus semua option sebelumnya
                    $select.append(`<option value=""></option>`); // Tambahkan option kosong

                    if (res && res.length > 0) {
                        res.forEach(function(item) {
                            $select.append(`<option value="${item.id}">${item.no_reference} - ${item.barang_name}</option>`);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        });
                    }
                }
            })
        }
    }

    function getSuratJalan(sumber, customer) {
        if (sumber == "surat_jalan" && customer) {
            $.ajax({
                url: `<?= base_url("/return-barang-sales/get-surat-jalan/"); ?>${customer}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    const $select = $("#reference_id");
                    $select.empty(); // Hapus semua option sebelumnya
                    $select.append(`<option value=""></option>`); // Tambahkan option kosong

                    if (res && res.length > 0) {
                        res.forEach(function(item) {
                            $select.append(`<option value="${item.id}">${item.no_reference}</option>`);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        });
                    }
                }
            })
        }
    }

    function getInvoice(sumber, customer) {
        if (sumber == "invoice" && customer) {
            $.ajax({
                url: `<?= base_url("/return-barang-sales/get-invoice/"); ?>${customer}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    const $select = $("#reference_id");
                    $select.empty(); // Hapus semua option sebelumnya
                    $select.append(`<option value=""></option>`); // Tambahkan option kosong

                    if (res && res.length > 0) {
                        res.forEach(function(item) {
                            $select.append(`<option value="${item.id}">${item.no_reference}</option>`);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        });
                    }
                }
            })
        }
    }

    function getReferenceDataDetail() {
        let sumber = $('.sumber_select option:selected').val();
        let customer = $('.id_customer option:selected').val();
        let reference = $('.reference_id option:selected').val();

        // Kosongkan list item dan bersihkan tabel
        itemList = [];
        table.clear().draw(); // Ini akan menghapus semua baris di tabel dan menggambar ulang

        if (sumber && customer && reference) {
            $.ajax({
                url: `<?= base_url("/return-barang-sales/get-"); ?>${sumber}/${customer}/${reference}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    res.forEach(element => {
                        itemList.push(element);
                    });
                    table.rows.add(itemList).draw(false); // Tambahkan data baru ke tabel
                }
            });
        }
    }
</script>

<?= $this->endSection(); ?>
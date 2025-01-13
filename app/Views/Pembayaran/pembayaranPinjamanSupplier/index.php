<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label>Pinjaman Supplier</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" action="pinjaman-supplier/save">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control name" id="no_pinjaman" name="no_pinjaman" placeholder="no_pinjaman">
                                    <label for="floatingInput">No Pinjaman</label>
                                </div>
                                <div style="" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: -8px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="date" class="form-control name" id="payment_date" name="payment_date" placeholder="payment_date">
                                <label for="floatingInput">Tanggal Pinjaman</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tipe_supplier" name="tipe_supplier" id="tipe_supplier">
                                    <option value="" selected></option>
                                    <option value="INTERNASIONAL">INTERNASIONAL</option>
                                    <option value="BAHAN PENOLONG">BAHAN PENOLONG</option>
                                    <option value="BAHAN BAKU">BAHAN BAKU</option>
                                </select>
                                <label for="floatingInput">Tipe Supplier</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select supplier_id" name="supplier_id" id="supplier_id">

                                </select>
                                <label for="floatingInput">Supplier</label>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tipe_pinjaman" name="tipe_pinjaman" id="tipe_pinjaman">
                                    <option value="" selected></option>
                                    <option value="MERAH">Merah</option>
                                    <option value="PUTIH">Putih</option>

                                </select>
                                <label for="floatingInput">Tipe Pinjaman</label>
                            </div>
                        </div> -->

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="total_pinjaman" name="total_pinjaman" placeholder="Nama" oninput="preventNegativeInput(this)" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Total Pinjaman</label>
                            </div>
                        </div>


                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <?php if (can('Pembayaran', 'Pinjaman Supplier', 'c')) : ?>
                    <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                <?php endif; ?>
                <?php ?>
                <?php if (can('Pembayaran', 'Pinjaman Supplier', 'd')) : ?>
                    <button type="button" class="btn btn-discard delete-form delete-btn">Hapus</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Pinjaman Supplier</h1>
        <button class="btn btn-show-form btn-add float-right" id="btn-display-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col mb-4">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating spp-ptspp mb-3" style="height: 50px;">
                        <select class="form-select kategori pinjaman_status form-out-search" name="pinjaman_status" id="pinjaman_status" aria-label="Floating label select example">
                            <option value="ALL">STATUS POSTING:SEMUA</option>
                            <option value="NOT_POSTING">STATUS POSTING:BELUM POSTING</option>
                            <option value="POSTING">STATUS POSTING:SUDAH POSTING</option>

                        </select>
                        <label for="floatingInput" class="l-spp-ptspp">Status Posting</label>
                    </div>
                </div>
                <div class="col mb-4">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('no_pinjaman')">No. Pinjaman</th>
                                <th onclick="changeSort('supplier_id')">Supplier</th>
                                <th onclick="changeSort('payment_date')">Payment Date</th>
                                <th onclick="changeSort('payment_amount')">Total Pinjaman</th>
                                <th onclick="changeSort('payment_amt_left')">Sisa Pinjaman</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- history modal -->
<div class="modal fade" id="historiModal" tabindex="-1" role="dialog" aria-labelledby="historiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historiModalLabel">Histori Pembayaran Pinjaman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control nomor_pinjaman" name="nomor_pinjaman" id="nomor_pinjaman">
                            <label for="floatingInput">No Pinjaman</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control supplier_name" name="supplier_name" id="supplier_name">
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                    <!-- <div class="col-sm-12">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control lpb_no" name="lpb_no" id="lpb_no">
                            <label for="floatingInput">Nomor LPB</label>
                        </div>
                    </div> -->
                </div>
                <div class="table-responsive">
                    <table class="table table-inside table-borderd nowrap table-hover-tobasurimi dataTable2" style="width: 100%;" id="tableHistori">
                        <thead>
                            <tr>
                                <td style="width: 10px;text-align: center;color:#E7323A;font-weight:bold;">No</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;" class="nomor">Nomor PO</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;">Total Pinjaman</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;">Bayar Pinjaman</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;">Payment Date</td>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "no_pinjaman";
    let sortType = "desc";
    let trigger = true;

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("pinjaman-supplier/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.status = $(".is_posted").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.pinjaman_status = $('.pinjaman_status option:selected').val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            },
            {
                data: "no_pinjaman",
                className: "text-center",
                // width: "10%"
            },
            {
                data: "supplier",
                className: "text-center"
            },
            {
                data: "payment_date",
                className: "text-center"
            },
            {
                data: "total_pinjaman",
                className: "text-center"
            },
            {
                data: "sisa_pinjaman",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,

                render: function(data, type, row) {
                    let id = row.id;
                    let is_posted = row.is_posted;

                    if (is_posted === '0') {

                        return `
                        <div class="mt-0">
        
                            <button data-toggle="tooltip" title="Posting" onclick="updateStatus('${id}', 1)" class="btn btn-success posting-pinjaman-supplier">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>

                            <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>

                        <div>
                        `
                    }
                    if (is_posted === '1') {
                        return `

                            <div class="mt-0">
                                <button  data-toggle="tooltip" title="Histori LPB" onclick="displayHistory('${id}')" class="btn btn-success posting-spp">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </button>
                            <div>
                        `

                    }


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

    // INIT VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            total_pinjaman: {
                required: true,

            },
            payment_date: {
                required: true
            },
            name: {
                required: true
            }

        },
        messages: {
            total_pinjaman: {
                required: "total pinjaman wajib diisi",

            },
            payment_date: {
                required: "date have to be selected"
            },
            name: {
                required: "Nama wajib diisi"
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

    function remove(id) {
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
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("pinjaman-supplier/delete"); ?>",
                    data: {
                        id: id,
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
                                    table.ajax.reload()
                                })
                        }
                    },
                });
            }
        })
    }

    const deleteForm = function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                let id = $(".id").val();
                setLoading()
                $.ajax({
                    url: "<?= base_url("pinjaman-supplier/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    method: "POST",
                    dataType: "json",
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
                                    table.ajax.reload()
                                    $(".add-modal").modal("hide")
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
        })
    }

    // DISPLAY MODAL
    $('#btn-display-modal').click(function() {
        $('.add-modal').modal('show');
        $(".title-name").text("Tambah");
        $(".delete-form").css('display', 'none');
        validator.resetForm();
        validator.reset();
        $(".create-form")[0].reset();
        $("#tipe_supplier").val(null).trigger('change');
        $("#supplier_id").val(null).trigger('change');
        $("#id").val('');
    })

    //delete form by form
    $('.delete-form').click(deleteForm);


    // HIDE MODAL
    $('.btn-discard').click(function() {
        $('.add-modal').modal('hide');
        validator.resetForm();
        validator.reset();
    });

    $('#tipe_supplier').select2({
        placeholder: "Pilih Tipe Supplier",
        theme: "bootstrap-5",
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
    });


    $("#tipe_supplier, #supplier_id")
        .parent('div')
        .find('label')
        .css('z-index', '1');



    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        $(".create-form")[0].reset()
        $(".delete-form").css('display', '');
        let id = data.id;
        // $('.add-modal').modal('show');
        $(".title-name").text("Update");
        validator.resetForm();
        validator.reset();

        $.ajax({
            url: "<?= base_url("pinjaman-supplier/id"); ?>" + "/" + id,
            method: "GET",
            dataType: "json",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
                validator.resetForm();
                validator.reset();
            },
            success: function(res) {
                if (res.status) {
                    try {
                        // APPEND TO DROPDOWN SUPPLIER
                        appendDropdownSupplier(res.supplier);
                        // APPEND TO FORM
                        $("#id").val(id);
                        $("#no_pinjaman").val(res.data.no_pinjaman);
                        $("#payment_date").val(res.data.payment_date);
                        $("#tipe_supplier").val(res.data.type).change();
                        $("#supplier_id").val(res.data.supplier_id);
                        $("#payment_date").val(res.data.payment_date);
                        $("#total_pinjaman").val(formatRupiah(res.data.total_pinjaman));
                        $("#sisa_pinjaman").val(res.data.fax);
                        $(".add-modal").modal("show");
                        $('#auto_generate').css('display', 'none');
                        $("#no_pinjaman").prop("disabled", true);
                        if (res.data.is_posted === "1") {
                            $("#payment_date").prop("disabled", true);
                            $("#tipe_supplier").prop("disabled", true);
                            $("#supplier_id").prop("disabled", true);
                            $("#total_pinjaman").prop("disabled", true);
                            $(".delete-form").css('display', 'none');
                        }

                        $('.modal').on('hidden.bs.modal', function() {
                            enableFields();
                        });

                        function enableFields() {
                            $("#no_pinjaman").prop("disabled", false);
                            $("#payment_date").prop("disabled", false);
                            $("#tipe_supplier").prop("disabled", false);
                            $("#supplier_id").prop("disabled", false);
                            $("#total_pinjaman").prop("disabled", false);
                            $(".delete-form").css('display', '');
                            $('#auto_generate').css('display', '');
                        }

                    } catch (error) {
                        console.log(error);
                    }

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
        })

    })

    // GET SUPPLIER BY TYPE
    $('#tipe_supplier').change(function() {
        var typeSupplier = $('#tipe_supplier option:selected').val();
        $.ajax({
            url: `<?= base_url('pinjaman-supplier/list-supplier'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_supplier: typeSupplier,
            },
            dataType: "json",
            success: function(res) {
                // APPEND TO DROPDOWN
                appendDropdownSupplier(res.data);
            }
        });
    });

    // APPEND DATA SUPPLIER BY TYPE
    function appendDropdownSupplier(data) {
        // $(".supplier_id").empty()
        $(".supplier_id").append(`<option value=""></option>`)
        data.forEach(function(item) {
            $(".supplier_id").append(`<option value="${item.id}">${item.name}</option>`)
        })
    }

    changeStatus();

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $.ajax({
                url: `<?= base_url("/pinjaman-supplier/generate-no-pinjaman"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res) {
                        $("#no_pinjaman").val(res);
                        $("#no_pinjaman").attr("readonly", true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#no_pinjaman").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#no_pinjaman").val("");
                    }
                }
            })
        } else {
            $("#no_pinjaman").attr("readonly", false);
            $("#no_pinjaman").val("");
        }

    }



    $(".btn-submit-parent").click(function() {
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
                    let data = new FormData(document.querySelector(".create-form"));
                    let totalPinjaman = destroyFormatRupiah($('#total_pinjaman').val());
                    data.set('total_pinjaman', totalPinjaman);
                    let id = $(".id").val();

                    $.ajax({
                        url: id ? "<?= base_url("pinjaman-supplier/update"); ?>" : "<?= base_url("pinjaman-supplier/save"); ?>",
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
                                        $(".add-modal").modal("hide")
                                        table.ajax.reload()
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
            })
        }
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const updateStatus = function(id, status) {
        Swal.fire({
            icon: 'question',
            title: status == '1' ? 'Yakin akan diposting ?' : 'Batalkan Posting ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                console.log("CSRF Token:", csrf.val()); // Debugging
                console.log("ID:", id); // Debugging
                console.log("Status:", status); // Debugging
                $.ajax({
                    url: "<?= base_url("pinjaman-supplier/update-status"); ?>",
                    data: {
                        id: id,
                        status: status
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
                        console.log("Response:", response); // Debugging
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    table.ajax.reload()
                                })
                        }
                    },
                });
            }
        })

    }

    $(".dateStart").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    $(".pinjaman_status").change(function() {
        table.ajax.reload();
    })

    $(".dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    $(".dataTable_info").addClass("pt-0");

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }

    function displayHistory(id) {
        // console.log(id);
        $.ajax({
            url: "<?= base_url("/pinjaman-supplier/history-pembayaran"); ?>",
            data: {
                id: id
            },
            method: "GET",
            success: function(response) {
                console.log(response);
                $('#nomor_pinjaman').val(response.pinjaman_detail.no_pinjaman);
                $('#supplier_name').val(response.pinjaman_detail.name);
                const table = $('#tableHistori');
                var no = 1;

                table.find('tbody').empty();
                if (response.data.length > 0) {

                    $.each(response.data, function(i, v) {
                        var newRow = $('<tr>');
                        newRow.append($('<td style="text-align:center;">').text(no++));
                        newRow.append($('<td style="text-align:center;">').text(v.multiple_lpb_no));
                        newRow.append($('<td style="text-align:center;">').text(v.total_pinjaman));
                        newRow.append($('<td style="text-align:center;">').text(v.bayar_pinjaman));
                        newRow.append($('<td style="text-align:center;">').text(v.payment_date));
                        table.find('tbody').append(newRow);
                    });
                } else {
                    var newRow = $('<tr>');
                    newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Pembayaran</td>'));
                    table.find('tbody').append(newRow);
                }
                if (response.pinjaman_detail.type === 'BAHAN PENOLONG') {
                    $(".nomor").text("Nomor Tanda Terima Faktur");
                } else {
                    $(".nomor").text("Nomor PO");
                }
                $('#historiModal').modal('show');

            },
        });
    }

    function formatRupiah(angka) {
        var formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });
        var parsedNumber = parseFloat(angka);
        if (isNaN(parsedNumber)) {
            return "0,00";
        }
        return formatter.format(parsedNumber).replace('Rp', '').trim();
    }

    function convertRupiahToNumber(rupiah) {
        if (rupiah == "") {
            return 0;
        } else {
            var withoutDot = rupiah.replace(/\./g, '');
            var numberWithDot = withoutDot.replace(',', '.');
            return parseFloat(numberWithDot);
        }
    }
</script>


<?= $this->endSection(); ?>
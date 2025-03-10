<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<div class="modal add-modal-internasional" id="add_modal_internasional" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Customer</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-internasional" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe_customer" class="tipe_customer" id="tipe_customer" value="INTERNASIONAL">
                    <input autocomplete="one-time-code" type="hidden" class="internasional_id" name="id" id="internasional_id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama Customer</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select country_id" name="country_id" id="country_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataCountry)) {
                                        foreach ($dataCountry as $dc) {
                                    ?>
                                            <option value="<?= $dc["id"]; ?>">(<?= $dc["code"]; ?>) <?= $dc['country_name'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Negara</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <textarea autocomplete="one-time-code" class="form-control address" id="address" name="address"></textarea>
                                <label for="floatingInput">Alamat (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select sales_id" name="sales_id" id="sales_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataSales)) {
                                        foreach ($dataSales as $sales) {
                                    ?>
                                            <option value="<?= $sales["id"]; ?>"><?= strtoupper($sales["name"]); ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Nama Sales</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent-internasional">Simpan</button>
                <?php if (can('Penjualan Ekspor', 'Customer', 'd')) : ?>
                    <button type="button" class="btn btn-discard delete-btn delete-form-internasional">Hapus</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Customer Ekspor</h1>
        <?php if (can('Penjualan Ekspor', 'Customer', 'c')) : ?>
            <button class="btn btn-show-form btn-add float-right btn-show-form-internasional" data-btn="create-modal">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        <?php endif; ?>
        <?php if (can('Penjualan Ekspor', 'Customer', 'p')) : ?>
            <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 20px;">
                Import / Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item btn-upload-excel-customer-global">Import Excel</button></li>
                <li><button class="dropdown-item" onclick="exportExcel('INTERNASIONAL')">Export Excel</button></li>
            </ul>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row  justify-content-end ">
                <div class="col-sm-3 mb-3" style="float: right;">
                    <input autocomplete="one-time-code" class="form-control search form-out-search mr-3 form-search-internasional" placeholder="Cari Kode / Nama Customer" value="" />
                </div>
            </div>
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTableInternasional" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('kode')" class="sort">Kode</th>
                                <th onclick="changeSort('name')" class="sort">Nama Pelanggan</th>
                                <th onclick="changeSort('country')" class="sort">Negara</th>
                                <th onclick="changeSort('address')" class="sort">Alamat</th>
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
<div class="modal" id="import_customer_global_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Customer Internasional</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary text-black" role="alert">
                    UNDUH TEMPLEATE EXCEL <a href="<?= base_url('assets/import/IMPORT_EXCEL_CUSTOMER.xlsx') ?>" style="text-decoration: none;"><b style="color: black;">DISINI</b></a>
                </div>
                <form class="form-excel-master-barang-ekspor" method="post">
                    <input type="hidden" name="tipe_customer" value="INTERNASIONAL">
                    <div class="form-floating" style="height: 50px;">
                        <input type="file" name="file" id="file" accept=".xlsx" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-import-excel-customer-global mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-excel-master-barang-ekspor">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "kode";
    let sortType = "desc";

    const dataTableInternasional = $('#dataTableInternasional').DataTable({

        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            // [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("customer-ekspor/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".form-search-internasional").val();
                data.sort = sort;
                data.sortType = sortType;
                data.customerType = "INTERNASIONAL";
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
        }, {
            data: "kode",
            className: "text-center"
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "countryName",
            className: "text-center"
        }, {
            data: "address",
            className: "text-center",
        }, ],
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


    $('.btn-upload-excel-customer-global').click(function() {
        $('#import_customer_global_modal').modal('show');
    });

    $('.btn-discard-import-excel-customer-global').click(function() {
        $('#import_customer_global_modal').modal('hide');
    });

    $('.btn-submit-excel-customer-global').click(function() {
        if ($('.form-excel-customer-global').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Import Excel?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrf = $(`[name="${csrfToken}"]`);
                    let formData = new FormData(document.querySelector(".form-excel-customer-global"));
                    $.ajax({
                        url: "<?= base_url("customer/import-excel"); ?>",
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
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then(() => {
                                    dataTableInternasional.ajax.reload();
                                    $('#import_customer_global_modal').modal('hide');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                        },
                    });

                }
            })
        }
    });

    const exportExcel = function(tipe_customer) {
        var url = "<?= base_url('customer/export-excel') ?>";
        if (tipe_customer == "LOKAL") {
            var company_id = $('.company_lokal_search').val();
        } else {
            var company_id = $('.company_internasional_search').val();
        }
        window.open(url + `?tipe_customer=${tipe_customer}&company_id=${company_id}&sort=${sort}&sortType=${sortType}&`, "_blank");
    }

    $(document).ready(function() {

        // COUNTRY
        //CSS SELECT2 FLOATING LABEL
        $('#sales_id').select2({
            placeholder: "Pilih Sales (Opsional)",
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $("#add_modal_internasional")
        });

        $('.country_id').select2({
            placeholder: "Pilih Negara",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal-internasional .modal-content")
        })


        $(".country_id,#sales_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".country_id,#sales_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');


        $(".country_id,#sales_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-8px');


        var validatorInternasional = $(".create-form-internasional").validate({
            rules: {
                name: {
                    required: true
                },
                country_id: {
                    required: true
                },
            },
            messages: {
                name: {
                    required: "Nama customer wajib diisi"
                },
                country_id: {
                    required: "Pilih negara"
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


        $(".form-search-internasional").keyup(function() {
            dataTableInternasional.ajax.reload();
        });

        $(".dataTable_info").addClass("pt-0");

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $('#dataTableInternasional tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = dataTableInternasional.row(this).data();
            $(".create-form-internasional")[0].reset()
            $(".delete-form-internasional").show();
            $(".title-name").text("Update")
            let id = data.id;

            $.ajax({
                url: "<?= base_url("customer-ekspor/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                success: function(res) {
                    if (res.data) {
                        $(".internasional_id").val(id);
                        $(".name").val(res.data.name);
                        $(".address").val(res.data.address);
                        $(".country_id").val(res.data.country_id).change();
                        $(".sales_id").val(res.data.sales_id).change();
                        validatorInternasional.resetForm();
                        validatorInternasional.reset();

                        $('#add_modal_internasional').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            });

            $('#add-modal-internasional')
        });

        // delete internasional
        $(".delete-form-internasional").click(function() {
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
                    let id = $(".internasional_id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("customer-ekspor/delete"); ?>",
                        data: {
                            id: id
                        },
                        beforeSend: function(xhr) {
                            setLoading();
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                        dataTableInternasional.ajax.reload()
                                        $(".add-modal-internasional").modal("hide")
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        },
                    });
                }
            })
        });

    });

    // INTERNASIONAL
    $('.btn-show-form-internasional').click(function() {
        $('.add-modal-internasional').modal('show');
        $('.title-name').text("Tambah");
        $('.internasional_id').val(null);
        $('.name').val(null);
        $('.country_id').val(null).change();
        $('.address').val(null);
        $('.sales_id').val(null).change();
        $('.delete-form-internasional').hide();
    });

    $('.btn-hide-form').click(function() {
        $('.add-modal-internasional').modal('hide');
        $('.internasional_id').val(null);
        $('.name').val(null);
        $('.country_id').val(null).change();
        $('.address').val(null);
        $('.sales_id').val(null).change();
    });

    $(".btn-submit-parent-internasional").click(function() {
        if ($(".create-form-internasional").valid()) {
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
                    let data = new FormData(document.querySelector(".create-form-internasional"));
                    let id = $(".internasional_id").val();

                    if (id) {
                        <?php if (can('Penjualan Ekspor', 'Customer', 'u')) : ?>
                            $.ajax({
                                url: "<?= base_url("customer-ekspor/update"); ?>",
                                data: data,
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
                                    $("#add_modal_internasional").modal("hide");
                                    if (response.status) {
                                        stopLoading()
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                $(".add_modal_internasional").modal("hide")
                                                dataTableInternasional.ajax.reload()
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            });
                        <?php else : ?>
                            Swal.fire({
                                icon: 'error',
                                title: 'Anda tidak punya akses untuk update',
                                confirmButtonColor: '#4e73df',
                            })
                        <?php endif; ?>

                    } else {
                        $.ajax({
                            url: "<?= base_url("customer-ekspor/save"); ?>",
                            data: data,
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
                                $("#add_modal_internasional").modal("hide");
                                if (response.status) {
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            dataTableInternasional.ajax.reload()
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        });
                    }


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
</script>


<?= $this->endSection(); ?>
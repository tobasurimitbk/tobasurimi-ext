<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Biaya Ekspor</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right" href="#" onclick="exportExcel()">
                <i class="fa fa-download"></i> Export
            </a>

            <?php if (can('Biaya Exim', 'Biaya Ekspor', 'c')) : ?>
                <a class="btn btn-show-form btn-success float-right" href="<?= base_url("biaya-eskpor/create"); ?>">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp mb-3">
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Start Date" value="">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="End Date">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select status_posting" name="status_posting" id="status_posting" aria-label="Floating label select example">
                        <option value="ALL" selected>STATUS : ALL</option>
                        <option value="POSTING EXIM">STATUS : POSTED EXIM</option>
                        <option value="BELUM POSTING EXIM">STATUS : NOT POSTED EXIM</option>
                        <option value="POSTING ACC">STATUS : POSTED ACC</option>
                        <option value="BELUM POSTING ACC">STATUS : NOT POSTED ACC</option>
                        <option value="POSTING AUDIT">STATUS : POSTED AUDIT</option>
                        <option value="BELUM POSTING AUDIT">STATUS : NOT POSTED AUDIT</option>

                    </select>
                </div>
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search Data" id="search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('biaya_ekspor.id')">No</th>
                                <th onclick="changeSort('biaya_ekspor.divisi_id')" class="sort">Departemen</th>
                                <th onclick="changeSort('biaya_ekspor.tanggal_invoice')" class="sort">Tanggal</th>
                                <th onclick="changeSort('biaya_ekspor.no_invoice')" class="sort">Invoice</th>
                                <th onclick="changeSort('biaya_ekspor.no_container')" class="sort">Container</th>
                                <th onclick="changeSort('customers.name')" class="sort">Customer</th>
                                <th onclick="changeSort('sales_contract.dicharge_port')" class="sort">Destination</th>
                                <th onclick="changeSort('vendor_pelayaran.nama_vendor')" class="sort">Vendor</th>
                                <th onclick="changeSort('biaya_ekspor.total_faktur')" class="sort">Total</th>
                                <th onclick="changeSort('biaya_ekspor.status_posting_exim')" class="sort">Exim</th>
                                <th onclick="changeSort('biaya_ekspor.status_posting_acc')" class="sort">Acc</th>
                                <th onclick="changeSort('biaya_ekspor.status_posting_audit')" class="sort">Audit</th>
                                <th onclick="changeSort('biaya_ekspor.status_bayar')" class="sort">Kasir</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal" id="modalPosting" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">
                    Update Status Posting
                </h5>
            </div>
            <form class="form-posting">
                <div class="modal-body">
                    <input type="hidden" name="id" class="id" id="id">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                Exim
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                                Accounting
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-toggle="tab" data-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                Audit
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select status_posting_exim" name="status_posting_exim" id="status_posting_exim">
                                            <option value=""></option>
                                            <option value="0">BELUM POSTING</option>
                                            <option value="1">SUDAH POSTING</option>
                                        </select>
                                        <label for="floatingInput">Status Posting Exim</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select user_exim_posted" name="user_exim_posted" id="user_exim_posted">
                                            <option value=""></option>
                                            <?php foreach ($dataUser as $d): ?>
                                                <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput">User Penanggung Jawab</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select status_posting_acc" name="status_posting_acc" id="status_posting_acc">
                                            <option value=""></option>
                                            <option value="0">BELUM POSTING</option>
                                            <option value="1">SUDAH POSTING</option>
                                        </select>
                                        <label for="floatingInput">Status Posting Accounting</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select user_acc_posted" name="user_acc_posted" id="user_acc_posted">
                                            <option value=""></option>
                                            <?php foreach ($dataUser as $d): ?>
                                                <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput">User Penanggung Jawab</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select status_posting_audit" name="status_posting_audit" id="status_posting_audit">
                                            <option value=""></option>
                                            <option value="0">BELUM POSTING</option>
                                            <option value="1">SUDAH POSTING</option>
                                        </select>
                                        <label for="floatingInput">Status Posting Audit</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select user_audit_posted" name="user_audit_posted" id="user_audit_posted">
                                            <option value=""></option>
                                            <?php foreach ($dataUser as $d): ?>
                                                <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput">User Penanggung Jawab</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3" id="btnHidePosting">Kembali</button>
                    <button type="button" class="btn btn-submit-form btn-submit-detail" id="btnSubmitPosting">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "biaya_ekspor.id";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({

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
            url: "<?= base_url("biaya-eskpor/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.status_posting = $(".status_posting").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        initComplete: function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-left",
            }, {
                data: "divisi",
                className: "text-left"
            }, {
                data: "tanggal_invoice",
                className: "text-left"
            }, {
                data: "no_invoice",
                className: "text-left",
            }, {
                data: "no_container",
                className: "text-left",
            }, {
                data: "customer_name",
                className: "text-left",
            }, {
                data: "destination",
                className: "text-left",
            }, {
                data: "nama_vendor",
                className: "text-left",
            },
            {
                data: "total_faktur",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            },
            {
                data: "status_posting_exim",
                className: "text-center",
                searchable: false,
                sortable: false,
                width: "5%",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting_exim == 1) {
                        htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
                            </div>`
                    }

                    return htmlRes;
                }
            },
            {
                data: "status_posting_acc",
                className: "text-center",
                searchable: false,
                width: "5%",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting_acc == 1) {
                        htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
                            </div>`
                    }

                    return htmlRes;
                }
            },
            {
                data: "status_posting_audit",
                className: "text-center",
                searchable: false,
                width: "5%",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting_audit == 1) {
                        htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
                            </div>`
                    }

                    return htmlRes;
                }
            },
            {
                data: "status_bayar",
                className: "text-center",
                searchable: false,
                width: "5%",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_bayar == 1) {
                        htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
                            </div>`
                    }

                    return htmlRes;
                }
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;

                    let res = '';

                    res += `
                        <div class="mt-0">
                            <button data-toggle="tooltip" title="Detail" onclick="detail('${id}')" class="btn btn-info">
                                <i class="fa fa-eye fa-sm" aria-hidden="true"></i>
                            </button>
                          <?php if (can('Biaya Exim', 'Biaya Ekspor', 'u')) : ?>
                            <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Lihat" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        <?php endif; ?>
                            <?php if (can('Biaya Exim', 'Biaya Ekspor', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("biaya-eskpor/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Biaya Exim', 'Biaya Ekspor', 'a')) : ?>
                                <button data-toggle="tooltip" title="Post / Unpost" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Biaya Exim', 'Biaya Ekspor', 'd')) : ?>
                                <button data-toggle="tooltip" title="Delete" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            </div>
                        `;


                    return `
                    <div class="mt-0">
                        ${res}
                    </div>
                    `;
                }
            }
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

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

    $(".date_revision").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })


    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data ?',
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
                    url: "<?= base_url("biaya-eskpor/delete"); ?>",
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

    function edit(id) {
        window.location.href = " <?= base_url('biaya-ekspor/id') ?>" + "/" + id;
    }

    function detail(id) {
        const width = 800;
        const height = 600;
        const left = window.innerWidth / 2 - width / 2;
        const top = window.innerHeight / 2 - height / 2;

        window.open(
            "<?= base_url('biaya-eskpor/detail/') ?>" + id,
            "_blank",
            `width=${width},height=${height},top=${top},left=${left},resizable=yes`
        );

    }

    function exportExcel() {
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        var statusPosting = $('#status_posting').val();
        var search = $('#search').val();

        if (dateStart == '' && dateEnd == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih Tanggal Awal dan Tanggal Akhir",
                confirmButtonColor: '#4e73df',
            })
        } else {
            window.location.href = "<?= base_url('biaya-eskpor/export-excel') ?>" + '?dateStart=' + dateStart + '&dateEnd=' + dateEnd + '&status_posting=' + statusPosting + '&search=' + search;

        }

    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    $('#btnHidePosting').click(function(e) {
        e.preventDefault();
        $('#modalPosting').modal('hide');
    })

    function posting(id) {
        $.ajax({
            url: `<?= base_url("biaya-eskpor/get-status-posting"); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                id: id,
            },
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    // Change
                    $('#id').val(res.data.id);
                    $('#status_posting_exim').val(res.data.status_posting_exim).change();
                    $('#user_exim_posted').val(res.data.user_exim_posted).change();
                    $('#status_posting_acc').val(res.data.status_posting_acc).change();
                    $('#user_acc_posted').val(res.data.user_acc_posted).change();
                    $('#status_posting_audit').val(res.data.status_posting_audit).change();
                    $('#user_audit_posted').val(res.data.user_audit_posted).change();

                    $('#modalPosting').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
        })
    }

    $('#btnSubmitPosting').click(function(e) {
        e.preventDefault();

        const statusPostingExim = $('#status_posting_exim option:selected').val();
        const userEximPosted = $('#user_exim_posted option:selected').val();
        const statusPostingAcc = $('#status_posting_acc option:selected').val();
        const userAccPosted = $('#user_acc_posted option:selected').val();
        const statusPostingAudit = $('#status_posting_audit option:selected').val();
        const userAuditPosted = $('#user_audit_posted option:selected').val();
        let state = true;
        let messageError = "";

        if (statusPostingExim == 1) {
            if (userEximPosted == "") {
                state = false;
                messageError = "Pilih penanggung jawab exim";
            }
        }

        if (statusPostingAcc == 1) {
            if (userAccPosted == "") {
                state = false;
                messageError = "Pilih penanggung jawab accounting";
            }
        }

        if (statusPostingAudit == 1) {
            if (userAuditPosted == "") {
                state = false;
                messageError = "Pilih penanggung jawab audit";
            }
        }

        if (state) {
            const csrf = $(`[name="${csrfToken}"]`);
            const id = $('#id').val();
            $.ajax({
                url: "<?= base_url("biaya-eskpor/update-status-posting"); ?>",
                data: {
                    id: id,
                    status_posting_exim: statusPostingExim,
                    user_exim_posted: userEximPosted,
                    status_posting_acc: statusPostingAcc,
                    user_acc_posted: userAccPosted,
                    status_posting_audit: statusPostingAudit,
                    user_audit_posted: userAuditPosted
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
                    $('#modalPosting').modal('hide');
                    if (response.status) {
                        Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            .then(() => {
                                table.ajax.reload()
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
        } else {
            Swal.fire({
                icon: 'error',
                title: messageError,
                confirmButtonColor: '#4e73df',
            })
        }
    })

    $(".dataTable_info").addClass("pt-0");

    $(".status_posting, .dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $(".btn-hide-detail").click(function() {
        $(".id_sales_order").val("");
        $(".keterangan_unpost").val("");
        $(".unpost-modal").modal("hide");
    })

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    $('.status_posting_exim,.status_posting_acc,.status_posting_audit').select2({
        placeholder: "Status Posting",
        theme: "bootstrap-5",
        dropdownParent: $('#modalPosting')
    }).change(function() {});

    $('.user_exim_posted,.user_acc_posted,.user_audit_posted').select2({
        placeholder: "Penanggungjawab",
        theme: "bootstrap-5",
        dropdownParent: $('#modalPosting')
    }).change(function() {});

    $('.status_posting_exim,.status_posting_acc,.status_posting_audit,.user_exim_posted,.user_acc_posted,.user_audit_posted')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.status_posting_exim,.status_posting_acc,.status_posting_audit,.user_exim_posted,.user_acc_posted,.user_audit_posted')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.status_posting_exim,.status_posting_acc,.status_posting_audit,.user_exim_posted,.user_acc_posted,.user_audit_posted')
        .parent('div')
        .find('label')
        .css('z-index', '1');

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
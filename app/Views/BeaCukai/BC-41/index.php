<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Dokumen BC 4.1</h1>
        <div class="col-button-tambah-spp">
            <?php if ($akunCeisa != null) : ?>
                <?php if ($akunCeisa['status_integrasi']) : ?>
                    <a href="<?= base_url('bea-cukai-bc-41/bc-41-outstanding') ?>" class="btn btn-save float-right" type="button">
                        <i class="fa fa-ship fa-sm" aria-hidden="true"></i>
                        Outstanding
                    </a>
                    <?php if (can("Bea Cukai", "BC 4.1", "c")) : ?>
                        <a href="#" type="button" class="btn btn-success float-right" id="btnTambahModal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row justify-content-start row-col-spp">
                <div class="col-md-3 mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker mulaiTanggalBC41" id="mulaiTanggalBC41" name="mulaiTanggalBC41" placeholder="Mulai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-mulaiTanggalBC41"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalBC41" id="selesaiTanggalBC41" name="selesaiTanggalBC41" placeholder="Selesai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-selesaiTanggalBC41"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="statusPosting" class="form-select statusPosting" id="statusPosting">
                        <option selected value="ALL">STATUS POSTING : SEMUA</option>
                        <option value="SUDAH POSTING">SUDAH POSTING</option>
                        <option value="BELUM POSTING">BELUM POSTING</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noAju search form-out-search" placeholder="Cari Data" value="" />
                </div>
            </div>
            <?php if ($akunCeisa == null) : ?>
                <div class="alert alert-danger mt-3 mb-3" role="alert">
                    SILAHKAN HUBUNGKAN AKUN CEISA BEA CUKAI TERLEBIH DAHULU SEBELUM MENGGUNAKAN MODUL INI
                </div>
            <?php else : ?>
                <?php if ($akunCeisa['status_integrasi'] === "0") : ?>
                    <div class="alert alert-danger mt-3 mb-3" role="alert">
                        SILAHKAN HUBUNGKAN AKUN CEISA BEA CUKAI TERLEBIH DAHULU SEBELUM MENGGUNAKAN MODUL INI
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Tujuan Pengeluaran</th>
                                <th>Penerima</th>
                                <th>No Referensi</th>
                                <th>No Aju / Daftar</th>
                                <th>Tanggal</th>
                                <th>Status Posting</th>
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

<div class="modal detail-modal" id="btnModalTambahDokumen" tabindex="1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Tambah Dokumen Pengeluaran BC 2.5</h5>
            </div>
            <form class="create-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3">
                                    <input autocomplete="one-time-code" type="text" class="form-control tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dokumen">
                                    <label for="floatingInput">Tanggal Dokumen</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-floating mb-3 mt-1" style="height: 50px;">
                                <select class="form-select jenis_pengeluaran" id="jenis_pengeluaran" name="jenis_pengeluaran" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="ORDER FORM LOKAL">ORDER FORM LOKAL</option>
                                    <option value="LAINNYA">LAINNYA</option>
                                </select>
                                <label style="z-index: 1;">Pilih Pengeluaran</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideTambahDokumen">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitTambah">Buat Dokumen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    let sort = "bc_41.id";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [5, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("bea-cukai-bc-41/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $('.dateStart').val();
                data.dateEnd = $('.dateEnd').val();
                data.status_posting = $('.statusPosting').val();
                data.search = $('.search').val();
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-left ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-left",
                sortable: false,
                width: "5%"
            },
            {
                data: "jenis_pengeluaran",
                className: "text-left",
            },
            {
                data: "reference_penerima",
                className: "text-left",
            },
            {
                data: "multiple_reference_no",
                className: "text-left",
                sortable: false,
            },
            {
                data: "no_aju",
                className: "text-left",
                sortable: false,
            },
            {
                data: "tanggal",
                className: "text-left",
            },
            {
                data: "status_posting",
                className: "text-left",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting == "1") {
                        htmlRes += `
                            <div class="text-success">
                                SUDAH POSTING
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="text-danger">
                                BELUM POSTING
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
                    let htmlRes = `
                        <a href="<?= base_url("bea-cukai-bc-41/id"); ?>/${row.id}" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    `;

                    if (row.status_posting == "0") {
                        <?php if (can('Bea Cukai', 'BC 4.1', 'd')) : ?>
                            htmlRes += `
                                <button data-toggle="tooltip" title="Hapus" onclick="deleteAction('${row.id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
                        <?php if (can('Bea Cukai', 'BC 4.1', 'a')) : ?>
                            htmlRes += `
                                <button data-toggle="tooltip" title="Posting" onclick="postingAction('${row.id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
                    } else {
                        htmlRes += `

                            `;
                    }

                    if (row.status_dokumen === "SIAP KIRIM") {
                        htmlRes += `
                                <button data-toggle="tooltip" title="Kirim Ke Ceisa" onclick="kirimCeisaAction('${row.id}')" class="btn btn-info kirim-ceisa-parent">
                                    <i class="fa fa-upload fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                    }

                    return htmlRes;
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
            emptyTable: "Tidak ada riwayat dokumen BC 4.1", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $("#tanggal,#dateStart,#dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    var validator = $(".create-form").validate({
        rules: {
            tanggal: {
                required: true
            },
            jenis_pengeluaran: {
                required: true
            },
        },
        messages: {
            tanggal: {
                required: "Tanggal dokumen wajib diisi"
            },
            jenis_pengeluaran: {
                required: "Pilih tujuan pengeluaran"
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

    $('#jenis_pengeluaran').select2({
        placeholder: "Pilih Jenis Pengeluaran",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#btnModalTambahDokumen')
    }).change(function() {

    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#btnTambahModal').click(function(e) {
        e.preventDefault();
        $('#tanggal').val(null).change();
        $('#btnModalTambahDokumen').modal('show');
    });

    $('#btnHideTambahDokumen').click(function(e) {
        e.preventDefault();
        $('#btnModalTambahDokumen').modal('hide');
    });

    $('#btnSubmitTambah').click(function(e) {
        if ($('.create-form').valid()) {
            var formData = new FormData();
            var tanggal = $('#tanggal').val();
            var jenisPengeluaran = $('#jenis_pengeluaran').val();

            formData.append("tanggal", tanggal);
            formData.append("jenis_pengeluaran", jenisPengeluaran);
            $.ajax({
                url: `<?= base_url("bea-cukai-bc-41/save"); ?>`,
                method: "POST",
                data: formData,
                beforeSend: function(xhr) {
                    setLoading();
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                complete: function() {
                    stopLoading();
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status) {
                        var id = res.id;
                        window.location.href = "<?= base_url('bea-cukai-bc-41/id/'); ?>" + id;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        })
                    }
                }
            })
        }
    });

    $('.dateStart, .dateEnd,.statusPosting').change(function() {
        table.ajax.reload();
    });

    $('.search').keyup(function() {
        table.ajax.reload();
    });

    function deleteAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 2.5 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-41/delete"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        csrf.val(res.token);
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            });
                        }
                    }
                })
            }
        })
    }

    function postingAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Dokumen BC 2.5 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-41/posting"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        }
                    }
                })
            }
        })
    }

    function kirimCeisaAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting BC 4.0 ke aplikasi Ceisa Bea Cukai ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-41/api/kirim-dokumen/"); ?>` + id,
                    method: "GET",
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            })
                        }
                    }
                })
            }
        })
    }
</script>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Dokumen BC 2.7</h1>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row justify-content-start row-col-spp">
                <div class="col-md-3 mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker mulaiTanggalBC40" id="mulaiTanggalBC40" name="mulaiTanggalBC40" placeholder="Mulai Tanggal BC 2.7 Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-mulaiTanggalBC40"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalBC40" id="selesaiTanggalBC40" name="selesaiTanggalBC40" placeholder="Selesai Tanggal BC 2.7 Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-selesaiTanggalBC40"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="statusBC" class="form-select statusBC" id="statusBC">
                        <option value="Belum Dibuat">STATUS BC : BELUM DIBUAT</option>
                        <option value="Belum Lengkap">STATUS BC : BELUM LENGKAP</option>
                        <option value="Siap Kirim">STATUS BC : SIAP KIRIM CEISA 4.0</option>
                        <option value="Sudah Kirim">STATUS BC : SUDAH KIRIM CEISA 4.0</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="statusLPB" class="form-select statusLPB" id="statusLPB">
                        <option value="SEMUA">JENIS LPB : SEMUA</option>
                        <option value="LOKAL BAKU">JENIS LPB : LOKAL BB</option>
                        <option value="LOKAL PENOLONG">JENIS LPB : LOKAL BP</option>
                        <option value="IMPORT BAKU">JENIS LPB : IMPORT BB</option>
                        <option value="IMPORT PENOLONG">JENIS LPB : IMPORT BP</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noBC23 search form-out-search" placeholder="Cari Nomor BC 2.7" value="" />
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noPenerimaanBarang search form-out-search" placeholder="Cari Nomor LPB" value="" />
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noAju search form-out-search" placeholder="Cari Nomor Aju BC 2.7" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No.</th>
                                <th onclick="changeSort('bc_27.bc_no_lokal')" class="sort" style="text-align: center;">No BC 2.7</th>
                                <th onclick="changeSort('bc_27.createdAt')" class="sort" style="text-align: center;">Tanggal BC 2.7</th>
                                <th onclick="changeSort('bc_27.no_aju')" class="sort" style="text-align: center;">No Aju BC 2.7</th>
                                <th style="text-align: center;">Jenis LPB</th>
                                <th onclick="changeSort('penerimaan_barang.no_penerimaan_barang')" class="sort" style="text-align: center;">No LPB</th>
                                <th onclick="changeSort('penerimaan_barang.warehouse_id')" class="sort" style="text-align: center;">Warehouse</th>
                                <th style="text-align: center;">Status BC 2.7</th>
                                <th style="text-align: center;">Action</th>
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

<div class="modal fade" id="modalUpdateNoAju" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Ubah Nomor Pengajuan</h5>
            </div>
            <form id="form-update">
                <input type="hidden" name="penerimaan_barang_id" class="penerimaan_barang_id" id="penerimaan_barang_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="tanggal_pengajuan" value="" name="tanggal_pengajuan" type="text" class="tanggal_pengajuan form-control" placeholder="">
                                    <label>Tanggal</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_urut_dokumen" name="no_urut_dokumen" type="number" class="no_urut_dokumen form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Nomor Urut</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_pengajuan" name="no_pengajuan" type="text" readonly class="no_pengajuan form-control" placeholder="">
                                <label>Preview Nomor Pengajuan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-submit-form" id="ubahNoAjuButton">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    let sort = "bea_cukai.id";
    let sortType = "desc";

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
            url: "<?= base_url("bea-cukai-bc-40/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulaiTanggalBC40 = $('.mulaiTanggalBC40').val();
                data.selesaiTanggalBC40 = $('.selesaiTanggalBC40').val();
                data.noBC23 = $('.noBC23').val();
                data.statusLPB = $('.statusLPB').val();
                data.statusBC = $('.statusBC').val();
                data.noPenerimaanBarang = $('.noPenerimaanBarang').val();
                data.noAju = $('.noAju').val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [

            {
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            },
            {
                data: "bc_no_lokal",
                className: "text-center",
            },
            {
                data: "tanggal_bc_40",
                className: "text-center",
            },
            {
                data: "no_aju",
                className: "text-center"
            },
            {
                data: "jenis_lpb",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "no_penerimaan_barang",
                className: "text-center"
            },
            {
                data: "warehouse_name",
                className: "text-center"
            },
            {
                data: "status",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status == "BELUM DIBUAT") {
                        htmlRes += `
                            <div class="text-danger">
                                BELUM DIBUAT
                            </div>`
                    } else {
                        if (row.status == "BELUM LENGKAP") {
                            htmlRes += `
                                <div class="text-warning">
                                    BELUM LENGKAP
                                </div>`
                        } else if (row.status == "SIAP KIRIM") {
                            htmlRes += `
                                <div class="text-primary">
                                    SIAP KIRIM
                                </div>`
                        } else {
                            htmlRes += `
                                <div class="text-success">
                                    SUDAH KIRIM
                                </div>`
                        }
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
                    let htmlRes = '';

                    if (row.status == "BELUM DIBUAT") {
                        htmlRes += `
                            -`
                    } else {
                        if (row.status == "BELUM LENGKAP") {
                            if (row.is_update_no_aju) {
                                htmlRes += `
                                <button onclick="noAjuShowModal('${row.penerimaan_barang_id}', '${row.no_aju}')" class="btn btn-warning posting-spp">
                                    <i class="fas fa-edit fa-sm"></i>
                                </button>
                                `;
                            }

                            htmlRes += `
                                <button onclick="deleteAction('${row.penerimaan_barang_id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            `;

                        } else if (row.status == "SIAP KIRIM") {

                            if (row.is_update_no_aju) {
                                htmlRes += `
                                <button onclick="noAjuShowModal('${row.penerimaan_barang_id}', '${row.no_aju}')" class="btn btn-warning posting-spp">
                                    <i class="fas fa-edit fa-sm"></i>
                                </button>
                                `;
                            }

                            htmlRes += `
                                <button onclick="postingAction('${row.penerimaan_barang_id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            `;

                            htmlRes += `
                                <button onclick="deleteAction('${row.penerimaan_barang_id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        } else {
                            htmlRes += `
                            <button class="btn btn-warning btn-print" onclick="alert('Hello')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>`
                        }
                    }

                    return htmlRes;

                }
            }


        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada riwayat dokumen BC 2.7", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("bea-cukai-bc-40/id/header/"); ?>${data.penerimaan_barang_id}`);
    });


    $('.mulaiTanggalBC40, .selesaiTanggalBC40').change(function() {
        table.ajax.reload();
    });

    $('.noBC23, .noPenerimaanBarang, .noAju').keyup(function() {
        table.ajax.reload();
    });

    $('.statusBC, .statusLPB').change(function() {
        table.ajax.reload();
    });

    $(".mulaiTanggalBC40, .selesaiTanggalBC40").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });


    var validator = $("#form-update").validate({
        rules: {
            tanggal_pengajuan: {
                required: true
            },
            no_urut_dokumen: {
                required: true,
                digits: true,
                minlength: 6,
            },
        },
        messages: {
            tanggal_pengajuan: {
                required: "Tanggal wajib diisi"
            },
            no_urut_dokumen: {
                required: "Nomor urut wajib diisi",
                digits: "Nomor urut harus berupa angka",
                minlength: "Nomor urut harus terdiri dari 6 digit",
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

    $('#ubahNoAjuButton').click(function(e) {
        e.preventDefault();
        if ($('#form-update').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Ubah Nomor Aju ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-update"));
                    $.ajax({
                        url: `<?= base_url("bea-cukai-bc-40/id/update-no-aju"); ?>`,
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
                                $('#modalUpdateNoAju').modal('hide');

                            } else {
                                Swal.fire({
                                    icon: 'error',
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
    });


    $('#no_urut_dokumen').keyup(function() {
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");
        splitValues[3] = $(this).val();
        $('#no_pengajuan').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    });

    $("#tanggal_pengajuan").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    }).change(function() {
        var tanggalPengajuan = $(this).val();
        var noAju = $('#no_pengajuan').val();
        var tanggalPengajuanSplit = tanggalPengajuan.split("/");
        var noPengajuanSplit = noAju.split("-");
        $('#no_pengajuan').val(noPengajuanSplit[0] + '-' + noPengajuanSplit[1] + '-' + tanggalPengajuanSplit[2] + '' + tanggalPengajuanSplit[1] + '' + tanggalPengajuanSplit[0] + '-' + noPengajuanSplit[3]);
    });

    function noAjuShowModal(id, noAju) {
        var splitValues = noAju.split("-");

        var year = splitValues[2].substring(0, 4);
        var month = splitValues[2].substring(4, 6);
        var day = splitValues[2].substring(6, 8);

        var formattedDate = day + '/' + month + '/' + year;

        $('#tanggal_pengajuan').val(formattedDate);
        $('#no_pengajuan').val(noAju);
        $('#no_urut_dokumen').val(splitValues[3]);
        $('#modalUpdateNoAju').modal('show');
        $('#penerimaan_barang_id').val(id);
    }

    function changeSort(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function deleteAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 2.7 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-40/id/delete"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            csrf.val(res.token);
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
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    table.ajax.reload();
                                }
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
            title: 'Posting BC 2.7 ke aplikasi Ceisa Bea Cukai ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.replace("<?= base_url('bea-cukai-bc-40/api/kirim-dokumen/') ?>" + id)
            }
        })

    }
</script>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Dokumen BC 4.0</h1>
        <div class="col-button-tambah-spp">
            <?php if ($akunCeisa != null) : ?>
                <?php if ($akunCeisa['status_integrasi']) : ?>
                    <!-- <a href="<?= base_url('bea-cukai-bc-40/online') ?>" class="btn btn-discard btn-dropdown-export float-right" type="button">
                        <i class="fa fa-upload fa-sm" aria-hidden="true"></i>
                        Status Respon
                    </a> -->
                    <a href="<?= base_url('bea-cukai-bc-40/bc-40-outstanding') ?>" class="btn btn-save float-right" type="button">
                        <i class="fa fa-ship fa-sm" aria-hidden="true"></i>
                        Outstanding
                    </a>
                    <?php if (can("Bea Cukai", "BC 4.0", "c")) : ?>
                        <a href="<?= base_url('bea-cukai-bc-40/create') ?>" type="button" class="btn btn-success btn-add float-right">
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
                <div class="col-md-2 mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker mulaiTanggalBC40" id="mulaiTanggalBC40" name="mulaiTanggalBC40" value="01/<?= date('m/Y') ?>" placeholder="Mulai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-mulaiTanggalBC40"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalBC40" id="selesaiTanggalBC40" name="selesaiTanggalBC40" placeholder="Sampai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-selesaiTanggalBC40"></i>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-3 mb-3">
                    <select name="statusBC" class="form-select statusBC" id="statusBC">
                        <option value="ALL">STATUS BC : ALL</option>
                        <option value="Belum Lengkap">STATUS BC : BELUM LENGKAP</option>
                        <option value="Siap Kirim">STATUS BC : SIAP KIRIM CEISA 4.0</option>
                        <option value="Sudah Kirim">STATUS BC : SUDAH KIRIM CEISA 4.0</option>
                    </select>
                </div> -->
                <div class="col-md-2 mb-3">
                    <select name="statusLPB" class="form-select statusLPB" id="statusLPB">
                        <option value="SEMUA">JENIS LPB : SEMUA</option>
                        <option value="LOKAL BAKU">JENIS LPB : LOKAL BB</option>
                        <option value="LOKAL PENOLONG">JENIS LPB : LOKAL BP</option>
                        <!-- <option value="IMPORT BAKU">JENIS LPB : IMPORT BB</option>
                        <option value="IMPORT PENOLONG">JENIS LPB : IMPORT BP</option> -->
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <select name="statusLPB" class="form-select statusPosting" id="statusPosting">
                        <option value="SEMUA">STATUS POSTING : SEMUA</option>
                        <option value="SUDAH POSTING">SUDAH POSTING</option>
                        <option value="BELUM POSTING">BELUM POSTING</option>
                        <!-- <option value="IMPORT BAKU">JENIS LPB : IMPORT BB</option>
                        <option value="IMPORT PENOLONG">JENIS LPB : IMPORT BP</option> -->
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <input autocomplete="one-time-code" class="form-control searchData search form-out-search" placeholder="Cari Data" value="" />
                </div>
                <!-- <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control supplierName search form-out-search" placeholder="Cari Nama Supplier" value="" />
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noPenerimaanBarang search form-out-search" placeholder="Cari Nomor LPB" value="" />
                </div> -->
                <!-- <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noPo search form-out-search" placeholder="Cari Nomor Purchase Order" value="" />
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noAju search form-out-search" placeholder="Cari Nomor Aju / No Daftar" value="" />
                </div> -->
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
                                <th style="text-align: center;">No</th>
                                <th onclick="changeSort('bc_purchase_order.supplier_id')" class="sort" style="text-align: center;">Supplier</th>
                                <th onclick="changeSort('bc_40.createdAt')" class="sort" style="text-align: center;">Tanggal</th>
                                <th onclick="changeSort('bc_40.no_aju')" class="sort" style="text-align: center;">No Aju / No Daftar</th>
                                <th onclick="changeSort('bc_purchase_order.po_type')" style="text-align: center;">Jenis PO</th>
                                <!-- <th onclick="changeSort('bc_purchase_order.multiple_lpb_id')" class="sort" style="text-align: center;">No LPB</th>
                                <th onclick="changeSort('bc_purchase_order.multiple_po_id')" class="sort" style="text-align: center;">No PO</th> -->
                                <th>Total Barang</th>
                                <th>Posting</th>
                                <th>Doc Ceisa (Host to Host)</th>
                                <th style="text-align: center;">Action</th>
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

<!-- <div class="modal fade" id="modalUpdateNoAju" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Ubah Nomor Pengajuan</h5>
            </div>
            <form id="form-update">
                <input type="hidden" name="bc_purchase_order_id" class="bc_purchase_order_id" id="bc_purchase_order_id">
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
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="ubahNoAjuButton">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div> -->

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    let sort = "bc_purchase_order.createdAt";
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
            url: "<?= base_url("bea-cukai-bc-40/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulaiTanggalBC40 = $('.mulaiTanggalBC40').val();
                data.selesaiTanggalBC40 = $('.selesaiTanggalBC40').val();
                // data.supplierName = $('.supplierName').val();
                data.statusLPB = $('.statusLPB').val();
                data.statusPosting = $('.statusPosting').val();
                data.searchData = $('.searchData').val();
                // data.statusBC = $('.statusBC').val();
                // data.noPenerimaanBarang = $('.noPenerimaanBarang').val();
                // data.noAju = $('.noAju').val();
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
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            },
            {
                data: "supplier_name",
                className: "text-left"
            },
            {
                data: "tanggal_bc_40",
                className: "text-left",
            },
            {
                data: "no_aju",
                className: "text-left"
            },
            {
                data: "po_type",
                className: "text-left",
            },
            {
                data: "total_barang",
                searchable: false,
                sortable: false,
                className: "text-left",
            },
            // {
            //     data: "lpb_no",
            //     className: "text-center"
            // },
            // {
            //     data: "po_no",
            //     className: "text-center"
            // },
            {
                data: "status_posting",
                className: "text-center",
                searchable: false,
                sortable: false,
                width: "5%",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting == "1") {
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
                data: "status",
                className: "text-left",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status == "BELUM DIBUAT") {
                        htmlRes += `
                        <div class="text-danger">
                            BELUM DIBUAT
                        </div>`
                    } else if (row.status == "BELUM LENGKAP") {
                        htmlRes += `
                        <div class="text-warning">
                            BELUM LENGKAP
                        </div>`
                    } else if (row.status == "SUDAH KIRIM") {
                        htmlRes += `
                        <div class="text-success">
                            TERKIRIM
                        </div>`
                    } else if (row.status == "SIAP KIRIM") {
                        htmlRes += `
                        <div class="text-primary">
                            SIAP KIRIM
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
                    let htmlRes = '';
                    htmlRes += `
                            <a href="<?= base_url("bea-cukai-bc-40/po"); ?>/${id}" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="detailBarang('${row.id}')" data-toggle="tooltip" title="Detail Barang" class="btn btn-warning posting-spp actions">
                                <i class="fas fa-eye"></i>
                            </a>
                        `;

                    if (row.status_posting === "0") {
                        if (row.no_aju != null) {
                            // htmlRes += `
                            //     <button data-toggle="tooltip" title="Update No Aju" onclick="noAjuShowModal('${row.id}', '${row.no_aju}')" class="btn btn-warning posting-spp">
                            //         <i class="fas fa-edit fa-sm"></i>
                            //     </button>
                            //     `;
                        }

                        htmlRes += `
                                <button data-toggle="tooltip" title="Hapus" onclick="deleteAction('${row.id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            `;

                        htmlRes += `
                                <button data-toggle="tooltip" title="Posting" onclick="postingAction('${row.id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            `;

                        if (row.status === "SIAP KIRIM") {
                            htmlRes += `
                                <button data-toggle="tooltip" title="Kirim Ke Ceisa" onclick="kirimCeisaAction('${row.id}')" class="btn btn-info kirim-ceisa-parent">
                                    <i class="fa fa-upload fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        }
                    } else {
                        if (row.status === "SIAP KIRIM") {
                            htmlRes += `
                                <button data-toggle="tooltip" title="Kirim Ke Ceisa" onclick="kirimCeisaAction('${row.id}')" class="btn btn-info kirim-ceisa-parent">
                                    <i class="fa fa-upload fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        }
                        <?php if (can('Bea Cukai', 'BC 4.0', 'ua')): ?>
                            buttonUnpost = `<button data-toggle="tooltip" title="Unpost" class="btn btn-danger btn-print" onclick="unpostingAction('${row.id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-ban fa-sm" aria-hidden="true"></i>
                                </button>
                        `;
                        <?php else: ?>
                            buttonUnpost = ``;
                        <?php endif; ?>

                        htmlRes += buttonUnpost;
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
            emptyTable: "Tidak ada riwayat dokumen BC 4.0", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
    //     const data = table.row(this).data();
    //     location.replace(`<?= base_url("bea-cukai-bc-40/po/"); ?>${data.id}`);
    // });


    $('.mulaiTanggalBC40, .selesaiTanggalBC40').change(function() {
        table.ajax.reload();
    });

    $('.searchData').keyup(function() {
        table.ajax.reload();
    });

    $('.statusPosting, .statusLPB').change(function() {
        table.ajax.reload();
    });

    $(".mulaiTanggalBC40, .selesaiTanggalBC40").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });


    // var validator = $("#form-update").validate({
    //     rules: {
    //         tanggal_pengajuan: {
    //             required: true
    //         },
    //         no_urut_dokumen: {
    //             required: true,
    //             digits: true,
    //             minlength: 6,
    //         },
    //     },
    //     messages: {
    //         tanggal_pengajuan: {
    //             required: "Tanggal wajib diisi"
    //         },
    //         no_urut_dokumen: {
    //             required: "Nomor urut wajib diisi",
    //             digits: "Nomor urut harus berupa angka",
    //             minlength: "Nomor urut harus terdiri dari 6 digit",
    //         },
    //     },
    //     errorElement: 'span',
    //     errorClass: 'text-danger',
    //     errorPlacement: function(error, element) {
    //         var elem = $(element);
    //         if (elem.hasClass("select2-hidden-accessible")) {
    //             element = $("#select2-" + elem.attr("id") + "-container").parent();
    //             error.insertAfter(element);
    //         } else {
    //             error.insertAfter(element);
    //         }
    //     },
    //     highlight: function(element) {
    //         $(element).closest('.form-group').addClass('has-error');
    //         $(element).addClass('select-class');

    //     },
    //     unhighlight: function(element) {
    //         $(element).closest('.form-group').removeClass('has-error');
    //         $(element).removeClass('select-class');
    //     },
    // });

    // $('#ubahNoAjuButton').click(function(e) {
    //     e.preventDefault();
    //     if ($('#form-update').valid()) {
    //         Swal.fire({
    //             icon: 'question',
    //             title: 'Ubah Nomor Aju ?',
    //             confirmButtonColor: '#4e73df',
    //             cancelButtonColor: '#d33',
    //             showCancelButton: true,
    //             reverseButtons: true,
    //             confirmButtonText: 'Ya',
    //             cancelButtonText: 'Kembali',
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 var formData = new FormData(document.querySelector("#form-update"));
    //                 $.ajax({
    //                     url: `<?= base_url("bea-cukai-bc-40/id/update-no-aju"); ?>`,
    //                     method: "POST",
    //                     data: formData,
    //                     beforeSend: function(xhr) {
    //                         setLoading();
    //                         xhr.setRequestHeader('X-CSRF-Token', csrf.val());
    //                     },
    //                     complete: function() {
    //                         stopLoading();
    //                     },
    //                     method: "POST",
    //                     dataType: "json",
    //                     processData: false,
    //                     contentType: false,
    //                     success: function(res) {
    //                         csrf.val(res.token);
    //                         if (res.status) {
    //                             Swal.fire({
    //                                 icon: 'success',
    //                                 title: res.message,
    //                                 confirmButtonColor: '#4e73df',
    //                                 confirmButtonText: 'Ok'
    //                             }).then((result) => {
    //                                 table.ajax.reload();
    //                             });
    //                             $('#modalUpdateNoAju').modal('hide');

    //                         } else {
    //                             Swal.fire({
    //                                 icon: 'error',
    //                                 title: res.message,
    //                                 confirmButtonColor: '#4e73df',
    //                                 confirmButtonText: 'Ok'
    //                             }).then((result) => {
    //                                 table.ajax.reload();
    //                             });
    //                         }
    //                     }
    //                 })
    //             }
    //         })
    //     }
    // });


    // $('#no_urut_dokumen').keyup(function() {
    //     var noAju = $('#no_pengajuan').val();
    //     var splitValues = noAju.split("-");
    //     splitValues[3] = $(this).val();
    //     $('#no_pengajuan').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    // });

    // $("#tanggal_pengajuan").datepicker({
    //     todayHighlight: true,
    //     format: "dd/mm/yyyy",
    //     orientation: "bottom auto",
    //     autoclose: true
    // }).change(function() {
    //     var tanggalPengajuan = $(this).val();
    //     var noAju = $('#no_pengajuan').val();
    //     var tanggalPengajuanSplit = tanggalPengajuan.split("/");
    //     var noPengajuanSplit = noAju.split("-");
    //     $('#no_pengajuan').val(noPengajuanSplit[0] + '-' + noPengajuanSplit[1] + '-' + tanggalPengajuanSplit[2] + '' + tanggalPengajuanSplit[1] + '' + tanggalPengajuanSplit[0] + '-' + noPengajuanSplit[3]);
    // });

    // function noAjuShowModal(id, noAju) {
    //     console.log(id, noAju);
    //     var splitValues = noAju.split("-");

    //     var year = splitValues[2].substring(0, 4);
    //     var month = splitValues[2].substring(4, 6);
    //     var day = splitValues[2].substring(6, 8);

    //     var formattedDate = day + '/' + month + '/' + year;

    //     $('#tanggal_pengajuan').val(formattedDate);
    //     $('#no_pengajuan').val(noAju);
    //     $('#no_urut_dokumen').val(splitValues[3]);
    //     $('#modalUpdateNoAju').modal('show');
    //     $('#bc_purchase_order_id').val(id);
    // }

    function changeSort(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function detailBarang(id) {
        const width = 800;
        const height = 600;
        const left = window.innerWidth / 2 - width / 2;
        const top = window.innerHeight / 2 - height / 2;

        window.open(
            "<?= base_url('bea-cukai-bc-40/detail-barang/') ?>" + id,
            "_blank",
            `width=${width},height=${height},top=${top},left=${left},resizable=yes`
        );

    }

    function deleteAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 4.0 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("bc_purchase_order_id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-40/id/delete"); ?>`,
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
            title: 'Posting Dokumen BC 4.0 Lokal ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("bc_purchase_order_id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-40/posting"); ?>`,
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

    function unpostingAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di unposting?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Un Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("bea-cukai-bc-40/unposting"); ?>",
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
                    url: `<?= base_url("bea-cukai-bc-40/api/kirim-dokumen/"); ?>` + id,
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
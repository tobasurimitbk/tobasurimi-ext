<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>PO Lokal Bahan Penolong</h1>
        <?php if (can('Pembelian', 'PO Lokal BP', 'c')): ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("po-lokal-bahan-penolong/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <div class="row justify-content-end row-col-spp">
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Mulai">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="form-floating spp-ptspp" style="height: 50px;">
                        <select class="form-select form-out-search is_posted" name="is_posted" id="is_posted" aria-label="Floating label select example">
                            <option value="">PILIH STATUS SPP</option>
                            <option value="SUDAH POSTING">SUDAH POSTING</option>
                            <option value="BELUM POSTING">BELUM POSTING</option>
                        </select>
                        <label for="floatingInput" class="l-spp-ptspp">Tipe SPP</label>
                    </div>
                </div>
                <div class="col mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik No PO" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Tanggal Dibuat</th>
                                <th onclick="changeSort('divisiName')" class="sort">Departemen</th>
                                <th onclick="changeSort('poNo')" class="sort">No. PO</th>
                                <th onclick="changeSort('sppNo')" class="sort">No. SPP</th>
                                <th onclick="changeSort('supplierName')" class="sort">Supplier</th>
                                <th onclick="changeSort('total')" class="sort">Total</th>
                                <th>Order</th>
                                <th onclick="changeSort('statusPenerimaan')" class="sort">Status</th>
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
<div class="modal fade" id="historiModal" tabindex="-1" role="dialog" aria-labelledby="historiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historiModalLabel">Histori Penerimaan Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control po_no" name="po_no" id="po_no">
                            <label for="floatingInput">Nomor PO</label>
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
                <table class="table table-inside table-borderd nowrap table-hover-tobasurimi dataTable2" style="width: 100%;" id="tableHistori">
                    <thead>
                        <tr>
                            <td style="width: 10px;text-align: center;color:#E7323A;font-weight:bold;">No</td>
                            <td style="text-align: center;color:#E7323A;font-weight:bold;">Kode Barang</td>
                            <td style="text-align: center;color:#E7323A;font-weight:bold;">Nama Barang</td>
                            <td style="text-align: center;color:#E7323A;font-weight:bold;">Qty Order</td>
                            <td style="text-align: center;color:#E7323A;font-weight:bold;">Diterima</td>
                            <td style="text-align: center;color:#E7323A;font-weight:bold;">Sisa</td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>'
    let sort = "id";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("po-lokal-bahan-penolong/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.status = $(".status").val();
                data.is_posted = $(".is_posted").val();
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
                orderable: false
            },
            {
                data: "po_date",
                className: "text-center",
                sortable: false,
                orderable: false,
            },
            {
                data: "divisiName",
                className: "text-center"
            },
            {
                data: "po_no",
                className: "text-center"
            },
            {
                data: "spp_no",
                className: "text-center"
            },
            {
                data: "supplierName",
                className: "text-center"
            },
            {
                data: "total",
                className: "text-center",
                render: function(param) {
                    // Pastikan nilai param valid
                    return greatFormatRupiah(param);
                }
            },
            {
                data: "itemCount",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "status_penerimaan",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let status = row.is_posted
                    let status_penerimaan = row.status_penerimaan
                    let purchase_request_id = row.purchase_request_id
                    let un_posting = row.un_posting;

                    // jika belum posting
                    if (status !== "1") {
                        return `
                        <div class="mt-0">
                        <?php if (can('Pembelian', 'PO Lokal BP', 'p')) : ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("po-lokal-bahan-penolong/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (can('Pembelian', 'PO Lokal BP', 'a')) : ?>
                            <button data-toggle="tooltip" title="Posting" onclick="posting('${id}', 1)" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (can('Pembelian', 'PO Lokal BP', 'd')) : ?>
                            <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        </div>
                    `
                    } else {
                        // jika belum close po
                        if (status_penerimaan !== "CLOSED") {
                            var un_posting_row = '';
                            if (!un_posting) {
                                un_posting_row += `
                                <button  data-toggle="tooltip" title="Un-Posting" onclick="posting('${id}', 0)" class="btn btn-danger posting-spp">
                                    <i class="fa-solid fa-ban"></i>    
                                </button>
                                `
                            }

                            return `
                            <div class="mt-0">
                            <?php if (can('Pembelian', 'PO Lokal BP', 'ua')) : ?>
                                ${un_posting_row}
                            <?php endif; ?>
                                <button  data-toggle="tooltip" title="Histori LPB" onclick="displayHistory('${id}')" class="btn btn-success posting-spp">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </button>

                              <?php if (can('Pembelian', 'PO Lokal BP', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("po-lokal-bahan-penolong/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Pembelian', 'PO Lokal BP', 'a')) : ?>
                                <button  data-toggle="tooltip" title="Close PO" onclick="closePO('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-xmark fa-sm" aria-hidden="true"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        `
                        } else {
                            return `
                            <div class="mt-0">
                            <button  data-toggle="tooltip" title="Histori LPB" onclick="displayHistory('${id}')" class="btn btn-success posting-spp">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </button>
                            <?php if (can('Pembelian', 'PO Lokal BP', 'p')) : ?>
                            <button  data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("po-lokal-bahan-penolong/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                            </div>
                        `
                        }
                    }
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
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });
    $('.dataTable2').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: false,
        serverSide: false,
        ordering: true,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
    });
    $(document).ready(function() {
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

        $('.icon-dateStart').click(function() {
            $(".dateStart").focus();
        });

        $('.icon-dateEnd').click(function() {
            $(".dateEnd").focus();
        });

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd, .is_posted").change(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("po-lokal-bahan-penolong/id"); ?>/${data.id}`);
        })
    })

    const posting = function(id, status) {
        Swal.fire({
            icon: 'question',
            title: status == '0' ? 'UnPosting PO ?' : 'Posting PO ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("po-lokal-bahan-penolong/update-status"); ?>",
                    data: {
                        id: id,
                        status: status
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading()
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
        })
    }

    const closePO = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan Close PO?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Close',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("po-lokal-bahan-penolong/close-po"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading()
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
        })
    }

    const remove = function(id) {
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
                    url: "<?= base_url("po-lokal-bahan-penolong/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading()
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
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Dihapus, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function displayHistory(id) {
        $.ajax({
            url: "<?= base_url("po-lokal-bahan-penolong/histori-lpb"); ?>",
            data: {
                id: id
            },
            method: "GET",
            success: function(response) {
                $('#po_no').val(response.po_detail.po_no);
                $('#supplier_name').val(response.po_detail.supplierName);
                $('#lpb_no').val(response.lpb_no);
                const table = $('#tableHistori');
                var no = 1;
                table.find('tbody').empty();
                $.each(response.list_barang, function(i, v) {
                    var newRow = $('<tr>');
                    newRow.append($('<td style="text-align:center;">').text(no++));
                    newRow.append($('<td style="text-align:center;">').text(v.kode_barang));
                    newRow.append($('<td style="text-align:center;">').text(v.nama_barang));
                    newRow.append($('<td style="text-align:center;">').text(v.qty));
                    newRow.append($('<td style="text-align:center;">').text(v.diterima));
                    newRow.append($('<td style="text-align:center;">').text(v.sisa));
                    table.find('tbody').append(newRow);
                });
                $('#historiModal').modal('show');

            },
        });
    }
</script>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Tanda Terima Faktur Penerimaan Lokal Bahan Penolong</h1>
        <?php if (can('T. Terima Supplier', 'P. Lokal Bahan Penolong', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("tanda-terima-faktur-lokal-bp/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col mb-4">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" value="01<?= date('/m/Y') ?>" placeholder="Tanggal Mulai">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Selesai">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <select name="status_lunas" id="status_lunas" class="form-select status_lunas">
                        <option value="LUNAS">LUNAS</option>
                        <option selected value="BELUM LUNAS">BELUM LUNAS</option>
                    </select>
                </div>
                <div class="col mb-4">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Data" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('divisi')" class="sort">Departemen</th>
                                <th onclick="changeSort('receive_date')" class="sort">Tgl Terima</th>
                                <th onclick="changeSort('faktur_no')" class="sort">No Terima Faktur</th>
                                <th onclick="changeSort('suppliers.name')" class="sort">Supplier</th>
                                <th onclick="changeSort('nominal_faktur')" class="sort">Nominal Faktur</th>
                                <th>Jml Item</th>
                                <th onclick="changeSort('recipient')" class="sort">Penerima</th>
                                <th class="sort">Action</th>
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
<!-- history modal -->
<div class="modal fade" id="historiModal" tabindex="-1" role="dialog" aria-labelledby="historiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historiModalLabel">Histori Pembayaran Tanda Terima Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-inside table-borderd nowrap table-hover-tobasurimi dataTable2" style="width: 100%;" id="tableHistori">
                        <thead>
                            <tr>
                                <td style="width: 10px;text-align: center;color:#E7323A;font-weight:bold;">No</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;">Nomor Pembayaran</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;">Tanggal Pembayaran</td>
                                <td style="text-align: center;color:#E7323A;font-weight:bold;">Nilai Dibayar</td>

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
    let sort = "tanda_terima_faktur.id";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({

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
            url: "<?= base_url("tanda-terima-faktur-lokal-bp/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.status_lunas = $('.status_lunas').val();
                data.divisi_id = $('.divisi_id').val();
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
                orderable: false,
                width: "5%"
            },
            {
                data: "divisi",
                className: "text-left"
            },
            {
                data: "receive_date",
                className: "text-left"
            },
            {
                data: "faktur_no",
                className: "text-left"
            },
            {
                data: "supplier_name",
                className: "text-left",
            },
            {
                data: "nominal_faktur",
                className: "text-left",
                render: function(data, type, row) {
                    return data ? greatFormatRupiah(data) : '';
                }
            },
            {
                data: "jumlah_item",
                className: "text-left",
                searchable: false,
                sortable: false,
            },
            {
                data: "recipient",
                className: "text-left"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let is_used = row.is_used;
                    let htmlResult = '';

                    htmlResult = `
                        <a href="<?= base_url("tanda-terima-faktur-lokal-bp/id"); ?>/${id}" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    `;
                    if (is_used) {
                        htmlResult += `
                            <button  data-toggle="tooltip" title="Histori Pembayaran" onclick="displayHistory('${id}')" class="btn btn-success posting-spp">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </button>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("tanda-terima-faktur-lokal-bp/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>  
                        `;
                    } else {
                        htmlResult += `
                            <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("tanda-terima-faktur-lokal-bp/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        `;
                    }

                    return `
                        <div class="mt-0">
                            ${htmlResult}
                        </div>
                    `
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

    // $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
    //     const data = table.row(this).data();
    //     location.replace(`<?= base_url("tanda-terima-faktur-lokal-bp/id/"); ?>${data.id}`);
    // })

    // $(".status_lunas").change(function() {
    //     var status_lunas = $(this).val();
    //     if (status_lunas == "LUNAS") {
    //         // Ubah Status Disbled StartDate dan EndDate menjadi false
    //         $(".dateStart, .dateEnd").attr('disabled', false);
    //     } else {
    //         // Ubah Status Disbled StartDate dan EndDate menjadi false
    //         $(".dateStart, .dateEnd").attr('disabled', true);
    //     }
    // });


    $(".search").keyup(function() {
        table.ajax.reload();
    });

    $('.dateStart, .dateEnd, .divisi_id').change(function() {
        table.ajax.reload();
    });

    $(".dateStart, .dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('.status_lunas').change(function() {
        table.ajax.reload();
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function print(url) {
        window.open(url, "_blank");
    }

    function remove(id) {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        Swal.fire({
            icon: 'question',
            title: 'Hapus Tanda Terima Faktur Ini ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    url: "<?= base_url("tanda-terima-faktur-lokal-bp/delete"); ?>",
                    data: formData,
                    method: "POST",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        }).then((result) => {
                            table.ajax.reload();
                        });

                    },
                });
            }
        });
    }

    function displayHistory(id) {
        // console.log(id);
        $.ajax({
            url: "<?= base_url("/tanda-terima-faktur-lokal-bp/history-pembayaran"); ?>",
            data: {
                id: id
            },
            method: "GET",
            success: function(response) {
                console.log(response);

                const table = $('#tableHistori');
                var no = 1;

                table.find('tbody').empty();
                if (response.length > 0) {

                    $.each(response, function(i, v) {
                        var newRow = $('<tr>');
                        newRow.append($('<td style="text-align:center;">').text(no++));
                        newRow.append($('<td style="text-align:center;">').text(v.payment_no));
                        newRow.append($('<td style="text-align:center;">').text(v.payment_date));
                        newRow.append($('<td style="text-align:center;">').text(v.amount));

                        table.find('tbody').append(newRow);
                    });
                } else {
                    var newRow = $('<tr>');
                    newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Pembayaran</td>'));
                    table.find('tbody').append(newRow);
                }
                $('#historiModal').modal('show');

            },
        });
    }
</script>

<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header d-flex justify-content-end">
        <h1 class="me-auto">Jurnal Umum</h1>
        <button class="btn btn-lg" id="buttonImportModals" style="background-color:rgb(29, 64, 180); color: white; padding: 8px 16px; font-size: 14px; border: none; display: inline-flex; align-items: center; gap: 8px; margin-right: 230px;">
            <i class="fa fa-arrow-down" aria-hidden="true"></i> Import
        </button>
        <?php if (can('Accounting', 'Jurnal', 'p')) : ?>
            <button class="btn btn-discard btn-dropdown-export dropdown-toggle" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item" onclick="pdfExcel('<?= base_url("jurnal/print-pdf"); ?>')">PDF</button></li>
                <li><button class="dropdown-item" onclick="pdfExcel('<?= base_url("jurnal/print-excel"); ?>')">EXCEL</button></li>
            </ul>
        <?php endif; ?>
        <?php if (can('Accounting', 'Jurnal', 'c')) : ?>
            <a class="btn btn-show-form btn-add" href="<?= base_url("jurnal/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3 row-col-spp">
                <div class="col">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker start_date" id="start_date" name="start_date" placeholder="Tanggal Awal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-start_date"></i>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker end_date" id="end_date" name="end_date" placeholder="Tanggal Akhir">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-end_date"></i>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <select class="form-select type_transaksi" name="type_transaksi" id="type_transaksi" aria-label="Floating label select example">
                        <option value="">PILIH TIPE TRANSAKSI</option>
                        <?php foreach ($tipeTransaksi as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['value'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" id="search" placeholder="Cari No Transaksi" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('transaksi_jurnal.id')" class="sort">No</th>
                                <th onclick="changeSort('transaksi_jurnal.type_transaksi')" class="sort">Transaksi</th>
                                <th onclick="changeSort('transaksi_jurnal.no_transaksi')" class="sort">Nomor</th>
                                <th onclick="changeSort('transaksi_jurnal.tanggal_transaksi')">Tanggal</th>
                                <th onclick="changeSort('transaksi_jurnal.uraian_transaksi')" class="sort">Keterangan</th>
                                <th>Invoice</th>
                                <th onclick="changeSort('transaksi_jurnal.metode_input')">Metode Input</th>
                                <th>Nilai Valas</th>
                                <th>Nilai (IDR)</th>
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
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <button class="btn btn-outline-primary" onclick="downloadTemplate()">
                                <i class="bi bi-download"></i> Download Template
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="excelFileInput" class="form-label">Select Excel File</label>
                            <input type="file" id="excelFileInput" class="form-control" accept=".xlsx,.xls">
                        </div>
                    </div>
                    <p class="text-muted">Upload your Excel file here, then click "Import" to upload data.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="importExcel()">Import</button>
                </div>
            </div>
        </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "created_at";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("jurnal/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.start_date = $("#start_date").val();
                data.end_date = $("#end_date").val();
                data.type_transaksi = $('#type_transaksi').val();
                data.search = $("#search").val();
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
                orderable: false
            },
            {
                data: "transaksi_type_name",
                className: "text-center"
            },
            {
                data: "no_transaksi",
                className: "text-center"
            },
            {
                data: "tanggal_transaksi",
                className: "text-center",

            },
            {
                data: "uraian_transaksi",
                className: "text-center"
            },
            {
                data: "invoice",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "metode_input",
                className: "text-center"
            },
            {
                data: "nilai",
                className: "text-center",
                render: function(data, type, row) {
                    return greatFormatRupiah(row.nilai);
                },
                searchable: false,
                sortable: false
            }, {
                data: "nilai_idr",
                className: "text-center",
                render: function(data, type, row) {
                    return greatFormatRupiah(row.nilai_idr);
                },
                searchable: false,
                sortable: false
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let tutupBuku = row.tutup_buku;

                    if (tutupBuku === 0) {
                        return `
                        <div class="mt-0">
                            <?php if (can('Accounting', 'Jurnal', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("jurnal/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Accounting', 'Jurnal', 'd')) : ?>
                                <button data-toggle="tooltip" title="Hapus" onclick="destroy('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    `
                    } else {

                        return `
                            <div class="mt-0" >
                              <?php if (can('Accounting', 'Jurnal', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("jurnal/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            </div>
                        `;
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

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("jurnal/id"); ?>/${data.id}`);
    });

    $("#start_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $("#end_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('.icon-dateStart').click(function() {
        $("#start_date").focus();
    });

    $('.icon-dateEnd').click(function() {
        $("#end_date").focus();
    });

    $(".dataTable_info").addClass("pt-0");

    $("#search").keyup(function() {
        table.ajax.reload();
    })

    $("#start_date,#end_date,#type_transaksi").change(function() {
        table.ajax.reload();
    })

    function destroy(id) {
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
                    url: "<?= base_url("jurnal/delete"); ?>",
                    data: {
                        id: id
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
                    onError: function(response) {
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

    function print(url) {
        window.open(url, '_blank');
    }

    function pdfExcel(url) {
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var type_transaksi = $('#type_transaksi').val();
        var search = $("#search").val();
        window.open(url + `?start_date=${start_date}&end_date=${end_date}&type_transaksi=${type_transaksi}&search=${search}&sort=${sort}&sortType=${sortType}`, "_blank");
    }

    function downloadTemplate() {
        $.ajax({
            url: '<?= base_url("jurnal/import/template") ?>', // Endpoint di BE
            method: 'GET',
            xhrFields: {
                responseType: 'blob' // Pastikan menerima file sebagai blob
            },
            success: function (data, status, xhr) {
                const fileName = xhr.getResponseHeader('Content-Disposition')
                    .split('filename=')[1]
                    .replace(/"/g, '');
                const url = window.URL.createObjectURL(data);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            },
            error: function (xhr, status, error) {
                console.error('Error downloading template:', error);
            }
        });
    }


    // Function to handle the Excel file upload using AJAX
    function importExcel() {
    const fileInput = document.getElementById('excelFileInput');
    const file = fileInput.files[0];
    const csrf = $(`[name="${csrfToken}"]`);
    
    if (file) {
        const formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: '<?= base_url("jurnal/import"); ?>',
            type: 'POST',
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    alert('Import successful');
                } else {
                    alert('Import failed: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error importing file: ' + errorThrown);
            }
        });
    } else {
        alert('Please select a file to import.');
    }
}


    $(document).ready(function() {
        $('#buttonImportModals').on('click', function() {
            // Ensure modal is properly initialized
            $('#importModal').modal({
            backdrop: 'static', // Prevent clicking outside to close
            keyboard: false // Prevent closing with the escape key
            });
            $('#importModal').modal('show'); // Show the modal
        });
    });


</script>
<?= $this->endSection(); ?>
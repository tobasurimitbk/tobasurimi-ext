<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Kwitansi Bulanan PO Bahan Baku</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-supplier-lokal-bb"); ?>">
                Kembali
            </a>
            <a class="btn btn-hide-form btn-discard float-right" id="btn_generate_no">
                <i class="fa-solid fa-clock-rotate-left"></i> Generate No
            </a>
            <?php if (can('Laporan', 'Supplier Lokal BB', 'p')) : ?>
                <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #FFA426 !important;color: white !important;border: 0px solid !important;">
                    Print All
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <!-- <li><button class="dropdown-item" id="btn-print-f4">Print TB F4 (U. Supplier)</button></li> -->
                    <li><button class="dropdown-item" id="btn-print-continous">Print TB (U. Supplier)</button></li>
                    <li><button class="dropdown-item" id="btn-print-kasir">Print TB (U. Kasir)</button></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-page-list-attendance">
                <div class="col-6 mb-2">
                    <form id="search_form" action="#" name="search_form" class="kt-form kt-form--fit kt-margin-b-20">
                        <select required name="month" class="month" id="month">
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                $temp = (strlen($i) == 1) ? ("0" . $i) : $i;
                                $checked = ($month == $temp) ? "selected" : "";
                            ?>
                                <option value="<?php echo $temp; ?>" <?php echo $checked; ?>><?php echo $temp; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <select required name="year" class="year" id="year">
                            <?php
                            for ($i = date("Y") - 2; $i <= date("Y") + 2; $i++) {
                                $checked = ($year == $i) ? "selected" : "";
                            ?>
                                <option value="<?php echo $i; ?>" <?php echo $checked; ?>><?php echo $i; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-brand--icon filterBulan" id="">
                            <span>
                                <i class="la la-print"></i>
                                <span>Cari</span>
                            </span>
                        </button>

                    </form>
                </div>
                <div class="col-6 mb-2">
                    <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <?= csrf_field() ?>
                <div class="row justify-content-end row-col-spp mb-3">
                    <div class="col-md-3">
                        <select class="form-select tb_search" name="tb_search" id="tb_search" aria-label="Floating label select example">
                            <option value="" selected>SEMUA SUPPLIER</option>
                            <option value="PUNYA TB">SUPPLIER PUNYA NILAI TB</option>
                            <option value="TIDAK PUNYA TB">SUPPLIER TIDAK PUNYA NILAI TB</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Data" value="" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th onclick="changeSort('name')">Supplier</th>
                                <th>Total</th>
                                <th>No Kwitansi</th>
                                <th>Tanggal</th>
                                <th style="width: 50px;">Print</th>
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
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Nomor Kwitansi TB</h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <div class="input-group">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" value="<?= date('Y-m') ?>" class="form-control periode_kwintansi_tb" id="periode_kwintansi_tb" name="periode_kwintansi_tb" />
                        <label style="z-index: 1;" style="z-index: 1;">Periode Kwintansi</label>
                    </div>
                    <div class="input-group-append" style="height:50px;">
                        <button class="btn btn-secondary" type="button" id="btn_search_kwintansi">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTableGenerate" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>No Kwitansi</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-3" id="btnHideGenerate">Kembali</button>
                <button type="button" class="btn btn-submit-form" id="btnSubmitNomor">Generate</button>
            </div>
        </div>

    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "name";
    let sortType = "asc";
    var row = 0;
    var listData = [];

    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [1000],
            [1000],
        ],
        pageLength: 1000,
        ajax: {
            url: "<?= base_url("laporan-supplier-lokal-bb/kwitansi-tb/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.year = $(".year").val();
                data.month = $(".month").val();
                data.tb_search = $(".tb_search").val();
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            },
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
            sortable: false
        }, {
            data: "name",
            className: "text-left",
        }, {
            data: "total",
            className: "text-left",
            searchable: false,
            sortable: false,
        }, {
            data: "no_kwitansi",
            searchable: false,
            sortable: false,
            className: "text-left"
        }, {
            data: "tanggal",
            searchable: false,
            sortable: false,
            className: "text-left",
            render: function(data, type, row) {
                if (row.no_kwitansi == "") {
                    return "";
                } else {
                    let inputId = "tanggal_" + row.id;
                    return `
                        <div class="mt-0">
                            <input disabled id="${inputId}" class="tanggal form-control search form-out-search bg-light" data-id="${row.id}" type="date" value="${row.tanggal}">
                        </div>
                    `;
                }

            }
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                let no_kwitansi_hash = row.no_kwitansi_hash;
                let no_kwitansi = row.no_kwitansi;
                let tanggal = row.tanggal;
                let year = $(".year").val();
                let month = $(".month").val();


                if (row.is_print == "0") {
                    return '';
                } else {
                    if (no_kwitansi != '') {
                        return `
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('laporan-supplier-lokal-bb/kwitansi-tb/print/${id}/${year}-${month}/${no_kwitansi_hash}', '${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        `;
                    }
                }

            }
        }],
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
    })

    $(".filterBulan").click(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('.tb_search').change(function() {
        table.ajax.reload();
    });

    $(".search").change(function() {
        table.ajax.reload();
    })

    const print = function(url, id) {
        let inputId = "tanggal_" + id;
        let element = $('#' + inputId);
        let splitData = url.split("/");
        let res = splitData[0] + '/' + splitData[1] + '/' + splitData[2] + '/' + splitData[3] + '/' + splitData[4] + '/' + element.val() + '/' + splitData[5];

        window.open("<?= base_url('/') ?>" + res, "_blank");
    }

    $("#periode_kwintansi_tb").datepicker({
        format: "yyyy-mm",
        startView: "months", // langsung tampilin bulan
        minViewMode: "months", // cuma bisa pilih bulan
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
    });

    $('#btnHideGenerate').click(function(e) {
        e.preventDefault();
        $('#generateModal').modal('hide')
    });

    $('#btnSubmitNomor').click(function(e) {
        e.preventDefault();
        if (listData.length == 0) {
            Swal.fire({
                icon: 'error',
                title: "Data yang akan digenerate kosong",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            var csrf = $(`[name="${csrfToken}"]`);
            var itemError = null;

            $.each(listData, function(i, v) {
                var supplierID = v.supplier_id;
                var noKwitansi = $(`.no_kwitansi[supplier_id="${supplierID}"]`).val();
                var tanggal = $(`.tanggal[supplier_id="${supplierID}"]`).val();

                if (noKwitansi == "" || tanggal == "") {
                    itemError = v;
                    return false;
                }

                listData[i].tanggal = tanggal;
                listData[i].no_kwitansi = noKwitansi;
            });

            if (itemError != null) {
                Swal.fire({
                    icon: 'error',
                    title: "No Kwitansi atas nama " + itemError.supplier_name + ", tidak ada tanggal dan nomor kwitansinya",
                    confirmButtonColor: '#4e73df',
                });
                return;
            } else {
                var formData = new FormData();
                formData.set('listData', JSON.stringify(listData));

                $.ajax({
                    url: "<?= base_url("laporan-supplier-lokal-bb/generate-no-kwitansi-action"); ?>",
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
                    success: function(response) {
                        if (response.status == false) {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            });
                            return;
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            });
                            listData = [];
                            table.ajax.reload();
                            $('#generateModal').modal('hide');
                        }

                    }
                });

            }
        }
    });

    // $('#btn-print-f4').on('click', function(e) {
    //     e.preventDefault();
    //     var month = $('#month').val();
    //     var year = $('#year').val();
    //     if (month == "") {
    //         alert("Pilih bulan");
    //         return;
    //     } else if (year == "") {
    //         alert("Pilih tahun");
    //         return;
    //     } else {
    //         var url = "<?= base_url('laporan-supplier-lokal-bb/print-all-kwitansi-tb') ?>?month=" + month + "&year=" + year + "&kertas=f4";
    //         window.open(url);
    //     }
    // });

    $('#btn_generate_no').click(function(e) {
        e.preventDefault();
        listData = [];
        drawTabel(listData);
        $('#generateModal').modal('show')
    });

    $('#btn_search_kwintansi').click(function(e) {
        e.preventDefault();
        var year_month = $('#periode_kwintansi_tb').val();
        if (year_month == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih periode kwintansi",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            $.ajax({
                url: "<?= base_url("laporan-supplier-lokal-bb/generate-no-kwintansi"); ?>",
                data: {
                    year_month: year_month
                },
                beforeSend: function(xhr) {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                method: "GET",
                success: function(response) {
                    if (response.status) {
                        listData = response.data;
                        drawTabel(listData);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            });
        }
    });

    $('#btn-print-continous').on('click', function(e) {
        e.preventDefault();
        var month = $('#month').val();
        var year = $('#year').val();
        if (month == "") {
            alert("Pilih bulan");
            return;
        } else if (year == "") {
            alert("Pilih tahun");
            return;
        } else {
            var url = "<?= base_url('laporan-supplier-lokal-bb/print-all-kwitansi-tb') ?>?month=" + month + "&year=" + year + "&kertas=continous";
            window.open(url);
        }
    });

    $('#btn-print-kasir').on('click', function(e) {
        e.preventDefault();
        var month = $('#month').val();
        var year = $('#year').val();
        if (month == "") {
            alert("Pilih bulan");
            return;
        } else if (year == "") {
            alert("Pilih tahun");
            return;
        } else {
            var url = "<?= base_url('laporan-supplier-lokal-bb/print-all-kwitansi-tb') ?>?month=" + month + "&year=" + year + "&kertas=kasir";
            window.open(url);
        }
    });

    function drawTabel(listData) {
        const table = $('#dataTableGenerate');
        var no = 1;
        table.find('tbody').empty();
        table.find('tfoot').empty();
        if (listData.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="5">Tidak ada data kwitansi bulanan</td>'));
            table.find('tfoot').append(newRow);
        } else {
            $.each(listData, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(v.supplier_name));
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.total).toFixed(2))));
                newRow.append($('<td>').html(`
                    <div class="mt-0">
                        <input supplier_id="${v.supplier_id}" class="no_kwitansi form-control form-out-search" type="text" value="${v.no_kwitansi}" style="height:40px !important;">
                    </div>
                `));
                newRow.append($('<td>').html(`
                    <div class="mt-0">
                        <input supplier_id="${v.supplier_id}" class="tanggal form-control form-out-search" type="text" value="${v.tanggal}" style="height:40px !important;">
                    </div>
                `));
                table.find('tbody').append(newRow);
            });
        }


        $(".tanggal").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });
    }

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
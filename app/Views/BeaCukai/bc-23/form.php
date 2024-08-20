<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>Tambah Dokumen BC 2.3</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Kembali
            </a>
            <?php if (can('Bea Cukai', 'BC 2.3', 'p')) : ?>
                <button class="btn btn-warning btn-dropdown-export ml-4  dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 10px;">
                    Export
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <li><button onclick="printPdf()" class="dropdown-item print-pdf">PDF</button></li>
                    <li><button onclick="printExcel()" class="dropdown-item print-pdf">EXCEL</button></li>
                </ul>
            <?php endif ?>
            <?php if (can('Bea Cukai', 'BC 2.3', 'c')) : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>

        </div>
    </div>
    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            PILIH NOMOR PURCHASE ORDER
        </div>
        <div class="card-body">
            <form class="create-form">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select class="form-select po_type" id="po_type" name="po_type" aria-label="Floating label select example">
                                <option value=""></option>
                                <option value="IMPORT BAKU">PO IMPORT BAHAN BAKU</option>
                                <option value="IMPORT PENOLONG">PO IMPORT BAHAN PENOLONG</option>

                            </select>
                            <label style="z-index: 1;">Tipe Purchase Order</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating mt-1" style="height: 50px;">
                            <select class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                            </select>
                            <label style="z-index: 1;">Supplier</label>
                        </div>
                        <small class="mb-3"><i>Hanya menampilkan data supplier yang LPB nya belum dibuatkan dokumen Bea Cukai</i></small>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: center; width:5px;">No</th>
                            <th style="text-align: center; width:5px;">#</th>
                            <th style="text-align: center;">Tgl PO</th>
                            <th style="text-align: center;">Tgl LPB</th>
                            <th style="text-align: center;">No LPB</th>
                            <th style="text-align: center;">No PO</th>
                            <th style="text-align: center;">Kode</th>
                            <th style="text-align: center;">Barang</th>
                            <th style="text-align: center;">Qty PO</th>
                            <th style="text-align: center;">Qty Diterima</th>
                            <th style="text-align: center;">Harga</th>
                        </tr>
                    </thead>
                    <tbody class="body-table">
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var listData = [];
    var listDataSelected = [];

    var dataTable = $('#dataTable').DataTable({
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
        display: "stripe",
        searching: true,
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('#po_type').select2({
        placeholder: "Pilih Tipe Purchase Order",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListSupplier();
    });


    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListPurchaseOrder();
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.btn-submit-parent').click(function(e) {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("penerimaan_barang_id");
        }).get();
        var id_selected = getIDListDataSelected();

        $.each(listData, function(i, v) {
            var currentID = Number(v.penerimaan_barang_id);
            if ($.inArray(currentID, dataIds) !== -1) {
                var isIDSelected = $.grep(listDataSelected, function(item) {
                    return item.penerimaan_barang_id == Number(currentID);
                }).length > 0;

                if (!isIDSelected) {
                    listDataSelected.push(listData[i]);
                }
            }
        });

        if (listDataSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: "Pilih data purchase order yang akan dibuat dokumen bea cukai",
                confirmButtonColor: '#4e73df',
            })
        } else {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                let data = new FormData(document.querySelector(".create-form"));
                data.append('listData', JSON.stringify(listDataSelected));

                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-23/create"); ?>",
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading()
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                window.location.replace("<?= base_url('bea-cukai-bc-23/po/') ?>" + response.id)
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan !',
                                    confirmButtonColor: '#4e73df',
                                    cancelButtonColor: '#d33',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                })
                            }
                        },
                    });
                }
            })
        }

    })

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listDataSelected, function(i, v) {
            id_selected.push(v.penerimaan_barang_id);
        })
        return id_selected;
    }


    function getListSupplier() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-23/list-supplier'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                po_type: $(".po_type option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".supplier_id").empty()
                $(".supplier_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".supplier_id").append(`<option  value="${item.id}">(${item.kode}) ${item.name}</option>`)
                })
                $(".supplier_id").val();
            }
        });
    }

    function getListPurchaseOrder() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-23/list-po'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                po_type: $(".po_type option:selected").val(),
                supplier_id: $(".supplier_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                // DRAW LIST PO TABLE
                var data = res.data;
                listData = [];
                listDataSelected = [];
                listData = data;

                if ($.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().clear().draw();
                    dataTable.destroy();
                }

                const table = $('#dataTable');
                table.find('tbody').empty();
                table.find('tfoot').empty();

                var no = 1;
                $.each(data, function(i, v) {
                    var newRow = $('<tr>');
                    newRow.append($('<td style="text-align:center;">').text(no++));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                                <div class="form-check">
                                    <input data-penerimaan_barang_id="${v.penerimaan_barang_id}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                                </div>
                            `
                    ));


                    newRow.append($('<td style="text-align:center;">').text(v.po_date));
                    newRow.append($('<td style="text-align:center;">').text(v.lpb_date));
                    newRow.append($('<td style="text-align:center;">').text(v.lpb_no));
                    newRow.append($('<td style="text-align:center;">').text(v.po_no));
                    newRow.append($('<td style="text-align:center;">').text(v.kode_barang));
                    newRow.append($('<td style="text-align:center;">').text(v.barang_name));
                    newRow.append($('<td style="text-align:center;">').text(parseFloat(v.qty_po).toFixed(2)));
                    newRow.append($('<td style="text-align:center;">').text(parseFloat(v.qty_lpb).toFixed(2)));
                    newRow.append($('<td style="text-align:center;">').text(v.harga));
                    table.find('tbody').append(newRow);
                });

                dataTable = $('#dataTable').DataTable({
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
                    lengthMenu: [
                        [100],
                        [100]
                    ],
                    display: "stripe",
                    searching: true,
                    language: {
                        emptyTable: "Tidak Ada Data",
                        lengthMenu: "Show _MENU_ entries",
                        paginate: {
                            previous: '<i class="fa fa-angle-left"></i>',
                            next: '<i class="fa fa-angle-right"></i>'
                        }
                    }
                });

                dataTable.draw();

            }
        });
    }

    function printPdf() {
        var supplierId = $(".supplier_id option:selected").val();
        var poType = $(".po_type option:selected").val();

        if (supplierId && poType) {
            window.open("<?= base_url('bea-cukai-bc-23/export-pdf') ?>?supplier_id=" + supplierId + "&po_type=" + poType, "_blank");
        } else {
            Swal.fire({
                icon: 'error',
                title: "Pilih Tipe Purchase Order dan Supplier Dahulu",
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        }
    }

    function printExcel() {
        var supplierId = $(".supplier_id option:selected").val();
        var poType = $(".po_type option:selected").val();

        if (supplierId && poType) {
            window.open("<?= base_url('bea-cukai-bc-23/export-excel') ?>?supplier_id=" + supplierId + "&po_type=" + poType, "_blank");
        } else {
            Swal.fire({
                icon: 'error',
                title: "Pilih Tipe Purchase Order dan Supplier Dahulu",
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        }
    }
</script>

<?= $this->endSection(); ?>
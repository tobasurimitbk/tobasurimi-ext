<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <div class="col-md-10">
            <h1>Costing</h1>
        </div>

        <div class="col-md-2 text-right">
            <div class="btn-group">
                <button type="button" class="btn btn-warning">Export</button>
                <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item" onclick="printPDF('<?= base_url("/laporan-accounting/costing/printPDF"); ?>')">PDF</a>
                    <a class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-accounting/costing/printExcel"); ?>')">Excel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="input-group" style="height: 50px;">
                                <input style="height: auto;" autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Pilih Bulan">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="card-table" style="display: none;">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark header-table" id="header-table">
                        </thead>
                        <tbody class="body-table" id="body-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let list_items_title_name = [];
    let list_items_title_name_horizontal = [];
    let list_items_production_detail = [];

    $(".dateStart").datepicker({
        todayHighlight: true,
        format: "mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        startView: "months",
        minViewMode: 1
    }).change(function(e) {
        list_items_title_name = [];
        list_items_title_name_horizontal = [];
        if ($(this).val()) {
            getCosting();
        } else {
            drawTableHeader();
            drawTable();
            $("#card-table").css("display", "none");
        }
    })

    const getCosting = function() {
        var tanggal_awal = $(".dateStart").val();
        setLoading();
        $.ajax({
            url: `<?= base_url("laporan-accounting/costing/getdata"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                month: tanggal_awal
            },
            success: function(res) {
                res.data.settingCosting.forEach(function(item) {
                    list_items_title_name.push(item);
                });
                res.data.productionResultDataTitle.forEach(function(item) {
                    list_items_title_name_horizontal.push(item);
                });
                drawTableHeader();
                drawTable();
                $("#card-table").css("display", "");
                stopLoading()
            }
        })
    }

    const drawTableHeader = function() {
        $('.header-table').empty();
        var no = 1;
        if (list_items_title_name_horizontal.length === 0 || list_items_title_name.length === 0) {
            $('.header-table').append(`
            <tr>
                <td colspan="3" class="text-center">Data Tidak Ada</td>
            </tr>
        `);
        } else {
            var headerRow = `<tr style="font-weight: bold !important;font-size: 14px !important;text">
                            <th colspan="2" rowspan="2" class="text-center">Keterangan</th>
                            <th rowspan="2" class="text-center"></th>
                            <th rowspan="2" class="text-center">Total</th>`;

            list_items_title_name_horizontal.forEach((valueSetting) => {
                if (valueSetting.type == "JADI") {
                    headerRow += `<th colspan="2" class="text-center">${valueSetting.barang_name}</th>`;
                }
            });
            headerRow += `</tr>`;
            $('.header-table').append(headerRow);

            var specRow = '<tr>';
            var priceRow = '<tr>';
            list_items_title_name_horizontal.forEach((valueSetting) => {
                if (valueSetting.type == "JADI") {
                    specRow += `<th class="text-center">${valueSetting.spesifikasi}</th>`;
                    specRow += `<th class="text-center">Harga</th>`;
                }
            });
            specRow += '</tr>';
            priceRow += '</tr>';
            $('.header-table').append(specRow);
            $('.header-table').append(priceRow);
        }
    }

    const drawTable = function() {
        $('.body-table').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items_title_name_horizontal.length === 0 || list_items_title_name.length === 0) {
            row += `
                <tr>
                    <td colspan="3" class="text-center">Data Tidak Ada</td>
                </tr>
            `;
            $('.tfoot').append(row);
        } else {
            list_items_title_name.forEach((valueSetting) => {
                if (valueSetting.parent_id === null) {
                    row += `<tr>`;
                    row += `<td colspan="2" style="font-weight: bold !important;font-size: 14px !important;">${valueSetting.name}</td>
                        <td></td>
                        <td></td>`;
                    list_items_title_name_horizontal.forEach((valueHorizontal) => {
                        if (valueHorizontal.type == "JADI") {
                            row += `<td class="text-center"></td>`;
                            row += `<td class="text-center"></td>`;
                        }
                    });
                    row += `</tr>`;
                    list_items_title_name.forEach((childSetting) => {
                        if (childSetting.parent_id === valueSetting.id) {
                            let jmlhJurnal = parseFloat(childSetting.jmlhJurnal);
                            row += `<tr>`;
                            row += `<td width="10%" style="font-size: 13px !important;">${childSetting.name}</td>
                            <td width="10%"></td>
                            <td width="3%"></td>
                            <td width="10%">${jmlhJurnal ? formatRupiah(jmlhJurnal) : ""}</td>`;

                            list_items_title_name_horizontal.forEach((valueHorizontal) => {
                                let qtyHorizontal = parseFloat(valueHorizontal.qty);
                                let jmlhTotal = jmlhJurnal * qtyHorizontal;
                                if (childSetting.name == "RAW MATERIAL I") {
                                    var sumQty = 0.0;
                                    childSetting.rawMaterial.forEach((valueSettingRawMaterialI) => {
                                        if (valueHorizontal.type == "JADI" && valueHorizontal.production_result_id == valueSettingRawMaterialI.production_result_id) {
                                            sumQty += parseFloat(valueSettingRawMaterialI.qty)
                                        }
                                    });
                                    row += `<td class="text-center">${formatRupiah(jmlhTotal)}</td>`;
                                    row += `<td class="text-center"></td>`;
                                }
                            });
                            row += `</tr>`;
                            if (childSetting.name == "RAW MATERIAL II") {
                                childSetting.rawMaterialPenolong.forEach((childParentSetting) => {
                                    let jmlhJurnal = parseFloat(childParentSetting.jmlhJurnal);
                                    row += `<tr>`;
                                    row += `<td width="10%"></td>
                                    <td width="10%">${childParentSetting.parent_name}</td>
                                    <td width="3%"></td>
                                    <td width="10%">${jmlhJurnal ? formatRupiah(jmlhJurnal) : ""}</td>`;
                                    list_items_title_name_horizontal.forEach((valueHorizontal) => {
                                        if (valueHorizontal.type == "JADI" && childParentSetting.production_result_id == valueHorizontal.production_result_id) {
                                            let qtyHorizontal = parseFloat(valueHorizontal.qty);
                                            let jmlhTotal = jmlhJurnal * qtyHorizontal;
                                            console.log("Result 1 : " + qtyHorizontal);
                                            console.log("Result 2 : " + jmlhTotal);
                                            row += `<td class="text-center">${formatRupiah(jmlhTotal)}</td>`;
                                            row += `<td class="text-center"></td>`;
                                        }
                                    });
                                    row += `</tr>`;
                                });
                            } else {
                                list_items_title_name.forEach((childParentSetting) => {
                                    let jmlhJurnal = parseFloat(childParentSetting.jmlhJurnal);
                                    if (childParentSetting.parent_id === childSetting.id) {
                                        row += `<tr>`;
                                        row += `<td width="10%"></td>
                                        <td width="10%">${childParentSetting.name}</td>
                                        <td width="3%"></td>
                                        <td width="10%">${jmlhJurnal ? formatRupiah(jmlhJurnal) : ""}</td>`;
                                        list_items_title_name_horizontal.forEach((valueHorizontal) => {
                                            let qtyHorizontal = parseFloat(valueHorizontal.qty);
                                            let jmlhTotal = jmlhJurnal * qtyHorizontal;
                                            if (valueHorizontal.type == "JADI") {
                                                row += `<td class="text-center">${formatRupiah(jmlhTotal)}</td>`;
                                                row += `<td class="text-center"></td>`;
                                            }
                                        });
                                        row += `</tr>`;
                                    }
                                });
                            }
                        }
                    });
                }
            });
            $('.body-table').append(row);
        }
    }

    const printPDF = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var search = $(".search").val() ? $(".search").val() : "all";
        var filter = $(".list_supplier").val() ? $(".list_supplier").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + search;
        // console.log(url2);
        window.open(url2, "_blank");
    }

    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var search = $(".search").val() ? $(".search").val() : "all";
        var filter = $(".list_supplier").val() ? $(".list_supplier").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + search;
        // console.log(url2);
        window.open(url2, "_blank");
    }

    function formatRupiah(angka) {
        var number_string = angka.toString(),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return 'Rp ' + rupiah;
    }
</script>
<?= $this->endSection(); ?>
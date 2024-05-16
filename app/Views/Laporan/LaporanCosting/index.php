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
                        <div class="col-md-3">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($dataDivisi as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Departemen</label>
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

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        list_items_title_name = [];
        list_items_title_name_horizontal = [];
        if ($(this).val()) {
            getCosting();
        } else {
            drawTableHeader();
            drawTable();
            $("#card-table").css("display", "none");
        }
    });

    $("#divisi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const getCosting = function() {
        var tanggal_awal = $(".dateStart").val();
        var divisi_id = $("#divisi_id").val();
        if (tanggal_awal && divisi_id) {
            setLoading();
            $.ajax({
                url: `<?= base_url("laporan-accounting/costing/getdata"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    month: tanggal_awal,
                    divisi_id: divisi_id,
                },
                success: function(res) {
                    console.log(res);
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
    }

    const drawTableHeader = function() {
        $('.header-table').empty();
        var no = 1;
        if (list_items_title_name_horizontal.length === 0) {
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
                headerRow += `<th colspan="3" class="text-center">${valueSetting.barang_name}</th>`;
            });
            headerRow += `</tr>`;
            $('.header-table').append(headerRow);

            var specRow = '<tr>';
            var priceRow = '<tr>';
            list_items_title_name_horizontal.forEach((valueSetting) => {
                specRow += `<th class="text-center">${valueSetting.spesifikasi}</th>`;
                specRow += `<th class="text-center">Qty</th>`;
                specRow += `<th class="text-center">Harga</th>`;
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
            let costOfProduction = 0;
            let costOfProductionPerSpek = 0;
            list_items_title_name.forEach((valueSetting) => {
                if (valueSetting.parent_id === null) {
                    if (valueSetting.name != "COST OF PRODUCTION") {
                        row += `<tr>`;
                        row += `<td colspan="2" style="font-weight: bold !important;font-size: 14px !important;">${valueSetting.name}</td>
                        <td></td>
                        <td></td>`;
                        list_items_title_name_horizontal.forEach((valueHorizontal) => {
                            // if (valueHorizontal.type == "JADI") {
                            row += `<td class="text-center"></td>`;
                            row += `<td class="text-center"></td>`;
                            // }
                        });
                        row += `</tr>`;
                    }
                    list_items_title_name.forEach((childSetting) => {
                        if (childSetting.parent_id === valueSetting.id) {
                            let jmlhJurnal = parseFloat(childSetting.jmlhJurnal);
                            let totalQtyHorizontal = 0;
                            list_items_title_name_horizontal.forEach((valueHorizontal) => {
                                let qtyHorizontal = parseFloat(valueHorizontal.qty);
                                if (childSetting.name == "RAW MATERIAL I") {
                                    var totalHarga = 0.0;
                                    childSetting.rawMaterial.forEach((valueSettingRawMaterialI) => {
                                        if (valueHorizontal.production_result_id == valueSettingRawMaterialI.production_result_id && valueHorizontal.production_result_detail_id == valueSettingRawMaterialI.production_result_detail_id) {
                                            totalHarga += parseFloat(valueSettingRawMaterialI.harga_barang)
                                        }
                                    });
                                    costOfProductionPerSpek += parseFloat(totalHarga);
                                    totalQtyHorizontal += totalHarga ? totalHarga : 0;
                                }
                            });
                            costOfProduction += parseFloat(totalQtyHorizontal);
                            row += `<tr>`;
                            row += `<td width="10%" style="font-size: 13px !important;">${childSetting.name}</td>
                            <td width="10%"></td>
                            <td width="3%"></td>
                            <td width="10%">${childSetting.name == "RAW MATERIAL I" ? formatRupiah(totalQtyHorizontal) : ""}</td>`;
                            list_items_title_name_horizontal.forEach((valueHorizontal) => {
                                let qtyHorizontal = parseFloat(valueHorizontal.qty);
                                if (childSetting.name == "RAW MATERIAL I") {
                                    var totalHarga = 0.0;
                                    var totalHargaSatuan = 0.0;
                                    childSetting.rawMaterial.forEach((valueSettingRawMaterialI) => {
                                        if (valueHorizontal.production_result_id == valueSettingRawMaterialI.production_result_id && valueHorizontal.production_result_detail_id == valueSettingRawMaterialI.production_result_detail_id) {
                                            totalHarga += parseFloat(valueSettingRawMaterialI.harga_barang)
                                        }
                                    });
                                    totalHargaSatuan = totalHarga / qtyHorizontal;
                                    row += `<td class="text-center">${totalHarga ? formatRupiah(totalHarga)  : formatRupiah(0)}</td>`;
                                    row += `<td class="text-center">${qtyHorizontal.toLocaleString().replaceAll(',', '.')}</td>`;
                                    row += `<td class="text-center">${formatRupiah(totalHargaSatuan)}</td>`;
                                }
                            });
                            row += `</tr>`;
                            if (childSetting.name == "RAW MATERIAL II") {
                                var totalHarga = 0.0;
                                childSetting.nameRawMaterialPenolong.forEach((childParentSetting) => {
                                    costOfProduction += parseFloat(childParentSetting.total_barang);
                                    row += `<tr>`;
                                    row += `<td width="10%"></td>
                                    <td width="10%">${childParentSetting.parent_name}</td>
                                    <td width="3%"></td>
                                    <td width="10%">${childParentSetting.total_barang ? formatRupiah(parseFloat(childParentSetting.total_barang)) : ""}</td>`;
                                    childSetting.rawMaterialPenolong.forEach((valueMaterialPenolong) => {
                                        // if () {
                                        list_items_title_name_horizontal.forEach((valueHorizontal) => {
                                            let qtyHorizontal = parseFloat(valueHorizontal.qty);
                                            if (valueMaterialPenolong.parent_type_id == childParentSetting.parent_type_id && valueMaterialPenolong.production_result_id == valueHorizontal.production_result_id && valueMaterialPenolong.production_result_detail_id == valueHorizontal.production_result_detail_id) {
                                                totalHarga = parseFloat(valueMaterialPenolong.total_barang);
                                                // costOfProductionPerSpek += parseFloat(totalHarga);
                                                Harga = parseFloat(valueMaterialPenolong.harga_barang) / qtyHorizontal;
                                                row += `<td class="text-center">${totalHarga ? formatRupiah(totalHarga) : formatRupiah(0)}</td>`;
                                                row += `<td class="text-center">${qtyHorizontal.toLocaleString().replaceAll(',', '.')}</td>`;
                                                row += `<td class="text-center">${Harga ? formatRupiah(Harga) : formatRupiah(0)}</td>`;
                                            }
                                        });
                                        // }
                                    });
                                    row += `</tr>`;
                                });
                            } else {
                                list_items_title_name.forEach((childParentSetting) => {
                                    let jmlhJurnal = 0;
                                    let totalQtyHorizontal = 0;
                                    if (childParentSetting.parent_id === childSetting.id) {
                                        console.log(childParentSetting);
                                        childParentSetting.nameCost.forEach((nameCost) => {
                                            if (nameCost.setting_costing_id == childParentSetting.id) {
                                                jmlhJurnal = parseFloat(nameCost.total_cost);
                                            }
                                        });
                                        costOfProduction += parseFloat(jmlhJurnal);
                                        row += `<tr>`;
                                        row += `<td width="10%"></td>
                                        <td width="10%">${childParentSetting.name}</td>
                                        <td width="3%"></td>
                                        <td width="10%">${jmlhJurnal ? formatRupiah(jmlhJurnal) : ""}</td>`;
                                        list_items_title_name_horizontal.forEach((valueHorizontal) => {
                                            let qtyHorizontal = parseFloat(valueHorizontal.qty);
                                            let total_cost = 0;
                                            let total_harga_satuan = 0;
                                            childParentSetting.rawCost.forEach((rawCost) => {
                                                if (rawCost.setting_costing_id == childParentSetting.id && rawCost.production_result_id == valueHorizontal.production_result_id && rawCost.production_result_detail_id == valueHorizontal.production_result_detail_id) {
                                                    total_cost = parseFloat(rawCost.total_cost);
                                                    total_harga_satuan = parseFloat(rawCost.harga_cost);
                                                }
                                            });
                                            // costOfProductionPerSpek += parseFloat(total_cost);
                                            // if (valueHorizontal.type == "JADI") {
                                            row += `<td class="text-center">${formatRupiah(total_cost)}</td>`;
                                            row += `<td class="text-center">${qtyHorizontal.toLocaleString().replaceAll(',', '.')}</td>`;
                                            row += `<td class="text-center">${formatRupiah(total_harga_satuan)}</td>`;
                                            // }
                                        });
                                        row += `</tr>`;
                                    }
                                });
                            }
                        }
                    });
                    if (valueSetting.name == "COST OF PRODUCTION") {
                        row += `<tr>`;
                        row += `<td colspan="2" style="font-weight: bold !important;font-size: 14px !important;">${valueSetting.name}</td>
                        <td></td>
                        <td>${formatRupiah(costOfProduction)}</td>`;
                        list_items_title_name_horizontal.forEach((valueHorizontal) => {
                            // if (valueHorizontal.type == "JADI") {
                            row += `<td class="text-center">${formatRupiah(costOfProductionPerSpek)}</td>`;
                            row += `<td class="text-center"></td>`;
                            row += `<td class="text-center"></td>`;
                            // }
                        });
                        row += `</tr>`;
                    }
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
        // Menggunakan toFixed(2) untuk membulatkan menjadi 2 angka di belakang koma
        var number_string = angka.toFixed(2).replace('.', ',');
        // Memisahkan angka ribuan dengan tanda titik
        var parts = number_string.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        // Menggabungkan kembali angka ribuan dan desimal
        return 'Rp ' + parts.join(",");
    }
</script>
<?= $this->endSection(); ?>
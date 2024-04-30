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

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr style="font-weight: bold !important;font-size: 14px !important;">
                                <th colspan="2" rowspan="2">Keterangan</th>
                                <th rowspan="2"></th>
                                <th rowspan="2">Total</th>
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

<script>
    let list_items_title_name = [];
    let list_items_production_detail = [];

    $(".dateStart").datepicker({
        todayHighlight: true,
        format: "mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        startView: "months",
        minViewMode: 1
    }).change(function(e) {
        getCosting();
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
                console.log(res.data.settingCosting);
                list_items_title_name = [];
                res.data.settingCosting.forEach(function(item) {
                    list_items_title_name.push(item);
                });
                drawTable();
                stopLoading()
            }
        })
    }

    const drawTable = function() {
        $('.body-table').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items_title_name.length === 0) {
            row += `
                <tr>
                    <td colspan="3" class="text-center">Data Tidak Ada</td>
                </tr>
            `;
            $('.tfoot').append(row);
        } else {
            list_items_title_name.forEach((valueSetting) => {
                if (valueSetting.parent_id === null) {
                    row += `
                    <tr>
                        <td colspan="2" style="font-weight: bold !important;font-size: 14px !important;">${valueSetting.name}</td>
                        <td></td>
                        <td></td>
                    </tr>
                `;
                    list_items_title_name.forEach((childSetting) => {
                        if (childSetting.parent_id === valueSetting.id) {
                            row += `
                            <tr>
                                <td width="10%" style="font-size: 13px !important;">${childSetting.name}</td>
                                <td width="10%"></td>
                                <td width="3%"></td>
                                <td></td>
                            </tr>
                        `;
                            list_items_title_name.forEach((childParentSetting) => {
                                if (childParentSetting.parent_id === childSetting.id) {
                                    row += `
                                    <tr>
                                        <td width="10%"></td>
                                        <td width="10%">${childParentSetting.name}</td>
                                        <td width="3%"></td>
                                        <td></td>
                                    </tr>
                                `;
                                }
                            });
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
</script>
<?= $this->endSection(); ?>
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true" width="100%">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Sales Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable2" id="dataTable2" width="100%" border="1" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th style="text-align: center;">Tanggal Keluar</th>
                                <th style="text-align: center;">Tipe Barang (Internal)</th>
                                <th style="text-align: center;">Dokumen Asal</th>
                                <th style="text-align: center;">Kode Barang (Internal)</th>
                                <th style="text-align: center;">Barang - Spesifikasi (Internal)</th>
                                <th style="text-align: center;">Kode Barang (Sales)</th>
                                <th style="text-align: center;">Barang - Spesifikasi (Sales)</th>
                                <th style="text-align: center;">No Stuffing / Pengeluaran Barang</th>
                                <th style="text-align: center;">Departemen / Warehouse Pengeluaran</th>
                                <th style="text-align: center;">Qty Keluar</th>
                                <th style="text-align: center;">Satuan (Sales)</th>
                                <th style="text-align: center;">Satuan (Internal)</th>
                                <th style="text-align: center;">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="body-table">
                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="14" style="text-align: center;">
                                    Tidak Ada Barang
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="section-header">
        <h1>BC 3.0 Outstanding</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" target="_blank" href=" <?= base_url("bea-cukai-bc-30/bc-30-outstanding-export"); ?>">
                <i class="fa-solid fa-print"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url("bea-cukai-bc-30"); ?>">
                Kembali
            </a>
        </div>

    </div>
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th style="text-align:center;">No</th>
                                <th style="text-align:center;">Tipe Sales Order</th>
                                <th style="text-align:center;">No Sales Order</th>
                                <th style="text-align:center;">No Surat Jalan</th>
                                <th style="text-align:center;">No stuffing </th>
                                <th style="text-align:center;">Customer</th>
                                <th style="text-align:center;">Jumlah Barang</th>
                                <th style="text-align:center;">Nilai Barang</th>
                                <th style="text-align:center;">DETAIL BARANG</th>
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
    let sort = "id";
    let sortType = "desc";
    $(document).ready(function() {
        drawTable();
    })

    function drawTable() {
        $.ajax({
            url: '<?= base_url('bea-cukai-bc-30/bc-30-outstanding-all') ?>',
            method: "GET",
            data: {

            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                const table = $('#dataTable');
                table.find('tbody').empty();
                var no = 1
                if (res.length > 0) {
                    $.each(res, function(i, v) {
                        var newRow = $('<tr style="border: none">');
                        newRow.append($('<td style="text-align:center;">').text(no++));
                        newRow.append($('<td style="text-align:center;">').text(v.tipe_sales_order));
                        newRow.append($('<td style="text-align:center;">').text(v.no_sales_order));
                        newRow.append($('<td style="text-align:center;">').text(v.no_surat_jalan));
                        newRow.append($('<td style="text-align:center;">').text(v.no_stuffing));

                        newRow.append($('<td style="text-align:center;">').text(v.customers));
                        newRow.append($('<td style="text-align:center;">').text(v.jumlah_barang));
                        newRow.append($('<td style="text-align:center;">').text(v.total_harga));

                        newRow.append($('<td style="text-align:center;">').html(`
                            <div class="mt-0">
                                <button  data-toggle="tooltip" title="Histori LPB" onclick="displayDetails('${v.id}', '${v.tipe_sales_order}')" class="btn btn-success posting-spp">
                                    <i class="fa-solid fa-box"></i>
                                </button>
                            <div>`));
                        table.find('tbody').append(newRow);
                    });
                } else {
                    var newRow = $('<tr style="border: none">');
                    newRow.append($('<td colspan ="10"  style="text-align:center;">').text("Tidak ada Dokumen Bea Cukai"));
                    table.find('tbody').append(newRow);
                }


            }
        })
    }



    function displayDetails(id, tipe) {
        var salesOrderId = id;
        var tipeSalesOrder = tipe;
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-30/list-barang'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                tipe_sales_order: tipeSalesOrder,
                sales_order_id: salesOrderId
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                drawDetailTable(listData);
            }
        });
        $('#detailModal').modal('show');
    }

    function drawDetailTable(listData) {
        var no = 1;
        const table = $('#dataTable2');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listData.length === 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="14" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            $.each(listData, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            ${no++} 
                        `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.tanggal_keluar));
                newRow.append($('<td style="text-align: center;">').text(v.tipe_barang));
                newRow.append($('<td style="text-align: center;">').text(v.dokumen_asal));
                newRow.append($('<td style="text-align: center;">').text(v.kode_barang_internal));
                newRow.append($('<td style="text-align: center;">').text(v.nama_barang_internal));
                newRow.append($('<td style="text-align: center;">').text(v.kode_barang_sales));
                newRow.append($('<td style="text-align: center;">').text(v.nama_barang_sales));
                newRow.append($('<td style="text-align: center;">').text(v.no_stuffing));
                newRow.append($('<td style="text-align: center;">').text(v.divisi + ' / ' + v.warehouse_name));
                newRow.append($('<td style="text-align: center;">').text(v.qty_keluar));
                newRow.append($('<td style="text-align: center;">').text(v.kode_satuan_sales));
                newRow.append($('<td style="text-align: center;">').text(v.kode_satuan_internal));
                newRow.append($('<td style="text-align: center;">').text(v.harga));
                table.find('tbody').append(newRow);
            });
        }
    }



    function formatRupiah(angka) {
        if (angka === null) {
            angka = 0;
        }
        angka = angka.toString();
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return ribuanFormatted + ',' + desimal;
    }

    function convertRupiahToNumber(rupiah) {
        if (rupiah == "") {
            return 0;
        } else {
            var withoutDot = rupiah.replace(/\./g, '');
            var numberWithDot = withoutDot.replace(',', '.');
            return parseFloat(numberWithDot);
        }
    }
</script>


<?= $this->endSection(); ?>
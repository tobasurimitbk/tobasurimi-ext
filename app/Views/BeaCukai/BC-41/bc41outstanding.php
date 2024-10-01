<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true" width="100%">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Sales Order Lain</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable2" width="100%" border="1" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;" colspan="8">Detail Barang yang Keluar</th>
                                <th style="text-align: center;" colspan="1">Data Harga</th>
                            </tr>
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th style="text-align: center;">Asal Barang</th>
                                <th style="text-align: center;">No Dokumen</th>
                                <th style="text-align: center;">Supplier</th>
                                <th style="text-align: center;">Dokumen Asal</th>
                                <th style="text-align: center;">Tgl Penerimaan</th>
                                <th style="text-align: center;">Barang - Spesifikasi</th>
                                <th style="text-align: center;">Qty Keluar</th>
                                <th style="text-align: center;">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody class="body-table">
                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="9" style="text-align: center;">
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
        <h1>BC 4.1 Outstanding</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" target="_blank" href=" <?= base_url("bea-cukai-bc-41/bc-41-outstanding-export"); ?>">
                <i class="fa-solid fa-print"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url(" bea-cukai-bc-41"); ?>">
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
                                <th style="text-align:center;">Asal Pengeluaran</th>
                                <th style="text-align:center;">Nama Customer / Supplier</th>
                                <th style="text-align:center;">No Sales Order</th>
                                <th style="text-align:center;">Department / Warehouse Pengeluaran</th>
                                <th style="text-align:center;">Tanggal</th>
                                <th style="text-align:center;">Jumlah Barang</th>
                                <th style="text-align:center;">Nilai Barang</th>
                                <th style="text-align:center;">Detail Barang</th>
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
            url: '<?= base_url('bea-cukai-bc-41/bc-41-outstanding-all') ?>',
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
                        newRow.append($('<td style="text-align:center;">').text(v.asal_pengeluaran));
                        newRow.append($('<td style="text-align:center;">').text(v.customer));
                        newRow.append($('<td style="text-align:center;">').text(v.no_sales_order));
                        newRow.append($('<td style="text-align:center;">').text(v.divisi + " / " + v.warehouse_name));
                        newRow.append($('<td style="text-align:center;">').text(v.tanggal));
                        newRow.append($('<td style="text-align:center;">').text(v.jumlah_barang));
                        newRow.append($('<td style="text-align:center;">').text(v.harga_barang));
                        newRow.append($('<td style="text-align:center;">').html(`
                            <div class="mt-0">
                                <button  data-toggle="tooltip" title="Detail" onclick="displayDetails('${v.id}','${v.asal_pengeluaran}')" class="btn btn-success posting-spp">
                                    <i class="fa-solid fa-box"></i>
                                </button>
                            <div>`));
                        table.find('tbody').append(newRow);
                    });
                } else {
                    var newRow = $('<tr style="border: none">');
                    newRow.append($('<td colspan ="11"  style="text-align:center;">').text("Tidak ada Dokumen Bea Cukai"));
                    table.find('tbody').append(newRow);
                }


            }
        })
    }

    function displayDetails(reference_id, asal_pengeluaran) {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-41/list-reference-detail'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                reference_id: reference_id,
                asal_pengeluaran: asal_pengeluaran,
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                // DRAWTABLE
                drawTableDetails(listData);
            }
        });
        $('#detailModal').modal('show');

    }

    function drawTableDetails(listData) {
        var no = 1;
        const table = $('#dataTable2');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listData.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="9" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var totalHarga = 0;
            $.each(listData, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                   ${no++} 
                `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.sumber));
                newRow.append($('<td style="text-align: center;">').text(v.stock_dokumen));
                newRow.append($('<td style="text-align: center;">').text(v.supplier_name));
                newRow.append($('<td style="text-align: center;">').text(v.bc_type + '/' + v.no_aju));
                newRow.append($('<td style="text-align: center;">').text(v.stock_date));
                newRow.append($('<td style="text-align: center;">').text(v.barang));
                newRow.append($('<td style="text-align: center;">').text(v.qty_konversi + " " + v.satuan));
                newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.total_harga)));
                table.find('tbody').append(newRow);

                totalHarga = totalHarga + parseFloat(v.total_harga);
            });
            // GRAND TOTAL
            var newRow = $('<tr style="color:whitesmoke; background-color:#f2c996">');
            newRow.append($('<td style="text-align: right;" colspan="8">').html("<b>GRAND TOTAL</b>"));
            newRow.append($('<td style="text-align: center;">').text(formatRupiah(totalHarga)));
            table.find('tbody').append(newRow);
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
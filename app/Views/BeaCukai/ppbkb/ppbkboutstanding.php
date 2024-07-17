<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true" width="100%">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Mutasi</h5>
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
                                <th style="text-align: center;">Tipe Barang</th>
                                <th style="text-align: center;">Kode Barang</th>
                                <th style="text-align: center;">Kode HS</th>
                                <th style="text-align: center;">Barang</th>
                                <th style="text-align: center;">Qty Mutasi</th>
                                <th style="text-align: center;">Satuan</th>
                                <th style="text-align: center;">Dokumen Pemasukan</th>
                                <th style="text-align: center;">No Aju</th>
                                <th style="text-align: center;">Tanggal Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="body-table">
                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="11" style="text-align: center;">
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
        <h1>PPBKB Outstanding</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" target="_blank" href=" <?= base_url("bea-cukai-ppbkb/ppbkb-outstanding-export"); ?>">
                <i class="fa-solid fa-print"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url("bea-cukai-ppbkb"); ?>">
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
                                <th style="text-align:center;">No Mutasi</th>
                                <th style="text-align:center;">Divisi / Warehouse Asal</th>
                                <th style="text-align:center;">Divisi / Warehouse Tujuan</th>
                                <th style="text-align:center;">Tanggal</th>
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
            url: '<?= base_url('bea-cukai-ppbkb/ppbkb-outstanding-all') ?>',
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
                        newRow.append($('<td style="text-align:center;">').text(v.no_mutasi));
                        newRow.append($('<td style="text-align:center;">').text(v.divisi_awal + " / " + v.warehouse_awal));
                        newRow.append($('<td style="text-align:center;">').text(v.divisi_tujuan + " / " + v.warehouse_tujuan));
                        newRow.append($('<td style="text-align:center;">').text(v.tanggal));
                        newRow.append($('<td style="text-align:center;">').text(v.jumlah_barang));
                        newRow.append($('<td style="text-align:center;">').text(v.total_harga));

                        newRow.append($('<td style="text-align:center;">').html(`
                            <div class="mt-0">
                                <button  data-toggle="tooltip" title="Detail" onclick="displayDetails('${v.id}')" class="btn btn-success posting-spp">
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



    function displayDetails(id) {
        var mutasiID = id;

        $.ajax({
            url: `<?= base_url('bea-cukai-ppbkb/list-barang-mutasi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                mutasi_id: mutasiID
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
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

        if (listData.length === 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="11" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            $.each(listData, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            ${no++} 
                        `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
                newRow.append($('<td style="text-align: center;">').text(v.kode_barang));
                newRow.append($('<td style="text-align: center;">').text(v.hs_code == null ? "-" : v.hs_code));
                newRow.append($('<td style="text-align: center;">').text(v.barang));
                newRow.append($('<td style="text-align: center;">').text(v.qty));
                newRow.append($('<td style="text-align: center;">').text(v.satuan));
                newRow.append($('<td style="text-align: center;">').text(v.bc_type));
                newRow.append($('<td style="text-align: center;">').text(v.no_aju));
                newRow.append($('<td style="text-align: center;">').text(v.stock_date));

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
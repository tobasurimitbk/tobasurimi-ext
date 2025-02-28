<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>BC 2.3 Outstanding</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" target="_blank" href=" <?= base_url("bea-cukai-bc-23/bc-23-outstanding-export"); ?>">
                <i class="fa-solid fa-print"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url(" bea-cukai-bc-23"); ?>">
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
                                <th style="text-align:center;">Supplier</th>
                                <th style="text-align:center;">Tipe PO</th>
                                <th style="text-align:center;">Tgl PO</th>
                                <th style="text-align:center;">Tgl LPB </th>
                                <th style="text-align:center;">No LPB</th>
                                <th style="text-align:center;">No PO</th>
                                <th style="text-align:center;">Kode</th>
                                <th style="text-align:center;">Barang</th>
                                <th style="text-align:center;">Qty PO</th>
                                <th style="text-align:center;">Qty Diterima</th>
                                <th style="text-align:center;">Harga</th>
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
            url: '<?= base_url('bea-cukai-bc-23/bc-23-outstanding-all') ?>',
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
                        newRow.append($('<td style="text-align:center;">').text(v.supplier));
                        newRow.append($('<td style="text-align:center;">').text(v.status_penerimaan + " " + v.tipe_bahan));
                        newRow.append($('<td style="text-align:center;">').text(v.po_date));
                        newRow.append($('<td style="text-align:center;">').text(v.lpb_date));
                        newRow.append($('<td style="text-align:center;">').text(v.no_penerimaan_barang));
                        newRow.append($('<td style="text-align:center;">').text(v.po_no));
                        newRow.append($('<td style="text-align:center;">').text(v.kode_barang));
                        newRow.append($('<td style="text-align:center;">').text(v.barang));
                        newRow.append($('<td style="text-align:center;">').text(v.qty_po));
                        newRow.append($('<td style="text-align:center;">').text(v.qty_lpb));
                        newRow.append($('<td style="text-align:center;">').text(v.sub_total));
                        table.find('tbody').append(newRow);
                    });
                } else {
                    var newRow = $('<tr style="border: none">');
                    newRow.append($('<td colspan ="12"  style="text-align:center;">').text("Tidak ada Dokumen Bea Cukai"));
                    table.find('tbody').append(newRow);
                }


            }
        })
    }
</script>


<?= $this->endSection(); ?>
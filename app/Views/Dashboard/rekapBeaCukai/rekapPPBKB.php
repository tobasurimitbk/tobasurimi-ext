<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Rekap Pembuatan Dokumen PPBKB</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="" onclick="exportSheet(event)">
                <i class="fa-solid fa-print"></i> Export
            </a>


            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url("dashboard"); ?>">
                Kembali
            </a>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-sm-2 mb-3">
                    <div class="input-group input-group-password align-items-center">
                        <input autocomplete="one-time-code" class="form-control input-picker date " id="date" name="date" placeholder="Pilih Bulan">
                        <div class="input-group-prepend group-prepend-password align-items-center" style="display: flex; justify-content: center; align-items: center;">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -50px; border: 0px; font-size: 1em;" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th style="text-align:center;">No</th>
                                <th style="text-align:center;">No Mutasi</th>
                                <th style="text-align:center;">No Dokumen</th>
                                <th style="text-align:center;">No Aju</th>
                                <th style="text-align:center;">No Daftar</th>
                                <th style="text-align:center;">Tgl Dokumen</th>
                                <th style="text-align:center;">Asal Divisi/Warehouse</th>
                                <th style="text-align:center;">Tujuan Divisi/Warehouse</th>
                                <th style="text-align:center;">Jumlah Barang </th>
                                <th style="text-align:center;">Total Barang </th>

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
        var currentDate = new Date();
        var formattedDate = (currentDate.getMonth() + 1).toString().padStart(2, '0') + '/' + currentDate.getFullYear();
        $("#date").val(formattedDate);
        drawTable();
    })

    $("#date").datepicker({
        todayHighlight: true,
        format: "mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        minViewMode: "months"
    });

    $("#date").change(function() {
        drawTable();
    })

    function drawTable() {
        $.ajax({
            url: '<?= base_url('/dashboard/list-dokumen-ppbkb/all') ?>',
            method: "GET",
            data: {
                date: $('#date').val()
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
                        newRow.append($('<td  style="text-align:center;">').text(no++));
                        newRow.append($('<td style="text-align:center;">').text(v.no_mutasi));
                        newRow.append($('<td style="text-align:center;">').text(v.no_stock_dokumen));
                        newRow.append($('<td style="text-align:center;">').text(v.no_aju));
                        newRow.append($('<td style="text-align:center;">').text(v.no_dokumen));
                        newRow.append($('<td style="text-align:center;">').text(v.tgl_dokumen));
                        newRow.append($('<td style="text-align:center;">').text(v.asal));
                        newRow.append($('<td style="text-align:center;">').text(v.tujuan));
                        newRow.append($('<td style="text-align:center;">').text(v.jumlah_barang + " Barang"));
                        newRow.append($('<td style="text-align:center;">').text(v.total_barang));

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

    function exportSheet(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('/dashboard/list-dokumen-ppbkb/exportsheet') ?>',
            method: "GET",
            data: {
                date: $('#date').val()
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response) {
                var date = $('#date').val();
                const blob = new Blob([response], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'Rekap_PPBKB_' + date + '.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    }
</script>


<?= $this->endSection(); ?>
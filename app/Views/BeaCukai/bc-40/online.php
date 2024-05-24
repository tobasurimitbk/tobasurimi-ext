<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Dokumen Online BC 4.0</h1>
        <a href="<?= base_url('bea-cukai-bc-40') ?>" type="button" class="btn btn-show-form btn-add float-right">
            <i class="fas fa-building mr-2"></i> Data Internal
        </a>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px">No</th>
                                <th style="text-align: center;">Nomor Aju</th>
                                <th style="text-align: center;">Kode Respon</th>
                                <th style="text-align: center;">Nomor Daftar</th>
                                <th style="text-align: center;">Tanggal Daftar</th>
                                <th style="text-align: center;">Nomor Respon</th>
                                <th style="text-align: center;">Tanggal Respon</th>
                                <th style="text-align: center;">Waktu Respon</th>
                                <th style="text-align: center;">Keterangan</th>
                                <th style="text-align: center;">PDF</th>
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
<div class="modal fade" id="prevModals" tabindex="-1" aria-labelledby="prevModals" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prevModals">Preview Response</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div id="mypdfs"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
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
                emptyTable: "Tidak Ada Data Online",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        getList();

        function getList() {
            $.ajax({
                url: `<?= base_url('bea-cukai-bc-40/all-online'); ?>`,
                method: "GET",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(res) {
                    // DRAW LIST PO TABLE
                    if (res.status == false) {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                    } else {
                        if (res.data.status == "Failed") {
                            Swal.fire({
                                icon: 'error',
                                title: res.data.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            });
                        } else {
                            if ($.fn.DataTable.isDataTable('#dataTable')) {
                                $('#dataTable').DataTable().clear().draw();
                                dataTable.destroy();
                            }

                            const table = $('#dataTable');
                            table.find('tbody').empty();

                            var no = 1;
                            $.each(res.data.dataRespon, function(i, v) {
                                var newRow = $('<tr>');
                                newRow.append($('<td style="text-align:center;">').text(no++));
                                newRow.append($('<td style="text-align:center;">').text(v.nomorAju));
                                newRow.append($('<td style="text-align:center;">').text(v.kodeRespon));
                                newRow.append($('<td style="text-align:center;">').text(v.nomorDaftar));
                                newRow.append($('<td style="text-align:center;">').text(v.tanggalDaftar));
                                newRow.append($('<td style="text-align:center;">').text(v.nomorRespon));
                                newRow.append($('<td style="text-align:center;">').text(v.tanggalRespon));
                                newRow.append($('<td style="text-align:center;">').text(v.waktuRespon));
                                newRow.append($('<td style="text-align:center;">').text(v.keterangan));
                                if (v.pdf != null) {
                                    newRow.append($('<td style="text-align:center;">').html(
                                        `
                                            <button class="btn btn-warning btn-print" onclick="pdf('v.pdf')" style="box-shadow: none !important;">
                                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                            </button>
                                        `
                                    ));
                                } else {
                                    newRow.append($('<td style="text-align:center;">').text(
                                        '-'
                                    ));
                                }

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
                    }

                }
            });
        }

        function pdf(url) {
            e.preventDefault();
            var file = $(this).data('file');
            PDFObject.embed("<?= base_url($baseUrl . '/') ?>" + url, "#mypdfs", {
                height: "700px"
            });
            $('#prevModals').modal('show');
        }
    })
</script>

<?= $this->endSection(); ?>
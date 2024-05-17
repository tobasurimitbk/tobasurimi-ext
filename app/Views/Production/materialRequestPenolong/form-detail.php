<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Request Info</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("material-request"); ?>">
                Batal
            </a>
            <?= csrf_field() ?>
            <?php
            if (!($dataMaterialRequestswithwo[0]->is_posted)) {
            ?>
                <button type="button" class="btn btn-success" onclick="posting('<?= encrypt($ids) ?>', 1)">
                    Posting
                </button>
            <?php
            }
            ?>
            <button class="btn btn-show-form btn-save float-right btn-submit-form">
                Print
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">PT. TOBA SURIMI INDONESIA, Tbk</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <h6>Kode Produksi : <?= !empty($dataMaterialRequestswithwo) ? $dataMaterialRequestswithwo[0]->wo_no : ""; ?></h6>
                <span><?= !empty($dataMaterialRequestswithwo) ? $dataMaterialRequestswithwo[0]->nama_barang . " : " . $dataMaterialRequestswithwo[0]->standart_production : ""; ?></span>
                <span>Tanggal Produksi : <?= !empty($dataMaterialRequestswithwo) ? date('d/m/Y', strtotime($dataMaterialRequestswithwo[0]->production_date)) : ""; ?></span>
                <?php
                if (!empty($dataMaterialRequestswithwo)) {
                    if ($dataMaterialRequestswithwo[0]->is_posted && $dataMaterialRequestswithwo[0]->is_approve == "1") {
                ?>
                        <span>Status : Approved</span>
                    <?php
                    } else if ($dataMaterialRequestswithwo[0]->is_posted && !($dataMaterialRequestswithwo[0]->is_approve)) {
                    ?>
                        <span>Status : Posted (Waiting to Approve)</span>
                    <?php
                    } else if (!($dataMaterialRequestswithwo[0]->is_posted) && !($dataMaterialRequestswithwo[0]->is_approve)) {
                    ?>
                        <span>Status : Waiting to Posted</span>
                    <?php
                    } else if ($dataMaterialRequestswithwo[0]->is_posted && $dataMaterialRequestswithwo[0]->is_approve == "2") {
                    ?>
                        <span>Status : Rejected</span>
                <?php
                    }
                }
                ?>
                <?php
                if (!empty($dataMaterialRequestswithwo)) {
                    if ($dataMaterialRequestswithwo[0]->note_approve) {
                ?>
                        <span>Keterangan : <?= $dataMaterialRequestswithwo[0]->note_approve ?></span>
                    <?php
                    } else {
                    ?>
                        <span>Keterangan : - </span>
                <?php
                    }
                }
                ?>
            </div>
            <!-- <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">Bill of Material</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTableBarang" id="dataTableBarang" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Consumption</th>
                                <th>Scrap</th>
                                <th>Spesifikasi</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div> -->
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">Material Request</label>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTableMaterialRequest" id="dataTableMaterialRequest" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>No. Request</th>
                                <th>Tgl. Request</th>
                                <th>Dept.</th>
                                <th>Warehouse</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let sortDataBarang = "createdAt";
    let sortTypeDataBarang = "DESC";
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    $(document).ready(function() {
        const dataTableMaterialRequest = $('.dataTableMaterialRequest').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            serverSide: true,
            ordering: true,
            order: [
                [4, 'desc']
            ],
            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            ajax: {
                url: "<?= base_url("material-request/data-detail-material"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.id = "<?= encrypt($ids) ?>";
                    data.search = "";

                    data.sort = sortDataBarang;
                    data.sortType = sortTypeDataBarang;
                },
                beforeSend: function() {
                    $.LoadingOverlay("show", {
                        image: "",
                        fontawesomeColor: "#222FCC",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
                complete: function() {
                    $.LoadingOverlay("hide", {
                        image: "",
                        fontawesomeColor: "#222FCC",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                    data: "no",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "req_no",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "tgl_req",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "nama_divisi",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "nama_warehouse",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "nama_barang",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "satuan",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
                {
                    data: "total",
                    className: "text-center",
                    searchable: false,
                    sortable: false
                },
            ],
            "drawCallback": function(settings) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            },
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
    });

    const posting = function(id, status_posting) {
        console.log(id);
        Swal.fire({
            icon: 'question',
            title: status_posting == "1" ? "Yakin Akan Diposting ?" : "Yakin Akan di Unposting ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url("material-request/update-status"); ?>",
                    data: {
                        id: id,
                        status_posting: status_posting
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        // Menghindari XSS dengan menghindari menyisipkan variabel PHP langsung ke dalam string JavaScript
                        const baseUrl = '<?= base_url("material-request/details"); ?>';

                        // Mengambil token CSRF dari respons dan memperbarui nilainya pada input CSRF
                        csrf.val(response.token);

                        // Menampilkan pesan berdasarkan respons dari server
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then(() => {
                                // Mengarahkan ke URL yang diperoleh dari PHP
                                location.replace(`${baseUrl}/${response.id}`);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            });
                        }
                    },
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Disimpan, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>
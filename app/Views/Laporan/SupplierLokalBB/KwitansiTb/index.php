<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Kwitansi Bulanan PO Bahan Baku</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-page-list-attendance">
                <div class="col-6 mb-2">
                    <form id="search_form" action="<?= base_url('kwitansi-tb?year=' . $year . '&month=' . $month) ?>" name="search_form" class="kt-form kt-form--fit kt-margin-b-20">
                        <select required name="month" id="month">
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                $temp = (strlen($i) == 1) ? ("0" . $i) : $i;
                                $checked = ($month == $temp) ? "selected" : "";
                            ?>
                                <option value="<?php echo $temp; ?>" <?php echo $checked; ?>><?php echo $temp; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <select required name="year" id="year">
                            <?php
                            for ($i = date("Y") - 2; $i <= date("Y") + 2; $i++) {
                                $checked = ($year == $i) ? "selected" : "";
                            ?>
                                <option value="<?php echo $i; ?>" <?php echo $checked; ?>><?php echo $i; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-brand--icon" id="filterYearMonth">
                            <span>
                                <i class="la la-print"></i>
                                <span>Cari</span>
                            </span>
                        </button>

                    </form>
                </div>
                <div class="col-6 mb-2">
                    <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <?= csrf_field() ?>

            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10; text-align:center">No</th>
                                <th style="text-align: center;" class="sort">Supplier</th>
                                <th style="text-align: center;" class="sort">Total</th>
                                <th style="text-align: center;" class="sort">No Kwitansi</th>
                                <th style="text-align: center;">Tanggal</th>
                                <th style="text-align: center; width: 10; ">Print</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">
                            <?php $no = 1; ?>
                            <?php foreach ($data as $d) : ?>
                                <tr style="text-align: center;">
                                    <td><?= $no++; ?></td>
                                    <td><?= $d['supplier'] ?></td>
                                    <td><?= "Rp " . number_format($d['total'], 2, ',', '.') ?></td>
                                    <td><?= $d['noKwitansi'] ?></td>
                                    <td style="width: 150px;">
                                        <div class="form-floating">
                                            <input data-id="<?= $d['id'] ?>" autocomplete="one-time-code" value="<?= date('d/m/Y', strtotime($d['tanggal'])) ?>" name="tanggal" type="text" required class="form-control target input-picker tanggal">
                                            <label>Tanggal Kwitansi</label>
                                        </div>
                                    </td>
                                    <td style="width: 100px;">
                                        <div class="mt-0">
                                            <button data-id="<?= $d['id'] ?>" data-no_kwitansi="<?= str_replace('/', '-', $d['noKwitansi']); ?>" class="btn btn-warning btn-print" style="box-shadow: none !important;">
                                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "name";
    let sortType = "asc";
    var row = 0;

    $('.dataTable').DataTable({
        ordering: false,
        //responsive: true,
        display: 'stripe',
        searching: true,
        lengthChange: false,
        pageLength: 25,
        columnDefs: [{
            defaultContent: '-',
            targets: '_all'
        }],
        "initComplete": function(settings, json) {
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        language: {
            emptyTable: "Tidak ada pembelian pada supplier "
        }
    })

    $(".tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('.btn-print').click(function() {
        var id = $(this).data('id');
        var yearMonth = "<?= $year . '-' . $month ?>";
        var tanggal = $('.tanggal[data-id="' + id + '"]').val();
        var noKwitansi = $(this).data('no_kwitansi');

        var parts = tanggal.split('/');
        var newDateFormat = parts[2] + '-' + parts[1] + '-' + parts[0];

        window.open("<?= base_url('laporan-supplier-lokal-bb/kwitansi-tb/print/') ?>" + id + '/' + yearMonth + '/' + newDateFormat + '/' + noKwitansi, "_blank");
    });
</script>

<?= $this->endSection(); ?>
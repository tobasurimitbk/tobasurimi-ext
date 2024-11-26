<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Buku Besar</h1>
        <?php if (can('Laporan', 'Accounting', 'p')) : ?>
            <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item" onclick="printPDF('<?= base_url("laporan-accounting/bukubesar/printPDF"); ?>')">PDF</button></li>
                <!-- <li><button class="dropdown-item" onclick="printExcel('<?= base_url("laporan-accounting/bukubesar/printExcel"); ?>')">Excel</button></li> -->
            </ul>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= base_url('/laporan-accounting/bukubesar') ?>" class="create-form form-add-spp" role="form">
                <?= csrf_field(); ?>
                <div class="row justify-content-start">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <div class="form-floating" style="height: 50px;">
                                <input placeholder="" value="<?= isset($_POST['dateStart']) ? ($_POST['dateStart']) : date('d/m/y') ?>" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                                <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button disabled class="btn btn-secondary" type="button">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <div class="form-floating" style="height: 50px;">
                                <input value="<?= isset($_POST['dateEnd']) ? $_POST['dateEnd']  : '' ?>" placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
                                <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button disabled class="btn btn-secondary" type="button">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" name="divisi_id" id="divisi_id">
                                <option value="">SEMUA</option>
                                <?php foreach ($divisi ?? [] as $d) : ?>
                                    <option <?= isset($_POST['divisi_id']) ? ($d['id'] == $_POST['divisi_id'] ? 'selected' : '') : '' ?> value="<?= $d['id']; ?>"><?= $d['divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select jenis_account" name="jenis_account" id="jenis_account" onchange="dropdownAccount()">
                                <option
                                    value="sub_account"
                                    <?= isset($_POST['jenis_account']) && $_POST['jenis_account'] === 'sub_account' ? 'selected' : '' ?>>
                                    SUB AKUN
                                </option>
                                <option <?= isset($_POST['jenis_account']) && $_POST['jenis_account'] === 'header_account' ? 'selected' : '' ?> value="header_account">HEADER AKUN</option>
                            </select>
                            <label for="floatingInput">Pilih Jenis Akun</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating" style="height: 50px;">
                            <select class="form-select account_id" multiple name="account_id[]" id="account_id" onchange="changeAccount()">
                                <?php foreach ($account as $a) : ?>
                                    <option
                                        value="<?= $a['id']; ?>"
                                        <?= isset($_POST['account_id']) && in_array($a['id'], $_POST['account_id']) ? 'selected' : ''; ?>>
                                        <?= $a['number'] . " " . $a['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="account_id">Pilih Akun (COA)</label>
                        </div>


                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select range_account_start_id" name="range_account_start_id" id="range_account_start_id" onchange="changeRangeAccount()">
                                <option value="">Pilih Range Awal Akun</option>
                                <?php foreach ($account as $a) : ?>
                                    <option value="<?= $a['id']; ?>" <?= isset($_POST['range_account_start_id']) && $_POST['range_account_start_id'] == $a['id'] ? 'selected' : ''; ?>>
                                        <?= $a['number'] . " " . $a['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="range_account_start_id">Range Awal Akun</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select range_account_finish_id" name="range_account_finish_id" id="range_account_finish_id" onchange="changeRangeAccount()">
                                    <option value="">Pilih Range Akhir Akun</option>
                                    <?php foreach ($account as $a) : ?>
                                        <option value="<?= $a['id']; ?>" <?= isset($_POST['range_account_finish_id']) && $_POST['range_account_finish_id'] == $a['id'] ? 'selected' : ''; ?>>
                                            <?= $a['number'] . " " . $a['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="range_account_finish_id">Range Akhir Akun</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button class="btn btn-secondary" name="cariTanggal" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
            <?php if (count($jurnalUmum) > 0): ?>
                <div class="row">
                    <?php foreach ($jurnalUmum as $j): ?>
                        <div class="alert alert-secondary alert-dismissible fade show mt-3 text-black" role="alert">
                            <b><?= $j['number'] ?> - <?= $j['name'] ?></b>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="myTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis Transaksi</th>
                                        <th>No Transaksi</th>
                                        <th>Desc</th>
                                        <th>Currency</th>
                                        <th>Exchange Rate</th>
                                        <th>Debit</th>
                                        <th>Kredit</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td colspan="8">Saldo Awal : </td>
                                        <td><?= toRupiah($j['saldo_lama']) ?></td>
                                    </tr>
                                    <?php
                                    $sisaSaldo = $j['saldo_lama'];
                                    $totalKredit = 0;
                                    $totalDebit = 0;

                                    ?>
                                    <?php foreach ($j['result'] as $r): ?>
                                        <?php
                                        $sisaSaldo += ($r['debit'] * $r['kurs']) - ($r['kredit'] * $r['kurs']);
                                        $totalDebit += $r['debit'] * $r['kurs'];
                                        $totalKredit += $r['kredit'] * $r['kurs'];
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($r['tanggal_jurnal'])) ?></td>
                                            <td><?= $r['jenis_transaksi'] ?></td>
                                            <td><?= $r['no_transaksi'] ?></td>
                                            <td><?= $r['keterangan'] ?></td>
                                            <td><?= toRupiah(abs($r['debit'] - $r['kredit'])) . " " . "<b>" . $r['valas'] . "</b>" ?></td>
                                            <td><?= $r['kurs'] == "1" ? "" : toRupiah($r['kurs']) ?></td>
                                            <td><?= toRupiah($r['debit'] * $r['kurs']) ?></td>
                                            <td><?= toRupiah($r['kredit'] * $r['kurs']) ?></td>
                                            <td><?= toRupiah($sisaSaldo) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center;font-weight:bold;">
                                            <b>Sub Total</b>
                                        </td>
                                        <td>
                                            <b>
                                                <?= toRupiah($totalDebit) ?>
                                            </b>
                                        </td>
                                        <td>
                                            <b>
                                                <?= toRupiah($totalKredit) ?>
                                            </b>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" style="text-align: center;">
                                            <b>Total</b>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <b>
                                                <?= toRupiah($sisaSaldo) ?>
                                            </b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                        </div>
                </div>

            <?php else: ?>

                <div class="alert alert-secondary alert-dismissible fade show mt-3 text-black" role="alert">
                    Silahkan Pilih Akun yang Akan Dieksekusi

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Mendapatkan tanggal saat ini
        var currentDate = new Date();
        $(".dateStart").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true,
            // Atur nilai awal menjadi tanggal 1 di bulan berjalan
            defaultViewDate: {
                year: currentDate.getFullYear(),
                month: currentDate.getMonth(),
                day: 1
            }
        });

        $(".dateEnd").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        //CSS SELECT2 FLOATING LABEL
        $('.account_id').select2({
            placeholder: "Pilih Account COA",
            theme: "bootstrap-5",
            allowClear: false
        });
        $('.divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.jenis_account').select2({
            placeholder: "Pilih Jenis Account",
            theme: "bootstrap-5",
            allowClear: false
        });
        $('.range_account_start_id').select2({
            placeholder: "Pilih Range Awal Akun",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.range_account_finish_id').select2({
            placeholder: "Pilih Range Akhir Akun",
            theme: "bootstrap-5",
            allowClear: true
        });

        $(' .account_id, .divisi_id,.jenis_account,.range_account_start_id,.range_account_finish_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(' .account_id, .divisi_id,.jenis_account,.range_account_start_id,.range_account_finish_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px').css('z-index', '1');

        $(' .account_id, .divisi_id,.jenis_account,.range_account_start_id,.range_account_finish_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');


    });

    function printPDFn(url) {
        const formData = $('.create-form').serializeArray();

        const $form = $('<form>', {
            action: url,
            method: 'POST',
            target: '_blank',
        });

        $.each(formData, function(index, field) {
            $('<input>')
                .attr({
                    type: 'hidden',
                    name: field.name,
                    value: field.value,
                })
                .appendTo($form);
        });

        $form.appendTo('body').submit().remove();
    };

    function changeAccount() {
        $('.range_account_start_id option:selected').val(null);
        $('.range_account_finish_id option:selected').val(null);
    }

    function changeRangeAccount() {
        $('.account_id').val(null);
    }

    // Dropdown Account
    function dropdownAccount() {
        $.ajax({
            url: `<?= base_url('laporan-accounting/bukubesar/dropdown-account'); ?>`,
            method: "GET",
            data: {
                jenis_account: $(".jenis_account option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                // account_id
                $(".account_id").empty()
                $(".account_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".account_id").append(`<option value="${item.id}">${item.number} - ${item.name}</option>`)
                })
                $(".account_id").val(null).change();
                // range_account_start_id
                $(".range_account_start_id").empty()
                $(".range_account_start_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".range_account_start_id").append(`<option value="${item.id}">${item.number} - ${item.name}</option>`)
                })
                $(".range_account_start_id").val(null).change();
                // range_account_finish_id
                $(".range_account_finish_id").empty()
                $(".range_account_finish_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".range_account_finish_id").append(`<option value="${item.id}">${item.number} - ${item.name}</option>`)
                })
                $(".range_account_finish_id").val(null).change();
            }
        });
    }
</script>


<?= $this->endSection(); ?>
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
                <li>
                    <a class="dropdown-item" href="<?= base_url("laporan-accounting/bukubesar/printPDF") . '?' . http_build_query($_GET) ?>" target="_blank">
                        PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url("laporan-accounting/bukubesar/printExcel") . '?' . http_build_query($_GET) ?>" target="_blank">
                        Excel
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="get" action="<?= base_url('/laporan-accounting/bukubesar') ?>" class="create-form form-add-spp" role="form">
                <div class="row justify-content-start">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <div class="form-floating" style="height: 50px;">
                                <input
                                    placeholder=""
                                    value="<?= isset($_GET['dateStart']) && !empty($_GET['dateStart'])
                                                ? $_GET['dateStart']
                                                : date('01/m/Y') ?>"
                                    class="form-control dateStart"
                                    id="dateStart"
                                    name="dateStart"
                                    aria-label="Floating label select example" />
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
                                <input value="<?= isset($_GET['dateEnd']) ? $_GET['dateEnd']  : '' ?>" placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
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
                                    <option <?= isset($_GET['divisi_id']) ? ($d['id'] == $_GET['divisi_id'] ? 'selected' : '') : '' ?> value="<?= $d['id']; ?>"><?= $d['divisi'] ?></option>
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
                                    <?= isset($_GET['jenis_account']) && $_GET['jenis_account'] === 'sub_account' ? 'selected' : '' ?>>
                                    SUB AKUN
                                </option>
                                <option <?= isset($_GET['jenis_account']) && $_GET['jenis_account'] === 'header_account' ? 'selected' : '' ?> value="header_account">HEADER AKUN</option>
                            </select>
                            <label for="floatingInput">Pilih Jenis Akun</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating" style="height: 50px;">
                            <select class="form-select account-select" name="account_id[]" id="account_id" multiple onchange="updateSelectedAccounts()">
                                <!-- Opsi akan di-load secara dinamis -->
                            </select>
                            <label for="floatingInput">Pilih Akun (COA)</label>
                        </div>
                        <div id="selectedAccounts" class="mt-2 d-flex flex-wrap gap-2"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select supplier_id" name="supplier_id" id="supplier_id">
                                <option value="">SEMUA</option>
                                <?php foreach ($supplier ?? [] as $s) : ?>
                                    <option <?= isset($_GET['supplier_id']) ? ($s['id'] == $_GET['supplier_id'] ? 'selected' : '') : '' ?> value="<?= $s['id']; ?>"><?= $s['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-50 bg-secondary" style="margin-bottom: 25px;height: 50px" type="submit" name="cariTanggal">
                            <i class="fas fa-search me-2"></i>
                        </button>
                    </div>


                </div>
            </form>
            <?php if (count($jurnalUmum) > 0): ?>
                <div class="row">
                    <?php foreach ($jurnalUmum as $j): ?>
                        <?php if (!empty($j['result'])): ?>  <!-- ✅ cek dulu result -->
                            <div class="alert alert-secondary alert-dismissible fade show mt-3 text-black" role="alert">
                                <b><?= $j['number'] ?> - <?= $j['name'] ?></b>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" id="myTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Company</th>
                                            <th>Jenis Transaksi</th>
                                            <th>Supplier</th>
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
                                            <td colspan="10">Saldo Awal :</td>
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
                                                <td><?= $r['company'] ?></td>
                                                <td><?= $r['jenis_transaksi'] ?></td>
                                                <td><?= $r['supplier_name'] ?></td>
                                                <td><?= $r['no_transaksi'] ?></td>
                                                <td><?= $r['keterangan'] ?></td>
                                                <td>
                                                    <?php 
                                                    $amount = (float)$r['kurs'] != 1 ? ((float)$r['kredit'] != 0 ? $r['kredit'] : $r['debit']) : 0;
                                                    echo toRupiah($amount) . " <b>{$r['valas']}</b>";
                                                    ?>
                                                </td>
                                                <td><?= $r['kurs'] == "1" ? "" : toRupiah($r['kurs']) ?></td>
                                                <td><?= toRupiah($r['debit'] * $r['kurs']) ?></td>
                                                <td><?= toRupiah($r['kredit']) ?></td>
                                                <td><?= toRupiah($sisaSaldo) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="8" style="text-align: center;font-weight:bold;">
                                                <b>Sub Total</b>
                                            </td>
                                            <td><b><?= toRupiah($totalDebit) ?></b></td>
                                            <td><b><?= toRupiah($totalKredit) ?></b></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" style="text-align: center;">
                                                <b>Total</b>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td><b><?= toRupiah($sisaSaldo) ?></b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
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
    
        // Fungsi untuk memuat selected options
        function loadSelectedOptions(selectElement, selectedIds) {
            if (selectedIds && selectedIds.length > 0) {
                $.ajax({
                    url: '<?= base_url("laporan-accounting/bukubesar/get-sub-akun"); ?>',
                    data: { 
                        no_subs: selectedIds,
                        jenis_account: $('#jenis_account').val()
                    },
                    dataType: 'json'
                }).done(function(data) {
                    data.forEach(function(item) {
                        var option = new Option(
                            item.number + ' ' + item.name,
                            item.id,
                            true,
                            true
                        );
                        selectElement.append(option);
                    });
                    selectElement.trigger('change');
                });
            }
        }

        // Inisialisasi Select2 untuk semua dropdown
        function initSelect2() {
            $('.account-select').select2({
                placeholder: "Pilih Akun",
                allowClear: true,
                theme: "bootstrap-5",
                ajax: {
                    url: '<?= base_url("laporan-accounting/bukubesar/get-sub-akun"); ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            jenis_account: $('#jenis_account').val()
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.number,
                                text: item.number + ' ' + item.name,
                            }))
                        };
                    }
                },
                minimumInputLength: 1
            });
        }

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

        initSelect2();
        
        // Load selected options dari form submit
        loadSelectedOptions(
            $('#account_id'), 
            <?= json_encode(isset($_GET['account_id']) ? $_GET['account_id'] : []) ?>
        );

        
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

        $('.supplier_id').select2({
            placeholder: "Pilih Supplier",
            theme: "bootstrap-5",
            allowClear: false
        });

        $('.divisi_id,.jenis_account,.range_account_start_id,.range_account_finish_id,.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.divisi_id,.jenis_account,.range_account_start_id,.range_account_finish_id,.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px').css('z-index', '1');

        $('.divisi_id,.jenis_account,.range_account_start_id,.range_account_finish_id,.supplier_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

    });

    function printPDF(url) {
        const formData = $('.create-form').serializeArray();
        
        // Untuk field multiple select, kita perlu format ulang
        const accountIds = $('#account_id').val(); // Ini akan array
        
        const $form = $('<form>', {
            action: url,
            method: 'GET',
            target: '_blank',
        });

        // Tambahkan semua field dari form
        $.each(formData, function(index, field) {
            $('<input>')
                .attr({
                    type: 'hidden',
                    name: field.name,
                    value: field.value,
                })
                .appendTo($form);
        });
        
        // Tambahkan account_id[] jika ada
        if (accountIds && accountIds.length > 0) {
            $.each(accountIds, function(index, value) {
                $('<input>')
                    .attr({
                        type: 'hidden',
                        name: 'account_id[]',
                        value: value,
                    })
                    .appendTo($form);
            });
        }

        $form.appendTo('body').submit().remove();
    };

    function printExcel(url) {
        const formData = $('.create-form').serializeArray();
        const accountIds = $('#account_id').val();
        
        const $form = $('<form>', {
            action: url,
            method: 'GET',
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
        
        // Tambahkan account_id[] jika ada
        if (accountIds && accountIds.length > 0) {
            $.each(accountIds, function(index, value) {
                $('<input>')
                    .attr({
                        type: 'hidden',
                        name: 'account_id[]',
                        value: value,
                    })
                    .appendTo($form);
            });
        }

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

    function updateSelectedAccounts() {
        const select = document.getElementById("account_id");
        const selectedDiv = document.getElementById("selectedAccounts");
        selectedDiv.innerHTML = "";

        Array.from(select.selectedOptions).forEach(opt => {
            const badge = document.createElement("span");
            badge.className = "badge bg-primary text-white px-3 py-2";
            badge.textContent = opt.text;
            selectedDiv.appendChild(badge);
        });
    }
</script>


<?= $this->endSection(); ?>
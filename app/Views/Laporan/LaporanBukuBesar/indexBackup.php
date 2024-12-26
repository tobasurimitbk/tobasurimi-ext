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
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="form-floating" style="height: 50px;">
                                <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                                <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button disabled class="btn btn-secondary" type="button">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_header" name="id_header" id="id_header">
                                <option value="">All</option>
                                <?php foreach ($dataHeaderAkun ?? [] as $HeaderAkunData) : ?>
                                    <option <?= isset($_POST['id_header']) ? (encrypt($HeaderAkunData->id) == $_POST['id_header'] ? 'selected' : '') : '' ?> value="<?= encrypt($HeaderAkunData->id); ?>" data-header-id=""><?= $HeaderAkunData->nama_header; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Header Akun</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="form-floating" style="height: 50px;">
                                <select class="form-select id_sub_akun" multiple name="id_sub_akun[]" id="id_sub_akun">
                                    <option value="">All</option>
                                    <?php foreach ($dataSubAkuns ?? [] as $SubAkunsData) : ?>
                                        <option <?= isset($_POST['id_sub_akun']) ? (in_array(encrypt($SubAkunsData->id), $_POST['id_sub_akun']) ? 'selected' : '') : '' ?> value="<?= encrypt($SubAkunsData->id); ?>" data-header-id=""><?= $SubAkunsData->nama_sub; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Pilih Sub Akun (COA)</label>
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
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-hover" id="myTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama Akun / Tanggal</th>
                                <th>Transaksi</th>
                                <th>No</th>
                                <th>Deskripsi</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($dataJurnalUmumWithGroup)  == 0): ?>
                                <tr>
                                    <td colspan="7">Tidak Ada Transaksi</td>
                                </tr>
                            <?php else: ?>
                                <?php
                                function format_ribuan($nilai)
                                {
                                    $nilais = "";
                                    if ($nilai < 0) {
                                        $nilaiFloat = floatval($nilai);
                                        $nilais = '(' . number_format(abs($nilaiFloat), 2, ',', '.') . ')';
                                    } else {
                                        $nilaiFloat = floatval($nilai);
                                        $nilais = number_format($nilaiFloat, 2, ',', '.');
                                    }
                                    return $nilais;
                                }
                                // kelompok
                                foreach ($dataHeaderAkun as $HeaderAkunData) :
                                    foreach ($dataJurnalUmumWithGroup as $JurnalUmumDataGroup) :
                                        if ($JurnalUmumDataGroup->header_id == $HeaderAkunData->id) :
                                ?>
                                            <tr class="clickable" data-toggle="collapse" data-target=".collapse_<?= encrypt($HeaderAkunData->id); ?>" aria-expanded="false" data-header-id="<?= encrypt($HeaderAkunData->id); ?>">
                                                <td colspan="7"><i class="fas fa-chevron-down"></i><?= $HeaderAkunData->no_header . "-" . $HeaderAkunData->nama_header; ?></td>
                                            </tr>

                                        <?php
                                        endif;
                                    endforeach;
                                    $saldo = 0;
                                    $totalsaldo = 0;
                                    $totaldebit = 0;
                                    $totalkredit = 0;
                                    foreach ($dataJurnalUmum as $JurnalUmumData) :
                                        if ($JurnalUmumData->header_id == $HeaderAkunData->id) :
                                            if ($JurnalUmumData->debit == 0) {
                                                $saldo = $saldo + $JurnalUmumData->debit - $JurnalUmumData->kredit;
                                            } else {
                                                $saldo = $saldo + $JurnalUmumData->debit;
                                            }
                                            $totaldebit += $JurnalUmumData->debit;
                                            $totalkredit += $JurnalUmumData->kredit;
                                        ?>
                                            <tr class="collapse_<?= encrypt($HeaderAkunData->id); ?> collapse out">
                                                <td><?= date('d-m-Y', strtotime($JurnalUmumData->tanggal_jurnal)); ?></td>
                                                <td><?= $JurnalUmumData->value; ?></td>
                                                <td><?= $JurnalUmumData->no_transaksi; ?></td>
                                                <td><?= $JurnalUmumData->keterangan; ?></td>
                                                <td><?= format_ribuan($JurnalUmumData->debit); ?></td>
                                                <td><?= format_ribuan($JurnalUmumData->kredit); ?></td>
                                                <td><?= format_ribuan($saldo); ?></td>
                                            </tr>
                                        <?php
                                        endif;
                                    endforeach;
                                    foreach ($dataJurnalUmumWithGroup as $JurnalUmumDataGroup) :
                                        if ($JurnalUmumDataGroup->header_id == $HeaderAkunData->id) :
                                        ?>
                                            <tr data-header-id="<?= encrypt($HeaderAkunData->id); ?>">
                                                <td colspan="4" style="text-align: right;">Total <?= $HeaderAkunData->no_header . "-" . $HeaderAkunData->nama_header; ?></td>
                                                <td><?= format_ribuan($totaldebit); ?></td>
                                                <td><?= format_ribuan($totalkredit); ?></td>
                                                <td><?= format_ribuan($totaldebit - $totalkredit); ?></td>
                                            </tr>
                                <?php
                                        endif;
                                    endforeach; // akhir kelompok 
                                endforeach; // akhir kelompok 
                                ?>
                            <?php endif ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Mendapatkan tanggal saat ini
        var currentDate = new Date();

        // Inisialisasi datepicker untuk dateStart dengan nilai default tanggal 1 di bulan berjalan
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

        // Inisialisasi datepicker untuk dateEnd
        $(".dateEnd").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        //CSS SELECT2 FLOATING LABEL
        $('.id_header').select2({
            placeholder: "Pilih Header Account",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.id_sub_akun').select2({
            placeholder: "Pilih Sub Account (COA)",
            theme: "bootstrap-5",
            allowClear: false
        });

        $('.id_header, .id_sub_akun')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_header, .id_sub_akun')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px').css('z-index', '1');

        $('.id_header, .id_sub_akun')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Tambahkan event listener untuk mengatur dateStart saat dateEnd berubah
        $(".dateEnd").on("changeDate", function(e) {
            // Ambil tanggal yang dipilih pada dateEnd
            var selectedDate = e.date;

            // Atur dateStart menjadi tanggal 1 di bulan yang sama
            $(".dateStart").datepicker("setDate", new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1));
        });

        // Set nilai awal dateStart pada saat dokumen siap (document ready)
        $(".dateStart").datepicker("setDate", new Date(currentDate.getFullYear(), currentDate.getMonth(), 1));
        // $(".dateEnd").datepicker("setDate", new Date(currentDate));
        $(".clickable").click(function(e) {
            e.preventDefault();
            var targetClass = $(this).data('target');
            if ($(targetClass).hasClass('out')) {
                $(targetClass).addClass('in')
                $(targetClass).removeClass('out')
            } else {
                $(targetClass).addClass('out')
                $(targetClass).removeClass('in')
            }
            $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
        });

        $("#id_header").change(function() {
            var selectedValue = $(this).val();
            if (!selectedValue) {
                // console.log(selectedValue);
                $("tbody tr").each(function() {
                    if ($(this).attr('data-header-id')) {
                        $(this).show();
                        var targetClass = $(this).data('target');
                        if ($(targetClass).hasClass('in')) {
                            $(targetClass).addClass('out')
                            $(targetClass).removeClass('in')
                        }
                    }
                });
            } else {
                $("tbody tr").each(function() {
                    var headerIdValue = $(this).data('header-id');
                    console.log("Selected :" + selectedValue);
                    console.log("Header :" + headerIdValue);
                    var targetClass = $(this).data('target');
                    if (headerIdValue === selectedValue) {
                        $(this).show();
                    } else {
                        $(this).hide();
                        if ($(targetClass).hasClass('in')) {
                            $(targetClass).addClass('out')
                            $(targetClass).removeClass('in')
                        }
                    }
                });
            }
        });
    });
    const convertDateFormat = function(dateString) {
        // Memisahkan tanggal, bulan, dan tahun dari string
        var dateParts = dateString.split("/");

        // Membalikkan urutan elemen array untuk membuat format "YYYY-MM-DD"
        var formattedDate = dateParts[2] + "-" + dateParts[1] + "-" + dateParts[0];

        return formattedDate;
    }
    const printPDF = function(url) {
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
    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var filter = $(".id_header").val() ? $(".id_header").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter;
        // console.log(url2);
        window.open(url2, "_blank");
    }
</script>


<?= $this->endSection(); ?>
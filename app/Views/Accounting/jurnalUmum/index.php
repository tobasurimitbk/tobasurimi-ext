<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <form action="<?= base_url('jurnal/addJurnal') ?>" method="post" onsubmit="return validateForm();">

        <input type="hidden" class="id" name="id" id="id" />
        <?= csrf_field() ?>
        <div class="section-header">
            <h1>Jurnal Umum</h1>
            <button class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        </div>
        <!-- Check and display success message -->
        <?php if (session()->has('success_message')) : ?>
            <div class="alert alert-success " id="alert-berhasil">
                <?= session('success_message') ?>
            </div>
        <?php endif; ?>

        <!-- Check and display error message -->
        <?php if (session()->has('error_message')) : ?>
            <div class="alert alert-danger " id="alert-error">
                <?= session('error_message') ?>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="col-form-label">Tanggal Transaksi</label>
                        <input autocomplete="one-time-code" class="form-control input-picker tgl_transaksi" id="tgl_transaksi" name="tgl_transaksi" placeholder="Pilih Tanggal">
                    </div>
                    <div class="form-group col-sm-3">
                        <label class="col-form-label">Type Transaksi</label>
                        <select class="form-select type_transaksi" name="type_transaksi" id="type_transaksi" required="">
                            <option value="" data-code=""></option>
                            <?php
                            if (!empty($dataMetadataTipeTransaksi)) {
                                foreach ($dataMetadataTipeTransaksi as $Tipe) {
                            ?>
                                    <option value="<?= $Tipe->hexid; ?>"><?= $Tipe->value; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi" id="" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="width: 3%;"></th>
                                <th scope="col" style="width: 20%;">Nama Akun</th>
                                <th scope="col" style="width: 20%;">Debit</th>
                                <th scope="col" style="width: 20%;">Kredit</th>
                                <th scope="col" style="width: 20%;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="tbody2" style="cursor: pointer;">
                            <tr>
                                <td scope="row"><input name="chk_a[]" type="checkbox" class="checkall_a" value="" /></td>
                                <td>
                                    <select name="cari[]" id="akun_coa_1" class="form-control " style="background-color: white;" required>
                                        <option value="" data-code=""></option>
                                        <?php
                                        if (!empty($subAkuns)) {
                                            foreach ($subAkuns as $sub_ar) {
                                        ?>
                                                <option value="<?= $sub_ar->hexid; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="debit[]" id="debit" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);getItems();" class="form-control yy">
                                </td>
                                <td>
                                    <input type="text" name="kredit[]" id="kredit" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" onchange="this.value = formatRupiah(this.value);getItems2();" class="form-control xx">
                                </td>
                                <td>
                                    <input type="text" name="ket[]" id="ket" class="form-control">
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <td scope="col"></td>
                            <td scope="col"></td>
                            <td scope="col"><input type="text" name="jumlahDebet" id="jumlahDebet" class="form-control" readonly></td>
                            <td scope="col"><input type="text" name="jumlahKredit" id="jumlahKredit" class="form-control" readonly></td>
                            <td scope="col">
                                <button type="button" class="btn btn-primary" onclick="addRow('tbody2')"><i class="fas fa-plus"></i></button>
                                <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')"><i class="far fa-trash-alt"></i></button>
                            </td>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    $("#akun_coa_1").select2({
        placeholder: "Pilih Akun",
        theme: "bootstrap-5"
    });

    $(".tgl_transaksi").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    function getItems() {
        var inputs = document.getElementsByClassName('yy'),
            result = document.getElementById('jumlahDebet'),
            sum = 0;
        for (var i = 0; i < inputs.length; i++) {
            var ip = inputs[i];

            // console.log(parseFloat(hilang_titik(ip.value)));
            if (ip.name && ip.name.indexOf("jumlahDebet") < 0) {
                sum += parseFloat(hilang_titik(ip.value)) || 0;
            }

        }
        result.value = formatRupiah(sum.toString());
    }

    function getItems2() {
        var inputs = document.getElementsByClassName('xx'),
            result = document.getElementById('jumlahKredit'),
            sum = 0;
        for (var i = 0; i < inputs.length; i++) {
            var ip = inputs[i];

            if (ip.name && ip.name.indexOf("jumlahKredit") < 0) {
                sum += parseFloat(hilang_titik(ip.value)) || 0;
            }

        }
        result.value = formatRupiah(sum.toString());
    }

    function addRow(tableID) {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var row = table.insertRow();
        var colCount = table.rows[0].cells.length;
        var counter = 1;
        // console.log(row);
        rowCount++;
        for (var i = 0; i < colCount; i++) {
            var newcell = row.insertCell(i);
            newcell.innerHTML = table.rows[0].cells[i].innerHTML;
            var child = newcell.children;
            for (var i2 = 0; i2 < child.length; i2++) {
                var test = newcell.children[i2].tagName;
                // console.log(test);
                switch (test) {
                    case "INPUT":
                        if (newcell.children[i2].type == 'checkbox') {
                            newcell.children[i2].value = "";
                            newcell.children[i2].checked = false;
                        } else {
                            newcell.children[i2].value = "";
                        }
                        if (newcell.children[i2].id == 'nomber') {
                            newcell.children[i2].value = counter++;
                        }
                        break;
                    case "SELECT":
                        var akunCoaSelect = newcell.children[i2];
                        var newID = "akun_coa_" + rowCount;
                        // console.log(newID);
                        akunCoaSelect.id = newID;
                        akunCoaSelect.value = ""; // Destroy the existing Select2 instance
                        $("#" + newID).next(".select2-container").remove();

                        $("#" + newID).select2({
                            placeholder: "Pilih Akun",
                            theme: "bootstrap-5"
                        });
                        break;
                    default:
                        break;
                }
            }
        }
    }

    function deleteRow(tableID) {
        try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;

            // Variable to track whether any checkbox is checked
            var isChecked = false;

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                var chkbox = row.cells[0].childNodes[0];

                if (null != chkbox && true == chkbox.checked) {
                    isChecked = true;
                    table.deleteRow(i);
                    rowCount--;
                    i--;
                }
            }

            // If no checkbox is checked, remove the last row
            if (!isChecked && rowCount > 1) {
                table.deleteRow(rowCount - 1);
                rowCount--;
            }

            // Recalculate the totals after deletion
            getItems();
            getItems2();
        } catch (e) {
            alert(e);
        }
    }


    function formatRupiah(angka) {
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return 'Rp. ' + ribuanFormatted + ',' + desimal;
    }

    function hilang_titik(string) {
        string = string.replace('Rp. ', '');
        string = string.split('.').join('')
        return string.replace(',', '.');
    }

    function validateForm() {
        var jumlahDebet = parseFloat(hilang_titik(document.getElementById('jumlahDebet').value)) || 0;
        var jumlahKredit = parseFloat(hilang_titik(document.getElementById('jumlahKredit').value)) || 0;

        if (jumlahDebet !== jumlahKredit) {
            // alert("Jumlah Debet dan Kredit harus sama.");
            Swal.fire({
                icon: 'error',
                title: 'Debit dan Kredit Tidak Balance',
                confirmButtonColor: '#4e73df',
            })
            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }

    getItems();
    getItems2();
    window.onload = function() {
        setTimeout(function() {
            <?php if (session()->has('success_message')) : ?>
                document.getElementById('alert-berhasil').style.display = 'none';
            <?php endif; ?>
            <?php if (session()->has('error_message')) : ?>
                document.getElementById('alert-error').style.display = 'none';
            <?php endif; ?>
        }, 5000);
    };
</script>


<?= $this->endSection(); ?>
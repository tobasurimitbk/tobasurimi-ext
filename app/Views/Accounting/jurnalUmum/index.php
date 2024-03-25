<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <form action="<?= base_url('jurnal/addJurnal') ?>" method="post" onsubmit="return validateForm();">

        <input type="hidden" class="id" name="id" id="id" />
        <?= csrf_field() ?>
        <div class="section-header">
            <h1>Jurnal</h1>
            <!-- <button class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Simpan Jurnal
            </button> -->
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
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select type_transaksi" name="type_transaksi" id="type_transaksi">
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
                                    <label for="floatingInput">Type Transaksi</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control no_bukti" id="no_bukti" name="no_bukti" placeholder="No. Bukti" value="">
                                    <label for="floatingInput">No. Bukti</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center" style="display: none;">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="generateNewCode()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi" id="" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="width: 20%;">Tanggal</th>
                                <th scope="col" style="width: 20%;">Nama Akun</th>
                                <th scope="col" style="width: 17%;">Keterangan</th>
                                <th scope="col" style="width: 20%;">Debit</th>
                                <th scope="col" style="width: 20%;">Kredit</th>
                                <th scope="col" style="width: 3%;"></th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="tbody2" style="cursor: pointer;">
                            <tr>
                                <td>
                                    <input autocomplete="one-time-code" class="form-control input-picker tgl_transaksi" id="tgl_transaksi" name="tgl_transaksi[]" placeholder="Pilih Tanggal">
                                </td>
                                <td>
                                    <input type="text" id="gsearchsimple" class="form-control" placeholder="Search Akun" />
                                    <input type="hidden" name="cari[]" id="id_coa" />

                                    <ul class="list-group position-absolute" id="searchResults" style="z-index: 1000;">

                                    </ul>
                                    <div id="localSearchSimple"></div>
                                </td>
                                <td>
                                    <input type="text" name="ket[]" id="ket" class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="debit[]" id="debit" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');checkValueDebitKredit();" onchange="this.value = formatRupiah(this.value);getItems();" class="form-control yy">
                                </td>
                                <td>
                                    <input type="text" name="kredit[]" id="kredit" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');checkValueDebitKredit();" onchange="this.value = formatRupiah(this.value);getItems2();" class="form-control xx">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')"><i class="far fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <td scope="col">
                                <button class="btn btn-show-form btn-add">
                                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Simpan Jurnal
                                </button>
                            </td>
                            <td scope="col"></td>
                            <td scope="col"></td>
                            <td scope="col"><input type="text" name="jumlahDebet" id="jumlahDebet" class="form-control" readonly></td>
                            <td scope="col"><input type="text" name="jumlahKredit" id="jumlahKredit" class="form-control" readonly></td>
                            <td scope="col">
                                <button type="button" class="btn btn-primary" onclick="addRow('tbody2')"><i class="fas fa-plus"></i></button>
                            </td>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    $(document).ready(function() {
        $("#type_transaksi").focus();
        $("#type_transaksi").change(function(e) {
            var noBukti = $("#no_bukti").val();
            if (noBukti) {
                $("#no_bukti").attr("readonly", false);
                $("#no_bukti").val('');
                $("#auto_generate").prop('checked', false);
                $("#no_bukti").focus();
            } else {
                $("#no_bukti").focus();
            }
            if ($(this).val()) {
                $(".input-generate").css('display', '')
            } else {
                $(".input-generate").css('display', 'none')
            }
        });

        $("#akun_coa_1").select2({
            placeholder: "Pilih Akun",
            theme: "bootstrap-5"
        });

        $("#tgl_transaksi").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        }).change(function(e) {
            if ($('#gsearchsimple').val()) {
                $('#tgl_transaksi').focus();
            } else {
                $('#gsearchsimple').focus();
            }
        });

        $('#ket, #debit, #kredit').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                addRow('tbody2');
            }
        });

        //search coa
        $('#gsearchsimple').on('keypress', function(e) {
            let csrfToken = '<?= csrf_token() ?>';
            var query = $('#gsearchsimple').val();
            let csrf = $(`[name="${csrfToken}"]`);
            var inputWidth = $(this).outerWidth();
            if (e.which == 13) {
                console.log($(this).val());
                e.preventDefault();
                if (query.length >= 2) {
                    $.ajax({
                        url: "<?= base_url("jurnal/getSubAkunsExact"); ?>",
                        method: "POST",
                        data: {
                            query: query
                        },
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        success: function(data) {
                            console.log(data);
                            // $('#searchResults').html('');
                            data.forEach(function(item) {
                                $('#id_coa').val(item.hexid);
                                $('#gsearchsimple').val(item.no_sub + " " + item.nama_sub).change();
                                $('#searchResults').css('display', 'none');
                            });
                        }
                    })
                }
            } else {
                $('#searchResults').css('width', inputWidth);
                $('#searchResults').css('display', 'block');
                if (query.length >= 2) {
                    $.ajax({
                        url: "<?= base_url("jurnal/getSubAkuns"); ?>",
                        method: "POST",
                        data: {
                            query: query
                        },
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        success: function(data) {
                            $('#searchResults').html('');
                            data.forEach(function(item) {
                                var subAkunId = item.hexid;
                                var noSubNamaSub = item.no_sub + ' ' + item.nama_sub;
                                var listItem = '<a href="javascript:void(0)" class="gsearch" data-sub_akun_id="' + subAkunId + '" style="color:#333;text-decoration:none;"><li class="list-group-item contsearch">' + noSubNamaSub + '</li></a>';
                                $('#searchResults').append(listItem); // Tambahkan item ke daftar hasil pencarian
                            });
                        }
                    })
                }
                if (query.length == 0) {
                    $('#searchResults').css('display', 'none');
                }
            }
        });

        $('#localSearchSimple').jsLocalSearch({
            action: "Show",
            html_search: true,
            mark_text: "marktext"
        });
        $('#searchResults').on('click', '.gsearch', function() {
            var coa = $(this).text();
            var subAkunId = $(this).data('sub_akun_id');
            $('#gsearchsimple').val(coa);
            $('#id_coa').val(subAkunId);
            $('#searchResults').css('display', 'none');
        });
        //end search coa
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
    var counter = 1;

    function addRow(tableID) {
        var table = document.getElementById(tableID);
        var row = table.insertRow();

        // Create cells with appropriate colspan
        row.innerHTML = `
        <td>
            <input autocomplete="one-time-code" class="form-control input-picker tgl_transaksi" id="tgl_transaksi_${counter}" name="tgl_transaksi[]" placeholder="Pilih Tanggal" >
        </td>
        <td>
            <input type="text" id="gsearchsimple_${counter}" data-counters="${counter}" class="form-control" placeholder="Search Akun" />
            <input type="hidden" name="cari[]" id="id_coa_${counter}"  />
            <ul class="list-group position-absolute" id="searchResults_${counter}" data-counters="${counter}" style="z-index: 1000;"></ul>
            <div id="localSearchSimple_${counter}"></div>
        </td>
        <td>
            <input type="text" name="ket[]" id="ket_${counter}" class="form-control">
        </td>
        <td>
            <input type="text" name="debit[]" id="debit_${counter}" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');checkValueDebitKredit('${counter}');" onchange="this.value = formatRupiah(this.value);getItems();" class="form-control yy">
        </td>
        <td>
            <input type="text" name="kredit[]" id="kredit_${counter}" onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');checkValueDebitKredit('${counter}');" onchange="this.value = formatRupiah(this.value);getItems2();" class="form-control xx">
        </td>
        <td>
            <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')"><i class="far fa-trash-alt"></i></button>
        </td>`;
        $(`#tgl_transaksi_${counter}`).datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });
        $(`#ket_${counter}, #debit_${counter}, #kredit_${counter}`).keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                addRow('tbody2');
            }
        });
        var counters = 0;

        //search coa
        $(`#gsearchsimple_${counter}`).on('keypress', function(e) {
            let csrfToken = '<?= csrf_token() ?>';
            var query = $(this).val();
            counters = $(this).data('counters');
            let csrf = $(`[name="${csrfToken}"]`);
            var inputWidth = $(this).outerWidth();
            var searchResultsId = `#searchResults_${counters}`;
            if (e.which == 13) {
                console.log($(this).val());
                e.preventDefault();
                if (query.length >= 2) {
                    $.ajax({
                        url: "<?= base_url("jurnal/getSubAkunsExact"); ?>",
                        method: "POST",
                        data: {
                            query: query
                        },
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        success: function(data) {
                            console.log(data);
                            // $('#searchResults').html('');
                            data.forEach(function(item) {
                                $(`#id_coa_${counters}`).val(item.hexid);
                                $(`#gsearchsimple_${counters}`).val(item.no_sub + " " + item.nama_sub).change();
                                $(searchResultsId).css('display', 'none');
                            });
                        }
                    })
                }
            } else {
                $(searchResultsId).css('width', inputWidth);
                $(searchResultsId).css('display', 'block');
                if (query.length >= 2) {
                    $.ajax({
                        url: "<?= base_url("jurnal/getSubAkuns"); ?>",
                        method: "POST",
                        data: {
                            query: query
                        },
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        success: function(data) {
                            $(searchResultsId).html('');
                            data.forEach(function(item) {
                                var subAkunId = item.hexid;
                                var noSubNamaSub = item.no_sub + ' ' + item.nama_sub;
                                var listItem = '<a href="javascript:void(0)" class="gsearch" data-sub_akun_id="' + subAkunId + '" style="color:#333;text-decoration:none;"><li class="list-group-item contsearch">' + noSubNamaSub + '</li></a>';
                                $(searchResultsId).append(listItem); // Tambahkan item ke daftar hasil pencarian
                            });
                        }
                    })
                }
                if (query.length == 0) {
                    $(searchResultsId).css('display', 'none');
                }
            }
        });

        $(`#localSearchSimple_${counter}`).jsLocalSearch({
            action: "Show",
            html_search: true,
            mark_text: "marktext"
        });
        $(`#searchResults_${counter}`).on('click', '.gsearch', function() {
            // var counters = $(this).data('counters');
            var coa = $(this).text();
            var subAkunId = $(this).data('sub_akun_id');
            $(`#gsearchsimple_${counters}`).val(coa);
            $(`#id_coa_${counters}`).val(subAkunId);
            $(`#searchResults_${counters}`).css('display', 'none');
        });

        counter++;
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

    function generateNewCode() {
        let csrfToken = '<?= csrf_token() ?>';
        let value = document.getElementById('auto_generate').checked ? true : false;
        let type = document.getElementById('type_transaksi').value;
        let csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $("input[name='kode_barang']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("jurnal/generate-no-bukti"); ?>`,
                data: {
                    transaksi: type
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                success: function(res) {
                    csrf.val(res.token);
                    $("input[name='no_bukti']").attr("readonly", true);
                    $("input[name='no_bukti']").val(res.codeNew);
                    $("#tgl_transaksi").focus();
                }
            })
        } else {
            $("input[name='no_bukti']").attr("readonly", false);
            $("input[name='no_bukti']").val("");
        }
    }

    function formatRupiah(angka) {
        if (angka.length) {
            angka = angka.replace(/\./g, ',');
            angka = angka.replace(/[^\d,]/g, '');
            var parts = angka.split(',');
            var ribuan = parts[0];
            var desimal = parts[1] || '00';
            var reverse = ribuan.toString().split('').reverse().join('');
            var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
            return 'Rp. ' + ribuanFormatted + ',' + desimal;
        } else {
            return "";
        }
    }

    function hilang_titik(string) {
        string = string.replace('Rp. ', '');
        string = string.split('.').join('')
        return string.replace(',', '.');
    }

    function validateForm() {
        var jumlahDebet = parseFloat(hilang_titik(document.getElementById('jumlahDebet').value)) || 0;
        var jumlahKredit = parseFloat(hilang_titik(document.getElementById('jumlahKredit').value)) || 0;

        // Check if jumlahDebet and jumlahKredit are equal
        if (jumlahDebet !== jumlahKredit) {
            Swal.fire({
                icon: 'error',
                title: 'Debit dan Kredit Tidak Balance',
                confirmButtonColor: '#4e73df',
            });
            return false; // Prevent form submission
        }

        // Check if any 'cari[]' fields are empty
        var cariInputs = document.getElementsByName('cari[]');
        var tglInputs = document.getElementsByName('tgl_transaksi[]');
        var debitInputs = document.getElementsByName('debit[]');
        var kreditInputs = document.getElementsByName('kredit[]');
        for (var i = 0; i < cariInputs.length; i++) {
            if (cariInputs[i].value.trim() === '' && tglInputs[i].value.trim() === '' && debitInputs[i].value.trim() === '' && kreditInputs[i].value.trim() === '') {
                deleteRow('tbody2')
            } else if (cariInputs[i].value.trim() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Pastikan Akun COA Sudah Terpilih',
                    confirmButtonColor: '#4e73df',
                });
                return false; // Prevent form submission
            } else if (tglInputs[i].value.trim() === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Pastikan Tanggal Sudah Terisi',
                    confirmButtonColor: '#4e73df',
                });
                return false; // Prevent form submission
            }
        }

        return true; // Allow form submission
    }

    function checkValueDebitKredit(counter) {
        if (counter) {
            console.log(counter);
            var debit = $(`#debit_${counter}`).val();
            var kredit = $(`#kredit_${counter}`).val();

            if (debit) {
                $(`#kredit_${counter}`).attr('readonly', true);
            } else {
                $(`#kredit_${counter}`).attr('readonly', false);
            }
            if (kredit) {
                $(`#debit_${counter}`).attr('readonly', true);
            } else {
                $(`#debit_${counter}`).attr('readonly', false);
            }
        } else {
            var debit = $('#debit').val();
            var kredit = $('#kredit').val();

            if (debit) {
                $('#kredit').attr('readonly', true);
            } else {
                $('#kredit').attr('readonly', false);
            }
            if (kredit) {
                $('#debit').attr('readonly', true);
            } else {
                $('#debit').attr('readonly', false);
            }
        }
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
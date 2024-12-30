<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="section-header">
            <h1>Setting Akun Costing</h1>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option value="<?= $d['id'] ?>" <?= !empty($rasio) && $rasio->department_id == $d['id'] ? "selected" : "" ?>>
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Keterangan</th>
                                    <th>COA</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table" style="cursor: pointer;">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id_costing" name="id_costing" id="id_costing" />
                    <input autocomplete="one-time-code" type="hidden" class="id_divisi" name="id_divisi" id="id_divisi" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" readonly placeholder="Nama Costing" id="parentName" name="parentName">
                                <label for="floatingInput">Nama Costing</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select coa_id" name="coa_id" id="coa_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun COA</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form" onclick="submitData()">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script>
    let sort = "nomor";
    let sortType = "asc";
    $(document).ready(function() {
        const csrfToken = '<?= csrf_token() ?>';
        const table = $('.dataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            serverSide: true,
            ordering: true,
            order: [
                [1, 'asc']
            ],
            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            ajax: {
                url: "<?= base_url("setting-akun-costing/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.divisi_id = $(".divisi_id").val();
                    data.sort = sort;
                    data.sortType = sortType;
                }
            },
            initComplete: function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 '>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            }, {
                data: "keterangan",
                className: "text-center",
            }, {
                data: "coa_id",
                className: "text-center",
            }],
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

        $(".divisi_id").change(function() {
            table.ajax.reload();
        });

        // Hide modal
        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            let csrf = $(`[name="${csrfToken}"]`);
            let id = data.id;
            let divisi_id = data.divisi_id;
            if (divisi_id) {
                let formData = new FormData();
                $('#parentName').val(null);
                console.log(id, divisi_id);

                $('.title-name').text("Update Akun Costing");
                $('.delete-btn').show();
                $.ajax({
                    url: "<?= base_url("setting-akun-costing/get"); ?>",
                    data: {
                        id: id,
                        csrf_token: csrf.val()
                    },
                    method: "POST",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    processData: true,
                    contentType: "application/x-www-form-urlencoded",
                    success: function(res) {
                        csrf.val();
                        if (res.status) {
                            console.log(res);
                            $("#id_costing").val(id);
                            $("#id_divisi").val(divisi_id);
                            $("#parentName").val(res.data.name);
                            $("#coa_id").val(res.data.coa_id).change();
                            $('.add-modal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                            });
                        }
                    }
                })
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: "Pilih dahulu department",
                    confirmButtonColor: '#4e73df',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            }

        });

        $(".coa_id").select2({
            placeholder: "Pilih Akun",
            theme: "bootstrap-5",
            allowClear: true
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {});

        $("#divisi_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');
    });


    function submitData() {
        // var value = $("#coa_id" + ID).val();
        var divisi = $("#id_divisi").val();
        var costing = $("#id_costing").val();
        var coa = $("#coa_id").val();
        let csrfToken = '<?= csrf_token() ?>';
        let csrf = $(`[name="${csrfToken}"]`);
        console.log({
            id: costing,
            coa: coa,
            divisi: divisi,
        })
        $.ajax({
            url: "<?= base_url("setting-akun-costing/save"); ?>",
            data: {
                id: costing,
                coa: coa,
                divisi: divisi,
                csrf_token: csrf.val()
            },
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            processData: true,
            contentType: "application/x-www-form-urlencoded",
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                        reverseButtons: true,
                        confirmButtonText: 'Oke',
                    }).then((result) => {
                        window.location.href = "<?= base_url('setting-akun-costing') ?>"
                    })
                }

            }
        });
    }
</script>

<?= $this->endSection(); ?>
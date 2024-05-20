<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <form>
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
                                <tr style="font-weight: bold !important;font-size: 14px !important;">
                                    <th colspan="2">Keterangan</th>
                                    <th>Akun COA</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table">
                                <?php if (isset($settingCosting)) : ?>
                                    <?php foreach ($settingCosting as $valueSetting) : ?>
                                        <?php if ($valueSetting['parent_id'] == NULL) : ?>
                                            <tr>
                                                <td colspan="2" style="font-weight: bold !important;font-size: 14px !important;"><?= $valueSetting['name'] ?></td>
                                                <td>
                                                    <select class="coa_id" name="coa_id<?= $valueSetting['id'] ?>" id="coa_id<?= $valueSetting['id'] ?>" onchange="submitData(<?= $valueSetting['id'] ?>, this.value)">
                                                        <option value=""></option>
                                                        <?php foreach ($subAkuns as $s) : ?>
                                                            <option value="<?= $s->id ?>" <?= $valueSetting['coa'] ==  $s->id ? "selected" : "" ?>><?= $s->no_sub ?> - <?= $s->nama_sub ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php foreach ($settingCosting as $childSetting) : ?>
                                                <?php if ($childSetting['parent_id'] == $valueSetting['id']) : ?>
                                                    <tr>
                                                        <td width="20%" style="font-weight: bold !important;font-size: 13px !important;"><?= $childSetting['name'] ?></td>
                                                        <td width="20%"></td>
                                                        <td>
                                                            <select class="coa_id" name="coa_id<?= $childSetting['id'] ?>" id="coa_id<?= $childSetting['id'] ?>" onchange="submitData(<?= $childSetting['id'] ?>, this.value)">
                                                                <option value=""></option>
                                                                <?php foreach ($subAkuns as $s) : ?>
                                                                    <option value="<?= $s->id ?>" <?= $valueSetting['coa'] ==  $s->id ? "selected" : "" ?>><?= $s->no_sub ?> - <?= $s->nama_sub ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <?php foreach ($settingCosting as $childParentSetting) : ?>
                                                        <?php if ($childParentSetting['parent_id'] == $childSetting['id']) : ?>
                                                            <tr>
                                                                <td width="20%"></td>
                                                                <td width="20%"><?= $childParentSetting['name'] ?></td>
                                                                <td>
                                                                    <select class="coa_id" name="coa_id<?= $childParentSetting['id'] ?>" id="coa_id<?= $childParentSetting['id'] ?>" onchange="submitData(<?= $childParentSetting['id'] ?>, this.value)">
                                                                        <option value=""></option>
                                                                        <?php foreach ($subAkuns as $s) : ?>
                                                                            <option value="<?= $s->id ?>" <?= $valueSetting['coa'] ==  $s->id ? "selected" : "" ?>><?= $s->no_sub ?> - <?= $s->nama_sub ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<script>
    $(document).ready(function() {
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

    function submitData(ID, value) {
        // var value = $("#coa_id" + ID).val();
        var divisi = $("#divisi_id").val();
        let csrfToken = '<?= csrf_token() ?>';
        let csrf = $(`[name="${csrfToken}"]`);
        console.log({
            id: ID,
            value: value,
            divisi: divisi,
        })
        $.ajax({
            url: "<?= base_url("setting-akun-costing/save"); ?>",
            data: {
                id: ID,
                value: value,
                divisi: divisi,
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
            processData: false,
            contentType: false,
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
<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Setting Akun Costing</h1>
    </div>
    <div class="card">
        <div class="card-body">
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
                                                <input type="text" id="gsearchsimple" class="form-control" placeholder="Search Akun" />
                                                <input type="hidden" name="cari[]" id="id_coa" />

                                                <ul class="list-group position-absolute" id="searchResults" style="z-index: 1000;">

                                                </ul>
                                                <div id="localSearchSimple"></div>
                                            </td>
                                        </tr>
                                        <?php foreach ($settingCosting as $childSetting) : ?>
                                            <?php if ($childSetting['parent_id'] == $valueSetting['id']) : ?>
                                                <tr>
                                                    <td width="20%" style="font-weight: bold !important;font-size: 13px !important;"><?= $childSetting['name'] ?></td>
                                                    <td width="20%"></td>
                                                    <td>
                                                        <input type="text" id="gsearchsimple" class="form-control" placeholder="Search Akun" />
                                                        <input type="hidden" name="cari[]" id="id_coa" />

                                                        <ul class="list-group position-absolute" id="searchResults" style="z-index: 1000;">

                                                        </ul>
                                                        <div id="localSearchSimple"></div>
                                                    </td>
                                                </tr>
                                                <?php foreach ($settingCosting as $childParentSetting) : ?>
                                                    <?php if ($childParentSetting['parent_id'] == $childSetting['id']) : ?>
                                                        <tr>
                                                            <td width="20%"></td>
                                                            <td width="20%"><?= $childParentSetting['name'] ?></td>
                                                            <td>
                                                                <input type="text" id="gsearchsimple" class="form-control" placeholder="Search Akun" />
                                                                <input type="hidden" name="cari[]" id="id_coa" />

                                                                <ul class="list-group position-absolute" id="searchResults" style="z-index: 1000;">

                                                                </ul>
                                                                <div id="localSearchSimple"></div>
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
</section>
<script>
    $(document).ready(function() {
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
</script>

<?= $this->endSection(); ?>
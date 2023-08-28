<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Log Attendance</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('employeeName')" class="sort">Employee</th>
                                <th onclick="changeSort('date_create')" class="sort">Check</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">
                            <?php
                            $i = 0;
                            foreach ($log as $value) {
                                $i++
                            ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $value->employeeName; ?></td>
                                    <td><?php echo $value->date_create; ?></td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function printReport() {
        document.location.href = 'log-attendance?month=' + document.getElementById('month').value + '&year=' + document.getElementById('year').value;
    }
</script>


<?= $this->endSection(); ?>
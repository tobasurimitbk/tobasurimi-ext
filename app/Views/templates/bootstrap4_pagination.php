<?php
$links = $pager->links();
$currentPage = 1;

// Cari current page
foreach ($links as $link) {
    if ($link['active']) {
        $currentPage = (int) $link['title'];
        break;
    }
}

// Tentukan range halaman yang ingin ditampilkan
$rangeStart = max(1, $currentPage - 1);
$rangeEnd = min(count($links), $currentPage + 1);
?>

<ul class="pagination justify-content-center mt-4">

    <?php if ($pager->hasPrevious()) : ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager->getPrevious() ?>">Previous</a>
        </li>
    <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link">Previous</span>
        </li>
    <?php endif; ?>

    <?php foreach ($links as $link) : ?>
        <?php
        $page = (int) $link['title'];
        if ($page < $rangeStart || $page > $rangeEnd) continue;
        ?>
        <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
            <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
        </li>
    <?php endforeach; ?>

    <?php if ($pager->hasNext()) : ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager->getNext() ?>">Next</a>
        </li>
    <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link">Next</span>
        </li>
    <?php endif; ?>

</ul>
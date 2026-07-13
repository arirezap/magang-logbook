<?php $pager->setSurroundCount(2); ?>
<div class="custom-pager-container d-inline-flex align-items-center bg-white shadow-sm rounded-pill px-3 py-2 gap-2" style="border: 1px solid #f0f0f0;">
    
    <!-- First Button -->
    <?php if ($pager->hasPrevious()) : ?>
        <a href="<?= $pager->getFirst() ?>" class="custom-pager-link text-dark text-decoration-none d-flex align-items-center justify-content-center" aria-label="<?= lang('Pager.first') ?>" title="Halaman Pertama">
            <i class="bi bi-chevron-double-left" style="font-size: 0.8rem; font-weight: bold;"></i>
        </a>
    <?php else: ?>
        <span class="custom-pager-link text-muted d-flex align-items-center justify-content-center" style="opacity: 0.4;">
            <i class="bi bi-chevron-double-left" style="font-size: 0.8rem; font-weight: bold;"></i>
        </span>
    <?php endif ?>

    <!-- Previous Button -->
    <?php if ($pager->hasPrevious()) : ?>
        <a href="<?= $pager->getPrevious() ?>" class="custom-pager-link text-dark text-decoration-none d-flex align-items-center justify-content-center" aria-label="<?= lang('Pager.previous') ?>">
            <i class="bi bi-chevron-left" style="font-size: 0.8rem; font-weight: bold;"></i>
        </a>
    <?php else: ?>
        <span class="custom-pager-link text-muted d-flex align-items-center justify-content-center" style="opacity: 0.4;">
            <i class="bi bi-chevron-left" style="font-size: 0.8rem; font-weight: bold;"></i>
        </span>
    <?php endif ?>

    <!-- Numbered Links -->
    <div class="d-flex align-items-center gap-1 mx-2">
        <?php foreach ($pager->links() as $link) : ?>
            <a href="<?= $link['uri'] ?>" class="custom-pager-link <?= $link['active'] ? 'active' : '' ?> text-decoration-none fw-semibold">
                <?= $link['title'] ?>
            </a>
        <?php endforeach ?>
    </div>

    <!-- Next Button -->
    <?php if ($pager->hasNext()) : ?>
        <a href="<?= $pager->getNext() ?>" class="custom-pager-link text-dark text-decoration-none d-flex align-items-center justify-content-center" aria-label="<?= lang('Pager.next') ?>">
            <i class="bi bi-chevron-right" style="font-size: 0.8rem; font-weight: bold;"></i>
        </a>
    <?php else: ?>
        <span class="custom-pager-link text-muted d-flex align-items-center justify-content-center" style="opacity: 0.4;">
            <i class="bi bi-chevron-right" style="font-size: 0.8rem; font-weight: bold;"></i>
        </span>
    <?php endif ?>

    <!-- Last Button -->
    <?php if ($pager->hasNext()) : ?>
        <a href="<?= $pager->getLast() ?>" class="custom-pager-link text-dark text-decoration-none d-flex align-items-center justify-content-center" aria-label="<?= lang('Pager.last') ?>" title="Halaman Terakhir">
            <i class="bi bi-chevron-double-right" style="font-size: 0.8rem; font-weight: bold;"></i>
        </a>
    <?php else: ?>
        <span class="custom-pager-link text-muted d-flex align-items-center justify-content-center" style="opacity: 0.4;">
            <i class="bi bi-chevron-double-right" style="font-size: 0.8rem; font-weight: bold;"></i>
        </span>
    <?php endif ?>

    <!-- Divider -->
    <div class="mx-1" style="width: 1px; height: 20px; background-color: #e0e0e0;"></div>

    <!-- Per Page Selector -->
    <div class="d-flex align-items-center">
        <?php
            $currentPerPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        ?>
        <select class="form-select custom-pager-select border-primary-subtle rounded-pill shadow-none fw-medium text-dark px-3 py-1" style="width: 115px; font-size: 0.85rem; cursor: pointer; border-color: #a3b8ff !important;" onchange="updatePerPage(this.value)">
            <option value="10" <?= $currentPerPage == 10 ? 'selected' : '' ?>>10 / page</option>
            <option value="25" <?= $currentPerPage == 25 ? 'selected' : '' ?>>25 / page</option>
            <option value="50" <?= $currentPerPage == 50 ? 'selected' : '' ?>>50 / page</option>
            <option value="100" <?= $currentPerPage == 100 ? 'selected' : '' ?>>100 / page</option>
        </select>
    </div>

    <!-- Go To Page -->
    <div class="d-flex align-items-center ms-2" style="font-size: 0.85rem; font-weight: 500;">
        <span class="text-dark me-2">Go to</span>
        <input type="number" min="1" class="form-control custom-pager-input rounded-pill text-center shadow-none border-primary-subtle p-0 fw-semibold text-primary" style="width: 45px; height: 32px; font-size: 0.85rem; border-color: #a3b8ff !important;" onkeydown="if(event.key === 'Enter') { goToPage(this.value); }">
        <span class="text-dark ms-2">Page</span>
    </div>

</div>

<style>
.custom-pager-link {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: #333;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}
.custom-pager-link:hover:not(.active) {
    background-color: #f5f5f5;
}
.custom-pager-link.active {
    background-color: #e6ecff;
    color: #4361ee;
    border: 1px solid #4361ee;
}
.custom-pager-input::-webkit-outer-spin-button,
.custom-pager-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.custom-pager-input[type=number] {
  -moz-appearance: textfield;
}
.custom-pager-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15) !important;
}
.custom-pager-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15) !important;
}
</style>

<script>
function updateUrlParams(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    if (key === 'per_page') {
        url.searchParams.delete('page_logbooks');
    }
    window.location.href = url.toString();
}

function updatePerPage(val) {
    updateUrlParams('per_page', val);
}

function goToPage(page) {
    if(page && !isNaN(page) && page > 0) {
        updateUrlParams('page_logbooks', page);
    }
}
</script>

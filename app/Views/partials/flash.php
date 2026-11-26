<?php if (session()->getFlashdata('success')): ?>
    <div class="sdm-flash sdm-flash-success" data-auto-dismiss>
        <span>&#10003;</span>
        <span><?= esc(session()->getFlashdata('success')) ?></span>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="sdm-flash sdm-flash-error">
        <span>!</span>
        <span><?= esc(session()->getFlashdata('error')) ?></span>
    </div>
<?php endif; ?>

<? if ($_SESSION['flash']): ?>
    <div class="row" id="flash-content">
        <div class="col s12">
            <div class="card-panel <?= $_SESSION['flash']['type'] == "success" ? "teal" : "red"; ?> lighten-3">
                <?= $_SESSION['flash']['text']; ?>
            </div>
        </div>
    </div>
<? endif; ?>

<?php

unset($_SESSION['flash']);

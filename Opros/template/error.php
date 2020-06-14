<? $this->title = $num . " - " . $message; ?>
<? $this->menuPoint = ""; ?>

<?= $this->render("inc/header"); ?>
<div class="container">
    <div class="row">
        <div class="col s12">
            <h1><?= $num; ?></h1>
            <p><?= $message; ?></p>
        </div>
    </div>
</div>

<?= $this->render("inc/footer"); ?>

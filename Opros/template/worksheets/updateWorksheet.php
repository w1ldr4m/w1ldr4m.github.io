<?php
/**
 * @var $worksheet Worksheets
 */
?>
<? $this->title = $worksheet->name . " - Редактировать анкету - Анкеты"; ?>
<? $this->breadcrumbs = [
    ['label' => "Анкеты", "url" => "?a=worksheets::index"],
    ['label' => "Редактировать анкету"],
];
?>
<?= $this->render("inc/header"); ?>
    <div class="container">
        <?= $this->render("inc/alert"); ?>
        <div class="row">
            <div class="col s12">
                <div class="left">
                    <h3><?= $worksheet->name; ?></h3>
                </div>
                <div class="right">
                    <a href="?a=worksheets::index" class="waves-effect waves-light btn-small">&larr; Вернуться</a>
                </div>
                <form action="?a=worksheets::update-worksheet&id=<?= $worksheet->id; ?>" method="post">
                    <div class="input-field col s12">
                        <input required
                               id="form-name"
                               type="text"
                               class="validate"
                               name="name"
                               value="<?= $worksheet->name; ?>">
                        <label for="form-name">Название</label>
                    </div>
                    <div class="input-field col s12">
                        <button type="submit" class="waves-effect waves-light btn-small blue">Обновить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?= $this->render("inc/footer"); ?>
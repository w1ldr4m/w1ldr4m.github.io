<? $this->title = "Добавить анкету - Анкеты"; ?>
<? $this->breadcrumbs = [
    ['label' => "Анкеты", "url" => "?a=worksheets::index"],
    ['label' => "Добавить анкету"],
];
?>
<?= $this->render("inc/header"); ?>
    <div class="container">
        <?= $this->render("inc/alert"); ?>
        <div class="row">
            <div class="col s12">
                <div class="left">
                    <h3><?= $this->title; ?></h3>
                </div>
                <div class="right">
                    <a href="?a=worksheets::index" class="waves-effect waves-light btn-small">&larr; Вернуться</a>
                </div>
                <div class="col s12">
                    <form action="?a=worksheets::create-worksheet" method="post">
                        <div class="input-field col s12">
                            <input required
                                   id="worksheet-name"
                                   type="text"
                                   class="validate"
                                   name="name"
                                   value="<?= $data['name']; ?>">
                            <label for="worksheet-name">Название<span style="color: red;"><sup>*</sup></span></label>
                        </div>
                        <br><br>
                        <div class="input-field col s12">
                        <p><span style="color: red;"><sup>*</sup></span> - Обязательно к заполнению.</p>
                            <button type="submit" class="waves-effect waves-light btn-small blue">Добавить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?= $this->render("inc/footer"); ?>
<? $this->title = "Настройки"; ?>
<? $this->breadcrumbs = [
    ['label' => "Настройки"],
];
?>
<?= $this->render("inc/header"); ?>
<div class="container">
    <?= $this->render("inc/alert"); ?>
    <div class="row">
        <div class="col s12">
            <div>
                <h3>Настройки</h3>
            </div>
            <form action="" method="post">
                <div class="input-field col s12">
                    <input required
                           id="worksheet-name"
                           type="text"
                           class="validate"
                           name="token_bot"
                           value="<?= $data->token_bot; ?>">
                    <label for="worksheet-name">Токен Бота<span style="color: red;"><sup>*</sup></span></label>
                </div>
                <div class="input-field col s12">
                    <input required
                           id="worksheet-name"
                           type="text"
                           class="validate"
                           name="username_bot"
                           value="<?= $data->username_bot; ?>">
                    <label for="worksheet-name">Username Бота<span style="color: red;"><sup>*</sup></span></label>
                </div>
                <div class="input-field col s12">
                    <input required
                           id="worksheet-name"
                           type="text"
                           class="validate"
                           name="admin_bot"
                           value="<?= $data->admin_bot; ?>">
                    <label for="worksheet-name">Админ Бота<span style="color: red;"><sup>*</sup></span></label>
                </div>

                <div class="input-field col s12">
                    <p><span style="color: red;"><sup>*</sup></span> - Обязательно к заполнению.</p>
                    <button type="submit" class="waves-effect waves-light btn-small blue">Сохранить изменения</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->render("inc/footer"); ?>

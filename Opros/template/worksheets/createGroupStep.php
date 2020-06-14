<?php
/**
 * @var $btns_step []
 */

?>

<? $this->title = "Добавить группу шагов";
$url = get_class($parent) == "WorksheetSteps" ? "step" : "worksheet";
if ($url == "step") {
    $this->breadcrumbs[] = ['label' => '...'];
}
$this->breadcrumbs[] = ['label' => $parent->name, "url" => "?a=worksheets::show-" . $url . "&id=" . $parent->id];
$this->breadcrumbs[] = ['label' => $this->title];
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
                <a href="?a=worksheets::show-<?= $url; ?>&id=<?= $parent->id; ?>"
                   class="waves-effect waves-light btn-small"
                   title="Вернуться">← Вернуться</a>
            </div>

            <div class="articles-form">
                <form action="" method="post">
                    <div class="row">
                        <div class="input-field col s12">
                            <select required name="parent_btn">
                                <? foreach ($btns_step as $key_btn => $value_btn): ?>
                                    <option value="<?= $key_btn; ?>"><?= $value_btn; ?></option>
                                <? endforeach; ?>
                            </select>
                            <label>Выводиться по кнопке родителя<span style="color: red;"><sup>*</sup></span></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <p><span style="color: red;"><sup>*</sup></span> - Обязательно к заполнению.</p>
                            <br>
                            <button type="submit" class="waves-effect waves-light btn-small blue">Добавить группу шагов</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
<?= $this->render("inc/footer"); ?>


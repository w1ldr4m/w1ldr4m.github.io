<?php

/**
 * @var $model WorksheetSteps
 * @var $steps WorksheetSteps[]
 */
?>

<? $this->title = "Просмотр шага"; ?>
<? $this->breadcrumbs = [
    ['label' => "Анкеты", "url" => "?a=worksheets::index"],
];
$parent = $model->getParent();
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
                <h3><?= $model->name; ?></h3>
            </div>
            <div class="right">
                <a href="?a=worksheets::show-<?= $url; ?>&id=<?= $parent->id; ?>"
                   class="waves-effect waves-light btn-small"
                   title="Вернуться">← Вернуться</a>
                <? if (count(WorksheetStepBtns::getAlsoBtnsByIdStep($model->id))): ?>
                    <a class='dropdown-trigger btn-small' href='#' data-target='drop_list_btn'>Добавить</a>
                    <ul id='drop_list_btn' class='dropdown-content'>
                        <li>
                            <a href="?a=worksheets::create-step&type=1&group=<?= $model->group_list; ?>&id=<?= $model->id; ?>">
                                Один шаг
                            </a>
                        </li>
                        <li>
                            <a href="?a=worksheets::create-group-step&id=<?= $model->id; ?>">
                                Группу
                            </a>
                        </li>
                    </ul>
                <? elseif ($model->group_list): ?>
                    <a href="?a=worksheets::create-step&type=1&group=<?= $model->group_list; ?>&id=<?= $model->id; ?>"
                       class="waves-effect waves-light btn-small">Добавить шаг</a>
                <? endif; ?>
            </div>
            <div id="formStep" class="grid-view">
                <table class="table">
                    <thead>
                    <tr>
                        <th width="5"><i class="material-icons">swap_vert</i></th>
                        <th>Название</th>
                        <th width="5"><i class="material-icons">edit</i></th>
                        <th width="5"><i class="material-icons">delete</i></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? if (count($steps)): ?>
                        <? foreach ($steps as $step): ?>
                            <tr data-key="<?= $step->id; ?>">
                                <td style="text-align: center;"><i class="material-icons handle">swap_vert</i></span>
                                </td>
                                <td>
                                    <? if (count($step->getBtns()) >= 2 || $step->group_list): ?>
                                        <a href="?a=worksheets::show-step&id=<?= $step->id; ?>">
                                            <?= Helper::encode($step->name); ?>
                                        </a>
                                    <? else: ?>
                                        <?= Helper::encode($step->name); ?>
                                    <? endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <? if ($step->group_list): ?>
                                        <b>G</b>
                                    <? else: ?>
                                        <a href="?a=worksheets::update-step&id=<?= $step->id; ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                    <? endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="#confirm_win"
                                       data-href="?a=worksheets::delete-step&id=<?= $step->id; ?>"
                                       class="confirm_win modal-trigger"
                                       data-title="Удалить шаг"
                                       data-body="Вы уверены что хотите удалить - <?= Helper::encode($step->name); ?>?">
                                        <i class="material-icons">delete</i>
                                    </a>
                                </td>
                            </tr>
                        <? endforeach; ?>
                    <? else: ?>
                        <tr>
                            <td colspan="4">
                                Нет записей
                            </td>
                        </tr>
                    <? endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->render("inc/footer"); ?>

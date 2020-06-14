<?php
/**
 * @var $worksheet Worksheets
 * @var $steps WorksheetSteps[]
 */
?>
<? $this->title = $worksheet->name . " - Просмотр анкеты - Анкеты"; ?>
<? $this->breadcrumbs = [
    ['label' => "Анкеты", "url" => "?a=worksheets::index"],
    ['label' => $worksheet->name],
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
                <a href="?a=worksheets::create-step&id=<?= $worksheet->id; ?>"
                   class="waves-effect waves-light btn-small">Добавить
                    шаг</a>
            </div>
            <div id="formStep">

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
                                <td style="text-align: center;"><i class="material-icons handle">swap_vert</i></td>
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
                                    <a href="?a=worksheets::update-step&id=<?= $step->id; ?>">
                                        <i class="material-icons">edit</i>
                                    </a>
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

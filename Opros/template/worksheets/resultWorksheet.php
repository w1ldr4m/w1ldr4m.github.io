<?php
/**
 * @var $worksheet Worksheets
 * @var $results Results[]
 */
?>
<? $this->title = $worksheet->name . " - Результаты - Анкеты"; ?>
<? $this->breadcrumbs = [
    ['label' => "Анкеты", "url" => "?a=worksheets::index"],
    ['label' => $worksheet->name],
]; ?>
<?= $this->render("inc/header"); ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <div class="left">
                <h3>Результаты "<?= $worksheet->name; ?>"</h3>
            </div>

            <div class="right">
                <a href="?a=worksheets::index" class="waves-effect waves-light btn-small">&larr; Вернуться</a>
            </div>

            <div id="w0" class="grid-view">
                <table class="table">
                    <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th>Статус</th>
                        <th style="text-align: center;">Дата оформления</th>
                        <th width="20%">Пользователь</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? if (count($results)): ?>
                        <? foreach ($results as $result): ?>
                            <tr>
                                <td>
                                    <a href="?a=worksheets::result-worksheet-show&id=<?= $result->id; ?>" title="Просмотр результата">
                                        <?= strtoupper($result->hash); ?>
                                    </a>
                                </td>
                                <td><? if($result->status == "new"): ?>Начат, но не закончен<? else: ?>Закончен и отправлен<? endif; ?></td>
                                <td style="text-align: center;"><?= date("d.m.Y в H:i", strtotime($result->create_at)); ?></td>
                                <td>
                                    <? $user = Users::find()->findByTelegramId($result->user_id); ?>
                                    <?= $user->getFullName(); ?><br>id:<?= $result->user_id; ?>
                                </td>
                            </tr>
                        <? endforeach; ?>
                    <? else: ?>
                        <tr>
                            <td colspan="4">Нет записей</td>
                        </tr>
                    <? endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->render("inc/footer"); ?>

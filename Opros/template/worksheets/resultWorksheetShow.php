<?php
/**
 * @var $result Results
 * @var $items ResultItems[]
 */
?>
<? $worksheet = Worksheets::findById($result->worksheet_id); ?>
<? $this->title = "Результаты - " . $worksheet->name . " - Конструктор анкет"; ?>
<? $this->breadcrumbs = [
    ['label' => "Анкеты", "url" => "?a=worksheets::index"],
    ['label' => "Результаты - " . $worksheet->name, "url" => "?a=worksheets::result-worksheet&id=" . $worksheet->id],
    ['label' => "Просмотр результата"],
]; ?>
<?= $this->render("inc/header"); ?>
<div class="container">
    <div class="row">
        <div class="col s12">
            <div class="left">
                <h4>Просмотр результата #<?= strtoupper($result->hash); ?>
                    от <?= date("d.m.Y H:i", strtotime($result->create_at)); ?></h4>
            </div>
            <div class="right">
                <a href="?a=worksheets::result-worksheet&id=<?= $worksheet->id; ?>" class="btn btn-primary btn-sm">&larr;
                    Вернуться</a>
            </div>
            <div class="row">
                <div class="col s12">
                    <? $user = Users::find()->findByTelegramId($result->user_id); ?>
                    <b>Пользователь:</b> <?= $user->getFullName(); ?> (id:<?= $result->user_id; ?>)<br>
                    <b>Статус:</b> <? if ($result->status == "new"): ?>Начат, но не закончен<? else: ?>Закончен и отправлен<? endif; ?>
                    <br><br>
                    <b>Детали заказа</b><br>
                    <? if (count($items)): ?>
                        <? foreach ($items as $item): ?>
                            <b><?= $item->preview; ?>:</b>
                            <? if ($item->type == "text"): ?>
                                <?= $item->body; ?>
                            <? else: ?>
                                <a href="tg://resolve?domain=<?= Settings::param("username_bot"); ?>&amp;start=mvr<?= $item->hash; ?>"><?= Files::$typeMedia[$item->type]; ?></a>
                            <? endif; ?>
                            <br>
                        <? endforeach; ?>
                    <? else: ?>
                        Нет данных
                    <? endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->render("inc/footer"); ?>
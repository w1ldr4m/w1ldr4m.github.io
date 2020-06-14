<? $this->title = "Анкеты"; ?>
<? $this->breadcrumbs = [
    ['label' => "Анкеты"],
];
?>
<?= $this->render("inc/header"); ?>
<div class="container">
    <?= $this->render("inc/alert"); ?>
    <div class="row">
        <div class="col s12">
            <div class="left"><h3>Анкеты</h3></div>
            <div class="right">
                <a href="?a=worksheets::create-worksheet" class="waves-effect waves-light btn-small">Добавить анкету</a>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>Название</th>
                    <th width="5"><i class="material-icons">list</i></th>
                    <th width="5"><i class="material-icons">edit</i></th>
                    <th width="5"><i class="material-icons">visibility</i></th>
                    <th width="5"><i class="material-icons">delete</i></th>
                </tr>
                </thead>
                <tbody>
                <? $nameBot = Settings::param("username_bot"); ?>
                <? if (count($worksheets)): ?>
                    <? foreach ($worksheets as $worksheet): ?>
                        <tr>
                            <td>
                                <a href="?a=worksheets::show-worksheet&id=<?= $worksheet->id; ?>">
                                    <?= Helper::encode($worksheet->name); ?>
                                </a><br>
                                <small>
                                    Ссылка на форму:<br>
                                    <a href="tg://resolve?domain=<?= $nameBot; ?>&amp;start=worksheet<?= $worksheet->hash; ?>"
                                       target="_blank">
                                        https://t.me/<?= $nameBot; ?>?start=worksheet<?= $worksheet->hash; ?>
                                    </a>
                                </small>
                            </td>
                            <td width="5">
                                <a href="?a=worksheets::result-worksheet&id=<?= $worksheet->id; ?>">
                                    <i class="material-icons">list</i>
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <a href="?a=worksheets::update-worksheet&id=<?= $worksheet->id; ?>">
                                    <i class="material-icons">edit</i>
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <a href="?a=worksheets::status-worksheet&id=<?= $worksheet->id; ?>&type=<?= (int)!$worksheet->hide; ?>"
                                   title="Сменить статус"
                                   data-confirm="Вы уверены, что хотите сменить статус?"
                                   class="confirm-modal">
                                    <? if ($worksheet->hide): ?>
                                        <i class="material-icons">visibility_off</i>
                                    <? else: ?>
                                        <i class="material-icons">visibility</i>
                                    <? endif; ?>
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <a href="#confirm_win"
                                   class="confirm_win modal-trigger"
                                   data-title="Удалить анкету"
                                   data-body="Вы уверены что хотите удалить - <?= Helper::encode($worksheet->name); ?>?"
                                   data-href="?a=worksheets::delete-worksheet&id=<?= $worksheet->id; ?>">
                                    <i class="material-icons">delete</i>
                                </a>
                            </td>
                        </tr>
                    <? endforeach; ?>
                <? else: ?>
                    <tr>
                        <td colspan="5">
                            Нет записей
                        </td>
                    </tr>
                <? endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->render("inc/footer"); ?>

<?php
/**
 * @var $users Users[]
 */
?>

<? $this->title = "Пользователи"; ?>
<? $this->breadcrumbs = [
    ['label' => "Пользователи"],
];
?>

<?= $this->render("inc/header"); ?>
<div class="container">
    <?= $this->render("inc/alert"); ?>
    <div class="row">
        <div class="col s12">
            <div>
                <h3>Пользователи</h3>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>Пользователь</th>
                    <th width="5" style="text-align: center;">Первый вход</th>
                    <th width="5" style="text-align: center;">Активность</th>
                    <th width="20" style="text-align: center;">Бан</th>
                </tr>
                </thead>
                <tbody>
                <? if (count($users)): ?>
                    <? foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <?= Helper::encode($user->getFullName()); ?><br>
                                id: <?= $user->telegram_id; ?>
                                <? if ($user->username): ?><br>@<?= $user->username; ?><? endif; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle;"><?= date("d.m.Y H:i", strtotime($user->create_at)); ?></td>
                            <td style="text-align: center; vertical-align: middle;"><?= date("d.m.Y H:i", strtotime($user->update_at)); ?></td>
                            <td style="text-align: center; vertical-align: middle;">
                                <a href="?a=users::blocked&id=<?= $user->id; ?>&type=<?= (int)!$user->ban; ?>"
                                   title="Изменить блокирование">
                                    <span class="new badge <? if ($user->ban): ?>red<? else: ?>teal<? endif; ?>"><? if ($user->ban): ?>да<? else: ?>нет<? endif; ?></span>
                                </a>
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

<?= $this->render("inc/footer"); ?>

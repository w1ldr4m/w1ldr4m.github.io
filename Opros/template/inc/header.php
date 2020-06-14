<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->title; ?></title>
    <link rel="icon" type="image/png" href="./favicon.png" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="./source/css/materialize.min.css" rel="stylesheet" media="screen,projection">
    <link href="./source/css/index.css" rel="stylesheet">
</head>
<body>
<header>
    <div class="row">
        <nav>
            <div class="col s12">
                <div class="nav-wrapper">
                    <a href="#!" class="brand-logo">AdminConsole</a>
                    <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                    <ul class="right hide-on-med-and-down">
                        <li <? if ($this->menuPoint == "users"): ?>class="active"<? endif; ?>><a
                                    href="?a=users::index">Пользователи</a></li>
                        <li <? if ($this->menuPoint == "worksheets"): ?>class="active"<? endif; ?>><a
                                    href="?a=worksheets::index">Анкеты</a></li>
                        <li <? if ($this->menuPoint == "settings"): ?>class="active"<? endif; ?>><a
                                    href="?a=settings::index">Настройки</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <?= $this->render("inc/breadcrumbs"); ?>

        <ul class="sidenav" id="mobile-demo">
            <li <? if ($this->menuPoint == "users"): ?>class="active"<? endif; ?>><a
                        href="?a=users::index">Пользователи</a>
            </li>
            <li <? if ($this->menuPoint == "worksheets"): ?>class="active"<? endif; ?>><a
                        href="?a=worksheets::index">Анкеты</a></li>
            <li <? if ($this->menuPoint == "settings"): ?>class="active"<? endif; ?>><a href="?a=settings::index">Настройки</a>
            </li>
        </ul>
    </div>
</header>
<main>

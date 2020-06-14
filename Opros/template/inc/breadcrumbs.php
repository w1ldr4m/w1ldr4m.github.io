<? if (count($this->breadcrumbs)): ?>
    <nav>
        <div class="nav-wrapper">
            <div class="col s12">
                <a href="?a=index" class="breadcrumb">Главная</a>
                <? foreach ($this->breadcrumbs as $breadcrumb): ?>
                    <? if (isset($breadcrumb['url'])): ?>
                        <a href="<?= $breadcrumb['url']; ?>" class="breadcrumb"><?= $breadcrumb['label']; ?></a>
                    <? else: ?>
                        <span class="breadcrumb"><?= $breadcrumb['label']; ?></span>
                    <? endif; ?>
                <? endforeach; ?>
            </div>
        </div>
    </nav>
<? endif; ?>

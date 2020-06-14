<? $this->title = "Добавить шаг"; ?>
<? $this->breadcrumbs = [
    ['label' => "Анкеты", "url" => "?a=worksheets::index"],
];
$url = get_class($parent) == "WorksheetSteps" ? "step" : "worksheet";
if ($url == "step") {
    $this->breadcrumbs[] = [
        'label' => '...'
    ];
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

            <?= $this->render("worksheets/_form_step", [
                "update" => false,
                "data" => $data,
                'buttons' => $buttons,
                'file' => $file,
                'btns_step' => $btns_step,
                "url" => $url,
                "parent" => $parent
            ]); ?>

        </div>
    </div>
</div>
<?= $this->render("inc/footer"); ?>


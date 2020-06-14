<?php

class SettingsController extends Controller
{
    public $menuPoint = "settings";

    public function index()
    {
        $model = Settings::model();
        if ($_POST) {
            $model->token_bot = $_POST['token_bot'];
            $model->username_bot = $_POST['username_bot'];
            $model->admin_bot = $_POST['admin_bot'];
            if ($model->save()) {
                $this->setFlash('success', 'Настройки успешно сохранены');
            } else {
                $this->setFlash('danger', 'Не удалось сохранить настройки. Попробуйте позже.');
            }
        }
        echo $this->render("settings/index", [
            'data' => $model
        ]);
    }
}
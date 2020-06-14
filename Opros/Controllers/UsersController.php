<?php

class UsersController extends Controller
{
    public $menuPoint = "users";

    /**
     *
     */
    public function index()
    {
        echo $this->render("users/index", [
            'users' => Users::find()->all(" ORDER BY update_at DESC ")
        ]);
    }

    /**
     *
     */
    public function blocked()
    {
        $user = Users::findById((int)$_GET['id']);
        if($user) {
            if($user->setBan((int)$_GET['type'])) {
                $this->setFlash("success", "Доступ пользователю изменен");
            } else {
                $this->setFlash("danger", "Ошибка при изменении доступа пользователю");
            }
            $this->goBack();
        } else {
            $this->error("404", "Пользователь не найден");
        }
    }


}
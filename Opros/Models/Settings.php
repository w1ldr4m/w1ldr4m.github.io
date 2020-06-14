<?php

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string $username_bot
 * @property string $token_bot
 * @property int $admin_bot
 */
class Settings extends Model
{
    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return 'settings';
    }

    /**
     * @return bool|Settings
     */
    public static function model()
    {
        $model = Settings::find()->one(" ORDER BY id DESC LIMIT 1 ");
        if(!$model) {
            $model = Settings::add();
        }
        return $model;
    }

    /**
     * @return bool|Settings
     */
    public static function add()
    {
        $model = new self();

        $model->token_bot = NULL;
        $model->username_bot = NULL;
        $model->admin_bot = NULL;

        return $model->save() ? $model : false;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public static function param($name)
    {
        $model = self::model();
        return isset($model->$name)
            ? $model->$name
            : null;
    }
}

<?php

/**
 * This is the model class for table "statement_items".
 *
 * @property int $id
 * @property int $result_id
 * @property int $step_id
 * @property int $user_id
 * @property string $preview
 * @property string $body
 * @property string $type
 * @property string $file_id
 * @property string $hash
 * @property int $relevant
 */
class ResultItems extends Model
{
    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return 'result_items';
    }

    /** Берем последний заполеный ответ
     * @param $user_id
     * @param $result_id
     * @param $step_id
     * @return mixed
     */
    public static function answer($user_id, $result_id, $step_id)
    {
        return self::find()->one(" WHERE user_id = :user_id AND result_id = :result_id AND step_id = :step_id ORDER BY id DESC LIMIT 1 ",
            [
                ':user_id' => (int)$user_id,
                ':result_id' => (int)$result_id,
                ':step_id' => (int)$step_id
            ]);
    }

    /**
     * @param $result_id
     * @param $step_id
     * @param $user_id
     * @param $preview
     * @param $type
     * @param $body
     * @param $file_id
     * @return bool
     * @throws \Throwable
     */
    public static function add($result_id, $step_id, $user_id, $preview, $type, $body, $file_id)
    {
        self::unRelAllByStepProfile($result_id, $step_id);

        $model = new self();

        $model->result_id = $result_id;
        $model->step_id = $step_id;
        $model->user_id = $user_id;
        $model->hash = mb_strcut(md5(rand(0, 1000000)), 0, 16);
        $model->preview = $preview;
        $model->type = $type;
        $model->body = $body;
        $model->file_id = $file_id;
        $model->relevant = 1;

        return $model->save();
    }

    /**
     * @param $result_id
     * @param $step_id
     */
    public static function unRelAllByStepProfile($result_id, $step_id)
    {
        $models = self::find()->all(" WHERE result_id = :result_id AND step_id = :step_id AND relevant = 1 ",
            [
                ':result_id' => (int)$result_id,
                ':step_id' => (int)$step_id
            ]);
        if (count($models)) {
            foreach ($models as $model) {
                $model->delete();
            }
        }
    }

    /**
     * @param $user_id
     * @param $result_id
     * @return array|self[]
     */
    public static function getAllAnswer($user_id, $result_id)
    {
        return self::find()->all(" WHERE result_id = :result_id AND user_id = :user_id AND relevant = 1 ORDER BY id ASC ",
            [
                ':result_id' => (int)$result_id,
                ':user_id' => (int)$user_id
            ]);
    }

    /** Получаем строки где есть файлы
     * @param $user_id
     * @param $result_id
     * @return array|self[]
     */
    public static function getAllMediaAnswer($user_id, $result_id)
    {
        return self::find()->all(" WHERE result_id = :result_id AND user_id = :user_id AND relevant = 1 AND type NOT IN ('text','location') ORDER BY id ASC ",
            [
                ':result_id' => (int)$result_id,
                ':user_id' => (int)$user_id
            ]);
    }

    /** Получаем строки где есть файлы
     * @param $user_id
     * @param $result_id
     * @return int|string
     */
    public static function getAllMediaAnswerCount($user_id, $result_id)
    {
        return self::find()->count(" result_id = :result_id AND user_id = :user_id AND relevant = 1 AND type NOT IN ('text','location') ",
            [
                ':result_id' => (int)$result_id,
                ':user_id' => (int)$user_id
            ]);
    }

    /** Получаем файл в бот
     * @param $id_user
     * @param $result_id
     * @param $current
     * @return bool|self
     */
    public static function getFileToView($id_user, $result_id, $current)
    {
        return self::find()->one(" WHERE user_id = :user_id AND result_id = :result_id AND relevant = 1 AND type NOT IN ('text','location') ORDER BY id ASC LIMIT " . $current . ", 1 ",
            [
                ':user_id' => (int)$id_user,
                ':result_id' => (int)$result_id,
            ]);
    }

    /**
     * @param $user_id
     * @param $result_id
     * @param $answer_id
     * @return array|self|null
     */
    public static function prevStep($user_id, $result_id, $answer_id)
    {
        return self::find()->one(" WHERE user_id = :user_id AND result_id = :result_id AND id < :answer_id AND relevant = 1 ORDER BY id DESC LIMIT 1 ",
            [
                ':user_id' => (int)$user_id,
                ':result_id' => (int)$result_id,
                ':answer_id' => (int)$answer_id
            ]);
    }

    /**
     * @param $user_id
     * @param $result_id
     * @param $answer_id
     * @return array|self|null
     */
    public static function nextStep($user_id, $result_id, $answer_id)
    {
        return self::find()->one(" WHERE user_id = :user_id AND result_id = :result_id AND id > :answer_id AND relevant = 0 ORDER BY id ASC LIMIT 1",
            [
                ':user_id' => (int)$user_id,
                ':result_id' => (int)$result_id,
                ':answer_id' => (int)$answer_id
            ]);
    }

    /**
     * @param $user_id
     * @param $order_id
     * @return bool|self
     */
    public static function lastStep($user_id, $result_id)
    {
        return self::find()->one(" WHERE user_id = :user_id AND result_id = :result_id AND relevant = 1 ORDER BY id DESC LIMIT 1 ",
            [
                ':user_id' => (int)$user_id,
                ':result_id' => (int)$result_id
            ]);
    }

    /** деактуалим все ответы что ниже по id указанного ответа
     * @param $user_id
     * @param $result_id
     * @param $id
     * @return bool
     */
    public static function relevantAllNextAnswers($user_id, $result_id, $id)
    {
        $model = new self();
        $update = $model->pdo()->prepare("UPDATE " . $model->getFullTableName() . " SET relevant = 0 WHERE user_id = :user_id AND result_id = :result_id AND id >= :id  AND relevant = 1 ");
        return $update->execute([
            ':user_id' => (int)$user_id,
            ':result_id' => (int)$result_id,
            ':id' => (int)$id,
        ]);
    }

    /** деактуалим все ответы заявки
     * @param $user_id
     * @param $result_id
     * @return bool
     */
    public static function relevantAllAnswers($user_id, $result_id)
    {
        $model = new self();
        $sql_ = "UPDATE " . $model->getFullTableName() . " SET relevant = 0 WHERE user_id = :user_id AND result_id = :result_id AND relevant = 1 ";
        $update = $model->pdo()->prepare($sql_);
        $data = [
            ':user_id' => (int)$user_id,
            ':result_id' => (int)$result_id,
        ];
        if (Config::debug('mysql')) {
            FileLog::set($model->sql_debug($sql_, $data), "mysql");
        }
        return $update->execute($data);
    }

    /**
     * @param $hash
     * @return array|self
     */
    public static function findByHash($hash)
    {
        return self::find()->one(" WHERE hash = :hash ", ['hash' => $hash]);
    }

}

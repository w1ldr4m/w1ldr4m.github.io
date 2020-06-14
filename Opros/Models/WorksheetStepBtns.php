<?php
/**
 * This is the model class for table "worksheet_step_btns".
 *
 * @property int $id
 * @property int $parent_worksheet
 * @property int $parent_step
 * @property string $name
 * @property string $sort
 */
class WorksheetStepBtns extends Model
{
    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return 'worksheet_step_btns';
    }

    /**
     * @param $sort
     * @return bool
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
        return $this->save();
    }

    /**
     * @param $parent
     * @param $name
     * @return array|self|null
     */
    public static function findByName($parent, $name)
    {
        return self::find()->one(" WHERE parent_step = :parent_step AND name = :name ",
            [
                ':parent_step' => $parent,
                ':name' => $name
            ]
        );
    }

    /** Получаем все кнопки по id экрана-шага
     * @param $id
     * @return self[]|bool
     */
    public static function getAllStepBtn($id)
    {
        return self::find()->all(" WHERE parent_step = :parent_step ORDER BY sort ASC ",
            [
                'parent_step' => (int)$id
            ]
        );
    }

    /** Преобразуем в массив
     * @param $id
     * @return array
     */
    public static function getAllStepBtnArr($id)
    {
        $btn = self::getAllStepBtn($id);
        return count($btn) ? Helper::map($btn, 'id', 'name') : [];
    }

    /** какие кнопки у шага
     * @param $id
     * @return array
     */
    public static function getAlsoBtnsByIdStep($id)
    {
        $steps = WorksheetSteps::find()->all(" WHERE parent_step = :id_step ",
            [
                'id_step' => (int)$id
            ]
        );
        $also = Helper::map($steps, 'parent_btn', 'name');
        return array_diff_key(self::getAllStepBtnArr($id), $also);
    }

    /**
     * @return mixed
     */
    public function getChild()
    {
        return $this->hasOne('WorksheetSteps', ['parent_btn' => 'id']);
    }

    /**
     * удаляем прикрепленный step
     */
    public function deleteSelf()
    {
        $child = $this->getChild();
        if ($child) {
            $child->deleteSelf();
        }
        return $this->delete();
    }

}

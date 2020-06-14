<?php

/**
 * This is the model class for table "worksheet_steps".
 *
 * @property int $id
 * @property string $name
 * @property int $parent_worksheet
 * @property int $parent_step
 * @property int $parent_btn
 * @property string $user_body
 * @property string $preview_body
 * @property string $type
 * @property string $file_id
 * @property int $sort
 * @property int $group_list
 * @property string $expect
 */
class WorksheetSteps extends Model
{
    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return 'worksheet_steps';
    }

    /**
     * @return mixed self[]
     */
    public function getChildren()
    {
        return $this->hasMany('WorksheetSteps', ['parent_worksheet' => 'id']);
    }

    /**
     * @return mixed WorksheetStepBtns[]
     */
    public function getBtns()
    {
        return $this->hasMany('WorksheetStepBtns', ['parent_step' => 'id'], " ORDER BY sort ASC ");
    }

    /**
     * @return mixed
     */
    public function innerOnBtnClickCount() {
        return self::find()
            ->count(" parent_step = :parent_step AND parent_btn IS NOT NULL ",
                [
                    ":parent_step" => $this->id
                ]
            );
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        if (is_null($this->parent_step)) {
            return $this->hasOne('Worksheets', ['id' => 'parent_worksheet']);
        } else {
            return $this->hasOne('WorksheetSteps', ['id' => 'parent_step']);
        }
    }

    /**
     * @param $worksheet_id
     * @param null $step_id
     * @return mixed
     */
    public static function getMaxSort($worksheet_id, $step_id = null)
    {
        $sql = " parent_worksheet = :parent_worksheet ";
        $data = [':parent_worksheet' => (int)$worksheet_id];
        if(is_null($step_id)) {
            $sql .= " AND parent_step IS NULL ";
        } else {
            $sql .= " AND parent_step = :parent_step ";
            $data[':parent_step'] = $step_id;
        }
        return self::find()->max('sort', $sql, $data);
    }

    /**
     * @param $worksheet_id
     * @return array|bool
     */
    public static function allByWorksheetId($worksheet_id)
    {
        return self::find()->all(" WHERE parent_worksheet = :worksheet_id AND parent_step IS NULL ORDER BY sort ASC ",
            [
                'worksheet_id' => (int)$worksheet_id
            ]
        );
    }

    /**
     * @param $step_id
     * @return array|bool
     */
    public static function allByStepId($step_id)
    {
        return self::find()->all(" WHERE parent_step = :step_id ORDER BY sort ASC ",
            [
                'step_id' => (int)$step_id
            ]
        );
    }

    /**
     * @return mixed|null
     */
    public function getFile()
    {
        if (!empty($this->file_id)) {
            return $this->hasOne('Files', ['key_name' => 'file_id']);
        }
        return null;
    }

    /**
     * @return bool
     */
    public function deleteSelf()
    {
        /**
         * @var $btns WorksheetStepBtns[]
         */
        $btns = $this->getBtns();
        if (count($btns)) {
            foreach ($btns as $btn) {
                $btn->deleteSelf();
            }
        }

        /**
         * @var $models self[]
         */
        $models = $this->getChildren();
        if (count($models)) {
            foreach ($models as $item) {
                $item->deleteSelf();
            }
        }

        $file = $this->getFile();
        if ($file) {
            $file->delete();
        }

        return $this->delete();
    }

    /**
     * @param $step_id
     * @return array|bool|self|null
     */
    public static function getNextStep($step_id)
    {
        $step = self::findById($step_id);
        if ($step) {
            $parent = $step->getParent();
            if ($step->parentType() == "worksheet") {
                return self::find()->one(" WHERE parent_worksheet = :parent_worksheet AND parent_step IS NULL AND sort > :sort ORDER BY sort ASC LIMIT 1 ",
                    [
                        'parent_worksheet' => $step->parent_worksheet,
                        'sort' => $step->sort
                    ]);
            } else {
                if($parent->group_list) {
                    $nextStep = self::find()->one(" WHERE parent_step = :parent_step AND sort > :sort ORDER BY sort ASC LIMIT 1 ",
                        [
                            'parent_step' => $step->parent_step,
                            'sort' => $step->sort
                        ]);
                    if ($nextStep) {
                        return $nextStep;
                    }
                }
                return self::getNextStep($parent->id);
            }
        }
        return false;
    }

    /**
     * @param $worksheet_id
     * @return array|self|null
     */
    public static function firstWorksheetStep($worksheet_id)
    {
        return self::find()->one(" WHERE parent_worksheet = :parent_worksheet AND parent_step IS NULL ORDER BY sort ASC, id ASC LIMIT 1 ",
            [
                'parent_worksheet' => $worksheet_id
            ]
        );
    }

    /**
     * @param null $step_id
     * @return array|self|null
     */
    public static function firstStep($step_id)
    {
        return self::find()->one(" WHERE parent_step = :parent_step ORDER BY sort ASC, id ASC LIMIT 1 ",
            [
                ':parent_step' => $step_id
            ]
        );
    }

    /** Получаем тип родителя
     * @return string
     */
    public function parentType()
    {
        return $this->parent_step ? "step" : "worksheet";
    }

    /** Проверяем родитель - шаг
     * @return bool
     */
    public function isParentStep()
    {
        return $this->parentType() == "step";
    }

    /** Проверяем родитель - анкета
     * @return bool
     */
    public function isParentWorksheet()
    {
        return $this->parentType() == "worksheet";
    }

    /** Проверяем шаг находиться в группе или нет
     * @return bool
     */
    public function isInGroup()
    {
        $parent = $this->getParent();
        if ($parent) {
            return $parent->group_list || $this->isParentWorksheet();
        }
        return false;
    }
}
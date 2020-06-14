<?php

/**
 * This is the model class for table "worksheets".
 *
 * @property int $id
 * @property string $name
 * @property string $hash
 */
class Worksheets extends Model
{
    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return 'worksheets';
    }

    /**
     * @return WorksheetSteps[]
     */
    public function getChildren()
    {
        return $this->hasMany('WorksheetSteps', ['parent_worksheet' => 'id']);
    }

    /**
     *
     */
    public function deleteChildren()
    {
        /**
         * @var $models WorksheetSteps[]
         */
        $models = $this->getChildren();
        if (count($models)) {
            foreach ($models as $item) {
                $item->deleteSelf();
            }
        }
    }

    /**
     * @param $name
     * @return bool|mixed
     */
    public static function add($name)
    {
        $model = new self();
        $model->name = $name;
        $model->hash = mb_strcut(md5(rand(0, 1000000)), 0, 8);
        return $model->save();
    }

    /**
     * @param $hide
     * @return array|self[]
     */
    public static function getAllForms($hide)
    {
        return self::find()->all(" WHERE hide = :hide ", [":hide" => $hide]);
    }

    /**
     * @param $hash
     * @return bool|self
     */
    public static function findByHash($hash)
    {
        return self::find()->one(" WHERE hash = :hash LIMIT 1 ", [":hash" => $hash]);
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->hasMany("Results", ["worksheet_id" => "id"], " ORDER BY create_at DESC ");
    }

}

<?php

/**
 * This is the model class for table "results".
 *
 * @property int $id
 * @property int $user_id
 * @property int $worksheet_id
 * @property string $status
 * @property string $hash
 * @property string $create_at
 */
class Results extends Model
{
    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return 'results';
    }

    /**
     * @param $field
     * @param $value
     * @return bool
     */
    public function setParam($field, $value)
    {
        $this->$field = $value;
        return $this->save();
    }

    /**
     * @return ResultItems[]|null
     */
    public function getItems()
    {
        return $this->hasMany('ResultItems', ['result_id' => 'id'], " AND relevant = 1 ORDER BY id ASC ");
    }

    /**
     * @return false|int
     */
    public function delSelf()
    {
        return $this->setParam("deleted", 1);
    }

    /**
     * @param $status
     * @return bool
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this->save();
    }

}

<?php

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $type
 * @property string $file_id
 * @property string $key_name
 * @property string $parent
 */
class Files extends Model
{
    public static $typeMedia = [
        "document" => "Документ",
        "photo" => "Изображение",
        "video" => "Видео-файл",
        "location" => "Геолокация"
    ];

    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return 'files';
    }

    /**
     * @param $key
     * @param null $parent
     * @return bool|mixed
     */
    public static function findByKey($key, $parent = null)
    {
        $sql = " WHERE key_name = :key ";
        $data[':key'] = $key;
        if(!is_null($parent)) {
            $sql .= " AND parent = :parent ";
            $data[':parent'] = $parent;
        }
        return self::find()->one($sql, $data);
    }

    /**
     * @param $file_id
     * @param $parent
     * @return bool|mixed
     */
    public static function findByFileId($file_id, $parent)
    {
        return self::find()->one(" WHERE file_id = :file_id  AND parent = :parent ",
            [
                'file_id' => $file_id,
                ':parent' => $parent
            ]
        );
    }
}

<?php

/**
 * Class Model
 * @property int $id
 */
abstract class Model
{
    abstract public function getTable();

    /** Получаем соединение к базе
     * @return PDO
     */
    public function pdo()
    {
        return Db::getInstance()->connect();
    }

    /** Название таблицы с префиксом
     * @return string
     */
    protected function getFullTableName()
    {
        return Config::$db_table_prefix . $this->getTable();
    }

    /** Сохраняем данные
     * @return bool|mixed
     */
    public function save()
    {
        // определяем что за сохранение
        if (empty($this->id)) {
            // добавление
            return $this->insert();
        } else {
            // обновление
            return $this->update();
        }
    }

    /** Добавляем объект
     * @return bool|mixed
     */
    protected function insert()
    {
        // получаем ключи
        $data = $this->getDataObj();
        // готовим запрос
        $sql = "INSERT INTO " . $this->getFullTableName() . " SET " .
            Db::pdoSet(array_keys($data), $values, $data);
        // прогоняем через пдо
        $insert = $this->pdo()->prepare($sql);
        // логируем
        if (Config::debug('mysql')) {
            FileLog::set($this->sql_debug($sql, $data), "mysql");
        }
        // возвращаем результат - новую модель или false
        return $insert->execute($values)
            ? static::getLastModel()
            : false;
    }

    /** Получаем последнюю запись
     * @return bool|mixed
     */
    public static function getLastModel()
    {
        return static::find()->one(" ORDER BY id DESC LIMIT 1 ");
    }

    /**
     * @param $class
     * @param $array
     * @param string $sql
     * @param array $data
     * @return mixed
     */
    public function hasMany($class, $array, $sql = "", $data = [])
    {
        $key = array_keys($array)[0];
        $val = $array[$key];
        $subData[":" . $val] = $this->$val;
        return $class::find()->all(" WHERE " . $key . " = :" . $val . $sql, array_merge($subData, $data));
    }

    /**
     * @param $class
     * @param $array
     * @param string $sql
     * @param array $data
     * @return mixed
     */
    public function hasOne($class, $array, $sql = "", $data = [])
    {
        $key = array_keys($array)[0];
        $val = $array[$key];
        $subData[":" . $val] = $this->$val;
        return $class::find()->one(" WHERE " . $key . " = :" . $val . $sql, array_merge($subData, $data));
    }

    /** Обновляем объект
     * @return $this|bool
     */
    protected function update()
    {
        // получаем старые и новые значения
        $new_data = $this->getDataObj();
        $old_data = $this->findById($this->id);
        // id добавляем по умолчанию
        $data['id'] = $new_data['id'];
        // удаляем из массива данные которые не поменялись
        foreach ($new_data as $key => $value) {
            if ($old_data->$key !== $new_data[$key]) {
                $data[$key] = $value;
            }
        }
        // Если есть данные которые нужно менять
        if (count($data) > 1) {
            // готовим запрос
            $sql = "UPDATE " . $this->getFullTableName() . " SET " .
                Db::pdoSet(array_keys($data), $values, $data)
                . " WHERE id = :id";
            // логируем
            if (Config::debug('mysql')) {
                FileLog::set($this->sql_debug($sql, $data), "mysql");
            }
            // прогоняем через пдо
            $update = $this->pdo()->prepare($sql);
            // возвращаем результат - текущий объект или false
            return $update->execute($values)
                ? $this
                : false;
        } else {
            // если нечего обновлять то вернем текущий объект
            return $this;
        }
    }

    /** Получаем текущие свойства (ключи) объекта
     * @return array
     */
    protected function getDataObj()
    {
        return get_object_vars($this);
    }

    /** Получаем объект для поиска
     * @return static
     */
    public static function find()
    {
        return new static();
    }

    /** Поиск по id
     * @param $id
     * @return object
     */
    public static function findById($id)
    {
        return static::find()->one(" WHERE id = :id LIMIT 1", [':id' => (int)$id]);
    }

    /** Получаем объект из базы
     * @param $sql
     * @param array $data
     * @return bool|static
     */
    public function one($sql, $data = [])
    {
        $item = $this->findPre($sql, $data);
        return $item->rowCount() > 0
            ? $item->fetch()
            : false;
    }

    /** Получаем объекты из базы
     * @param $sql
     * @param array $data
     * @return static[]|bool
     */
    public function all($sql = "", $data = [])
    {
        $item = $this->findPre($sql, $data);
        return $item->rowCount() > 0
            ? $item->fetchAll()
            : [];
    }

    /** Готовим для запроса find...
     * @param $sql
     * @param array $data
     * @return bool|PDOStatement
     */
    protected function findPre($sql, $data = [])
    {
        // готовим запрос
        $sql_ = "SELECT * FROM " . $this->getFullTableName() . $sql;
        // логируем
        if (Config::debug('mysql')) {
            FileLog::set($this->sql_debug($sql_, $data), "mysql");
        }
        // прогоняем через пдо
        $item = $this->pdo()->prepare($sql_);
        // выполняем запрос
        $item->execute($data);
        // указываем что мы хотим получить на выходе - объекты
        $item->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        // возвращаем результат
        return $item;
    }

    /** Удаляем все по условию
     * @param $sql
     * @param array $data
     * @return bool
     */
    public function deleteAll($sql, $data = [])
    {
        // готовим запрос
        $sql_ = "DELETE FROM " . $this->getFullTableName() . $sql;
        // логируем
        if (Config::debug('mysql')) {
            FileLog::set($this->sql_debug($sql_, $data), "mysql");
        }
        // прогоняем через пдо
        $item = $this->pdo()->prepare($sql_);
        // возвращаем результат выполнения запроса
        return $item->execute($data);
    }

    /** Удалить объект
     * @return bool
     */
    public function delete()
    {
        // готовим массив с данными
        $data = ['id' => $this->id];
        // готовим запрос
        $sql = "DELETE FROM " . $this->getFullTableName() . " WHERE id = :id";
        // логируем
        if (Config::debug('mysql')) {
            FileLog::set($this->sql_debug($sql, $data), "mysql");
        }
        // прогоняем через пдо
        $item = $this->pdo()->prepare($sql);
        // возвращаем результат выполнения запроса
        return $item->execute($data);
    }

    /** Получаем количество
     * @param $sql
     * @param array $data
     * @return mixed
     */
    public function count($sql, $data = [])
    {
        // готовим запрос
        $sql_ = "SELECT COUNT(*) FROM " . $this->getFullTableName() . " WHERE " . $sql;
        // логируем
        if (Config::debug('mysql')) {
            FileLog::set($this->sql_debug($sql_, $data), "mysql");
        }
        // прогоняем через пдо
        $item = $this->pdo()->prepare($sql_);
        // выполняем запрос
        $item->execute($data);
        // получаем результат
        return $item->fetchColumn();
    }

    /**
     * @param $column
     * @param $sql
     * @param array $data
     * @return mixed
     */
    public function max($column, $sql, $data = [])
    {
        // готовим запрос
        $sql_ = "SELECT MAX(" . $column . ") FROM " . $this->getFullTableName() . " WHERE " . $sql;
        // логируем
        if (Config::debug('mysql')) {
            FileLog::set($this->sql_debug($sql_, $data), "mysql");
        }
        // прогоняем через пдо
        $item = $this->pdo()->prepare($sql_);
        // выполняем запрос
        $item->execute($data);
        // получаем результат
        return $item->fetchColumn();
    }

    /** Собираем запрос для дебага
     * @param $sql_string
     * @param array|null $params
     * @return mixed|string|string[]|null
     */
    public function sql_debug($sql_string, array $params = null)
    {
        if (!empty($params)) {
            $indexed = $params == array_values($params);
            foreach ($params as $k => $v) {
                if (is_object($v)) {
                    if ($v instanceof \DateTime) $v = $v->format('Y-m-d H:i:s');
                    else continue;
                } elseif (is_string($v)) $v = "'$v'";
                elseif ($v === null) $v = 'NULL';
                elseif (is_array($v)) $v = implode(',', $v);

                if ($indexed) {
                    $sql_string = preg_replace('/\?/', $v, $sql_string, 1);
                } else {
                    if ($k[0] != ':') $k = ':' . $k; //add leading colon if it was left out
                    $sql_string = str_replace($k, $v, $sql_string);
                }
            }
        }
        return $sql_string;
    }

}
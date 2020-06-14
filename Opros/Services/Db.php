<?php

/**
 * Class Db
 */
class Db
{
    use TSingltone; // трейт для синглтона

    // @param PDO
    protected $pdo = null;

    /** получаем соединение
     * @return PDO
     */
    public function connect()
    {
        if (!$this->isActiveConnection()) {
            $this->setPdo();
        }

        $this->pdo->query("SET session wait_timeout=28800");

        return $this->pdo;
    }

    public function isActiveConnection () {
        if (is_null($this->pdo)) {
            return false;
        }
        try {
            $testRes = $this->pdo->query('SELECT 1+2+3');
            $testArray = $testRes->fetch();
            if (current($testArray) == 6) {
                return true;
            }
        }
        catch (PDOException $e) {
            return false;
        }
        return false;
    }

    /**
     *  Создаем соединение с БД
     */
    private function setPdo()
    {
        // задаем тип БД, хост, имя базы данных и чарсет
        $dsn = "mysql:host=" . Config::$db_host . ";dbname=" . Config::$db_name . ";charset=" . Config::$db_charset;
        // дополнительные опции
        $opt = [
            // способ обработки ошибок - режим исключений
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // тип получаемого результата по-умолчанию - ассоциативный массив
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // отключаем эмуляцию подготовленных запросов
            PDO::ATTR_EMULATE_PREPARES => false,
            // определяем кодировку запросов
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            PDO::ATTR_PERSISTENT => true,
        ];
        // записываем объект PDO в свойство $this->pdo
        $this->pdo = new PDO($dsn, Config::$db_user, Config::$db_pass, $opt);
    }

    /** Готовим данные для запроса
     * @param $allowed
     * @param $values
     * @param array $source
     * @return bool|string
     */
    public static function pdoSet($allowed, &$values, $source)
    {
        $set = '';
        $values = [];
        foreach ($allowed as $field) {
            if (isset($source[$field]) || is_null($source[$field])) {
                $values[$field] = $source[$field];
                if ($field == "id") {
                    continue;
                } else {
                    $set .= "`" . str_replace("`", "``", $field) . "`" . " = :" . $field . ", ";
                }
            }
        }
        return substr($set, 0, -2);
    }
}
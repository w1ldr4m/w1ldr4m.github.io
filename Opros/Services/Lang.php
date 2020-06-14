<?php

/**
 * Class Lang
 * @property string $lang
 * @property array $data
 */
class Lang
{
    private $lang;
    private $data;
    private static $dir = __DIR__ . '/lang';

    /**
     * Lang constructor.
     * @param $lang
     */
    public function __construct($lang)
    {
        $this->lang = $lang;
        $this->getData();
    }

    /**
     * Получаем данные из файла и записываем в свойство
     */
    private function getData()
    {
        // Получаем настройки
        $this->data = json_decode(file_get_contents(self::$dir . "/" . $this->lang . '.json'), true);
    }

    /** Получаем текст по запросу
     * @param $param
     * @param array $data
     * @return mixed
     */
    public function getParam($param, $data = [])
    {
        $text = $this->data[$param];
        // Если значение найдено - обрабатываем
        if (isset($text)) {
            if (count($data) > 0) {
                foreach ($data as $key => $val) {
                    $text = str_replace("{" . $key . "}", $val, $text);
                }
            }
        } else {
            // Выводим ошибку
            $text = "Unknown Text";
        }
        return $text;
    }

    /** Получаем список языковых файлов
     * @return array
     */
    public static function getList()
    {
        $result = [];
        $langs = glob(self::$dir . "/*");
        if (count($langs)) {
            foreach ($langs as $key => $file) {
                if (!in_array($result, ['.', ".."])) {
                    // извлекаем название файла
                    $fileName = explode("/", $file);
                    // получаем из названия файла название языка
                    $langName = explode(".", end($fileName))[0];
                    // получаем название языка
                    $content = json_decode(file_get_contents($file), TRUE)['name_lang'];
                    // формируем массив
                    $result[$key] = [
                        "lang" => $langName,
                        "name" => isset($content) ? $content : "Unknown Lang"
                    ];
                }
            }
        }
        return $result;
    }

    /** Вернем название языка
     * @return string
     */
    public function getName()
    {
        return $this->lang;
    }

    /** Получаем название языка по короткому имени
     * @param $separator
     * @return mixed
     */
    public static function getNameLangBySeparator($separator)
    {
        return json_decode(file_get_contents(self::$dir . "/" . $separator . '.json'), true)["name_lang"];
    }
}





















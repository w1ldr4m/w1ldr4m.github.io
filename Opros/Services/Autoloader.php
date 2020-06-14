<?php

class Autoloader
{
    // массив путей директорий
    public $dirArray = [];

    /** Наполняем массив путями к директориям
     * Autoloader constructor.
     */
    public function __construct()
    {
        $this->getDirs(__DIR__ . "/../");
    }

    // Подключаем класс (имена классов должны быть уникальными - подключит первый который совпадет по названию)
    public function getClass($className)
    {
        foreach ($this->dirArray as $path) {
            $filename = "{$path}/{$className}.php";
            if (file_exists($filename)) {
                include $filename;
                break;
            }
        }
    }

    /** Получаем список директорий
     * @param $dir
     */
    public function getDirs($dir)
    {
        $arr = glob("{$dir}/*", GLOB_ONLYDIR);
        if (count($arr)) {
            foreach ($arr as $dirname) {
                // Директории только с заглавными буквами
                if (preg_match("~^{$dir}\/([A-Z]{1})\w*~", $dirname)) {
                    $this->dirArray[] = $dirname;
                    $this->getDirs($dirname);
                }
            }
        }
    }

    public function setLog($fileName, $data) {
        $fh = fopen($fileName, 'a');
        fwrite($fh, date('d-m-Y H:i:s') . " - " . $data . "\n");
        fclose($fh);
    }
}
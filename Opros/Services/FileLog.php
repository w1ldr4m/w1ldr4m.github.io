<?php

abstract class FileLog
{

    /**
     * @param $bot Bot
     */
    public static function access($bot)
    {
        self::set(self::prepareData($bot), "access");
    }

    /**
     * @param $bot Bot
     * @return bool
     */
    private static function prepareData($bot)
    {
        $text = $bot->getType() . "::";
        $text .= $bot->getUpdateId() . "::";
        $text .= $bot->getChatId() . " (";
        $text .= $bot->getFullName() . "::";
        $text .= $bot->getChatUserName() . ") || ";
        $text .= str_replace(array("\r\n", "\r", "\n"), '', $bot->getText()) . "\n";
        $text .= json_encode($bot->data);

        return $text;
    }

    /**
     * @param $data
     * @param $name
     * @param bool $time
     */
    public static function set($data, $name, $time = true)
    {
        $dir = __DIR__ . "/../logs/" . $name . "/";
        if(!file_exists($dir)) {
            mkdir($dir);
        }
        $ext = ".log";
        $datetime = $time ? date("Y-m-d H:i:s") . " || " : "";
        $max_num = 1000;

        if (@filesize($dir . $name . $ext) > (2 * (1024 ** 2))) {
            for ($i = 1; $i < $max_num; $i++) {
                if (!file_exists($dir . $name . $i . $ext)) {
                    rename($dir . $name . $ext, $dir . $name . $i . $ext);
                    break;
                }
            }
        }

        $fh = fopen($dir . $name . $ext, 'a');
        fwrite($fh, $datetime . $data . "\n==================================\n");
        fclose($fh);
    }


}
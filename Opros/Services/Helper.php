<?php

abstract class Helper
{
    /** Логируем
     * @param $fileName
     * @param $data
     */
    public static function setLog($fileName, $data)
    {
        $fh = fopen(__DIR__ . "/../logs/" . $fileName, 'a');
        fwrite($fh, date('d-m-Y H:i:s') . " - " . $data . "\n");
        fclose($fh);
    }

    /**
     * @param $bot Bot
     * @return array
     */
    public static function params($bot)
    {
        return $bot->isCallBack()
            ? explode("_", $bot->getText())
            : null;
    }

    /**
     * @param $webhook WebHook
     * @return array
     */
    public static function paramsFromText($text)
    {
        return explode("_", $text);
    }

    /**
     * @param $array
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            return $array->$key;
        } elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }

    /**
     * @param $array
     * @param $from
     * @param $to
     * @param null $group
     * @return array
     */
    public static function map($array, $from, $to, $group = null)
    {
        $result = [];
        foreach ($array as $element) {
            $key = static::getValue($element, $from);
            $value = static::getValue($element, $to);
            if ($group !== null) {
                $result[static::getValue($element, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param int $strength
     * @return string
     */
    public static function generate_string($strength = 16) {
        $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        return $random_string;
    }

    /**
     * @param $text
     * @return string|string[]|null
     */
    public static function prepareText($text)
    {
        $text = strip_tags($text, "<b><strong><i><em><pre><code><a><br>");

        $text = preg_replace_callback("~(<b>)(.*)(<\/b>)~U", function ($matches) {
            return $matches[1] . strip_tags($matches[2], "<br>") . $matches[3];
        }, $text);

        $text = preg_replace_callback("~(<strong>)(.*)(<\/strong>)~U", function ($matches) {
            return $matches[1] . strip_tags($matches[2], "<br>") . $matches[3];
        }, $text);

        $text = preg_replace_callback("~(<em>)(.*)(<\/em>)~U", function ($matches) {
            return $matches[1] . strip_tags($matches[2], "<br>") . $matches[3];
        }, $text);

        $text = preg_replace_callback("~(<i>)(.*)(<\/i>)~U", function ($matches) {
            return $matches[1] . strip_tags($matches[2], "<br>") . $matches[3];
        }, $text);

        $text = preg_replace_callback("~(<pre>)(.*)(<\/pre>)~U", function ($matches) {
            return $matches[1] . strip_tags($matches[2], "<br>") . $matches[3];
        }, $text);

        $text = preg_replace_callback("~(<code>)(.*)(<\/code>)~U", function ($matches) {
            return $matches[1] . strip_tags($matches[2], "<br>") . $matches[3];
        }, $text);

        $text = preg_replace_callback("~(<a\s.*>)(.*)(<\/a>)~U", function ($matches) {
            return $matches[1] . strip_tags($matches[2], "<br>") . $matches[3];
        }, $text);

        return $text;
    }

    /**
     * @param $text
     * @param $length
     * @return string
     */
    public static function pl_truncate($text, $length)
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        } else {
            // текущая длина добавленного текста
            $totalLength = 0;
            // массив тегов которые открыты и находятся в добавленном тексте
            $openTags = [];
            // добавленный текст
            $truncate = '';
            // флаг откр или закр тег
            $tag_type = 1;
            // флаг отсутсвия тега
            $tag_null = 0;

            // разделяем текст в массив
            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);

            // перебираем массив
            foreach ($tags as $tag) {
                // [0] - все вхождение тег + текст после тега до след тега
                // [1] - весь тег - может отсутствовать
                // [2] - название тега - может отсутсвтовать
                // [3] - текст в теге - может отсутствовать

                // проверка на наличие тегов
                if (preg_match('/<[\w]+[^>]*>/s', $tag[1])) {
                    // открывающие теги
                    if (!empty($tag[2]) && !empty($tag[3])) {
                        if($tag[2] != "br") {
                            // добавляем в память для закрытия
                            array_unshift($openTags, $tag[2]);
                        }
                        // ставим флаг открытого тега
                        $tag_type = 1;
                    } else {
                        // ставим флаг закрытого тега
                        $tag_type = 0;
                    }
                    // ставим флаг наличия тега
                    $tag_null = 0;
                } elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[1])) {
                    // закрывающие теги
                    // удаляем из памяти
                    $pos = array_search($tag[2], $openTags);
                    if ($pos !== false) {
                        array_splice($openTags, $pos, 1);
                    }
                    // ставим флаг закрытого тега
                    $tag_type = 0;
                    // ставим флаг наличия тега
                    $tag_null = 0;
                } else {
                    // тег отстутствует
                    $tag_null = 1;
                }

                // если текста нет то выходим (тк у нас нет вложенности тегов)
                if (empty($tag[3])) {
                    // переходим дальше
                    continue;
                }

                // TODO !preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])
                // TODO preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3])

                // определяем размер тега если это откр тег
                $len_tag = $tag_type && !$tag_null ? mb_strlen($tag[1]) : 0;
                // размер закр тега если это откры тег
                $len_close_tag = $tag[2] != "br" && $tag_type && !$tag_null ? mb_strlen("</" . $tag[2] . ">") : 0;
                // общий размер тега откр и закрт
                $len_total_tag = $len_tag + $len_close_tag;
                // размер всего текста
                $len_text_block = mb_strlen($tag[3]);
                /**
                 * 1 сценарий: запрашиваемая длинна больше или равна чем допущенная длина + весь тег + блок текста
                 */
                if ($length >= ($totalLength + $len_total_tag + $len_text_block)) {
                    // добавляем весь блок
                    $truncate .= $tag[0];
                    // увеличиваем добавленную часть текста
                    $totalLength += ($len_total_tag + $len_text_block);
                } /**
                 *  2 сценарий: запрашиваемая длинна меньше чем допущенная длина + весь тег + блок текста
                 */
                else {
                    /**
                     * 2.1 сценарий: тег присутствует
                     */
                    if (!$tag_null) {
                        /**
                         * 2.1.1 сценарий: запрашиваемая длина больше чем допущенная длина + весь тег
                         */
                        if ($length > ($totalLength + $len_total_tag)) {
                            // вычисляем остаток длины
                            $left = $length - ($totalLength + $len_total_tag);
                            // отрезаем от блока текста отрезок равный остатку
                            $temp_text = mb_substr($tag[3], 0, $left);
                            // добавляем текст и увеличиваем счетчик
                            $truncate .= $tag[1] . $temp_text;
                            // выходим
                            break;
                        } /**
                         * 2.1.2 сценарий: запрашиваемая длина меньше чем допущенная длина + весь тег
                         */
                        else {
                            // смотрим остаток
                            $left = $length - $totalLength;
                            // отрезаем кусок от текста
                            $temp_text = mb_substr($tag[3], 0, $left);
                            // добавляем текст и увеличиваем счетчик
                            $truncate .= $temp_text;
                            // удаляем если это открывающийся тег из массива
                            if ($tag_type) {
                                $pos = array_search($tag[2], $openTags);
                                if ($pos !== false) {
                                    array_splice($openTags, $pos, 1);
                                }
                            }
                            // выходим
                            break;
                        }
                    } /**
                     * 2.2 сценарий: тег отсутствует
                     */
                    else {
                        /**
                         * 2.2.1 сценарий: запрашиваемая длинна больше или равна чем допущенная длина + текст
                         */
                        if ($length >= ($totalLength + $len_text_block)) {
                            // добавляем текст
                            $truncate .= $tag[3];
                            // увеличиваем счетчик
                            $totalLength += $len_text_block;
                        } /**
                         * 2.2.2 сценарий: запрашиваемая длинна меньше чем допущенная длина + текст
                         */
                        else {
                            // вычисляем остаток длины
                            $left = $length - $totalLength;
                            // отрезаем от блока текста отрезок равный остатку
                            $temp_text = mb_substr($tag[3], 0, $left);
                            // добавляем текст и увеличиваем счетчик
                            $truncate .= $temp_text;
                            // выходим
                            break;
                        }
                    }
                }
            }
            // закрываем теги
            if (count($openTags)) {
                foreach ($openTags as $tag) {
                    $truncate .= '</' . $tag . '>';
                }
            }
            return $truncate;
        }
    }

    /**
     * @param $content
     * @param bool $doubleEncode
     * @return string
     */
    public static function encode($content, $doubleEncode = true)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

}
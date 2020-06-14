<?php

abstract class Route
{
    private static $rules = [
        [
            'template' => '~^\/start$~',
            'method' => 'WebHook::start'
        ],
        [
            'template' => '~^\/start m[\w]{17}$~', // загрузка медиа админом
            'method' => 'Media::run'
        ],
        [
            'template' => '~^\/start mv[\w]{17}$~', // просмотр медиа админом
            'method' => 'Media::viewFile'
        ],
        [
            'template' => '~^\/start worksheet[\w]{8}$~', // вывод анкеты
            'method' => 'Claim::start'
        ],
        [
            'template' => '~^\/geo_[\w]{16}$~', // просмотр гео
            'method' => 'Claim::showLocation'
        ],
    ];

    /** Набор динамических кнопок клавиатуры
     * @var array
     */
    private static $dinamicButton = [
//        [
//            'name' => 'viewHelp', // Помощь
//            'method' => 'Help::run'
//        ],
    ];

    /** Проверяем данные на правила команд бота
     * @param $text
     * @return array|mixed
     */
    private static function checkCommand($text)
    {
        if (count(self::$rules)) {
            foreach (self::$rules as $rule) {
                if (preg_match($rule['template'], $text)) {
                    $rule['result'] = true;
                    return $rule;
                }
            }
        }
//        if (count(self::$dinamicButton)) {
//            foreach (self::$dinamicButton as $btn) {
//                $pattern = '~^' . Params::get($btn['name']) . '$~';
//                if (preg_match($pattern, $text)) {
//                    $btn['result'] = true;
//                    return $btn;
//                }
//            }
//        }
        return ['result' => false];
    }

    /** Проверяем необходимость передать данные в запланированный метод
     * @param $action
     * @return array
     */
    private static function write($action)
    {
        if (!empty($action)) {
            $param = Helper::paramsFromText($action);
            return ['result' => true, 'method' => $param[0]];
        } else {
            return ['result' => false];
        }
    }

    /**  Запускаем роутер
     * @param $wh WebHook
     * @return bool
     */
    public static function run($wh)
    {
        if ($wh->bot->isMessage()) {
            $write = self::write($wh->user->getAction());
            if ($wh->bot->isText()) {
                $command = self::checkCommand($wh->bot->getText());
                if ($write['result'] && !$command['result']) {
                    $write['method']($wh);
                } elseif ($command['result']) {
                    $wh->user->setAction("");
                    $command['method']($wh);
                }
            } else {
                if ($write['result']) {
                    $write['method']($wh);
                }
            }
        } elseif ($wh->bot->isCallBack()) {
            $class = current(Helper::params($wh->bot));
            $class($wh);
        }
        return true;
    }

    /**
     * @param $class_self
     */
    public static function web($class_self)
    {
        $path = explode("::", $_GET['a']);
        if (!isset($_GET['a'])) {
            $class_self->index();
        } elseif (count($path) == 1) {
            $method = strtolower($path[0]);
            if (method_exists($class_self, $method)) {
                $class_self->$method();
            } else {
                $class_self->error("404", "Not found");
            }
        } elseif (count($path) == 2) {
            $class_ = ucfirst($path[0]) . "Controller";
            if(class_exists($class_)) {
                $path_method = explode("-", $path[1]);
                $method = "";
                foreach($path_method as $key => $path_method_) {
                    $method .= $key > 0
                        ? ucfirst($path_method_)
                        : $path_method_;
                }
                $class = new $class_();
                if (method_exists($class, $method)) {
                    $class->$method();
                } else {
                    $class_self->error("404", "Not found");
                }
            } else {
                $class_self->error("404", "Not found");
            }
        } else {
            $class_self->error("404", "Not found");
        }
    }


}
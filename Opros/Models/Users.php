<?php

/**
 * Class User
 * @property int $id
 * @property int $telegram_id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $lang
 * @property string $action
 * @property int $ban
 * @property int $create_at
 * @property int $update_at
 */
class Users extends Model
{
    public function getTable()
    {
        return "users";
    }

    /** Авторизуем (обновляем или добавляем)
     * @param $bot Bot
     * @return bool|null|Users
     */
    public static function login($bot)
    {
        $data = self::prepareUserData($bot);
        if (!is_null($data['telegram_id'])) {
            $model = self::find()->findByTelegramId($data['telegram_id']);
            return ($model)
                ? $model->updateUserData($data)
                : self::insertUserData($data);

        } else {
            return null;
        }
    }

    /** Получаем объект пользователя по telegram_id
     * @param $telegram_id
     * @return bool|Users
     */
    public function findByTelegramId($telegram_id)
    {
        return $this->one(" WHERE telegram_id = :telegram_id LIMIT 1",
            [
                ':telegram_id' => (int)$telegram_id
            ]
        );
    }

    /** Добавляем пользователя
     * @param $userData
     * @return bool|Users
     */
    public static function insertUserData($userData)
    {
        $model = new self();
        $model->telegram_id = $userData['telegram_id'];
        $model->first_name = $userData['first_name'];
        $model->last_name = $userData['last_name'];
        $model->username = $userData['username'];
        $model->create_at = date("Y-m-d H:i:s");
        return $model->save();
    }

    /** Обновляем данные пользователя
     * @param $userData
     * @return bool|Users
     */
    public function updateUserData($userData)
    {
        $this->first_name = $userData['first_name'];
        $this->last_name = $userData['last_name'];
        $this->username = $userData['username'];
        $this->update_at = date("Y-m-d H:i:s");
        return $this->save();
    }

    //////////////////////////////////////////////////////
    ///
    /// Работа со свойствами
    ///
    //////////////////////////////////////////////////////
    /** Получаем id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /** Получаем telegram_id
     * @return int
     */
    public function getTelegramId()
    {
        return $this->telegram_id;
    }

    /** Проверяем на админа
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isAdminCheck($this->getTelegramId());
    }

    /** Проверяем на админа
     * @return bool
     */
    public function isAdminCheck($user_id)
    {
        return $user_id === Config::$botAdmin;
    }

    /** Получаем настройку lang
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /** Меняем настройку lang
     * @param $lang
     * @return bool|Users
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
        return $this->save();
    }

    /** Меняем последнее действие
     * @param $action
     * @return bool|Users
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this->save();
    }

    /** Получаем последнее действие
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /** Меняем значение ban
     * @param $type
     * @return bool|Users
     */
    public function setBan($type)
    {
        $this->ban = $type;
        return $this->save();
    }

    /** Получаем значение ban
     * @return string
     */
    public function getBan()
    {
        return $this->ban;
    }

    /** Получаем first_name
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /** Получаем last_name
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /** Получаем username
     * @return string
     */
    public function getUserName()
    {
        return $this->username;
    }

    /** Получаем полное имя пользователя
     * @return string
     */
    public function getFullName()
    {
        return trim($this->getFirstName() . " " . $this->getLastName());
    }

    //////////////////////////////////////////////////////
    ///
    /// Проверка и установка языковых настроек
    ///
    //////////////////////////////////////////////////////
    /** Проверяем выбран ли язык у пользователя
     * @param $webhook WebHook
     */
    public function userLang($webhook)
    {
        if (is_null($this->getLang())) {
            // получаем языковые настройки
            $langs = Lang::getList();
            //  проверить если lang 1 в системе то установить его по умолчанию
            if (count($langs) == 1) {
                // устанавливаем язык пользовтаелю
                $setLang = $this->setLang($langs[0]['lang']);
                if ($setLang) {
                    // задаем язык приложению
                    $webhook->setLang(new Lang($this->getLang()));
                    // перенаправляем на старт бота
                    WebHook::start($webhook);
                } else {
                    // выводим сообщение об ошибке popup
                    $webhook->bot->sendMessage($this->telegram_id, "Failed to change language, try again");
                }
            } elseif (!count($langs)) {
                // выводим ошибку-предупреждение
                $webhook->bot->sendMessage($this->telegram_id, "Failed to change language, try again (error 001)");
            } else {
                // получаем текст
                $text = $webhook->bot->getText();
                // возможно это запрос на 1-ю смену языка, т.е. язык не установлен
                // просто при инлайн запросе все равно в index запрос сюда идет в userLang
                // просто перенаправляем в setUserLangInline_ а то цикл замкнутый идет
                if (preg_match("~setUserLangInline_~", $text)) {
                    // отправляем на смену
                    self::setUserLangInline($webhook);
                } else {
                    // делаем запрос на смену язык
                    self::changeLang($webhook);
                }
            }
        } else {
            // получаем языковые настройки
            //$this->lang = new Lang($this->getLang());
            $webhook->setLang(new Lang($this->getLang()));
            // передаем в роутер
            Route::run($webhook);
        }
    }

    /** Запрос на установку языка
     * @param $webhook WebHook
     */
    public static function changeLang($webhook)
    {
        // Подгружаем данные языковых настроек
        foreach (Lang::getList() as $lang) {
            $buttons[][] = $webhook->bot->buildInlineKeyboardButton($lang['name'], 'User::setUserLangInline_' . $lang['lang']);;
        }
        // отправляем сообщение
        $webhook->bot->sendMessage($webhook->bot->getChatId(), 'Selected language', $buttons);
    }

    /** Обработка команды inline по установке языка
     * @param $webhook WebHook
     */
    public static function setUserLangInline($webhook)
    {
        // 1 - lang
        $param = Helper::params($webhook);
        // устанавливаем язык пользовтаелю
        $setLang = $webhook->user->setLang($param[1]);
        // проверяем
        if ($setLang) {
            // глушим уведомление
            $webhook->bot->notice();
            // удаляем сообщение
            $webhook->bot->deleteMessage();
            // Задаем язык
            $webhook->setLang(new Lang($webhook->user->getLang()));
            // перенаправляем на старт бота
            WebHook::start($webhook);
        } else {
            // выводим сообщение об ошибке popup
            $webhook->bot->notice("Failed to change language, try again");
        }
    }

    /** Получаем список за(раз)баненных пользователей
     * @param int $type
     * @param $current
     * @param $count
     * @return array|bool
     */
    public static function allBanUser($type, $current, $count)
    {
        return self::find()->all(" WHERE ban = :type ORDER BY id LIMIT " . ($current * $count) . ", " . $count, ['type' => $type]);
    }

    /** Общее кол-во за(раз)баненных пользователей
     * @param $type
     * @return mixed
     */
    public static function allBanCount($type)
    {
        return self::find()->count(" ban = :type ", ['type' => $type]);
    }

    //////////////////////////////////////////////////////
    ///
    /// Вспомогательные методы
    ///
    //////////////////////////////////////////////////////
    /** Готовим данные для пользователя
     * @param $bot Bot
     * @return array
     */
    public static function prepareUserData($bot)
    {
        return [
            'telegram_id' => $bot->getChatId(),
            'first_name' => $bot->getChatFirstName(),
            'last_name' => $bot->getChatLastName(),
            'username' => $bot->getChatUserName(),
        ];
    }


}
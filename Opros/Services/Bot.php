<?php

/**
 * Class Bot
 * @property string $token
 * @property object $data
 */
class Bot
{
    // для хранения массива данных от Телеграм
    public $data;

    /**
     * Bot constructor.
     */
    public function __construct()
    {
        // записываем в свойство данные
        $this->data = json_decode(file_get_contents('php://input'));
    }

    /**
     * @return mixed
     */
    public function getUpdateId()
    {
        return $this->data->update_id;
    }

    /** Получаем id
     * @return mixed
     */
    public function getChatId()
    {
        if ($this->isCallBack()) {
            return $this->data->callback_query->message->chat->id;
        } elseif ($this->isMessage()) {
            return $this->data->message->chat->id;
        } elseif ($this->isEditedMessage()) {
            return $this->data->edited_message->chat->id;
        } else {
            return null;
        }
    }

    /** Получаем first_name
     * @return mixed
     */
    public function getChatFirstName()
    {
        if ($this->isCallBack()) {
            return $this->data->callback_query->message->chat->first_name;
        } elseif ($this->isMessage()) {
            return $this->data->message->chat->first_name;
        } elseif ($this->isEditedMessage()) {
            return $this->data->edited_message->chat->first_name;
        } else {
            return null;
        }
    }

    /** Получаем last_name
     * @return mixed
     */
    public function getChatLastName()
    {
        if ($this->isCallBack()) {
            return $this->data->callback_query->message->chat->last_name;
        } elseif ($this->isMessage()) {
            return $this->data->message->chat->last_name;
        } elseif ($this->isEditedMessage()) {
            return $this->data->edited_message->chat->last_name;
        } else {
            return null;
        }
    }

    /** Получаем username
     * @return mixed
     */
    public function getChatUserName()
    {
        if ($this->isCallBack()) {
            return $this->data->callback_query->message->chat->username;
        } elseif ($this->isMessage()) {
            return $this->data->message->chat->username;
        } elseif ($this->isEditedMessage()) {
            return $this->data->edited_message->chat->username;
        } else {
            return null;
        }
    }

    public function getFullName()
    {
        return trim($this->getChatFirstName() . " " . $this->getChatLastName());
    }

    /** Получаем id сообщения
     * @return mixed
     */
    public function getMessageId()
    {
        if ($this->isCallBack()) {
            return $this->data->callback_query->message->message_id;
        } elseif ($this->isMessage()) {
            return $this->data->message->message_id;
        } elseif ($this->isEditedMessage()) {
            return $this->data->edited_message->message_id;
        } else {
            return null;
        }
    }

    /** Получим значение текст
     * @return mixed
     */
    public function getText()
    {
        if ($this->isCallBack()) {
            return $this->data->callback_query->data;
        } elseif ($this->isMessage()) {
            if ($this->isAudio() || $this->isDocument() || $this->isVoice() || $this->isPhoto()) {
                return $this->data->message->caption;
            } elseif ($this->isLocation()) {
                return json_encode($this->data->message->location);
            } elseif ($this->isText()) {
                return $this->data->message->text;
            } else {
                return NULL;
            }
        } elseif ($this->isEditedMessage()) {
            if ($this->isEditedAudio() || $this->isEditedDocument() || $this->isEditedVoice() || $this->isEditedPhoto()) {
                return $this->data->edited_message->caption;
            } elseif ($this->isEditedLocation()) {
                return json_encode($this->data->edited_message->location);
            } elseif ($this->isEditedText()) {
                return $this->data->edited_message->text;
            } else {
                return NULL;
            }
        }
        return NULL;
    }

    /** Узнаем какой тип данных пришел
     * @return bool|string
     */
    public function getType()
    {
        if (isset($this->data->callback_query)) {
            return "callback_query";
        } elseif (isset($this->data->message)) {
            return "message";
        } elseif (isset($this->data->edited_message)) {
            return "edited_message";
        } else {
            return false;
        }
    }

    public function isCallBack()
    {
        return $this->getType() == "callback_query";
    }

    public function isMessage()
    {
        return $this->getType() == "message";
    }

    public function isEditedMessage()
    {
        return $this->getType() == "edited_message";
    }

    public function isText()
    {
        return isset($this->data->message->text);
    }

    public function isEditedText()
    {
        return isset($this->data->edited_message->text);
    }

    public function isPhoto()
    {
        return isset($this->data->message->photo);
    }

    public function isEditedPhoto()
    {
        return isset($this->data->edited_message->photo);
    }

    public function isAudio()
    {
        return isset($this->data->message->audio);
    }

    public function isEditedAudio()
    {
        return isset($this->data->edited_message->audio);
    }

    public function isDocument()
    {
        return isset($this->data->message->document);
    }

    public function isEditedDocument()
    {
        return isset($this->data->edited_message->document);
    }

    public function isAnimation()
    {
        return isset($this->data->message->animation);
    }

    public function isEditedAnimation()
    {
        return isset($this->data->edited_message->animation);
    }

    public function isSticker()
    {
        return isset($this->data->message->sticker);
    }

    public function isEditedSticker()
    {
        return isset($this->data->edited_message->sticker);
    }

    public function isVoice()
    {
        return isset($this->data->message->voice);
    }

    public function isEditedVoice()
    {
        return isset($this->data->edited_message->voice);
    }

    public function isVideoNote()
    {
        return isset($this->data->message->video_note);
    }

    public function isEditedVideoNote()
    {
        return isset($this->data->edited_message->video_note);
    }

    public function isVideo()
    {
        return isset($this->data->message->video);
    }

    public function isEditedVideo()
    {
        return isset($this->data->edited_message->video);
    }

    public function isLocation()
    {
        return isset($this->data->message->location);
    }

    public function isEditedLocation()
    {
        return isset($this->data->edited_message->location);
    }

    public function isReplyMessage()
    {
        return isset($this->data->message->reply_to_message);
    }

    public function getReplyMessageId()
    {
        return $this->data->message->reply_to_message->message_id;
    }

    public function replyForwardFromId()
    {
        return $this->data->message->reply_to_message->forward_from->id;
    }

    public function isBotReplyMessage()
    {
        return !!$this->data->message->reply_to_message->from->is_bot;
    }

    /** Получаем entities
     * @return object | null
     */
    public function getEntities()
    {
        if ($this->isMessage()) {
            return isset($this->data->message->entities) ? $this->data->message->entities : NULL;
        } elseif ($this->isEditedMessage()) {
            return isset($this->data->edited_message->entities) ? $this->data->edited_message->entities : NULL;
        } else {
            return NULL;
        }
    }

    /** Получаем тип сообщения
     * @return string
     */
    public function getMessageType()
    {
        if ($this->isText()) {
            return "text";
        } elseif ($this->isPhoto()) {
            return "photo";
        } elseif ($this->isAudio()) {
            return "audio";
        } elseif ($this->isDocument()) {
            return "document";
        } elseif ($this->isAnimation()) {
            return "animation";
        } elseif ($this->isSticker()) {
            return "sticker";
        } elseif ($this->isVoice()) {
            return "voice";
        } elseif ($this->isVideoNote()) {
            return "video_note";
        } elseif ($this->isVideo()) {
            return "video";
        } elseif ($this->isLocation()) {
            return "location";
        } else {
            return "no_detected";
        }
    }

    public function getMessageFileId()
    {
        $message = $this->data->message;
        if ($this->isPhoto()) {
            return end($message->photo)->file_id;
        } elseif ($this->isAudio()) {
            return $message->audio->file_id;
        } elseif ($this->isDocument()) {
            return $message->document->file_id;
        } elseif ($this->isAnimation()) {
            return $message->animation->file_id;
        } elseif ($this->isSticker()) {
            return $message->sticker->file_id;
        } elseif ($this->isVoice()) {
            return $message->voice->file_id;
        } elseif ($this->isVideoNote()) {
            return $message->video_note->file_id;
        } elseif ($this->isVideo()) {
            return $message->video->file_id;
        } else {
            return NULL;
        }
    }

    /** Уведомление в клиенте
     * @param $text
     * @param bool $type
     */
    public function notice($text = "", $type = false)
    {
        $data = [
            'callback_query_id' => $this->data->callback_query->id,
            'show_alert' => $type,
        ];
        if (!empty($text)) {
            $data['text'] = $text;
        }
        $this->botApiQuery("answerCallbackQuery", $data);
    }

    /**
     * @param string $text
     * @param bool $type
     */
    public function noticeDelete($text = "", $type = false)
    {
        if ($this->isCallBack()) {
            $this->notice($text, $type);
            $this->deleteMessageSelf();
        }
    }

    /** Удаляем сообщение без параметров
     * @return mixed
     */
    public function deleteMessageSelf()
    {
        return $this->deleteMessage($this->getChatId(), $this->getMessageId());
    }

    /** Удаляем сообщение
     * @param $chat_id
     * @param $message_id
     * @return mixed
     */
    public function deleteMessage($chat_id, $message_id)
    {
        return $this->botApiQuery("deleteMessage", [
                "chat_id" => $chat_id,
                "message_id" => $message_id
            ]
        );
    }

    /** Кнопка inline
     * @param $text
     * @param string $callback_data
     * @param string $url
     * @return array
     */
    public function buildInlineKeyboardButton($text, $callback_data = '', $url = '')
    {
        // рисуем кнопке текст
        $replyMarkup = [
            'text' => $text,
        ];
        // пишем одно из обязательных дополнений кнопке
        if ($url != '') {
            $replyMarkup['url'] = $url;
        } elseif ($callback_data != '') {
            $replyMarkup['callback_data'] = $callback_data;
        }
        // возвращаем кнопку
        return $replyMarkup;
    }

    /** набор кнопок inline
     * @param array $options
     * @return string
     */
    public function buildInlineKeyBoard(array $options)
    {
        // собираем кнопки
        $replyMarkup = [
            'inline_keyboard' => $options,
        ];
        // преобразуем в JSON объект
        $encodedMarkup = json_encode($replyMarkup, true);
        // возвращаем клавиатуру
        return $encodedMarkup;
    }

    /** кнопка клавиатуры
     * @param $text
     * @param bool $request_contact
     * @param bool $request_location
     * @return array
     */
    public function buildKeyboardButton($text, $request_contact = false, $request_location = false)
    {
        $replyMarkup = [
            'text' => $text,
            'request_contact' => $request_contact,
            'request_location' => $request_location,
        ];
        return $replyMarkup;
    }

    /** готовим набор кнопок клавиатуры
     * @param array $options
     * @param bool $onetime
     * @param bool $resize
     * @param bool $selective
     * @return string
     */
    public function buildKeyBoard(array $options, $onetime = false, $resize = true, $selective = true)
    {
        $replyMarkup = [
            'keyboard' => $options,
            'one_time_keyboard' => $onetime,
            'resize_keyboard' => $resize,
            'selective' => $selective,
        ];
        $encodedMarkup = json_encode($replyMarkup, true);
        return $encodedMarkup;
    }

    /** Отправляем текстовое сообщение
     * @param $user_id
     * @param $text
     * @param null $buttons
     * @param bool $type
     * @param bool $url
     * @return mixed
     */
    public function sendMessage($user_id, $text, $buttons = NULL, $type = false, $url = false)
    {
        // готовим массив данных
        $data_send = [
            'chat_id' => $user_id,
            'text' => $text,
            'parse_mode' => 'html',
            'disable_web_page_preview' => $url
        ];
        // если переданны кнопки то добавляем их к сообщению
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $type ? $this->buildKeyBoard($buttons) : $this->buildInlineKeyBoard($buttons);
        }
        // отправляем текстовое сообщение
        return $this->botApiQuery("sendMessage", $data_send);
    }

    /** Отправляем фотографию
     * @param $user_id
     * @param $photo
     * @param null $buttons
     * @param null $caption
     * @param bool $url
     * @return mixed
     */
    public function sendPhoto($user_id, $photo, $caption = NULL, $buttons = NULL, $url = false)
    {
        // готовим массив данных
        $data_send = [
            'chat_id' => $user_id,
            'photo' => $photo,
            'parse_mode' => 'html',
            'disable_web_page_preview' => $url
        ];
        // если есть описание
        if (!is_null($caption)) {
            $data_send['caption'] = $caption;
        }
        // если переданны кнопки то добавляем их к сообщению
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $this->buildInlineKeyBoard($buttons);
        }
        // отправляем фотографию
        return $this->botApiQuery("sendPhoto", $data_send);
    }

    /** Отправляем геолокацию
     * @param $user_id
     * @param $latitude
     * @param $longitude
     * @param null $buttons
     * @return mixed
     */
    public function sendLocation($user_id, $latitude, $longitude, $buttons = null)
    {
        $data_send = [
            'chat_id' => $user_id,
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $this->buildInlineKeyBoard($buttons);
        }
        return $this->botApiQuery("sendLocation", $data_send);
    }

    /** Меняем содержимое сообщения
     * @param $user_id
     * @param $message_id
     * @param $text
     * @param null $buttons
     * @param bool $type
     * @return mixed
     */
    public function editMessageText($user_id, $message_id, $text, $buttons = NULL, $type = false)
    {
        // готовим массив данных
        $data_send = [
            'chat_id' => $user_id,
            'text' => $text,
            'message_id' => $message_id,
            'parse_mode' => 'html'
        ];
        // если переданны кнопки то добавляем их к сообщению
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $type ? $this->buildKeyBoard($buttons) : $this->buildInlineKeyBoard($buttons);
        }
        // отправляем текстовое сообщение
        return $this->botApiQuery("editMessageText", $data_send);
    }

    /**
     * @param $user_id
     * @param $message_id
     * @param $media
     * @param null $buttons
     * @return mixed
     */
    public function editMessageMedia($user_id, $message_id, $media, $buttons = null)
    {
        $data_send = [
            'chat_id' => $user_id,
            'message_id' => $message_id,
            'media' => json_encode($media)
        ];
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $this->buildInlineKeyBoard($buttons);
        }
        return $this->botApiQuery("editMessageMedia", $data_send);
    }

    /** Элемент группы медиа
     * @param $media
     * @param null $caption
     * @return array
     */
    public function inputMediaPhoto($media, $caption = null)
    {
        // готовим массив данных
        $data_send = [
            'type' => 'photo',
            'media' => $media,
            'parse_mode' => 'html'
        ];
        // если есть описание добавляем
        if (!is_null($caption)) {
            $data_send['caption'] = $caption;
        }
        // отправляем текстовое сообщение
        return $data_send;
    }

    /**
     * @param $media
     * @param $type
     * @param null $caption
     * @return array
     */
    public function inputMedia($media, $type, $caption = null)
    {
        // готовим массив данных
        $data_send = [
            'type' => $type,
            'media' => $media,
            'parse_mode' => 'html'
        ];
        // если есть описание добавляем
        if (!is_null($caption)) {
            $data_send['caption'] = $caption;
        }
        // вернем массив
        return $data_send;
    }


    /** Отправляем группу медиа
     * @param $user_id
     * @param $arrayMedia
     * @param bool $disable
     * @param null $reply_id
     * @return mixed
     */
    public function sendMediaGroup($user_id, $arrayMedia, $disable = false, $reply_id = null)
    {
        // готовим массив данных
        $data_send = [
            'chat_id' => $user_id,
            'media' => json_encode($arrayMedia),
            'disable_notification' => $disable,
        ];
        // если это ответ на сообщение
        if (!is_null($reply_id)) {
            $data_send['reply_to_message_id'] = $reply_id;
        }
        // отправляем текстовое сообщение
        return $this->botApiQuery("sendMediaGroup", $data_send);
    }

    // Отправляем стикер
    public function sendSticker($user_id, $file_id, $buttons = NULL, $type = false, $reply_id = null, $disable = false)
    {
        $data_send = [
            'chat_id' => $user_id,
            'sticker' => $file_id,
            'disable_notification' => $disable,
        ];
        // если это ответ на сообщение
        if (!is_null($reply_id)) {
            $data_send['reply_to_message_id'] = $reply_id;
        }
        // если переданны кнопки то добавляем их к сообщению
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $type ? $this->buildKeyBoard($buttons) : $this->buildInlineKeyBoard($buttons);
        }
        return $this->botApiQuery("sendSticker", $data_send);
    }

    /** Отправляем видео с inline кнопками
     * @param $user_id
     * @param $video
     * @param null $buttons
     * @param null $caption
     * @param bool $url
     * @return mixed
     */
    public function sendVideo($user_id, $video, $caption = NULL, $buttons = NULL, $url = false)
    {
        $data_send = [
            'chat_id' => $user_id,
            'video' => $video,
            'parse_mode' => 'html',
            'disable_web_page_preview' => $url
        ];
        if (!is_null($caption)) {
            $data_send['caption'] = $caption;
        }
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $this->buildInlineKeyBoard($buttons);
        }
        return $this->botApiQuery("sendVideo", $data_send);
    }

    /** Отправляем документ с inline кнопками
     * @param $user_id
     * @param $document
     * @param null $buttons
     * @param null $caption
     * @param bool $url
     * @return mixed
     */
    public function sendDocument($user_id, $document, $caption = NULL, $buttons = NULL, $url = false)
    {
        $data_send = [
            'chat_id' => $user_id,
            'document' => $document,
            'parse_mode' => 'html',
            'disable_web_page_preview' => $url
        ];
        if (!is_null($caption)) {
            $data_send['caption'] = $caption;
        }
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $this->buildInlineKeyBoard($buttons);
        }
        return $this->botApiQuery("sendDocument", $data_send);
    }

    /** Переадресуем сообщение
     * @param $chat_id
     * @return mixed
     */
    public function forwardMessage($chat_id)
    {
        $data_send = [
            'chat_id' => $chat_id,
            'from_chat_id' => $this->getChatId(),
            'message_id' => $this->getMessageId(),
            'disable_notification' => false,
            'parse_mode' => 'html'
        ];
        return $this->botApiQuery("forwardMessage", $data_send);
    }

    /** Отправляем копию сообщения
     * @param $user_id
     * @param int $type
     * @return mixed
     */
    public function sendCopy($user_id, $type = 0)
    {
        $messageType = $this->getMessageType();
        $dop = "";

        if ($type) {
            $link = !empty($this->getChatUserName())
                ? "@" . $this->getChatUserName()
                : "<a href='tg://user?id=" . $this->getChatId() . "'>Профиль</a>";
            $dop = "USER_ID::" . $this->getChatId() . "::\nfrom <b>" . $this->getFullName() . "</b> | " . $link . "\n----- " . $messageType . " ----\n";
        }

        // дополнительное сообщение для feedback
        $dopSend = !in_array($messageType, ['location', 'sticker', 'video_note']) ? 0 : 1;

        $data['chat_id'] = $user_id;
        $data['parse_mode'] = 'html';

        if($messageType == "location") {
            $data['longitude'] = $this->data->location->longitude;
            $data['latitude'] = $this->data->location->latitude;
        } elseif($messageType == "text") {
            $data['text'] = $dop . $this->prepareMessageWithEntities($this->getText(), $this->getEntities());
        } else {
            $data[$messageType] = $this->getMessageFileId();
        }

        if(!in_array($messageType, ['text', 'location', 'sticker', 'video_note'])) {
            $data['caption'] = $dop . $this->prepareMessageWithEntities($this->getText(), $this->getEntities());
        }

        $method = $this->getMethodByMessageType($messageType);
        if (!is_null($method)) {
            // отправляем доп инфу (USER_ID) админу это в тех случаях когда в сообщении нет текстовой оcновы
            if ($type && $dopSend) {
                $dopAdmin = $this->sendMessage(Config::$botAdmin, $dop);
                Message::add($dopAdmin->result->message_id, $this->getChatId(), $this, -1);
            }
            $result = $this->botApiQuery($method, $data);
            return $result->result->message_id;
        }
    }

    /** Добавляем форматирование
     * @param $text
     * @param $entities
     * @return mixed
     */
    public function prepareMessageWithEntities($text, $entities)
    {
        if ($entities) {
            $prepareText = "";
            foreach ($entities as $key => $entity) {
                // добавляем все что между форматированием
                if ($entity->offset > 0) {
                    /*
                     * старт = если начало больше 0 и это первый элемент то берем сначала с нуля
                     * если не первый то берем сразу после предыдущего элемента
                     *
                     * длина = это разница между стартом и текущим началом
                     */
                    $start = $key == 0 ? 0 : ($entities[$key - 1]->offset + $entities[$key - 1]->length);
                    $length = $entity->offset - $start;
                    // добавляем
                    $prepareText .= mb_substr($text, $start, $length);
                }
                // выбираем текущий элемент форматирования
                $charts = mb_substr($text, $entity->offset, $entity->length);
                // обрамляем в необходимый формат
                if ($entity->type == "bold") {
                    $charts = "<b>" . $charts . "</b>";
                } elseif ($entity->type == "italic") {
                    $charts = "<i>" . $charts . "</i>";
                } elseif ($entity->type == "code") {
                    $charts = "<code>" . $charts . "</code>";
                } elseif ($entity->type == "pre") {
                    $charts = "<pre>" . $charts . "</pre>";
                } elseif ($entity->type == "text_link") {
                    $charts = "<a href='" . $entity->url . "'>" . $charts . "</a>";
                }
                // добавляем
                $prepareText .= $charts;
            }
            // добавляем остатки текста если такие есть
            $prepareText .= mb_substr($text, (end($entities)->offset + end($entities)->length));
            // возвращаем результат
            return $prepareText;
        }
        return $text;
    }

    /** Определяем метод по подтипу message
     * @param $type
     * @return string|null
     */
    public function getMethodByMessageType($type) {
        if($type == "photo") {
            return "sendPhoto";
        } elseif($type == "text") {
            return "sendMessage";
        } elseif($type == "audio") {
            return "sendAudio";
        } elseif($type == "document") {
            return "sendDocument";
        } elseif($type == "animation") {
            return "sendAnimation";
        } elseif($type == "sticker") {
            return "sendSticker";
        } elseif($type == "voice") {
            return "sendVoice";
        } elseif($type == "video_note") {
            return "sendVideoNote";
        } elseif($type == "video") {
            return "sendVideo";
        } elseif($type == "location") {
            return "sendLocation";
        } else {
            return NULL;
        }
    }


    /** Запросы в Бот АПИ
     * @param $method
     * @param array $fields
     * @return mixed
     */
    public function botApiQuery($method, $fields = array())
    {
        $ch = curl_init('https://api.telegram.org/bot' . Settings::param('token_bot') . '/' . $method);
        curl_setopt_array($ch, array(
//            CURLOPT_PROXY => Config::$proxy,
            CURLOPT_POST => count($fields),
            CURLOPT_POSTFIELDS => http_build_query($fields),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 10
        ));
        $r = json_decode(curl_exec($ch));
        curl_close($ch);
        return $r;
    }

    /** Запросы в Бот АПИ для отправки файла
     * @param $file
     * @param array $fields
     * @return mixed
     */
    public function botApiQueryFiles($fields, $file)
    {
        $file = curl_file_create(__DIR__ . "/../" . $file, mime_content_type($file), pathinfo($file)['basename']);
        $fields['document'] = $file;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . Settings::param('token_bot') . '/sendDocument');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: multipart/form-data;charset=utf-8']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);
        $errmsg = curl_error($ch);
        $err = curl_errno($ch);
        curl_close($ch);
        return $result;
    }
}


<?php

abstract class Media
{
    /**
     * @var array
     */
    public static $type = [
        'w' => 'worksheet',
        'r' => 'result',
    ];

    /**
     * @param $wh WebHook
     */
    public static function beforeAction($wh)
    {
        if ($wh->user->telegram_id != Settings::param('admin_bot')) {
            $wh->bot->sendMessage(
                $wh->user->telegram_id,
                $wh->lang->getParam("mediaNoAdmin", ["uid" => $wh->user->telegram_id])
            );
            exit();
        }
    }

    /**
     * @param $wh WebHook
     */
    public static function run($wh)
    {
        self::beforeAction($wh);
        preg_match("~^\/start m([\w]{1})([\w]{16})$~", $wh->bot->getText(), $matches);
        $wh->user->setAction("Media::saveFile_" . $matches[1] ."_" . $matches[2]);
        $text = $wh->lang->getParam("mediaPleaseSend");
        $wh->bot->sendMessage(Settings::param('admin_bot'), $text);
    }

    /**
     * @param $wh WebHook
     */
    public static function saveFile($wh)
    {
        self::beforeAction($wh);
        if (
            $wh->bot->isPhoto()
            || $wh->bot->isVideo()
            || $wh->bot->isDocument()
        ) {
            $params = Helper::paramsFromText($wh->user->getAction());
            $wh->user->setAction("");
            $model = new Files();
            $model->type = $wh->bot->getMessageType();
            $model->file_id = $wh->bot->getMessageFileId();
            $model->parent = self::$type[$params[1]];
            $model->key_name = $params[2];
            $text = $model->save()
                ? $wh->lang->getParam("mediaSuccessfulSend")
                : $wh->lang->getParam("mediaErrorSend");
        } else {
            $text = $wh->lang->getParam("mediaWrongFormat");
        }
        $wh->bot->sendMessage(Settings::param('admin_bot'), $text);
    }

    /** Просмотр файла для админки
     * @param $wh WebHook
     */
    public static function viewFile($wh)
    {
        self::beforeAction($wh);
        preg_match("~^\/start mv([\w]{1})([\w]{16})$~", $wh->bot->getText(), $matches);
        $type = self::$type[$matches[1]];
        if($type == "worksheet") {
            $model = Files::findByKey($matches[2], $type);
        } elseif ($type == "result") {
            $model = ResultItems::findByHash($matches[2]);
        }
        if ($model) {
            $method = "send" . ucfirst($model->type);
            if($model->type == "location") {
                $data = json_decode($model->body, 1);
                $wh->bot->$method(Settings::param('admin_bot'), $data['latitude'], $data['longitude']);
            } else {
                $wh->bot->$method(Settings::param('admin_bot'), $model->file_id);
            }
        } else {
            $wh->bot->sendMessage(Settings::param('admin_bot'), $wh->lang->getParam("mediaFileNotFound"));
        }
    }


}
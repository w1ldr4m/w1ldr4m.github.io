<?php

abstract class Start
{
    /**
     * @param $wh WebHook
     */
    public static function run($wh)
    {
        $wh->bot->sendMessage(
            $wh->user->telegram_id,
            $wh->lang->getParam(
                "startHello",
                [
                    "name" => $wh->user->getFullName()
                ]
            )
        );
    }
}
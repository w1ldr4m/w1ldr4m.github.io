<?php

/**
 * Class WebHook
 * @property Users $user
 * @property Bot $bot
 * @property Lang $lang
 * @property Route $route
 */
class WebHook
{
    /**
     * @var bool|Users
     */
    public $user;
    /**
     * @var Bot
     */
    public $bot;
    /**
     * @var Lang
     */
    public $lang;

    /**
     * Инициализируем работу класса
     */
    public function __construct()
    {
        $this->bot = new Bot();
        $this->user = Users::login($this->bot);
        if(Config::debug('access')) { FileLog::access($this->bot); }
        $this->user->userLang($this);
    }

    /**
     * @param $webhook WebHook
     */
    public static function start($webhook)
    {
        Start::run($webhook);
    }

    /** Задаем объекту lang
     * @param $lang Lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }
}
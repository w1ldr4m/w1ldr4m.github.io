<?php

class Controller
{
    public $title = "Title";
    public $breadcrumbs = [];

    /**
     * @param $template
     * @param array $data
     * @return false|string
     */
    public function render($template, $data = [])
    {
        $file_name = $this->filename($template);
        if (file_exists($file_name)) {
            extract($data);
            ob_start();
            include($file_name);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
    }

    /**
     * @param $template
     * @return string
     */
    private function filename($template)
    {
        return __DIR__ . "/../template/" . $template . ".php";
    }

    /**
     * @param $type
     * @param $message
     */
    public function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'text' => $message
        ];
    }

    /**
     * @param $status
     * @param $message
     */
    public function error($status, $message)
    {
        exit($this->render("error", [
            "num" => $status,
            "message" => $message
        ]));
    }

    /**
     * @param $url
     */
    public function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }

    /**
     *
     */
    public function goBack()
    {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    /**
     * @return bool
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
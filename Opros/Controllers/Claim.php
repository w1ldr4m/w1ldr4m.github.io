<?php

abstract class Claim
{
    /**
     * @var array
     */
    public static $allow = [
        'text' => ['all', 'text', 'location'],
        'photo' => ['all', 'media', 'photo'],
        'video' => ['all', 'media', 'video'],
        'document' => ['all', 'media', 'document'],
        'location' => ['location'],
        'sticker' => [],
        'audio' => [],
        'animation' => [],
        'voice' => [],
        'video_note' => [],
    ];

    /** Выводим анкету
     * @param $wh WebHook
     */
    public static function start($wh)
    {
        $buttons = null;
        $text = $wh->lang->getParam("error");
        if (preg_match("~^\/start worksheet([\w]{8})$~", $wh->bot->getText(), $params)) {
            $worksheet = Worksheets::findByHash($params[1]);
            if ($worksheet) {
                $buttons[][] = $wh->bot->buildInlineKeyboardButton($wh->lang->getParam("startWorksheetBtn"), "Claim::step_" . $worksheet->id);
                $text = $wh->lang->getParam("startWorksheet", ["worksheetName" => $worksheet->name]);
            }
        }
        $wh->bot->sendMessage($wh->user->telegram_id, $text, $buttons);
    }

    /** Выводим шаг
     * @param $wh WebHook
     * @param null $id_worksheet
     * @param null $id_result
     * @param null $id_step
     */
    public static function step($wh, $id_worksheet = null, $id_result = null, $id_step = null)
    {
        $wh->bot->noticeDelete();
        if ($wh->bot->isCallBack() && is_null($id_result)) {
            // [1] - id анкеты
            // [2] - id заявки
            // [3] - id выводимого шага
            $params = Helper::params($wh->bot);
            $id_worksheet = $params[1];
            $id_result = !empty($params[2]) ? $params[2] : null;
            $id_step = !empty($params[3]) ? $params[3] : null;
        }
        if (is_null($id_result)) {
            $result = new Results();
            $result->worksheet_id = $id_worksheet;
            $result->user_id = $wh->user->telegram_id;
            $result->create_at = date("Y-m-d H:i:s");
            $result->status = "new";
            $result->hash = mb_strcut(md5(rand(0, 1000000)), 0, 8);
            $newResult = $result->save();
            if ($newResult) {
                $id_result = $newResult->id;
            } else {
                self::start($wh);
                exit();
            }
        }
        $step = is_null($id_step)
            ? WorksheetSteps::firstWorksheetStep($id_worksheet)
            : WorksheetSteps::findById($id_step);
        if ($step) {
            $wh->user->setAction("Claim::saveStep_" . $id_worksheet . "_" . $id_result . "_" . $step->id);
            $oldAnswer = ResultItems::answer($wh->user->telegram_id, $id_result, $step->id);
            if ($oldAnswer) {
                if ($oldAnswer->type == "text") {
                    $val_s = $oldAnswer->body;
                } elseif ($oldAnswer->type == "location") {
                    $val_s = $wh->lang->getParam("answerLocation", ['hash' => $oldAnswer->hash]);
                } else {
                    $val_s = $wh->lang->getParam("stepOldAnswerType", ["type" => Files::$typeMedia[$oldAnswer->type]]);
                }
                $oldAnswerText = $wh->lang->getParam("stepOldAnswer", ["answer" => $val_s]);
            } else {
                $oldAnswerText = "";
            }
            $text = str_replace("<br>", "\n", $step->user_body) . $oldAnswerText;
            $answer_from_btn = null;
            $prepareBtns = $step->getBtns();
            if (count($prepareBtns)) {
                foreach ($prepareBtns as $btn) {
                    $buttons[][] = $wh->bot->buildInlineKeyboardButton(
                        $btn->name,
                        "Claim::saveStep_" . $id_worksheet . "_" . $id_result . "_" . $step->id . "_" . $btn->id
                    );
                    if ($oldAnswer && $btn->name == $oldAnswer->body) {
                        $answer_from_btn = $btn->id;
                    }
                }
            }
            if ($oldAnswer) {
                if (!in_array($oldAnswer->type, ['text', 'location'])) {
                    $buttons[][] = $wh->bot->buildInlineKeyboardButton(
                        $wh->lang->getParam("showFileBtn"),
                        "Claim::viewFile_" . $id_worksheet . "_" . $id_result . "_" . $step->id . "_" . $oldAnswer->id
                    );
                }
                $buttons[][] = $wh->bot->buildInlineKeyboardButton(
                    $wh->lang->getParam("skipBtn"),
                    "Claim::skip_" . $id_worksheet . "_" . $id_result . "_" . $step->id . "_" . $oldAnswer->id . "_" . $answer_from_btn
                );
            }
            $prevStep = ResultItems::lastStep($wh->user->telegram_id, $id_result);
            $goBackBtn = ($step->isParentWorksheet() && !$prevStep)
                ? $wh->lang->getParam("cancelBtn")
                : $wh->lang->getParam("goBackBtn");
            $buttons[][] = $wh->bot->buildInlineKeyboardButton(
                $goBackBtn,
                "Claim::goBack_" . $id_worksheet . "_" . $id_result . "_" . $step->id
            );
            if ($step->type == "message") {
                $wh->bot->sendMessage($wh->user->telegram_id, $text, $buttons);
            } else {
                $method = "send" . ucfirst($step->type);
                $file = Files::findByKey($step->file_id, 'worksheet');
                $wh->bot->$method($wh->user->telegram_id, $file->file_id, $text, $buttons);
            }
        } else {
            $wh->bot->sendMessage($wh->user->telegram_id, $wh->lang->getParam("error"));
        }
    }

    /**
     * @param $wh WebHook
     * @throws Throwable
     */
    public static function saveStep($wh)
    {
        $wh->bot->noticeDelete();
        // [1] - id_worksheet
        // [2] - id_result
        // [3] - id_step
        // [4] - id_btn
        $params = !$wh->bot->isCallBack()
            ? Helper::paramsFromText($wh->user->getAction())
            : Helper::params($wh->bot);
        $step = WorksheetSteps::findById($params[3]);
        //
        $btn = null;
        if ($wh->bot->isCallBack() && in_array($step->expect, ['all', 'text', 'location'])) {
            /**
             * @var $btn WorksheetStepBtns
             */
            $btn = WorksheetStepBtns::findById($params[4]);
            $answer = $btn->name;
            $file_id = null;
            $type = "text";
        } else {
            if ($step->innerOnBtnClickCount()) {
                $wh->bot->sendMessage($wh->user->telegram_id, $wh->lang->getParam("selectVariableOnBtn"));
                exit();
            } else {
                $type = $wh->bot->getMessageType();
                if (in_array($step->expect, self::$allow[$type])) {
                    if ($step->expect == "location") {
                        if ($type == "location") {
                            $answer = $wh->bot->getText();
                        } else {
                            $answer = Helper::pl_truncate($wh->bot->getText(), 1000);
                            $type = "text";
                        }
                        $file_id = null;
                    } else {
                        $answer = ($type == "text") ? Helper::pl_truncate($wh->bot->getText(), 1000) : null;
                        $file_id = ($type == "text") ? null : $wh->bot->getMessageFileId();
                    }
                } else {
                    $wh->bot->sendMessage($wh->user->telegram_id, $wh->lang->getParam("wrongFormat"));
                    exit();
                }
            }
        }
        $statementItem = ResultItems::add($params[2], $params[3], $wh->user->telegram_id, $step->preview_body, $type, $answer, $file_id);
        if ($statementItem) {
            self::goNext($wh, $btn, $params[1], $params[2], $params[3]);
        } else {
            $wh->bot->sendMessage($wh->user->telegram_id, $wh->lang->getParam("error"));
        }
    }

    /** Выводим следующий шаг
     * @param $wh WebHook
     * @param $btn null | WorksheetStepBtns
     * @param $id_worksheet
     * @param $id_result
     * @param $id_step
     */
    public static function goNext($wh, $btn, $id_worksheet, $id_result, $id_step)
    {
        if ($btn) {
            $nextStep = $btn->getChild();
            if ($nextStep) {
                if ($nextStep->group_list) {
                    $step = WorksheetSteps::firstStep($nextStep->id);
                } else {
                    $step = $nextStep;
                }
            }
        }
        if (!$step) {
            $step = WorksheetSteps::getNextStep($id_step);
        }
        if (!$step) {
            self::previewResult($wh, $id_worksheet, $id_result, $id_step);
        } else {
            self::step($wh, $id_worksheet, $id_result, $step->id);
        }
    }

    /** Возвращаем на шаг назад
     * @param $wh WebHook
     */
    public static function goBack($wh)
    {
        // [1] - id_worksheet
        // [2] - id_result
        // [3] - id_step текущий
        $params = Helper::params($wh->bot);
        $step = WorksheetSteps::findById($params[3]);
        if ($step) {
            $prevStep = ResultItems::lastStep($wh->user->telegram_id, $params[2]);
            if ($step->isParentWorksheet() && !$prevStep) {
                self::askToChangeWorksheet($wh);
            } else {
                ResultItems::relevantAllNextAnswers($wh->user->telegram_id, $params[2], $prevStep->id);
                self::step($wh, $params[1], $params[2], $prevStep->step_id);
            }
        } else {
            $wh->bot->notice($wh->lang->getParam("errorStep"));
        }
    }

    /** Переходим на последний заполненный шаг с превью экрана
     * @param $wh WebHook
     */
    public static function goBackFromPreview($wh)
    {
        // [1] - id_worksheet
        // [2] - id_result
        // [3] - id_step
        $params = Helper::params($wh->bot);
        $step = WorksheetSteps::findById($params[3]);
        if ($step) {
            $oldAnswer = ResultItems::answer($wh->user->telegram_id, $params[2], $step->id);
            ResultItems::relevantAllNextAnswers($wh->user->telegram_id, $params[2], $oldAnswer->id);
            self::step($wh, $params[1], $params[2], $oldAnswer->step_id);
        } else {
            $wh->bot->notice($wh->lang->getParam("errorStep"));
        }
    }

    /** Редактируем заявку
     * @param $wh WebHook
     * @param null $id_worksheet
     * @param null $id_result
     */
    public static function editClaim($wh, $id_worksheet = null, $id_result = null)
    {
        // [1] - id_worksheet
        // [2] - id_result
        $params = Helper::params($wh->bot);
        $id_worksheet = !is_null($id_worksheet) ? $id_worksheet : $params[1];
        $id_result = !is_null($id_result) ? $id_result : $params[2];
        ResultItems::relevantAllAnswers($wh->user->telegram_id, $id_result);
        self::step($wh, $id_worksheet, $id_result);
    }

    /**
     * @param $wh WebHook
     * @throws Throwable
     */
    public static function skip($wh)
    {
        // [1] - id_worksheet
        // [2] - id_result
        // [3] - id_step
        // [4] - id_answer
        // [5] - id_btn - может не быть
        $params = Helper::params($wh->bot);
        /**
         * @var $oldAnswer ResultItems
         */
        $oldAnswer = ResultItems::findById($params[4]);
        $resultItem = ResultItems::add(
            $oldAnswer->result_id,
            $oldAnswer->step_id,
            $oldAnswer->user_id,
            $oldAnswer->preview,
            $oldAnswer->type,
            $oldAnswer->body,
            $oldAnswer->file_id
        );
        if ($resultItem) {
            $wh->bot->noticeDelete();
        } else {
            $wh->bot->notice($wh->lang->getParam("errorStep"));
        }
        $nextStep = ResultItems::nextStep($wh->user->telegram_id, $params[2], $params[4]);
        if ($nextStep) {
            self::step($wh, $params[1], $params[2], $nextStep->step_id);
        } else {
            /**
             * @var $btn WorksheetStepBtns
             */
            $btn = WorksheetStepBtns::findById($params[5]);
            self::goNext($wh, $btn, $params[1], $params[2], $params[3]);
        }
    }

    /** Запрос на отмену
     * @param $wh WebHook
     */
    public static function askToChangeWorksheet($wh)
    {
        $wh->bot->noticeDelete();
        // [1] - id_worksheet
        // [2] - id_result
        // [3] - id_step
        // [4] - type
        $params = Helper::params($wh->bot);
        $callbackToNo = !(int)$params[4] ? "step" : "previewResult";
        $buttons = [
            [
                $wh->bot->buildInlineKeyboardButton($wh->lang->getParam("yesBtn"), "Claim::deleteResult_" . $params[2]),
                $wh->bot->buildInlineKeyboardButton($wh->lang->getParam("noBtn"), "Claim::" . $callbackToNo . "_" . $params[1] . "_" . $params[2] . "_" . $params[3]),
            ],
        ];
        $wh->bot->sendMessage(
            $wh->user->telegram_id,
            $wh->lang->getParam("cancelStep"),
            $buttons
        );
    }

    /** Удаление заявки
     * @param $wh WebHook
     */
    public static function deleteResult($wh)
    {
        $params = Helper::params($wh->bot);
        $result = Results::findById($params[1]);
        if ($result->delSelf()) {
            $wh->bot->noticeDelete($wh->lang->getParam("cancelSuccess"));
            Start::run($wh);
        } else {
            $wh->bot->notice($wh->lang->getParam("cancelError"));
        }
    }

    /** Просмотр файла
     * @param $wh WebHook
     */
    public static function viewFile($wh)
    {
        // [1] - id_worksheet
        // [2] - id_result
        // [3] - id_step
        // [4] - id_answer
        $params = Helper::params($wh->bot);
        $model = ResultItems::findById($params[4]);
        if ($model) {
            $wh->bot->noticeDelete();
            $buttons[][] = $wh->bot->buildInlineKeyboardButton(
                $wh->lang->getParam("goBackBtn"),
                "Claim::step_" . $params[1] . "_" . $params[2] . "_" . $params[3]);
            $method = "send" . ucfirst($model->type);
            $wh->bot->$method(
                $wh->user->telegram_id,
                $model->file_id,
                $wh->lang->getParam("showFileStep")
                . str_replace("<br>", "\n", Helper::prepareText($model->preview)),
                $buttons
            );
        } else {
            $wh->bot->notice($wh->user->telegram_id, $wh->lang->getParam("fileNotFound"));
        }
    }

    /** Предпросмотр заявки
     * @param $wh WebHook
     * @param $id_worksheet
     * @param $id_result
     * @param $id_step - последнего шага
     */
    public static function previewResult($wh, $id_worksheet = null, $id_result = null, $id_step = null)
    {
        if ($wh->bot->isCallBack()) {
            $wh->bot->noticeDelete();
            // [1] - id_form
            // [2] - id_order
            // [3] - id_step
            $params = Helper::params($wh->bot);
            $id_worksheet = $params[1];
            $id_result = $params[2];
            $id_step = $params[3];
        }
        /**
         * @var $result Results
         * @var $worksheet Worksheets
         * @var $answers ResultItems[]
         */
        $wh->user->setAction("");
        $result = Results::findById($id_result);
        $worksheet = Worksheets::findById($result->worksheet_id);
        $items_text = "";
        $answers = ResultItems::getAllAnswer($wh->user->telegram_id, $result->id);
        foreach ($answers as $answer) {
            if ($answer->type == "text") {
                $body = $answer->body;
            } elseif ($answer->type == "location") {
                $body = $wh->lang->getParam("answerLocation", ['hash' => $answer->hash]);
            } else {
                $body = $wh->lang->getParam("stepAnswerType", ["type" => Files::$typeMedia[$answer->type]]);;
            }
            $items_text .= "<b>" . $answer->preview . ":</b> " . $body . "\n";
        }
        $text = $wh->lang->getParam("previewBody", ["worksheetName" => $worksheet->name, "items" => str_replace("<br>", "\n", Helper::prepareText($items_text))]);
        if (count(ResultItems::getAllMediaAnswer($wh->user->telegram_id, $result->id))) {
            $buttons[][] = $wh->bot->buildInlineKeyboardButton(
                $wh->lang->getParam("showFilesBtn"),
                "Claim::viewFiles_" . $id_worksheet . "_" . $result->id . "_" . $id_step
            );
        }
        $buttons[][] = $wh->bot->buildInlineKeyboardButton(
            $wh->lang->getParam("goBackBtn"),
            "Claim::goBackFromPreview_" . $id_worksheet . "_" . $result->id . "_" . $id_step
        );
        $buttons[][] = $wh->bot->buildInlineKeyboardButton(
            $wh->lang->getParam("sendBtn"),
            "Claim::send_" . $id_worksheet . "_" . $result->id . "_" . $id_step
        );
        $buttons[][] = $wh->bot->buildInlineKeyboardButton(
            $wh->lang->getParam("editBtn"),
            "Claim::editClaim_" . $id_worksheet . "_" . $result->id
        );
        $buttons[][] = $wh->bot->buildInlineKeyboardButton(
            $wh->lang->getParam("cancelBtn"),
            "Claim::askToChangeWorksheet_" . $id_worksheet . "_" . $result->id . "_" . $id_step . "_1"
        );
        $wh->bot->sendMessage($wh->user->telegram_id, $text, $buttons);
    }

    /** Просматриваем файлы заявки
     * @param $wh WebHook
     */
    public static function viewFiles($wh)
    {
        // [1] - id_worksheet
        // [2] - id_result
        // [3] - id_step
        // [4] - current
        // [5] - switch ?
        $params = Helper::params($wh->bot);
        $current = !empty($params[4]) ? $params[4] : 0;
        $edit = (int)$params[5];
        $totalFiles = ResultItems::getAllMediaAnswerCount($wh->user->telegram_id, $params[2]);
        if ($totalFiles > 1) {
            if ($current) {
                $buttons[0][] = $wh->bot->buildInlineKeyboardButton(
                    "<<",
                    "Claim::viewFiles_" . $params[1] . "_" . $params[2] . "_" . $params[3] . "_" . ($current - 1) . "_1"
                );
            }
            if (($current + 1) < $totalFiles) {
                $buttons[0][] = $wh->bot->buildInlineKeyboardButton(
                    ">>",
                    "Claim::viewFiles_" . $params[1] . "_" . $params[2] . "_" . $params[3] . "_" . ($current + 1) . "_1"
                );
            }
        }
        $file = ResultItems::getFileToView($wh->user->telegram_id, $params[2], $current);
        if ($file) {
            $buttons[][] = $wh->bot->buildInlineKeyboardButton(
                $wh->lang->getParam("goBackBtn"),
                "Claim::previewResult_" . $params[1] . "_" . $params[2] . "_" . $params[3]
            );
            $text = str_replace("<br>", "\n", Helper::prepareText($file->preview));
            $text .= $wh->lang->getParam("fileIs", ["current" => ($current + 1), "total" => $totalFiles]);
            $wh->bot->notice();
            if (!$edit) {
                $wh->bot->deleteMessageSelf();
                $method = "send" . ucfirst($file->type);
                $wh->bot->$method($wh->user->telegram_id, $file->file_id, $text, $buttons);
            } else {
                $wh->bot->editMessageMedia(
                    $wh->user->telegram_id,
                    $wh->bot->getMessageId(),
                    $wh->bot->inputMedia($file->file_id, $file->type, $text),
                    $buttons
                );
            }
        } else {
            $wh->bot->notice($wh->lang->getParam("fileNotFound"));
        }
    }

    /**
     * @param $wh WebHook
     */
    public static function send($wh)
    {
        // [1] - id_worksheet
        // [2] - id_result
        // [3] - id_step
        $params = $wh->bot->isCallBack()
            ? Helper::params($wh->bot)
            : Helper::paramsFromText($wh->user->getAction());
        $order = Results::findById($params[2]);
        $wh->user->setAction("");
        if ($order->setStatus("successful")) {
            $wh->bot->noticeDelete($wh->lang->getParam("successfulSend"), true);
            Start::run($wh);
        } else {
            $wh->bot->notice($wh->lang->getParam("errorSend"));
        }
    }

    /**
     * @param $wh WebHook
     */
    public static function showLocation($wh)
    {
        $wh->bot->deleteMessageSelf();
        if (preg_match("~^\/geo_([\w]{16})$~", $wh->bot->getText(), $params)) {
            $item = ResultItems::findByHash($params[1]);
            if ($item) {
                $data = json_decode($item->body);
                $buttons[][] = $wh->bot->buildInlineKeyboardButton(
                    $wh->lang->getParam("closeBtn"),
                    "Claim::close_0"
                );
                $wh->bot->sendLocation($wh->user->telegram_id, $data->latitude, $data->longitude, $buttons);
                exit();
            }
        }
        $wh->bot->sendMessage($wh->user->telegram_id, $wh->lang->getParam("modelNotFound"));
    }

    /**
     * @param $wh WebHook
     */
    public static function close($wh)
    {
        $wh->bot->noticeDelete();
    }

}
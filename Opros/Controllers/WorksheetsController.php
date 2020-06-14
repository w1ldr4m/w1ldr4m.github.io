<?php

class WorksheetsController extends Controller
{
    public $menuPoint = "worksheets";

    public function index()
    {
        echo $this->render("worksheets/index", [
            'worksheets' => Worksheets::find()->all(" ORDER BY id ASC ")
        ]);
    }

    /**
     * Создаем анкету
     */
    public function createWorksheet()
    {
        $data = [];
        if ($_POST) {
            $data = $_POST;
            if (!empty($data['name'])) {
                $model = Worksheets::add($data['name']);
                if ($model) {
                    $this->setFlash('success', 'Анкета успешно добавлена.');
                    $this->redirect("?a=worksheets::index");
                } else {
                    $this->setFlash('warning', 'Не удалось добавить новую анкету. Попробуйте позже.');
                }
            } else {
                $this->setFlash('danger', 'Не заполнено обязательное поле');
            }
        }
        echo $this->render("worksheets/createWorksheet", ["data" => $data]);
    }

    /**
     * Редактируем анкету
     */
    public function updateWorksheet()
    {
        $model = Worksheets::findById((int)$_GET['id']);
        if ($model) {
            if ($_POST) {
                if (!empty($_POST['name'])) {
                    $model->name = $_POST['name'];
                    if ($model->save()) {
                        $this->setFlash('success', 'Анкета успешно обновлена.');
                        $this->goBack();
                    } else {
                        $this->setFlash('warning', 'Не удалось обновить анкету. Попробуйте позже.');
                    }
                } else {
                    $this->setFlash('danger', 'Не заполнено обязательное поле');
                }
            }
            echo $this->render("worksheets/updateWorksheet", ["worksheet" => $model]);
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     * Меняем видимость анкеты
     */
    public function statusWorksheet()
    {
        $model = Worksheets::findById((int)$_GET['id']);
        if ($model) {
            if (isset($_GET['type'])) {
                if (count($model->getChildren()) || $_GET['type']) {
                    $model->hide = (int)$_GET['type'];
                    if ($model->save()) {
                        $this->setFlash('success', 'Статус анкеты успешно изменен');
                    } else {
                        $this->setFlash('warning', 'Не удалось обновить статус анкеты. Попробуйте позже.');
                    }
                } else {
                    $this->setFlash('warning', 'Вы не можете включить статус Доступный, в форме нет добавленных шагов');
                }
            }
            $this->goBack();
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     * Удаляем анкету
     */
    public function deleteWorksheet()
    {
        $model = Worksheets::findById((int)$_GET['id']);
        if ($model) {
            if ($model->delete()) {
                $model->deleteChildren();
                $this->setFlash('success', 'Анкета успешно удалена');
            } else {
                $this->setFlash('warning', 'Ошибка при удалении записи');
            }
            $this->goBack();
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     * Просмотр анкеты
     */
    public function showWorksheet()
    {
        $model = Worksheets::findById((int)$_GET['id']);
        if ($model) {
            $steps = WorksheetSteps::allByWorksheetId($model->id);
            echo $this->render('worksheets/showWorksheet', [
                'worksheet' => $model,
                'steps' => $steps,
            ]);
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     * Добавляем группу шагов
     */
    public function createGroupStep()
    {
        $parent = WorksheetSteps::findById((int)$_GET['id']);
        if ($parent) {
            $btns_step = WorksheetStepBtns::getAlsoBtnsByIdStep($parent->id);
            if ($_POST) {
                $step = new WorksheetSteps();
                $step->parent_worksheet = $parent->parent_worksheet;
                $step->parent_step = $parent->id;
                $step->parent_btn = (int)$_POST['parent_btn'];
                $step->name = WorksheetStepBtns::findById($step->parent_btn)->name;
                $step->user_body = "group";
                $step->preview_body = "group";
                $step->type = "group";
                $sort = WorksheetSteps::getMaxSort($step->parent_worksheet, $step->parent_step);
                $step->sort = is_null($sort) ? (int)$sort : ($sort + 1);
                $step->group_list = 1;
                $newStep = $step->save();
                if ($newStep) {
                    $this->redirect('?a=worksheets::show-step&id=' . $newStep->id);
                } else {
                    $this->setFlash('warning', 'Ошибка при добавлении. Попробуйте позже.');
                }
            }
            echo $this->render('worksheets/createGroupStep', [
                'parent' => $parent,
                'btns_step' => $btns_step
            ]);
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     *  Добавление шага
     */
    public function createStep()
    {
        $group = isset($_GET['group']) ? (int)$_GET['group'] : 0;
        $type = isset($_GET['type']) ? (int)$_GET['type'] : 0;
        $id = (int)$_GET['id'];
        $parent = $type ? WorksheetSteps::findById($id) : Worksheets::findById($id);
        if ($parent) {
            $buttons = [];
            $data = ['expect' => 'text', 'type' => 'message'];
            $btns_step = $type ? WorksheetStepBtns::getAlsoBtnsByIdStep($parent->id) : [];
            if ($type && !$group && !count($btns_step)) {
                $this->redirect('?a=worksheets::show-step?id=' . $parent->id);
            }
            if ($_POST) {
                $data = $_POST;
                $buttons = isset($_POST['buttons']) ? $_POST['buttons'] : [];
                $stepParent_btn = (int)$_POST['parent_btn'];
                $stepName = $type && !$group ? WorksheetStepBtns::findById($stepParent_btn)->name : Helper::encode($_POST['name']);
                if (empty($stepName) || empty($_POST['user_body']) || empty($_POST['preview_body'])) {
                    $this->setFlash('danger', 'Заполнены не все обязательные поля');
                } else {
                    $step = new WorksheetSteps();
                    $step->type = Helper::encode($_POST['type']);
                    $step->name = $stepName;
                    $step->user_body = $_POST['user_body'];
                    $step->preview_body = Helper::encode($_POST['preview_body']);
                    $step->expect = Helper::encode($_POST['expect']);
                    $step->file_id = Helper::encode($_POST['file_id']);
                    $step->parent_worksheet = $type ? $parent->parent_worksheet : $parent->id;
                    $step->parent_step = $type ? $parent->id : null;
                    $step->parent_btn = $type ? $stepParent_btn : null;
                    $sort = WorksheetSteps::getMaxSort($step->parent_worksheet, $step->parent_step);
                    $step->sort = is_null($sort) ? (int)$sort : ($sort + 1);
                    $step->user_body = Helper::pl_truncate(Helper::prepareText($step->user_body), $step->type == "message" ? 4096 : 1024);
                    $newStep = $step->save();
                    if ($newStep) {
                        if (count($buttons)) {
                            $sort_num = 0;
                            foreach ($buttons as $btn) {
                                $button = new WorksheetStepBtns();
                                $button->parent_worksheet = $type ? $parent->parent_worksheet : $parent->id;
                                $button->parent_step = $newStep->id;
                                $button->name = $btn;
                                $button->sort = $sort_num;
                                $button->save();
                                $sort_num += 1;
                            }
                        }
                        $this->redirect('?a=worksheets::update-step&id=' . $newStep->id);
                    } else {
                        if (!empty($step->file_id)) {
                            $file = Files::findByFileId($step->file_id, 'worksheet');
                        }
                        $this->setFlash('warning', 'Ошибка при добавлении. Попробуйте позже.');
                    }
                }
            }
            echo $this->render('worksheets/createStep', [
                'parent' => $parent,
                'buttons' => $buttons,
                'file' => !$file ? "" : $file,
                'btns_step' => $btns_step,
                'data' => $data
            ]);
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     * Проверяем добавление медиа-файла
     */
    public function checkMedia()
    {
        if ($this->isAjax()) {
            $key = $_GET['key'];
            $model = Files::findByKey($key);
            if ($model) {
                $result = [
                    'result' => 'success',
                    'type_string' => Files::$typeMedia[$model->type],
                    'type' => $model->type,
                    'key_name' => $model->key_name
                ];
            } else {
                $result = ['result' => 'error'];
            }
            echo json_encode($result);
            exit();
        }
    }

    /**
     * Редактирование шага
     */
    public function updateStep()
    {
        $id = (int)$_GET['id'];
        /**
         * @var $step WorksheetSteps
         */
        $step = WorksheetSteps::findById($id);
        if ($step) {
            $buttons_old = WorksheetStepBtns::getAllStepBtnArr($step->id);
            $file = Files::findByKey($step->file_id, 'worksheet');
            if ($_POST) {
                $step->user_body = $_POST['user_body'];
                $step->preview_body = Helper::encode($_POST['preview_body']);
                $step->expect = Helper::encode($_POST['expect']);
                $step->file_id = Helper::encode($_POST['file_id']);
                $step->type = Helper::encode($_POST['type']);
                $buttons = isset($_POST['buttons']) ? $_POST['buttons'] : [];
                $step->name = isset($_POST['parent_btn'])
                    ? WorksheetStepBtns::findById((int)$_POST['parent_btn'])->name
                    : Helper::encode($_POST['name']);
                if (empty($step->name) || empty($_POST['user_body']) || empty($_POST['preview_body'])) {
                    $this->setFlash('danger', 'Заполнены не все обязательные поля');
                } else {
                    $step->parent_btn = isset($_POST['parent_btn'])
                        ? (int)$_POST['parent_btn']
                        : null;
                    $step->user_body = Helper::pl_truncate(Helper::prepareText($step->user_body), $step->type == "message" ? 4096 : 1024);
                    if ($step->save()) {
                        $keys_buttons_post = array_keys($buttons);
                        $keys_buttons_old = array_keys($buttons_old);
                        $buttons_to_add_in_bd = array_diff($keys_buttons_post, $keys_buttons_old);
                        $buttons_to_remove_from_bd = array_diff($keys_buttons_old, $keys_buttons_post);
                        if (count($buttons_to_add_in_bd)) {
                            foreach ($buttons_to_add_in_bd as $b_add_in_bd) {
                                $button = new WorksheetStepBtns();
                                $button->parent_worksheet = $step->parent_worksheet;
                                $button->parent_step = $id;
                                $button->name = $buttons[$b_add_in_bd];
                                $button->save();
                            }
                        }
                        if (count($buttons_to_remove_from_bd)) {
                            foreach ($buttons_to_remove_from_bd as $id) {
                                $button = WorksheetStepBtns::findById($id);
                                $button->deleteSelf();
                            }
                            $old_buttons_lost = array_diff($keys_buttons_old, $buttons_to_remove_from_bd);
                            if (count($old_buttons_lost) == 1 && !count($buttons_to_add_in_bd)) {
                                $button_old = WorksheetStepBtns::findById($old_buttons_lost[0]);
                                /**
                                 * @var $child WorksheetSteps
                                 */
                                $child = $button_old->getChild();
                                if ($child) {
                                    $child->deleteSelf();
                                }
                            }
                        }
                        if (count($buttons)) {
                            $num_btn_sort = 0;
                            foreach ($buttons as $item_val) {
                                $btn_s = WorksheetStepBtns::findByName($id, $item_val);
                                if ($btn_s) {
                                    $btn_s->setSort($num_btn_sort);
                                    $num_btn_sort += 1;
                                }
                            }
                        }
                        $this->redirect('?a=worksheets::update-step&id=' . $step->id);
                    } else {
                        $this->setFlash('warning', 'Ошибка при редактировании. Попробуйте позже');
                    }
                }
            } else {
                $buttons = $buttons_old;
            }
            echo $this->render('worksheets/updateStep', [
                'parent' => $step->getParent(),
                'data' => (array)$step,
                'buttons' => $buttons,
                'file' => !$file ? "" : $file
            ]);
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     * Удаляем шаг
     */
    public function deleteStep()
    {
        $id = (int)$_GET['id'];
        $model = WorksheetSteps::findById($id);
        if ($model) {
            $parent = $model->getParent();
            $url = get_class($parent) == "WorksheetSteps" ? "step" : "worksheet";
            if ($model->deleteSelf()) {
                $this->setFlash('success', 'Запись удалена');
            } else {
                $this->setFlash('warning', 'Ошибка при удалении записи');
            }
            $this->redirect('?a=worksheets::show-' . $url . '&id=' . $parent->id);
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     *  Просмотр шага
     */
    public function showStep()
    {
        $id = (int)$_GET['id'];
        $model = WorksheetSteps::findById($id);
        if ($model) {
            echo $this->render('worksheets/showStep', [
                'model' => $model,
                'steps' => WorksheetSteps::allByStepId($model->id)
            ]);
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     * Просмотр результатов
     */
    public function resultWorksheet()
    {
        $worksheet = Worksheets::findById((int)$_GET['id']);
        /**
         * @var $worksheet Worksheets
         */
        if($worksheet) {
            echo $this->render('worksheets/resultWorksheet', [
                'worksheet' => $worksheet,
                'results' => $worksheet->getItems()
            ]);
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     * Просмотр результата
     */
    public function resultWorksheetShow()
    {
        $model = Results::findById((int)$_GET['id']);
        if($model) {
            echo $this->render('worksheets/resultWorksheetShow', [
                'result' => $model,
                'items' => $model->getItems()
            ]);
        } else {
            $this->error("404", "Модель не найдена");
        }
    }

    /**
     * Сортируем
     */
    public function sort()
    {
        $items = $_POST['items'];
        foreach ($items as $key => $id) {
            $model = WorksheetSteps::findById($id);
            if ($model) {
                $model->sort = $key;
                $model->save();
            }
        }
    }
}
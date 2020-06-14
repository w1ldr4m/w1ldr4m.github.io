<form action="" method="post">
    <div class="row">
        <?php
        if ($url == "step" && !$parent->group_list && !$update): ?>
            <div class="input-field col s12">
                <select required name="parent_btn">
                    <? foreach ($btns_step as $key_btn => $value_btn): ?>
                        <option value="<?= $key_btn; ?>"><?= $value_btn; ?></option>
                    <? endforeach; ?>
                </select>
                <label>Выводиться по кнопке родителя<span style="color: red;"><sup>*</sup></span></label>
            </div>
        <? else: ?>
            <div class="input-field col s12">
                <? if (isset($data['parent_btn'])): ?>
                    <input type="hidden" name="parent_btn" value="<?= $data['parent_btn']; ?>">
                <? endif; ?>
                <input required
                       id="worksheetStep-name"
                       type="text"
                       class="validate"
                       name="name"
                       value="<?= $data['name']; ?>" <?= (!is_null($data['parent_step']) && !$parent->group_list) ? "disabled" : ""; ?>>
                <label for="worksheetStep-name">Название шага<span
                        style="color: red;"><sup>*</sup></span></label>
            </div>
        <? endif; ?>
    </div>

    <div class="row">
        <div class="input-field col s12">
                            <textarea required
                                      id="worksheetStep-user_body"
                                      type="text"
                                      class="validate materialize-textarea"
                                      name="user_body"><?= $data['user_body']; ?></textarea>
            <label for="worksheetStep-user_body">Инструкция для пользователя<span
                    style="color: red;"><sup>*</sup></span></label>
            <span class="helper-text"><a href="#" class="modal-trigger" data-toggle="modal-formal"
                                         data-target="preformated">Пример HTML форматирования</a> | Для текстового сообщения 4096 знаков, для медиа сообщения 1024 знаков - включая форматирование (теги), обрезается автоматически</span>
        </div>
    </div>
    <div id="preformated" class="modal">
        <div class="modal-content">
            <h5>Пример HTML форматирования</h5>
            <div class="modal-body">
                        <pre><code>&lt;b&gt;Полужирный&lt;/b&gt;, &lt;strong&gt;Полужирный&lt;/strong&gt;
&lt;i&gt;Наклонный&lt;/i&gt;, &lt;em&gt;Наклонный&lt;/em&gt;
&lt;u&gt;Подчеркнутый&lt;/u&gt;, &lt;ins&gt;Подчеркнутый&lt;/ins&gt;
&lt;s&gt;Зачеркнутый&lt;/s&gt;, &lt;strike&gt;Зачеркнутый&lt;/strike&gt;, &lt;del&gt;Зачеркнутый&lt;/del&gt;
&lt;b&gt;Полужирный &lt;i&gt;Наклонный Полужирный &lt;s&gt;Наклонный Полужирный Зачеркнутый&lt;/s&gt; &lt;u&gt;Подчеркнутый Наклонный Полужирный&lt;/u&gt;&lt;/i&gt; Полужирный&lt;/b&gt;
&lt;a href="http://www.example.com/"&gt;Ссылка&lt;/a&gt;
&lt;a href="tg://user?id=123456789"&gt;Ссылка на профиль пользователя&lt;/a&gt;
&lt;code&gt;Фрагмент кода строка&lt;/code&gt;
&lt;pre&gt;Фрагмент кода блок&lt;/pre&gt;
&lt;pre&gt;&lt;code class="language-python"&gt;Фрагмент кода блок со стилем python&lt;/code&gt;&lt;/pre&gt;</code></pre>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-small red">Закрыть</a>
        </div>
    </div>
    <div class="row">
        <div class="input-field col s12">
            <input required
                   id="worksheetStep-preview_body"
                   type="text"
                   class="validate"
                   name="preview_body"
                   value="<?= $data['preview_body']; ?>">
            <label for="worksheetStep-preview_body">Превью<span
                    style="color: red;"><sup>*</sup></span></label>
        </div>
    </div>

    <div class="row">
        <?php
        $expects = [
            'text' => 'Результат по кнопке или Текст',
            'all' => 'Любой результат исключая Геолокацию',
            'media' => 'Изображение, Видео-файл или Документ',
            'photo' => 'Изображение',
            'video' => 'Видео-файл',
            'document' => 'Документ',
            'location' => 'Геолокация или текст'
        ];
        ?>
        <div class="input-field col s12">
            <select required name="expect">
                <? foreach ($expects as $key => $expect): ?>
                    <? $selected = $key == $data['expect'] ? "selected" : ""; ?>
                    <option value="<?= $key; ?>" <?= $selected; ?>><?= $expect; ?></option>
                <? endforeach; ?>
            </select>
            <label>Результат ответа<span style="color: red;"><sup>*</sup></span></label>
        </div>
    </div>

    <div class="row">
        <?
        $nameBot = Settings::param("username_bot");
        $sured = isset($data['file_id']) && !empty($data['file_id']);
        $rand_key = $sured ? $data['file_id'] : Helper::generate_string(16);
        ?>
        <div class="col s12">
            <label style="font-size: 1em;">Медиафайл</label>
            <br>
            <span id="wrap_media_file_href"
                  style="display: <? if (!$sured): ?>inline<? else: ?>none<? endif; ?>">
                                    <a href="tg://resolve?domain=<?= $nameBot; ?>&amp;start=mw<?= $rand_key; ?>"
                                       id="upload_file_from_bot"
                                       target="_blank"
                                       data-href="tg://resolve?domain=<?= $nameBot; ?>&amp;start=mw">
                                        Добавить медиафайл
                                    </a>
                            </span>
            <span id="wrap_media_file_info"
                  style="display: <? if ($sured): ?>inline<? else: ?>none<? endif; ?>">
                                    Добавлен медиафайл:
                                    <a id="wrap_media_file_info_type"
                                       href="tg://resolve?domain=<?= $nameBot; ?>&amp;start=mvw<?= $rand_key; ?>"
                                       target="_blank"
                                       data-href="tg://resolve?domain=<?= $nameBot; ?>&amp;start=mvw">
                                        <? if ($data['type'] != "message"): ?><?= Files::$typeMedia[$data['type']]; ?><? endif; ?>
                                    </a>
                                    <a href="#" id="wrap_media_file_info_del" class="media_file" style="color: red;"><i class="material-icons">close</i></a>
                                </span>
        </div>
        <input type="hidden"
               id="key_data"
               data-type="formsteps-type"
               data-file="formsteps-file_id"
               value="<?= $rand_key; ?>">
        <input type="hidden"
               id="formsteps-type"
               name="type"
               value="<?= $data['type']; ?>">
        <input type="hidden"
               id="formsteps-file_id"
               name="file_id"
               value="<?= $data['file_id']; ?>">
        <br>
    </div>

    <div class="row">
        <div class="col s12">
            <label style="font-size: 1em;">Кнопки - варианты ответов</label>
            <br>

            <a href="#!" data-target="button_add_win"
               class="modal-trigger">+ Добавить кнопку
            </a>

            <table class="table" id="wrap_buttons">
                <thead>
                <tr>
                    <th style="width: 5px;"><i class="material-icons">swap_vert</i></th>
                    <th>Кнопка</th>
                    <th style="width: 5px;"><i class="material-icons">delete</i></th>
                </tr>
                </thead>
                <tbody class="sortable_btns ui-sortable">
                <? if (count($buttons)): ?>
                    <? foreach ($buttons as $key => $item): ?>
                        <tr>
                            <td><i class="material-icons icon-sort-btn">swap_vert</i></td>
                            <td><?= Helper::encode($item); ?><input type="hidden"
                                                                    name="buttons[<?= $key; ?>]"
                                                                    value="<?= $item; ?>"></td>
                            <td><i class="material-icons deleteButton">delete</i></td>
                        </tr>
                    <? endforeach; ?>
                <? endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    <div id="button_add_win" class="modal" style="width: 300px; overflow-y: inherit;">
        <div class="modal-content" style="margin-bottom: 0; padding-bottom: 0;">
            <div class="row emoji_wrap">
                <div class="input-field col offset-s1 s10">
                    <input id="new-button" type="text" class="validate">
                    <span class="prefix select-emoji">😀</span>
                    <label for="icon_prefix">Текст кнопки</label>
                </div>
                <div class="select_emoji_wrap" id="buttons-emoji">
                    <div class="emoji_list"><h4 class="text-center">Смайлы и люди</h4>
                        <span>😀</span><span>😃</span><span>😄</span><span>😁</span><span>😆</span><span>😅</span><span>😂</span><span>🤣</span><span>😊</span><span>😇</span><span>🙂</span><span>🙃</span><span>😉</span><span>😌</span><span>😍</span><span>😘</span><span>😗</span><span>😙</span><span>😚</span><span>😋</span><span>😜</span><span>😝</span><span>😛</span><span>🤑</span><span>🤗</span><span>🤓</span><span>😎</span><span>🤡</span><span>🤠</span><span>😏</span><span>😒</span><span>😞</span><span>😔</span><span>😟</span><span>😕</span><span>🙁</span><span>☹️</span><span>😣</span><span>😖</span><span>😫</span><span>😩</span><span>😤</span><span>😠</span><span>😡</span><span>😶</span><span>😐</span><span>😑</span><span>😯</span><span>😦</span><span>😧</span><span>😮</span><span>😲</span><span>😵</span><span>😳</span><span>😱</span><span>😨</span><span>😰</span><span>😢</span><span>😥</span><span>🤤</span><span>😭</span><span>😓</span><span>😪</span><span>😴</span><span>🙄</span><span>🤔</span><span>🤥</span><span>😬</span><span>🤐</span><span>🤢</span><span>🤧</span><span>😷</span><span>🤒</span><span>🤕</span><span>😈</span><span>👿</span><span>👹</span><span>👺</span><span>💩</span><span>👻</span><span>💀</span><span>☠️</span><span>👽</span><span>👾</span><span>🤖</span><span>🎃</span><span>😺</span><span>😸</span><span>😹</span><span>😻</span><span>😼</span><span>😽</span><span>🙀</span><span>😿</span><span>😾</span><span>👐</span><span>🙌</span><span>👏</span><span>🙏</span><span>🤝</span><span>👍</span><span>👎</span><span>👊</span><span>✊</span><span>🤛</span><span>🤜</span><span>🤞</span><span>✌️</span><span>🤘</span><span>👌</span><span>👈</span><span>👉</span><span>👆</span><span>👇</span><span>☝️</span><span>✋</span><span>🤚</span><span>🖐</span><span>🖖</span><span>💄</span><span>💋</span><span>👄</span><span>👅</span><span>👂</span><span>👃</span><span>👣</span><span>👁</span><span>👀</span><span>🗣</span><span>👤</span><span>👥</span><span>👶</span><span>👦</span><span>👧</span><span>👨</span><span>👩</span><span>👱‍♀️</span><span>👱</span><span>👴</span><span>👵</span><span>👲</span><span>👳‍♀️</span><span>👳</span><span>👮‍♀️</span><span>👮</span><span>👷‍♀️</span><span>👷</span><span>💂‍♀️</span><span>💂</span><span>🕵️‍♀️</span><span>🕵️</span><span>👩‍⚕️</span><span>👨‍⚕️</span><span>👩‍🌾</span><span>👨‍🌾</span><span>👩‍🍳</span><span>👨‍🍳</span><span>👩‍🎓</span><span>👨‍🎓</span><span>👩‍🎤</span><span>👨‍🎤</span><span>👩‍🏫</span><span>👨‍🏫</span><span>👩‍🏭</span><span>👨‍🏭</span><span>👩‍💻</span><span>👨‍💻</span><span>👩‍💼</span><span>👨‍💼</span><span>👩‍🔧</span><span>👨‍🔧</span><span>👩‍🔬</span><span>👨‍🔬</span><span>👩‍🎨</span><span>👨‍🎨</span><span>👩‍🚒</span><span>👨‍🚒</span><span>👩‍✈️</span><span>👨‍✈️</span><span>👩‍🚀</span><span>👨‍🚀</span><span>👩‍⚖️</span><span>👨‍⚖️</span><span>🤶</span><span>🎅</span><span>👸</span><span>🤴</span><span>👰</span><span>🤵</span><span>👼</span><span>🤰</span><span>🙇‍♀️</span><span>🙇</span><span>💁</span><span>💁‍♂️</span><span>🙅🏿</span><span>🙅🏿</span><span>🙆</span><span>🙆‍♂️</span><span>🙋</span><span>🙋‍♂️</span><span>🤦‍♀️</span><span>🤦‍♂️</span><span>🤷‍♀️</span><span>🤷‍♂️</span><span>🙎</span><span>🙎‍♂️</span><span>🙍</span><span>🙍‍♂️</span><span>💇</span><span>💇‍♂️</span><span>💆</span><span>💆‍♂️</span><span>🕴</span><span>💃</span><span>🕺</span><span>👯</span><span>👯‍♂️</span><span>🚶‍♀️</span><span>🚶</span><span>🏃‍♀️</span><span>🏃</span><span>👫</span><span>👭</span><span>👬</span><span>💑</span><span>👩‍❤️‍👩</span><span>👨‍❤️‍👨</span><span>💏</span><span>👩‍❤️‍💋‍👩</span><span>👨‍❤️‍💋‍👨</span><span>👪</span><span>👨‍👩‍👧</span><span>👨‍👩‍👧‍👦</span><span>👨‍👩‍👦‍👦</span><span>👨‍👩‍👧‍👧</span><span>👩‍👩‍👦</span><span>👩‍👩‍👧</span><span>👩‍👩‍👧‍👦</span><span>👩‍👩‍👦‍👦</span><span>👩‍👩‍👧‍👧</span><span>👨‍👨‍👦</span><span>👨‍👨‍👧</span><span>👨‍👨‍👧‍👦</span><span>👨‍👨‍👦‍👦</span><span>👨‍👨‍👧‍👧</span><span>👩‍👦</span><span>👩‍👧</span><span>👩‍👧‍👦</span><span>👩‍👦‍👦</span><span>👩‍👧‍👧</span><span>👨‍👦</span><span>👨‍👧</span><span>👨‍👧‍👦</span><span>👨‍👦‍👦</span><span>👨‍👧‍👧</span><span>👚</span><span>👕</span><span>👖</span><span>👔</span><span>👗</span><span>👙</span><span>👘</span><span>👠</span><span>👡</span><span>👢</span><span>👞</span><span>👟</span><span>👒</span><span>🎩</span><span>🎓</span><span>👑</span><span>⛑</span><span>🎒</span><span>👝</span><span>👛</span><span>👜</span><span>💼</span><span>👓</span><span>🕶</span><span>🌂</span><span>☂️</span><br><br>
                        <h5 style="text-align: center">Животные и природа</h5>
                        <span>🐶</span><span>🐱</span><span>🐭</span><span>🐹</span><span>🐰</span><span>🦊</span><span>🐻</span><span>🐼</span><span>🐨</span><span>🐯</span><span>🦁</span><span>🐮</span><span>🐷</span><span>🐽</span><span>🐸</span><span>🐵</span><span>🙈</span><span>🙉</span><span>🙊</span><span>🐒</span><span>🐔</span><span>🐧</span><span>🐦</span><span>🐤</span><span>🐣</span><span>🐥</span><span>🦆</span><span>🦅</span><span>🦉</span><span>🦇</span><span>🐺</span><span>🐗</span><span>🐴</span><span>🦄</span><span>🐝</span><span>🐛</span><span>🦋</span><span>🐌</span><span>🐚</span><span>🐞</span><span>🐜</span><span>🕷</span><span>🕸</span><span>🐢</span><span>🐍</span><span>🦎</span><span>🦂</span><span>🦀</span><span>🦑</span><span>🐙</span><span>🦐</span><span>🐠</span><span>🐟</span><span>🐡</span><span>🐬</span><span>🦈</span><span>🐳</span><span>🐋</span><span>🐊</span><span>🐆</span><span>🐅</span><span>🐃</span><span>🐂</span><span>🐄</span><span>🦌</span><span>🐪</span><span>🐫</span><span>🐘</span><span>🦏</span><span>🦍</span><span>🐎</span><span>🐖</span><span>🐐</span><span>🐏</span><span>🐑</span><span>🐕</span><span>🐩</span><span>🐈</span><span>🐓</span><span>🦃</span><span>🕊</span><span>🐇</span><span>🐁</span><span>🐀</span><span>🐿</span><span>🐾</span><span>🐉</span><span>🐲</span><span>🌵</span><span>🎄</span><span>🌲</span><span>🌳</span><span>🌴</span><span>🌱</span><span>🌿</span><span>☘️</span><span>🍀</span><span>🎍</span><span>🎋</span><span>🍃</span><span>🍂</span><span>🍁</span><span>🍄</span><span>🌾</span><span>💐</span><span>🌷</span><span>🌹</span><span>🥀</span><span>🌻</span><span>🌼</span><span>🌸</span><span>🌺</span><span>🌎</span><span>🌍</span><span>🌏</span><span>🌕</span><span>🌖</span><span>🌗</span><span>🌘</span><span>🌑</span><span>🌒</span><span>🌓</span><span>🌔</span><span>🌚</span><span>🌝</span><span>🌞</span><span>🌛</span><span>🌜</span><span>🌙</span><span>💫</span><span>⭐️</span><span>🌟</span><span>✨</span><span>⚡️</span><span>🔥</span><span>💥</span><span>☄️</span><span>☀️</span><span>🌤</span><span>⛅️</span><span>🌥</span><span>🌦</span><span>🌈</span><span>☁️</span><span>🌧</span><span>⛈</span><span>🌩</span><span>🌨</span><span>☃️</span><span>⛄️</span><span>❄️</span><span>🌬</span><span>💨</span><span>🌪</span><span>🌫</span><span>🌊</span><span>💧</span><span>💦</span><span>☔️</span><br><br>
                        <h5 style="text-align: center">Еда и напитки</h5>
                        <span>🍏</span><span>🍎</span><span>🍐</span><span>🍊</span><span>🍋</span><span>🍌</span><span>🍉</span><span>🍇</span><span>🍓</span><span>🍈</span><span>🍒</span><span>🍑</span><span>🍍</span><span>🥝</span><span>🥑</span><span>🍅</span><span>🍆</span><span>🥒</span><span>🥕</span><span>🌽</span><span>🌶</span><span>🥔</span><span>🍠</span><span>🌰</span><span>🥜</span><span>🍯</span><span>🥐</span><span>🍞</span><span>🥖</span><span>🧀</span><span>🥚</span><span>🍳</span><span>🥓</span><span>🥞</span><span>🍤</span><span>🍗</span><span>🍖</span><span>🍕</span><span>🌭</span><span>🍔</span><span>🍟</span><span>🥙</span><span>🌮</span><span>🌯</span><span>🥗</span><span>🥘</span><span>🍝</span><span>🍜</span><span>🍲</span><span>🍥</span><span>🍣</span><span>🍱</span><span>🍛</span><span>🍚</span><span>🍙</span><span>🍘</span><span>🍢</span><span>🍡</span><span>🍧</span><span>🍨</span><span>🍦</span><span>🍰</span><span>🎂</span><span>🍮</span><span>🍭</span><span>🍬</span><span>🍫</span><span>🍿</span><span>🍩</span><span>🍪</span><span>🥛</span><span>🍼</span><span>☕️</span><span>🍵</span><span>🍶</span><span>🍺</span><span>🍻</span><span>🥂</span><span>🍷</span><span>🥃</span><span>🍸</span><span>🍹</span><span>🍾</span><span>🥄</span><span>🍴</span><span>🍽</span><br><br>
                        <h5 style="text-align: center">Деятельность и спорт</h5>
                        <span>⚽️</span><span>🏀</span><span>🏈</span><span>⚾️</span><span>🎾</span><span>🏐</span><span>🏉</span><span>🎱</span><span>🏓</span><span>🏸</span><span>🥅</span><span>🏒</span><span>🏑</span><span>🏏</span><span>⛳️</span><span>🏹</span><span>🎣</span><span>🥊</span><span>🥋</span><span>⛸</span><span>🎿</span><span>⛷</span><span>🏂</span><span>🏋️‍♀️</span><span>🏋️</span><span>🤺</span><span>🤼‍♀️</span><span>🤼‍♂️</span><span>🤸‍♀️</span><span>🤸‍♂️</span><span>⛹️‍♀️</span><span>⛹️</span><span>🤾‍♀️</span><span>🤾‍♂️</span><span>🏌️‍♀️</span><span>🏌️</span><span>🏄‍♀️</span><span>🏄</span><span>🏊‍♀️</span><span>🏊</span><span>🤽‍♀️</span><span>🤽‍♂️</span><span>🚣‍♀️</span><span>🚣</span><span>🏇</span><span>🚴‍♀️</span><span>🚴</span><span>🚵‍♀️</span><span>🚵</span><span>🎽</span><span>🏅</span><span>🎖</span><span>🥇</span><span>🥈</span><span>🥉</span><span>🏆</span><span>🏵</span><span>🎗</span><span>🎫</span><span>🎟</span><span>🎪</span><span>🤹‍♀️</span><span>🤹‍♂️</span><span>🎭</span><span>🎨</span><span>🎬</span><span>🎤</span><span>🎧</span><span>🎼</span><span>🎹</span><span>🥁</span><span>🎷</span><span>🎺</span><span>🎸</span><span>🎻</span><span>🎲</span><span>🎯</span><span>🎳</span><span>🎮</span><span>🎰</span><br><br>
                        <h5 style="text-align: center">Места и путешествия</h5>
                        <span>🚗</span><span>🚕</span><span>🚙</span><span>🚌</span><span>🚎</span><span>🏎</span><span>🚓</span><span>🚑</span><span>🚒</span><span>🚐</span><span>🚚</span><span>🚛</span><span>🚜</span><span>🛴</span><span>🚲</span><span>🛵</span><span>🏍</span><span>🚨</span><span>🚔</span><span>🚍</span><span>🚘</span><span>🚖</span><span>🚡</span><span>🚠</span><span>🚟</span><span>🚃</span><span>🚋</span><span>🚞</span><span>🚝</span><span>🚄</span><span>🚅</span><span>🚈</span><span>🚂</span><span>🚆</span><span>🚇</span><span>🚊</span><span>🚉</span><span>🚁</span><span>🛩</span><span>✈️</span><span>🛫</span><span>🛬</span><span>🚀</span><span>🛰</span><span>💺</span><span>🛶</span><span>⛵️</span><span>🛥</span><span>🚤</span><span>🛳</span><span>⛴</span><span>🚢</span><span>⚓️</span><span>🚧</span><span>⛽️</span><span>🚏</span><span>🚦</span><span>🚥</span><span>🗺</span><span>🗿</span><span>🗽</span><span>⛲️</span><span>🗼</span><span>🏰</span><span>🏯</span><span>🏟</span><span>🎡</span><span>🎢</span><span>🎠</span><span>⛱</span><span>🏖</span><span>🏝</span><span>⛰</span><span>🏔</span><span>🗻</span><span>🌋</span><span>🏜</span><span>🏕</span><span>⛺️</span><span>🛤</span><span>🛣</span><span>🏗</span><span>🏭</span><span>🏠</span><span>🏡</span><span>🏘</span><span>🏚</span><span>🏢</span><span>🏬</span><span>🏣</span><span>🏤</span><span>🏥</span><span>🏦</span><span>🏨</span><span>🏪</span><span>🏫</span><span>🏩</span><span>💒</span><span>🏛</span><span>⛪️</span><span>🕌</span><span>🕍</span><span>🕋</span><span>⛩</span><span>🗾</span><span>🎑</span><span>🏞</span><span>🌅</span><span>🌄</span><span>🌠</span><span>🎇</span><span>🎆</span><span>🌇</span><span>🌆</span><span>🏙</span><span>🌃</span><span>🌌</span><span>🌉</span><span>🌁</span><br><br>
                        <h5 style="text-align: center">Предметы</h5>
                        <span>⌚️</span><span>📱</span><span>📲</span><span>💻</span><span>⌨️</span><span>🖥</span><span>🖨</span><span>🖱</span><span>🖲</span><span>🕹</span><span>🗜</span><span>💽</span><span>💾</span><span>💿</span><span>📀</span><span>📼</span><span>📷</span><span>📸</span><span>📹</span><span>🎥</span><span>📽</span><span>🎞</span><span>📞</span><span>☎️</span><span>📟</span><span>📠</span><span>📺</span><span>📻</span><span>🎙</span><span>🎚</span><span>🎛</span><span>⏱</span><span>⏲</span><span>⏰</span><span>🕰</span><span>⌛️</span><span>⏳</span><span>📡</span><span>🔋</span><span>🔌</span><span>💡</span><span>🔦</span><span>🕯</span><span>🗑</span><span>🛢</span><span>💸</span><span>💵</span><span>💴</span><span>💶</span><span>💷</span><span>💰</span><span>💳</span><span>💎</span><span>⚖️</span><span>🔧</span><span>🔨</span><span>⚒</span><span>🛠</span><span>⛏</span><span>🔩</span><span>⚙️</span><span>⛓</span><span>🔫</span><span>💣</span><span>🔪</span><span>🗡</span><span>⚔️</span><span>🛡</span><span>🚬</span><span>⚰️</span><span>⚱️</span><span>🏺</span><span>🔮</span><span>📿</span><span>💈</span><span>⚗️</span><span>🔭</span><span>🔬</span><span>🕳</span><span>💊</span><span>💉</span><span>🌡</span><span>🚽</span><span>🚰</span><span>🚿</span><span>🛁</span><span>🛀</span><span>🛎</span><span>🔑</span><span>🗝</span><span>🚪</span><span>🛋</span><span>🛏</span><span>🛌</span><span>🖼</span><span>🛍</span><span>🛒</span><span>🎁</span><span>🎈</span><span>🎏</span><span>🎀</span><span>🎊</span><span>🎉</span><span>🎎</span><span>🏮</span><span>🎐</span><span>✉️</span><span>📩</span><span>📨</span><span>📧</span><span>💌</span><span>📥</span><span>📤</span><span>📦</span><span>🏷</span><span>📪</span><span>📫</span><span>📬</span><span>📭</span><span>📮</span><span>📯</span><span>📜</span><span>📃</span><span>📄</span><span>📑</span><span>📊</span><span>📈</span><span>📉</span><span>🗒</span><span>🗓</span><span>📆</span><span>📅</span><span>📇</span><span>🗃</span><span>🗳</span><span>🗄</span><span>📋</span><span>📁</span><span>📂</span><span>🗂</span><span>🗞</span><span>📰</span><span>📓</span><span>📔</span><span>📒</span><span>📕</span><span>📗</span><span>📘</span><span>📙</span><span>📚</span><span>📖</span><span>🔖</span><span>🔗</span><span>📎</span><span>🖇</span><span>📐</span><span>📏</span><span>📌</span><span>📍</span><span>📌</span><span>🎌</span><span>🏳️</span><span>🏴</span><span>🏁</span><span>🏳️‍🌈</span><span>✂️</span><span>🖊</span><span>🖋</span><span>✒️</span><span>🖌</span><span>🖍</span><span>📝</span><span>✏️</span><span>🔍</span><span>🔎</span><span>🔏</span><span>🔐</span><span>🔒</span><span>🔓</span><br><br>
                        <h5 style="text-align: center">Символы</h5>
                        <span>❤️</span><span>💛</span><span>💚</span><span>💙</span><span>💜</span><span>🖤</span><span>💔</span><span>❣️</span><span>💕</span><span>💞</span><span>💓</span><span>💗</span><span>💖</span><span>💘</span><span>💝</span><span>💟</span><span>☮️</span><span>✝️</span><span>☪️</span><span>🕉</span><span>☸️</span><span>✡️</span><span>🔯</span><span>🕎</span><span>☯️</span><span>☦️</span><span>🛐</span><span>⛎</span><span>♈️</span><span>♉️</span><span>♊️</span><span>♋️</span><span>♌️</span><span>♍️</span><span>♎️</span><span>♏️</span><span>♐️</span><span>♑️</span><span>♒️</span><span>♓️</span><span>🆔</span><span>⚛️</span><span>🉑</span><span>☢️</span><span>☣️</span><span>📴</span><span>📳</span><span>🈶</span><span>🈚️</span><span>🈸</span><span>🈺</span><span>🈷️</span><span>✴️</span><span>🆚</span><span>💮</span><span>🉐</span><span>㊙️</span><span>㊗️</span><span>🈴</span><span>🈵</span><span>🈹</span><span>🈲</span><span>🅰️</span><span>🅱️</span><span>🆎</span><span>🆑</span><span>🅾️</span><span>🆘</span><span>❌</span><span>⭕️</span><span>🛑</span><span>⛔️</span><span>📛</span><span>🚫</span><span>💯</span><span>💢</span><span>♨️</span><span>🚷</span><span>🚯</span><span>🚳</span><span>🚱</span><span>🔞</span><span>📵</span><span>🚭</span><span>❗️</span><span>❕</span><span>❓</span><span>❔</span><span>‼️</span><span>⁉️</span><span>🔅</span><span>🔆</span><span>〽️</span><span>⚠️</span><span>🚸</span><span>🔱</span><span>⚜️</span><span>🔰</span><span>♻️</span><span>✅</span><span>🈯️</span><span>💹</span><span>❇️</span><span>✳️</span><span>❎</span><span>🌐</span><span>💠</span><span>Ⓜ️</span><span>🌀</span><span>💤</span><span>🏧</span><span>🚾</span><span>♿️</span><span>🅿️</span><span>🈳</span><span>🈂️</span><span>🛂</span><span>🛃</span><span>🛄</span><span>🛅</span><span>🚹</span><span>🚺</span><span>🚼</span><span>🚻</span><span>🚮</span><span>🎦</span><span>📶</span><span>🈁</span><span>🔣</span><span>ℹ️</span><span>🔤</span><span>🔡</span><span>🔠</span><span>🆖</span><span>🆗</span><span>🆙</span><span>🆒</span><span>🆕</span><span>🆓</span><span>0️⃣</span><span>1️⃣</span><span>2️⃣</span><span>3️⃣</span><span>4️⃣</span><span>5️⃣</span><span>6️⃣</span><span>7️⃣</span><span>8️⃣</span><span>9️⃣</span><span>🔟</span><span>🔢</span><span>#️⃣</span><span>*️⃣</span><span>▶️</span><span>⏸</span><span>⏯</span><span>⏹</span><span>⏺</span><span>⏭</span><span>⏮</span><span>⏩</span><span>⏪</span><span>⏫</span><span>⏬</span><span>◀️</span><span>🔼</span><span>🔽</span><span>➡️</span><span>⬅️</span><span>⬆️</span><span>⬇️</span><span>↗️</span><span>↘️</span><span>↙️</span><span>↖️</span><span>↕️</span><span>↔️</span><span>↪️</span><span>↩️</span><span>⤴️</span><span>⤵️</span><span>🔀</span><span>🔁</span><span>🔂</span><span>🔄</span><span>🔃</span><span>🎵</span><span>🎶</span><span>➕</span><span>➖</span><span>➗</span><span>✖️</span><span>💲</span><span>💱</span><span>™️</span><span>©️</span><span>®️</span><span>〰️</span><span>➰</span><span>➿</span><span>🔚</span><span>🔙</span><span>🔛</span><span>🔝</span><span>✔️</span><span>☑️</span><span>🔘</span><span>⚪️</span><span>⚫️</span><span>🔴</span><span>🔵</span><span>🔺</span><span>🔻</span><span>🔸</span><span>🔹</span><span>🔶</span><span>🔷</span><span>🔳</span><span>🔲</span><span>▪️</span><span>▫️</span><span>◾️</span><span>◽️</span><span>◼️</span><span>◻️</span><span>⬛️</span><span>⬜️</span><span>🔈</span><span>🔇</span><span>🔉</span><span>🔊</span><span>🔔</span><span>🔕</span><span>📣</span><span>📢</span><span>👁‍🗨</span><span>💬</span><span>💭</span><span>🗯</span><span>♠️</span><span>♣️</span><span>♥️</span><span>♦️</span><span>🃏</span><span>🎴</span><span>🀄️</span><span>🕐</span><span>🕑</span><span>🕒</span><span>🕓</span><span>🕔</span><span>🕕</span><span>🕖</span><span>🕗</span><span>🕘</span><span>🕙</span><span>🕚</span><span>🕛</span><span>🕜</span><span>🕝</span><span>🕞</span><span>🕟</span><span>🕠</span><span>🕡</span><span>🕢</span><span>🕣</span><span>🕤</span><span>🕥</span><span>🕦</span><span>🕧</span><br><br>
                        <h5 style="text-align: center">Флаги</h5>
                        <span>🏳️</span><span>🏴</span><span>🏁</span><span>🚩</span><span>🏳️‍🌈</span><span>🇦🇫</span><span>🇦🇽</span><span>🇦🇱</span><span>🇩🇿</span><span>🇦🇸</span><span>🇦🇩</span><span>🇦🇴</span><span>🇦🇮</span><span>🇦🇶</span><span>🇦🇬</span><span>🇦🇷</span><span>🇦🇲</span><span>🇦🇼</span><span>🇦🇺</span><span>🇦🇹</span><span>🇦🇿</span><span>🇧🇸</span><span>🇧🇭</span><span>🇧🇩</span><span>🇧🇧</span><span>🇧🇾</span><span>🇧🇪</span><span>🇧🇿</span><span>🇧🇯</span><span>🇧🇲</span><span>🇧🇹</span><span>🇧🇴</span><span>🇧🇦</span><span>🇧🇼</span><span>🇧🇷</span><span>🇮🇴</span><span>🇻🇬</span><span>🇧🇳</span><span>🇧🇬</span><span>🇧🇫</span><span>🇧🇮</span><span>🇰🇭</span><span>🇨🇲</span><span>🇨🇦</span><span>🇮🇨</span><span>🇨🇻</span><span>🇧🇶</span><span>🇰🇾</span><span>🇨🇫</span><span>🇹🇩</span><span>🇨🇱</span><span>🇨🇳</span><span>🇨🇽</span><span>🇨🇨</span><span>🇨🇴</span><span>🇰🇲</span><span>🇨🇬</span><span>🇨🇩</span><span>🇨🇰</span><span>🇨🇷</span><span>🇨🇮</span><span>🇭🇷</span><span>🇨🇺</span><span>🇨🇼</span><span>🇨🇾</span><span>🇨🇿</span><span>🇩🇰</span><span>🇩🇯</span><span>🇩🇲</span><span>🇩🇴</span><span>🇪🇨</span><span>🇪🇬</span><span>🇸🇻</span><span>🇬🇶</span><span>🇪🇷</span><span>🇪🇪</span><span>🇪🇹</span><span>🇪🇺</span><span>🇫🇰</span><span>🇫🇴</span><span>🇫🇯</span><span>🇫🇮</span><span>🇫🇷</span><span>🇬🇫</span><span>🇵🇫</span><span>🇹🇫</span><span>🇬🇦</span><span>🇬🇲</span><span>🇬🇪</span><span>🇩🇪</span><span>🇬🇭</span><span>🇬🇮</span><span>🇬🇷</span><span>🇬🇱</span><span>🇬🇩</span><span>🇬🇵</span><span>🇬🇺</span><span>🇬🇹</span><span>🇬🇬</span><span>🇬🇳</span><span>🇬🇼</span><span>🇬🇾</span><span>🇭🇹</span><span>🇭🇳</span><span>🇭🇰</span><span>🇭🇺</span><span>🇮🇸</span><span>🇮🇳</span><span>🇮🇩</span><span>🇮🇷</span><span>🇮🇶</span><span>🇮🇪</span><span>🇮🇲</span><span>🇮🇱</span><span>🇮🇹</span><span>🇯🇲</span><span>🇯🇵</span><span>🎌</span><span>🇯🇪</span><span>🇯🇴</span><span>🇰🇿</span><span>🇰🇪</span><span>🇰🇮</span><span>🇽🇰</span><span>🇰🇼</span><span>🇰🇬</span><span>🇱🇦</span><span>🇱🇻</span><span>🇱🇧</span><span>🇱🇸</span><span>🇱🇷</span><span>🇱🇾</span><span>🇱🇮</span><span>🇱🇹</span><span>🇱🇺</span><span>🇲🇴</span><span>🇲🇰</span><span>🇲🇬</span><span>🇲🇼</span><span>🇲🇾</span><span>🇲🇻</span><span>🇲🇱</span><span>🇲🇹</span><span>🇲🇭</span><span>🇲🇶</span><span>🇲🇷</span><span>🇲🇺</span><span>🇾🇹</span><span>🇲🇽</span><span>🇫🇲</span><span>🇲🇩</span><span>🇲🇨</span><span>🇲🇳</span><span>🇲🇪</span><span>🇲🇸</span><span>🇲🇦</span><span>🇲🇿</span><span>🇲🇲</span><span>🇳🇦</span><span>🇳🇷</span><span>🇳🇵</span><span>🇳🇱</span><span>🇳🇨</span><span>🇳🇿</span><span>🇳🇮</span><span>🇳🇪</span><span>🇳🇬</span><span>🇳🇺</span><span>🇳🇫</span><span>🇰🇵</span><span>🇲🇵</span><span>🇳🇴</span><span>🇴🇲</span><span>🇵🇰</span><span>🇵🇼</span><span>🇵🇸</span><span>🇵🇦</span><span>🇵🇬</span><span>🇵🇾</span><span>🇵🇪</span><span>🇵🇭</span><span>🇵🇳</span><span>🇵🇱</span><span>🇵🇹</span><span>🇵🇷</span><span>🇶🇦</span><span>🇷🇪</span><span>🇷🇴</span><span>🇷🇺</span><span>🇷🇼</span><span>🇼🇸</span><span>🇸🇲</span><span>🇸🇦</span><span>🇸🇳</span><span>🇷🇸</span><span>🇸🇨</span><span>🇸🇱</span><span>🇸🇬</span><span>🇸🇽</span><span>🇸🇰</span><span>🇸🇮</span><span>🇬🇸</span><span>🇸🇧</span><span>🇸🇴</span><span>🇿🇦</span><span>🇰🇷</span><span>🇸🇸</span><span>🇪🇸</span><span>🇱🇰</span><span>🇧🇱</span><span>🇸🇭</span><span>🇰🇳</span><span>🇱🇨</span><span>🇵🇲</span><span>🇻🇨</span><span>🇸🇩</span><span>🇸🇷</span><span>🇸🇿</span><span>🇸🇪</span><span>🇨🇭</span><span>🇸🇾</span><span>🇹🇼</span><span>🇹🇯</span><span>🇹🇿</span><span>🇹🇭</span><span>🇹🇱</span><span>🇹🇬</span><span>🇹🇰</span><span>🇹🇴</span><span>🇹🇹</span><span>🇹🇳</span><span>🇹🇷</span><span>🇹🇲</span><span>🇹🇨</span><span>🇹🇻</span><span>🇻🇮</span><span>🇺🇬</span><span>🇺🇦</span><span>🇦🇪</span><span>🇬🇧</span><span>🇺🇸</span><span>🇺🇾</span><span>🇺🇿</span><span>🇻🇺</span><span>🇻🇦</span><span>🇻🇪</span><span>🇻🇳</span><span>🇼🇫</span><span>🇪🇭</span><span>🇾🇪</span><span>🇿🇲</span><span>🇿🇼</span><br><br>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="text-align: center;">
            <a href="#!" id="add_new_button" class="waves-effect waves-green btn-small">Добавить</a>
        </div>
    </div>

    <br><br>
    <p><span style="color: red;"><sup>*</sup></span> - Обязательно к заполнению.</p>

    <div class="row">
        <div class="input-field col s12">
            <button type="submit" class="waves-effect waves-light btn-small blue"><? if($update): ?>Редактировать<? else: ?>Добавить<? endif; ?> шаг</button>
        </div>
    </div>
</form>

<script>
    var emoji_on = 1;
</script>
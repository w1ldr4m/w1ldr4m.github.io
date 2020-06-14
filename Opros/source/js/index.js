$(document).ready(function () {
    $("#flash-content").on("click", function () {
        $(this).remove();
    });

    $('#formStep').SortableGridView('?a=worksheets::sort');

    $('#confirm_win, #preformated').modal();

    $(".confirm_win").on("click", function (e) {
        e.preventDefault();
        $("#confirm_win_title").text($(this).data("title"));
        $("#confirm_win_body").text($(this).data("body"));
        $("#confirm_win_href").attr("href", $(this).data("href"));
    });

    if (typeof emoji_on !== "undefined") {

        $(".select-emoji").on("click", function () {
            var em_list = $("#buttons-emoji");
            em_list.toggle();
            if (em_list.css("display") == "block") {
                $(this).html("❌");
            } else {
                $(this).html("😀");
            }
        });

        $(".select_emoji_wrap").on("click", "span", function () {
            var emoji = $(this).html();
            var target = $("input#new-button");
            var caretPos = target[0].selectionStart;
            var textAreaTxt = target.val();
            target.val(textAreaTxt.substring(0, caretPos) + emoji + textAreaTxt.substring(caretPos));
            target.focus();
        });

        $('#button_add_win').modal({
            onCloseEnd: function () {
                $(".select_emoji_wrap").hide();
                $("#new-button").val("");
                $(".select-emoji").html("😀");
            }
        });

        var fixHelperModified = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width())
                });
                return $helper;
            },
            updateIndex = function (e, ui) {
                $('td.index', ui.item.parent()).each(function (i) {
                    $(this).html(i + 1);
                });
                $('input[type=text]', ui.item.parent()).each(function (i) {
                    $(this).val(i + 1);
                });
            };

        $(".sortable_btns").sortable({
            helper: fixHelperModified,
            stop: updateIndex,
            distance: 5,
            delay: 100,
            opacity: 0.6,
            cursor: 'move',
            axis: "y",
            handle: ".icon-sort-btn"
        }).disableSelection();

        $("table#wrap_buttons tbody").on("click", ".deleteButton", function () {
            $(this).parent().parent().remove();
        });

        $("#add_new_button").on("click", function () {
            var reaction = $("#new-button").val();
            if (reaction.length == 0) {
                return false;
            } else {
                var target = $('table#wrap_buttons tbody');
                var text = '<tr><td><i class="material-icons icon-sort-btn">swap_vert</i></td>' +
                    '<td>' + htmlspecialchars(reaction) +
                    '<input type="hidden" name="buttons[' + randomInteger(10000, 9999999) + ']" value="' + htmlspecialchars(reaction) + '">' +
                    '</td>' +
                    '<td><i class="material-icons deleteButton">delete</i></td>';
                target.append(text);

                $("#button_add_win").modal('close');
            }
        });
    }

    function htmlspecialchars(r) {
        return r.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function randomInteger(min, max) {
        // случайное число от min до (max+1)
        var rand = min + Math.random() * (max + 1 - min);
        return Math.floor(rand);
    }

    function randomString(length = 10) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (var i = 0; i < length; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }
        return text;
    }

    $("#upload_file_from_bot").on("click", function () {
        var checkData = setInterval((function () {
            var key_data = $("#key_data");
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "?a=worksheets::check-media&key=" + key_data.val(),
                success: (function (data) {
                    if (data.result == "success") {
                        clearInterval(checkData);
                        $("#wrap_media_file_href").css("display", "none");
                        $("#wrap_media_file_info_type").attr("href", $("#wrap_media_file_info_type").attr("data-href") + $("#key_data").val()).text(data.type_string);
                        $("#" + key_data.data("type")).val(data.type);
                        $("#" + key_data.data("file")).val(data.key_name);
                        $("#wrap_media_file_info").css("display", "inline");
                    } else {
                        console.log("проверка не прошла", key_data.val());
                    }
                }).bind($(this))
            });
        }).bind($(this)), 2000);
    });

    // удаляем прекрепленную картинку
    $("#wrap_media_file_info_del").on("click", function (e) {
        e.preventDefault();
        var key_data = $("#key_data");
        var rand = randomString(16);
        // определяем ссылку на скачивание
        $("#upload_file_from_bot").attr("href", $("#upload_file_from_bot").attr("data-href") + rand);
        // опредляем новый код
        key_data.val(rand);
        // открываем ссылку на загрузку
        $("#wrap_media_file_href").css("display", "inline");
        // скидываем тип файла на message
        $("#" + key_data.data("type")).val("message");
        // удаляем file_id
        $("#" + key_data.data("file")).val("");
        // скрываем инфо файла
        $("#wrap_media_file_info").css("display", "none");
    });

    $('.dropdown-trigger').dropdown();
    $('.fixed-action-btn').floatingActionButton();
    $('.sidenav').sidenav();
    $('select').formSelect();
});
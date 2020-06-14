(function ($) {
    var fixHelper = function (e, ui) {
        ui.children().each(function () {
            $(this).width($(this).width());
        });
        return ui;
    };

    $.fn.SortableGridView = function (action, options = "{}") {
        var widget = this;
        var grid = $('tbody', this);

        var initialIndex = [];
        $('tr', grid).each(function () {
            initialIndex.push($(this).data('key'));
        });

        options = JSON.parse(options);

        grid.sortable({
            items: 'tr',
            axis: 'y',
            handle: 'i.handle',
            //containment: 'tbody.ui-sortable',
            cursor: 'move',
            update: function () {
                var data = [];
                $('tr', grid).each(function () {
                    data.push($(this).data('key'));
                });

                $.ajax({
                    'url': action,
                    'type': 'post',
                    'data': {'items': data},
                    'success': function () {
                        widget.trigger('sortableSuccess');
                    },
                    'error': function (request, status, error) {
                        alert(status + ' ' + error);
                    }
                });
            },
            helper: fixHelper
        }).disableSelection();
    };
})(jQuery);
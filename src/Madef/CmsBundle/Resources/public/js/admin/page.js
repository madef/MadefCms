$(function() {
    // Global values
    var widgetList = {};
    var layoutList = [];

    // Drag and dropable area in the form page
    if ($('#form_layout_identifier').length) {
        $('#form_layout_identifier').change(function() {
            if ($('#form_version').val() === null) {
                $(this).popover({
                    title: $(this).attr('data-error-version-title'),
                    content: $(this).attr('data-error-version-content'),
                    placement: "bottom"
                });
                setTimeout(function() { $('#form_layout_identifier').popover('destroy'); }, 1500);
                 $('#form_layout_identifier').val('');
                return false;
            }
            // Render scructure
            $.ajax({
                url: $(this).attr('data-ajax-url'),
                method: 'post',
                dataType: 'text',
                data: {
                    structure: layoutList[$(this).val()].structure
                }
            }).done(function(data) {
                $('.js-structure-container').html('');
                $('.js-structure-container').append($(data));
                renderWidgetInContent();

                $( ".js-sortable-column" ).sortable({
                    connectWith: ".js-sortable-column",
                    handle: ".js-widget-item",
                    cancel: ".js-widget-item-action",
                    placeholder: "row structure__widget-item structure__widget-item--virtual",
                    start: function(event, ui) {
                        if (!ui.item.parent().hasClass('js-sortable-column-initial')) {
                            return;
                        }
                        var newItem = ui.item.clone();
                        newItem.attr('style', '')
                        ui.placeholder.after(newItem);
                        
                    },
                    stop: function(event, ui) {
                        if (ui.item.parent().hasClass('js-sortable-column-initial')) {
                            ui.item.remove();
                        }
                        refreshFieldContent();
                    }
                });
            });
        });
        var refreshFieldContent = function() {
            var contentObj = {};

            $.each($( ".js-sortable-column-final .js-widget-item"), function() {
                var blockIdentifier = $(this).parent().parent().attr('data-identifier');
                var widgetIdentifier = $(this).attr('data-identifier');
                var widgetVars = JSON.parse($(this).attr('data-vars'));

                if (!contentObj[blockIdentifier]) {
                    contentObj[blockIdentifier] = [];
                }

                contentObj[blockIdentifier].push({
                    identifier: widgetIdentifier,
                    vars: widgetVars
                });
            });
            $('#form_content').val(JSON.stringify(contentObj));
        };

        var renderWidgetInContent = function() {
            if ($('#form_content').val().length) {
                var formContent = JSON.parse($('#form_content').val());
            } else {
                var formContent = {};
            }
            $.ajax({
                url: $('#form_content').attr('data-ajax-render-content-url'),
                method: 'post',
                dataType: 'json',
                data: {
                    content: formContent
                }
            }).done(function(data) {
                $.each(data, function(blockIdentifier, widgetList) {
                    var block = $('.js-sortable-column[data-identifier=' + blockIdentifier + ']');
                    block.html('');

                    $.each(widgetList, function(key, widget) {
                        block.append($(widget));
                    });
                });
            });
        };

        $('#form_version').change(function() {
            // Do ajax to refresh the layouts and widgets avalaible
            $.ajax({
                url: $(this).attr('data-ajax-url'),
                method: 'post',
                dataType: 'json',
                data: {
                    version: $(this).val()
                }
            }).done(function(data) {
                layoutList = data.layoutList;
                widgetList = data.widgetList;

                $('.js-widget-container').html('');
                $('.js-widget-container').append($(data.widgetListHtml));

                var oldValue = $('#form_layout_identifier').val();
                $('#form_layout_identifier').html('');
                $.each(data.layoutList, function() {
                    $('#form_layout_identifier').append(
                            $('<option />')
                                .attr('value', this.identifier)
                                .text(this.identifier)
                    );
                });
                $('#form_layout_identifier').val(oldValue);
                if ($('#form_layout_identifier').val()) {
                    $('#form_layout_identifier').change();
                }
            });
        });
        
        // Initialization
        if ($('#form_version').val()) {
            $('#form_version').change();
        }

        $('.js-structure-container').on('click', '.js-widget-item-remove', function() {
            if (confirm($(this).attr('data-confirm'))) {
                $(this).parent().parent().remove();
                refreshFieldContent();
            }
        });
        $('.js-structure-container').on('click', '.js-widget-item-configure', function() {
            var identifier = $(this).parent().attr('data-identifier');
            var version = $('#form_version').val();
            if ($(this).parent().attr('data-vars').length) {
                var vars = JSON.parse($(this).parent().attr('data-vars'));
            } else {
                var vars = widgetList[identifier]['default_content'];
            }
            var fields = widgetList[identifier]['form'];
            
            $.ajax({
                url: $('#form_content').attr('data-ajax-render-widget-from-url'),
                method: 'post',
                dataType: 'text',
                context: $(this).parent(),
                data: {
                    identifier: identifier,
                    vars: vars,
                    version: version
                }
            }).done(function(data) {
                var modal = $(data);
                modal.modal();

                var element = $(this);

                modal.on('shown.bs.modal', function() {
                    $(this).find('textarea').each(function() {
                        new Autoresize(this);
                    });
                    $('.js-modal-form').submit(function() {
                        var values = {};
                        $.each($(this).serializeArray(), function(key, data) {
                            values[data.name] = data.value;
                        });
                        element.attr('data-vars', JSON.stringify(values));
                        refreshFieldContent();
                        modal.modal('hide');
                        return false;
                    });
                });
            });
        });
    }
});

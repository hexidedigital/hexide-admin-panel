require('./scripts/ajax_inputs')
require('./scripts/duplication_row')
require('./scripts/slug_generate')
require('./scripts/image_select')
require('./scripts/set_require_inputs')
require('./scripts/bootstrapToggle')

// require('./scripts/legacy-source')

$(document).ready(function () {
    window._token = $('meta[name="csrf-token"]').attr('content')

    // --------------------

    if (window.CKEDITOR && CKEDITOR?.config) {
        // CKEDITOR
        CKEDITOR.config.removePlugins = 'elementspath'
        CKEDITOR.config.enterMode = CKEDITOR.ENTER_P
        CKEDITOR.config.shiftEnterMode = CKEDITOR.ENTER_BR
    }

    // --------------------

    if (window.moment) {
        moment.updateLocale('en', {
            week: {dow: 1} // Monday is the first day of the week
        })
    }

    $('.date').datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'en'
    })

    $('.datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        locale: 'en',
        sideBySide: true
    })

    $('.timepicker').datetimepicker({
        format: 'HH:mm:ss'
    })

    // --------------------

    // ((div > (btn.select + btn.deselect)) + select.select2[multiple])
    $('.select-all').click(function () {
        const $select2 = $(this).parent().siblings('.select2')
        $select2.find('option').prop('selected', 'selected')
        $select2.trigger('change')
    })
    $('.deselect-all').click(function () {
        const $select2 = $(this).parent().siblings('.select2')
        $select2.find('option').prop('selected', '')
        $select2.trigger('change')
    })
    $('.select2').select2()

})

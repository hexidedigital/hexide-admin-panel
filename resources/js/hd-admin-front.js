$(document).ready(function () {
    window._token = $('meta[name="csrf-token"]').attr('content')

    // --------------------

    // CKEDITOR
    CKEDITOR.config.removePlugins   = 'elementspath'
    CKEDITOR.config.enterMode       = CKEDITOR.ENTER_P
    CKEDITOR.config.shiftEnterMode  = CKEDITOR.ENTER_BR

    // --------------------

    require('./ajax_inputs')
    require('./duplication_row')
    require('./slug_generate')

    // --------------------


    // make toggle/switch from checkbox
    $(document).ready(function () {
        $('.toggle_attributes').bootstrapToggle()
        $('.toogle').bootstrapToggle()
    })

    // --------------------

    moment.updateLocale('en', {
        week: {dow: 1} // Monday is the first day of the week
    })

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

    // --------------------

    // setup `required` attribute for inputs (or other by name)
    // inside `div` with `required` class
    if (document.getElementsByClassName('required').length !== 0){
        let req_inputs = [];

        $('.required').each((i, el)=>{
            let for_input = $($(el).find('label')).attr('for');

            req_inputs[for_input] = 'required';

            $(el).find('[name="'+for_input+'"]').attr('required', 'required')
        })

        console.log(req_inputs);

        $('.content form').validate({
            rules:{...req_inputs},
            messages: {},
            submitHandler: function(form) {
                form.submit();
            }
        })
    }
})

// (legacy code)
$(document).ready(function () {
    //
    $('.treeview').each(function () {
        var shouldExpand = false
        $(this).find('li').each(function () {
            if ($(this).hasClass('active')) {
                shouldExpand = true
            }
        })
        if (shouldExpand) {
            $(this).addClass('active')
        }
    })

    //
    $(document).on('click', '.delete-file', function () {
        document.getElementById('document-upload').value = ''
        $('body input[name=hidden_document]').attr('value', '')
        $('body #preview-document').attr('src', '')
        $('body #old-document').hide()
    })

    //
    let flag = false
    $(document).on('click', '.browse', function () {
        var file = $(this).parents().find('.file')
        file.trigger('click')
        flag = true
    })

    //
    $('input[type="file"]').change(function (e) {
        const inputElement = $(this)
        const parentSection = $(this).parent('div')
        const imageElement = parentSection.find('img')
        var fileName = e.target.files[0].name
        $('#file').val(fileName)

        var reader = new FileReader()
        reader.onload = function (e) {
            // get loaded data and render thumbnail.
            if (!flag === true) {
                imageElement.attr('src', e.target.result)
            } else {
                flag = false
                document.getElementById('preview-document').src = e.target.result
            }
        }
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0])
    })

    // ad images in news
    $(document).on('change', '.input-file', function (e) {
        var fileName = e.target.files[0].name
        $('#file').val(fileName)
        const inputElement = $(this)
        const parentSection = $(this).parent('div')
        const imageElement = parentSection.find('img')

        var reader = new FileReader()
        reader.onload = function (e) {
            imageElement.attr('src', e.target.result)
        }

        reader.readAsDataURL(this.files[0])
    })

    $('.with-loading').on('click', function () {
        if (!$(this).find('loading').length) {
            new Loading($(this))
        }
        return setTimeout(() => {
            var $form
            $form = $(this).closest('form')
            if ($form.length) {
                return $form.submit()
            }
        }, 200)
    });

    Loading = class Loading {
        constructor(obj) {
            var $loader, position
            this.obj = obj
            $loader = $('<div id="loader" class="loading"><i class="fa fa-cog fa-spin" aria-hidden="true"></i></div>').appendTo(this.obj)
            position = this.obj.css('position')
            if (position !== 'absolute' && position !== 'fixed' && position !== 'relative') {
                this.obj.css('position', 'relative')
            }
        }

        hide() {
            return this.obj.find('.loading').remove()
        }
    }
});

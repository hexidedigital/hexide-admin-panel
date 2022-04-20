// duplication row (block)
window.ic = 1

window.duplicate_row = function ($this) {
    var $nrow, $parent
    if (!$this.hasClass('duplication')) {
        $parent = $this.closest('.duplication')
    } else {
        $parent = $this
    }
    $nrow = $parent.find('.duplicate').clone(true)
    if ($nrow.length === 0) {
        return
    }
    window.ic++
    $nrow[0].innerHTML = $nrow[0].innerHTML.replace(/replaseme/g, window.ic)
    $nrow.removeClass('duplicate').insertBefore($parent.find('.duplication-button'))
    $nrow.find('.form-control').each(function () {
        $(this).attr('name', $(this).data('name'))
        if ($(this).data('required')) {
            return $(this).attr('required', $(this).data('required'))
        }
    })
    if(!$nrow.attr('data-init')){
        // init ckeditor for textarea with class `with-editor`
        $($nrow).find('textarea.with-editor').each((i, el)=>{
            CKEDITOR.replace($(el).attr('name'))
        })

        // init ckeditor for select with class `with-select2`
        // init select2 for select with class `with-select2`
        $($nrow)
            .not('.duplicate.duplicate_select')
            .find('select.with-select2').select2()
    }
    return true
}

$(document).ready(function () {
    window.ic = $('.duplication-row').length
    $('.duplication.duplicate-on-start').each(function () {
        return duplicate_row($(this))
    })
    $(document).on('click', '.duplication .create', function () {
        return duplicate_row($(this))
    })
    return $(document).on('click', '.duplication .destroy', function () {
        var $this, id, name
        $this = $(this)
        if ($this.hasClass('exist')) {
            id = $this.data('id')
            if (id) {
                name = $(this).data('name')
                $(this).closest('form').append('<input type="hidden" name="' + name + '" value="' + id + '" />')
            }
        }
        return $(this).closest('.duplication-row').remove()
    })
})

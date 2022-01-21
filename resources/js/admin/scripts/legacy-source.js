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

});

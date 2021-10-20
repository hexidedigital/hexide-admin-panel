// ajaxComplete required for datatable
$(document).ajaxComplete(function () {
    $('.ajax_ckeckbox').bootstrapToggle()
})

$(document).on('change', '.ajax_ckeckbox', function (event) {
    const field = $(this).data('field')
    const id = $(this).data('id')
    const _token = $(this).data('token')
    const value = ($(this).is(':checked')) ? 1 : 0
    const url = $(this).data('url')

    handleAjax(url, id, field, value, _token);
})

$(document).on('change', '.ajax_input', function (event) {
    const field = $(this).data('field')
    const id = $(this).data('id')
    const _token = $(this).data('token')
    const value = $(this).val()
    const url = $(this).data('url')

    handleAjax(url, id, field, value, _token);
})

function handleAjax(url, id, field, value, _token, type='POST'){
    $.ajax({
        type: type,
        url: url,
        data: {_token: _token, id: id, field: field, value: value},
        success: function ({message}) {
            toastr.success(message)
        },
        error: function (resp) {
            console.log(resp)
            toastr.error(resp.responseJSON.message)
        }
    })
}

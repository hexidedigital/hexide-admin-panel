// tools for remove image and mark as removed on update
$(".removeImage").on('click', function () {
    const parentSection = $(this).parent().parent('div')

    const imageElement = parentSection.find('img.preview')
    const fileElement = parentSection.find('input[type="file"].imageInput')
    const flagElement = parentSection.find('input[type="hidden"].isRemoveImage')

    $(flagElement).attr('value', '1');
    $(imageElement).attr('src', '/img/800x800.png');
    $(fileElement).val('');

    $(this).attr('hidden', true);
})

$(".imageInput").on('change', function () {
    const parentSection = $(this).parent('div')

    const flagElement = parentSection.find('input[type="hidden"].isRemoveImage')
    const btnElement = parentSection.find('button[type="button"].removeImage')

    $(flagElement).attr('value', '0');
    $(btnElement).attr('hidden', false);
})

// ad images
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

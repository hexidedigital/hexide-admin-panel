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

// tools for generate slug
$(document).on('click', '.generate_slug', function (e) {
    e.preventDefault()

    var generate_value = $('.generate_value').val()

    if (generate_value) {
        $('.generate_info').attr('hidden', true)
        var result = translite(generate_value)
        $('.slug_value').val('')
        $('.slug_value').val(result)
    } else {
        $('.generate_info').removeAttr('hidden')
    }

    // !generate_value ?  $('.generate_info').removeAttr('hidden') : $('.generate_info').attr('hidden', true);
})
function translite(str) {
    var space = '-'

    str = str.toLowerCase()

    var transl = {
        а: 'a', б: 'b', в: 'v', г: 'g', д: 'd', е: 'e', ё: 'e', ж: 'zh',
        з: 'z', и: 'i', й: 'j', к: 'k', л: 'l', м: 'm', н: 'n',
        о: 'o', п: 'p', р: 'r', с: 's', т: 't', у: 'u', ф: 'f', х: 'h',
        ц: 'c', ч: 'ch', ш: 'sh', щ: 'sh', ъ: '~', ы: 'y', ь: '~', э: 'e', ю: 'yu', я: 'ya'
    }

    var link = ''

    for (var i = 0; i < str.length; i++) {
        if (/[а-яё]/.test(str.charAt(i))) { // если текущий символ - русская буква, то меняем его
            link += transl[str.charAt(i)]
        } else if (/[a-z0-9]/.test(str.charAt(i))) {
            link += str.charAt(i) // если текущий символ - английская буква или цифра, то оставляем как есть
        } else {
            if (link.slice(-1) !== space) link += space // если не то и не другое то вставляем space
        }
    }
    return (link.replace(/~/g, ''))
}

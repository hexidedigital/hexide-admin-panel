/*
$(document).on('click', '.generate_slug', function (e) {
    e.preventDefault()

    const generate_value = $('.generate_value').val()

    if (generate_value) {
        $.ajax({
            type: 'POST',
            url: '/admin/generate/slug',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                value: generate_value,
            },
            success: function (message) {
                $('.generate_info').attr('hidden', true)
                $('.slug_value').val(message)
            },
            error: function (resp) {
                console.log(resp)
                toastr.error(resp.message)
            },
            finally: function () {
                $('.generate_info').removeAttr('hidden')
            }
        })
    }

    // !generate_value ?  $('.generate_info').removeAttr('hidden') : $('.generate_info').attr('hidden', true);
})
*/


$(document).ready(function () {
    $('.generate_slug').click(function () {
        const source = $.trim($('.generate_value').val());

        if (source.length > 0) {
            $('.slug_value').val($.fn.transliterate(source, 'ua', {'lowercase': true}));
        }
    });
});

(function ($) {
    $.fn.transliterate = function (sourceText, ruleName, options) {
        if (!$.fn.transliterate.rules.hasOwnProperty(ruleName)) {
            throw new Error('Rule was not found');
        }

        options = $.fn.extend($.fn.defaults, options);
        const rules = $.fn.transliterate.rules[ruleName];

        const words = sourceText.split(/[-_\s\n]/);
        for (let n in words) {
            let word = words[n];

            for (let ruleNumber in rules) {
                word = word.replace(
                    new RegExp(rules[ruleNumber]['pattern'], 'gm'),
                    rules[ruleNumber]['replace']
                );
            }

            sourceText = sourceText.replace(words[n], word);
        }

        sourceText = sourceText.replace(/[\s]/gm, '-');

        if (options['lowercase']) {
            sourceText = sourceText.toLowerCase();
        }

        return sourceText;
    };

    $.fn.transliterate.registerRules = function (rulesName, rules) {
        $.fn.transliterate.rules[rulesName] = rules;
    };

    // Default rules
    $.fn.transliterate.rules = {};
    $.fn.defaults = {
        'lowercase': true
    };
})(jQuery);

const rules = [
    {'pattern': '??', 'replace': 'a'},
    {'pattern': '??', 'replace': 'b'},
    {'pattern': '??', 'replace': 'v'},
    {'pattern': '????', 'replace': 'zgh'},
    {'pattern': '????', 'replace': 'Zgh'},
    {'pattern': '??', 'replace': 'h'},
    {'pattern': '??', 'replace': 'g'},
    {'pattern': '??', 'replace': 'd'},
    {'pattern': '??', 'replace': 'e'},
    {'pattern': '^??', 'replace': 'ye'},
    {'pattern': '??', 'replace': 'ie'},
    {'pattern': '??', 'replace': 'zh'},
    {'pattern': '??', 'replace': 'z'},
    {'pattern': '??', 'replace': 'y'},
    {'pattern': '??', 'replace': 'i'},
    {'pattern': '^??', 'replace': 'yi'},
    {'pattern': '??', 'replace': 'i'},
    {'pattern': '^??', 'replace': 'y'},
    {'pattern': '??', 'replace': 'i'},
    {'pattern': '??', 'replace': 'k'},
    {'pattern': '??', 'replace': 'l'},
    {'pattern': '??', 'replace': 'm'},
    {'pattern': '??', 'replace': 'n'},
    {'pattern': '??', 'replace': 'o'},
    {'pattern': '??', 'replace': 'p'},
    {'pattern': '??', 'replace': 'r'},
    {'pattern': '??', 'replace': 's'},
    {'pattern': '??', 'replace': 't'},
    {'pattern': '??', 'replace': 'u'},
    {'pattern': '??', 'replace': 'f'},
    {'pattern': '??', 'replace': 'kh'},
    {'pattern': '??', 'replace': 'ts'},
    {'pattern': '??', 'replace': 'ch'},
    {'pattern': '??', 'replace': 'sh'},
    {'pattern': '??', 'replace': 'shch'},
    {'pattern': '????', 'replace': 'io'},
    {'pattern': '????', 'replace': 'ii'},
    {'pattern': '??', 'replace': ''},
    {'pattern': '^??', 'replace': 'yu'},
    {'pattern': '??', 'replace': 'iu'},
    {'pattern': '^??', 'replace': 'ya'},
    {'pattern': '??', 'replace': 'ia'},
    {'pattern': '??', 'replace': 'A'},
    {'pattern': '??', 'replace': 'B'},
    {'pattern': '??', 'replace': 'V'},
    {'pattern': '??', 'replace': 'H'},
    {'pattern': '??', 'replace': 'G'},
    {'pattern': '??', 'replace': 'D'},
    {'pattern': '??', 'replace': 'E'},
    {'pattern': '^??', 'replace': 'Ye'},
    {'pattern': '??', 'replace': 'Ie'},
    {'pattern': '??', 'replace': 'Zh'},
    {'pattern': '??', 'replace': 'Z'},
    {'pattern': '??', 'replace': 'Y'},
    {'pattern': '??', 'replace': 'I'},
    {'pattern': '^??', 'replace': 'Yi'},
    {'pattern': '??', 'replace': 'I'},
    {'pattern': '^??', 'replace': 'Y'},
    {'pattern': '??', 'replace': 'I'},
    {'pattern': '??', 'replace': 'K'},
    {'pattern': '??', 'replace': 'L'},
    {'pattern': '??', 'replace': 'M'},
    {'pattern': '??', 'replace': 'N'},
    {'pattern': '??', 'replace': 'O'},
    {'pattern': '??', 'replace': 'P'},
    {'pattern': '??', 'replace': 'R'},
    {'pattern': '??', 'replace': 'S'},
    {'pattern': '??', 'replace': 'T'},
    {'pattern': '??', 'replace': 'U'},
    {'pattern': '??', 'replace': 'F'},
    {'pattern': '??', 'replace': 'Kh'},
    {'pattern': '??', 'replace': 'Ts'},
    {'pattern': '??', 'replace': 'Ch'},
    {'pattern': '??', 'replace': 'Sh'},
    {'pattern': '??', 'replace': 'Shch'},
    {'pattern': '??', 'replace': ''},
    {'pattern': '^??', 'replace': 'Yu'},
    {'pattern': '??', 'replace': 'Iu'},
    {'pattern': '^??', 'replace': 'Ya'},
    {'pattern': '??', 'replace': 'Ia'},
    {'pattern': '\'', 'replace': ''},
    {'pattern': '???', 'replace': ''},
    {'pattern': ' ', 'replace': '-'}
];

$.fn.transliterate.registerRules('ua', rules);

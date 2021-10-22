$(document).ready(function () {
    $('body').find('#nav-tabs').find('.nav-link.major').on('click', function () {
        let url = "?id=" + $(this).attr('href').split('#')[1]
        let pathname = window.location.pathname;
        window.history.pushState(null, null, pathname + url);
    })
})

$(window).on("load", () => {
    let currentUrl = window.location.href.split('=')[1]

    if (currentUrl) {
        currentUrl = '#' + currentUrl
        if (currentUrl.indexOf('&') > -1) {
            currentUrl = currentUrl.split('&')[0]
        }
    } else {
        currentUrl = '#tab_us'
    }

    let items = $('body').find('#nav-tabs').find('.nav-link.major')
    items.each(function (e) {
        if ($(this).hasClass('active')) {
            if ($(this).attr('href') !== currentUrl) {
                $(this).removeClass('active')
                $(this).attr('aria-selected', false)
                $('body').find('#tab-content').children().removeClass('show active')
            }
        }

        if ($(this).attr('href') === currentUrl) {
            $(this).addClass('active')
            $(this).attr('aria-selected', true)
            let id = e + 1
            $('body').find('#tab-content .tab-pane:nth-child(' + id + ")").addClass('show active')
        }
    })
})

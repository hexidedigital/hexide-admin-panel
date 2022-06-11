// make toggle/switch from checkbox
window.initToggles = () => {
    $('.' + toggleInitClass).bootstrapToggle()
    $('.toogle').bootstrapToggle()
}

$(document).ready(function () {
    initToggles()
})

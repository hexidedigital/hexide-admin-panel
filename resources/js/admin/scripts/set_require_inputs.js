$(document).ready(function () {
    // setup `required` attribute for inputs (or other by name)
    // inside `div` with `required` class
    if (document.getElementsByClassName('required').length !== 0) {
        let req_inputs = [];

        $('.required').each((i, el) => {
            let for_input = $($(el).find('label')).attr('for');

            req_inputs[for_input] = 'required';

            $(el).find('[name="' + for_input + '"]').attr('required', 'required')
        })

        console.log(req_inputs);

        $('.content form').validate({
            rules: {...req_inputs},
            messages: {},
            submitHandler: function (form) {
                form.submit();
            }
        })
    }
})

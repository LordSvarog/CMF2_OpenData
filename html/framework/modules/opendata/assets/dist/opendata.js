$(document).ready(function () {

    // разрешаю
    $('#inputDeal').change(function () {
        if ($(this).prop('checked') === true) {
            $('#recourseFormBtnSubmit').prop('disabled', false);
        }
        else {
            $('#recourseFormBtnSubmit').prop('disabled', true);
        }
    });


    $('#opendataCommentForm').validate({
        // добавляем класс к обертке у элементов формы при валидации
        highlight: function (element, errorClass, validClass) {
            $(element).closest(".form-group")
                .removeClass(validClass)
                .addClass(errorClass);
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).closest(".form-group")
                .removeClass(errorClass)
                .addClass(validClass);
        },
        // выводим сообщение в шапку формы о наличии ошибок
        invalidHandler: function (event, validator) {
            // 'this' refers to the form
            var errors = validator.numberOfInvalids();
            if (errors) {
                var message = 'Проверьте, пожалуйста, правильность заполнения формы.';
                $(this).find("div.error-message").html(message);
                $(this).find("div.error-message").show();
            } else {
                $(this).find("div.error-message").hide();
            }
        },
        rules: {
            "CommentForm[name]": {
                required: true
            },
            "CommentForm[email]": {
                required: true,
                is_email: true
            },
            "CommentForm[comment]": {
                required: true
            }
        },
        submitHandler: function (form, e) {
            e.preventDefault();
            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                dataType: 'JSON',
                data: $(form).serialize(),
                success: function (data) {
                    if (data.success) {
                        $(form).find('.error-message').hide();
                        $(form).find('.error-summary').remove();
                        $('.modal').modal('hide');
                        $('#modalOk').modal();
                    } else if (data.errors) {
                        var summary = '';
                        for (var i in data.errors) {
                            if (data.errors.hasOwnProperty(i)) {
                                summary += '<span class="error">' + data.errors[i].join('<br>') + '</span><br>';
                            }
                        }
                        $(form).find('.error-message').text('Проверьте, пожалуйста, правильность заполнения формы.').show().after(
                            '<div class="error-summary">' + summary + '</div>'
                        );
                    }
                }
            });
        }
    });

    $.expr[":"].contains = $.expr.createPseudo(function (arg) {
        return function (elem) {
            return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
        };
    });
    $('form.contains').on('submit', function (e) {
        e.preventDefault();
        var search = $(this).find('input[type="text"]:first').val(),
            where = $("." + $(this).data('where'));
        if (where.length <= 0) return false;
        if (search.length) {
            where.find('li').hide();
            where.find('li:contains(' + search + ')').show();
        } else {
            where.find('li').show();
        }
    });

    $('.list-dataset__download').on('click', '.dropdown-menu li', function () {
        var $li = $(this),
            $option = $(this).closest('.bootstrap-select').find('select option').eq($li.data('original-index'));
        if ($option.length) {
            document.location = $option.data('url');
        }
    })
});
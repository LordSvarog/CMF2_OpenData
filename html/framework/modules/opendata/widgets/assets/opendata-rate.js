$(document).ready(function () {
    var $rate_row = $(".rate_row:first");
    $rate_row.starwarsjs({
        stars: $rate_row.data("max"),
        count: 1,
        default_stars: $rate_row.data("rate"),
        input_name: $rate_row.data("name")
    });

    $('#opendataRateForm').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: $form.serialize(),
            xhrFields: {
                withCredentials: true
            },
            success: function (data) {
                if(data.success){
                    $form.addClass('send');
                    var $container = $('.rating-container');
                    $container.removeClass('hidden')
                        .find('.count').text(data.count);
                    $container.find('.rating').text(data.rating);
                }
                if (data.errors) {
                    console.error(data.errors);
                }
            }
        });
    });
});
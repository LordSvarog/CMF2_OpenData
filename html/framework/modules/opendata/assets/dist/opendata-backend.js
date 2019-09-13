$(document).ready(function () {

    $('.delete-property').on('click', function (e) {
        e.preventDefault();
        var $row = $(this).closest('.property');
        $row.addClass('hidden');
        $row.find('.delete').val('1');
        if ($('.property:visible').length <= 1) {
            $('.property .delete-property').addClass('hidden');
        }
    });

    $('.create-property').on('click', function (e) {
        e.preventDefault();
        var $row = $('.property-container .property:first').clone(true),
            rowIndex = $('.property-container .property').length;
        $row.find('.property-id').remove();

        $row.find('input').each(function (index, elem) {
            $(elem).val('');
            var name = $(elem).attr('name');
            name = name.replace(/\[([\d]+)\]/i, '[a' + rowIndex + ']');
            $(elem).attr('name', name);
        });
        $row.find('select').each(function (index, elem) {
            var name = $(elem).attr('name');
            name = name.replace(/\[([\d]+)\]/i, '[a' + rowIndex + ']');
            $(elem).attr('name', name);
            var $select = $(elem).clone(true);
            $(elem).closest('.bootstrap-select').replaceWith($select);
            $select.find('option:first').prop('selected', true);
            $select.selectpicker();
        });

        $('.property-container').append($row);
        $row.find('select').selectpicker('refresh');
        if ($('.property:visible').length > 1) {
            $('.property .delete').removeClass('hidden');
        }
    });
    
    $('#form-set button[type="submit"]').on('click', function(e){
        e.preventDefault();
        var container = $('.property-container'),
            hasError = false;

        //remove errors
        container.find('.help-block').html('');
        container.find('.has-error').removeClass('has-error');
        
        container.find('input:text:not(:hidden)').each(function(index, elem){
            if($(elem).val().length == 0){
                $(elem).next('.help-block').html('Заполните поле');
                $(elem).parents('.form-group').addClass('has-error');
            }
        });        
        hasError = (container.find('.has-error').length > 0);
        
        if(container.hasClass('js-has-chart')){
            $('.js-prop-error div.content').html('');

            if(container.find('.js-chart-prop option[value="1"]:selected').length == 0){
                $('.js-prop-error div.content').append($('<div></div>').html('Одно свойство должно быть обозначено, как ряды для диаграммы'));
            }
            if(container.find('.js-chart-prop option[value="1"]:selected').length > 1){
                $('.js-prop-error div.content').append($('<div></div>').html('Только одно свойство может быть обозначено, как ряды для диаграммы'));
            }            
            if(container.find('.js-chart-prop option[value="2"]:selected').length == 0){
                $('.js-prop-error div.content').append($('<div></div>').html('Хотя бы одно свойство должно быть категорией для диаграммы'));
            }
            
            if($('.js-prop-error div.content').html().length > 0){
                hasError = true;
                $('.js-prop-error').removeClass('hidden');
            }else{
                $('.js-prop-error').addClass('hidden');
            }
        }

        //if(container.hasClass('js-has-map')){
        if($('#opendataset-map_region').val() == 1){
            $('.js-prop-error div.content').html('');

            if(container.find('.js-map-prop option[value="1"]:selected').length == 0){
                $('.js-prop-error div.content').append($('<div></div>').html('Одно свойство должно быть обозначено, как регион для карты'));
            }
            if(container.find('.js-map-prop option[value="1"]:selected').length > 1){
                $('.js-prop-error div.content').append($('<div></div>').html('Только одно свойство может быть обозначено, как регион для карты'));
            }
            if(container.find('.js-map-prop option[value="2"]:selected').length == 0){
                $('.js-prop-error div.content').append($('<div></div>').html('Хотя бы одно свойство должно быть показателем для карты'));
            }

            if($('.js-prop-error div.content').html().length > 0){
                hasError = true;
                $('.js-prop-error').removeClass('hidden');
            }else{
                $('.js-prop-error').addClass('hidden');
            }
        }

        if(hasError){
            return false;
        }
        $('#form-set').submit();
    });
    
    $('.js-prop-error a.close').on('click', function(e){
        $('.js-prop-error').addClass('hidden');
    });
});
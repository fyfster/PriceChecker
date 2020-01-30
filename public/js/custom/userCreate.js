$(document).ready(function() {
    InitCounties();

    $('.country_select').on('change', function(){
        InitCounties();
    });

    $('.county_select').on('change', function(){
        InitCities();
    });

    $("input[type=file]").change(function(){
        var file = $(this).val();
        $(".custom-file-upload").text(file);
    });

    function InitCounties()
    {
        if ($('.country_select').val() != "") {
            $.ajax({
                url: $('.country_select').data('address-action'),
                data: {
                    id: $('.country_select').val()
                }
            }).done(function( data ) {
                $('.county_select').html("<option></option>");
                $('.city_select').html("<option></option>");
                if (data.success == 1) {
                    var counties = data.data.counties;
                    for (i = 0; i < counties.length; i++) {
                        var county = counties[i];
                        var select = (county.id == $('.county_id_input').val()) ? true : false;

                        $('.county_select').append(
                            $('<option>',{
                                value: county.id,
                                text: county.name,
                                selected: select
                            }));
                    }
                    InitCities();
                    if (counties.length > 0) {
                        toggleAddressInputs(false);
                    } else {
                        toggleAddressInputs(true);
                        $('.county_select').val('');
                        $('.city_select').val('');
                    }
                } else {
                    $('.country_select').val("");
                }
            });
        }
    }

    function InitCities()
    {
        if ($('.county_select').val() != "") {
            $.ajax({
                url: $('.county_select').data('action'),
                data: {
                    id: $('.county_select').val()
                }
            }).done(function( data ) {
                $('.city_select').html("<option></option>");
                if (data.success == 1) {
                    var cities = data.data.cities;
                    for (i = 0; i < cities.length; i++) {
                        var city = cities[i];
                        var select = (city.id == $('.city_id_input').val()) ? true : false;
                        $('.city_select').append(
                            $('<option>',{
                                value: city.id,
                                text: city.name,
                                selected: select
                            }));
                    }
                } else {
                    $('.city_select').val("");
                }
            });
        }
    }

    function toggleAddressInputs(toggleValue) {
        if (toggleValue == true) {
            $('.county_select').attr('disabled', 'disabled');
            $('.county_select').attr('readonly', 'readonly');
            $('.city_select').attr('disabled', 'disabled');
            $('.city_select').attr('readonly', 'readonly');
        } else {
            $('.county_select').removeAttr('disabled');
            $('.county_select').removeAttr('readonly');
            $('.city_select').removeAttr('disabled');
            $('.city_select').removeAttr('readonly');
        }
    }
});

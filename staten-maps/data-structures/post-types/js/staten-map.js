jQuery(function ($) {

    var $body = $('body');
    $body.on('keypress', '.acf-field-staten-address input[type="text"]', function (e) {

        if (e.which === 13) {
            e.preventDefault();

            var $parent = $(this).parents('.acf-field-staten-address');
            $parent.find('.staten-get-lat-lng').click();
        }

    });
    $body.on('click', '.staten-get-lat-lng', function (e) {
        e.preventDefault();


        var $parent = $(this).parents('.acf-row'),
            $addressOuter = $parent.find('.acf-field-staten-address'),
            address = $addressOuter.find('input[type="text"]').val(),
            nonce = $addressOuter.find('.nonce').attr('data-nonce'),
            $message = $addressOuter.find('.lat-lng-message'),
            $lat = $parent.find('.acf-field-staten-lat').find('input[type="text"]'),
            $lng = $parent.find('.acf-field-staten-lng').find('input[type="text"]'),
            prePopulatedLat = $lat.val(),
            prePopulatedLng = $lng.val(),
            $spinner = $parent.find('.spinner'),
            setMessage = function (message) {
                if (!message) {
                    $message.css('display', 'none');
                    return;
                }
                $message.css('display', 'block');
                $message.html(message);
            },


            loading = function (hide) {
                if (hide === 'hide') {
                    $spinner.removeClass('is-active');
                    return;
                }
                $spinner.addClass('is-active');

            };

        setMessage('');


        if (prePopulatedLat || prePopulatedLng) {
            var confirmClearLatLng = confirm("This will clear out anything in this items Latitude and Longitude, are you sure you want to continue?");
            if (!confirmClearLatLng) {
                loading('hide');
                return;
            }
        }

        $lat.val('');
        $lng.val('');


        loading();
        if (!address) {

            setMessage(statenMap.missingAddress);
            loading('hide');
            return;
        }

        var data = {
            action: 'get_lat_lng',
            address: address,
            security: nonce
        };

        $.post(ajaxurl, data, function (response) {
            loading('hide');
            if (response.success) {


                $lat.val(response.data.lat);
                $lng.val(response.data.lng);

            } else {
                setMessage(statenMap.badAddress);
            }
        });

    });


});
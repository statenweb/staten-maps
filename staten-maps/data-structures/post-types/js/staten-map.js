jQuery(function ($) {

    var clearMessage = function ($elem) {
            $elem.html('');
        },
        setMessage = function (message, $elem, clear) {
            if (clear) {
                clearMessage($elem);
            }
            $elem.html(message);
        };


    $('body').on('click', '.staten-get-lat-lng', function (e) {
        e.preventDefault();


        var $parent = $(this).parents('.acf-repeater'),
            $addressOuter = $(this).parents('.acf-fields').find('.acf-field-staten-address'),
            address = $addressOuter.find('input[type="text"]').val(),
            nonce = $addressOuter.find('.nonce').attr('data-nonce'),
            $message = $addressOuter.find('.lat-lng-message');
        $parent.css('opacity', '.5');
        if (!address) {

            setMessage('Please enter in an address', $message, true);
            return;
        }

        var data = {
            action: 'get_lat_lng',
            address: address,
            security: nonce
        };

        $.post(ajaxurl, data, function (response) {
            $parent.css('opacity', '1');
            console.log(response);
            if (response.success) {
                var $lat = $parent.find('.acf-field-staten-lat').find('input[type="text"]'),
                    $lng = $parent.find('.acf-field-staten-lng').find('input[type="text"]');

                $lat.val(response.data.lat);
                $lng.val(response.data.lng);

            } else {
                setMessage(response.data.message, $message, true);
            }
        });

    });


});
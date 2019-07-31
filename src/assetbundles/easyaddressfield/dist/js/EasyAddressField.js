;(function ($, window, document, undefined) {

    var handle;
    var address = {};
    var dragPin = {};

    $(document).on('click', '.easyaddressfield-marker', function () {
        handle = $(this).closest('.easyaddressfield-field').attr('id');
        modalDragPin(handle);
    });


    function modalDragPin(handle) {

        if (handle in dragPin) {
            dragPin[handle]['modal'].show();
        } else {
            dragPin[handle] = {};

            $('form.easyaddressfield-modal-drag-pin').remove();
            var $modal = $('<form id="easyaddressfield-' + handle + '-modal-drag-pin" class="modal elementselectormodal easyaddressfield-modal-drag-pin"/>').appendTo(Garnish.$bod),
                $body = $('<div class="body"/>').appendTo($modal).html('<div id="easyaddressfield-' + handle + '-drag-pin-canvas" style="height:100%"></div>'),
                $footer = $('<footer class="footer"/>').appendTo($modal),
                $buttons = $('<div class="buttons right"/>').appendTo($footer),
                $cancelBtn = $('<div class="btn modal-cancel">' + Craft.t('app', 'Cancel') + '</div>').appendTo($buttons);
            $okBtn = $('<input type="submit" class="btn submit modal-submit-drag-pin" value="' + Craft.t('app', 'Done') + '"/>').appendTo($buttons);

            dragPin[handle]['modal'] = new Garnish.Modal($modal);
        }

        var mapCanvas = document.getElementById('easyaddressfield-' + handle + '-drag-pin-canvas');

        var refreshIntervalId = setInterval(function () {

            if ($(mapCanvas).width()) {

                var existingCoords;
                var zoom = 11;

                var lat = $('#' + handle + '-latitude').val();
                var lng = $('#' + handle + '-longitude').val();

                var defaultData = $('#' + handle + '-drag-pin-data').data();
                if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                    existingCoords = {
                        'lat': lat,
                        'lng': lng
                    };
                } else if (defaultData.default) {
                    existingCoords = {
                        'lat': defaultData.lat,
                        'lng': defaultData.lng
                    };
                    zoom = defaultData.zoom;
                } else {
                    existingCoords = false;
                }

                var coords = getCoordinates(handle, existingCoords);
                var marker = renderMap(handle, mapCanvas, coords, zoom);

                $('.modal-cancel').on('click', function () {
                    dragPin[handle]['modal'].hide();
                });

                $('.modal-submit-drag-pin').on('click', function () {
                    $('#' + handle + '-latitude').val(marker.getPosition().lat());
                    $('#' + handle + '-longitude').val(marker.getPosition().lng());
                    dragPin[handle]['modal'].hide();
                    return false;
                });

                clearInterval(refreshIntervalId);
            }

        }, 10);

    }

    function getCoordinates(handle, existingCoords) {

        var coords;

        if (existingCoords) {
            coords = existingCoords;
        } else {

            coords = {
                'lat': 0,
                'lng': 0
            };

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var center = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    dragPin[handle]['marker'].setPosition(center);
                    dragPin[handle]['map'].panTo(center);
                });
            }
        }

        return new google.maps.LatLng(coords.lat, coords.lng);
    }

    function renderMap(handle, mapCanvas, coords, zoom) {

        if (dragPin[handle]['map']) {
            dragPin[handle]['marker'].setMap(null);
            dragPin[handle]['map'].panTo(coords);
        } else {
            var mapOptions = {
                center: coords,
                zoom: zoom,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            dragPin[handle]['map'] = new google.maps.Map(mapCanvas, mapOptions);
        }

        dragPin[handle]['marker'] = new google.maps.Marker({
            position: coords,
            map: dragPin[handle]['map'],
            draggable: true
        });

        google.maps.event.addListener(dragPin[handle]['marker'], 'dragend', function (event) {
            dragPin[handle]['map'].panTo(event.latLng);
        });

        return dragPin[handle]['marker'];
    }


})(jQuery, window, document);

;(function ($, window, document, undefined) {

    var handle;
    var address = {};
    var dragPin = {};

    $(document).on('click', '.easyaddressfield-marker', function () {
        var $handle = $(this).closest('.easyaddressfield-field').attr('id');
        modalDragPinOSM($handle);
    });

    function modalDragPinOSM(handle) {
        if (handle in dragPin && dragPin[handle]['modal']) {
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
        dragPin[handle]['map'] = false;

        var mapCanvas = document.getElementById('easyaddressfield-' + handle + '-drag-pin-canvas');

        var refreshIntervalId = setInterval(function () {
            if ($(mapCanvas).width()) {
                var existingCoords;
                var zoom = 15;

                var lat = $('#' + handle + '-latitude').val();
                var lng = $('#' + handle + '-longitude').val();
                var link  = $('#' + handle + '-drag-pin-data');
                var defaultData = { 'lat': link.data('lat'), 'lng': link.data('lng')};

                if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                    coords = {
                        'lat': lat,
                        'lng': lng
                    };
                } else if (defaultData) {
                    coords = {
                        'lat': defaultData.lat,
                        'lng': defaultData.lng
                    };
                }
                zoom = defaultData.zoom;
                var marker = renderMapOSM(handle, mapCanvas, coords, zoom);

                $('.modal-cancel').on('click', function () {
                    dragPin[handle]['modal'].hide();
                    dragPin[handle] = [];
                });

                $('.modal-submit-drag-pin').on('click', function () {
                    dragPin[handle]['modal'].hide();
                    dragPin[handle] = [];
                    $('#' + handle + '-latitude').val(marker.getLatLng().lat);
                    $('#' + handle + '-longitude').val(marker.getLatLng().lng);
                    return false;
                });
                clearInterval(refreshIntervalId);
            }

        }, 10);
    }

    function renderMapOSM(handle, mapCanvas, coords, zoom = 14) {
        if(dragPin[handle]['map'] === false) {
            dragPin[handle]['map'] = L.map(mapCanvas).setView([coords.lat,coords.lng], zoom);
        }

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Data ©<a href="http://osm.org/copyright">OpenStreetMap</a>',
            maxZoom: 18
        }).addTo(dragPin[handle]['map']);
        var marker = L.marker([coords.lat,coords.lng], {draggable:'true'}).addTo(dragPin[handle]['map']);
        marker.on('dragend', function(event){
            var marker = event.target;
            var position = marker.getLatLng();
            marker.setLatLng(new L.LatLng(position.lat, position.lng),{draggable:'true'});
            dragPin[handle]['map'].panTo(new L.LatLng(position.lat, position.lng))
        });
        dragPin[handle]['map'].addLayer(marker);
        return marker;
    }
})(jQuery, window, document);

class MapsBuilder
{
    constructor() {
        this._buildMaps();
    }

    _buildMaps() {
        let maps = document.querySelectorAll('[data-type="leaflet-map"]');
        maps.forEach((map) => this._buildMap(map));
    }

    _buildMap(map)
    {

        let addMarkerControl =  L.Control.extend({

            options: {
                position: 'topleft'
            },

            onAdd: function (map) {
                let container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                let linkAddMarker = L.DomUtil.create('a', 'leaflet-control-add-marker')
                linkAddMarker.onclick = function(){
                    // Generate unique id
                    let markerId = 'lol';
                    let marker = L.marker(map.getCenter(), {draggable: true, markerId}).addTo(map);

                    let popup = L.popup();
                    popup.setContent('<textarea style="width: 250px; resize: none;"></textarea>');

                    marker.bindPopup(popup);
                    setTimeout(function(){
                        marker.openPopup();
                        // focus textarea
                    }, 100);

                    map.on('popupopen', function(e){
                        $(e.popup._contentNode).find('textarea').focus();
                    });

                    marker.on('dragend', function(e){
                        let $input = $(map._container.querySelector('input.value'));
                        let tmpValue = $input.prop('tmpValue') || {};
                        debugger
                    });
                }
                container.append(linkAddMarker);
                return container;
            }
        });

        let mymap = L.map(map).setView([51.505, -0.09], 13);
        const mapBoxToken = 'pk.eyJ1IjoiZXJ1dWFuIiwiYSI6ImNrMnA2czhvbDAxNGQzY3J1MjY0YzZkMXIifQ.U0oT_VSdkZ8wjLSFpxyhMg';
        L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=' + mapBoxToken, {
            attribution: '<a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox.streets',
            accessToken: mapBoxToken
        }).addTo(mymap);
        mymap.addControl(new addMarkerControl());

    }

}

new MapsBuilder();
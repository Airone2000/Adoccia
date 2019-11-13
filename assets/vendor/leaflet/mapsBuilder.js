import uniqid from 'uniqid';
import {fetch as aFetch} from 'whatwg-fetch';
import places from 'places.js';

class MapsBuilder
{
    constructor() {
        this.MAPBOX_PUBLIC_KEY = 'pk.eyJ1IjoiZXJ1dWFuIiwiYSI6ImNrMnA2czhvbDAxNGQzY3J1MjY0YzZkMXIifQ.U0oT_VSdkZ8wjLSFpxyhMg';
        this.cachedInputValues = {};
        this.timeoutMarker = null;

        this._buildMaps();
        this._lockSearchLocationFormOnSubmit();
    }

    _lockSearchLocationFormOnSubmit() {
        $('.SearchLocationForm').submit((e) => {
            e.preventDefault();
        });
    }

    registerJSONMap(mapElement) {
        let mapId = mapElement.id;
        let $element = $(mapElement);
        let jsonMap = JSON.parse($element.find('input.value').val());
        this.cachedInputValues[mapId] = jsonMap;

        // If not undefined, then it's an array -> to convert to object
        if (jsonMap.markers.length === 0) {
            jsonMap.markers = {};
        }

        // Default values here
        if (jsonMap.center === null) {
            jsonMap.center = {lat: 0, lng: 0};
        }

        // Default zoom
        if (jsonMap.zoom === null) {
            jsonMap.zoom = 1;
        }

        return jsonMap;
    }

    getJSONMap(map) {
        let id = map._container.id;
        return this.cachedInputValues[id];
    }

    setJSONMapValue(map, attribute, value) {
        let jsonMap = this.getJSONMap(map);
        jsonMap[attribute] = value;
        this.saveJSONMap(map);
    }

    saveJSONMap(map) {
        let id = map._container.id;
        let $value = $(`#${id} input.value`);
        if ($value.length === 1) {
            let jsonMap = this.getJSONMap(map);
            jsonMap = JSON.stringify(jsonMap);
            $value.val(jsonMap);
            $value.attr('value', jsonMap);
        }
    }

    _buildMaps() {
        let maps = document.querySelectorAll('[data-type="leaflet-map"]');
        maps.forEach((map) => this._buildMap(map));
    }

    _addMarkerToMap(map, markerData = null) {

        let mode = map._container.dataset.mode;
        let markerId, markerPosition, markerLabel, autoOpenPopup, saveInJsonMap;

        // Data already exists
        if (markerData === null) {
            autoOpenPopup = true;
            markerId = uniqid();
            markerPosition = map.getCenter();
            markerLabel = null;
            saveInJsonMap = true;
        }
        else {
            autoOpenPopup = false;
            markerId = markerData['id'];
            markerPosition = markerData['position'];
            markerLabel = markerData['label'];
            saveInJsonMap = false;
        }

        if (mode !== 'DISPLAY') {
            markerLabel = `<textarea style="width: 250px; resize: none;">${markerLabel}</textarea>`;
        }

        let marker = L.marker(markerPosition, {draggable: mode !== 'DISPLAY', markerId}).addTo(map);

        // Register marker in JSONMap
        if (saveInJsonMap) {
            let jsonMap = this.getJSONMap(map);
            jsonMap.markers[markerId] = {
                id: markerId,
                position: markerPosition,
                label: null
            };
            this.saveJSONMap(map);
        }

        let popup = L.popup();
        popup.setContent(markerLabel);
        marker.bindPopup(popup);

        // Open popup
        if (autoOpenPopup) {
            setTimeout(function () {
                marker.openPopup();
            }, 100);
        }

        // Save new position of marker each time it changes
        marker.on('dragend', (e) => {
            let markerId = e.target.options.markerId;
            let map = e.target._map;
            let position = e.target._latlng;
            let jsonMap = this.getJSONMap(map);
            let marker = jsonMap.markers[markerId];
            marker.position = position;
            this.saveJSONMap(map);
        });
    }

    _buildAddMarkerControl() {
        let addMarkerControl =  L.Control.extend({
            options: {position: 'topleft'},
            onAdd: (map) => {
                let container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                let linkAddMarker = L.DomUtil.create('a', 'leaflet-control-add-marker');
                linkAddMarker.onclick = () => this._addMarkerToMap(map);
                container.append(linkAddMarker);
                return container;
            }
        });
        return new addMarkerControl();
    }

    _buildSearchForm(uniqIdentifier) {
        let searchForm = L.Control.extend({
            options: {position: 'topright'},
            onAdd: (map) => {
                let container = L.DomUtil.create('div', 'leaflet-bar leaflet-control SearchLocationForm');
                container.innerHTML = `<input id="${uniqIdentifier}" type="search" class="SearchLocationInput" placeholder="Find..." />`;
                return container;
            }
        });
        return new searchForm();
    }

    _buildMap(mapElement)
    {
        let jsonMap = this.registerJSONMap(mapElement);
        let mode = mapElement.dataset.mode;

        // Create a map with tile from MAPBOX
        let map = L.map(mapElement, {
            scrollWheelZoom: false
        }).setView(jsonMap.center, jsonMap.zoom);

        L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=' + this.MAPBOX_PUBLIC_KEY, {
            attribution: '<a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 20,
            id: 'mapbox.streets',
            accessToken: this.MAPBOX_PUBLIC_KEY
        }).addTo(map);

        // Add initial markers that are in jsonMap.markers
        Object.values(jsonMap.markers).forEach((markerData) => {
            this._addMarkerToMap(map, markerData);
        });

        // When popup open ...
        if (mode !== 'DISPLAY') {

            // Add control to map
            let uniqIdentifier = uniqid('map');
            map.addControl(this._buildAddMarkerControl());
            map.addControl(this._buildSearchForm(uniqIdentifier));

            let placesAutocomplete = places({
                appId: 'plM0G3WSJJ2P',
                apiKey: '6ab7181da7b42d2d74a99fb760f146e1',
                container: document.getElementById(uniqIdentifier)
            });

            map.on('popupopen', (e) => {
                let $textarea = $(e.popup._contentNode).find('textarea');
                let markerId = e.popup._source.options.markerId;
                let map = e.target;
                let jsonMap = this.getJSONMap(map);
                let marker = jsonMap.markers[markerId];

                let label = marker.label;
                $textarea.val(label);

                $textarea.focus();
                $textarea.on('input', (textEvt) => {
                    clearTimeout(this.timeoutMarker);
                    this.timeoutMarker = setTimeout(() => {
                        let label = $(textEvt.target).val().trim();
                        if (label.length === 0) label = null;
                        marker.label = label;
                        this.saveJSONMap(map);
                    }, 250);
                });
            });

            // When zoom end ...
            map.on('zoomend', (e) => {
                this.setJSONMapValue(e.target, 'zoom', e.target._zoom);
            });

            // When map is moved ...
            map.on('moveend', (e) => {
                this.setJSONMapValue(e.target, 'center', e.target.getCenter());
            });
        }
    }

}

new MapsBuilder();
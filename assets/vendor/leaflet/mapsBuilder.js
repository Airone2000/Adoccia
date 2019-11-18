import uniqid from 'uniqid';
import {fetch as aFetch} from 'whatwg-fetch';
require('./mapsBuilder.scss');


L.Control.Photon = L.Control.extend({

    includes: L.Mixin.Events,
    querying: false,

    options: {
        url: 'https://photon.komoot.de/api/?',
        placeholder: "Aller à...",
        emptyMessage: "Aucun résultat",
        minChar: 3,
        limit: 10,
        submitDelay: 300,
        includePosition: true,
        noResultLabel: "Aucun résultat",
        lang: 'fr'
    },

    CACHE: '',
    RESULTS: [],
    KEYS: {
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        TAB: 9,
        RETURN: 13,
        ESC: 27,
        APPLE: 91,
        SHIFT: 16,
        ALT: 17,
        CTRL: 18
    },

    onAdd: function (map, options) {
        this.map = map;
        this.container = L.DomUtil.create('div', 'leaflet-photon');

        this.options = L.Util.extend(this.options, options);
        let CURRENT = null;

        try {
            Object.defineProperty(this, "CURRENT", {
                get: function () {
                    return CURRENT;
                },
                set: function (index) {
                    if (typeof index === "object") {
                        index = this.resultToIndex(index);
                    }
                    CURRENT = index;
                }
            });
        } catch (e) {
            // Hello IE8
        }

        this.createInput();
        this.createResultsContainer();
        return this.container;
    },

    createInput: function () {
        this.input = L.DomUtil.create('input', 'photon-input', this.container);
        this.input.type = 'text';
        this.input.placeholder = this.options.placeholder;
        this.input.autocomplete = 'off';
        L.DomEvent.disableClickPropagation(this.input);

        L.DomEvent.on(this.input, 'keydown', this.onKeyDown, this);
        L.DomEvent.on(this.input, 'keyup', this.onKeyUp, this);
        L.DomEvent.on(this.input, 'blur', this.onBlur, this);
        L.DomEvent.on(this.input, 'focus', this.onFocus, this);
    },

    createResultsContainer: function () {
        this.resultsContainer = L.DomUtil.create('ul', 'photon-autocomplete', document.querySelector('body'));
    },

    resizeContainer: function()
    {
        let l = this.getLeft(this.input);
        let t = this.getTop(this.input) + this.input.offsetHeight;
        this.resultsContainer.style.left = l + 'px';
        this.resultsContainer.style.top = t + 'px';
        let width = this.options.width ? this.options.width : this.input.offsetWidth - 2;
        this.resultsContainer.style.width = width + "px";
    },

    onKeyDown: function (e) {
        switch (e.keyCode) {
            case this.KEYS.TAB:
                if(this.CURRENT !== null)
                {
                    this.setChoice();
                }
                L.DomEvent.stop(e);
                break;
            case this.KEYS.RETURN:
                L.DomEvent.stop(e);
                this.setChoice();
                break;
            case this.KEYS.ESC:
                L.DomEvent.stop(e);
                this.hide();
                this.input.blur();
                break;
            case this.KEYS.DOWN:
                if(this.RESULTS.length > 0) {
                    if(this.CURRENT !== null && this.CURRENT < this.RESULTS.length - 1) { // what if one resutl?
                        this.CURRENT++;
                        this.highlight();
                    }
                    else if(this.CURRENT === null) {
                        this.CURRENT = 0;
                        this.highlight();
                    }
                }
                break;
            case this.KEYS.UP:
                if(this.CURRENT !== null) {
                    L.DomEvent.stop(e);
                }
                if(this.RESULTS.length > 0) {
                    if(this.CURRENT > 0) {
                        this.CURRENT--;
                        this.highlight();
                    }
                    else if(this.CURRENT === 0) {
                        this.CURRENT = null;
                        this.highlight();
                    }
                }
                break;
        }
    },

    onKeyUp: function (e) {
        let special = [
            this.KEYS.TAB,
            this.KEYS.RETURN,
            this.KEYS.LEFT,
            this.KEYS.RIGHT,
            this.KEYS.DOWN,
            this.KEYS.UP,
            this.KEYS.APPLE,
            this.KEYS.SHIFT,
            this.KEYS.ALT,
            this.KEYS.CTRL
        ];
        if (special.indexOf(e.keyCode) === -1)
        {
            if (typeof this.submitDelay === "number") {
                window.clearTimeout(this.submitDelay);
                delete this.submitDelay;
            }
            this.submitDelay = window.setTimeout(L.Util.bind(this.search, this), this.options.submitDelay);
        }
    },

    onBlur: function (e) {
        this.fire('blur');
        let self = this;
        setTimeout(function () {
            self.hide();
        }, 100);
    },

    onFocus: function (e) {
        this.fire('focus');
        this.input.select();
    },

    clear: function () {
        this.RESULTS = [];
        this.CURRENT = null;
        this.CACHE = '';
        this.resultsContainer.innerHTML = '';
    },

    hide: function() {
        this.fire('hide');
        this.clear();
        this.resultsContainer.style.display = 'none';
    },

    setChoice: function (choice) {
        choice = choice || this.RESULTS[this.CURRENT];
        if (choice) {
            this.hide();
            this.input.value = "";
            this.fire('selected', {choice: choice.feature});
            this.onSelected(choice.feature);
        }
    },

    search: function() {
        let val = this.input.value;
        console.log(val,this.options.minChar);
        if (val.length < this.options.minChar) {
            this.clear();
            return;
        }
        if(!val) {
            this.clear();
            return;
        }
        if( val + '' === this.CACHE + '') {
            return;
        }
        else {
            this.CACHE = val;
        }
        this._do_search(val);
    },

    _do_search: function (val) {
        this.ajax(val, this.handleResults, this);
    },

    _onSelected: function (feature) {
        this.map.setView([feature.geometry.coordinates[1], feature.geometry.coordinates[0]], 16);
    },

    onSelected: function (choice) {
        return (this.options.onSelected || this._onSelected).call(this, choice);
    },

    _formatResult: function (feature, el) {
        let title = L.DomUtil.create('strong', '', el),
            detailsContainer = L.DomUtil.create('small', '', el),
            details = [],
            type = this.formatType(feature);
        title.innerHTML = feature.properties.name;
        if (type) details.push(type);
        if (feature.properties.city && feature.properties.city !== feature.properties.name) {
            details.push(feature.properties.city);
        }
        if (feature.properties.country) details.push(feature.properties.country);
        detailsContainer.innerHTML = details.join(', ');
    },

    formatResult: function (feature, el) {
        return (this.options.formatResult || this._formatResult).call(this, feature, el);
    },

    formatType: function (feature) {
        return (this.options.formatType || this._formatType).call(this, feature);
    },

    _formatType: function (feature) {
        return '';
        return feature.properties.osm_value;
    },

    createResult: function (feature) {
        let el = L.DomUtil.create('li', '', this.resultsContainer);
        this.formatResult(feature, el);
        let result = {
            feature: feature,
            el: el
        };
        // Touch handling needed
        L.DomEvent.on(el, 'mouseover', function (e) {
            this.CURRENT = result;
            this.highlight();
        }, this);
        L.DomEvent.on(el, 'mousedown', function (e) {
            this.setChoice();
        }, this);
        return result;
    },

    resultToIndex: function (result) {
        let out = null;
        this.forEach(this.RESULTS, function (item, index) {
            if (item === result) {
                out = index;
                return;
            }
        });
        return out;
    },

    handleResults: function(geojson) {
        let self = this;
        this.clear();
        this.resultsContainer.style.display = "block";
        this.resizeContainer();
        this.forEach(geojson.features, function (feature, index) {
            self.RESULTS.push(self.createResult(feature));
        });
        if (geojson.features.length === 0) {
            let noresult = L.DomUtil.create('li', 'photon-no-result', this.resultsContainer);
            noresult.innerHTML = this.options.noResultLabel;
        }
        if (this.options.feedbackEmail) {
            let feedback = L.DomUtil.create('a', 'photon-feedback', this.resultsContainer);
            feedback.href = "mailto:" + this.options.feedbackEmail;
            feedback.innerHTML = "Feedback";
        }
        this.CURRENT = 0;
        this.highlight();
        if (this.options.resultsHandler) {
            this.options.resultsHandler(geojson);
        }
    },

    highlight: function () {
        let self = this;
        this.forEach(this.RESULTS, function (item, index) {
            if (index === self.CURRENT) {
                L.DomUtil.addClass(item.el, 'on');
            }
            else {
                L.DomUtil.removeClass(item.el, 'on');
            }
        });
    },

    getLeft: function (el) {
        let tmp = el.offsetLeft;
        el = el.offsetParent;
        while(el) {
            tmp += el.offsetLeft;
            el = el.offsetParent;
        }
        return tmp;
    },

    getTop: function (el) {
        let tmp = el.offsetTop;
        el = el.offsetParent;
        while(el) {
            tmp += el.offsetTop;
            el = el.offsetParent;
        }
        return tmp;
    },

    forEach: function (els, callback) {
        Array.prototype.forEach.call(els, callback);
    },

    ajax: function (val, callback, thisobj) {

        if (this.querying) {
            return;
        }

        this.querying = true;

        // Prevent from sending the request if there's already one

        let params = {
            q: val,
            lang: this.options.lang,
            limit: this.options.limit
        };

        let url = this.options.url + this.buildQueryString(params);

        this.fire('ajax:send');
        aFetch(url, {method: 'get'})
            .then(response => {
                if (response.status === 200) {
                    return response.json();
                }
                else throw new Error();
            })
            .then(jsonResponse => {
                callback.call(thisobj || this, jsonResponse);
            })
            .catch(() => {})
            .finally(() => {
                this.querying = false;
            })
        ;
    },

    buildQueryString: function (params) {
        let query_string = [];
        for (let key in params) {
            if (params[key]) {
                query_string.push(encodeURIComponent(key) + "=" + encodeURIComponent(params[key]));
            }
        }
        return query_string.join('&');
    }

});

L.Map.addInitHook(function () {
    if (this.options.photonControl) {
        this.photonControl = new L.Control.Photon(this.options.photonControlOptions || {});
        this.addControl(this.photonControl);
    }
});


class MapsBuilder
{
    constructor() {
        this.MAPBOX_PUBLIC_KEY = 'pk.eyJ1IjoiZXJ1dWFuIiwiYSI6ImNrMnA2czhvbDAxNGQzY3J1MjY0YzZkMXIifQ.U0oT_VSdkZ8wjLSFpxyhMg';
        this.cachedInputValues = {};
        this.timeoutMarker = null;
        this.findPlacesTimeout = null;

        this._buildMaps();
        this._listenForFindPlaces();
    }

    _listenForFindPlaces() {
        $(document).on('keyup', '.SearchLocationInput', (e) => {
            clearTimeout(this.findPlacesTimeout);
            this.findPlacesTimeout = setTimeout(() => {
                let $input = $(e.target);
                let value = $input.val();
                let $resultContainer = $input.next('datalist');
                let url = `https://photon.komoot.de/api/?q=${value}&limit=10&lang=fr`;
                aFetch(url, {method: 'get'})
                    .then(response => {
                        if (response.status === 200) {
                            return response.json();
                        }
                        else throw new Error();
                    })
                    .then(jsonResponse => {
                        $resultContainer.find('option:not(:first)').remove();
                        jsonResponse.features.forEach((result) => {
                            console.log(result);
                            $resultContainer.append(`<option>${result.properties.name}, ${result.properties.country}</option>`);
                        });
                    })
                    .catch(() => {})
                ;
            }, 300);
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
        let searchForm = L.Control.Photon;
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
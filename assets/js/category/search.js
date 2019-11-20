import {fetch as aFetch} from "whatwg-fetch";

require('./../../css/category/search.scss');
import $ from './../../vendor/jquery/jquery';
import autoComplete from './../../vendor/vanilla-autocomplete/auto-complete.js';
require('./../../vendor/vanilla-autocomplete/auto-complete.css');

class Search {
    constructor() {
        this._listenForCriteriaPicked();
        this._listenForGeolocateMe();
        this._buildLocationAutocomplete();

        // Keypress takes precedence on keyup
        // This is required when selecting places in autocomplete with enter
        // to prevent the form from being submitted before the user
        // has finished
        $('form input.mapSearchLocationInput').on('keypress', function(e) {
            return e.which !== 13;
        });
    }

    _buildLocationAutocomplete() {
        $(document).find('.mapSearchLocationInput').each((idx, elmt) => {
            let elmtId = elmt.id;
            let $elmt = $(elmt);
            new autoComplete({
                selector: `#${elmtId}`,
                minChars: 1,
                cache: false,
                renderItem: (item, search) => {
                    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    let re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
                    return '<div class="autocomplete-suggestion" data-val="' + item.label + '" data-lat="'+item.lat+'" data-lng="'+item.lng+'">' + item.label.replace(re, "<b>$1</b>") + '</div>';
                },
                source: (term, suggest) => {
                    term = term.toLowerCase();
                    let choices = [];
                    let coords = {};
                    let suggestions = [];

                    let url = `https://photon.komoot.de/api/?q=${term}&limit=15&lang=fr`;
                    aFetch(url, {method: 'get'})
                        .then(response => {
                            if (response.status === 200) {
                                return response.json();
                            }
                            else throw new Error();
                        })
                        .then(jsonResponse => {
                            jsonResponse.features.forEach((result) => {
                                let place = `${result.properties.name}, ${result.properties.country}`;
                                if (choices.indexOf(place) < 0) {
                                    choices.push(place);
                                    let resultCoords = {
                                        lat: result.geometry.coordinates[1],
                                        lng: result.geometry.coordinates[0]
                                    };
                                    coords[place] = resultCoords;
                                }
                            });

                            for (let i=0;i<choices.length;i++)
                                if (~choices[i].toLowerCase().indexOf(term)) suggestions.push({
                                    label: choices[i],
                                    lat: coords[choices[i]]['lat'],
                                    lng: coords[choices[i]]['lng']
                                });
                            suggest(suggestions);
                        })
                    ;
                },
                delay: 250,
                onSelect: (e, term, item) => {
                    let lat = item.getAttribute('data-lat');
                    let lng = item.getAttribute('data-lng');
                    let $latLngHiddenInput = $elmt.parents('.sinput.mapAround').find('.mapSearchLatLngInput');
                    let $searchLocationInput = $elmt.parents('.sinput.mapAround').find('.mapSearchLocationInput');
                    let $selectedLocation = $elmt.parents('.sinput.mapAround').find('.mapSearchSelectedLocation');
                    $selectedLocation.val(term);
                    let value = JSON.stringify({lat, lng});
                    $latLngHiddenInput.val(value);
                    $searchLocationInput.val('');
                }
            });
        });
    }

    _listenForGeolocateMe() {
        $(document).on('click', 'button.findMe', (e) => {
            e.preventDefault();

            let $button = $(e.target);
            let uniqid = $button.data('uniqid');
            let $targetDisplay = $(document).find(`.selectedLocation_${uniqid}`);
            let $targetValue = $(document).find(`.latlng_${uniqid}`);

            if (navigator.geolocation) {
                $targetDisplay.val('Patientez, nous recherchons votre position ...');
                navigator.geolocation.getCurrentPosition((position) => {
                    let coords = JSON.stringify({
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    });

                    $targetDisplay.val('Position actuelle');
                    $targetValue.val(coords);
                }, () => {
                    $targetDisplay.prop('readonly', false);
                    $targetDisplay.val('Erreur');
                }, {
                    timeout: 5000
                });
            }
            else {
                $targetDisplay.prop('readonly', false);
                $targetDisplay.val('Erreur');
            }
        });
    }

    // Display inputs associated to criteria
    _listenForCriteriaPicked() {
        $(document).on('change', 'select[name$="[criteria]"]', (e) => {
            let $picker = $(e.target);
            let $itemSelected = $($picker.children(':selected')[0]);
            let inputsToDisplaySelector = $itemSelected.data('inputs');

            $picker.siblings('*').addClass('hidden');
            if (inputsToDisplaySelector) {
                $picker.siblings(inputsToDisplaySelector).removeClass('hidden');
            }
        });

        // Make inputs appear by triggering a first change on criteriaPicker
        $(document).find('select[name$="[criteria]"]').trigger('change');
    }
}

new Search();
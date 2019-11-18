require('./../../css/category/search.scss');
import $ from './../../vendor/jquery/jquery';

class Search {
    constructor() {
        this._listenForCriteriaPicked();
        this._listenForGeolocateMe();
    }

    _listenForGeolocateMe() {
        $(document).on('click', 'button.findMe', (e) => {
            e.preventDefault();

            let $button = $(e.target);
            let uniqid = $button.data('uniqid');
            let $targetDisplay = $(document).find(`.location_${uniqid}`);
            let $targetValue = $(document).find(`.latlng_${uniqid}`);

            if (navigator.geolocation) {
                $targetDisplay.val('Patientez...');
                navigator.geolocation.getCurrentPosition((position) => {
                    let coords = JSON.stringify({
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    });

                    $targetDisplay.val('Position actuelle');
                    $targetValue.val(coords);
                });
            }
            else {
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
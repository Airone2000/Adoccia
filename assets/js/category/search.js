require('./../../css/category/search.scss');
import $ from './../../vendor/jquery/jquery';

class Search {
    constructor() {
        this._listenForCriteriaPicked();
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
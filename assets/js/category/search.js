require('./../../css/category/search.scss');
import $ from './../../vendor/jquery/jquery';

class Search {
    constructor() {
        this._listenForCriteriaPicked();
    }

    // Display value2 field to allow the user to insert a second value in the advanced search
    _listenForCriteriaPicked() {
        $(document).on('change', 'select[name$="[criteria]"]', (e) => {
            let $picker = $(e.target);
            let $itemSelected = $($picker.children(':selected')[0]);
            let $value2 = $picker.siblings('.value2.hidden');
            if ($itemSelected.hasClass('display-value2')) {
                $value2.removeClass('hidden').addClass('visible');
            }
            else {
                $value2.removeClass('visible').addClass('hidden');
            }
        });
    }
}

new Search();
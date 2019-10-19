require('./../../css/form/edit.scss');
import $ from './../../vendor/jquery/jquery';
import Sortable from './../../vendor/Sortable/Sortable';
import {fetch as aFetch} from 'whatwg-fetch';

class Edit
{
    constructor() {

        this.timeoutResize = null;
        this.timeoutSort = null;
        this.loading = false;

        this._makeItSortable();
        this._listenForResizeArea();
        this._listenForAddArea();
        this._listenForDeleteArea();
        this._listenForWidgetTypePicked();
        this._listenForOpenModalSetWidgetOptions();
    }

    _makeFormLoading() {
        this.loading = true;
        $('#formWrapper').addClass('loading');
    }

    _stopFormLoading() {
        this.loading = false;
        $('#formWrapper').removeClass('loading');
    }

    _makeItSortable() {
        let el = document.getElementById('form');
        new Sortable(el, {
            animation: 150,
            chosenClass: "sortable-chosen",
            dragClass: "sortable-drag",
            easing: "cubic-bezier(1, 0, 0, 1)",
            handle: '.handle',
            draggable: '.area',
            onEnd: () => {

                clearTimeout(this.timeoutSort);

                // Build a map area -> position
                let map = {};
                let $areas = $('#form').find('.area');
                $areas.each((areaIdx, area) => {
                    map[$(area).data('id')] = areaIdx + 1;
                });

                let $form = $('#form');
                const formId = $form.data('id') || 0;
                let url = endpoints.sortFormAreas;
                url = url.replace(':id', formId);

                this._makeFormLoading();

                let body = JSON.stringify(map);
                let headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                };

                this.timeoutSort = setTimeout(() => {
                    aFetch(url, {method: 'post', headers, body})
                        .then(response => {
                            switch (response.status) {
                                case 204:
                                    break;
                                case 401:
                                    window.location.href = endpoints.login;
                                    break;
                                default:
                                    alert('ERROR');
                            }
                        })
                    ;
                }, 250);
            }
        });
    }

    _listenForResizeArea() {
        $(document).on('change', '.resizer', (evt) => {

            clearTimeout(this.timeoutResize);

            let $resizer = $(evt.target);
            const selectedSize = +$resizer.val();
            const maxSize = +$resizer.prop('max');
            const minSize = +$resizer.prop('min');
            let size = selectedSize;
            size = size < minSize ? minSize : size;
            size = size > maxSize ? maxSize : size;
            let $parentArea = $resizer.parents('.area');
            let originalSize = $parentArea.data('size');
            $resizer.val(size);
            $parentArea.css('width', 'calc('+size+'% - 20px)');

            let $form = $('#form');
            const formId = $form.data('id') || 0;
            let $area = $resizer.parents('.area');
            let areaId = $area.data('id');
            let url = endpoints.setFormAreaSize;
            url = url.replace(':formId', formId);
            url = url.replace(':formAreaId', areaId);

            let headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            };
            let body = JSON.stringify({size});

            this.timeoutResize = setTimeout(() => {
                aFetch(url, {method: 'put', headers, body})
                    .then(response => {
                        switch (response.status) {
                            case 204:
                                break;
                            case 401:
                                window.location.href = endpoints.login;
                                break;
                            default:
                                $parentArea.css('width', 'calc('+originalSize+'% - 20px)');
                                $resizer.val(originalSize);
                        }
                    })
                ;
            }, 250);
        });
    }

    _listenForAddArea() {
        $('.add-formArea').on('click', function(){

            let url = endpoints.addFormAreaToForm;
            let $form = $('#form');
            const formId = $form.data('id') || 0;
            url = url.replace(':id', formId);

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            aFetch(url, {'method': 'post', headers})
                .then(response => {
                    switch (response.status) {
                        case 200:
                            return response.json();
                        case 401:
                            window.location.href = endpoints.login;
                            break;
                        default:
                            alert('ERROR');
                    }
                })
                .then(response => {
                    $form.append(response.view);
                })
            ;

        });
    }

    _listenForDeleteArea() {
        $(document).on('click', '.delete-formArea', function(){

            let $form = $('#form');
            const formId = $form.data('id') || 0;
            let $area = $(this).parents('.area');
            let areaId = $area.data('id') || 0;
            let url = endpoints.deleteFormArea;
            url = url.replace(':formId', formId);
            url = url.replace(':formAreaId', areaId);

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            aFetch(url, {method: 'delete', headers})
                .then(response => {
                    switch (response.status) {
                        case 204:
                            $area.css('opacity', 0);
                            setTimeout(function(){
                                $area.remove();
                            }, 200);
                            break;
                        case 401:
                            window.location.href = endpoints.login;
                            break;
                        default:
                            alert('ERROR');
                    }
                })
            ;
        });
    }

    _listenForWidgetTypePicked() {
        $(document).on('change', '.widgetTypePicker', (e) => {
            let $picker = $(e.target);
            let pickedWidget = $picker.val();

            let $form = $('#form');
            const formId = $form.data('id') || 0;
            let $area = $picker.parents('.area');
            let areaId = $area.data('id');
            let url = endpoints.changeFormAreaWidgetType;
            url = url.replace(':formId', formId);
            url = url.replace(':formAreaId', areaId);

            let headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            };
            let body = JSON.stringify({type: pickedWidget});

            this.timeoutResize = setTimeout(() => {
                aFetch(url, {method: 'put', headers, body})
                    .then(response => {
                        switch (response.status) {
                            case 200:
                                return response.json();
                                break;
                            case 401:
                                window.location.href = endpoints.login;
                                break;
                            default:
                                alert('ERROR');
                        }
                    })
                    .then(response => {
                        $area.replaceWith($(response.view));
                    })
                ;
            }, 250);
        });
    }

    _listenForOpenModalSetWidgetOptions() {

        let $wrapperModal = $('#wrapperModalSetWidgetOptions');
        let $wrapperModalContent = $wrapperModal.find('.content');

        $wrapperModal.on('click', function(){
            $wrapperModalContent.empty();
            $wrapperModal.addClass('hidden');
        });

        $wrapperModalContent.on('click', function(e) {
            e.stopPropagation();
        });

        $(document).on('click', '.openModalSetWidgetOptions', (e) => {
            let $button = $(e.target);
            let $form = $('#form');
            const formId = $form.data('id') || 0;
            let $area = $button.parents('.area');
            let areaId = $area.data('id');
            let url = endpoints.getFormAreaWidgetSettingsView;
            url = url.replace(':formId', formId);
            url = url.replace(':formAreaId', areaId);

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            aFetch(url, {'method': 'get', headers})
                .then(response => {
                    switch (response.status) {
                        case 200:
                            return response.json();
                        case 401:
                            window.location.href = endpoints.login;
                            break;
                        default:
                            alert('ERROR');
                    }
                })
                .then(response => {
                    $wrapperModalContent.html(response.view);
                    $wrapperModal.removeClass('hidden');
                })
            ;

        });
    }
}

new Edit();
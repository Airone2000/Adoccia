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
        this._listenForSaveOnChange();
    }

    _makeFormSyncing() {
        this.loading = true;
        $('#formWrapper').addClass('loading');
    }

    _stopFormSyncing() {
        this.loading = false;
        setTimeout(() => {
            $('#formWrapper').removeClass('loading');
        }, 650)
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

                this._makeFormSyncing();

                let body = JSON.stringify(map);
                let headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                };

                this.timeoutSort = setTimeout(() => {
                    this._makeFormSyncing();
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
                        .finally(() => {
                            this._stopFormSyncing();
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


            let $area = $resizer.parents('.area');
            let areaId = $area.data('id');
            let url = endpoints.setFormAreaWidth;
            url = url.replace(':id', areaId);

            let headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            };
            let body = JSON.stringify({size});

            this.timeoutResize = setTimeout(() => {
                this._makeFormSyncing();
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
                    .finally(() => {
                        this._stopFormSyncing();
                    })
                ;
            }, 250);
        });
    }

    _listenForAddArea() {
        $('.add-formArea').on('click', () => {

            let url = endpoints.addFormAreaToForm;
            let $form = $('#form');
            const formId = $form.data('id') || 0;
            url = url.replace(':id', formId);

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            this._makeFormSyncing();
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
                .finally(() => {
                    this._stopFormSyncing();
                })
            ;

        });
    }

    _listenForDeleteArea() {
        $(document).on('click', '.delete-formArea', (e) => {

            let $trashBtn = $(e.target);
            let $area = $trashBtn.parents('.area');
            let areaId = $area.data('id') || 0;
            let url = endpoints.deleteFormArea;
            url = url.replace(':id', areaId);

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            this._makeFormSyncing();
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
                .finally(() => {
                    this._stopFormSyncing();
                })
            ;
        });
    }

    _listenForWidgetTypePicked() {
        $(document).on('change', '.widgetTypePicker', (e) => {
            let $picker = $(e.target);
            let pickedWidget = $picker.val();

            let $area = $picker.parents('.area');
            let widgetId = $picker.data('widgetid');
            let url = endpoints.widgetChangeType;
            url = url.replace(':id', widgetId);

            let headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            };
            let body = JSON.stringify({type: pickedWidget});

            this._makeFormSyncing();
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
                .finally(() => {
                    this._stopFormSyncing();
                })
            ;
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
            let widgetId = $button.parents('.area').data('widgetid');
            let url = endpoints.widgetSettingsView;
            url = url.replace(':id', widgetId);

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            this._makeFormSyncing();
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
                .finally(() => {
                    this._stopFormSyncing();
                })
            ;

        });
    }

    _listenForSaveOnChange() {

        this.autoSaveTimeouts = {};

        let handler = (e) => {
            let $input = $(e.target);
            let attribute = $input.data('attribute') || null;
            let value = $input.val().trim();
            value = value.length === 0 ? null : value;

            let widgetId;

            let $parentArea = $input.parents('.area');
            if ($parentArea.length === 1) {
                widgetId = $parentArea.data('widgetid')
            }
            else {
                let $parentSettings = $input.parents('.settings');
                if ($parentSettings.length === 1) {
                    widgetId = $parentSettings.data('widgetid');
                }
            }

            if (widgetId && attribute) {
                let uniqKeyTimeout = `${widgetId}-${attribute}`;
                clearTimeout(this.autoSaveTimeouts[uniqKeyTimeout]);
                this.autoSaveTimeouts[uniqKeyTimeout] = setTimeout(() => {

                    let url = endpoints.widgetSetSetting;
                    url = url.replace(':id', widgetId);

                    let headers = {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json'};
                    let body = JSON.stringify({attribute, value});

                    this._makeFormSyncing();
                    aFetch(url, {method: 'post', headers, body})
                        .then(response => {
                            switch (response.status) {
                                case 204:
                                    console.log('OK');
                                    break;
                                case 401:
                                    window.location.href = endpoints.login;
                                    break;
                                default:
                                    alert('ERROR');
                            }
                        })
                        .finally(() => {
                            this._stopFormSyncing();
                        })
                    ;
                }, 300);
            }
        };

        $(document).on('change', '.saveOnChange', handler);
        $(document).on('input', '.saveOnInput', handler)
    }
}

new Edit();
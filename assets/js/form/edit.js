require('./../../css/form/edit.scss');
import $ from './../../vendor/jquery/jquery';
import Sortable from './../../vendor/Sortable/Sortable';
import {fetch as aFetch} from 'whatwg-fetch';

class Edit
{
    constructor() {

        this.$wrapperModal = $('#wrapperModal');
        this.$wrapperModalContent = this.$wrapperModal.find('.content');
        this.timeoutResize = null;
        this.timeoutSort = null;
        this.loading = false;

        this._initializeModal();
        this._makeItSortable();
        this._listenForResizeArea();
        this._listenForAddArea();
        this._listenForDeleteArea();
        this._listenForWidgetTypePicked();
        this._listenForOpenModalSetWidgetOptions();
        this._listenForPublish();
        this._listenForDeleteDraftForm();
        this._listenForConfigureArea();
        this._listenForFormAreaSettingsSubmit();
        this._listenForWidgetSettingsSubmit();
        this._listenForPreview();
    }

    _initializeModal() {
        this.$wrapperModal.on('click', () => {
            this._hideModal();
        });

        this.$wrapperModalContent.on('click', function(e) {
            e.stopPropagation();
        });
    }

    _redirect(url) {
        window.location.replace(url);
    }

    _hideModal() {
        this.$wrapperModalContent.empty();
        this.$wrapperModal.addClass('hidden');
    }

    _displayError(error) {
        let $errorDisplay = $('#formErrorDisplay');
        $errorDisplay.find('p').text(error);
        $errorDisplay.addClass('visible');
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
                                    this._redirect(endpoints.login);
                                    break;
                                default:
                                    this._displayError('sortError');
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
                                this._redirect(endpoints.login);
                                break;
                            default:
                                $parentArea.css('width', 'calc('+originalSize+'% - 20px)');
                                $resizer.val(originalSize);
                                this._displayError('resizeError');
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
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('addAreaError');
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
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('deleteAreaError');
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
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('setWidgetTypeError');
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
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('openWidgetModalOptionsError');
                    }
                })
                .then(response => {
                    this.$wrapperModalContent.html(response.view);
                    this.$wrapperModal.removeClass('hidden');
                })
                .finally(() => {
                    this._stopFormSyncing();
                })
            ;
        });
    }

    _listenForPublish() {
        $(document).on('click', '.publishDraftForm', (e) => {
            let $buttonPublish = $(e.target);
            let formId = $('#form').data('id');
            let url = endpoints.publishDraftForm;
            url = url.replace(':id', formId);
            let headers = {'X-Requested-With': 'XMLHttpRequest'};

            this._makeFormSyncing();
            $buttonPublish.prop('disabled', true);

            aFetch(url, {method: 'post', headers})
                .then(response => {
                    switch (response.status) {
                        case 204:
                            this._redirect(endpoints.editCategory.replace(':id', categoryId));
                            return;
                        case 401:
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('publishError');
                    }
                })
                .finally(() => {
                    this._stopFormSyncing();
                    $buttonPublish.prop('disabled', false);
                })
            ;
        });
    }

    _listenForDeleteDraftForm() {
        $(document).on('click', '.deleteDraftForm', (e) => {

            let $buttonDelete = $(e.target);
            let url = endpoints.deleteDraftForm.replace(':id', formId);
            let headers = {'X-Requested-With': 'XMLHttpRequest'};

            this._makeFormSyncing();
            $buttonDelete.prop('disabled', true);

            aFetch(url, {method: 'delete', headers})
                .then(response => {
                    switch (response.status) {
                        case 204:
                            this._redirect(endpoints.editCategory.replace(':id', categoryId));
                            return;
                        case 401:
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('deleteFormError');
                    }
                })
                .finally(() => {
                    this._stopFormSyncing();
                    $buttonDelete.prop('disabled', false);
                })
            ;

        });
    }

    _listenForConfigureArea() {

        $(document).on('click', '.configure-formArea', (e) => {
            let $button = $(e.target);
            let areaId = $button.parents('.area').data('id');
            let url = endpoints.formAreasSettingsView.replace(':id', areaId);

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            this._makeFormSyncing();
            aFetch(url, {'method': 'get', headers})
                .then(response => {
                    switch (response.status) {
                        case 200:
                            return response.json();
                        case 401:
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('openModalFormAreaSettings');
                    }
                })
                .then(response => {
                    this.$wrapperModalContent.html(response.view);
                    this.$wrapperModal.removeClass('hidden');
                })
                .finally(() => {
                    this._stopFormSyncing();
                })
            ;

        });
    }

    _listenForFormAreaSettingsSubmit() {
        $(document).on('submit', '#FormArea_SettingsForm', (e) => {
            e.preventDefault();
            let $target = $(e.target);
            let body = new FormData(e.target);
            let url = $target.prop('action');
            let method = $target.prop('method');

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            this._makeFormSyncing();
            aFetch(url, {method, headers, body})
                .then(response => {
                    switch (response.status) {
                        case 204:
                            this._hideModal();
                            break;
                        case 400:
                            response.json().then((jsonResponse) => {
                                this.$wrapperModalContent.html(jsonResponse.view);
                            });
                            break;
                        case 401:
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('openModalFormAreaSettings');
                    }
                })
                .finally(() => {
                    this._stopFormSyncing();
                })
            ;

        });
    }

    _listenForWidgetSettingsSubmit() {
        $(document).on('submit', '#Widget_SettingsForm', (e) => {
            e.preventDefault();
            let $target = $(e.target);
            let body = new FormData(e.target);
            let url = $target.prop('action');
            let method = $target.prop('method');

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            this._makeFormSyncing();
            aFetch(url, {method, headers, body})
                .then(response => {
                    switch (response.status) {
                        case 200:
                            response.json().then((responseJSON) => {
                                let areaId = responseJSON['area'];
                                let newFormAreaView = $(responseJSON['formAreaView']);
                                $(document).find(`#area-${areaId}`).replaceWith(newFormAreaView);
                                this._hideModal();
                            });
                            break;
                        case 400:
                            response.json().then((jsonResponse) => {
                                this.$wrapperModalContent.html(jsonResponse.view);
                            });
                            break;
                        case 401:
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('errorWhileWidgetSetting');
                    }
                })
                .finally(() => {
                    this._stopFormSyncing();
                })
            ;
        });
    }

    _listenForPreview() {
        $(document).on('click', '.preview-draftForm', (e) => {
            e.preventDefault();
            let $btn = $(e.currentTarget);
            let url = $btn.prop('href');

            let headers = {'X-Requested-With': 'XMLHttpRequest'};
            this._makeFormSyncing();

            aFetch(url, {'method': 'get', headers})
                .then(response => {
                    switch (response.status) {
                        case 200:
                            return response.json();
                        case 401:
                            this._redirect(endpoints.login);
                            break;
                        default:
                            this._displayError('openModalPreview');
                    }
                })
                .then(response => {
                    this.$wrapperModalContent.html(response.view);
                    this.$wrapperModal.removeClass('hidden');
                })
                .finally(() => {
                    this._stopFormSyncing();
                })
            ;

        });
    }
}

new Edit();
import {fetch as aFetch} from 'whatwg-fetch';
import uniqid from 'uniqid';

import('croppie/croppie.min.js');
import('croppie/croppie.css');

class PictureUploader
{
    constructor() {
        $(() => this._listenForOpenModalPictureUpload());
    }

    _listenForOpenModalPictureUpload()
    {
        // Give the modal a unique id
        this.modal = $('.modal-background');
        this.modalUid = uniqid();
        this.modal.attr('id', this.modalUid);

        // When openModalBtn is clicked, then, follow the process :
        $('.openPictureUploader').on('click', (e) => {

            // Save the button opener to dispatch event
            this.buttonOpener = e.currentTarget;

            // 1. Open the modal in a loading state
            this._openLoadingModal();
            // 2. Load the picture type
            this._loadPictureType().then((response) => {
                this._listenForFileChange();
                this._listenForSubmit();
            });
        });
    }

    _openLoadingModal()
    {
        this.modal.find('.modal').html('loading ...');
        this.modal.removeClass('hidden');
        this._listenForCloseModal();
    }

    _listenForCloseModal()
    {
        this.modal.find('.modal').click((e) => {
            e.stopPropagation();
        });

        this.modal.click((e) => {
            this.modal.find('.modal').empty();
            this.modal.addClass('hidden');
        });
    }

    _loadPictureType()
    {
        return new Promise((resolve, reject) => {
            let url = endpoints.uploadPicture;
            let headers = {
                'X-Requested-With': 'XMLHttpRequest'
            };

            aFetch(url, {method: 'get', headers})
                .then(response => {
                    if (response.status === 200) {
                        return response.json();
                    }
                    throw new Error('ERROR');
                })
                .then(responseJSON => {
                    this.modal.find('.modal').html(responseJSON.view);
                    resolve(responseJSON);
                })
                .catch(() => {
                    reject(false);
                })
            ;
        });
    }

    _listenForFileChange()
    {
        let $formPicture = this.formPicture = this.modal.find('form');
        $formPicture.find('input[type=file]').on('change', (input) => {

            let $img = $formPicture.find('.picture-preview img');
            let $input =  $(input.target);

            if (input.target.files && input.target.files[0]) {
                let reader = new FileReader();

                reader.onload = (e) => {
                    $uploadCrop.croppie('bind', {
                        url: e.target.result
                    }).then(() => {
                        // bind complete
                    });

                }

                reader.readAsDataURL(input.target.files[0]);
            }

            let $uploadCrop = this.uploadCrop = $input.data('croppie') || $img.croppie({
                viewport: {
                    width: 250,
                    height: 250,
                    type: 'square'
                },
                boundary: { width: 300, height: 300 },
                enableExif: true
            });
            $input.data('croppie', $uploadCrop);

        });
    }

    _listenForSubmit()
    {
        this.formPicture.submit((e) => {
            e.preventDefault();
            this.uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'original'
            }).then((base64Picture) => {

                let url = endpoints.uploadPicture;
                let headers = {
                    'X-Requested-With': 'XMLHttpRequest'
                };
                let body = new FormData(e.currentTarget);
                body.set('picture_uploader[base64Picture]', base64Picture);
                aFetch(url, {method: 'POST', headers, body})
                    .then(response => {
                        if (response.status === 200) {
                            return response.json();
                        }
                        throw new Error('ERROR')
                    })
                    .then(responseJSON => {
                        // Hide modal & trigger special event
                        this.modal.trigger('click');
                        $(this.buttonOpener).trigger('newPicture', responseJSON);
                    })
                    .catch(() => {
                        alert('ERREUR');
                    })
                ;
            });
        });
    }


}

new PictureUploader();
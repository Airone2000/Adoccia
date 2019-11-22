import Croppr from 'croppr';
import('croppr/dist/croppr.css');
import uniqid from 'uniqid';

class AdvancedPicture
{
    constructor() {
        this._listenForFileInputChange();
    }

    _listenForFileInputChange() {
        $('input[type="file"].file').on('change', (e) => {
            this._displayPicture(e);
        });
    }

    _displayPicture(e) {
        let input = e.target;
        let $coordsInput = $(input).parents('.advanced-picture').find('input[type="hidden"].cropCoords');

        let oldCroppr = $coordsInput.data('croppr');
        if (oldCroppr) {
            oldCroppr.destroy();
            $coordsInput.data('croppr', null);
            $coordsInput.val('');
        }

        let $displayer = $(input).parents('.advanced-picture').find('.picture-preview > img');

        let id = uniqid();
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = (e) => {
                $displayer.attr('src', e.target.result);
                $displayer.attr('id', id);

                setTimeout( () => {
                    let croppr = new Croppr(`#${id}`, {
                        aspectRatio: 1,
                        startSize: [100, 100],
                        returnMode: 'real',
                        onCropMove: (data) => {
                            data = JSON.stringify(data);
                            $coordsInput.val(data);
                        },
                        onCropEnd: (data) => {
                            data = JSON.stringify(data);
                            $coordsInput.val(data);
                        }
                    });

                    $coordsInput.data('croppr', croppr);
                }, 50);


            }
            reader.readAsDataURL(input.files[0]);
        }
    }
}

new AdvancedPicture();
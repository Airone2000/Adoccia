class FicheForm
{
    constructor() {


        // When new picture is uploaded, display it and store its value id
        $('.openPictureUploader').on('newPicture', (e, {pictureId, pictureURL}) => {

            let uniqueId = $(e.target).data('unique-id');
            let {pictureIdInputSelector, picturePreviewSelector, btnDeleteSelector, pictureURLInputSelector} = this._getSelectorForId(uniqueId);

            $(pictureIdInputSelector).val(pictureId);
            $(picturePreviewSelector).attr('src', pictureURL);
            $(btnDeleteSelector).show();
            $(pictureURLInputSelector).val(pictureURL);
        });

        // Remove previously selected picture
        $('[data-unique-id].btn-delete').click((e) => {
            let uniqueId = $(e.target).data('unique-id');
            let {pictureIdInputSelector, picturePreviewSelector, btnDeleteSelector, pictureURLInputSelector} = this._getSelectorForId(uniqueId);
            $(pictureIdInputSelector).val('');
            $(picturePreviewSelector).attr('src', null);
            $(btnDeleteSelector).hide();
            $(pictureURLInputSelector).val('');
        });


    }

    _getSelectorForId(uniqueId) {
        let pictureIdInputSelector = `[data-unique-id="${uniqueId}"].input-id`;
        let picturePreviewSelector = `[data-unique-id="${uniqueId}"].img-preview`;
        let btnDeleteSelector = `[data-unique-id="${uniqueId}"].btn-delete`;
        let pictureURLInputSelector = `[data-unique-id="${uniqueId}"].picture-url`;
        return {pictureIdInputSelector, picturePreviewSelector, btnDeleteSelector, pictureURLInputSelector};
    }


}

new FicheForm();
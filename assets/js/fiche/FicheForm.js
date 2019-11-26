class CategoryForm
{
    constructor() {


        // When new picture is uploaded, display it and store its value id
        $('.openPictureUploader').on('newPicture', (e, {pictureId, pictureURL}) => {

            let uniqueId = $(e.target).data('unique-id');
            let {pictureIdInputSelector, picturePreviewSelector, btnDeleteSelector} = this._getSelectorForId(uniqueId);

            $(pictureIdInputSelector).val(pictureId);
            $(picturePreviewSelector).attr('src', pictureURL);
            $(btnDeleteSelector).show();
        });

        // Remove previously selected picture
        $('[data-unique-id].btn-delete').click((e) => {
            let uniqueId = $(e.target).data('unique-id');
            let {pictureIdInputSelector, picturePreviewSelector, btnDeleteSelector} = this._getSelectorForId(uniqueId);
            $(pictureIdInputSelector).val('');
            $(picturePreviewSelector).attr('src', null);
            $(btnDeleteSelector).hide();
        });


    }

    _getSelectorForId(uniqueId) {
        let pictureIdInputSelector = `[data-unique-id="${uniqueId}"].input-id`;
        let picturePreviewSelector = `[data-unique-id="${uniqueId}"].img-preview`;
        let btnDeleteSelector = `[data-unique-id="${uniqueId}"].btn-delete`;
        return {pictureIdInputSelector, picturePreviewSelector, btnDeleteSelector};
    }


}

new CategoryForm();
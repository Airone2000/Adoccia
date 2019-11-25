class CategoryForm
{
    constructor() {

        let $picturePreview = $('#fiche_picture_preview');
        let $pictureIdInput = $('#InnerFiche_RowsWrapper_picture_pictureId');
        let $btnRemovePicture = $('#fiche_picture_remove');

        // When new picture is uploaded, display it and store its value id
        $('.openPictureUploader').on('newPicture', (e, {pictureId, pictureURL}) => {
            $pictureIdInput.val(pictureId);
            $picturePreview.attr('src', pictureURL);
            $btnRemovePicture.show();
        });

        // Remove previously selected picture
        $btnRemovePicture.click((e) => {
            $picturePreview.attr('src', null);
            $pictureIdInput.val('');
            $btnRemovePicture.hide();
        });

    }

}

new CategoryForm();
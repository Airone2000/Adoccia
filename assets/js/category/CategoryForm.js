class CategoryForm
{
    constructor() {

        let $picturePreview = $('#category_picture_preview');
        let $pictureIdInput = $('#category_picture_pictureId');
        let $btnRemovePicture = $('#category_picture_remove');

        // When new picture is uploaded, display it and store its value id
        $('.openPictureUploader').on('newPicture', (e, {pictureId, pictureURL}) => {
            $pictureIdInput.val(pictureId);
            $picturePreview.attr('src', pictureURL);
            $btnRemovePicture.show();
        });

        // Remove previously selected picture
        $('#category_picture_remove').click((e) => {
            $picturePreview.attr('src', null);
            $pictureIdInput.val('');
            $btnRemovePicture.hide();
        });

    }

}

new CategoryForm();
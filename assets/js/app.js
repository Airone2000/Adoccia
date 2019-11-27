require('./../../node_modules/inputmask/dist/jquery.inputmask.bundle');

class App
{
    constructor()
    {
        this._defineInputMaskForAllProject();
        this._onLinkClicked();
    }

    _defineInputMaskForAllProject()
    {
        $('[data-masked]').inputmask();
    }

    _onLinkClicked() {
        $(document).on('click', '.link', (e) => {
            e.preventDefault();
            let $target = $(e.currentTarget);
            let url = $target.data('target') || null;
            if (url) {
                window.location.href = url;
            }
        });
    }
}

new App();
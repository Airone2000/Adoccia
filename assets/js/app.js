require('./../../node_modules/inputmask/dist/jquery.inputmask.bundle');


class App
{
    constructor()
    {
        this._defineInputMaskForAllProject();
    }

    _defineInputMaskForAllProject()
    {
        $('[data-masked]').inputmask();
    }
}

new App();
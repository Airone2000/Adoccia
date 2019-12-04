require('./../../node_modules/inputmask/dist/jquery.inputmask.bundle');

class App
{
    constructor()
    {
        this._defineInputMaskForAllProject();
        this._onLinkClicked();
        this._listenForOpeningModal();
        this._listenForClosingModal();
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

    _listenForOpeningModal()
    {
        $(document).on('click', '.openModal', (e) => {
            e.preventDefault();
            let target = e.currentTarget.getAttribute('data-target-selector') || null;
            if (target) {
                let element = document.querySelectorAll(target)[0] || null;
                if (element) {
                    document.querySelector('#Adoccia').classList.add('blur-10');
                    element.classList.remove('hidden');
                }
            }
        });
    }

    _listenForClosingModal()
    {
        $('.BtnCloseModal').click((e) => {
            document.querySelector('#Adoccia').classList.remove('blur-10');
            e.target.closest('.modal-wrapper').classList.add('hidden');
        });
    }
}

new App();
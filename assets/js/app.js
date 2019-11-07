require('./../../node_modules/inputmask/dist/jquery.inputmask.bundle');
require('leaflet');
import('leaflet/dist/leaflet.css');

class App
{
    constructor()
    {
        this._defineInputMaskForAllProject();

        let map = L.map('map').setView([51.505, -0.09], 13);
        L.tileLayer('').addTo(map);
    }

    _defineInputMaskForAllProject()
    {
        $('[data-masked]').inputmask();
    }
}

new App();
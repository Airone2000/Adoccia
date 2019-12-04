var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addStyleEntry('mainStyle','./assets/css/main.scss')
    .addStyleEntry('add_fiche', './assets/css/category/add_fiche.scss')
    .addStyleEntry('fiche', './assets/css/fiche/fiche.scss')
    .addEntry('form/edit', './assets/js/form/edit.js')
    .addEntry('category/search', './assets/js/category/search.js')
    .addEntry('app', './assets/js/app.js')
    .addEntry('mapsBuilder', './assets/vendor/leaflet/mapsBuilder')
    .addEntry('vanillaAutocomplete', './assets/vendor/vanilla-autocomplete/auto-complete')
    .addEntry('pictureUploader', './assets/js/PictureUploader.js')
    .addEntry('categoryForm', './assets/js/category/CategoryForm.js')
    .addEntry('ficheForm', './assets/js/fiche/FicheForm.js')
    .addStyleEntry('categoryIndex', './assets/layout/category/index/index.scss')
    .addStyleEntry('categoryShow', './assets/css/category/show.scss')
    .addStyleEntry('categoryFormBuilder', './assets/layout/category/form_builder.scss')

    // New layout
    .addStyleEntry('layout', './assets/layout/main.scss')

    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    .enableSassLoader()
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();

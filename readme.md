# Nouveau type de widget
1. Ajouter le nouveau type à `\App\Enum\WidgetTypeEnum`
2. Ajouter une nouvelle colonne sur l'entité `Value` : valueOfType[$type]

# Widget settings
1. Créer \_widget_[$type].html.twig
2. Créer \_settings_[$type].html.twig
3. Créer `\App\Form\WidgetSettingsType\[$type]WidgetSettingsType`
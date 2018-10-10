Change Log: `yii2-detail-view`
==============================

## Version 1.8.2

**Date:** 10-Oct-2018

- bug #167: Correct initBsVersion.

## Version 1.8.1

**Date:** 27-Sep-2018

- Enhancement for `BootstrapTrait::getCssClass`.

## Version 1.8.0

**Date:** 27-Sep-2018

- Bump up version.

## Version 1.7.9

**Date:** 26-Sep-2018

- Enhancements in parsing Bootstrap CSS classes (ref: kartik-v/yii2-krajee-base#100).

## Version 1.7.8

**Date:** 23-Sep-2018

- (enh #166): Refactor code to implement `kartik\base\BootstrapInterface`.

## Version 1.7.7

**Date:** 11-Sep-2018

- Move all source code to `src` directory.
- Updates to support Bootstrap v4.x.
- (kartik-v/yii2-krajee-base#94): Refactor code and consolidate / optimize properties within traits.
- (bug #160, #161): Correct hidden row style when both view and edit modes are hidden.
- (enh #158, #159): Correct multi select display.
- (enh #155): Update Italian translations.

## Version 1.7.6

**Date:** 04-May-2017

- Chronological ordering of issues for change log.
- Update copyright year to current.
- Add `hashVarLoadPosition` property.
- (enh #145): Add support for unicode characters in attribute names (sammousa).
- (enh #143): Fix missing implementation of checkbox and radio button groups.
- (enh #139): Add option to show `errorSummary` in error flash.
- (bug #138): Correct `INPUT_DROPDOWN_LIST` constant definition.
- Add github contribution and issue/PR logging templates.
- Update message config to include all default standard translation files.
- Enhance PHP Documentation for all classes and methods in the extension.
- (enh #134): Update Dutch translations.
- (enh #132, enh #135): Ensure correct parsing of `{buttons}` token in `mainTemplate`.
- (enh #131): Add Dutch translations.
- (enh #124): New properties `panelOptions` and `panelCssPrefix` to custom style the bootstrap panel.

## Version 1.7.5

**Date:** 22-Jun-2016

- (enh #121): Add Ukranian translations.
- (bug #120): Move form instantiation to init.
- (enh #118): Add Estonian translations.
- (enh #117): Change private properties to protected and better instantiation of formOptions`['fieldConfig']['template']`.
- (enh #116): Ability to use `form` instance and custom markup in edit mode.
- (enh #113): Add Thai translations.
- (enh #112): Adjust required version of yii2-helpers to 1.3.5.
- Add composer branch alias to allow getting latest `dev-master` updates.
- (enh #108): Update extension documentation for new properties.
- (enh #107): Remove redundant code.
- (enh #106): Enhance model `hasErrors` check.
- (enh #104): Use more correct `StarRating` repo.
- (enh #95): Enhance alert messaging session keys to be specific to each detail view.
- (enh #88): Implement Krajee Dialog for prettier and better alerts and confirmation dialog.

## Version 1.7.4

**Date:** 11-Jan-2016

- (enh #102): Enhancements for PJAX reinitialization. Complements enhancements in kartik-v/yii2-krajee-base#52 and kartik-v/yii2-krajee-base#53.
- (enh #101): Enhance plugin and widget with destroy method.
- (enh #98): CSS Styling enhancements for `table-condensed`.
- (enh #97): Enhance widget to parse visible attributes correctly.
- (enh #93): Add Polish translations.
- (enh #89): Fix documentation for type to correct constant.
- (enh #87): Add ability to show values as not set when empty.
- (bug #86): Fix Inflector class dependency.

## Version 1.7.3

**Date:** 13-Sep-2015

- (enh #84): Allow DetailView to be readonly without form for `enableEditMode = false`.
- (bug #82, #83): Enhance `rowOptions` and `hideIfEmpty`.
- (bug #81): Correct tooltip asset registration.
- (enh #80): Allow configuration of ActiveForm class.
- (enh #78): Fix missing asset dependencies for tooltips.
- (enh #77): Enhance default styling of toolbar buttons.
- (enh #76): Better parsing of xhr.responsetext.
- (bug #73): Parse `visible` attribute setting.

## Version 1.7.2

**Date:** 23-Aug-2015

- (enh #72): Enhancement to support children attributes and multi columnar layouts.
- (enh #70): Add `hideAlerts` property to control display of alerts.
- (enh #69): Allow DetailView to be configured for multiple models.
    - new `viewModel` and `editModel` properties at attributes level for each attribute
      which will override the `model` property at the widget level.
- (enh #67): Add Chinese translations.
- (enh #65): Add Indonesian translations.
- (enh #62): Add Spanish translations.
- (enh #60): Add Czech translations.

## Version 1.7.1

**Date:** 22-May-2015

- (bug #59): Fix parsing of panel `headingOptions` and `footerOptions`.
- (enh #58): Correct button styling on hover due to tooltips side effect.
- (enh #53): Added French Translations.
- (enh #52): Enhance form loading and record delete CSS progress states.
- (enh #51): Add `inputContainer` to control HTML options and ability to use bootstrap grid column classes.
- (enh #49): New loading indicator styling enhancements.
- (enh #48): New enhanced alert embedding functionality.
    - New alert container that will be automatically displayed in a `panel-body` above the DetailView.
    - One can use this to show alerts after update (via Yii session flashes) or after delete via ajax response.New properties:
        - `alertContainerOptions`: _array_, the HTML attributes for the alert block container which will display any alert messages received on update or delete of record.  This will not be displayed if there are no alert messages.
        - `alertWidgetOptions`: _array_, the widget settings for each bootstrap alert displayed in the alert container block. The CSS class in `options` within this will be auto derived and appended.
            - For `update` error messages will be displayed if you have set messages using Yii::$app->session->setFlash. The CSS class for the error block will be auto-derived based on flash message type using `alertMessageSettings`.
            - For `delete` this will be displayed based on the ajax response. The ajax response should be an object that contain the following:
              - success: _boolean_, whether the ajax delete is successful.
              - messages: _array_, the list of messages to display as key value pairs. The key must be one of the message keys in the `alertMessageSettings`, and the value must be the message content to be displayed.
        - `alertMessageSettings`: The session flash or alert message type and its corresponding CSS class. Defaults to:
```php
[
    'kv-detail-error' => 'alert alert-danger',
    'kv-detail-success' => 'alert alert-success',
    'kv-detail-info' => 'alert alert-info',
    'kv-detail-warning' => 'alert alert-warning'
]
```    
- (enh #47): Delete functionality enhancements.
    - Ability to trigger ajax based delete by default
    - The `deleteOptions` property takes in the following properties
        - `url`
        - `label`
        - `params`: the parameters to pass to ajax based response as key value pairs
        - `confirm`: confirmation alert message
        - `ajaxSettings`: the complete ajax configuration to override or append to if needed
- Use `\kartik\base\WidgetTrait` to initialize krajee plugin.
- (enh #43): Russian translations updated.

## Version 1.7.0

**Date:** 02-Mar-2015

- (enh #42): Improve validation to retrieve the right translation messages folder.
- (enh #41): Auto set to edit mode when model has validation errors.
- (enh #40): Panel heading and footer enhancements.
    - Allow `panel['heading']` to be set as string or a boolean `false` to disable it. This will display the panel title.
    - Add new property `panel['headingOptions']` which contains HTML attributes for panel heading title. Defaults to `['class'=>'panel-title']`. The following special options are recognized:
       - `tag`: defaults to `h3`
       - `template`: defaults to `{buttons}{title}` where `{title}` will be replaced with `panel['heading']` and `{buttons}` with the detail view action buttons
    - Allow `panel['footer']` to be set as string or a boolean `false` to disable it. This will display the panel title.
    - Add new property `panel['footerOptions']` which contains HTML attributes for panel footer title. Defaults to `['class'=>'panel-title']`. The following special options are recognized:
       - `tag`: defaults to `h3`
       - `template`: defaults to `{title}` where `{title}` will be replaced with `panel['footer']`
    - New property `{buttonContainer}` at widget level to set button toolbar options.

> NOTE: The extension includes a BC Breaking change with v1.7.0. With this release, the `template` property of the yii core DetailView is not anymore supported. One can use `rowOptions`, `labelColOptions`, `valueColOptions` at the widget level or widget `attributes` level to configure advanced layout functions.
- (enh #38): German translations updated.
- Set copyright year to current.
- (enh #37): Add bootstrap tooltips support for button titles.
- (enh #36): Ability to selectively hide rows in Edit mode or View mode.
- (enh #35): Add support for HIDDEN INPUT.
- (enh #34): Ability to configure rowOptions, labelColOptions, and valueColOptions at attribute level.
- (enh #33): Added ability to configure rowOptions.
- (enh #32): Added new reset button for use in edit mode.
- (enh #18): Ability to group attributes.
- (enh #17): Ability to hide rows with empty elements.

## Version 1.6.0

**Date:** 28-Jan-2015

- (enh #29): Russian translation added.
- (bug #28): Revert #20 Undo fix for switch inputs addressed now by plugin upgrade.
- (enh #27): Romanian translation added.

## Version 1.5.0

**Date:** 12-Jan-2015

- Revamp to use new Krajee base TranslationTrait.
- Code formatting updates as per Yii2 standards.
- (bug #25): Fix namespaces in use of Html and Config helpers.
- (bug #24): Fix undefined class constant 'self::INPUT_RADIO'.
- (bug #23): Fix HTML5 Input type initialization.

## Version 1.4.0

**Date:** 06-Dec-2014

- (bug #20): Reinitialize Switch Inputs in detail view edit mode.
- (bug #16): Correct method for validating input widget using `\kartik\base\Config`.

## Version 1.3.0

**Date:** 10-Nov-2014

- Delete button default option enhancements
- Set release to stable
- Better validation of Krajee input widgets 
- Set dependency on Krajee base components
- PSR4 alias change

## Version 1.2.0

**Date:** 19-Oct-2014

- (enh #15): Refactor and optimize client code
- (enh #14): Add various container properties to configure HTML options
- (enh #13): Improve hide of elements and remove fade delay at initialization

## Version 1.1.0

**Date:** 15-Jul-2014

- PSR4 alias change
- (enh #10): Added animation to fade out between view and edit modes

## Version 1.0.0

**Date:** 15-May-2014

- Added support for more inputs
  - `DetailView::INPUT_DATE_RANGE` or `\kartik\widgets\DateRangePicker`
  - `DetailView::INPUT_SORTABLE` or `\kartik\sortinput\SortableInput`
- (enh #8): Added Hungarian language translations (monghuz)
- (enh #4): Added confirmation message management (lestat1968)
- (enh #4): Added Italian language translations (lestat1968)
- (enh #1): Changed `static` variable references to `self` (kartik-v)
- Initial release

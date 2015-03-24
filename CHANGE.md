version 1.7.1
=============
**Date:** 20-Mar-2015

- Use `\kartik\base\WidgetTrait` to initialize krajee plugin.
- (enh #43): Russian translations updated.
- (enh #47): Delete functionality enhancements.
    - Ability to trigger ajax based delete by default
    - The `deleteOptions` property takes in the following properties
        - `url`
        - `label`
        - `params`: the parameters to pass to ajax based response as key value pairs
        - `confirm`: confirmation alert message
        - `ajaxSettings`: the complete ajax configuration to override or append to if needed
- (enh #48): New enhanced alert embedding functionality.
    - New alert container that will be automatically displayed in a `panel-body` above the DetailView.
    - One can use this to show alerts after update (via Yii session flashes) or after delete via ajax response.New properties:
        - `alertContainerOptions`: _array_, the HTML attributes for the alert block container which will display any alert messages received on update or delete of record.  This will not be displayed if there are no alert messages.
        - `alertWidgetOptions`: _array_, the widget settings for each bootstrap alert displayed in the alert container block. The CSS class in `options` within this will be auto derived and appended.
            - For `update` error messages will be displayed if you have set messages using Yii::$app->session->setFlash. The CSS class for the error block will be auto-derived based on flash message type using `alertMessageSettings`.
            - For `delete` this will be displayed based on the ajax response. The ajax response
          should be an object that contain the following:
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
- (enh #49): New loading indicator styling enhancements.
- (enh #51): Add `inputContainer` to control HTML options and ability to use bootstrap grid column classes.
- (enh #52): Enhance form loading and record delete CSS progress states.

version 1.7.0
=============
**Date:** 02-Mar-2015

- (enh #17): Ability to hide rows with empty elements.
- (enh #18): Ability to group attributes.
- (enh #32): Added new reset button for use in edit mode.
- (enh #33): Added ability to configure rowOptions.
- (enh #34): Ability to configure rowOptions, labelColOptions, and valueColOptions at attribute level.
- (enh #35): Add support for HIDDEN INPUT.
- (enh #36): Ability to selectively hide rows in Edit mode or View mode.
- (enh #37): Add bootstrap tooltips support for button titles.
- Set copyright year to current.
- (enh #38): German translations updated.
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

- (enh #41): Auto set to edit mode when model has validation errors.
- (enh #42): Improve validation to retrieve the right translation messages folder.

version 1.6.0
=============
**Date:** 28-Jan-2015

- (enh #27): Romanian translation added.
- (bug #28): Revert #20 Undo fix for switch inputs addressed now by plugin upgrade.
- (enh #29): Russian translation added.

version 1.5.0
=============
**Date:** 12-Jan-2015

- (bug #23): Fix HTML5 Input type initialization.
- (bug #24): Fix undefined class constant 'self::INPUT_RADIO'.
- (bug #25): Fix namespaces in use of Html and Config helpers.
- Code formatting updates as per Yii2 standards.
- Revamp to use new Krajee base TranslationTrait.

version 1.4.0
=============
**Date:** 06-Dec-2014

- bug #16: Correct method for validating input widget using `\kartik\base\Config`.
- bug #20: Reinitialize Switch Inputs in detail view edit mode.

version 1.3.0
=============
**Date:** 10-Nov-2014

- PSR4 alias change
- Set dependency on Krajee base components
- Better validation of Krajee input widgets 
- Set release to stable
- Delete button default option enhancements

version 1.2.0
=============
**Date:** 19-Oct-2014

- enh #13: Improve hide of elements and remove fade delay at initialization
- enh #14: Add various container properties to configure HTML options
- enh #15: Refactor and optimize client code

version 1.1.0
=============
**Date:** 15-Jul-2014

- enh #10: Added animation to fade out between view and edit modes
- PSR4 alias change

version 1.0.0
=============
**Date:** 15-May-2014

- Initial release
- enh #1: Changed `static` variable references to `self` (kartik-v)
- enh #4: Added confirmation message management (lestat1968)
- enh #4: Added Italian language translations (lestat1968)
- enh #8: Added Hungarian language translations (monghuz)
- Added support for more inputs
  - `DetailView::INPUT_DATE_RANGE` or `\kartik\widgets\DateRangePicker`
  - `DetailView::INPUT_SORTABLE` or `\kartik\sortinput\SortableInput`
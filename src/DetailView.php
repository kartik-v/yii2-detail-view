<?php

/**
 * @package   yii2-detail-view
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2022
 * @version   1.8.7
 */

namespace kartik\detail;

use Closure;
use Exception;
use kartik\base\BootstrapInterface;
use kartik\base\BootstrapTrait;
use kartik\base\Config;
use kartik\base\Lib;
use kartik\base\TranslationTrait;
use kartik\base\WidgetTrait;
use kartik\base\PluginAssetBundle;
use kartik\dialog\Dialog;
use ReflectionException;
use Yii;
use yii\base\Arrayable;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView as YiiDetailView;

/**
 * DetailView displays the detail of a single data [[model]]. This widget enhances the [[YiiDetailView]] widget with
 * ability to edit detail view data, configure multi columnar layouts, merged section headers, and various other
 * bootstrap styling enhancements.
 *
 * DetailView is best used for displaying a model in a regular format (e.g. each model attribute is displayed as a
 * row in a table or one can define multiple columns by defining child attributes in each attribute configuration.)
 * The model can be either an instance of [[Model]] or an associative array.
 *
 * DetailView uses the [[attributes]] property to determines which model attributes should be displayed and how they
 * should be formatted.
 *
 * A typical usage of DetailView is as follows:
 *
 * ```php
 * echo DetailView::widget([
 *     'model' => $model,
 *     'attributes' => [
 *         'title',               // title attribute (in plain text)
 *         'description:html',    // description attribute in HTML
 *         [                      // the owner name of the model
 *             'label' => 'Owner',
 *             'value' => $model->owner->name,
 *         ],
 *         'created_at:datetime', // creation date formatted as datetime
 *     ],
 * ]);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class DetailView extends YiiDetailView implements BootstrapInterface
{
    use WidgetTrait;
    use BootstrapTrait;
    use TranslationTrait;

    /**
     * view mode for the detail view
     */
    const MODE_VIEW = 'view';

    /**
     * edit mode for the detail view
     */
    const MODE_EDIT = 'edit';

    /**
     * the **default** bootstrap contextual color type (applicable only for panel contextual style)
     */
    const TYPE_DEFAULT = 'default';

    /**
     * the **light** bootstrap contextual color type (applicable only for panel contextual style)
     */
    const TYPE_LIGHT = 'light';

    /**
     * the **dark** bootstrap contextual color type (applicable only for panel contextual style)
     */
    const TYPE_DARK = 'dark';

    /**
     * the **primary** bootstrap contextual color type
     */
    const TYPE_PRIMARY = 'primary';

    /**
     * the **secondary** bootstrap contextual color type
     */
    const TYPE_SECONDARY = 'secondary';

    /**
     * the **information** bootstrap contextual color type
     */
    const TYPE_INFO = 'info';

    /**
     * the **danger** bootstrap contextual color type
     */
    const TYPE_DANGER = 'danger';

    /**
     * the **warning** bootstrap contextual color type
     */
    const TYPE_WARNING = 'warning';

    /**
     * the **success** bootstrap contextual color type
     */
    const TYPE_SUCCESS = 'success';

    /**
     * the **active** bootstrap contextual color type (applicable only for table row contextual style)
     */
    const TYPE_ACTIVE = 'active';

    /**
     * horizontal **right** alignment for grid cells
     */
    const ALIGN_RIGHT = 'right';

    /**
     * horizontal **center** alignment for grid cells
     */
    const ALIGN_CENTER = 'center';

    /**
     * horizontal **left** alignment for grid cells
     */
    const ALIGN_LEFT = 'left';

    /**
     * vertical **top** alignment for grid cells
     */
    const ALIGN_TOP = 'top';

    /**
     * vertical **middle** alignment for grid cells
     */
    const ALIGN_MIDDLE = 'middle';

    /**
     * vertical **bottom** alignment for grid cells
     */
    const ALIGN_BOTTOM = 'bottom';

    /**
     * static input (styled using bootstrap style)
     */
    const INPUT_STATIC = 'staticInput';

    /**
     * hidden input.
     */
    const INPUT_HIDDEN = 'hiddenInput';

    /**
     * hidden static input
     */
    const INPUT_HIDDEN_STATIC = 'hiddenStaticInput';

    /**
     * text input
     */
    const INPUT_TEXT = 'textInput';

    /**
     * text area
     */
    const INPUT_TEXTAREA = 'textarea';

    /**
     * password input
     */
    const INPUT_PASSWORD = 'passwordInput';

    /**
     * dropdown list allowing single select
     */
    const INPUT_DROPDOWN_LIST = 'dropDownList';

    /**
     * list box allowing multiple select
     */
    const INPUT_LIST_BOX = 'listBox';

    /**
     * checkbox input
     */
    const INPUT_CHECKBOX = 'checkbox';

    /**
     * radio input
     */
    const INPUT_RADIO = 'radio';

    /**
     * checkbox inputs as a list allowing multiple selection
     */
    const INPUT_CHECKBOX_LIST = 'checkboxList';

    /**
     * radio inputs as a list
     */
    const INPUT_RADIO_LIST = 'radioList';

    /**
     * bootstrap styled checkbox button group
     */
    const INPUT_CHECKBOX_BUTTON_GROUP = 'checkboxButtonGroup';

    /**
     * bootstrap styled radio button group
     */
    const INPUT_RADIO_BUTTON_GROUP = 'radioButtonGroup';

    /**
     * Krajee styled multiselect input that allows formatted checkbox list and radio list
     */
    const INPUT_MULTISELECT = 'multiselect';

    /**
     * file input
     */
    const INPUT_FILE = 'fileInput';

    /**
     * other HTML5 input (e.g. color, range, email etc.)
     */
    const INPUT_HTML5 = 'input';

    /**
     * input widget
     */
    const INPUT_WIDGET = 'widget';

    /**
     * Krajee dependent dropdown input widget [[\kartik\depdrop\DepDrop]]
     */
    const INPUT_DEPDROP = '\kartik\depdrop\DepDrop';

    /**
     * Krajee select2 input widget [[\kartik\select2\Select2]]
     */
    const INPUT_SELECT2 = '\kartik\select2\Select2';

    /**
     * Krajee typeahead input widget [[\kartik\typeahead\Typeahead]]
     */
    const INPUT_TYPEAHEAD = '\kartik\typeahead\Typeahead';

    /**
     * Krajee switch input widget [[\kartik\switchinput\SwitchInput]]
     */
    const INPUT_SWITCH = '\kartik\switchinput\SwitchInput';

    /**
     * Krajee touch spin input widget [[\kartik\touchspin\TouchSpin]]
     */
    const INPUT_SPIN = '\kartik\touchspin\TouchSpin';

    /**
     * Krajee star rating input widget [[\kartik\rating\StarRating]]
     */
    const INPUT_RATING = '\kartik\rating\StarRating';

    /**
     * Krajee range input widget [[\kartik\range\RangeInput]]
     */
    const INPUT_RANGE = '\kartik\range\RangeInput';

    /**
     * Krajee color input widget [[\kartik\color\ColorInput]]
     */
    const INPUT_COLOR = '\kartik\color\ColorInput';

    /**
     * Krajee file input widget [[\kartik\file\FileInput]]
     */
    const INPUT_FILEINPUT = '\kartik\file\FileInput';

    /**
     * Krajee date picker input widget [[\kartik\date\DatePicker]]
     */
    const INPUT_DATE = '\kartik\date\DatePicker';

    /**
     * Krajee Time picker input widget [[\kartik\time\TimePicker]]
     */
    const INPUT_TIME = '\kartik\time\TimePicker';

    /**
     * Krajee date time Picker input widget [[\kartik\datetime\DateTimePicker]]
     */
    const INPUT_DATETIME = '\kartik\datetime\DateTimePicker';

    /**
     * Krajee date range picker input widget [[\kartik\daterange\DateRangePicker]]
     */
    const INPUT_DATE_RANGE = '\kartik\daterange\DateRangePicker';

    /**
     * Krajee sortable input widget [[\kartik\sortinput\SortableInput]]
     */
    const INPUT_SORTABLE = '\kartik\sortinput\SortableInput';

    /**
     * Krajee slider input widget [[\kartik\slider\Slider]]
     */
    const INPUT_SLIDER = '\kartik\slider\Slider';

    /**
     * Krajee mask money input widget [[\kartik\money\MaskMoney]]
     */
    const INPUT_MONEY = '\kartik\money\MaskMoney';

    /**
     * Krajee checkbox extended input widget [[\kartik\checkbox\CheckboxX]]
     */
    const INPUT_CHECKBOX_X = '\kartik\checkbox\CheckboxX';

    /**
     * @var string[] list of form inputs (non dropdown based)
     */
    protected static $_inputsList = [
        self::INPUT_HIDDEN => 'hiddenInput',
        self::INPUT_TEXT => 'textInput',
        self::INPUT_PASSWORD => 'passwordInput',
        self::INPUT_TEXTAREA => 'textarea',
        self::INPUT_CHECKBOX => 'checkbox',
        self::INPUT_RADIO => 'radio',
        self::INPUT_LIST_BOX => 'listBox',
        self::INPUT_DROPDOWN_LIST => 'dropDownList',
        self::INPUT_CHECKBOX_LIST => 'checkboxList',
        self::INPUT_RADIO_LIST => 'radioList',
        self::INPUT_HTML5 => 'input',
        self::INPUT_FILE => 'fileInput',
        self::INPUT_WIDGET => 'widget',
        self::INPUT_CHECKBOX_BUTTON_GROUP => 'checkboxButtonGroup',
        self::INPUT_RADIO_BUTTON_GROUP => 'radioButtonGroup',
    ];

    /**
     * @var string[] list of form inputs (dropdown based)
     */
    protected static $_dropDownInputs = [
        self::INPUT_LIST_BOX => 'listBox',
        self::INPUT_DROPDOWN_LIST => 'dropDownList',
        self::INPUT_CHECKBOX_LIST => 'checkboxList',
        self::INPUT_RADIO_LIST => 'radioList',
        self::INPUT_CHECKBOX_BUTTON_GROUP => 'checkboxButtonGroup',
        self::INPUT_RADIO_BUTTON_GROUP => 'radioButtonGroup',
    ];

    /**
     * @var string the mode for the Detail View when its initialized
     */
    public $mode = self::MODE_VIEW;

    /**
     * @var integer the fade animation delay in microseconds when toggling between the view and edit modes.
     */
    public $fadeDelay = 800;

    /**
     * @var string the horizontal alignment for the label column
     */
    public $hAlign = self::ALIGN_RIGHT;

    /**
     * @var string the vertical alignment for the label column
     */
    public $vAlign = self::ALIGN_MIDDLE;

    /**
     * @var array the HTML attributes for each attribute row
     */
    public $rowOptions = [];

    /**
     * @var array the HTML attributes for the label column
     */
    public $labelColOptions = ['style' => 'width: 20%'];

    /**
     * @var array the HTML attributes for the value column
     */
    public $valueColOptions = [];

    /**
     * @var boolean whether to hide all alerts. Defaults to `false`.
     */
    public $hideAlerts = false;

    /**
     * @var bool whether to show form error summary as part of the error alert. Defaults to `false`.
     */
    public $showErrorSummary = false;

    /**
     * @var boolean whether to show values as not set if empty string
     */
    public $notSetIfEmpty = false;

    /**
     * @var array the HTML attributes for the alert block container which will display any alert messages received on
     * update or delete of record. This will not be displayed if there are no alert messages.
     */
    public $alertContainerOptions = [];

    /**
     * @var array the widget settings for each bootstrap alert displayed in the alert container block.
     *
     * The CSS class in `options` within this property will be auto derived and appended.
     *
     * - For `update`, the error messages will be displayed, only if you have set messages using `Yii::$app->session->setFlash`.
     *   The CSS class for the error block will be auto-derived based on flash message type using `alertMessageSettings`.
     * - For `delete`, the error messages will be displayed based on the ajax response. The ajax response should be an object that
     *   contain the following settings:
     *   - `success`: _boolean_, whether the ajax delete is successful.
     *   - `messages`: _array_|_object_,the list of messages to display as key value pairs. The key must be one of the
     *     message keys in the `alertMessageSettings`, and the value must be the message content to be displayed.
     */
    public $alertWidgetOptions = [];

    /**
     * @var array the alert flash message settings.
     *
     * This will be set as `$key => $value`, pairs where:
     *
     * - `$key`: _string_, flash message key e.g. `error`, `success`.
     * - `$value`: _string_|_array_, of CSS classes for the flash message e.g. `['alert', 'alert-danger']`. This defaults to
     *     the following setting:
     *
     * ```php
     * [
     *     'kv-detail-error' => ['alert', 'alert-danger'],
     *     'kv-detail-success' => ['alert', 'alert-success'],
     *     'kv-detail-info' =>  ['alert', 'alert-info'],
     *     'kv-detail-warning' =>  ['alert', 'alert-warning'],
     * ]
     * ```
     */
    public $alertMessageSettings = [];

    /**
     * @var array the HTML attributes for the detail view table
     */
    public $options = [];

    /**
     * @var boolean whether the grid view will have Bootstrap table styling.
     */
    public $bootstrap = true;

    /**
     * @var boolean whether the grid table will have a `bordered` style. Applicable only if [[bootstrap]] is `true`.
     */
    public $bordered = true;

    /**
     * @var boolean whether the grid table will have a `striped` style. Applicable only if [[bootstrap]] is `true`.
     */
    public $striped = true;

    /**
     * @var boolean whether the grid table will have a `condensed` style. Applicable only if [[bootstrap]] is `true`.
     */
    public $condensed = false;

    /**
     * @var boolean whether the grid table will have a `responsive` style. Applicable only if [[bootstrap]] is `true`.
     */
    public $responsive = true;

    /**
     * @var boolean whether the grid table will highlight row on `hover`. Applicable only if [[bootstrap]] is `true`.
     */
    public $hover = false;

    /**
     * @var boolean whether to enable edit mode for the detail view.
     */
    public $enableEditMode = true;

    /**
     * @var boolean whether to hide rows in view mode if value is null or empty.
     */
    public $hideIfEmpty = false;

    /**
     * @var boolean whether to display bootstrap style tooltips for titles on hover of buttons.
     */
    public $tooltips = true;

    /**
     * @var boolean whether to convert array values to string using `print_r`.
     */
    public $arrayValueToString = false;

    /**
     * @var array configuration settings for the [[Dialog]] widget that will be used to render alerts and
     * confirmation dialog prompts.
     *
     * @see http://demos.krajee.com/dialog
     */
    public $krajeeDialogSettings = [];

    /**
     * @var array a list of attributes to be displayed in the detail view. Each array element represents the
     * specification for displaying one particular attribute.
     *
     * An attribute can be specified as a string in the format of "attribute", "attribute:format" or
     * "attribute:format:label", where "attribute" refers to the attribute name, and "format" represents the format
     * of the attribute. The "format" is passed to the [[Formatter::format()]] method to format an attribute value
     * into a displayable text. Please refer to [[Formatter]] for the supported types. Both "format" and "label"
     * are optional. They will take default values if absent.
     *
     * An attribute can also be specified in terms of an array with the following elements.
     *
     * - `attribute`: _string|Closure_, the attribute name. This is required if either "label" or "value" is not specified.
     * - `label`: _string|Closure_, the label associated with the attribute. If this is not specified, it will be generated
     *   from the attribute name.
     * - `value`: _mixed|Closure_, the value to be displayed. If this is not specified, it will be retrieved from [[model]]
     *   using the attribute name by calling [[ArrayHelper::getValue()]]. Note that this value will be formatted into
     *   a displayable text according to the "format" option.
     * - `format`: _mixed|Closure_, the type of the value that determines how the value would be formatted into a
     *   displayable text. Please refer to [[Formatter]] for supported types.
     * - `visible`: _boolean|Closure_, whether the attribute is visible. If set to `false`, the attribute will NOT be
     *   displayed.
     *
     * Additional special settings are:
     * - `viewModel`: _Model|Closure_, the model to be used for this attribute in VIEW mode. This will override the `model`
     *   setting at the widget level. If not set, the widget `model` setting will be used.
     * - `editModel`: _Model|Closure_, the model to be used for this attribute in EDIT mode. This will override the `model`
     *   setting at the widget level. If not set, the widget `model` setting will be used.
     * - `rowOptions`: _array|Closure_, HTML attributes for the row (if not set, this will be defaulted to the `rowOptions`
     *   set at the widget level)
     * - `labelColOptions`: _array|Closure_, HTML attributes for the label column (if not set, this will be defaulted to
     *   the `labelColOptions` set at the widget level)
     * - `valueColOptions`: _array|Closure_, HTML attributes for the value column (if not set, this will be defaulted to
     *   `valueColOptions` set at the widget level)
     * - `group`: _boolean|Closure_, whether to group the selection by merging the label and value into a single column.
     * - `groupOptions`: _array|Closure_, HTML attributes for the grouped/merged column when `group` is set to `true`.
     * - `type`: _string|Closure_, the input type for rendering the attribute in edit mode. Must be one of the
     *   `INPUT_` constants.
     * - `displayOnly`: _boolean|Closure_, if the input is to be set to as `display only` in edit mode.
     * - widgetOptions: array|Closure_, the widget options if you set `type` to [[INPUT_WIDGET]]. The
     *   following special options are recognized:
     *   - `class`: _string the fully namespaced widget class.
     * - `items`: _array|Closure_, the list of data items  for dropDownList, listBox, checkboxList & radioList
     * - `inputType`: _string|Closure_, the HTML 5 input type if `type` is set to [[INPUT_HTML5]].
     * - `inputContainer`: _array|Closure_, HTML attributes for the input container
     * - `inputWidth`: _string|Closure_, the width of the container holding the input, should be appended along with the
     *   width unit (`px` or `%`) - this property is deprecated since v1.7.7
     * - `fieldConfig`: _array|Closure_, optional, the Active field configuration.
     * - `options`: _array|Closure_, optional, the HTML attributes for the input.
     * - `updateAttr`: _string|Closure_, optional, the name of the attribute to be updated, when in edit mode. This will
     *   default to the `attribute` setting.
     * - `updateMarkup`: _string|Closure_, the raw markup to render in edit mode. If not set, this normally will be
     *   automatically generated based on `attribute` or `updateAttr` setting. If this is set it will override the
     *   default markup.
     *
     * Note that all of the attribute properties above can also be setup as a Closure callback with the signature
     *    `function($form, $widget)`, where:
     * - `$form`: _ActiveForm_, is the current active form object in the detail view.
     * - `$widget`: _DetailView_, is the current detail view widget instance.
     */
    public $attributes;

    /**
     * @var string the form widget class name (extending from [[ActiveForm]]) to be used for editing
     */
    public $formClass = 'kartik\form\ActiveForm';

    /**
     * @var array the options for the [[ActiveForm]] instance that will be generated in edit mode based on [[formClass]].
     */
    public $formOptions = [];

    /**
     * @var string the template for rendering the grid within a bootstrap styled panel / card layout.
     * The following special tokens are recognized and will be replaced:
     * - `{prefix}`: the CSS prefix name as set in [[panelCssPrefix]].
     * - `{type}`: the panel type that will append the bootstrap contextual CSS.
     * - `{panelHeading}`: the panel heading content.
     * - `{panelBefore}`: the content before panel.
     * - `{panelAfter}`: the content after panel.
     * - `{panelFooter}`: the panel footer content.
     * - `{items}`: the detail view items.
     * - `{buttons}`: buttons to be displayed as set in [[buttons1]] or [[buttons2]].
     * - `{title}`: the panel heading title content.
     */
    public $panelTemplate = <<< HTML
{panelHeading}
{panelBefore}
{items}
{panelAfter}
{panelFooter}
HTML;

    /**
     * @var string the template for rendering the panel heading. The following special tokens are
     * recognized and will be replaced:
     * - `{title}`: _string_, which will render the panel heading title content.
     * - `{buttons}`: _string_, which will render the buttons in the panel heading toolbar based on the [[buttons1]] or
     *   [[buttons2]] property.].
     */
    public $panelHeadingTemplate = <<< HTML
    {buttons}
    {title}
    <div class="clearfix"></div>
HTML;

    /**
     * @var array the panel settings for displaying the grid view within a bootstrap styled panel. This property is
     * therefore applicable only if [[bootstrap]] property is `true`. The following array keys can be configured:
     * - `type`: _string_, the panel contextual type. Set it to one of the TYPE constants. If not set, will default to
     *   [[TYPE_DEFAULT]].
     * - `options`: _array_, the HTML attributes for the panel container. If the `class` is not set, it will be auto
     *   derived using the panel `type` and [[panelCssPrefix]] specific to the configured bootstrap version.
     * - `heading`: _string_|_boolean_, the panel heading. If set to `false`, will not be displayed.
     * - `headingOptions`: _array_, HTML attributes for the panel heading container. Defaults to:
     *    - `['class'=>'card-heading {COLOR}']` for Bootstrap 4.x & Bootstrap 5.x. The {COLOR} token will be auto
     *     calculated and replaced based on the `type` setting.
     *    - `['class'=>'panel-heading']` for Bootstrap 3.x.
     * - `titleOptions`: _array_, HTML attributes for the panel title container. The following tags are specially
     *   parsed:
     *   - `tag`: _string_, the HTML tag to render the title. Defaults to:
     *      - `span` for Bootstrap 4.x & Bootstrap 5.x.
     *      - `h3` for Bootstrap 3.x.
     *
     *   The `titleOptions` defaults to:
     *   - `[]` for Bootstrap 4.x & Bootstrap 5.x.
     *   - `['class'=>'panel-title']` for Bootstrap 3.x.
     * - `footer`: _string_|_boolean_, the panel footer. If set to `false` will not be displayed.
     * - `footerOptions`: _array_, HTML attributes for the panel footer container. Defaults to:
     *   - `['class'=>'card-footer']` for Bootstrap 4.x & Bootstrap 5.x.
     *   - `['class'=>'panel-footer']` for Bootstrap 3.x.
     * - `before`: _string_|_boolean_, content to be placed before/above the grid (after the header). To not display
     *   this section, set this to `false`.
     * - `beforeOptions`: _array_, HTML attributes for the `before` text. If the `class` is not set, it will default to
     *   `kv-panel-before`.
     * - `after`: _string_|_boolean_, any content to be placed after/below the grid (before the footer). To not
     *   display this section, set this to `false`.
     * - `afterOptions`: _array_, HTML attributes for the `after` text. If the `class` is not set, it will default to
     *   `kv-panel-after`.
     */
    public $panel = [];

    /**
     * @var array bootstrap panel options.
     *
     * @deprecated since v1.8.6. Use `DetailView::panel['options']` instead.
     */
    public $panelOptions = [];

    /**
     * @var string the CSS class prefix to apply to the bootstrap panel container (applicable when [[panel]] is
     * configured).
     */
    public $panelCssPrefix;

    /**
     * @var string the main template to render the detail view. The following tokens will be parsed and replaced:
     * - `{detail}`: will be replaced by the rendered detail view
     * - `{buttons}`: the buttons to be displayed as set in [[buttons1]] or [[buttons2]].
     */
    public $mainTemplate = "{detail}";

    /**
     * @var array the options for the button toolbar container
     */
    public $buttonContainer = ['class' => 'float-right float-end pull-right'];

    /**
     * @var string the buttons to show when in view mode. The following tokens will be parsed and replaced:
     * - `{view}`: the view button
     * - `{update}`: the update button
     * - `{delete}`: the delete button
     * - `{save}`: the save button
     */
    public $buttons1 = '{update} {delete}';

    /**
     * @var string the buttons template to show when in edit mode. The following tokens will be parsed and replaced:
     * - `{view}`: the view button
     * - `{update}`: the update button
     * - `{reset}`: the reset button
     * - `{delete}`: the delete button
     * - `{save}`: the save button
     */
    public $buttons2 = '{view} {reset} {save}';

    /**
     * @var array the HTML attributes for the container displaying the VIEW mode attributes.
     */
    public $viewAttributeContainer = [];

    /**
     * @var array the HTML attributes for the container displaying the EDIT mode attributes.
     */
    public $editAttributeContainer = [];

    /**
     * @var array the HTML attributes for the container displaying the VIEW mode buttons.
     */
    public $viewButtonsContainer = [];

    /**
     * @var array the HTML attributes for the container displaying the EDIT mode buttons.
     */
    public $editButtonsContainer = [];

    /**
     * @var array the HTML attributes for the view button. This will toggle the view from edit mode to view mode. The
     * following special options are recognized:
     * - `label`: the save button label. This will not be HTML encoded.
     *    - Defaults to `<i class="fas fa-eye"></i>` for Bootstrap 4.x & Bootstrap 5.x.
     *    - Defaults to `<i class="glyphicon glyphicon-eye-open"></i>` for Bootstrap 3.x.
     */
    public $viewOptions = [];

    /**
     * @var array the HTML attributes for the update button. This button will toggle the edit mode on. The following
     * special options are recognized:
     * - `label`: the update button label. This will not be HTML encoded.
     *    - Defaults to `<i class="fas fa-pencil-alt"></i>` for Bootstrap 4.x & Bootstrap 5.x.
     *    - Defaults to `<i class="glyphicon glyphicon-pencil"></i>` for Bootstrap 3.x.
     */
    public $updateOptions = [];

    /**
     * @var array the HTML attributes for the reset button. This button will reset the form in edit mode. The following
     * special options are recognized:
     * - `label`: _string_, the reset button label. This will not be HTML encoded.
     *    - Defaults to `<i class="fas fa-ban"></i>` for Bootstrap 4.x & Bootstrap 5.x.
     *    - Defaults to `<i class="glyphicon glyphicon-ban-circle"></i>` for Bootstrap 3.x.
     */
    public $resetOptions = [];

    /**
     * @var array the HTML attributes for the edit button. The following special options are recognized:
     * - `label`: _string_, the delete button label. This will not be HTML encoded.
     *    - Defaults to `<i class="fas fa-trash-alt"></i>` for Bootstrap 4.x & Bootstrap 5.x.
     *    - Defaults to `<i class="glyphicon glyphicon-trash"></i>` for Bootstrap 3.x.
     * - `url`: _string_, the delete button url. Defaults to `#`.
     * - `params`: _array_, the parameters to the delete action and their values to be passed via ajax which you must
     *   set as key value pairs. This will be automatically json string encoded, so to escape javascript variables &
     *   methods you can use [[yii\web\JsExpression]]
     * - `ajaxSettings`: _array_, the [ajax settings](https://api.jquery.com/jquery.ajax/) if you choose to override the delete ajax settings.
     * - `confirm`: _string_, the confirmation message before triggering delete.
     *   Defaults to `Are you sure you want to delete this item?` (will be translated for your locale language set).
     * - `showErrorStack`: _boolean_, whether to show the complete error stack. Defaults to `false`.
     */
    public $deleteOptions = [];

    /**
     * @var array the HTML attributes for the save button. This will default to a form submit button. The following
     * special options are recognized:
     * - `label`: _string_, the save button label. This will not be HTML encoded. Defaults to:
     *    - `<i class="fas fa-save"></i>` for Bootstrap 4.x & Bootstrap 5.x.
     *    - `<i class="glyphicon glyphicon-floppy-disk"></i>` for Bootstrap 3.x.
     */
    public $saveOptions = [];

    /**
     * @var array the HTML attributes for the widget container
     */
    public $container = [];

    /**
     * @var array the HTML attributes for the table container
     */
    public $tableContainer = [];

    /**
     * @var array HTML attributes for the child table (applicable when using with multiple child columns).
     */
    public $childTableOptions = [];

    /**
     * @var ActiveForm the active form instance
     */
    protected $_form;

    /**
     * @var array internally processed HTML attributes for each table row. Defaults settings from [[rowOptions]].
     */
    protected $_rowOptions = [];

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->initWidget();
        parent:: init();
    }

    /**
     * @inheritdoc
     * @throws ReflectionException
     */
    public function run()
    {
        $this->runWidget();
    }

    /**
     * Initializes the detail view widget
     *
     * @throws InvalidConfigException
     */
    protected function initWidget()
    {
        $this->_msgCat = 'kvdetail';
        $this->pluginName = 'kvDetailView';
        $this->initBsVersion();
        $notBs3 = !$this->isBs(3);
        Html::addCssClass($this->container, 'kv-container-bs' . ($notBs3 ? 4 : 3));
        if ($this->enableEditMode) {
            /**
             * @var string|ActiveForm $formClass
             */
            $formClass = $this->formClass;
            $activeForm = ActiveForm::class;
            if (!is_subclass_of($formClass, $activeForm) && $formClass != $activeForm) {
                throw new InvalidConfigException("Form class '{$formClass}' must exist and extend from '{$activeForm}'.");
            }
            $this->validateDisplay();
            if (!isset($this->formOptions['fieldConfig']['template'])) {
                $this->formOptions['fieldConfig']['template'] = "{input}\n{hint}\n{error}";
            }
            $this->_form = $formClass::begin($this->formOptions);
        }
        if ($this->bootstrap) {
            Html::addCssClass($this->options, 'table');
            if ($this->condensed) {
                $this->addCssClass($this->options, self::BS_TABLE_CONDENSED);
            }
            if ($notBs3) {
                $this->childTableOptions = $this->options;
                unset($this->childTableOptions['id']);
            }
            if ($this->hover) {
                Html::addCssClass($this->options, 'table-hover');
            }
            if ($this->bordered) {
                Html::addCssClass($this->options, 'table-bordered');
            }
            if ($this->striped) {
                Html::addCssClass($this->options, 'table-striped');
            }
        }
        Html::addCssClass($this->options, 'detail-view');
        Html::addCssClass($this->childTableOptions, 'kv-child-table');
        Html::addCssStyle($this->labelColOptions, "text-align:{$this->hAlign};vertical-align:{$this->vAlign};");
    }

    /**
     * Prepares and runs the detail view widget.
     *
     * @throws ReflectionException
     * @throws Exception
     */
    protected function runWidget()
    {
        if (empty($this->container['id'])) {
            $this->container['id'] = $this->getId() . '-container';
        }
        $this->initI18N(__DIR__);
        $this->addCssClass($this->alertContainerOptions, self::BS_PANEL_BODY);
        Html::addCssClass($this->alertContainerOptions, 'kv-alert-container');
        foreach ($this->alertMessageSettings as $key => $setting) {
            $this->alertMessageSettings[$key] = (array)$setting;
        }
        $this->alertMessageSettings += [
            'kv-detail-error' => ['alert', 'alert-danger'],
            'kv-detail-success' => ['alert', 'alert-success'],
            'kv-detail-info' => ['alert', 'alert-info'],
            'kv-detail-warning' => ['alert', 'alert-warning'],
        ];

        $this->registerAssets();
        $output = $this->renderDetailView();
        if (is_array($this->panel) && !empty($this->panel)) {
            $output = $this->renderPanel($output);
        }
        $output = Lib::strtr(Html::tag('div', $this->mainTemplate, $this->container), ['{detail}' => $output]);
        Html::addCssClass($this->viewButtonsContainer, 'kv-buttons-1');
        $buttons = Html::tag('span', $this->renderButtons(), $this->viewButtonsContainer);
        if ($this->enableEditMode) {
            Html::addCssClass($this->editButtonsContainer, 'kv-buttons-2');
            $buttons .= Html::tag('span', $this->renderButtons(2), $this->editButtonsContainer);
        }
        echo Lib::str_replace('{buttons}', Html::tag('div', $buttons, $this->buttonContainer), $output);
        if ($this->enableEditMode) {
            /**
             * @var ActiveForm $formClass
             */
            $formClass = $this->formClass;
            $formClass::end();
        }

    }

    /**
     * Initializes and renders alert container block.
     *
     * @throws Exception
     */
    protected function renderAlertBlock()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();

        if ($this->showErrorSummary) {
            // Show form error summary in error flash
            if ($this->model->getErrors()) {
                $flashes['kv-detail-error'] = $this->_form->errorSummary($this->model);
            }
        }

        if (count($flashes) === 0) {
            Html::addCssStyle($this->alertContainerOptions, 'display:none;');
        }
        $out = Html::beginTag('div', $this->alertContainerOptions);
        $alertWidgetClass = $this->getBSClass('Alert');
        foreach ($flashes as $type => $message) {
            if (!isset($this->alertMessageSettings[$type])) {
                continue;
            }
            $opts = $this->alertWidgetOptions;
            $options = ArrayHelper::getValue($opts, 'options', []);
            Html::addCssClass($options, $this->alertMessageSettings[$type]);
            $opts['body'] = is_array($message) ? implode('<br>', $message) : $message;
            $opts['options'] = $options;
            /** @noinspection PhpUndefinedMethodInspection */
            $out .= "\n" . $alertWidgetClass::widget($opts);
            $session->removeFlash($type);
        }
        $out .= "\n</div>";
        return $out;
    }

    /**
     * Check if model has editing errors.
     *
     * @return boolean
     */
    protected function hasEditErrors()
    {
        if ($this->model instanceof Model && $this->model->hasErrors()) {
            return true;
        }
        foreach ($this->attributes as $attribute) {
            /**
             * @var Model $attribute ['editModel']
             */
            if (empty($attribute['editModel']) || !$attribute['editModel'] instanceof Model) {
                continue;
            }
            if ($attribute['editModel']->hasErrors()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Validates the display of correct attributes and buttons at initialization based on mode.
     *
     * @return void
     */
    protected function validateDisplay()
    {
        $none = 'display:none';
        if ($this->hasEditErrors()) {
            $this->mode = self::MODE_EDIT;
        }
        if ($this->mode === self::MODE_EDIT) {
            Html::addCssClass($this->container, 'kv-edit-mode');
            Html::addCssStyle($this->viewAttributeContainer, $none);
            Html::addCssStyle($this->viewButtonsContainer, $none);
        } else {
            Html::addCssClass($this->container, 'kv-view-mode');
            Html::addCssStyle($this->editAttributeContainer, $none);
            Html::addCssStyle($this->editButtonsContainer, $none);
        }
    }

    /**
     * Renders the main detail view widget.
     *
     * @return string the detail view content
     * @throws InvalidConfigException
     */
    protected function renderDetailView()
    {
        $rows = [];
        foreach ($this->attributes as $attribute) {
            $rows[] = $this->renderAttributeRow($attribute);
        }
        $tag = ArrayHelper::remove($this->options, 'tag', 'table');
        $output = Html::tag($tag, implode("\n", $rows), $this->options);
        $css = 'kv-detail-view';
        if ($this->bootstrap && $this->responsive) {
            $css = [$css, 'table-responsive'];
        }
        Html::addCssClass($this->tableContainer, $css);
        return Html::tag('div', $output, $this->tableContainer);
    }

    /**
     * Renders a single attribute.
     *
     * @param array $attribute the specification of the attribute to be rendered.
     *
     * @return string the rendering result
     * @throws InvalidConfigException
     */
    protected function renderAttributeRow($attribute)
    {
        $this->_rowOptions = ArrayHelper::getValue($attribute, 'rowOptions', $this->rowOptions);
        if (isset($attribute['columns'])) {
            Html::addCssClass($this->_rowOptions, 'kv-child-table-row');
            $table = Html::beginTag('table', $this->childTableOptions);
            $content = '<td class="kv-child-table-cell" colspan=2>' . $table . '<tr>';
            foreach ($attribute['columns'] as $child) {
                $content .= $this->renderAttributeItem($child);
            }
            $content .= '</tr></table></td>';
        } else {
            $content = $this->renderAttributeItem($attribute);
        }
        return Html::tag('tr', $content, $this->_rowOptions);
    }

    /**
     * Renders a single attribute item combination.
     *
     * @param array $attribute the specification of the attribute to be rendered.
     *
     * @return string the rendering result
     * @throws InvalidConfigException
     */
    protected function renderAttributeItem($attribute)
    {
        $labelColOpts = ArrayHelper::getValue($attribute, 'labelColOptions', $this->labelColOptions);
        $valueColOpts = ArrayHelper::getValue($attribute, 'valueColOptions', $this->valueColOptions);
        if (ArrayHelper::getValue($attribute, 'group', false)) {
            $groupOptions = ArrayHelper::getValue($attribute, 'groupOptions', []);
            $label = ArrayHelper::getValue($attribute, 'label', '');
            if (empty($groupOptions['colspan'])) {
                $groupOptions['colspan'] = 2;
            }
            return Html::tag('th', $label, $groupOptions);
        }
        if ($this->hideIfEmpty === true && empty($attribute['value'])) {
            Html::addCssClass($this->_rowOptions, 'kv-view-hidden');
        }
        if (ArrayHelper::getValue($attribute, 'type', 'text') === self::INPUT_HIDDEN) {
            Html::addCssClass($this->_rowOptions, 'kv-edit-hidden');
        }
        /**
         * issue #158 & enh #185
         */
        $value = $attribute['value'];

        if ($this->arrayValueToString && is_array($attribute['value'])) {
            $value =  print_r($value, true);
        }

        if ($this->notSetIfEmpty && ($value === '' || $value === null)) {
            $value = null;
        }
        $dispAttr = $this->formatter->format($value, $attribute['format']);
        Html::addCssClass($this->viewAttributeContainer, 'kv-attribute');
        Html::addCssClass($this->editAttributeContainer, 'kv-form-attribute');
        $output = Html::tag('div', $dispAttr, $this->viewAttributeContainer) . "\n";
        if ($this->enableEditMode) {
            $editInput = ArrayHelper::getValue($attribute, 'displayOnly', false) ? $dispAttr :
                $this->renderFormAttribute($attribute);
            $output .= Html::tag('div', $editInput, $this->editAttributeContainer);
        }
        return Html::tag('th', $attribute['label'], $labelColOpts) . "\n" . Html::tag('td', $output, $valueColOpts);
    }

    /**
     * Checks if a bootstrap grid column class has been added to the container.
     *
     * @param  array  $container
     *
     * @return boolean
     * @throws Exception
     */
    protected static function hasGridCol($container = [])
    {
        $css = ArrayHelper::getValue($container, 'class', '');
        $css = Lib::trim($css);
        $css = Lib::preg_replace('/\s+/', ' ', $css);
        if (empty($css)) {
            return false;
        }
        $classes = Lib::explode(' ', $css);
        if (!empty($classes)) {
            foreach ($classes as $class) {
                if (Lib::substr(Lib::trim($class), 0, 4) === 'col-') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Renders each form attribute.
     *
     * @param array $config the attribute config
     *
     * @return mixed
     * @throws InvalidConfigException
     */
    protected function renderFormAttribute($config)
    {
        if (empty($config['attribute'])) {
            return '';
        }
        $model = ArrayHelper::getValue($config, 'editModel', $this->model);
        if (!$model instanceof Model) {
            $model = $this->model;
        }
        if (isset($config['updateMarkup'])) {
            $markup = $config['updateMarkup'];
            return $markup instanceof Closure ? $markup($this->_form, $this) : $markup;
        }
        $attr = ArrayHelper::getValue($config, 'updateAttr', $config['attribute']);
        $input = ArrayHelper::getValue($config, 'type', self::INPUT_TEXT);
        $fieldConfig = ArrayHelper::getValue($config, 'fieldConfig', []);
        $inputWidth = ArrayHelper::getValue($config, 'inputWidth', '');
        $container = ArrayHelper::getValue($config, 'inputContainer', []);
        if ($inputWidth != '') {
            Html::addCssStyle($container, "width: {$inputWidth}"); // deprecated since v1.7.7
        }
        $template = ArrayHelper::getValue($fieldConfig, 'template', "{input}\n{error}\n{hint}");
        $row = Html::tag('div', $template, $container);
        if (static::hasGridCol($container)) {
            $row = '<div class="row">' . $row . '</div>';
        }
        $fieldConfig['template'] = $row;
        if (!isset($fieldConfig['options'])) {
            $fieldConfig['options'] = ['class' => ''];
        } else {
            Html::removeCssClass($fieldConfig['options'], 'mb-3');
        }
        if (Lib::substr($input, 0, 8) == "\\kartik\\") {
            Config::validateInputWidget($input, 'as an input widget for DetailView edit mode');
        } elseif ($input !== self::INPUT_WIDGET && !in_array($input, self::$_inputsList)) {
            throw new InvalidConfigException(
                "Invalid input type '{$input}' defined for the attribute '" . $config['attribute'] . "'."
            );
        }
        $options = ArrayHelper::getValue($config, 'options', []);
        $widgetOptions = ArrayHelper::getValue($config, 'widgetOptions', []);
        $class = ArrayHelper::remove($widgetOptions, 'class', '');
        if (!empty($config['options'])) {
            $widgetOptions['options'] = $config['options'];
        }
        if (Config::isInputWidget($input)) {
            $class = $input;
            return $this->_form->field($model, $attr, $fieldConfig)->widget($class, $widgetOptions);
        }
        if ($input === self::INPUT_WIDGET) {
            if ($class == '') {
                throw new InvalidConfigException("Widget class not defined in 'widgetOptions' for {$input}'.");
            }
            return $this->_form->field($model, $attr, $fieldConfig)->widget($class, $widgetOptions);
        }
        if (in_array($input, self::$_dropDownInputs)) {
            $items = ArrayHelper::getValue($config, 'items', []);
            return $this->_form->field($model, $attr, $fieldConfig)->$input($items, $options);
        }
        if ($input == self::INPUT_HTML5) {
            $inputType = ArrayHelper::getValue($config, 'inputType', self::INPUT_TEXT);
            return $this->_form->field($model, $attr, $fieldConfig)->$input($inputType, $options);
        }
        return $this->_form->field($model, $attr, $fieldConfig)->$input($options);
    }

    /**
     * Sets a default css class within `options` if not set.
     *
     * @param array $options the HTML options
     * @param string|array $css the CSS class to test and append
     */
    protected static function initCss(&$options, $css)
    {
        if (!isset($options['class'])) {
            $options['class'] = $css;
        }
    }

    /**
     * Sets the grid panel layout based on the [[template]] and [[panel]] settings.
     *
     * @param string $items
     * @return string
     * @throws InvalidConfigException|Exception
     */
    protected function renderPanel($items)
    {
        if (!$this->bootstrap || !is_array($this->panel) || empty($this->panel)) {
            return '';
        }
        $options = ArrayHelper::getValue($this->panel, 'options', []);
        $type = ArrayHelper::getValue($this->panel, 'type', 'default');
        $heading = ArrayHelper::getValue($this->panel, 'heading', '');
        $footer = ArrayHelper::getValue($this->panel, 'footer', false);
        $before = ArrayHelper::getValue($this->panel, 'before', '');
        $after = ArrayHelper::getValue($this->panel, 'after', false);
        $headingOptions = ArrayHelper::getValue($this->panel, 'headingOptions', []);
        $titleOptions = ArrayHelper::getValue($this->panel, 'titleOptions', []);
        $footerOptions = ArrayHelper::getValue($this->panel, 'footerOptions', []);
        $beforeOptions = ArrayHelper::getValue($this->panel, 'beforeOptions', []);
        $afterOptions = ArrayHelper::getValue($this->panel, 'afterOptions', []);
        $panelHeading = '';
        $panelBefore = '';
        $panelAfter = '';
        $panelFooter = '';
        $notBs3 = !$this->isBs(3);
        if (isset($this->panelCssPrefix)) {
            static::initCss($options, $this->panelCssPrefix . $type);
        } else {
            $this->addCssClass($options, self::BS_PANEL);
            Html::addCssClass($options, $notBs3 ? "border-{$type}" : "panel-{$type}");
        }
        if ($after === false && $footer === false) {
            Html::addCssClass($this->container, 'kv-flat-b');
        }
        $titleTag = ArrayHelper::remove($titleOptions, 'tag', ($notBs3 ? 'h5' : 'h3'));
        static::initCss($titleOptions, $notBs3 ? 'm-0' : $this->getCssClass(self::BS_PANEL_TITLE));
        if ($heading !== false) {
            $color = ' ' . $this->getCssClass("panel-{$type}");
            static::initCss($headingOptions, $this->getCssClass(self::BS_PANEL_HEADING) . $color);
            $panelHeading = Html::tag('div', $this->panelHeadingTemplate, $headingOptions);
        }
        if ($footer !== false) {
            static::initCss($footerOptions, $this->getCssClass(self::BS_PANEL_FOOTER));
            $panelFooter = Html::tag('div', $footer, $footerOptions);
        }
        if ($before !== false) {
            static::initCss($beforeOptions, 'kv-panel-before');
            $alertBlock = $this->hideAlerts ? '' : $this->renderAlertBlock() . "\n";
            $panelBefore = Html::tag('div', $alertBlock . $before, $beforeOptions);
        }
        if ($after !== false) {
            static::initCss($afterOptions, 'kv-panel-after');
            $panelAfter = Html::tag('div', $after, $afterOptions);
        }
        $out = Lib::strtr($this->panelTemplate, [
            '{panelHeading}' => $panelHeading,
            '{type}' => $type,
            '{items}' => $items,
            '{panelFooter}' => $panelFooter,
            '{panelBefore}' => $panelBefore,
            '{panelAfter}' => $panelAfter,
        ]);

        return Html::tag('div', Lib::strtr($out, [
            '{title}' => Html::tag($titleTag, $heading, $titleOptions),
        ]), $options);
    }

    /**
     * Renders the buttons for a specific mode.
     *
     * @param integer $mode
     *
     * @return string the buttons content
     * @throws InvalidConfigException
     */
    protected function renderButtons($mode = 1)
    {
        $buttons = "buttons{$mode}";
        return Lib::strtr(
            $this->$buttons,
            [
                '{view}' => $this->renderButton('view'),
                '{update}' => $this->renderButton('update'),
                '{delete}' => $this->renderButton('delete'),
                '{save}' => $this->renderButton('save'),
                '{reset}' => $this->renderButton('reset'),
            ]
        );
    }

    /**
     * Renders a button.
     *
     * @param string $type the button type
     *
     * @return string
     * @throws InvalidConfigException
     */
    protected function renderButton($type)
    {
        if (!$this->enableEditMode) {
            return '';
        }
        switch ($type) {
            case 'view':
                return $this->getDefaultButton('view', 'eye-open', 'eye', Yii::t('kvdetail', 'View'));
            case 'update':
                return $this->getDefaultButton('update', 'pencil', 'pencil-alt', Yii::t('kvdetail', 'Update'));
            case 'delete':
                return $this->getDefaultButton('delete', 'trash', 'trash-alt', Yii::t('kvdetail', 'Delete'));
            case 'save':
                return $this->getDefaultButton('save', 'floppy-disk', 'save', Yii::t('kvdetail', 'Save'));
            case 'reset':
                return $this->getDefaultButton('reset', 'ban-circle', 'ban', Yii::t('kvdetail', 'Cancel Changes'));
            default:
                return '';
        }
    }

    /**
     * Gets the default button.
     *
     * @param string $type the button type
     * @param string $iconBs3 the bootstrap 3 icon suffix name
     * @param string $iconNotBs3 the non bootstrap 3 icon suffix name
     * @param string $title the title to display on hover
     *
     * @return string
     * @throws InvalidConfigException|Exception
     */
    protected function getDefaultButton($type, $iconBs3, $iconNotBs3, $title)
    {
        $buttonOptions = $type . 'Options';
        $options = $this->$buttonOptions;
        $css = $this->getDefaultIconPrefix() . (!$this->isBs(3) ? $iconNotBs3 : $iconBs3);
        $label = ArrayHelper::remove($options, 'label', '<i class="' . $css . '"></i>');
        if (empty($options['class'])) {
            $options['class'] = 'kv-action-btn';
        }
        Html::addCssClass($options, 'kv-btn-' . $type);
        $options = ArrayHelper::merge(['title' => $title], $options);
        if ($this->tooltips) {
            $options['data-toggle'] = 'tooltip';
            $options['data-container'] = 'body';
        }
        switch ($type) {
            case 'reset':
                return Html::resetButton($label, $options);
            case 'save':
                return Html::submitButton($label, $options);
            case 'delete':
                $url = ArrayHelper::remove($options, 'url', '#');
                return Html::a($label, $url, $options);
        }
        $options['type'] = 'button';
        return Html::button($label, $options);
    }

    /**
     * Register client assets for the [[DetailView]] widget.
     *
     * @throws Exception
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        DetailViewAsset::register($view);
        Dialog::widget($this->krajeeDialogSettings);
        if (empty($this->alertWidgetOptions['closeButton'])) {
            $button = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        } else {
            $opts = $this->alertWidgetOptions['closeButton'];
            $tag = ArrayHelper::remove($opts, 'tag', 'button');
            $label = ArrayHelper::remove($opts, 'label', '&times;');
            if ($tag === 'button' && !isset($opts['type'])) {
                $opts['type'] = 'button';
            }
            $button = Html::tag($tag, $label, $opts);
        }
        $opts = ArrayHelper::getValue($this->alertWidgetOptions, 'options', []);
        $css = '{class} fade ' . $this->getCssClass(self::BS_SHOW);
        if (!empty($opts['class'])) {
            $opts['class'] .= ' ' . $css;
        } else {
            $opts['class'] = $css;
        }
        $deleteConfirmMsg = Yii::t('kvdetail', 'Are you sure you want to delete this item?');
        $this->pluginOptions = [
            'fadeDelay' => $this->fadeDelay,
            'alertTemplate' => Html::tag('div', $button . '{content}', $opts),
            'alertMessageSettings' => $this->alertMessageSettings,
            'deleteParams' => ArrayHelper::getValue($this->deleteOptions, 'params', []),
            'deleteAjaxSettings' => ArrayHelper::getValue($this->deleteOptions, 'ajaxSettings', []),
            'deleteConfirm' => ArrayHelper::remove($this->deleteOptions, 'confirm', $deleteConfirmMsg),
            'showErrorStack' => ArrayHelper::remove($this->deleteOptions, 'showErrorStack', false),
            'dialogLib' => ArrayHelper::getValue($this->krajeeDialogSettings, 'libName', 'krajeeDialog'),
        ];
        $id = 'jQuery("#' . $this->container['id'] . '")';
        $this->registerPlugin($this->pluginName, $id);
        if ($this->tooltips) {
            PluginAssetBundle::registerBundle($view, $this->bsVersion);
            $view->registerJs($id . '.find("[data-toggle=tooltip]").tooltip();');
        }
    }

    /**
     * Normalizes the attribute specifications.
     *
     * @throws InvalidConfigException
     */
    protected function normalizeAttributes()
    {
        if ($this->attributes === null) {
            if ($this->model instanceof Model) {
                $this->attributes = $this->model->attributes();
            } elseif (is_object($this->model)) {
                $this->attributes = $this->model instanceof Arrayable ? $this->model->toArray() :
                    array_keys(get_object_vars($this->model));
            } elseif (is_array($this->model)) {
                $this->attributes = array_keys($this->model);
            } else {
                throw new InvalidConfigException('The "model" property must be either an array or an object.');
            }
            sort($this->attributes);
        }
        foreach ($this->attributes as $i => $attribute) {
            $this->attributes[$i] = $this->parseAttributeItem($attribute);
            if (isset($attribute['visible']) && !$attribute['visible']) {
                unset($this->attributes[$i]);
            }
        }
    }

    /**
     * Parses and returns the attribute.
     *
     * @param string|array $attribute the attribute item configuration
     *
     * @return array the parsed attribute item configuration
     * @throws InvalidConfigException
     */
    protected function parseAttributeItem($attribute)
    {
        if (is_string($attribute)) {
            $matches = [];
            if (!Lib::preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $attribute, $matches)) {
                throw new InvalidConfigException(
                    'The attribute must be specified in the format of "attribute", "attribute:format" or ' .
                    '"attribute:format:label"'
                );
            }
            $attribute = [
                'attribute' => ArrayHelper::getValue($matches, 1),
                'format' => ArrayHelper::getValue($matches, 3, 'text'),
                'label' => ArrayHelper::getValue($matches, 5),
            ];
        }
        if (!is_array($attribute)) {
            throw new InvalidConfigException('The attribute configuration must be an array.');
        }
        foreach ($attribute as $prop => $setting) {
            $attribute[$prop] = $this->parseAttributeProp($setting);
        }
        if (isset($attribute['columns'])) {
            foreach ($attribute['columns'] as $j => $child) {
                $attr = $this->parseAttributeItem($child);
                if (isset($attr['visible']) && !$attr['visible']) {
                    unset($attribute['columns'][$j]);
                    continue;
                }
                $attribute['columns'][$j] = $attr;
            }
            return $attribute;
        }
        $attr = ArrayHelper::getValue($attribute, 'updateAttr');
        if ($attr && !ctype_alnum(Lib::str_replace('_', '', $attr))) {
            throw new InvalidConfigException("The 'updateAttr' name '{$attr}' is invalid.");
        }
        $attr = ArrayHelper::getValue($attribute, 'attribute', '');
        if ($attr && Lib::strpos($attr, '.') !== false) {
            throw new InvalidConfigException(
                "The attribute '{$attr}' is invalid. You cannot directly pass relational attributes in string format " .
                "within '\\kartik\\widgets\\DetailView'. Instead use the array format with 'attribute' property " .
                "set to base field, and the 'value' property returning the relational data. You can also override the " .
                "widget 'model' settings by setting the 'viewModel' and / or 'editModel' at the attribute array level."
            );
        }
        if (!isset($attribute['format'])) {
            $attribute['format'] = 'text';
        }
        if (isset($attribute['attribute'])) {
            $attributeName = $attribute['attribute'];
            $model = !empty($attribute['viewModel']) && $attribute['viewModel'] instanceof Model ?
                $attribute['viewModel'] : $this->model;
            if (!isset($attribute['label'])) {
                $attribute['label'] = $model instanceof Model ? $model->getAttributeLabel($attributeName) :
                    Inflector::camel2words($attributeName, true);
            }
            if (!array_key_exists('value', $attribute)) {
                $attribute['value'] = ArrayHelper::getValue($model, $attributeName);
            }
        } elseif (!isset($attribute['label']) || !array_key_exists('value', $attribute)) {
            if (ArrayHelper::getValue($attribute, 'group', false) || isset($attribute['columns'])) {
                $attribute['value'] = '';
                return $attribute;
            }
            throw new InvalidConfigException(
                'The attribute configuration requires the "attribute" element to determine the value and display label.'
            );
        }
        return $attribute;
    }

    /**
     * Parses the attribute configuration and validates if a property is configured as a Closure callback. If so, the
     * callback is executed and the attribute property is set to this callback output.
     *
     * The signature of the callback is `function($form, $widget)`, where:
     *
     * - `$form`: [[ActiveForm]], is the current active form object in the detail view.
     * - `$widget`: [[DetailView]], is the current detail view widget instance.
     *
     * @param mixed $setting is the attribute property setting
     *
     * @return mixed the parsed attribute setting
     */
    protected function parseAttributeProp($setting)
    {
        /**
         * @var Closure|mixed $setting
         */
        return $setting instanceof Closure ? $setting($this->_form, $this) : $setting;
    }
}
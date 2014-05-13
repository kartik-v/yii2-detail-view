<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-grid
 * @version 1.0.0
 */

namespace kartik\detail;

use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\helpers\Json;
use kartik\helpers\Html;
use yii\base\InvalidConfigException;
use yii\bootstrap\ButtonDropdown;

/**
 * Enhances the Yii DetailView widget with various options to include Bootstrap
 * specific styling enhancements. Also allows to simply disable Bootstrap styling
 * by setting `bootstrap` to false. In addition, it allows you to directly edit
 * the detail grid data using a form.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DetailView extends \yii\widgets\DetailView
{

    /**
     * Bootstrap Contextual Color Types
     */
    const TYPE_DEFAULT = 'default'; // only applicable for panel contextual style
    const TYPE_PRIMARY = 'primary';
    const TYPE_INFO = 'info';
    const TYPE_DANGER = 'danger';
    const TYPE_WARNING = 'warning';
    const TYPE_SUCCESS = 'success';
    const TYPE_ACTIVE = 'active'; // only applicable for table row contextual style

    /**
     * Alignment
     */
    // Horizontal Alignment
    const ALIGN_RIGHT = 'right';
    const ALIGN_CENTER = 'center';
    const ALIGN_LEFT = 'left';
    // Vertical Alignment
    const ALIGN_TOP = 'top';
    const ALIGN_MIDDLE = 'middle';
    const ALIGN_BOTTOM = 'bottom';
    // CSS for preventing cell wrapping
    const NOWRAP = 'kv-nowrap';

    /**
     * Edit input types
     */
    // input types
    const INPUT_TEXT = 'textInput';
    const INPUT_TEXTAREA = 'textArea';
    const INPUT_CHECKBOX = 'checkbox';
    const INPUT_RADIO = 'radio';
    const INPUT_DROPDOWN_LIST = 'dropDownList';
    const INPUT_CHECKBOX_LIST = 'checkboxList';
    const INPUT_RADIO_LIST = 'radioList';
    const INPUT_HTML5_INPUT = 'input';

    // input widget classes
    const INPUT_SELECT2 = '\kartik\widgets\Select2';
    const INPUT_TYPEAHEAD = '\kartik\widgets\Typeahead';
    const INPUT_SWITCH = '\kartik\widgets\SwitchInput';
    const INPUT_SPIN = '\kartik\widgets\TouchSpin';
    const INPUT_STAR = '\kartik\widgets\StarRating';
    const INPUT_DATE = '\kartik\widgets\DatePicker';
    const INPUT_TIME = '\kartik\widgets\TimePicker';
    const INPUT_DATETIME = '\kartik\widgets\DateTimePicker';
    const INPUT_RANGE = '\kartik\widgets\RangeInput';
    const INPUT_COLOR = '\kartik\widgets\ColorInput';

    private static $_inputsList = [
        self::INPUT_TEXT => 'textInput',
        self::INPUT_PASSWORD => 'passwordInput',
        self::INPUT_TEXTAREA => 'textArea',
        self::INPUT_CHECKBOX => 'checkbox',
        self::INPUT_RADIO => 'radio',
        self::INPUT_LIST_BOX => 'listBox',
        self::INPUT_DROPDOWN_LIST => 'dropDownList',
        self::INPUT_CHECKBOX_LIST => 'checkboxList',
        self::INPUT_RADIO_LIST => 'radioList',
        self::INPUT_HTML5_INPUT => 'input',
        self::INPUT_FILE => 'fileInput',
        self::INPUT_WIDGET => 'widget',
    ];

    private static $_inputWidgets = [
        self::INPUT_SELECT2 => '\kartik\widgets\Select2',
        self::INPUT_TYPEAHEAD => '\kartik\widgets\Typeahead',
        self::INPUT_SWITCH => '\kartik\widgets\SwitchInput',
        self::INPUT_SPIN => '\kartik\widgets\TouchSpin',
        self::INPUT_STAR => '\kartik\widgets\StarRating',
        self::INPUT_DATE => '\kartik\widgets\DatePicker',
        self::INPUT_TIME => '\kartik\widgets\TimePicker',
        self::INPUT_DATETIME => '\kartik\widgets\DateTimePicker',
        self::INPUT_RANGE => '\kartik\widgets\RangeInput',
        self::INPUT_COLOR => '\kartik\widgets\ColorInput',
    ];

    private static $_dropDownInputs = [
        self::INPUT_LIST_BOX => 'listBox',
        self::INPUT_DROPDOWN_LIST => 'dropDownList',
        self::INPUT_CHECKBOX_LIST => 'checkboxList',
        self::INPUT_RADIO_LIST => 'radioList',

    ];

    /**
     * @var array the HTML attributes for the detail view table
     */
    public $options = [];

    /**
     * @var bool whether the grid view will have Bootstrap table styling.
     */
    public $bootstrap = true;

    /**
     * @var bool whether the grid table will have a `bordered` style.
     * Applicable only if `bootstrap` is `true`. Defaults to `true`.
     */
    public $bordered = true;

    /**
     * @var bool whether the grid table will have a `striped` style.
     * Applicable only if `bootstrap` is `true`. Defaults to `true`.
     */
    public $striped = true;

    /**
     * @var bool whether the grid table will have a `condensed` style.
     * Applicable only if `bootstrap` is `true`. Defaults to `false`.
     */
    public $condensed = false;

    /**
     * @var bool whether the grid table will have a `responsive` style.
     * Applicable only if `bootstrap` is `true`. Defaults to `true`.
     */
    public $responsive = true;

    /**
     * @var bool whether to enable edit mode for the grid. Defaults to `true`.
     */
    public $enableEditMode = true;

    /**
     * @var array a list of attributes to be displayed in the detail view. Each array element
     * represents the specification for displaying one particular attribute.
     *
     * An attribute can be specified as a string in the format of "attribute", "attribute:format" or "attribute:format:label",
     * where "attribute" refers to the attribute name, and "format" represents the format of the attribute. The "format"
     * is passed to the [[Formatter::format()]] method to format an attribute value into a displayable text.
     * Please refer to [[Formatter]] for the supported types. Both "format" and "label" are optional.
     * They will take default values if absent.
     *
     * An attribute can also be specified in terms of an array with the following elements:
     *
     * - attribute: the attribute name. This is required if either "label" or "value" is not specified.
     * - label: the label associated with the attribute. If this is not specified, it will be generated from the attribute name.
     * - value: the value to be displayed. If this is not specified, it will be retrieved from [[model]] using the attribute name
     *   by calling [[ArrayHelper::getValue()]]. Note that this value will be formatted into a displayable text
     *   according to the "format" option.
     * - format: the type of the value that determines how the value would be formatted into a displayable text.
     *   Please refer to [[Formatter]] for supported types.
     * - visible: whether the attribute is visible. If set to `false`, the attribute will NOT be displayed.
     *
     * Additional special attributes are:
     * - updateAttr: string, the name of the attribute to be updated, when in edit mode. If not set, will default to the
     *   `attribute` setting.
     * - type: string, the input type for rendering the attribute in edit mode. Must be one of the [[self::INPUT_]] constants.
     * - widgetOptions: array, the widget options if you set `type` to [[self::INPUT_WIDGET]]. The following special options are
     *   recognized:
     *   - `class': string the fully namespaced widget class.
     * - items: array, the list of data items  for dropDownList, listBox, checkboxList & radioList
     * - inputType: string, the HTML 5 input type if `type` is set to [[self::INPUT_HTML 5]].
     * - options: array, the HTML attributes for the input
     */
    public $attributes;

    /**
     * @var array the options for the ActiveForm that will be generated in edit mode.
     */
    public $formOptions = [];

    /**
     * @var bool whether the grid table will highlight row on `hover`.
     * Applicable only if `bootstrap` is `true`. Defaults to `false`.
     */
    public $hover = false;

    /**
     * @var array the panel settings. If this is set, the grid widget
     * will be embedded in a bootstrap panel. Applicable only if `bootstrap`
     * is `true`. The following array keys are supported:
     * - `heading`: string, the panel heading. If not set, will not be displayed. The buttons by default
     *   will be displayed at the right top corner.
     * - `type`: string, the panel contextual type (one of the TYPE constants,
     *    if not set will default to `default` or `self::TYPE_DEFAULT`),
     * - `footer`: string, the panel footer. If not set, will not be displayed.
     * - 'preBody': string, content to be placed before/above the detail view table (after the header).
     * - 'postBody': string, any content to be placed after/below the detail view table (before the footer).
     */
    public $panel = [];

    /**
     * @var string the main template to render the detail view. The following tags will be replaced:
     * - `{view}`: will be replaced by the rendered detail view
     * - `{buttons}`: the buttons to edit and save.
     */
    public $mainTemplate = "{view}";

    /**
     * @var callable a callback that creates a button URL using the specified model information.
     * The signature of the callback should be the same as that of [[createUrl()]].
     * If this property is not set, button URLs will be created using [[createUrl()]].
     */
    public $urlCreator;

    /**
     * @var string the buttons to show when in view mode. The following tags will be replaced:
     * - `{view}`: the view button
     * - `{update}`: the update button
     * - `{delete}`: the delete button
     * - `{save}`: the save button
     * Defaults to `{edit} {delete}`.
     */
    public $buttons1 = '{update} {delete}';

    /**
     * @var string the buttons template to show when in edit mode. The following tags will be replaced:
     * - `{view}`: the view button
     * - `{update}`: the update button
     * - `{delete}`: the delete button
     * - `{save}`: the save button
     * Defaults to `{view} {save}`.
     */
    public $buttons2 = '{view} {save}';

    /**
     * @var array the HTML attributes for the update button. This button will toggle the edit mode on.
     * The following special options are recognized:
     * - `label`: the update button label. This will not be HTML encoded.
     *    Defaults to '<span class="glyphicon glyphicon-pencil"></span>'.
     * - `url`: the edit button url. If not set will default to `#`.
     */
    public $updateButtonOptions = [];

    /**
     * @var array the HTML attributes for the edit button. The following special options are recognized:
     * - `label`: the delete button label. This will not be HTML encoded.
     *    Defaults to '<span class="glyphicon glyphicon-trash"></span>'.
     * - `url`: the edit button url. If not set will default to `#`.
     */
    public $deleteButtonOptions = [];

    /**
     * @var array the HTML attributes for the save button. This will default to a form submit button.
     * The following special options are recognized:
     * - `label`: the save button label. This will not be HTML encoded.
     *    Defaults to '<span class="glyphicon glyphicon-floppy-disk"></span>'.
     */
    public $saveButtonOptions = [];

    /**
     * @var array the HTML attributes for the view button. This will toggle the view from edit mode to view mode.
     * The following special options are recognized:
     * - `label`: the save button label. This will not be HTML encoded.
     *    Defaults to '<span class="glyphicon glyphicon-eye-open"></span>'.
     */
    public $viewButtonOptions = [];

    protected $this->_form;

    private $_id;

    public function init()
    {
        Html::addCssClass($this->options, 'detail-view');
        if ($this->bootstrap) {
            Html::addCssClass($this->options, 'table');
            if ($this->hover) {
                Html::addCssClass($this->options, 'table-hover');
            }
            if ($this->bordered) {
                Html::addCssClass($this->options, 'table-bordered');
            }
            if ($this->striped) {
                Html::addCssClass($this->options, 'table-striped');
            }
            if ($this->condensed) {
                Html::addCssClass($this->options, 'table-condensed');
            }
        }
        parent:: init();
        $this->_id = $this->getId();
        Html::addCssClass($this->formOptions, 'kv-detail-view-form');
        $this->formOptions['fieldConfig']['template'] = "{input}\n{hint}\n{error}";
        $this->_form = ActiveForm::begin($this->formOptions);
        $this->registerAssets();
    }

    /**
     * Renders the detail view.
     * This is the main entry of the whole detail view rendering.
     */
    public function run()
    {
        $output = $this->renderDetailView();
        if (is_array($this->panel) && !empty($this->panel) && $this->panel !== false) {
            $output = $this->renderPanel($output);
        }
        echo strtr($output, [
            '{buttons1}' => $this->renderButtons(1)
        ]);
        ActiveForm::end();
    }

    /**
     * @return string the detail view content
     */
    protected function renderDetailView()
    {
        $rows = [];
        $i = 0;
        foreach ($this->attributes as $attribute) {
            $attr = '<div class="kv-attribute">' . $this->renderAttribute($attribute, $i++) . "</div>\n";
            if ($this->enableEditMode) {
                $rows[] = $attr . '<div class="kv-form-attribute kv-hide">' . $this->renderFormAttribute($attribute) . '</div>';
            } else {
                $rows[] = $attr;
            }
        }
        $tag = ArrayHelper::remove($this->options, 'tag', 'table');
        $output = Html::tag($tag, implode("\n", $rows), $this->options);
        return ($this->bootstrap && $this->responsive) ?
            '<div id="' . $this->_id . '" class="table-responsive">' . $output . '</div>' :
            '<div id="' . $this->_id . '">' . $output . '</div>';
    }

    /**
     * Renders each form attribute
     *
     * @param array $config the attribute config
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    protected function renderFormAttribute($config)
    {
        $attr = ArrayHelper::getValue($config, 'updateAttr', $config['attribute']);
        $input = ArrayHelper::getValue($config, 'type', self::INPUT_TEXT);
        if ($input !== self::INPUT_WIDGET && !in_array($input, static::$_inputsList) && !in_array($input, static::$_widgetsList)) {
            throw new InvalidConfigException("Invalid input type '{$input}' defined for the attribute '" . $config['attribute'] . "'.");
        }
        $options = ArrayHelper::getValue($config, 'options', []);
        $widgetOptions = ArrayHelper::getValue($config, 'widgetOptions', []);
        $class = ArrayHelper::remove($widgetOptions, 'class', '');
        if (!empty($config['options'])) {
            $widgetOptions['options'] = $config['options'];
        }
        if (in_array($input, static::$_widgetsList)) {
            $class = $input;
            return $this->_form($this->model, $attr)->widget($class, $widgetOptions);
        }
        if ($input === self::INPUT_WIDGET) {
            if ($class == '') {
                throw new InvalidConfigException("Widget class not defined in 'widgetOptions' for {$input}'.");
            }
            return $this->_form($this->model, $attr)->widget($class, $widgetOptions);
        }
        if (in_array($input, static::$_dropDownInputs)) {
            $items = ArrayHelper::getValue($config, 'items', []);
            return $this->_form($this->model, $attr)->$input($items, $options);
        }
        return $this->_form($this->model, $attr)->$input($options);
    }

    /**
     * Renders the buttons for a specific mode
     *
     * @param integer $mode
     * @return string the buttons content
     */
    protected function renderButtons($mode = 1)
    {
        $buttons = "buttons{$mode}";
        return '<div class="kv-buttons">' . strtr($this->$buttons, [
            '{view}' => $this->renderButton('view'),
            '{update}' => $this->renderButton('update'),
            '{delete}' => $this->renderButton('delete'),
            '{save}' => $this->renderButton('save'),
        ]) . '</div>';
    }

    /**
     * Returns the bootstrap panel
     *
     * @param string $content
     * @return string
     */
    protected function renderPanel($content)
    {
        $panel = $this->panel;
        $type = ArrayHelper::remove($panel, 'type', self::TYPE_DEFAULT);
        $panel['heading'] = '<div class="pull-right">{buttons1}</div>' .
            ArrayHelper::getValue($panel, 'heading', '');
        $panel['body'] = $content;
        return Html::panel($panel, $type);
    }

    /**
     * Register assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        DetailViewAsset::register($view);
        if ($this->enableEditMode) {
            $options = [
                'buttons1' => $this->renderButtons(1),
                'buttons2' => $this->renderButtons(2)
            ];
            $view->registerJs('$("#' . $this->_id . '").kvDetailView(' . Json::encode($options) . ');');
        }
    }

    /**
     * Renders a button
     *
     * @param string $type the button type
     * @return string
     */
    protected function renderButton($type)
    {
        if (!$this->enableEditMode) {
            return '';
        }
        if ($type === 'view') {
            $options = $this->viewOptions;
            $label = ArrayHelper::remove($options, 'label', '<span class="glyphicon glyphicon-eye-open"></span>');
            Html::addCssClass($options, 'kv-btn-view');
            $options += ['title' => Yii::t('kv-detail', 'View')];
            return Html::button($label, $options);
        }
        if ($type === 'update') {
            $options = $this->updateOptions;
            $label = ArrayHelper::remove($options, 'label', '<span class="glyphicon glyphicon-pencil"></span>');
            Html::addCssClass($options, 'kv-btn-update');
            $options += ['title' => Yii::t('kv-detail', 'Update')];
            return Html::button($label, $options);
        }
        if ($type === 'delete') {
            $options = $this->deleteOptions;
            $label = ArrayHelper::remove($options, 'label', '<span class="glyphicon glyphicon-trash"></span>');
            $url = ArrayHelper::remove($options, 'url', '#');
            Html::addCssClass($options, 'kv-btn-delete');
            $options += [
                'title' => Yii::t('kv-detail', 'Delete'),
                'data-confirm' => Yii::t('kv-detail', 'Are you sure to delete this item?'),
                'data-method' => 'post'
            ];
            return Html::a($label, $url, $options);
        }
        if ($type === 'save') {
            $options = $this->saveOptions;
            $label = ArrayHelper::remove($options, 'label', '<span class="glyphicon glyphicon-floppy-disk"></span>');
            Html::addCssClass($options, 'kv-btn-save');
            $options += ['title' => Yii::t('yii', 'Save')];
            return Html::submitButton($label, $options);
        }
    }
}
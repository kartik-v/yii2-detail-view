<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-detail-view
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
     * Detail View Modes
     */
    const MODE_VIEW = 'view';
    const MODE_EDIT = 'edit';

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

    /**
     * @var string the mode for the Detail View when its initialized
     */
    public $mode = self::MODE_VIEW;
    
    /**
     * @var integer the fade animation delay in microseconds when 
     * toggling between the view and edit modes.
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
     * Edit input types
     */
    // input types
    const INPUT_TEXT = 'textInput';
    const INPUT_PASSWORD = 'passwordInput';
    const INPUT_TEXTAREA = 'textArea';
    const INPUT_CHECKBOX = 'checkbox';
    const INPUT_RADIO = 'radio';
    const INPUT_LIST_BOX = 'listBox';
    const INPUT_DROPDOWN_LIST = 'dropDownList';
    const INPUT_CHECKBOX_LIST = 'checkboxList';
    const INPUT_RADIO_LIST = 'radioList';
    const INPUT_FILE = 'fileInput';
    const INPUT_HTML5_INPUT = 'input';
    const INPUT_WIDGET = 'widget';

    // input widget classes
    const INPUT_DEPDROP = '\kartik\widgets\DepDrop';
    const INPUT_SELECT2 = '\kartik\widgets\Select2';
    const INPUT_TYPEAHEAD = '\kartik\widgets\Typeahead';
    const INPUT_SWITCH = '\kartik\widgets\SwitchInput';
    const INPUT_SPIN = '\kartik\widgets\TouchSpin';
    const INPUT_STAR = '\kartik\widgets\StarRating';
    const INPUT_DATE = '\kartik\widgets\DatePicker';
    const INPUT_TIME = '\kartik\widgets\TimePicker';
    const INPUT_DATETIME = '\kartik\widgets\DateTimePicker';
    const INPUT_DATE_RANGE = '\kartik\daterange\DateRangePicker';
    const INPUT_SORTABLE = '\kartik\sortinput\SortableInput';
    const INPUT_RANGE = '\kartik\widgets\RangeInput';
    const INPUT_COLOR = '\kartik\widgets\ColorInput';
    const INPUT_RATING = '\kartik\widgets\StarRating';
    const INPUT_FILEINPUT = '\kartik\widgets\FileInput';
    const INPUT_SLIDER = '\kartik\slider\Slider';
    const INPUT_MONEY = '\kartik\money\MaskMoney';
    const INPUT_CHECKBOX_X = '\kartik\checkbox\CheckboxX';

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
        self::INPUT_DEPDROP => '\kartik\widgets\DepDrop',
        self::INPUT_SELECT2 => '\kartik\widgets\Select2',
        self::INPUT_TYPEAHEAD => '\kartik\widgets\Typeahead',
        self::INPUT_SWITCH => '\kartik\widgets\SwitchInput',
        self::INPUT_SPIN => '\kartik\widgets\TouchSpin',
        self::INPUT_STAR => '\kartik\widgets\StarRating',
        self::INPUT_DATE => '\kartik\widgets\DatePicker',
        self::INPUT_TIME => '\kartik\widgets\TimePicker',
        self::INPUT_DATETIME => '\kartik\widgets\DateTimePicker',
        self::INPUT_DATE_RANGE => '\kartik\widgets\DateRangePicker',
        self::INPUT_SORTABLE => '\kartik\sortinput\SortableInput',
        self::INPUT_RANGE => '\kartik\widgets\RangeInput',
        self::INPUT_COLOR => '\kartik\widgets\ColorInput',
        self::INPUT_RATING => '\kartik\widgets\StarRating',
        self::INPUT_FILEINPUT => '\kartik\widgets\FileInput',
        self::INPUT_SLIDER => '\kartik\slider\Slider',
        self::INPUT_MONEY => '\kartik\money\MaskMoney',
        self::INPUT_CHECKBOX_X => '\kartik\checkbox\CheckboxX',
    ];

    private static $_dropDownInputs = [
        self::INPUT_LIST_BOX => 'listBox',
        self::INPUT_DROPDOWN_LIST => 'dropDownList',
        self::INPUT_CHECKBOX_LIST => 'checkboxList',
        self::INPUT_RADIO_LIST => 'radioList',
    ];

    /**
     * @var array the HTML attributes for the label column
     */
    public $labelColOptions = ['style' => 'width: 20%'];

    /**
     * @var array the HTML attributes for the value column
     */
    public $valueColOptions = [];

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
     * @var bool whether the grid table will highlight row on `hover`.
     * Applicable only if `bootstrap` is `true`. Defaults to `false`.
     */
    public $hover = false;

    /**
     * @var bool whether to enable edit mode for the detail view. Defaults to `true`.
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
     * Additional special settings (for edit mode) are:
     * - type: string, the input type for rendering the attribute in edit mode. Must be one of the [[self::INPUT_]] constants.
     * - displayOnly: boolean, if the input is to be set to as `display only` in edit mode.
     * - widgetOptions: array, the widget options if you set `type` to [[self::INPUT_WIDGET]]. The following special options are
     *   recognized:
     *   - `class`: string the fully namespaced widget class.
     * - items: array, the list of data items  for dropDownList, listBox, checkboxList & radioList
     * - inputType: string, the HTML 5 input type if `type` is set to [[self::INPUT_HTML 5]].
     * - inputWidth: string, the width of the container holding the input, should be appended along with the width unit (`px` or `%`)
     * - fieldConfig: array, optional, the Active field configuration.
     * - options: array, optional, the HTML attributes for the input.
     * - updateAttr: string, optional, the name of the attribute to be updated, when in edit mode. This will default to the
     *   the `attribute` setting.
     */
    public $attributes;

    /**
     * @var array the options for the ActiveForm that will be generated in edit mode.
     */
    public $formOptions = [];

    /**
     * @var array the panel settings. If this is set, the grid widget
     * will be embedded in a bootstrap panel. Applicable only if `bootstrap`
     * is `true`. The following array keys are supported:
     * - `heading`: string, the panel heading. If not set, will not be displayed. The buttons by default
     *   will be displayed at the right top corner.
     * - `type`: string, the panel contextual type (one of the TYPE constants,
     *    if not set will default to `default` or `self::TYPE_DEFAULT`),
     * - `footer`: string, the panel footer. If not set, will not be displayed.
     */
    public $panel = [];

    /**
     * @var string the main template to render the detail view. The following tags will be replaced:
     * - `{detail}`: will be replaced by the rendered detail view
     * - `{buttons}`: the buttons to be displayed as set in `buttons1` and `buttons2`.
     */
    public $mainTemplate = "{detail}";

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
     * @var array the HTML attributes for the view button. This will toggle the view from edit mode to view mode.
     * The following special options are recognized:
     * - `label`: the save button label. This will not be HTML encoded.
     *    Defaults to '<span class="glyphicon glyphicon-eye-open"></span>'.
     */
    public $viewOptions = [];

    /**
     * @var array the HTML attributes for the update button. This button will toggle the edit mode on.
     * The following special options are recognized:
     * - `label`: the update button label. This will not be HTML encoded.
     *    Defaults to '<span class="glyphicon glyphicon-pencil"></span>'.
     */
    public $updateOptions = [];

    /**
     * @var array the HTML attributes for the edit button. The following special options are recognized:
     * - `label`: the delete button label. This will not be HTML encoded.
     *    Defaults to '<span class="glyphicon glyphicon-trash"></span>'.
     * - `url`: the delete button url. If not set will default to `#`.
     */
    public $deleteOptions = [];

    /**
     * @var array the HTML attributes for the save button. This will default to a form submit button.
     * The following special options are recognized:
     * - `label`: the save button label. This will not be HTML encoded.
     *    Defaults to '<span class="glyphicon glyphicon-floppy-disk"></span>'.
     */
    public $saveOptions = [];

    /**
     * @var array the HTML attributes for the widget container
     */
    public $container = [];

    /**
     * @var array the the internalization configuration for this module
     */
    public $i18n = [];

    /**
     * @var ActiveForm the form instance
     */
    protected $_form;

    public function init()
    {
        foreach ($this->attributes as $attribute) {
            static::validateAttribute($attribute);
        }
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
        Html::addCssStyle($this->labelColOptions, "text-align:{$this->hAlign};vertical-align:{$this->vAlign};");
        parent:: init();
        if (empty($this->container['id'])) {
            $this->container['id'] = $this->getId();
        }
        $this->initI18N();
        $this->template = strtr($this->template, [
            '<th>' => Html::beginTag('th', $this->labelColOptions),
            '<td>' => Html::beginTag('td', $this->valueColOptions)
        ]);
        Html::addCssClass($this->formOptions, 'kv-detail-view-form');
        $this->formOptions['fieldConfig']['template'] = "{input}\n{hint}\n{error}";
        $this->_form = ActiveForm::begin($this->formOptions);
        $this->registerAssets();
    }

    public function initI18N()
    {
        Yii::setAlias('@kvdetail', dirname(__FILE__));
        if (empty($this->i18n)) {
            $this->i18n = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@kvdetail/messages',
                'forceTranslation' => true
            ];
        }
        Yii::$app->i18n->translations['kvdetail'] = $this->i18n;
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
        $output = strtr($this->mainTemplate, [
            '{detail}' => '<div id="' . $this->container['id'] . '">' . $output . '</div>'
        ]);
        echo strtr($output, [
            '{buttons}' => '<span class="kv-buttons-1">' . $this->renderButtons(1) .
                '</span> <span class="kv-buttons-2 kv-hide">' . $this->renderButtons(2) . '</span>'
        ]);
        ActiveForm::end();
    }

    /**
     * Renders a single attribute.
     *
     * @param array $attribute the specification of the attribute to be rendered.
     * @param integer $index the zero-based index of the attribute in the [[attributes]] array
     * @return string the rendering result
     */
    protected function renderAttribute($attribute, $index)
    {
        $dispAttr = $this->formatter->format($attribute['value'], $attribute['format']);
        $output = '<div class="kv-attribute">' . $dispAttr . "</div>\n";
        if ($this->enableEditMode) {
            $editInput = (!empty($attribute['displayOnly']) && $attribute['displayOnly']) ? $dispAttr : $this->renderFormAttribute($attribute);
            $output .= '<div class="kv-form-attribute kv-hide">' . $editInput . '</div>';
        }
        if (is_string($this->template)) {
            return strtr($this->template, [
                '{label}' => $attribute['label'],
                '{value}' => $output
            ]);
        } else {
            return call_user_func($this->template, $attribute, $index, $this);
        }
    }

    /**
     * @return string the detail view content
     */
    protected function renderDetailView()
    {
        $rows = [];
        $i = 0;
        foreach ($this->attributes as $attribute) {
            $rows[] = $this->renderAttribute($attribute, $i++);
        }
        $tag = ArrayHelper::remove($this->options, 'tag', 'table');
        $output = Html::tag($tag, implode("\n", $rows), $this->options);
        return ($this->bootstrap && $this->responsive) ?
            '<div class="table-responsive">' . $output . '</div>' :
            $output;
    }

    /**
     * Check if attribute name is valid
     *
     * @param mixed $attribute
     * @throws \yii\base\InvalidConfigException
     */
    protected static function validateAttribute($attribute)
    {
        if (is_array($attribute) && !empty($attribute['updateAttr'])) {
            $attrib = $attribute['updateAttr'];
            if (ctype_alnum(str_replace('_', '', $attrib))) {
                return;
            } else {
                throw new InvalidConfigException("The 'updateAttr' name '{$attrib}' is invalid.");
            }
        }
        $attrib = is_string($attribute) ? $attribute : (empty($attribute['attribute']) ? '' : $attribute['attribute']);
        if (strpos($attrib, '.') > 0) {
            throw new InvalidConfigException("The attribute '{$attrib}' is invalid. You cannot directly pass relational " .
                "attributes in string format within '\kartik\widgets\DetailView'. Instead use the array format with 'attribute' " .
                "property set to base field, and the 'value' property returning the relational data.");
        }
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
        $fieldConfig = ArrayHelper::getValue($config, 'fieldConfig', []);
        $inputWidth = ArrayHelper::getValue($config, 'inputWidth', '');
        if ($inputWidth != '') {
            $template = ArrayHelper::getValue($fieldConfig, 'template', "{input}\n{error}\n{hint}");
            $fieldConfig['template'] = "<div style='width:{$inputWidth};'>{$template}</div>";
        }
        if ($input !== self::INPUT_WIDGET && !in_array($input, self::$_inputsList) && !in_array($input, self::$_inputWidgets)) {
            throw new InvalidConfigException("Invalid input type '{$input}' defined for the attribute '" . $config['attribute'] . "'.");
        }
        $options = ArrayHelper::getValue($config, 'options', []);
        $widgetOptions = ArrayHelper::getValue($config, 'widgetOptions', []);
        $class = ArrayHelper::remove($widgetOptions, 'class', '');
        if (!empty($config['options'])) {
            $widgetOptions['options'] = $config['options'];
        }
        if (in_array($input, self::$_inputWidgets)) {
            $class = $input;
            return $this->_form->field($this->model, $attr, $fieldConfig)->widget($class, $widgetOptions);
        }
        if ($input === self::INPUT_WIDGET) {
            if ($class == '') {
                throw new InvalidConfigException("Widget class not defined in 'widgetOptions' for {$input}'.");
            }
            return $this->_form->field($this->model, $attr, $fieldConfig)->widget($class, $widgetOptions);
        }
        if (in_array($input, self::$_dropDownInputs)) {
            $items = ArrayHelper::getValue($config, 'items', []);
            return $this->_form->field($this->model, $attr, $fieldConfig)->$input($items, $options);
        }
        return $this->_form->field($this->model, $attr, $fieldConfig)->$input($options);
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
        return strtr($this->$buttons, [
            '{view}' => $this->renderButton('view'),
            '{update}' => $this->renderButton('update'),
            '{delete}' => $this->renderButton('delete'),
            '{save}' => $this->renderButton('save'),
        ]);
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
        $panel['heading'] = '<div class="pull-right">{buttons}</div>' .
            ArrayHelper::getValue($panel, 'heading', '');
        $panel['preBody'] = $content;
        return Html::panel($panel, $type);
    }

    /**
     * Register assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        DetailViewAsset::register($view);
        $options = ['fadeDelay' => $this->fadeDelay];
        if ($this->enableEditMode) {
            $options['mode'] = $this->mode;
            $view->registerJs('$("#' . $this->container['id'] . '").kvDetailView(' . Json::encode($options) . ');');
        }
    }

    /**
     * Gets the default button
     *
     * @param string $type the button type
     * @return string
     */
    protected function getDefaultButton($type, $label, $title, $options)
    {
        $btnStyle = empty($this->panel['type']) ? self::TYPE_DEFAULT : $this->panel['type'];
        $label = ArrayHelper::remove($options, 'label', $label);
        if (empty($options['class'])) {
            $options['class'] = 'btn btn-xs btn-' . $btnStyle;
        }
        Html::addCssClass($options, 'kv-btn-' . $type);
        $options = ArrayHelper::merge(['title' => Yii::t('kvdetail', $title)], $options);
        if ($type !== 'delete' && $type !== 'save') {
            $options['type'] = 'button';
            return Html::button($label, $options);
        } elseif ($type === 'delete') {
            $url = ArrayHelper::remove($options, 'url', '#');
            $options = ArrayHelper::merge([
                'data-method' => 'post',
                'data-confirm' => Yii::t('kvdetail', 'Are you sure you want to delete this item?')
            ], $options);
            return Html::a($label, $url, $options);
        } else {
            return Html::submitButton($label, $options);
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
            return $this->getDefaultButton('view', '<span class="glyphicon glyphicon-eye-open"></span>', 'View', $this->viewOptions);
        }
        if ($type === 'update') {
            return $this->getDefaultButton('update', '<span class="glyphicon glyphicon-pencil"></span>', 'Update', $this->updateOptions);
        }
        if ($type === 'delete') {
            return $this->getDefaultButton('delete', '<span class="glyphicon glyphicon-trash"></span>', 'Delete', $this->deleteOptions);
        }
        if ($type === 'save') {
            return $this->getDefaultButton('save', '<span class="glyphicon glyphicon-floppy-disk"></span>', 'Save', $this->saveOptions);
        }
    }
}
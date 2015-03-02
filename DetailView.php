<?php

/**
 * @package   yii2-detail-view
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   1.7.0
 */

namespace kartik\detail;

use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use kartik\base\Config;
use kartik\helpers\Html;

/**
 * Enhances the Yii DetailView widget with various options to include Bootstrap
 * specific styling enhancements. Also allows to simply disable Bootstrap styling
 * by setting `bootstrap` to false. In addition, it allows you to directly edit
 * the detail grid data using a form.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class DetailView extends \yii\widgets\DetailView
{
    use \kartik\base\TranslationTrait;

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

    const INPUT_TEXT = 'textInput';
    const INPUT_PASSWORD = 'passwordInput';
    const INPUT_TEXTAREA = 'textArea';
    const INPUT_CHECKBOX = 'checkbox';

    /**
     * Edit input types
     */
    // input types
    const INPUT_HIDDEN = 'hiddenInput';
    const INPUT_RADIO = 'radio';
    const INPUT_LIST_BOX = 'listBox';
    const INPUT_DROPDOWN_LIST = 'dropDownList';
    const INPUT_CHECKBOX_LIST = 'checkboxList';
    const INPUT_RADIO_LIST = 'radioList';
    const INPUT_FILE = 'fileInput';
    const INPUT_HTML5_INPUT = 'input';
    const INPUT_WIDGET = 'widget';
    const INPUT_DEPDROP = '\kartik\depdrop\DepDrop';
    const INPUT_SELECT2 = '\kartik\select2\Select2';
    const INPUT_TYPEAHEAD = '\kartik\typeahead\Typeahead';
    const INPUT_SWITCH = '\kartik\switchinput\SwitchInput';

    // input widget classes
    const INPUT_SPIN = '\kartik\touchspin\TouchSpin';
    const INPUT_RATING = '\kartik\widgets\StarRating';
    const INPUT_RANGE = '\kartik\range\RangeInput';
    const INPUT_COLOR = '\kartik\color\ColorInput';
    const INPUT_FILEINPUT = '\kartik\file\FileInput';
    const INPUT_DATE = '\kartik\date\DatePicker';
    const INPUT_TIME = '\kartik\time\TimePicker';
    const INPUT_DATETIME = '\kartik\datetime\DateTimePicker';
    const INPUT_DATE_RANGE = '\kartik\daterange\DateRangePicker';
    const INPUT_SORTABLE = '\kartik\sortinput\SortableInput';
    const INPUT_SLIDER = '\kartik\slider\Slider';
    const INPUT_MONEY = '\kartik\money\MaskMoney';
    const INPUT_CHECKBOX_X = '\kartik\checkbox\CheckboxX';
    private static $_inputsList = [
        self::INPUT_HIDDEN => 'hiddenInput',
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
    private static $_dropDownInputs = [
        self::INPUT_LIST_BOX => 'listBox',
        self::INPUT_DROPDOWN_LIST => 'dropDownList',
        self::INPUT_CHECKBOX_LIST => 'checkboxList',
        self::INPUT_RADIO_LIST => 'radioList',
    ];
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
     * @var bool whether to hide rows in view mode if value is null or empty.
     */
    public $hideIfEmpty = false;

    /**
     * @var bool whether to display bootstrap style tooltips for titles on hover of buttons
     */
    public $tooltips = true;

    /**
     * @var array a list of attributes to be displayed in the detail view. Each
     * array element represents the specification for displaying one particular
     * attribute.
     *
     * An attribute can be specified as a string in the format of "attribute",
     * "attribute:format" or "attribute:format:label", where "attribute" refers
     * to the attribute name, and "format" represents the format of the attribute.
     * The "format" is passed to the [[Formatter::format()]] method to format an
     * attribute value into a displayable text. Please refer to [[Formatter]] for
     * the supported types. Both "format" and "label" are optional. They will take
     * default values if absent.
     *
     * An attribute can also be specified in terms of an array with the following
     * elements:
     *
     * - attribute: the attribute name. This is required if either "label" or
     *   "value" is not specified.
     * - label: the label associated with the attribute. If this is not specified,
     *   it will be generated from the attribute name.
     * - value: the value to be displayed. If this is not specified, it will be
     *   retrieved from [[model]] using the attribute name by calling
     *   [[ArrayHelper::getValue()]]. Note that this value will be formatted into
     *   a displayable text according to the "format" option.
     * - format: the type of the value that determines how the value would be
     *   formatted into a displayable text. Please refer to [[Formatter]] for
     *   supported types.
     * - visible: whether the attribute is visible. If set to `false`, the
     *   attribute will NOT be displayed.
     *
     * Additional special settings are:
     * - rowOptions: array, HTML attributes for the row (if not set, will default
     *   to the `rowOptions` set at the widget level)
     * - labelColOptions: array, HTML attributes for the label column (if not set, will default
     *   to the `labelColOptions` set at the widget level)
     * - valueColOptions: array, HTML attributes for the value column (if not set, will default
     *   to the `valueColOptions` set at the widget level)
     * - group: bool, whether to group the selection by merging the label and value into a single column.
     * - groupOptions: array, HTML attributes for the grouped/merged column when `group` is set to `true`.
     * - type: string, the input type for rendering the attribute in edit mode.
     *   Must be one of the [[DetailView::::INPUT_]] constants.
     * - displayOnly: boolean, if the input is to be set to as `display only`
     *   in edit mode.
     * - widgetOptions: array, the widget options if you set `type` to
     *   [[DetailView::::INPUT_WIDGET]]. The following special options are recognized:
     *   - `class`: string the fully namespaced widget class.
     * - items: array, the list of data items  for dropDownList, listBox,
     *   checkboxList & radioList
     * - inputType: string, the HTML 5 input type if `type` is set to
     *   [[DetailView::::INPUT_HTML 5]].
     * - inputWidth: string, the width of the container holding the input,
     *   should be appended along with the width unit (`px` or `%`)
     * - fieldConfig: array, optional, the Active field configuration.
     * - options: array, optional, the HTML attributes for the input.
     * - updateAttr: string, optional, the name of the attribute to be updated,
     *   when in edit mode. This will default to the the `attribute` setting.
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
     * - `heading`: string | boolean, the panel heading title value. If set to false, 
     *   the entire heading will be not displayed. Note that the `{title}` tag in the 
     *   `headingOptions['template']` will be replaced with this value.
     * - `headingOptions`: array, the HTML attributes for the panel heading. Defaults
     *   to `['class'=>'panel-title']`. The following additional options are available:
     *   - `tag`: string, the tag to render the heading. Defaults to `h3`.
     *   - `template`: string, the template to render the heading. Defaults to `{buttons}{title}`,
     *      where: 
     *      - `{title}` will be replaced with the `heading` value, and 
     *      -`{buttons}` will be replaced by the rendered buttons.
     * - `type`: string, the panel contextual type (one of the TYPE constants,
     *    if not set will default to `default` or `self::TYPE_DEFAULT`),
     * - `footer`: string | boolean, the panel footer title value. Defaults to `false`. If set to false, 
     *   the entire footer will be not displayed. Note that the `{title}` tag in the 
     *   `footerOptions['template']` will be replaced with this value.
     * - `footerOptions`: array, the HTML attributes for the panel footer. Defaults
     *   to `['class'=>'panel-title']`. The following additional options are available:
     *   - `tag`: string, the tag to render the footer. Defaults to `h4`.
     *   - `template`: string, the template to render the footer. Defaults to `{title}`,
     *      where: 
     *      - `{title}` will be replaced with the `footer`, and 
     *      -`{buttons}` will be replaced by the rendered buttons.
     */
    public $panel = [];

    /**
     * @var string the main template to render the detail view. The following
     * tags will be replaced:
     * - `{detail}`: will be replaced by the rendered detail view
     * - `{buttons}`: the buttons to be displayed as set in `buttons1` and
     *    `buttons2`.
     */
    public $mainTemplate = "{detail}";
    
    /**
     * @var array the options for the button toolbar container
     */
    public $buttonContainer = ['class'=>'pull-right'];

    /**
     * @var string the buttons to show when in view mode. The following
     * tags will be replaced:
     * - `{view}`: the view button
     * - `{update}`: the update button
     * - `{delete}`: the delete button
     * - `{save}`: the save button
     * Defaults to `{edit} {delete}`.
     */
    public $buttons1 = '{update} {delete}';

    /**
     * @var string the buttons template to show when in edit mode. The
     * following tags will be replaced:
     * - `{view}`: the view button
     * - `{update}`: the update button
     * - `{reset}`: the reset button
     * - `{delete}`: the delete button
     * - `{save}`: the save button
     * Defaults to `{view} {save}`.
     */
    public $buttons2 = '{view} {reset} {save}';

    /**
     * @var array the HTML attributes for the container displaying the
     * VIEW mode attributes.
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
     * @var array the HTML attributes for the reset button. This button will reset the form in edit mode.
     * The following special options are recognized:
     * - `label`: the reset button label. This will not be HTML encoded.
     *    Defaults to '<span class="glyphicon glyphicon-ban-circle"></span>'.
     */
    public $resetOptions = [];

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
     * @var array the the internalization configuration for this widget
     */
    public $i18n = [];

    /**
     * @var string translation message file category name for i18n
     */
    protected $_msgCat = 'kvdetail';

    /**
     * @var ActiveForm the form instance
     */
    protected $_form;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->validateAttributes();
        Html::addCssClass($this->options, 'detail-view');
        $this->validateDisplay();
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
        $this->initI18N(__DIR__);
        $this->template = Html::beginTag('tr', $this->rowOptions) . "\n" .
            Html::beginTag('th', $this->labelColOptions) . "\n{label}</th>\n" .
            Html::beginTag('td', $this->valueColOptions) . "\n{label}</td>\n" .
            "</tr>";
        Html::addCssClass($this->formOptions, 'kv-detail-view-form');
        $this->formOptions['fieldConfig']['template'] = "{input}\n{hint}\n{error}";
        $this->_form = ActiveForm::begin($this->formOptions);
        $this->registerAssets();
    }

    /**
     * Validate and parse attributes
     *
     * @param mixed $attribute the attribute name
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function validateAttributes()
    {
        foreach ($this->attributes as $key => $attribute) {
            if (is_array($attribute) && ArrayHelper::getValue($attribute, 'group', false) === true) {
                $this->attributes[$key]['value'] = '';
            }
            if (is_array($attribute) && !empty($attribute['updateAttr'])) {
                $attrib = $attribute['updateAttr'];
                if (ctype_alnum(str_replace('_', '', $attrib))) {
                    return;
                } else {
                    throw new InvalidConfigException("The 'updateAttr' name '{$attrib}' is invalid.");
                }
            }
            $attrib = is_string($attribute) ? $attribute :
                (empty($attribute['attribute']) ? '' : $attribute['attribute']);
            if (strpos($attrib, '.') > 0) {
                throw new InvalidConfigException(
                    "The attribute '$attrib' is invalid. You cannot directly pass relational " .
                    "attributes in string format within '\kartik\widgets\DetailView'. Instead " .
                    "use the array format with 'attribute' property set to base field, and the " .
                    "'value' property returning the relational data."
                );
            }
        }
    }

    /**
     * Validates the display of correct attributes and buttons
     * at initialization based on mode
     *
     * @return void
     */
    protected function validateDisplay()
    {
        $none = 'display:none';
        if (count($this->model->getErrors()) > 0) {
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
     * Register assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        DetailViewAsset::register($view);
        $options = ['fadeDelay' => $this->fadeDelay];
        $id = 'jQuery("#' . $this->container['id'] . '")';
        if ($this->enableEditMode) {
            $options['mode'] = $this->mode;
            $view->registerJs($id . '.kvDetailView(' . Json::encode($options) . ');');
        }
        if ($this->tooltips) {
            $view->registerJs($id . '.find("[data-toggle=tooltip]").tooltip();');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $output = $this->renderDetailView();
        if (is_array($this->panel) && !empty($this->panel) && $this->panel !== false) {
            $output = $this->renderPanel($output);
        }
        $output = strtr(
            $this->mainTemplate,
            ['{detail}' => Html::tag('div', $output, $this->container)]
        );
        Html::addCssClass($this->viewButtonsContainer, 'kv-buttons-1');
        Html::addCssClass($this->editButtonsContainer, 'kv-buttons-2');
        $buttons = Html::tag('span', $this->renderButtons(1), $this->viewButtonsContainer) .
                    Html::tag('span', $this->renderButtons(2), $this->editButtonsContainer);
        echo str_replace('{buttons}', Html::tag('div', $buttons, $this->buttonContainer), $output);
        ActiveForm::end();
    }

    /**
     * Renders the main detail view widget
     *
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
     * Renders a single attribute.
     *
     * @param array $attribute the specification of the attribute to be rendered.
     * @param int   $index the zero-based index of the attribute in the [[attributes]] array
     *
     * @return string the rendering result
     */
    protected function renderAttribute($attribute, $index)
    {
        $rowOptions = ArrayHelper::getValue($attribute, 'rowOptions', $this->rowOptions);
        $labelColOptions = ArrayHelper::getValue($attribute, 'labelColOptions', $this->labelColOptions);
        $valueColOptions = ArrayHelper::getValue($attribute, 'valueColOptions', $this->valueColOptions);
        if (ArrayHelper::getValue($attribute, 'group', false) === true) {
            $groupOptions = ArrayHelper::getValue($attribute, 'groupOptions', []);
            $label = ArrayHelper::getValue($attribute, 'label', '');
            if (empty($groupOptions['colspan'])) {
                $groupOptions['colspan'] = 2;
            }
            return Html::tag('tr', Html::tag('th', $label, $groupOptions), $rowOptions);
        }
        if ($this->hideIfEmpty === true && empty($attribute['value'])) {
            Html::addCssClass($rowOptions, 'kv-view-hidden');
        }
        if (ArrayHelper::getValue($attribute, 'type', 'text') === self::INPUT_HIDDEN) {
            Html::addCssClass($rowOptions, 'kv-edit-hidden');
        }
        $dispAttr = $this->formatter->format($attribute['value'], $attribute['format']);
        Html::addCssClass($this->viewAttributeContainer, 'kv-attribute');
        Html::addCssClass($this->editAttributeContainer, 'kv-form-attribute');
        $output = Html::tag('div', $dispAttr, $this->viewAttributeContainer) . "\n";
        if ($this->enableEditMode) {
            $editInput = !empty($attribute['displayOnly']) && $attribute['displayOnly'] ? 
                $dispAttr : 
                $this->renderFormAttribute($attribute);
            $output .= Html::tag('div', $editInput, $this->editAttributeContainer);
        }
        return Html::beginTag('tr', $rowOptions) . "\n" .
            Html::beginTag('th', $labelColOptions) . $attribute['label'] . "</th>\n" .
            Html::beginTag('td', $valueColOptions) . $output . "</td>\n</tr>";
    }

    /**
     * Renders each form attribute
     *
     * @param array $config the attribute config
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    protected function renderFormAttribute($config)
    {
        if (empty($config['attribute'])) {
            return '';
        }
        $attr = ArrayHelper::getValue($config, 'updateAttr', $config['attribute']);
        $input = ArrayHelper::getValue($config, 'type', self::INPUT_TEXT);
        $fieldConfig = ArrayHelper::getValue($config, 'fieldConfig', []);
        $inputWidth = ArrayHelper::getValue($config, 'inputWidth', '');
        if ($inputWidth != '') {
            $template = ArrayHelper::getValue($fieldConfig, 'template', "{input}\n{error}\n{hint}");
            $fieldConfig['template'] = "<div style='width:{$inputWidth};'>{$template}</div>";
        }
        if (substr($input, 0, 8) == "\\kartik\\") {
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

        if ($input == self::INPUT_HTML5_INPUT) {
            $inputType = ArrayHelper::getValue($config, 'inputType', self::INPUT_TEXT);
            return $this->_form->field($this->model, $attr, $fieldConfig)->$input($inputType, $options);
        }

        return $this->_form->field($this->model, $attr, $fieldConfig)->$input($options);
    }

    /**
     * Returns the bootstrap panel
     *
     * @param string $content
     *
     * @return string
     */
    protected function renderPanel($content)
    {
        $panel = $this->panel;
        $type = ArrayHelper::remove($panel, 'type', self::TYPE_DEFAULT);
        if (($heading = $this->renderPanelTitleBar('heading')) !== false) {
            $panel['heading'] = $heading;
        }
        if (($footer = $this->renderPanelTitleBar('footer')) !== false) {
            $panel['footer'] = $footer;
        }
        $panel['preBody'] = $content;
        return Html::panel($panel, $type);
    }

    /**
     * Renders the panel title bar
     *
     * @param string $type whether 'heading' or 'footer'
     * @return string | boolean
     */
    protected function renderPanelTitleBar($type)
    {
        $title = ArrayHelper::getValue($this->panel, $type, ($type === 'heading' ? '' : false));
        if ($title === false) {
            return false;
        }
        $tag = ArrayHelper::remove($options, 'tag', ($type === 'heading' ? 'h3' : 'h4'));
        $template = ArrayHelper::remove($options, 'template', ($type === 'heading' ? '{buttons}{title}' : '{title}'));
        $options = ArrayHelper::getValue($this->panel, $type . 'Options', []);
        Html::addCssClass($options, 'panel-title');
        $title = Html::tag($tag, $title, $options);
        return str_replace('{title}', $title, $template);
    }

    /**
     * Renders the buttons for a specific mode
     *
     * @param integer $mode
     *
     * @return string the buttons content
     */
    protected function renderButtons($mode = 1)
    {
        $buttons = "buttons{$mode}";
        return strtr(
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
     * Renders a button
     *
     * @param string $type the button type
     *
     * @return string
     */
    protected function renderButton($type)
    {
        if (!$this->enableEditMode) {
            return '';
        }
        switch ($type) {
            case 'view':
                return $this->getDefaultButton('view', 'eye-open', Yii::t('kvdetail', 'View'));
            case 'update':
                return $this->getDefaultButton('update', 'pencil', Yii::t('kvdetail', 'Update'));
            case 'delete':
                return $this->getDefaultButton('delete', 'trash', Yii::t('kvdetail', 'Delete'));
            case 'save':
                return $this->getDefaultButton('save', 'floppy-disk', Yii::t('kvdetail', 'Save'));
            case 'reset':
                return $this->getDefaultButton('reset', 'ban-circle', Yii::t('kvdetail', 'Cancel Changes'));
            default:
                return '';
        }
    }

    /**
     * Gets the default button
     *
     * @param string $type the button type
     * @param string $icon the glyphicon icon suffix name
     * @param string $title the title to display on hover
     *
     * @return string
     */
    protected function getDefaultButton($type, $icon, $title)
    {
        $buttonOptions = $type . 'Options';
        $options = $this->$buttonOptions;
        $btnStyle = empty($this->panel['type']) ? self::TYPE_DEFAULT : $this->panel['type'];
        $isEmpty = empty($options);
        $label = ArrayHelper::remove($options, 'label', "<i class='glyphicon glyphicon-{$icon}'></i>");
        if (empty($options['class'])) {
            $options['class'] = 'btn btn-xs btn-' . $btnStyle;
        }
        Html::addCssClass($options, 'kv-btn-' . $type);
        $options = ArrayHelper::merge(['title' => $title], $options);
        if ($this->tooltips) {
            $options['data-toggle'] = 'tooltip';
        }
        if ($type === 'reset') {
            return Html::resetButton($label, $options);
        } elseif ($type === 'save') {
            return Html::submitButton($label, $options);
        } elseif ($type === 'delete') {
            $url = ArrayHelper::remove($options, 'url', '#');
            if ($isEmpty) {
                $options = ArrayHelper::merge(
                    [
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('kvdetail', 'Are you sure you want to delete this item?')
                    ],
                    $options
                );
            }
            return Html::a($label, $url, $options);
        } else {
            $options['type'] = 'button';
            return Html::button($label, $options);
        } 
    }
}
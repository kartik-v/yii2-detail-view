yii2-detail-view
================

An extended Yii2 DetailView with many additional features. Extends the Yii DetailView to work in both VIEW and 
EDIT modes. Accelerates your development by using a single configuration of attributes for both VIEW and EDIT. The extension also 
includes easier methods to style your detail view widget cells, data, form inputs, widgets, and columns (more specifically for Bootstrap 3). 
The widget by default can be styled within a Bootstrap 3 panel with a buttons toolbar to toggle modes and control your data.
Refer [detailed documentation](http://demos.krajee.com/detail-view) and/or a [complete demo](http://demos.krajee.com/detail-view-demo).

> NOTE: This extension depends on the [kartik-v/yii2-widgets](https://github.com/kartik-v/yii2-widgets) and 
[kartik-v/yii2-helpers](https://github.com/kartik-v/yii2-helpers) extensions which in turn depends on the
[yiisoft/yii2-bootstrap](https://github.com/yiisoft/yii2/tree/master/extensions/bootstrap) extension. Check the 
[composer.json](https://github.com/kartik-v/yii2-detail-view/blob/master/composer.json) for this extension's requirements and dependencies. 
Note: Yii 2 framework is still in active development, and until a fully stable Yii2 release, your core yii2-bootstrap packages (and its dependencies) 
may be updated when you install or update this extension. You may need to lock your composer package versions for your specific app, and test 
for extension break if you do not wish to auto update dependencies.

### Latest Release
The latest version of the extension is release v1.1.0. Refer the [CHANGE LOG](https://github.com/kartik-v/yii2-detail-view/blob/master/CHANGE.md) for details of various releases.

### Demo
You can see detailed [documentation](http://demos.krajee.com/detail-view) and [demonstration](http://demos.krajee.com/detail-view-demo) on usage of the extension.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

> Note: You must set the `minimum-stability` to `dev` in the **composer.json** file in your application root folder before installation of this extension.

Either run

```
$ php composer.phar require kartik-v/yii2-detail-view "dev-master"
```

or add

```
"kartik-v/yii2-detail-view": "dev-master"
```

to the ```require``` section of your `composer.json` file.

## Usage
```php
use kartik\detail\DetailView;
echo DetailView::widget([
    'model'=>$model,
    'condensed'=>true,
    'hover'=>true,
    'mode'=>DetailView::MODE_VIEW,
    'panel'=>[
        'heading'=>'Book # ' . $model->id,
        'type'=>DetailView::PANEL_INFO,
    ],
    'attributes'=>[
        'code',
        'name',
        ['attribute'=>'publish_date', 'type'=>DetailView::INPUT_DATE],
    ]
]);
```

## License

**yii2-detail-view** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.

<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-grid
 * @version 1.0.0
 */

namespace kartik\detail;

use kartik\widgets\AssetBundle;

/**
 * Asset bundle for DetailView Widget
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DetailViewAsset extends AssetBundle
{

	public function init()
	{
		$this->setSourcePath(__DIR__ . '/../assets');
		$this->setupAssets('js', ['js/kv-detail-view']);
		$this->setupAssets('css', ['css/kv-detail-view']);
		parent::init();
	}

}
<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-detail-view
 * @version 1.4.0
 */

namespace kartik\detail;

/**
 * Asset bundle for DetailView Widget
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DetailViewAsset extends \kartik\base\AssetBundle
{
	public function init()
	{
		$this->setSourcePath(__DIR__ . '/assets');
		$this->setupAssets('js', ['js/kv-detail-view']);
		$this->setupAssets('css', ['css/kv-detail-view']);
		parent::init();
	}

}
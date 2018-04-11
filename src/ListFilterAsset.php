<?php
namespace kr0lik\listFilter;

use yii\web\{AssetBundle, JqueryAsset};
use yii\jui\JuiAsset;
use yii\bootstrap\{BootstrapAsset, BootstrapPluginAsset};
use kr0lik\juiTouchPunch\UiTouchPunchAsset;

class ListFilterAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';
    public $css = ['filter.css'];
    public $js = ['filter.js'];
    public $depends = [
        JqueryAsset::class,
        JuiAsset::class,
        UiTouchPunchAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
    ];
}

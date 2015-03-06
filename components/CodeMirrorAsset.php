<?php
namespace infinite\docHelper\components;

use yii\web\AssetBundle;

class CodeMirrorAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/codemirror';
    public $css = [
        'lib/codemirror.css',
        'theme/base16-dark.css',
        'addon/fold/foldgutter.css',
    ];
    public $js = [
        'lib/codemirror.js',
        'addon/fold/foldcode.js',
        'addon/fold/foldgutter.js',
        'addon/fold/brace-fold.js',
        'addon/fold/xml-fold.js',
        'addon/fold/markdown-fold.js',
        'addon/fold/comment-fold.js',
        'addon/edit/matchbrackets.js',
        'mode/php/php.js',
        'mode/htmlmixed/htmlmixed.js',
        'mode/clike/clike.js',
        'mode/css/css.js',
    ];
    public $depends = [
    ];
}

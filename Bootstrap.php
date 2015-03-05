<?php

namespace infinite\docHelper;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->setModule('docHelper', ['class' => Module::className()]);
        $module = $app->getModule('deferredAction');
    }
}

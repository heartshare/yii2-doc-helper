<?php
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$asset = infinite\docHelper\components\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php
NavBar::begin([
    'brandLabel' => 'Code Documentation Helper',
    'brandUrl' => ['default/index'],
    'options' => ['class' => 'navbar-inverse navbar-fixed-top'],
]);
echo Nav::widget([
    'options' => ['class' => 'nav navbar-nav navbar-right'],
    'items' => [
        ['label' => 'Resume', 'url' => ['default/resume'], 'visible' => !empty(Yii::$app->getModule('docHelper')->scan) && Yii::$app->getModule('docHelper')->scan->isScanned],
        ['label' => 'Create Project', 'url' => ['default/create-project']],
        ['label' => 'Start Scan', 'url' => ['default/start'], 'visible' => !empty(Yii::$app->getModule('docHelper')->scan)],
    ],
]);
NavBar::end();
?>

<div class="container">
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

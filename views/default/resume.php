<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Progress;
$percent = $scan->percentCompleted;
echo Progress::widget([
      'percent' => $percent,
      'label' => $percent.'% Complete',
      'barOptions' => ['class' => 'progress-bar-success'],
      'options' => ['class' => 'progress-striped']
 ]);
echo Html::tag('h3', $fileScan->title);


?>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

echo Html::tag('h2', 'Start Project');
$form = ActiveForm::begin(['id' => "create-project"]);
echo $form->field($model, 'directories');
echo $form->field($model, 'fileFilter');
echo $form->field($model, 'templateVariableQuery');
echo Html::tag('div', Html::submitButton('Start', ['name' => 'start', 'class' => 'btn btn-primary']), ['class' => 'form-group']);
ActiveForm::end();
?>
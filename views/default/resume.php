<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Progress;
$percent = $scan->percentCompleted;
$form = ActiveForm::begin(['id' => "fill-form"]);
//\d(unserialize(serialize($scan)));exit;
echo Progress::widget([
      'percent' => $percent,
      'label' => $percent.'% Complete ('. $scan->filledBlanks .'/'. $scan->totalBlanks.')',
      'barOptions' => ['class' => 'progress-bar-success'],
      'options' => ['class' => 'progress-striped']
 ]);
$this->title = $fileScan->title;
echo Html::tag('h3', $fileScan->title);
$asset = infinite\docHelper\components\CodeMirrorAsset::register($this);
echo Html::textarea('code', file_get_contents($fileScan->file), ['class' => 'codemirror infinite-dochelper-code-context',  'style' => 'height: 100px']);
echo '<br /><br />';
foreach ($fileScan->results as $segment) {
	echo Html::beginTag('div', ['class' => 'panel panel-default']);
	echo Html::beginTag('div', ['class' => 'panel-heading']);
	echo Html::tag('h3', $segment->title, ['class' => 'panel-title']);
	echo Html::endTag('div');
	echo Html::beginTag('div', ['class' => 'panel-body']);
	echo Html::beginTag('div', ['class' => 'infinite-dochelper-doc-block']);
	$lineNumber = $segment->startLine;
	foreach ($segment->contentArray as $line) {
		$lineKey = md5($lineNumber);
		echo Html::beginTag('div', ['class' => 'line']);
		if (isset($segment->scanResults[$lineKey])) {
			//echo count($segment->scanResults[$lineKey]) .': ';
			foreach ($segment->scanResults[$lineKey] as $matchId => $match) {
				$placeholder = trim($match, '[]');
				$placeholder = preg_replace('/(\@[^\s]+ )/', '', $placeholder);
				$inputName = '['. $fileScan->hash .']['. $segment->id .']['. $lineKey . ']['. $matchId .']';
				$inputValue = '';
				$options = ['class' => 'infinite-dochelper-blank form-control', 'placeholder' => $placeholder, 'title' => $placeholder];
				if (isset($scan->globalValues[$match])) {
					$inputValue = $scan->globalValues[$match];
					Html::addCssClass($options, 'global-value');
				}
				$options['data-autogrow'] = json_encode(['maxWidth' => 600, 'minWidth' => 300]);
				$globalElement = Html::checkbox('Global'.$inputName, false, ['title' => 'Make this value the default for '.$placeholder]);
				$inputElement = Html::input('text', 'Blank'.$inputName, $inputValue, $options);
				$replaceText = Html::beginTag('div', ['class'=>'input-group input-group-inline']);
				$replaceText .= $inputElement;
				$replaceText .= Html::tag('span', $globalElement, ['class' => 'input-group-addon']);
				$replaceText .= Html::endTag('div');
				$line = strtr($line, [$match => $replaceText]);
			}
		}
		echo $line;
		echo Html::endTag('div');
		$lineNumber++;
	}
	echo Html::endTag('div');
	echo Html::textarea('code', $segment->context->content, ['class' => 'codemirror infinite-dochelper-code-context',  'style' => 'height: 100px']);
	echo Html::endTag('div');
	echo Html::endTag('div');
}
echo Html::tag('div', Html::submitButton('Continue >>', ['name' => 'continue', 'class' => 'btn btn-primary']), ['class' => 'form-group']);
ActiveForm::end();
?>
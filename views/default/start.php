<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

echo Html::tag('h2', 'Scan Project');
$buttons = [];
$startButton = false;
if ($scan->isScanned) {
	echo Html::tag('h3', 'Scan Results');
	$scanResults = $scan->fileScans;
	if (empty($scanResults)) {
		echo Html::tag('div', 'No template matches were found in this project.', ['class' => ['alert alert-warning']]);
	} else {
		echo Html::beginTag('div', ['class' => 'list-group']);
		foreach ($scanResults as $fileScan) {
			$extra = Html::tag('span', $fileScan->foundCount, ['class' => 'badge']);
			echo Html::tag('div', $fileScan->file . $extra, ['class' => 'list-group-item']);
		}
		echo Html::endTag('div');
		$buttons[] = Html::a('Fill Template Variables', ['resume'], ['class' => 'btn btn-primary']);
	}
	$buttons[] = Html::a('Rescan', ['start', 'restart' => 1], ['class' => 'btn btn-warning']);
} else {
	if (!empty($_GET['scanning'])) {
		$scanned = $scan->scan(100);
		$this->registerMetaTag(['http-equiv' => 'refresh', 'content' => '3']);
		echo Html::tag('h4', 'Scanned ('.$scanned['remaining'].' remaining)');
		echo Html::beginTag('div', ['class' => 'list-group']);
		foreach ($scanned['files'] as $file) {
			echo Html::tag('div', $file, ['class' => 'list-group-item']);
		}
		echo Html::endTag('div');
	} else {
		echo Html::tag('h3', 'Files to Scan');
		echo Html::beginTag('div', ['class' => 'list-group']);
		foreach ($scan->files as $file) {
			echo Html::tag('div', $file, ['class' => 'list-group-item']);
		}
		echo Html::endTag('div');
		$buttons[] = Html::a('Start Scan', ['start', 'scanning' => 1], ['class' => 'btn btn-warning']);
	}
}
if (!empty($buttons)) {
	echo Html::beginTag('div', ['class' => 'btn-group btn-group-lg']);
	echo implode($buttons);
	echo Html::endTag('div');
}
?>
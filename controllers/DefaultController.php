<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace infinite\docHelper\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use infinite\docHelper\components\Scan;
use infinite\docHelper\components\Project;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DefaultController extends Controller
{
    public $layout = 'main';


    public function actionIndex()
    {
        $this->layout = 'main';
        if (($scan = Yii::$app->getModule('docHelper')->getScan())) {
            return $this->redirect(['resume']);
        } elseif (($project = Yii::$app->getModule('docHelper')->getLastProject())) {
            return $this->redirect(['start']);
        }
        $this->redirect(['create-project']);
    }

    public function actionResume()
    {
        $params = [];
        if (!($params['scan'] = Yii::$app->getModule('docHelper')->getScan())) {
            return $this->redirect(['start']);
        }
        if (empty($_GET['file']) || empty($params['scan']->fileScans[$_GET['file']]) || empty($params['scan']->fileScans[$_GET['file']]->results)) {
            $nextFile = $params['scan']->nextFileScan;
            if (!$nextFile) {
                return $this->render('done');
            }
            return $this->redirect(['resume', 'file' => $nextFile]);
        }
        $params['fileScan'] = $fileScan = $params['scan']->fileScans[$_GET['file']];
        if (!empty($_POST) && isset($_POST['Blank'][$fileScan->hash])) {
            foreach ($fileScan->results as $segment) {
                if (!isset($_POST['Blank'][$fileScan->hash][$segment->id])) {
                    \d("no segment post");
                    continue;
                }
                $globalSegmentPost = isset($_POST['Global'][$fileScan->hash][$segment->id]) ? $_POST['Global'][$fileScan->hash][$segment->id] : [];
                $segmentPost = $_POST['Blank'][$fileScan->hash][$segment->id];
                $lineNumber = $segment->startLine;
                foreach ($segment->contentArray as $line) {
                    $lineKey = md5($lineNumber);
                    if (!isset($segmentPost[$lineKey])) {
                        $lineNumber++;
                        continue;
                    }
                    $linePost = $segmentPost[$lineKey];
                    $globalLinePost = isset($globalSegmentPost[$lineKey]) ? $globalSegmentPost[$lineKey] : [];
                    if (isset($segment->scanResults[$lineKey])) {
                        foreach ($segment->scanResults[$lineKey] as $matchId => $match) {
                            if (!isset($linePost[$matchId])) {
                                continue;
                            }
                            $matchValue = $linePost[$matchId];
                            $globalValue = !empty($globalLinePost[$matchId]) ? true : false;
                            if (!$segment->resolveMatch($lineNumber, $lineKey, $matchId, $matchValue)) {
                                \d(['boom', $matchValue]);exit;
                            }
                        }
                    }
                    $lineNumber++;
                }
            }
            return $this->redirect(['resume']);
        }

        return $this->render('resume', $params);
    }

    public function actionStart()
    {
        $params = [];
        if (!($params['project'] = Yii::$app->getModule('docHelper')->getLastProject())) {
            return $this->redirect(['create-project']);
        }
        $params['scan'] = Yii::$app->getModule('docHelper')->getScan();
        if (empty($params['scan']) || !empty($_GET['restart'])) {
            $params['scan'] = Yii::$app->getModule('docHelper')->getScan(true);
            if (!empty($_GET['restart'])) {
                Yii::$app->getModule('docHelper')->saveScan($params['scan']);
                return $this->redirect(['start', 'scanning' => 1]);
            }
        }
        $rendered = $this->render('start', $params);
        Yii::$app->getModule('docHelper')->saveScan($params['scan']);
        return $rendered;
    }

    public function actionCreateProject()
    {
        $params = [];
        $params['model'] = new Project;
        if (($lastProject = Yii::$app->getModule('docHelper')->getLastProject())) {
            $params['model']->directories = $lastProject->directories;
            $params['model']->templateVariableQuery = $lastProject->templateVariableQuery;
            $params['model']->fileFilter = $lastProject->fileFilter;
        }
        if (!empty($_POST)) {
            $params['model']->load($_POST);
            if ($params['model']->validate()) {
                if (Yii::$app->getModule('docHelper')->saveNewProject($params['model'])) {
                    return $this->redirect(['start']);
                }
            }
        }

        return $this->render('create_project', $params);
    }

}

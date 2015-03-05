<?php
namespace infinite\docHelper;

use Yii;
use yii\base\Application;
use yii\base\Event;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use infinite\docHelper\components\Scan;
use infinite\docHelper\components\Project;

/**
 * Module [[@doctodo class_description:infinite\deferred\Module]].
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Module extends \yii\base\Module
{
    public $allowedIPs = ['127.0.0.1', '::1', '*'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $app = Yii::$app;
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                $this->id => $this->id . '/default/index',
                $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>' => $this->id . '/<controller>/<action>',
            ], false);
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (Yii::$app instanceof \yii\web\Application && !$this->checkAccess()) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }

        return true;
    }

    /**
     * @return boolean whether the module can be accessed by the current user
     */
    protected function checkAccess()
    {
        $ip = Yii::$app->getRequest()->getUserIP();
        foreach ($this->allowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        Yii::warning('Access to Gii is denied due to IP address restriction. The requested IP is ' . $ip, __METHOD__);

        return false;
    }

    public function getScan($refresh = false)
    {
        if ($refresh && $this->lastProject) {
            $scan = new Scan;
            $scan->project = $this->lastProject;
            $scan->files;
            file_put_contents($this->getScanPath(), serialize($scan));
            return $scan;
        }
        if (file_exists($this->getScanPath())) {
            return unserialize(file_get_contents($this->getScanPath()));
        }
        return false;
    }

    public function saveScan(Scan $scan)
    {
        if ($scan === false) {
            unlink($this->scanPath);
            return true;
        }
        file_put_contents($this->scanPath, serialize($scan));
        return file_exists($this->scanPath);
    }

    public function saveNewProject(Project $project)
    {
        file_put_contents($this->lastProjectPath, serialize($project));
        if (file_exists($this->getScanPath())) {
            unlink($this->getScanPath());
        }
        return file_exists($this->lastProjectPath);
    }

    public function getLastProject($refresh = false)
    {
        if (file_exists($this->getLastProjectPath())) {
            return unserialize(file_get_contents($this->getLastProjectPath()));
        }
        return false;
    }

    protected function getScanPath()
    {
        return Yii::getAlias('@runtime/doc_helper_scan');
    }

    protected function getLastProjectPath()
    {
        return Yii::getAlias('@runtime/doc_helper_project');
    }
}

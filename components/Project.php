<?php
namespace infinite\docHelper\components;

use Yii;
use yii\base\Model;

class Project extends Model
{
	public $directories = '';
	public $fileFilter = '*.php';
    public $templateVariableQuery = '/(\[\[\@doctodo [^\]]+\]\])/';
    

    public function attributeLabels()
    {
        return [
        	'directories' => 'Directory Aliases (comma separated)',
        	'fileFilter' => 'File Filters (comma separated)',
        	'templateVariableQuery' => 'Template Regular Expression',
        ];
    }

    public function rules()
    {
    	return [
    		[['directories', 'templateVariableQuery'], 'required'],
    		[['fileFilter'], 'safe'],
    	];
    }

    public function attributes()
    {
    	return array_keys($this->attributeLabels());
    }

    public function getFileFilterArray()
    {
    	return explode(',', $this->fileFilter);
    }

    public function getDirectoryArray()
    {
    	$directoryArray = explode(',', $this->directories);
    	$directories = [];
    	if (empty($directoryArray)) {
    		return [];
    	}
    	foreach ($directoryArray as $directory) {
    		$directories[] = Yii::getAlias($directory);
    	}
        return $directories;
    }

}

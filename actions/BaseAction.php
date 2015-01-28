<?php

/**
* @link https://github.com/gilek/yii-gtreetable
* @copyright Copyright (c) 2015 Maciej Kłak
* @license https://github.com/gilek/yii-gtreetable/blob/master/LICENSE
*/

abstract class BaseAction extends CAction 
{
    public $treeModelName;
    
    public function getNodeById($id, $with = array()) {
        $model = CActiveRecord::model($this->treeModelName)->with($with)->findByPk($id);

        if ($model === null) {
            throw new CHttpException(404, Yii::t('gtreetable', 'Position is not exists!'));
        }
        return $model;
    }    
}

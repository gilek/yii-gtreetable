<?php

/*
 * @author Maciej "Gilek" Kłak
 * @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
 * @version 2.0.0-alpha
 * @package yii-gtreetable
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

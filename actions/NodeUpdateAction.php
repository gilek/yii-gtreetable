<?php
/*
* @author Maciej "Gilek" Kłak
* @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
* @version 1.1a
* @package Yii-GTreeTable
*/
class NodeUpdateAction extends CAction {
    public $treeModelName;
    public $access;

    public function run($id) {
        if ($this->access!==null)
            if (!Yii::app()->user->checkAccess($this->access))
                throw new CHttpException(403);  
          
        $model = CActiveRecord::model($this->treeModelName)->with()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, Yii::t('gtreetable','Position is not exists!'));
          
        $model->scenario = 'update';
        $model->attributes = $_POST;        

        if (!$model->validate())             
            throw new CHttpException(500,current(current($model->getErrors())));
        
        try {
            if (!$model->saveNode(false))
                    throw new CDbException(Yii::t('gtreetable','Update operation `{name}` failed!',array('{name}'=>CHtml::encode((string)$model))));   

        } catch(CException $e) {
            throw new CHttpException(500,$e->getMessage());
        }   
    }
}
?>
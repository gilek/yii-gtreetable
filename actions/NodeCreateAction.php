<?php
/*
* @author Maciej "Gilek" Kłak
* @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
* @version 1.1a
* @package Yii-GTreeTable
*/
class NodeCreateAction extends CAction {
    public $treeModelName;
    public $access;

    public function run() {
        if ($this->access!==null)
            if (!Yii::app()->user->checkAccess($this->access))
                throw new CHttpException(403);  
            
        $model = new $this->treeModelName('create');
        $model->attributes = $_POST;

        if (!$model->validate())             
            throw new CHttpException(500,current(current($model->getErrors())));
            
        if (!($model->parentRel instanceof $this->treeModelName))
            throw new CHttpException(404,Yii::t('gtreetable','Position indicated by parent ID is not exists!'));

        try {
            if (!$model->appendTo($model->parentRel))
                throw new CDbException(Yii::t('gtreetable','Adding operation `{name}` failed!',array('{name}'=>CHtml::encode((string)$model))));
            
            echo CJavaScript::jsonEncode(array('id'=>$model->getPrimaryKey()));

        } catch(CException $e) {
            throw new CHttpException(500,$e->getMessage());
        }   
    }
}
?>
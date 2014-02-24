<?php
/*
* @author Maciej "Gilek" Kłak
* @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
* @version 1.1a
* @package Yii-GTreeTable
*/
class NodeDeleteAction extends CAction {
    public $treeModelName;
    public $access;    
    public $depending = array();

    public function run($id) {
        if ($this->access!==null)
            if (!Yii::app()->user->checkAccess($this->access))
                throw new CHttpException(403);          
        
        $depending = array_keys($this->depending);    
        $model = CActiveRecord::model($this->treeModelName)->with($depending)->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, Yii::t('gtreetable','Position is not exists!'));
        
        if ($model->type==$model::TYPE_ROOT)
            throw new CHttpException(500, Yii::t('gtreetable','Main element can`t be deleted!'));            
        
        $nodes = $model->descendants()->with($depending)->findAll();
        $nodes[] = $model;
        
        $trans = $model->dbConnection->beginTransaction();
        try {        
            foreach($nodes as $node) {
                foreach((array)$this->depending as $rel=>$message) 
                    if ($node->$rel > 0)
                        throw new CHttpException(400,  str_replace ('{count}', $node->$rel, $message));           
            }    
            if (!$model->deleteNode()) 
                throw new CDbException(Yii::t('gtreetable','Deleting operation `{name}` failed!',array('{name}'=>CHtml::encode((string)$model))));               
            
            $trans->commit();
        } catch(CException $e) {
            $trans->rollBack();
            throw new CHttpException(500,$e->getMessage());
        }         
    }
}
?>

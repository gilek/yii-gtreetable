<?php
/*
* @author Maciej "Gilek" Kłak
* @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
* @version 1.1a
* @package Yii-GTreeTable
*/
class NodeChildrenAction extends CAction {
    public $treeModelName;
    public $access;

    public function run($id) {
        if ($this->access!==null)
            if (!Yii::app()->user->checkAccess($this->access))
                throw new CHttpException(403);  
        
        $root = ($id==0) ? true : false;
        $model = new $this->treeModelName();
        $criteria = new CDbCriteria();
        if ($root) {
            $criteria->compare('type',$model::TYPE_ROOT);
            $criteria->limit = 1;
        } else 
            $criteria->compare('id',$id);
        
        
        $parent = $model->find($criteria);
        if ($parent===null)
            throw new CHttpException(404,Yii::t('gtreetable','Position indicated by parent ID is not exists!'));

        $nodes = $root ? array($parent) : $parent->children()->findAll();
        $result = array();

        foreach($nodes as $node) {
            $result[] = array(
                'id'=>$node->id,
                'name'=>$node->name,
                'level'=>$node->level
            );
        }
        echo CJSON::encode($result);
    }
}
?>

<?php

/*
 * @author Maciej "Gilek" Kłak
 * @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
 * @version 2.0.0-alpha
 * @package yii-gtreetable
 */

Yii::import('ext.gtreetable.actions.BaseAction');

class NodeChildrenAction extends BaseAction
{
      public function run($id) {
        $nodes = array();
        
        if ((integer)$id === 0) {
            $nodes = CActiveRecord::model($this->treeModelName)->roots()->findAll();
        } else {
            $parent = $this->getNodeById($id);
            if ($parent === null) {
                throw new CHttpException(404, Yii::t('gtreetable', 'Position indicated by parent ID is not exists!'));
            }
            $nodes = $parent->children()->findAll();
        }
        $result = array();
        foreach ($nodes as $node) {
            $result[] = array(
                'id' => $node->getPrimaryKey(),
                'name' => $node->getName(),
                'level' => $node->getLevel(),
                'type' => $node->getType()
            );
        }
        echo CJSON::encode($result);
    }
}


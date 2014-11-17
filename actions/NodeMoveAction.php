<?php

/*
 * @author Maciej "Gilek" Kłak
 * @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
 * @version 2.0.0-alpha
 * @package yii-gtreetable
 */

Yii::import('ext.gtreetable.actions.BaseAction');
Yii::import('ext.gtreetable.models.BaseModel');

class NodeMoveAction extends BaseAction {

    public function run($id) {
        $model = $this->getNodeById($id);
        $model->scenario = 'move';
        $model->attributes = $_POST;

        if (!$model->validate()) {
            throw new CHttpException(500, current(current($model->getErrors())));
        }

        if (!($model->relatedNode instanceof $this->treeModelName)) {
            throw new CHttpException(404, Yii::t('gtreetable', 'Position indicated by related ID is not exists!'));
        }

        try {
            $action = $this->getMoveAction($model);
            if (!call_user_func(array($model, $action), $model->relatedNode)) {
                throw new CDbException(Yii::t('gtreetable', 'Moving operation `{name}` failed!', array('{name}' => Html::encode((string) $model))));
            }
                          
            echo CJSON::encode(array(
                'id' => $model->getPrimaryKey(),
                'name' => $model->getName(),
                'level' => $model->getLevel(),
                'type' => $model->getType()
            ));
        } catch (CException $e) {
            throw new CHttpException(500, $e->getMessage());
        }
    }

    protected function getMoveAction($model) {
        if ($model->relatedNode->isRoot() && $model->position !== BaseModel::POSITION_LAST_CHILD) {
            return 'moveAsRoot';
        } else if ($model->position === BaseModel::POSITION_BEFORE) {
            return 'moveBefore';
        } else if ($model->position === BaseModel::POSITION_AFTER) {
            return 'moveAfter';
        } else if ($model->position === BaseModel::POSITION_LAST_CHILD) {
            return 'moveAsLast';
        } else {
            throw new CHttpException(500, Yii::t('gtreetable', 'Unsupported move position!'));
        }
    }

}
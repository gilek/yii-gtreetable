<?php
/*
 * @author Maciej "Gilek" Kłak
 * @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
 * @version 2.0.0-alpha
 * @package yii-gtreetable
 */

Yii::import('ext.gtreetable.actions.BaseAction');
Yii::import('ext.gtreetable.models.BaseModel');

class NodeCreateAction extends BaseAction {

   public function run() {
        $model = new $this->treeModelName();
        $model->scenario = 'create';
        $model->attributes = $_POST;

        $isRootNode = !(integer)$model->parent > 0;

        if (!$isRootNode && !($model->relatedNode instanceof $this->treeModelName)) {
            throw new CHttpException(404, Yii::t('gtreetable', 'Position indicated by related ID is not exists!'));
        }

        try {
            $action = $isRootNode ? 'saveNode' : $this->getInsertAction($model);
            if (!call_user_func(array($model, $action), $model->relatedNode)) {
                throw new CDbException(Yii::t('gtreetable', 'Adding operation `{name}` failed!', array('{name}' => Html::encode((string) $model))));
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

    protected function getInsertAction($model) {
        if ($model->position === BaseModel::POSITION_BEFORE) {
            return 'insertBefore';
        } else if ($model->position === BaseModel::POSITION_AFTER) {
            return 'insertAfter';
        } else if ($model->position === BaseModel::POSITION_FIRST_CHILD) {
            return 'prependTo';
        } else if ($model->position === BaseModel::POSITION_LAST_CHILD) {
            return 'appendTo';
        } else {
            throw new CHttpException(500, Yii::t('gtreetable', 'Unsupported insert position!'));
        }
    } 
}

<?php

/*
 * @author Maciej "Gilek" Kłak
 * @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
 * @version 2.0.0-alpha
 * @package yii-gtreetable
 */

Yii::import('ext.gtreetable.actions.BaseAction');

class NodeUpdateAction extends BaseAction
{

    public function run($id)
    {
        $model = $this->getNodeById($id);
        $model->scenario = 'update';
        $model->attributes = $_POST;

        if (!$model->validate()) {
            throw new CHttpException(500, current(current($model->getErrors())));
        }

        try {
            if ($model->saveNode(false) === false) {
                throw new CDbException(Yii::t('gtreetable', 'Update operation `{name}` failed!', array('{name}' => Html::encode((string) $model))));
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
}

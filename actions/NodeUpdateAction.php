<?php

/**
* @link https://github.com/gilek/yii-gtreetable
* @copyright Copyright (c) 2015 Maciej Kłak
* @license https://github.com/gilek/yii-gtreetable/blob/master/LICENSE
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

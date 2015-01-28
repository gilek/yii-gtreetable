<?php

/**
* @link https://github.com/gilek/yii-gtreetable
* @copyright Copyright (c) 2015 Maciej Kłak
* @license https://github.com/gilek/yii-gtreetable/blob/master/LICENSE
*/

Yii::import('ext.gtreetable.actions.BaseAction');

class NodeDeleteAction extends BaseAction
{
    
    public function run($id) {
        $model = $this->getNodeById($id);

        if ($model->isRoot() && (integer) $model->roots()->count() === 1) {
            throw new CHttpException(500, Yii::t('gtreetable', 'Main element can`t be deleted!'));
        }

        $nodes = $model->descendants()->findAll();
        $nodes[] = $model;

        $trans = $model->dbConnection->beginTransaction();
        try {
            if (!$model->deleteNode()) {
                throw new CDbException(Yii::t('gtreetable', 'Deleting operation `{name}` failed!', array('{name}' => Html::encode((string) $model))));
            }

            $trans->commit();
            return true;
        } catch (CException $e) {
            $trans->rollBack();
            throw new CHttpException(500, $e->getMessage());
        }
    }    
}

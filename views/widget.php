<?php

/**
* @link https://github.com/gilek/yii-gtreetable
* @copyright Copyright (c) 2015 Maciej Kłak
* @license https://github.com/gilek/yii-gtreetable/blob/master/LICENSE
*/

Yii::import('ext.gtreetable.helpers.LocaleHelper');

if (!isset($routes)) {
    $routes = array();
}

$controller = (!isset($controller)) ? '' : $controller.'/';

$routes = array_merge(array(
    'nodeChildren' => $controller.'nodeChildren',
    'nodeCreate' => $controller.'nodeCreate',
    'nodeUpdate' => $controller.'nodeUpdate',
    'nodeDelete' => $controller.'nodeDelete',
    'nodeMove' => $controller.'nodeMove'
),$routes);

$defaultOptions = array(
    'source' => new CJavaScriptExpression("function (id) {
        return {
            type: 'GET',
            url: '".$this->createUrl($routes['nodeChildren'])."',
            data: { 'id': id },        
            dataType: 'json',
            error: function(XMLHttpRequest) {
                alert(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
            }
        };
    }"),
    'onSave' => new CJavaScriptExpression("function (oNode) {
        return {
            type: 'POST',
            url: !oNode.isSaved() ? '".$this->createUrl($routes['nodeCreate'])."' : URI('".$this->createUrl($routes['nodeUpdate'])."').addSearch({'id':oNode.getId()}),
            data: {
                parent: oNode.getParent(),
                name: oNode.getName(),
                position: oNode.getInsertPosition(),
                related: oNode.getRelatedNodeId()
            },
            dataType: 'json',
            error: function(XMLHttpRequest) {
                alert(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
            }
        };        
    }"),
    'onDelete' => new CJavaScriptExpression("function(oNode) {
        return {
            type: 'POST',
            url: URI('".$this->createUrl($routes['nodeDelete'])."').addSearch({'id':oNode.getId()}),
            dataType: 'json',
            error: function(XMLHttpRequest) {
                alert(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
            }
        };        
    }"),
    'onMove' => new CJavaScriptExpression("function(oSource, oDestination, position) {
        return {
            type: 'POST',
            url: URI('".$this->createUrl($routes['nodeMove'])."').addSearch({'id':oSource.getId()}),
            data: {
                related: oDestination.getId(),
                position: position
            },
            dataType: 'json',
            error: function(XMLHttpRequest) {
                alert(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
            }
        };        
    }"),
    'language' => LocaleHelper::normalize(Yii::app()->language),
    'rootLevel' => 1
);

$options = !isset($options) ? $defaultOptions : array_merge($defaultOptions, $options);
    
$widget = $this->createWidget('ext.gtreetable.EGTreeTable',array(
    'options'=> $options,
));
$widget->run();

Yii::app()->clientScript->registerScriptFile($widget->baseScriptUrl . '/URIjs/src/URI.'. (YII_DEBUG ? 'min.' : '') .'js');
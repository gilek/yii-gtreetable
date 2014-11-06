<?php

/*
 * @author Maciej "Gilek" Kłak
 * @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
 * @version 2.0.0-alpha
 * @package yii-gtreetable
 */

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
        return URI('".$this->createUrl($routes['nodeChildren'])."').addSearch({'id':id});
    }"),
    'onSave' => new CJavaScriptExpression("function (oNode) {
        return jQuery.ajax({
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
        });        
    }"),
    'onDelete' => new CJavaScriptExpression("function(oNode) {
        return jQuery.ajax({
            type: 'POST',
            url: URI('".$this->createUrl($routes['nodeDelete'])."').addSearch({'id':oNode.getId()}),
            dataType: 'json',
            error: function(XMLHttpRequest) {
                alert(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
            }
        });        
    }"),
    'onMove' => new CJavaScriptExpression("function(oSource, oDestination, position) {
        return jQuery.ajax({
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
        });        
    }")
);



$options = !isset($options) ? $defaultOptions : array_merge($defaultOptions, $options);
    
$widget = $this->createWidget('ext.gtreetable.EGTreeTable',array(
    'options'=> $options,
));
$widget->run();

Yii::app()->clientScript->registerScriptFile($widget->baseScriptUrl . '/URIjs/src/URI.'. (YII_DEBUG ? 'min.' : '') .'js');
<?php
return [
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'v1/video',
        'patterns' => [
            'POST upload' => 'upload',
            'GET' => 'list',
            'GET {id}' => 'view',
            'GET {id}/download' => 'download',
            'DELETE {id}' => 'delete'
        ]
    ],
];
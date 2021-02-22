<#1>
<?php
$fields = [
    'field_id' => [
        'type' => 'integer',
        'length' => 4,
        'notnull' => true
    ],
    'option' => [
        'type' => 'text',
        'length' => 250,
        'notnull' => false
    ]
];

$ilDB->createTable("udf_plugin_ms", $fields);
$ilDB->addPrimaryKey("udf_plugin_ms", ['field_id', 'option']);
?>
<#2>
<?php
$fields = [
    'field_id' => [
        'type' => 'integer',
        'length' => 4,
        'notnull' => true
    ],
    'user_id' => [
        'type' => 'integer',
        'length' => 4,
        'notnull' => true
    ],
    'option' => [
        'type' => 'text',
        'length' => 250,
        'notnull' => false
    ]
];

$ilDB->createTable("udf_plugin_ms_value", $fields);
$ilDB->addPrimaryKey("udf_plugin_ms", ['field_id', 'user_id','option']);
?>

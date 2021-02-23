<#1>
<?php
$fields = [
    'usr_id' => [
        'type' => ilDBConstants::T_INTEGER,
        'length' => 4,
        'notnull' => true
    ],
    'license_id' => [
        'type' => ilDBConstants::T_TEXT,
        'length' => 250,
        'notnull' => true
    ],
    'license_type' => [
        'type' => ilDBConstants::T_TEXT,
        'length' => 250,
        'notnull' => true
    ],
    'valid_from' => [
        'type' => ilDBConstants::T_DATE,
        'notnull' => false
    ],
    'valid_till' => [
        'type' => ilDBConstants::T_DATE,
        'notnull' => false
    ],
    'reg_nr' => [
        'type' => ilDBConstants::T_TEXT,
        'length' => 250,
        'notnull' => false
    ],
    'is_primary' => [
        'type' => ilDBConstants::T_INTEGER,
        'length' => 1,
        'notnull' => true
    ]
];

$ilDB->createTable("udf_license_types", $fields);
$ilDB->addPrimaryKey("udf_license_types", ['usr_id', 'license_id']);
?>

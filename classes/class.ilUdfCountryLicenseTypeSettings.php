<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

class ilUdfCountryLicenseTypeSettings
{
    /**
     * @var string
     */
    private const SETTING_NAME = 'udf_pl_clt';

    /**
     * @var ilSetting
     */
    private $settings;

    /**
     * @var int
     */
    private $primary_definition_id;

    /**
     * @var int
     */
    private $secondary_definition_id;


    public function __construct()
    {
        $this->settings = new ilSetting(self::SETTING_NAME);
        $this->init();
    }

    /**
     * @return int
     */
    public function getSecondaryDefinitionId() : int
    {
        return $this->secondary_definition_id;
    }

    /**
     * @param int $secondary_definition_id
     */
    public function setSecondaryDefinitionId(int $secondary_definition_id)
    {
        $this->secondary_definition_id = $secondary_definition_id;
    }


    /**
     * @return int
     */
    public function getPrimaryDefinitionId() : int
    {
        return $this->primary_definition_id;
    }

    /**
     * @param int $primary_definition_id
     */
    public function setPrimaryDefinitionId(int $primary_definition_id)
    {
        $this->primary_definition_id = $primary_definition_id;
    }

    /**
     * init settings
     */
    private function init()
    {
        $this->primary_definition_id = $this->settings->get('primary_definition_id', 0);
        $this->secondary_definition_id = $this->settings->get('secondary_definition_id', 0);
    }
}

<?php

class ilUdfCountryLicenseTypeXmlImporter
{
    /**
     * @var array
     */
    private $definition = [];

    /**
     * @var
     */
    private $user_id;

    /**
     * @var string
     */
    private $xml;

    /**
     * @var ilUdfCountryLicenseTypeSettings
     */
    private $settings;

    /**
     * @var ilLogger
     */
    private $logger;

    public function __construct(array $definition, int $user_id, string $xml)
    {
        global $DIC;

        $this->settings   = new ilUdfCountryLicenseTypeSettings();
        $this->definition = $definition;
        $this->user_id    = $user_id;
        $this->xml        = $xml;

        $this->logger = $DIC->logger()->usr();
    }

    /**
     * throws Exception
     */
    public function import()
    {
        $root = simplexml_load_string($this->xml);
        if ($this->isPrimaryDefinition($root)) {
            $this->updatePrimaryDefinition($root);
        } elseif ($this->isSecondaryDefinition($root)) {
            $this->updateSecondaryDefinition($root);
        }
    }

    /**
     * @param SimpleXMLElement $root
     * @return bool
     */
    protected function isPrimaryDefinition(SimpleXMLElement $root)
    {
        $field_id = $this->definition['field_id'];
        if ($field_id != $this->settings->getPrimaryDefinitionId()) {
            $this->logger->info('Ignoring xml definition for non primary type');
            return false;
        }

        foreach ($root->LicenseType as $licenseType) {
            if ($licenseType['primary'] == 'true') {
                return true;
            }
        }
        $this->logger->warning('Could not find primary definition in xml: ' . $this->xml);
        return false;
    }

    /**
     * @param SimpleXMLElement $root
     * @return bool
     */
    protected function isSecondaryDefinition(SimpleXMLElement $root)
    {
        $field_id = $this->definition['field_id'];
        if ($field_id != $this->settings->getSecondaryDefinitionId()) {
            $this->logger->info('Ignoring xml definition for primary type');
            return false;
        }
        foreach ($root->LicenseType as $licenseType) {
            if ($licenseType['primary'] == 'true') {
                $this->logger->warning('xml definition contains primary type');
                return false;
            }
        }
        return true;
    }

    protected function updatePrimaryDefinition(SimpleXMLElement $root)
    {
        ilUdfCountryLicenseType::deleteForUserAndPrimaryType($this->user_id, true);
        foreach ($root->LicenseType as $licenseType) {
            $type = new ilUdfCountryLicenseType();
            $type->setUsrId($this->user_id);
            $type->setIsPrimary(true);
            $type->setLicenseId((string) $licenseType['id']);
            $type->setLicenseType((string) $licenseType->DisplayName);
            $type->setRegNr((string) $licenseType->RegistrationNumber);
            $type->save();
            return;
        }
    }

    /**
     * @param SimpleXMLElement $root
     * @return bool
     */
    protected function updateSecondaryDefinition(SimpleXMLElement $root)
    {
        ilUdfCountryLicenseType::deleteForUserAndPrimaryType($this->user_id, false);
        foreach ($root->LicenseType as $licenseType) {
            $type = new ilUdfCountryLicenseType();
            $type->setUsrId($this->user_id);
            $type->setIsPrimary(false);
            $type->setLicenseId((string) $licenseType['id']);
            $type->setLicenseType((string) $licenseType->DisplayName);
            $type->setRegNr((string) $licenseType->RegistrationNumber);
            $type->save();
        }
    }
}
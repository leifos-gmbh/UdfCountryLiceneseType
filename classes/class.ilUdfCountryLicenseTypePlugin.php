<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilUdfCountryLicenseTypePlugin extends ilUDFDefinitionPlugin
{
    private const CDATA_BEGIN = '![CDATA[';
    private const CDATA_END = ']]';

    /**
     * @var string
     */
    private const CLT_NAME = 'UdfCountryLicenseType';

    /**
     * @var int
     */
    private const CLT_TYPE_ID = 61;

    /**
     * @var string
     */
    private const FORM_DEFINITION_NAME = 'options';


    /**
     * @var ilUdfCountryLicenseTypePlugin
     */
    private static $instance = null;

    /**
     * Get singleton instance
     * @return ilUdfCountryLicenseTypePlugin
     */
    public static function getInstance()
    {
        global $DIC;

        $ilPluginAdmin = $DIC['ilPluginAdmin'];

        if (self::$instance) {
            return self::$instance;
        }
        return self::$instance = ilPluginAdmin::getPluginObject(
            self::UDF_C_TYPE,
            self::UDF_C_NAME,
            self::UDF_SLOT_ID,
            self::CLT_NAME
        );
    }

    /**
     * Auto load implementation
     * @param string class name
     */
    private final function autoLoad($a_classname)
    {
        $class_file = $this->getClassesDirectory() . '/class.' . $a_classname . '.php';
        if (file_exists($class_file)) {
            include_once($class_file);
        }
    }

    /**
     * Init auto load
     */
    protected function init()
    {
        $this->initAutoLoad();
    }

    /**
     * Init auto loader
     * @return void
     */
    protected function initAutoLoad()
    {
        spl_autoload_register(
            array($this, 'autoLoad')
        );
    }

    /**
     * Get plugin name
     * @return string
     */
    public function getPluginName()
    {
        return self::CLT_NAME;
	}

    /**
     * @return int
     */
    public function getDefinitionType()
    {
        return self::CLT_TYPE_ID;
    }

    public function getDefinitionTypeName()
    {
        return $this->txt('ms_type_name');
    }

    public function getDefinitionUpdateFormTitle()
    {
        return $this->txt('ms_form_update');
    }

    /**
     * Lookup user data
     * Values are store in udf_text => nothing todo here
     * @param int[] $a_user_ids
     * @param int[] $a_field_ids
     * @return array
     */
    public function lookupUserData($a_user_ids, $a_field_ids)
    {
        global $DIC;

        $settings = new ilUdfCountryLicenseTypeSettings();
        if (
            !in_array($settings->getPrimaryDefinitionId(), $a_field_ids) &&
            !in_array($settings->getSecondaryDefinitionId(), $a_field_ids)
        ) {
            return [];
        }

        $db = $DIC->database();

        $user_field_map = [];
        if (in_array($settings->getPrimaryDefinitionId(), $a_field_ids)) {
            $query = 'select usr_id, license_type, is_primary from udf_license_types ' .
                'where ' . $db->in('usr_id', $a_user_ids, false, ilDBConstants::T_INTEGER) . ' ' .
                'and is_primary = ' . $db->quote(1, ilDBConstants::T_INTEGER);
            $res = $db->query($query);
            while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
                $user_field_map[$row->usr_id][$settings->getPrimaryDefinitionId()] = $row->license_type;
            }
        }
        if (in_array($settings->getPrimaryDefinitionId(), $a_field_ids)) {
            $query = 'select usr_id, license_type, is_primary from udf_license_types ' .
                'where ' . $db->in('usr_id', $a_user_ids, false, ilDBConstants::T_INTEGER) . ' ' .
                'and is_primary = ' . $db->quote(0, ilDBConstants::T_INTEGER);
            $res = $db->query($query);
            $user_secondary_map = [];
            while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
                $user_secondary_map[$row->usr_id][] = $row->license_type;
            }
            foreach ($user_secondary_map as $usr_id => $license_types) {
                $user_field_map[$usr_id][$settings->getSecondaryDefinitionId()] = implode(', ', $license_types);
            }
        }
        return $user_field_map;
    }

    /**
     * @param \ilRadioOption $option
     * @param type           $field_id
     */
    public function addDefinitionTypeOptionsToRadioOption(\ilRadioOption $radio, $field_id)
    {
    }

    /**
     * Update from form
     */
    public function updateDefinitionFromForm(ilPropertyFormGUI $form, $a_field_id = 0)
    {
    }

    /**
     * Get form property for definition
     * Context: edit user; registration; edit user profile
     * @return ilFormPropertyGUI
     */
    public function getFormPropertyForDefinition($definition, $a_changeable = true, $a_default_value = null)
    {
        global $DIC;

        $field_id = $definition['field_id'];
        $name = $definition['field_name'];

        $nev = new ilNonEditableValueGUI($name, 'udf_' . $definition['field_id']);
        $nev->setValue($a_default_value);
        return $nev;
    }

    /**
     * @param $field_id
     * @return bool
     */
    public function usesCustomValueStorage() : bool
    {
        return true;
    }

    /**
     * @param array $definition
     * @param mixed $value
     */
    public function updateCustomValue(array $definition, int $user_id, $value)
    {
        $value = trim($value);
        if (substr($value,0,8) !== self::CDATA_BEGIN) {
            ilLoggerFactory::getLogger('usr')->debug('Update not from xml import');
            return;
        }
        $value = substr($value,8);
        $value = substr($value,0,-2);

        $importer = new ilUdfCountryLicenseTypeXmlImporter(
            $definition,
            $user_id,
            $value
        );
        try {
            $importer->import();
        } catch (Exception $e) {
            ilLoggerFactory::getLogger('usr')->error('Udf import failed with message: ' . $e->getMessage());
            ilLoggerFactory::getLogger('usr')->error('Trying to import xml: ' . $value);
        }
    }



}
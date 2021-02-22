<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilUdfCountryLicenseTypePlugin extends ilUDFDefinitionPlugin
{
    /**
     * @var string
     */
    private const MS_SELECT_NAME = 'UdfCountryLicenseType';

    /**
     * @var string
     */
    private const FORM_DEFINITION_NAME = 'options';

    /**
     * @var int
     */
    private const CLT_TYPE_ID = 52;

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
            self::CLT_TYPE_ID
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
        return self::CLT_TYPE_ID;
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
     * @todo try own data storage
     * Lookup user data
     * Values are store in udf_text => nothing todo here
     * @param int[] $a_user_ids
     * @param int[] $a_field_ids
     * @return array
     */
    public function lookupUserData($a_user_ids, $a_field_ids)
    {
        global $DIC;

        $db = $DIC->database();
        $query = 'select * from udf_text ' .
            'where  ' . $db->in('usr_id', $a_user_ids, false, ilDBConstants::T_INTEGER) . ' ' .
            'and field_id = ' . $db->quote($a_field_ids, ilDBConstants::T_INTEGER);
        $res = $db->query($query);
        $user_data = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $value = explode('~|~', $row->value);
            $user_data[$row->usr_id][$row->field_id] = $value;
        }
        return $user_data;
    }

    /**
     * @param \ilRadioOption $option
     * @param type           $field_id
     */
    public function addDefinitionTypeOptionsToRadioOption(\ilRadioOption $radio, $field_id)
    {
        $options = new ilTextInputGUI($this->txt('ms_entries'), self::FORM_DEFINITION_NAME);
        $options->setRequired(true);
        $options->setMulti(true);
        $options->setMaxLength(255);
        $radio->addSubItem($options);

        if ($field_id) {
            $field_options = ilUdfMultiSelectOptions::getInstanceByFieldId($field_id);
            if ($field_options->getOptions()) {
                $options->setMultiValues($field_options->getOptions());
                $options->setValue(array_shift($field_options->getOptions()));
            }
        }
    }

    /**
     * Update from form
     */
    public function updateDefinitionFromForm(ilPropertyFormGUI $form, $a_field_id = 0)
    {
        if (!$a_field_id) {
            return;
        }
        $field_options = ilUdfMultiSelectOptions::getInstanceByFieldId($a_field_id);
        $field_options->setOptions((array) $form->getInput(self::FORM_DEFINITION_NAME));
        $field_options->update();
    }

    /**
     * Get form property for definition
     * Context: edit user; registration; edit user profile
     * @return ilFormPropertyGUI
     */
    public function getFormPropertyForDefinition($definition, $a_changeable = true, $a_default_value = null)
    {
        global $DIC;

        $user_id = $DIC->user()->getId();

        $field_id = $definition['field_id'];
        $name = $definition['field_name'];

        $field_options = ilUdfMultiSelectOptions::getInstanceByFieldId($field_id);

        $checkbox_group = new ilCheckboxGroupInputGUI($name, 'udf_' . $field_id);
        $checkbox_group->setDisabled(!$a_changeable);

        if (strlen($a_default_value)) {
            $parsed_values = explode('~|~', $a_default_value);
            $checkbox_group->setValue($parsed_values);
        }
        foreach ($field_options->getOptions() as $key => $option) {
            $selection_item = new ilCheckboxOption($option, $option);
            $checkbox_group->addOption($selection_item);
        }
        return $checkbox_group;
    }

    /**
     * @param $field_id
     * @return bool
     */
    public function usesCustomValueStorage() : bool
    {
        return true;
    }

    public function updateCustomValue(array $definition, $user_id, $value)
    {
        global $DIC;

        $db = $DIC->database();
        $sep = '~|~';
        $values = [];
        foreach ((array) $value  as $single_value) {
            $values[] = $single_value;
        }
        $single_value_string = implode($sep, $values);

        $query = 'delete from udf_text ' .
            'where field_id  = ' . $db->quote($definition['field_id'], ilDBConstants::T_INTEGER) . ' ' .
            'and usr_id = ' . $db->quote($user_id, ilDBConstants::T_INTEGER);
        $db->manipulate($query);

        $query = 'insert into udf_text (usr_id, field_id, value) ' .
            'values ( ' .
            $db->quote($user_id, ilDBConstants::T_INTEGER) . ', ' .
            $db->quote($definition['field_id'], ilDBConstants::T_INTEGER) . ', ' .
            $db->quote($single_value_string, ilDBConstants::T_TEXT) . ' ' .
            ')';
        $db->manipulate($query);
    }
}

?>
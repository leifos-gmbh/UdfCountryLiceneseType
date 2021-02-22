<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilUdfMultiSelectOptions
{
    /**
     * @var ilUdfMultiSelectOptions[]
     */
    private static $instances = null;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var int
     */
    private $field_id;

    /**
     * @var string
     */
    private const TABLE_NAME = 'udf_plugin_ms';

    /**
     * @var ilDBInterface
     */
    private $db;

    /**
     * ilUdfMultiSelectOptions constructor.
     * @param int $field_id
     */
    public function __construct(int $field_id)
    {
        global $DIC;

        $this->field_id = $field_id;
        $this->db = $DIC->database();

        $this->read();
    }

    /**
     * @param int $field_id
     * @return ilUdfMultiSelectOptions
     */
    public static function getInstanceByFieldId(int $field_id)
    {
        if (isset(self::$instances[$field_id])) {
            return self::$instances[$field_id];
        }
        return self::$instances[$field_id] = new self($field_id);
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Update options
     */
    public function update()
    {
        $this->delete();

        foreach ($this->getOptions() as $key => $option) {
            $query = 'insert into ' . self::TABLE_NAME . ' (field_id, option ) ' .
                'values ( ' .
                $this->db->quote($this->field_id, ilDBConstants::T_INTEGER) . ', ' .
                $this->db->quote($option, ilDBConstants::T_TEXT) . ' ' .
                ')';
            $this->db->manipulate($query);
        }
    }

    public function delete()
    {
        $query = 'delete from ' . self::TABLE_NAME . ' ' .
            'where field_id = ' . $this->db->quote($this->field_id, ilDBConstants::T_INTEGER);
        $this->db->manipulate($query);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @throws ilDatabaseException
     */
    protected function read()
    {
        $query = 'select * from ' . self::TABLE_NAME . ' ' .
            'where field_id = ' . $this->db->quote($this->field_id, ilDBConstants::T_INTEGER);
        $res = $this->db->query($query);
        $this->options = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $this->options[] = $row->option;
        }
    }

}

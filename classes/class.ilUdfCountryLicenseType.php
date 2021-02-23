<?php

class ilUdfCountryLicenseType
{
    public const TABLE_NAME = 'udf_license_types';

    /**
     * @var int
     */
    private $usr_id;


    /**
     * @var string
     */
    private $license_id;

    /**
     * @var string
     */
    private $license_type;

    /**
     * @var ?ilDate
     */
    private $valid_from;

    /**
     * @var ?ilDate
     */
    private $valid_till;

    /**
     * @var string
     */
    private $reg_nr;

    /**
     * @var bool
     */
    private $is_primary;

    /**
     * @var ilDBInterface
     */
    private $db;

    /**
     * ilUdfCountryLicenseType constructor.
     */
    public function __construct()
    {
        global $DIC;

        $this->db = $DIC->database();
    }
    /**
     * @return int
     */
    public function getUsrId() : int
    {
        return $this->usr_id;
    }

    /**
     * @param int $usr_id
     */
    public function setUsrId(int $usr_id) : void
    {
        $this->usr_id = $usr_id;
    }

    /**
     * @return string
     */
    public function getLicenseId() : string
    {
        return $this->license_id;
    }

    /**
     * @param string $license_id
     */
    public function setLicenseId(string $license_id) : void
    {
        $this->license_id = $license_id;
    }

    /**
     * @return string
     */
    public function getLicenseType() : string
    {
        return $this->license_type;
    }

    /**
     * @param string $license_type
     */
    public function setLicenseType(string $license_type) : void
    {
        $this->license_type = $license_type;
    }

    /**
     * @return mixed
     */
    public function getValidFrom() : ?ilDate
    {
        return $this->valid_from;
    }

    /**
     * @param mixed $valid_from
     */
    public function setValidFrom(?ilDate $valid_from) : void
    {
        $this->valid_from = $valid_from;
    }

    /**
     * @return mixed
     */
    public function getValidTill() : ?ilDate
    {
        return $this->valid_till;
    }

    /**
     * @param mixed $valid_till
     */
    public function setValidTill(?ilDate $valid_till) : void
    {
        $this->valid_till = $valid_till;
    }

    /**
     * @return string
     */
    public function getRegNr() : string
    {
        return $this->reg_nr;
    }

    /**
     * @param string $reg_nr
     */
    public function setRegNr(string $reg_nr) : void
    {
        $this->reg_nr = $reg_nr;
    }

    /**
     * @return bool
     */
    public function isIsPrimary() : bool
    {
        return $this->is_primary;
    }

    /**
     * @param bool $is_primary
     */
    public function setIsPrimary(bool $is_primary) : void
    {
        $this->is_primary = $is_primary;
    }

    /**
     * @param int  $usr_id
     * @param bool $is_primary
     */
    public static function deleteForUserAndPrimaryType(int $usr_id, bool $is_primary)
    {
        global $DIC;

        $db = $DIC->database();
        $query = 'delete from ' . self::TABLE_NAME . ' ' .
            'where usr_id = ' . $db->quote($usr_id, ilDBConstants::T_INTEGER) . ' ' .
            'and is_primary = ' . $db->quote((int) $is_primary, ilDBConstants::T_INTEGER);
        $db->manipulate($query);
    }

    /**
     *
     */
    public function save()
    {
        $query = 'insert into ' . self::TABLE_NAME . ' (usr_id,license_id,license_type,valid_from,valid_till,reg_nr,is_primary) ' .
            'values ( ' .
            $this->db->quote($this->getUsrId(), ilDBConstants::T_INTEGER) . ', ' .
            $this->db->quote($this->getLicenseId(), ilDBConstants::T_TEXT) . ',  ' .
            $this->db->quote($this->getLicenseType(), ilDBConstants::T_TEXT) . ', ' .
            $this->db->quote(null, ilDBConstants::T_DATE) . ', ' .
            $this->db->quote(null, ilDBConstants::T_DATE) . ',  ' .
            $this->db->quote($this->getRegNr(), ilDBConstants::T_TEXT) . ', ' .
            $this->db->quote($this->isIsPrimary(), ilDBConstants::T_INTEGER) .
            ')';
        $this->db->manipulate($query);

    }
}
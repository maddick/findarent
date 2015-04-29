<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/23/15
 * Time: 9:17 PM
 */

abstract class Custom_AbstractCriteria
{
    protected $_validationErrors = array();
    protected $_criteriaValue;

    /**
     * @var array
     * @deprecated since values are now considered separate and no longer transformed
     */
    protected $_transformedCriteria = array();

    /**
     * Creates a new criteria object give the $criteriaValue then immediately validates the criteria
     *
     * @param $criteriaValue
     */
    public function __construct( $criteriaValue )
    {
        $this->_criteriaValue = $criteriaValue;
        $this->_validate();
    }

    abstract protected function _validate();

    /**
     * @return mixed the criteria's value
     */
    public function getCriteria()
    {
        return $this->_criteriaValue;
    }

    /**
     * @return array of validation errors
     */
    public function getValidationErrors()
    {
        return $this->_validationErrors;
    }

    /**
     * @return bool true if no validation errors are present and false otherwise
     */
    public function isValid()
    {
        return empty( $this->_validationErrors );
    }
}
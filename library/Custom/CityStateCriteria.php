<?php

/**
 * Class Custom_CityStateCriteria
 *
 * This class is responsible for the validation of city/state combinations.
 */
class Custom_CityStateCriteria extends Custom_AbstractCriteria
{
    /**
     * @var string contains the city value
     */
    protected $_city;

    /**
     * @var string contains the state value
     */
    protected $_state;

    public function __construct($cityState)
    {
        parent::__construct($cityState);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'cityState was unspecified';
        } else {
            $cityStateRegEx = '/^((?:\b[a-zA-Z]+\b\s?)+),?\s?([a-zA-Z]{2})$/';
            $cityStateValidator = new Zend_Validate_Regex( array( 'pattern' => $cityStateRegEx) );
            if ( !$cityStateValidator->isValid( $this->_criteriaValue ) ) {
                $this->_validationErrors[] = 'not a valid city/state value';
            } else {
                preg_match( $cityStateRegEx, $this->_criteriaValue, $regExArray );
                $this->_city = trim( $regExArray[1] );
                $this->_state = trim( $regExArray[2] );
            }
        }
    }

    /**
     * @return string|false city value or false
     */
    public function getCity()
    {
        return ( $this->isValid() ) ? $this->_city : false;
    }

    /**
     * @return string|false state value or false
     */
    public function getState()
    {
        return ( $this->isValid() ) ? $this->_state : false;
    }
}
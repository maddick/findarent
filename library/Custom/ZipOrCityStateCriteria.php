<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/23/15
 * Time: 9:23 PM
 */

/**
 * Class Custom_ZipOrCityStateCriteria
 * @deprecated since transformations are no longer done and the values are being passed separately
 */
class Custom_ZipOrCityStateCriteria extends Custom_AbstractCriteria
{
    /**
     * This criteria is responsible for the validation and transformation of the
     * zipOrCityState parameter. Transformed values will be stored in the $_transformedCriteria
     * array for later. Given the value of $zipOrCityState, the $_transformedCriteria will have zipCode
     * key containing the zip code or a city and a state key containing the city and state values respectively
     *
     * @param $zipOrCityState
     */
    public function __construct($zipOrCityState)
    {
        parent::__construct($zipOrCityState);
    }

    /**
     * This is the validation method for zipOrCityState. Once verified as valid, the
     * $_criteriaValue is then transformed into either zip or city and state and those
     * values are stored in the $_transformedCriteria array for later use.
     */
    protected function _validate()
    {
        //check if a value was passed at all
        if ( empty($this->_criteriaValue) ) {
            $this->_validationErrors[] = 'zipOrCityState was unspecified';
        } else {
            //check for a valid zip or city / state with regex (both are accepted)
            $zipCityStateRegEx = '/^(\d{5})$|^((?:\b[a-zA-Z]+\b\s?)+),?\s?([a-zA-Z]{2})$/';
            $zipCityStateValidator = new Zend_Validate_Regex( array( 'pattern' => $zipCityStateRegEx) );
            if ( !$zipCityStateValidator->isValid( $this->_criteriaValue) ) {
                $this->_validationErrors[] = 'Not a valid zip or city/state combination';
            } else {
                //determine if we are dealing with a zip or city / state and put
                //any transformed values into the $_transformedCriteria array
                preg_match( $zipCityStateRegEx, $this->_criteriaValue, $matches );
                if ( strlen( $matches[1] ) !== 0 ) {
                    $this->_transformedCriteria['zipCode'] = $matches[1];
                } else {
                    $this->_transformedCriteria['city'] = $matches[2];
                    $this->_transformedCriteria['state'] = $matches[3];
                }
            }
        }
    }

    public function isCityState() {
        return ( array_key_exists( 'city', $this->_transformedCriteria ) and
                 array_key_exists( 'state', $this->_transformedCriteria ) );
    }

    public function isZipCode() {
        return array_key_exists( 'zipCode', $this->_transformedCriteria );
    }
}
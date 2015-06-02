<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/23/15
 * Time: 9:23 PM
 */

/**
 * Class Custom_AutocompleteCriteria
 */
class Custom_AutocompleteCriteria extends Custom_AbstractCriteria
{
    protected $_city;

    protected $_state;

    protected $_zipCode;

    /**
     * This criteria is responsible for the validation and transformation of the autocomplete
     * parameters. Data is expected to be either a zip code or a city / state.
     *
     * @param $zipOrCityState
     */
    public function __construct($zipOrCityState)
    {
        parent::__construct($zipOrCityState);
    }

    /**
     * This is the validation method for autocomplete data. Once verified as valid, the
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
            $zipCityStateRegEx = '/^(\d{1,5})$|^((?:[a-zA-Z]+\s?)+),?\s?([a-zA-Z]{1,2})?$/';
            $zipCityStateValidator = new Zend_Validate_Regex( array( 'pattern' => $zipCityStateRegEx) );
            if ( !$zipCityStateValidator->isValid( $this->_criteriaValue) ) {
                $this->_validationErrors[] = 'Not a valid zip or city/state combination';
            } else {
                //parse our input and store the discovered values
                preg_match( $zipCityStateRegEx, $this->_criteriaValue, $matches );

                print_r($matches);

                if ( count( $matches ) === 2 ) {
                    $this->_zipCode = $matches[1];
                }

                if ( count( $matches ) > 2 ) {
                    $this->_city = $matches[2];
                    if ( count( $matches ) === 4 ) {
                        $this->_state = $matches[3];
                    }
                }
            }
        }
    }

    public function hasCity()
    {
        return isset( $this->_city );
    }

    public function hasState()
    {
        return isset( $this->_state );
    }

    public function hasZipCode()
    {
        return isset( $this->_zipCode );
    }

    public function getCity()
    {
        return $this->_city;
    }

    public function getState()
    {
        return $this->_state;
    }

    public function getZipCode()
    {
        return $this->_zipCode;
    }
}
<?php

/**
 * Class Custom_ZipCriteria
 *
 * This class is responsible for the validation of zip code criteria
 */
class Custom_ZipCodeCriteria extends Custom_AbstractCriteria
{

    public function __construct($zipCode)
    {
        parent::__construct($zipCode);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'zip was unspecified';
        } else {
            $zipCodeRegEx = '/^(\d{5})$/';
            $zipCodeValidator = new Zend_Validate_Regex( array( 'pattern' => $zipCodeRegEx) );
            if ( !$zipCodeValidator->isValid( $this->_criteriaValue ) ) {
                $this->_validationErrors[] = 'Not a valid zip code';
            }
        }
    }
}
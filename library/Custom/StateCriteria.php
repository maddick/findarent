<?php

class Custom_StateCriteria extends Custom_AbstractCriteria
{
    public function __construct($state)
    {
        parent::__construct($state);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'state was unspecified';
        } else {
            $stateRegEx = '/^[a-zA-Z]{2}$/';
            $stateValidator = new Zend_Validate_Regex( array( 'pattern' => $stateRegEx ) );
            if ( !$stateValidator->isValid($this->_criteriaValue) ) {
                $this->_validationErrors[] = 'not a valid state value';
            }
        }
    }
}
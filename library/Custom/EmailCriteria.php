<?php

class Custom_EmailCriteria extends Custom_AbstractCriteria
{
    public function __construct($emailAddress)
    {
        parent::__construct($emailAddress);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'emailAddress was unspecified';
        } else {
            $validator = new Zend_Validate_EmailAddress();
            if ( !$validator->isValid( $this->_criteriaValue ) ) {
                $this->_validationErrors[] = 'not a valid email address';
            }
        }
    }
}
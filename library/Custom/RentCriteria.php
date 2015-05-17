<?php

class Custom_RentCriteria extends Custom_AbstractCriteria
{
    public function __construct($rent)
    {
        parent::__construct($rent);
    }

    protected function _validate()
    {
        if ( empty($this->_criteriaValue) ) {
            $this->_validationErrors[] = 'rent value was unspecified';
        } else {
            if ( !is_double($this->_criteriaValue) ) {
                $this->_validationErrors[] = 'rent must be a decimal value (double)';
            }
        }
    }
}
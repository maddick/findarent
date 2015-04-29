<?php

class Custom_IdCriteria extends Custom_AbstractCriteria
{

    public function __construct($id)
    {
        parent::__construct($id);
    }

    protected function _validate()
    {
        if ( empty($this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'ID was unspecified';
        } else {
            if( !is_int( $this->_criteriaValue ) ) {
                $this->_validationErrors[] = 'An ID must be an integer value';
            }
        }
    }
}
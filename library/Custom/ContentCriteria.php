<?php

class Custom_ContentCriteria extends Custom_AbstractCriteria
{
    public function __construct($criteriaValue)
    {
        parent::__construct($criteriaValue);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'content was unspecified';
        } else {
            if ( $this->_criteriaValue !== 'About' and
                 $this->_criteriaValue !== 'Contact' and
                 $this->_criteriaValue !== 'Privacy' and
                 $this->_criteriaValue !== 'EqualHousing'
            )
            {
                $this->_validationErrors[] = 'Not a valid content value';
            }
        }
    }
}
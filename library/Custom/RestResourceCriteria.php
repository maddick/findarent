<?php
class Custom_RestResourceCriteria extends Custom_AbstractCriteria
{
    public function __construct($restResource)
    {
        parent::__construct($restResource);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'rest resource was unspecified or empty';
        } else {
            if ( !is_array( $this->_criteriaValue ) ) {
                $this->_validationErrors[] = 'rest resource values must be of type array';
            }
        }
    }
}
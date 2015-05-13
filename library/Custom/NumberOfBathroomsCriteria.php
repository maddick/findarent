<?php

class Custom_NumberOfBathroomsCriteria extends Custom_AbstractCriteria
{
    /**
     * @param $numberOfBathrooms
     */
    public function __construct($numberOfBathrooms)
    {
        parent::__construct($numberOfBathrooms);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'numberOfBathrooms was unspecified';
        } else {
            if ( !is_double( $this->_criteriaValue ) ) {
                $this->_validationErrors[] = 'numberOfBathrooms must be a decimal value (double)';
            }
        }
    }
}
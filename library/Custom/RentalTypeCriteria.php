<?php
class Custom_RentalTypeCriteria extends Custom_AbstractCriteria
{
    public function __construct($rentalType)
    {
        parent::__construct($rentalType);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'rentalType was unspecified';
        } else {
            if ( $this->_criteriaValue !== 'Rentals' and
                 $this->_criteriaValue !== 'Subsidized / 62+ / Disabled' and
                 $this->_criteriaValue !== 'Vacation Rentals' and
                 $this->_criteriaValue !== 'Furnished / Short-Term Rentals' and
                 $this->_criteriaValue !== 'Rent to Own'
            ) {
                $this->_validationErrors[] = 'not a valid rentalType value';
            }
        }
    }
}
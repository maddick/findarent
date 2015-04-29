<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/23/15
 * Time: 11:20 PM
 */

/**
 * Class Custom_NumberOfBedroomsCriteria
 *
 * This class is responsible for the validation of number of bedrooms criteria
 */
class Custom_NumberOfBedroomsCriteria extends Custom_AbstractCriteria
{
    /**
     * @param $numberOfBedrooms
     */
    public function __construct($numberOfBedrooms)
    {
        parent::__construct($numberOfBedrooms);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'numberOfBedrooms was unspecified';
        } else {
            if ( !is_double( $this->_criteriaValue ) ) {
                $this->_validationErrors[] = 'numberOfBedrooms must be a decimal value (double)';
            }
        }
    }
}
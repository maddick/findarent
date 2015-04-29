<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 4/23/15
 * Time: 10:06 PM
 */

class Custom_RadiusCriteria extends Custom_AbstractCriteria
{
    /**
     * This class is responsible for handling the validation and transformation
     * of radius parameter. Transformed values will be stored in the $_transformedCriteria
     * array for later use.
     *
     * @param $radius
     * @throws Exception when $radius is not an integer
     */
    public function __construct( $radius )
    {
        parent::__construct( $radius );
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'Radius was unspecified';
        } else {
            if ( !is_int( $this->_criteriaValue ) ) {
                $this->_validationErrors[] = 'Radius must be an integer value';
            }
        }
    }
}
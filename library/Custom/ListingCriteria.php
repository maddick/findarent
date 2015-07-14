<?php
class Custom_ListingCriteria extends Custom_AbstractCriteria
{
    protected $_validKeys = array(
        'ListingID',
        'Headline',
        'Rent',
        'Bedrooms',
        'Bathrooms',
        'State',
        'City',
        'MarketingMessage',
        'URL',
        'Image'
    );

    public function __construct($listing)
    {
        parent::__construct($listing);
    }

    protected function _validate()
    {
        if ( empty( $this->_criteriaValue ) ) {
            $this->_validationErrors[] = 'listing was unspecified or empty';
        } else {
            if ( !is_array( $this->_criteriaValue ) ) {
                $this->_validationErrors[] = 'listing values must be of type array';
            } else {
                foreach($this->_criteriaValue as $key => $value ) {
                    $keyWasFound = false;
                    foreach($this->_validKeys as $validKey ) {
                        if ( $key === $validKey ) {
                            $keyWasFound = true;
                            break;
                        }
                    }

                    if (!$keyWasFound) {
                        $this->_validationErrors[] = $key . ' was missing';
                    }
                }
            }
        }
    }
}
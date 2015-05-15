<?php

class Listing_Model_Listing
{
    /**
     * @var Custom_IdCriteria
     */
    protected $_listingIdCriteria;

    /**
     * @var array
     */
    protected $_result = array();

    /**
     * Attempts to get a listing from the database
     *
     * @return array containing the results of the get operation
     * @throws Exception when no listingIdCriteria is set before getting a listing
     */
    public function getListing()
    {
        if ( !isset( $this->_listingIdCriteria ) ) {
            throw new Exception('No listingIdCriteria was set.');
        }

        //validate the listing id criteria and attempt to find the listing requested
        if ( !$this->_listingIdCriteria->isValid() ) {
            $this->_result['result'] = 'error';
            $this->_result['reasons'] = $this->_listingIdCriteria->getValidationErrors();
        } else {
            $id = $this->_listingIdCriteria->getCriteria();
            $db = Zend_Db_Table::getDefaultAdapter();
            try {
                $sql = 'CALL FAR_Listings_GetListingByID(:id, 1, 0, 1)';
                $resultObj = $db->prepare($sql);
                $resultObj->execute(array('id' => $id));
                $this->_result['result'] = 'success';
                $this->_result['listing'] = $resultObj->fetchAll();
            } catch ( Exception $e ) {
                $this->_result['result'] = 'error';
                $this->_result['reasons'] = $e->getMessage();
            }
        }

        //return our results
        return $this->_result;
    }

    /**
     * Sets the dependency for listing id criteria
     *
     * @param $listingIdCriteria
     * @return $this
     * @throws Exception when $listingIdCriteria is not an instance of Custom_IdCriteria
     */
    public function setListingIdCriteria($listingIdCriteria)
    {
        if ( $listingIdCriteria instanceof Custom_IdCriteria ) {
            $this->_listingIdCriteria = $listingIdCriteria;
        } else {
            throw new Exception('listingIdCriteria must be an instance of Custom_IdCriteria');
        }

        return $this;
    }
}
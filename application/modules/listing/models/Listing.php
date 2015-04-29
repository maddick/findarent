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
     * Creates a new Listing model with a given id criteria
     *
     * @param $listingIdCriteria
     * @throws Exception when $listingIdCriteria is not an instance of Custom_IdCriteria
     */
    public function __construct($listingIdCriteria)
    {
        if ( $listingIdCriteria instanceof Custom_IdCriteria ) {
            $this->_listingIdCriteria = $listingIdCriteria;
        } else {
            throw new Exception('listingIdCriteria must be an instance of Custom_IdCriteria');
        }
    }


    /**
     * Attempts to get a listing from the database
     *
     * @return array containing the results of the get operation
     */
    public function getListing()
    {
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
}
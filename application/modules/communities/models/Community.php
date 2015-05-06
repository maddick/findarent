<?php

class Communities_Model_Community
{
    /**
     * @var Custom_IdCriteria
     */
    protected $_communityIdCriteria;

    /**
     * @var array
     */
    protected $_result = array();

    /**
     * Creates a new Community model with the given id criteria
     *
     * @param $communityIdCriteria
     * @throws Exception when $communityIdCriteria is not an instance of Custom_IdCriteria
     */
    public function __construct ( $communityIdCriteria )
    {
        if ( $communityIdCriteria instanceof Custom_IdCriteria ) {
            $this->_communityIdCriteria = $communityIdCriteria;
        } else {
            throw new Exception('communityIdCriteria must be an instance of Custom_IdCriteria');
        }
    }

    /**
     * Attempts to retrieve a community from the database.
     *
     * @return array containing the results of the stored procedure call
     */
    public function getCommunity()
    {
        if ( !$this->_communityIdCriteria->isValid() ) {
            $this->_result['result'] = 'error';
            $this->_result['reasons'] = $this->_communityIdCriteria->getValidationErrors();
        } else {
            $id = $this->_communityIdCriteria->getCriteria();
            $db = Zend_Db_Table::getDefaultAdapter();

            try {
                $sql = 'CALL FAR_Accounts_GetCommunityByID( :id, 1, 0, 1 )';
                $resultObj = $db->prepare($sql);
                $resultObj->execute( array( 'id' => $id ) );
                $this->_result['result'] = 'success';
                $this->_result['communities'] = $resultObj->fetchAll();
            } catch ( Exception $e ) {
                $this->_result['result'] = 'error';
                $this->_result['reasons'] = $e->getMessage();
            }
        }

        return $this->_result;
    }
}
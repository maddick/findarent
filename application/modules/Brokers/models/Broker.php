<?php

class Brokers_Model_Broker
{
    /**
     * @var Custom_IdCriteria
     */
    protected $_brokerIdCriteria;

    /**
     * @var array
     */
    protected $_result = array();

    /**
     * Creates a new Broker model with the given id criteria
     *
     * @param $brokerIdCriteria
     * @throws Exception when $communityIdCriteria is not an instance of Custom_IdCriteria
     */
    public function __construct ( $brokerIdCriteria )
    {
        if ( $brokerIdCriteria instanceof Custom_IdCriteria ) {
            $this->_brokerIdCriteria = $brokerIdCriteria;
        } else {
            throw new Exception('brokerIdCriteria must be an instance of Custom_IdCriteria');
        }
    }

    /**
     * Attempts to retrieve a broker from the database.
     *
     * @return array containing the results of the stored procedure call
     */
    public function getBroker()
    {
        //validate the id criteria
        if ( !$this->_brokerIdCriteria->isValid() ) {
            $this->_result['result'] = 'error';
            $this->_result['reasons'] = $this->_brokerIdCriteria->getValidationErrors();
        } else {
            $id = $this->_brokerIdCriteria->getCriteriaValue();
            $db = Zend_Db_Table::getDefaultAdapter();
            try {
                $sql = 'CALL FAR_Accounts_GetBrokerByID( :id, 1, 0, 1 )';
                $resultObj = $db->prepare($sql);
                $resultObj->execute( array( 'id' => $id ) );
                $this->_result['result'] = 'success';
                $this->_result['brokers'] = $resultObj->fetchAll();
            } catch ( Exception $e ) {
                $this->_result['result'] = 'server error';
                $this->_result['reasons'] = $e->getMessage();
            }
        }

        return $this->_result;
    }
}
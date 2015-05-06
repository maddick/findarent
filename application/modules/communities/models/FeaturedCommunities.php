<?php

class Communities_Model_FeaturedCommunities
{
    protected $_stateCriteria;

    protected $_results = array();

    public function getCommunityCitiesByState()
    {
        if ( !isset( $this->_stateCriteria ) ) {
            throw new Exception('No state criteria was set.');
        }

        if ( !$this->_stateCriteria->isValid() ) {
            $this->_results['result'] = 'error';
            $this->_results['reasons'] = $this->_stateCriteria->getValidationErrors();
        } else {
            try {
                $db = Zend_Db_Table::getDefaultAdapter();
                $sql = 'CALL FAR_Accounts_GetCommunityCitiesByState( :state )';
                $stmt = $db->prepare( $sql );
                $stmt->execute( array( 'state' => $this->_stateCriteria->getCriteria() ) );
                $communityCitiesByState = $stmt->fetchAll();
                $this->_results['result'] = 'success';
                $this->_results['communities'] = $communityCitiesByState;
            } catch ( Exception $e ) {
                $this->_results['result'] = 'server error';
                $this->_results['reasons'] = $e->getMessage();
            }
        }

        return $this->_results;
    }

    public function setStateCriteria( $stateCriteria )
    {
        if ( $stateCriteria instanceof Custom_StateCriteria ) {
            $this->_stateCriteria = $stateCriteria;
        } else {
            throw new Exception('stateCriteria must be an instance of Custom_StateCriteria');
        }
    }
}
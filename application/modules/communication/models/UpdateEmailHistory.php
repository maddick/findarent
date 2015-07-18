<?php
class Communication_Model_UpdateEmailHistory {
    /**
     * @var Custom_IdCriteria
     */
    protected $_tenantIdCriteria;

    /**
     * @var Custom_IdCriteria
     */
    protected $_jobIdCriteria;

    protected $_result = array();

    public function updateEmailHistory()
    {
        $validationErrors = array();

        if ( isset($this->_tenantIdCriteria) ) {
            if ( !$this->_tenantIdCriteria->isValid() ) {
                $this->_result['result'] = 'error';
                $validationErrors = array_merge( $validationErrors, $this->_tenantIdCriteria->getValidationErrors() );
            }
        } else {
            $this->_result['result'] = 'error';
            $validationErrors[] = 'tenantId not specified';
        }

        if ( isset( $this->_jobIdCriteria ) ) {
            if ( !$this->_jobIdCriteria->isValid() ) {
                $this->_result['result'] = 'error';
                $validationErrors = array_merge( $validationErrors, $this->_jobIdCriteria->getValidationErrors() );
            }
        } else {
            $this->_result['result'] = 'error';
            $validationErrors[] = 'jobId not specified';
        }

        if ( !empty( $validationErrors ) ) {
            $this->_result['reasons'] = $validationErrors;
            return $this->_result;
        }

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $stmt = $db->prepare('CALL FAR_EmailHistory_EmailOpened(:tenantId, 1, :jobId)');
            $stmt->execute(array(
                'tenantId' => $this->_tenantIdCriteria->getCriteriaValue(),
                'jobId' => $this->_jobIdCriteria->getCriteriaValue()
            ));

            $this->_result['result'] = 'success';
            $this->_result['message'] = 'Email history updated.';

        } catch ( Exception $e ) {
            $this->_result['result'] = 'server error';
            $this->_result['reason'] = $e->getMessage();
        }

        return $this->_result;
    }

    public function setTenantIdCriteria($tenantId)
    {
        if ( $tenantId instanceof Custom_IdCriteria ) {
            $this->_tenantIdCriteria = $tenantId;
        } else {
            throw new Exception('tenantIdCriteria must be an instance of Custom_IdCriteria');
        }

        return $this;
    }

    public function setJobIdCriteria($jobId)
    {
        if ( $jobId instanceof Custom_IdCriteria ) {
            $this->_jobIdCriteria = $jobId;
        } else {
            throw new Exception('jobIdCriteria must be an instance of Custom_IdCriteria');
        }

        return $this;
    }
}
<?php

class Content_Model_Content {

    /**
     * @var Custom_ContentCriteria
     */
    protected $_contentCriteria;

    protected $_validationErrors = array();

    protected $_result = array();

    public function getContent()
    {
        if ( !isset($this->_contentCriteria) ) {
            $this->_validationErrors[] = 'No content criteria was specified';
        } else {
            if ( !$this->_contentCriteria->isValid() ) {
                $this->_validationErrors = array_merge($this->_validationErrors, $this->_contentCriteria->getValidationErrors() );
            }
        }

        if ( !empty( $this->_validationErrors ) ) {
            $this->_result['result'] = 'error';
            $this->_result['reasons'] = $this->_validationErrors;
            return $this->_result;
        }

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $stmt = $db->prepare('CALL ContentEditor_GetContentSectionByTag(:tag)');
            $stmt->execute(array( 'tag' => $this->_contentCriteria->getCriteriaValue() ) );
            $this->_result['content'] = $stmt->fetchAll();
            $this->_result['result'] = 'success';
        } catch (Exception $e ) {
            $this->_result['result'] = 'server error';
            $this->_result['reasons'] = $e->getMessage();
        }

        return $this->_result;
    }

    public function setContentCriteria($contentCriteria)
    {
        if ( $contentCriteria instanceof Custom_ContentCriteria ) {
            $this->_contentCriteria = $contentCriteria;
        } else {
            throw new Exception('$contentCriteria must be an instance of Custom_ContentCriteria');
        }

        return $this;
    }
}
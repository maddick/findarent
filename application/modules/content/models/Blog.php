<?php

class Content_Model_Blog
{
    protected $_results = array();

    public function getPublishedBlogs()
    {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();

            $sql =
                'SELECT *
                FROM wp_posts
                WHERE post_status = \'publish\'';

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $this->_results['result'] = 'success';
            $this->_results['blogs'] = $stmt->fetchAll();
        } catch ( Exception $e ) {
            $this->_results['result'] = 'server error';
            $this->_results['reasons'] = $e->getMessage();
        }

        return $this->_results;
    }
}
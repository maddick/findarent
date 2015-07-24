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
                WHERE post_status = \'publish\'
                AND post_type = \'post\'';

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $this->_results['result'] = 'success';
            $this->_results['blogs'] = $stmt->fetchAll();
            foreach( $this->_results['blogs'] as $key => $value ) {
                $this->_results['blogs'][$key]['post_content'] = json_encode(utf8_encode($this->_results['blogs'][$key]['post_content']));
            }
        } catch ( Exception $e ) {
            $this->_results['result'] = 'server error';
            $this->_results['reasons'] = $e->getMessage();
        }

        return $this->_results;
    }
}
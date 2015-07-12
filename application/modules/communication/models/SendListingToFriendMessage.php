<?php
class Communication_Model_SendListingToFriendMessage extends Custom_AbstractMessage
{
    protected $_senderName;

    protected $_recipientName;

    protected $_listingTitle;

    protected $_listingURL;

    protected $_listingNumber;

    protected function _createMessage()
    {
        $reasons = array();
        if ( !isset($this->_senderName) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'No sender name was provided';
        }

        if ( !isset($this->_recipientName ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'No recipient name was provided';
        }

        if ( !isset( $this->_recipientAddress ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'No recipient address was provided.';
        }

        if ( !isset( $this->_listingTitle ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'No listing title was provided.';
        }

        if ( !isset( $this->_listingURL ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'No listing URL was provided.';
        }

        if ( !isset( $this->_listingNumber ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'No listing number was provided.';
        }

        if ( !empty( $reasons ) ) {
            $this->_results['reasons'] = $reasons;
            return false;
        }

        $this->_subject = "FindARent.Net: Listing #" . $this->_listingNumber . " From " . $this->_senderName;

        $this->_body = "Dear " . $this->_recipientName . ",<br><br>" .
            $this->_senderName . " has sent you this link in reference to a FindARent.net rental listing " .
            "titled <b>" . $this->_listingTitle . "</b>. To view the listing, click the following link: <br><br>" .
            "<a href=\"" . $this->_listingURL . "\">" . $this->_listingTitle . "</a>";

        return true;
    }

    public function setSenderName($senderName)
    {
        $this->_senderName = $senderName;
        return $this;
    }

    public function setRecipientName($recipientName)
    {
        $this->_recipientName = $recipientName;
        return $this;
    }

    public function setListingTitle($listingTitle)
    {
        $this->_listingTitle = $listingTitle;
        return $this;
    }

    public function setListingURL($listingURL)
    {
        $this->_listingURL = $listingURL;
        return $this;
    }

    public function setListingNumber($listingNumber)
    {
        $this->_listingNumber = $listingNumber;
        return $this;
    }
}
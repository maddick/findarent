<?php
class Communication_Model_SendEmailToOwnerMessage extends Custom_AbstractMessage
{
    /**
     * @var Custom_RestResourceCriteria
     */
    protected $_restResource;

    protected $_ownerName;

    protected $_senderEmail;

    protected $_senderMessage;

    protected $_senderName;

    protected $_senderPhone;

    protected $_type;

    const LISTING = 'LISTING';

    const BROKER = 'BROKER';

    const COMMUNITY = 'COMMUNITY';

    protected $_farNotice = ""; //TODO: this is based on source being 2 denoting Craig's List

    protected function _createMessage()
    {
        //reasons array to hold failure messages
        $reasons = array();

        if ( !isset( $this->_type ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'type was not provided';
        } else {
            if ( !isset( $this->_restResource ) ) {
                $this->_results['result'] = 'error';
                $reasons[] = 'rest resource was not provided';
            } else {
                if ( !$this->_restResource->isValid() ) {
                    $this->_results['result'] = 'error';
                    $reasons = array_merge( $reasons, $this->_restResource->getValidationErrors() );
                }
            }
        }

        if ( !isset( $this->_senderEmail ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'senderEmail was not provided';
        }
        if ( !isset( $this->_senderMessage ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'senderMessage was not provided';
        }
        if ( !isset( $this->_senderName ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'senderName was not provided';
        }
        if ( !empty( $reasons ) ) {
            $this->_results['reasons'] = $reasons;
            return false;
        }

        //determine the owner name based on contact name off of the provided resource
        if ( $this->_type === Communication_Model_SendEmailToOwnerMessage::LISTING) {
            $this->_generateListingEmail();
        }

        if ( $this->_type === Communication_Model_SendEmailToOwnerMessage::BROKER ) {
            $this->_generateBrokerEmail();
        }

        if ( $this->_type === Communication_Model_SendEmailToOwnerMessage::COMMUNITY ) {
            $this->_generateCommunityEmail();
        }
        return true;
    }

    private function _generateListingEmail()
    {
        $recipientAddress = new Custom_EmailCriteria('mike.matovic@gmail.com');
        $this->setRecipientAddress($recipientAddress);

        //generate a mini-listing using with a listing:
        // - listingID
        // - headline
        // - rent
        // - bedrooms
        // - state
        // - city
        // - description (MarketingMessage)
        // - link to the listing
        // - photo of the listing

        $listing = $this->_restResource->getCriteriaValue();
        $this->_ownerName = $listing['ContactName'];

        //attempt to get an owner name if one is not on the listing record
        $db = Zend_Db_Table::getDefaultAdapter();
        if ( empty( $this->_ownerName ) ) {
            $this->_ownerName = $this->_getOwnerName('CALL FAR_Accounts_GetBrokerByID(:id,1,0,1)', $listing['BrokerID'], $db);

            if ( empty ( $this->_ownerName) ) {
                $this->_ownerName = $this->_getOwnerName('CALL FAR_Accounts_GetCommunityByID(:id,1,0,1)', $listing['CommunityID'], $db);

                if( empty( $this->_ownerName )) {
                    $this->_ownerName = $this->_getOwnerName('CALL FAR_Accounts_GetLandlordByID(:id,1,0,1)', $listing['CommunityID'], $db);
                }
            }
        }

        $listingURL = 'http://www.findarent.net/Listings/' . $listing['ListingID'];

        //get a listing image
        $listingImageSql =
            'SELECT PhotoId,ImageURL
            FROM far_listings_photos
            WHERE ListingID = :id
            AND (Active = 1)
            AND Deleted = 0
            ORDER BY `Order`
            LIMIT 1';

        $imageStmt = $db->prepare($listingImageSql);
        $imageStmt->execute(array( 'id' => $listing['ListingID']));
        $imageResult = $imageStmt->fetchAll();

        $listingImage = 'http://findarent.net/Images/Listings/' . $imageResult['ImageURL'];

        $miniListing =
            "<div style=\"color: #5a5a5a;\">" .
            "<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"12\" align=\"center\">" .
            "<tbody>" .
            "<tr>" .
            "<td align=\"center\" valign=\"top\">" .
            "<table style=\"width: 850px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F2C747\">" .
            "<tbody>" .
            "<tr>" .
            "<td>" .
            "<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" bgcolor=\"#F9F9F9\">" .
            "<tbody>" .
            "<tr>" .
            "<td align=\"center\" width=\"300\">" .
            "<a href=\"" . $listingURL . "\" rel=\"nofollow\" target=\"_blank\"><img src=\"" . $listingImage . "\" alt=\"\" width=\"250px\"></a>" .
            "<br><strong>Listing #" . $listing['ListingID'] . "</strong>" .
            "</td>" .
            "<td>" .
            "<div style=\"color: #2f6d9d;\"><strong>" . $listing['Headline'] . "</strong></div>" .
            "<hr size=\"1\" noshade=\"\" style=\"border-top: 1px solid #BDBCAB;\">" .
            "<table style=\"width: 98%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" .
            "<tbody>" .
            "<tr>" .
            "<td>" .
            "<br />" .
            "<div style=\"color: #f5472c;\">" .
            "<strong>Rent: $" . $listing['Rent'] . "</strong>" .
            "</div>Bedrooms: " . $listing['Bedrooms'] .
            "<br>" .
            "Bathrooms: " . $listing['Bathrooms'] .
            "</td>" .
            "<td>" .
            $listing['City'] . ", " . $listing['State'] .
            "<br>" .
            "<a href=\"" . $listingURL . "\" rel=\"nofollow\" target=\"_blank\"><strong>Click for contact info &amp; details</strong></a>" .
            "</td>" .
            "</tr>" .
            "<tr>" .
            "<td colspan=\"2\">" .
            "<div style=\"color: #000000;\">" .
            $listing['MarketingMessage'] .
            "<a href=\"" . $listingURL . "\" rel=\"nofollow\" target=\"_blank\"> <strong>MORE »</strong></a>" .
            "<br>" .
            "<br>" .
            "<hr size=\"1\" noshade=\"\" style=\"border-top: 1px solid #BDBCAB;\">" .
            "<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">" .
            "<tbody>" .
            "<tr>" .
            "<td>" .
            "<a href=\"http://www.findarent.net\"><img src=\"http://www.findarent.net/App_Themes/Default/Images/logo_cl.png\" alt=\"FindARent.net\" border=\"0\" style=\"margin: 5px 0;\"></a>" .
            "</td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </div>
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>";

        $this->_subject = "FindARent.Net : Listing #" . $listing['ListingID'] . " Inquiry";

        $this->_body = "Dear " . $this->_ownerName . ", <br />" .
            "<br/>" .
            $this->_farNotice .
            "<p>The following email inquiry was sent in response to the following listing:</p>" .
            "<br />" .
            $miniListing .
            "<br />" .
            "<hr />" .
            "<br />" .
            "<b><u>Contact Information</u>:</b><br />" .
            "<b>Name:</b>&nbsp;&nbsp;" . $this->_senderName . "<br />" .
            "<b>Email:</b>&nbsp;&nbsp;" . $this->_senderEmail . "<br />" .
            "<b>Phone:</b>&nbsp;&nbsp;" . $this->_senderPhone . "<br />" .
            "<b>Message:</b><br />" .
            $this->_senderMessage . "<br />" .
            "<br />" .
            "<hr />" .
            "<a href=\"http://www.findarent.net\"><img src=\"http://www.findarent.net/App_Themes/Default/Images/mainlogo.png\" alt=\"FindARent.net\" border=\"0\" style=\"margin: 5px 0;\" /></a><br />" .
            "<a href=\"http://www.findarent.net\">http://www.findarent.net/</a><br />" .
            "<br />";
    }

    private function _generateBrokerEmail()
    {
        $recipientAddress = new Custom_EmailCriteria('mike.matovic@gmail.com');
        $this->setRecipientAddress($recipientAddress);

        $this->setBCC('mmatovic@conncoll.edu');//$this->setBCC('notifications@findarent.net');

        $broker = $this->_restResource->getCriteriaValue();

        if ( empty( $broker['MiddleName'] ) ) {
            $this->_ownerName = $broker['FirstName'] . ' ' . $broker['LastName'];
        } else {
            $this->_ownerName = $broker['FirstName'] . ' ' . $broker['MiddleName'] . ' ' . $broker['LastName'];
        }

        $this->_subject = "FindARent.Net: Broker Inquiry";

        $this->_body = 'Dear ' . $this->_ownerName . ', <br />
                        <br />
                        The below email inquiry was sent using the Contact Broker form:<br />
                        <br />
                        <b>Contact Information:</b><br />
                        <b>Name:</b>&nbsp;&nbsp;' . $this->_senderName . '<br />
                        <b>Email:</b>&nbsp;&nbsp;' . $this->_senderEmail . '<br />
                        <b>Phone:</b>&nbsp;&nbsp;' . $this->_senderPhone . '<br />
                        <b>Message:</b><br />'
                        . $this->_senderMessage . '<br />
                        <br />
                        <hr />
                        <a href="http://www.findarent.net"><img src="http://www.findarent.net/App_Themes/Default/Images/mainlogo.png" alt="FindARent.net" border="0" style="margin: 5px 0;" /></a><br />
                        <a href="http://www.findarent.net">http://www.findarent.net/</a><br />
                        <br />';
    }

    private function _generateCommunityEmail()
    {
        $recipientAddress = new Custom_EmailCriteria('mike.matovic@gmail.com');
        $this->setRecipientAddress($recipientAddress);

        $this->setBCC('mmatovic@conncoll.edu');//$this->setBCC('notifications@findarent.net');

        $community = $this->_restResource->getCriteriaValue();

        if ( empty( $community['MiddleName'] ) ) {
            $this->_ownerName = $community['FirstName'] . ' ' . $community['LastName'];
        } else {
            $this->_ownerName = $community['FirstName'] . ' ' . $community['MiddleName'] . ' ' . $community['LastName'];
        }

        $this->_subject = 'FindARent.Net: ' . $community['Community'] . ' Inquiry';

        $this->_body = 'Dear ' . $this->_ownerName . ', <br />
                        <br />
                        The below email inquiry was sent in response to the following featured community:<br />
                        <br />
                        <b>Contact Information:</b><br />
                        <b>Name:</b>&nbsp;&nbsp;' . $this->_senderName . '<br />
                        <b>Email:</b>&nbsp;&nbsp;' . $this->_senderEmail . '<br />
                        <b>Phone:</b>&nbsp;&nbsp;' . $this->_senderPhone . '<br />
                        <b>Message:</b><br />'
                        . $this->_senderMessage . '<br />
                        <br />
                        <hr />
                        <a href="http://www.findarent.net"><img src="http://www.findarent.net/App_Themes/Default/Images/mainlogo.png" alt="FindARent.net" border="0" style="margin: 5px 0;" /></a><br />
                        <a href="http://www.findarent.net">http://www.findarent.net/</a><br />
                        <br />';
    }

    /**
     * @param $sql
     * @param $id
     * @param $db Zend_Db_Adapter_Abstract
     * @return string
     */
    private function _getOwnerName($sql, $id, $db)
    {
        $stmt = $db->prepare($sql);
        $stmt->execute(array('id' => $id));
        $stmtResult = $stmt->fetchAll();
        $stmt->closeCursor();

        if (!empty($stmtResult)) {
            if (empty($stmtResult['MiddleName'])) {
                return $this->_ownerName = $stmtResult['FirstName'] . ' ' . $stmtResult['LastName'];
            } else {
                return $this->_ownerName = $stmtResult['FirstName'] . ' ' . $stmtResult['MiddleName'] . ' ' . $stmtResult['LastName'];
            }
        }
        return '';
    }

    public function setRestResource($listing)
    {
        if ( $listing instanceof Custom_RestResourceCriteria ) {
            $this->_restResource = $listing;
        } else {
            throw new Exception('$listing must be an instance of Custom_ListingCriteria');
        }
        return $this;
    }

    public function setSenderEmail($senderEmail)
    {
        $this->_senderEmail = $senderEmail;
    }

    public function setSenderMessage($senderMessage)
    {
        $this->_senderMessage = $senderMessage;
    }

    public function setSenderName($senderName)
    {
        $this->_senderName = $senderName;
    }

    public function setSenderPhone($senderPhone = '')
    {
        $this->_senderPhone = $senderPhone;
    }

    public function setType($type)
    {
        $this->_type = $type;
    }
}
<?php

namespace Cminds\MultiUserAccounts\Model\Data;

use Magento\Framework\DataObject;

/**
 * Class containing secure subaccount data that cannot be exposed as part
 * of \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface.
 *
 * @method string getRpToken()
 * @method string getRpTokenCreatedAt()
 * @method string getPasswordHash()
 * @method SubaccountSecure setRpToken(string $rpToken)
 * @method SubaccountSecure setRpTokenCreatedAt(string $rpTokenCreatedAt)
 * @method SubaccountSecure setPasswordHash(string $hashedPassword)
 */
class SubaccountSecure extends DataObject
{

}

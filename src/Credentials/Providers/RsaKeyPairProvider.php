<?php
/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * --------------------------------------------------------------------------
 *
 * PHP version 5
 *
 * @category AlibabaCloud
 *
 * @author    Alibaba Cloud SDK <sdk-team@alibabacloud.com>
 * @copyright 2018 Alibaba Group
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link https://github.com/aliyun/openapi-sdk-php-client
 */

namespace AlibabaCloud\Client\Credentials\Providers;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Credentials\AccessKeyCredential;
use AlibabaCloud\Client\Credentials\RsaKeyPairCredential;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Signature\ShaHmac256WithRsaSignature;

/**
 * Class RsaKeyPairProvider
 *
 * @package AlibabaCloud\Client\Credentials\Providers
 *
 * @author    Alibaba Cloud SDK <sdk-team@alibabacloud.com>
 * @copyright 2018 Alibaba Group
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link https://github.com/aliyun/openapi-sdk-php-client
 */
class RsaKeyPairProvider
{
    use ProviderTrait;

    /**
     * @return AccessKeyCredential
     * @throws ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     */
    public function getSessionCredential()
    {
        /**
         * @var RsaKeyPairCredential $credential
         */
        $credential       = $this->client->getCredential();
        $sessionAccessKey = $this->getCredentialsInCache($credential);

        if ($sessionAccessKey === null) {
            $clientName = __CLASS__ . \uniqid('rsa', true);
            AlibabaCloud::client(
                new AccessKeyCredential(
                    $credential->getPublicKeyId(),
                    $credential->getPrivateKey()
                ),
                new ShaHmac256WithRsaSignature()
            )->name($clientName);

            $result = (new GenerateSessionAccessKeyRequest($credential, $this->timeout))
                ->client($clientName)
                ->debug($this->client->isDebug())
                ->request();

            if (!$result->hasKey('SessionAccessKey')) {
                throw new ClientException('Result contains no SessionAccessKey', \ALI_INVALID_CREDENTIAL);
            }

            $sessionAccessKey = $result['SessionAccessKey'];
            $this->cache($credential, $sessionAccessKey);
        }
        return new AccessKeyCredential(
            $sessionAccessKey['SessionAccessKeyId'],
            $sessionAccessKey['SessionAccessKeySecret']
        );
    }

    /**
     * @param RsaKeyPairCredential $credential
     *
     * @return string
     */
    protected function key(RsaKeyPairCredential $credential)
    {
        return $credential->getPrivateKey() . '#' . $credential->getPublicKeyId();
    }
}

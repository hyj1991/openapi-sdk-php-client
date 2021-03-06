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

use AlibabaCloud\Client\Credentials\RamRoleArnCredential;
use AlibabaCloud\Client\Request\RpcRequest;

/**
 * Retrieving assume role credentials.
 *
 * @package AlibabaCloud\Client\Credentials\Providers
 *
 * @author    Alibaba Cloud SDK <sdk-team@alibabacloud.com>
 * @copyright 2018 Alibaba Group
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link https://github.com/aliyun/openapi-sdk-php-client
 */
class AssumeRoleRequest extends RpcRequest
{

    /**
     * AssumeRoleRequest constructor.
     *
     * @param RamRoleArnCredential $arnCredential
     * @param int                  $timeout
     */
    public function __construct(RamRoleArnCredential $arnCredential, $timeout)
    {
        $this->product('Sts');
        $this->version('2015-04-01');
        $this->action('AssumeRole');
        $this->domain(STS_DOMAIN);
        $this->options['query']['RoleArn']         = $arnCredential->getRoleArn();
        $this->options['query']['RoleSessionName'] = $arnCredential->getRoleSessionName();
        $this->options['query']['DurationSeconds'] = ROLE_ARN_EXPIRE_TIME;
        $this->protocol('https');
        $this->regionId('cn-hangzhou');
        $this->format('JSON');
        $this->timeout($timeout);
        $this->connectTimeout($timeout);
        parent::__construct();
    }
}

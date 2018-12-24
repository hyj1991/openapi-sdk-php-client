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
 * @category  AlibabaCloud
 *
 * @author    Alibaba Cloud SDK <sdk-team@alibabacloud.com>
 * @copyright 2018 Alibaba Group
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link      https://github.com/aliyun/openapi-sdk-php-client
 */

namespace AlibabaCloud\Client\Tests\Unit\Client\Request;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Request\RpcRequest;
use AlibabaCloud\Client\Tests\Mock\Services\Cdn\DescribeCdnServiceRequest;
use AlibabaCloud\Client\Tests\Mock\Services\Rds\DeleteDatabaseRequest;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest
 *
 * @package   AlibabaCloud\Client\Tests\Unit\Client\Request
 *
 * @author    Alibaba Cloud SDK <sdk-team@alibabacloud.com>
 * @copyright 2018 Alibaba Group
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link      https://github.com/aliyun/openapi-sdk-php-client
 * @coversDefaultClass \AlibabaCloud\Client\Request\Request
 */
class RequestTest extends TestCase
{

    /**
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public function testMergeOptions()
    {
        // Setup
        $clientName = 'option';
        $expected   = 'i \'m request';

        // Test
        AlibabaCloud::accessKeyClient('key', 'secret')
                    ->asGlobalClient()
                    ->regionId('cn-hangzhou')
                    ->options([
                                  'headers' => [
                                      'client' => 'client',
                                  ],
                              ])
                    ->name($clientName);

        $request = (new DescribeCdnServiceRequest())->client($clientName)
                                                    ->options(['request1' => 'request'])
                                                    ->options(['request2' => 'request2'])
                                                    ->options([
                                                                  'headers' => [
                                                                      'client' => $expected,
                                                                  ],
                                                              ]);
        $request->mergeOptionsIntoClient();

        // Assert
        $this->assertEquals($expected, $request->options['headers']['client']);
    }

    /**
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    public function testGetGlobalRegionId()
    {
        AlibabaCloud::accessKeyClient('key', 'secret')->name('temp');
        AlibabaCloud::setGlobalRegionId('cn-hangzhou');
        $request = (new DeleteDatabaseRequest())->client('temp');
        self::assertEquals('cn-hangzhou', $request->realRegionId());
    }

    /**
     * @expectedException \AlibabaCloud\Client\Exception\ClientException
     * @expectedExceptionCode    0
     * @expectedExceptionMessage Missing required 'RegionId' for Request
     */
    public function testNotFoundRegionId()
    {
        AlibabaCloud::flush();
        AlibabaCloud::accessKeyClient('key', 'secret')
                    ->name('temp');
        $request = (new DeleteDatabaseRequest())->client('temp');
        $request->realRegionId();
    }

    /**
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @covers ::isDebug
     */
    public function testIsDebug()
    {
        AlibabaCloud::accessKeyClient('key', 'secret')
                    ->name('temp');
        $request = (new DeleteDatabaseRequest())->client('temp')
                                                ->debug(false);
        self::assertFalse($request->isDebug());

        unset($request->options['debug']);
        AlibabaCloud::get('temp')->debug(false);
        self::assertFalse($request->isDebug());

        unset($request->options['debug'], AlibabaCloud::get('temp')->options['debug']);
        self::assertFalse($request->isDebug());
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        // Setup
        $request = new RpcRequest([
                                      'debug'   => true,
                                      'timeout' => 1,
                                  ]);
        // Assert
        self::assertTrue($request->isDebug());
        self::assertEquals(1, $request->options['timeout']);

        // Setup
        $request->debug(false);
        $request->timeout(2);

        // Assert
        self::assertFalse($request->isDebug());
        self::assertEquals(2, $request->options['timeout']);
    }
}

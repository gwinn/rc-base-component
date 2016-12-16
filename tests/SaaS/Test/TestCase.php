<?php

/**
 * PHP version 5.3
 *
 * @category ApiClient
 * @package  SaaS\Test
 * @author   Alex Lushpai <lushpai@gmail.com>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     http://github.com/gwinn/saas-connector
 * @see      http://github.com/gwinn/saas-connector
 */
namespace SaaS\Test;

use SaaS\Http\Response;
use SaaS\Service\Insales\Api as InSalesApi;
use SaaS\Service\Tiu\Api as TiuApi;
use Saas\Service\Inpost\Api as InPostApi;
use SaaS\Service\Courierist\Api as CourieristApi;

/**
 * Class TestCase
 *
 * @category ApiClient
 * @package  SaaS\Test
 * @author   Alex Lushpai <lushpai@gmail.com>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     http://github.com/gwinn/saas-connector
 * @see      http://github.com/gwinn/saas-connector
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Get InSales API clent
     *
     * @param null $domain   internal domain
     * @param null $apiKey   token
     * @param null $password password
     *
     * @return InSalesApi
     */
    public static function getInsalesApiClient(
        $domain = null, $apiKey = null, $password = null
    ) {

        $testDomain = $domain ?: $_SERVER['INSALES_DOMAIN'];
        $testApiKey = $apiKey ?: $_SERVER['INSALES_API_KEY'];
        $testPassword = $password ?: $_SERVER['INSALES_PASSWORD'];

        return new InSalesApi($testApiKey, $testPassword, $testDomain);
    }

    /**
     * Get Tiu API clent
     *
     * @param string $token token
     * @param string $url   internal url
     *
     *
     * @return TiuApi
     */
    public static function getTiuApiClient(
        $token = null,
        $url = null
    ) {
        $testToken = !is_null($token) ? $token : $_SERVER['TIU_TOKEN'];
        $testUrl =  !is_null($url) ? $url : $_SERVER['TIU_URL'];

        return new TiuApi($testToken, $testUrl);
    }

    /**
     * Get InPost API clent
     *
     * @return InPostApi
     */
    public static function getInpostApiClient()
    {
        return new InPostApi();
    }

    /**
     * Get Courieris API clent
     *
     * @param string $login login
     * @param string $pass  password
     *
     *
     * @return CourieristApi
     */
    public static function getCourieristApiClient(
        $login = null,
        $pass = null
    ) {
        $testLogin = !is_null($login) ? $login : $_SERVER['COURIERIST_LOGIN'];
        $testPass =  !is_null($pass) ? $pass : $_SERVER['COURIERIST_PASS'];

        return new CourieristApi($testLogin, $testPass);
    }

    /**
     * @param Response $response
     */
    public static function checkResponse(Response $response)
    {
        self::assertInstanceOf('SaaS\Http\Response', $response);
        self::assertTrue(in_array($response->getStatusCode(), array(200, 201)));
        self::assertTrue($response->isSuccessful());
    }
}

<?php

/**
 * PHP version 5.6
 *
 * @category Horoshop
 * @package  SaaS
 * @author   Alex Lushpai <lushpai@gmail.com>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     http://github.com/gwinn/saas-connector
 * @see      http://goo.gl/gBlC0T
 */
namespace SaaS\Service\Horoshop;

use SaaS\Http\Response;

/**
 * Horoshop api class
 *
 * @category Horoshop
 * @package  SaaS
 * @author   Alex Lushpai <lushpai@gmail.com>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     http://github.com/gwinn/saas-connector
 * @see      http://goo.gl/gBlC0T
 */
class Api
{
    protected $token;
    protected $request;

    /**
     * Client constructor.
     *
     * @param string $domain   User domain
     * @param string $login    User login
     * @param string $password User password
     * @param mixed $token     User token
     */
    public function __construct($domain, $login, $password, $token = null)
    {
        if (empty($domain) || empty($login) || empty($password)) {
            throw new \InvalidArgumentException(
                "domain & login & password must be not empty"
            );
        }

        $this->request = new Request($domain);

        if (is_null($token)) {
            $auth = $this->auth($login, $password);
            $this->token = !empty($auth['response']['token']) ? $auth['response']['token'] : null;
        } else {
            $this->token = $token;
        }
    }

    /**
     * Get request token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set request token
     *
     * @param string $token Horoshop token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Auth method
     *
     * @param string $login    user login
     * @param string $password user password
     *
     * @return Response
     */
    public function auth($login, $password)
    {
        $path = "auth";
        $parameters = array('login' => $login, 'password' => $password);

        return $this->request->makeRequest($path, Request::METHOD_POST, $parameters);
    }

    /**
     * Get orders
     *
     * @param array  $params Parameters for request
     *
     * @return Response
     */
    public function ordersGet($params = array())
    {
        $path = "orders/get";

        $parameters = array_merge(
            array('additionalData' => true, 'token' => $this->token),
            $params
        );

        return $this->request->makeRequest($path, Request::METHOD_PUT, $parameters);
    }

    /**
     * Update orders
     *
     * @param array $orders Data orders
     *
     * @return Response
     */
    public function ordersUpdate($orders = array())
    {
        $path = "orders/update";
        $parameters = array('orders' => $orders, 'token' => $this->token);

        return $this->request->makeRequest($path, Request::METHOD_PUT, $parameters);
    }

    /**
     * Get products
     *
     * @param array  $params Parameters for request
     *
     * @return Response
     */
    public function productsGet($params = array())
    {
        $path = "catalog/export";
        $parameters = array_merge(array('token' => $this->token), $params);

        return $this->request->makeRequest($path, Request::METHOD_GET, $parameters);
    }

    /**
     * Get categories
     *
     * @param array  $params Parameters for request
     *
     * @return Response
     */
    public function categoriesGet($params = array())
    {
        $path = "pages/export";
        $parameters = array_merge(array('token' => $this->token), $params);

        return $this->request->makeRequest($path, Request::METHOD_GET, $parameters);
    }

    /**
     * Get currency
     *
     * @param array  $params Parameters for request
     *
     * @return Response
     */
    public function currencyGet($params = array())
    {
        $path = "currency/export";
        $parameters = array_merge(array('token' => $this->token), $params);

        return $this->request->makeRequest($path, Request::METHOD_GET, $parameters);
    }

    /**
     * Update inventories
     *
     * @param array $products
     *
     * @return Response
     */
    public function importResidues($products)
    {
        if (empty($products)) {
            throw new \InvalidArgumentException("Products are empty or not defined");
        }

        $path = "catalog/importResidues";

        $parameters = array(
            'token' => $this->token,
            'products' => $products
        );

        return $this->request->makeRequest($path, Request::METHOD_PUT, $parameters);
    }

    /**
     * Update products
     *
     * @param array $products
     *
     * @return Response
     */
    public function productsImport($products)
    {
        if (empty($products)) {
            throw new \InvalidArgumentException("Products are empty or not defined");
        }

        $path = "catalog/import";

        $parameters = array(
            'token' => $this->token,
            'products' => $products
        );

        return $this->request->makeRequest($path, Request::METHOD_PUT, $parameters);
    }

    /**
     * Set webhook
     *
     * @param string $event название события
                        order_created - событие срабатывающее при оформлении пользователем заказа либо при создании заказа в админ. панели
                        user_signup   - событие срабатывающее при регистрации пользователя
     * @param string $url ссылка по которой необходимо отправить данные при срабатывании события
     *
     * @return Response
     */
    public function setWebhook($event, $url)
    {
        if (empty($event) || empty($url)) {
            throw new \InvalidArgumentException("Event or url webhook are empty or not defined");
        }

        $path = "hooks/subscribe";

        $parameters = array(
            'token' => $this->token,
            'event' => $event,
            'target_url' => $url,
        );

        return $this->request->makeRequest($path, Request::METHOD_PUT, $parameters);
    }

    /**
     * Delete webhook
     *
     * @param string $id  идентификатор подписки полученный в функции hooks/subscribe
     * @param string $url ссылка по которой необходимо отправить данные при срабатывании события
     *
     * @return Response
     * @throws \SaaS\Exception\HoroshopLimitException
     */
    public function deleteWebhook($id, $url)
    {
        if (empty($id) || empty($url)) {
            throw new \InvalidArgumentException("Id or url webhook are empty or not defined");
        }

        $path = "hooks/unSubscribe";

        $parameters = array(
            'token' => $this->token,
            'id' => $id,
            'target_url' => $url,
        );

        return $this->request->makeRequest(
            $path,
            Request::METHOD_DELETE,
            $parameters,
            array('content-type' => 'application/json')
        );
    }

    /**
     * Get delivery variants
     *
     * @return Response
     */
    public function getDeliveryVariants()
    {
        $path = "delivery/export";

        $parameters = array(
            'token' => $this->token
        );

        return $this->request->makeRequest($path, Request::METHOD_GET, $parameters);
    }

    /**
     * Get delivery types
     *
     * @return Response
     */
    public function getDeliveryTypes()
    {
        $path = "delivery/exportTypes";

        $parameters = array(
            'token' => $this->token
        );

        return $this->request->makeRequest($path, Request::METHOD_GET, $parameters);
    }

    /**
     * Get payment variants
     *
     * @return Response
     */
    public function getPaymentVariants()
    {
        $path = "payment/export";

        $parameters = array(
            'token' => $this->token
        );

        return $this->request->makeRequest($path, Request::METHOD_GET, $parameters);
    }

    /**
     * Get payment methods
     *
     * @return Response
     */
    public function getPaymentMethods()
    {
        $path = "payment/exportMethods";

        $parameters = array(
            'token' => $this->token
        );

        return $this->request->makeRequest($path, Request::METHOD_GET, $parameters);
    }

    /**
     * Import product set
     *
     * @param array $items список комплектов
     *
     * @return Response
     */
    public function productSetImport($items)
    {
        if (empty($items)) {
            throw new \InvalidArgumentException("Product set are empty or not defined");
        }


        $path = "productSet/import";

        $parameters = array(
            'token' => $this->token,
            'items' => $items
        );

        return $this->request->makeRequest($path, Request::METHOD_PUT, $parameters);
    }

    /**
     * Remove product set
     *
     * @param array $articles Array of article numbers of the kits to be removed
     *
     * @return Response
     */
    public function productSetRemove($articles)
    {
        if (empty($articles)) {
            throw new \InvalidArgumentException("Articles are empty or not defined");
        }

        $path = "productSet/remove";

        $parameters = array(
            'token' => $this->token,
            'articles' => $articles
        );

        return $this->request->makeRequest($path, Request::METHOD_PUT, $parameters);
    }

    /**
     * Get active users
     *
     * @param array $params Parameters for request
     *
     * @return Response
     */
    public function usersGet($params = array())
    {
        $path = "users/export";
        $parameters = array_merge(array('token' => $this->token), $params);

        return $this->request->makeRequest($path, Request::METHOD_GET, $parameters);
    }
}

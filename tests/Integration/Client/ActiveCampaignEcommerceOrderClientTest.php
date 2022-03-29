<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use DateTime;
use Psr\Http\Message\RequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrder;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscount;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscountInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProduct;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\CreateEcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\UpdateEcommerceOrderResponse;

final class ActiveCampaignEcommerceOrderClientTest extends KernelTestCase
{
    private ActiveCampaignResourceClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        HttpClientStub::setUp();

        $this->client = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_order');
    }

    public function test_it_creates_ecommerce_order_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 201;
        HttpClientStub::$responseBodyContent = '{"connections":[{"service":"example","externalid":"examplestore","name":"My Example Store","isInternal":"0","connectionType":"ecommerce","status":"1","syncStatus":"0","sync_request_time":null,"sync_start_time":null,"lastSync":null,"logoUrl":"https://myexamplestore.com/images/logo.jpg","linkUrl":"https://myexamplestore.com","cdate":"2018-01-12T13:13:53-06:00","udate":"2018-01-12T13:13:53-06:00","credentialExpiration":null,"links":{"options":"https://exampleaccount.api-us1.com/api/3/connections/1/options","customers":"https://exampleaccount.api-us1.com.api-us1.com/api/3/connections/1/customers"},"id":"1","serviceName":"shopify"}],"ecomOrderProducts":[{"externalid":"PROD12345","name":"PogoStick","price":4900,"quantity":1,"category":"Toys","sku":"POGO-12","description":"lorem ipsum...","imageUrl":"https://example.com/product.jpg","productUrl":"https://store.example.com/product12345"},{"externalid":"PROD23456","name":"Skateboard","price":3000,"quantity":1,"category":"Toys","sku":"SK8BOARD145","description":"lorem ipsum...","imageUrl":"https://example.com/product.jpg","productUrl":"https://store.example.com/product45678"}],"ecomOrderDiscounts":[{"name":"1OFF","type":"order","orderid":"5355","discountAmount":"100","id":"1","createdDate":"2019-09-05T12:16:18-05:00","updatedDate":"2019-09-05T12:16:18-05:00"}],"ecomOrder":{"externalid":"3246315234","source":"1","email":"alice@example.com","currency":"USD","connectionid":"1","customerid":"1","orderUrl":"https://example.com/orders/3246315233","shippingMethod":"UPS Ground","totalPrice":9111,"shippingAmount":200,"taxAmount":500,"discountAmount":100,"externalCreatedDate":"2016-09-13T16:41:39-05:00","totalProducts":2,"createdDate":"2019-09-05T12:16:18-05:00","updatedDate":"2019-09-05T12:16:18-05:00","state":1,"connection":"1","orderProducts":["1","2"],"orderDiscounts":["1"],"customer":"1","orderDate":"2016-09-13T16:41:39-05:00","tstamp":"2019-09-05T12:16:18-05:00","links":{"connection":"https://exampleaccount.api-us1.com/api/3/ecomOrders/1/connection","customer":"https://exampleaccount.api-us1.com/api/3/ecomOrders/1/customer","orderProducts":"https://exampleaccount.api-us1.com/api/3/ecomOrders/1/orderProducts","orderDiscounts":"https://exampleaccount.api-us1.com/api/3/ecomOrders/1/orderDiscounts","orderActivities":"https://exampleaccount.api-us1.com/api/3/ecomOrders/1/orderActivities"},"id":"1"}}';
        $ecommerceOrder = new EcommerceOrder(
            'alice@example.com',
            '1',
            '1',
            'USD',
            9111,
            new DateTime('2016-09-13T17:41:39-04:00'),
            '3246315233',
            null,
            null,
            EcommerceOrderInterface::REAL_TIME_SOURCE_CODE,
            [
                new EcommerceOrderProduct(
                    'Pogo Stick',
                    4900,
                    1,
                    'PROD12345',
                    'Toys',
                    'POGO-12',
                    'lorem ipsum...',
                    'https://example.com/product.jpg',
                    'https://store.example.com/product12345',
                ),
                new EcommerceOrderProduct(
                    'Skateboard',
                    3000,
                    1,
                    'PROD23456',
                    'Toys',
                    'SK8BOARD145',
                    'lorem ipsum...',
                    'https://example.com/product.jpg',
                    'https://store.example.com/product45678',
                ),
            ],
            200,
            500,
            100,
            'https://example.com/orders/3246315233',
            new DateTime('2016-09-14T17:41:39-04:00'),
            'UPS Ground',
            'myorder-123',
            [
                new EcommerceOrderDiscount(
                    '1OFF',
                    EcommerceOrderDiscountInterface::ORDER_DISCOUNT_TYPE,
                    100
                ),
            ],
        );

        $createdEcommerceOrder = $this->client->create($ecommerceOrder);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/ecomOrders', $sentRequest->getUri()->getPath());
        self::assertEquals('POST', $sentRequest->getMethod());
        self::assertEquals(
            '{"ecomOrder":{"email":"alice@example.com","connectionid":"1","customerid":"1","currency":"USD","totalPrice":9111,"externalCreatedDate":"2016-09-13T17:41:39-04:00","externalid":"3246315233","externalcheckoutid":null,"source":"1","orderProducts":[{"name":"Pogo Stick","price":4900,"quantity":1,"externalid":"PROD12345","category":"Toys","sku":"POGO-12","description":"lorem ipsum...","imageUrl":"https:\/\/example.com\/product.jpg","productUrl":"https:\/\/store.example.com\/product12345"},{"name":"Skateboard","price":3000,"quantity":1,"externalid":"PROD23456","category":"Toys","sku":"SK8BOARD145","description":"lorem ipsum...","imageUrl":"https:\/\/example.com\/product.jpg","productUrl":"https:\/\/store.example.com\/product45678"}],"shippingAmount":200,"taxAmount":500,"discountAmount":100,"orderUrl":"https:\/\/example.com\/orders\/3246315233","externalUpdatedDate":"2016-09-14T17:41:39-04:00","abandonedDate":null,"shippingMethod":"UPS Ground","orderNumber":"myorder-123","orderDiscounts":[{"name":"1OFF","type":"order","discountAmount":100}]}}',
            $sentRequest->getBody()->getContents()
        );

        self::assertNotNull($createdEcommerceOrder);
        self::assertInstanceOf(CreateEcommerceOrderResponse::class, $createdEcommerceOrder);
        self::assertEquals(1, $createdEcommerceOrder->getResourceResponse()->getId());
    }

    public function test_it_updates_ecommerce_order_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{"ecomOrderProducts":[{"orderid":"1","connectionid":"1","externalid":"PROD12345","sku":"POGO-12","name":"PogoStick","description":"loremipsum...","price":"4900","quantity":"1","category":"Toys","imageUrl":"https://example.com/product.jpg","productUrl":"https://store.example.com/product12345","createdDate":"2019-09-05T13:55:37-05:00","updatedDate":"2019-09-05T13:55:37-05:00","tstamp":"2019-09-05T13:55:37-05:00","links":{"ecomOrder":"https://youraccounthere.api-us1.com/api/3/ecomOrderProducts/1/ecomOrder"},"id":"3","ecomOrder":"1"},{"orderid":"1","connectionid":"1","externalid":"PROD23456","sku":"SK8BOARD145","name":"Skateboard","description":"loremipsum...","price":"3000","quantity":"1","category":"Toys","imageUrl":"https://example.com/product.jpg","productUrl":"https://store.example.com/product45678","createdDate":"2019-09-05T13:55:37-05:00","updatedDate":"2019-09-05T13:55:37-05:00","tstamp":"2019-09-05T13:55:37-05:00","links":{"ecomOrder":"https://youraccounthere.api-us1.com/api/3/ecomOrderProducts/1/ecomOrder"},"id":"4","ecomOrder":"1"}],"ecomOrderDiscounts":[{"name":"1OFF","type":"order","orderid":"5355","discountAmount":"100","id":"1","createdDate":"2019-09-05T12:16:18-05:00","updatedDate":"2019-09-05T12:16:18-05:00"}],"ecomOrder":{"customerid":"1","connectionid":"1","state":"1","source":"1","externalid":"3246315237","orderNumber":"","email":"alice@example.com","totalPrice":9111,"discountAmount":100,"shippingAmount":200,"taxAmount":500,"totalProducts":2,"currency":"USD","shippingMethod":"UPSGround","orderUrl":"https://example.com/orders/3246315233","externalCreatedDate":"2016-09-13T16:41:39-05:00","externalUpdatedDate":"2016-09-15T16:41:39-05:00","createdDate":"2019-09-05T12:52:13-05:00","updatedDate":"2019-09-05T13:55:37-05:00","orderProducts":["3","4"],"orderDiscounts":["1"],"customer":"1","orderDate":"2016-09-13T16:41:39-05:00","tstamp":"2019-09-05T13:55:37-05:00","links":{"connection":"https://youraccounthere.api-us1.com/api/3/ecomOrders/1/connection","customer":"https://youraccounthere.api-us1.com/api/3/ecomOrders/1/customer","orderProducts":"https://youraccounthere.api-us1.com/api/3/ecomOrders/1/orderProducts","orderDiscounts":"https://exampleaccount.api-us1.com/api/3/ecomOrders/1/orderDiscounts","orderActivities":"https://youraccounthere.api-us1.com/api/3/ecomOrders/1/orderActivities"},"id":"1","connection":"1"}}';
        $ecommerceOrder = new EcommerceOrder(
            'alice@example.com',
            '1',
            '1',
            'USD',
            9111,
            new DateTime('2016-09-13T17:41:39-04:00'),
            '3246315237',
            null,
            null,
            EcommerceOrderInterface::REAL_TIME_SOURCE_CODE,
            [
                new EcommerceOrderProduct(
                    'Pogo Stick',
                    4900,
                    1,
                    'PROD12345',
                    'Toys',
                    'POGO-12',
                    'lorem ipsum...',
                    'https://example.com/product.jpg',
                    'https://store.example.com/product12345',
                ),
                new EcommerceOrderProduct(
                    'Skateboard',
                    3000,
                    1,
                    'PROD23456',
                    'Toys',
                    'SK8BOARD145',
                    'lorem ipsum...',
                    'https://example.com/product.jpg',
                    'https://store.example.com/product45678',
                ),
            ],
            200,
            500,
            100,
            'https://example.com/orders/3246315233',
            new DateTime('2016-09-15T17:41:39-04:00'),
            'UPS Ground',
            '12345-1',
            [
                new EcommerceOrderDiscount(
                    '1OFF',
                    EcommerceOrderDiscountInterface::ORDER_DISCOUNT_TYPE,
                    100
                ),
            ],
        );

        $updatedEcommerceOrder = $this->client->update(3, $ecommerceOrder);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/ecomOrders/3', $sentRequest->getUri()->getPath());
        self::assertEquals('PUT', $sentRequest->getMethod());
        self::assertEquals(
            '{"ecomOrder":{"email":"alice@example.com","connectionid":"1","customerid":"1","currency":"USD","totalPrice":9111,"externalCreatedDate":"2016-09-13T17:41:39-04:00","externalid":"3246315237","externalcheckoutid":null,"source":"1","orderProducts":[{"name":"Pogo Stick","price":4900,"quantity":1,"externalid":"PROD12345","category":"Toys","sku":"POGO-12","description":"lorem ipsum...","imageUrl":"https:\/\/example.com\/product.jpg","productUrl":"https:\/\/store.example.com\/product12345"},{"name":"Skateboard","price":3000,"quantity":1,"externalid":"PROD23456","category":"Toys","sku":"SK8BOARD145","description":"lorem ipsum...","imageUrl":"https:\/\/example.com\/product.jpg","productUrl":"https:\/\/store.example.com\/product45678"}],"shippingAmount":200,"taxAmount":500,"discountAmount":100,"orderUrl":"https:\/\/example.com\/orders\/3246315233","externalUpdatedDate":"2016-09-15T17:41:39-04:00","abandonedDate":null,"shippingMethod":"UPS Ground","orderNumber":"12345-1","orderDiscounts":[{"name":"1OFF","type":"order","discountAmount":100}]}}',
            $sentRequest->getBody()->getContents()
        );

        self::assertNotNull($updatedEcommerceOrder);
        self::assertInstanceOf(UpdateEcommerceOrderResponse::class, $updatedEcommerceOrder);
        self::assertEquals(1, $updatedEcommerceOrder->getResourceResponse()->getId());
    }

    public function test_it_removes_ecommerce_order_on_active_campaign(): void
    {
        HttpClientStub::$responseStatusCode = 200;
        HttpClientStub::$responseBodyContent = '{}';

        $this->client->remove(2);

        self::assertCount(1, HttpClientStub::$sentRequests);
        $sentRequest = reset(HttpClientStub::$sentRequests);
        self::assertInstanceOf(RequestInterface::class, $sentRequest);
        self::assertEquals('/api/3/ecomOrders/2', $sentRequest->getUri()->getPath());
        self::assertEquals('DELETE', $sentRequest->getMethod());
        self::assertEmpty($sentRequest->getBody()->getContents());
    }
}

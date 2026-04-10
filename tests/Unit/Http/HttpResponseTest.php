<?php
declare(strict_types=1);

namespace Pronnect\GpWebPayTest\Unit\Http;

use PHPUnit\Framework\TestCase;
use Pronnect\GpWebPay\Http\Response\HttpResponse;
use Pronnect\GpWebPayApi\Http\Response\HttpResponseInterface;

/**
 * @covers \Pronnect\GpWebPay\Http\Response\HttpResponse
 */
class HttpResponseTest extends TestCase
{
    private function successParams(): array
    {
        return [
            'OPERATION'   => 'CREATE_ORDER',
            'ORDERNUMBER' => '123456',
            'PRCODE'      => '0',
            'SRCODE'      => '0',
            'RESULTTEXT'  => 'OK',
        ];
    }

    public function testImplementsInterface(): void
    {
        $response = HttpResponse::fromArray($this->successParams());
        $this->assertInstanceOf(HttpResponseInterface::class, $response);
    }

    public function testIsSuccessReturnsTrueWhenBothCodesAreZero(): void
    {
        $response = HttpResponse::fromArray($this->successParams());
        $this->assertTrue($response->isSuccess());
    }

    public function testIsSuccessReturnsFalseWhenPrCodeIsNonZero(): void
    {
        $params            = $this->successParams();
        $params['PRCODE']  = '30';
        $params['SRCODE']  = '0';

        $response = HttpResponse::fromArray($params);
        $this->assertFalse($response->isSuccess());
    }

    public function testIsSuccessReturnsFalseWhenSrCodeIsNonZero(): void
    {
        $params            = $this->successParams();
        $params['PRCODE']  = '0';
        $params['SRCODE']  = '5';

        $response = HttpResponse::fromArray($params);
        $this->assertFalse($response->isSuccess());
    }

    public function testFromArrayParsesAllFields(): void
    {
        $params = [
            'OPERATION'        => 'CREATE_ORDER',
            'ORDERNUMBER'      => '123456',
            'MERORDERNUM'      => '654321',
            'MD'               => 'myref',
            'PRCODE'           => '0',
            'SRCODE'           => '0',
            'RESULTTEXT'       => 'OK',
            'TOKEN'            => 'tok-abc-def',
            'EXPIRY'           => '2612',
            'ACSRES'           => 'Y',
            'ACCODE'           => '012345',
            'PANPATTERN'       => '405607*******0016',
            'DAYTOCAPTURE'     => '15032026',
            'TOKENREGSTATUS'   => 'SUCCESS',
            'ACRC'             => '00',
            'RRN'              => '123456789012',
            'PAR'              => 'PAR-123',
            'TRACEID'          => 'TRACE-456',
        ];

        $response = HttpResponse::fromArray($params);

        $this->assertSame('CREATE_ORDER', $response->getOperation());
        $this->assertSame(123456, $response->getOrderNumber());
        $this->assertSame('654321', $response->getMerOrderNum());
        $this->assertSame('myref', $response->getMd());
        $this->assertSame(0, $response->getPrCode());
        $this->assertSame(0, $response->getSrCode());
        $this->assertSame('OK', $response->getResultText());
        $this->assertSame('tok-abc-def', $response->getToken());
        $this->assertSame('2612', $response->getExpiry());
        $this->assertSame('Y', $response->getAcsRes());
        $this->assertSame('012345', $response->getAcCode());
        $this->assertSame('405607*******0016', $response->getPanPattern());
        $this->assertSame('15032026', $response->getDayToCapture());
        $this->assertSame('SUCCESS', $response->getTokenRegStatus());
        $this->assertSame('00', $response->getAcrc());
        $this->assertSame('123456789012', $response->getRrn());
        $this->assertSame('PAR-123', $response->getPar());
        $this->assertSame('TRACE-456', $response->getTraceId());
    }

    public function testFromArrayWithMissingOptionalFieldsReturnsNulls(): void
    {
        $response = HttpResponse::fromArray($this->successParams());

        $this->assertNull($response->getMerOrderNum());
        $this->assertNull($response->getMd());
        $this->assertNull($response->getToken());
        $this->assertNull($response->getExpiry());
        $this->assertNull($response->getPanPattern());
        $this->assertNull($response->getTokenRegStatus());
    }

    public function testFromArrayWithEmptyStringFieldsTreatedAsNull(): void
    {
        $params               = $this->successParams();
        $params['MERORDERNUM'] = '';
        $params['MD']          = '';
        $params['TOKEN']       = '';

        $response = HttpResponse::fromArray($params);

        $this->assertNull($response->getMerOrderNum());
        $this->assertNull($response->getMd());
        $this->assertNull($response->getToken());
    }

    public function testFromServerRequestWithPostMethod(): void
    {
        $mockRequest = new class {
            public function getMethod(): string   { return 'POST'; }
            public function getParsedBody(): array {
                return [
                    'OPERATION'   => 'CREATE_ORDER',
                    'ORDERNUMBER' => '999',
                    'PRCODE'      => '0',
                    'SRCODE'      => '0',
                    'RESULTTEXT'  => 'OK',
                ];
            }
            public function getQueryParams(): array { return []; }
        };

        $response = HttpResponse::fromServerRequest($mockRequest);

        $this->assertSame(999, $response->getOrderNumber());
        $this->assertTrue($response->isSuccess());
    }

    public function testFromServerRequestWithGetMethod(): void
    {
        $mockRequest = new class {
            public function getMethod(): string   { return 'GET'; }
            public function getParsedBody(): array { return []; }
            public function getQueryParams(): array {
                return [
                    'OPERATION'   => 'CREATE_ORDER',
                    'ORDERNUMBER' => '888',
                    'PRCODE'      => '30',
                    'SRCODE'      => '0',
                    'RESULTTEXT'  => 'Declined',
                ];
            }
        };

        $response = HttpResponse::fromServerRequest($mockRequest);

        $this->assertSame(888, $response->getOrderNumber());
        $this->assertFalse($response->isSuccess());
        $this->assertSame(30, $response->getPrCode());
    }

    public function testFromServerRequestWithLowercasePostMethod(): void
    {
        $mockRequest = new class {
            public function getMethod(): string   { return 'post'; }
            public function getParsedBody(): array {
                return ['OPERATION' => 'CREATE_ORDER', 'ORDERNUMBER' => '777', 'PRCODE' => '0', 'SRCODE' => '0'];
            }
            public function getQueryParams(): array { return []; }
        };

        $response = HttpResponse::fromServerRequest($mockRequest);
        $this->assertSame(777, $response->getOrderNumber());
    }
}

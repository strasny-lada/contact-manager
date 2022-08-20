<?php declare(strict_types = 1);

namespace App;

use PHPUnit\Framework\Assert;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiTestCase extends WebTestCase
{

    /**
     * @param mixed[] $data
     */
    public static function createJsonRequest(
        string $method,
        string $url,
        array $data = []
    ): Crawler
    {
        $client = self::createClient();

        return $client->request($method, $url, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data, JSON_THROW_ON_ERROR));
    }

    /**
     * @param mixed[]|mixed[][] $expectedData
     */
    protected static function assertJsonResponse(
        int $expectedStatusCode,
        array $expectedData,
        Response $actualResponse
    ): void
    {
        Assert::assertSame($expectedStatusCode, $actualResponse->getStatusCode(), 'Unexpected response code');

        $bodyData = self::parseJsonResponse($actualResponse);

        Assert::assertSame($expectedData, $bodyData);
    }

    /**
     * For getting response data which cannot be asserted due to additional HTML item.
     *
     * @return mixed[]
     */
    protected static function parseJsonResponse(Response $actualResponse): array
    {
        Assert::assertSame(
            'application/json',
            $actualResponse->headers->get('Content-Type'),
            sprintf(
                'Expected Content-Type "application/json" got "%s"',
                $actualResponse->headers->get('Content-Type')
            )
        );

        $content = $actualResponse->getContent();
        Assert::assertNotFalse($content);

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    protected static function parseCrawlerFromJsonResponse(
        int $expectedStatusCode,
        Response $actualResponse,
        string $fieldName
    ): Crawler
    {
        Assert::assertSame($expectedStatusCode, $actualResponse->getStatusCode(), 'Unexpected response code');

        Assert::assertSame(
            'application/json',
            $actualResponse->headers->get('Content-Type'),
            sprintf(
                'Expected Content-Type "application/json" got "%s"',
                $actualResponse->headers->get('Content-Type')
            )
        );

        $content = $actualResponse->getContent();
        Assert::assertNotFalse($content);

        $bodyData = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        Assert::assertNotNull($bodyData);

        $fieldData = null;
        foreach (explode('/', $fieldName) as $fieldNameChunk) {
            $fieldData = ($fieldData ?? $bodyData)[$fieldNameChunk] ?? null;
        }

        Assert::assertNotNull($fieldData, sprintf(
            'Field "%s" not found in JSON with keys: "%s"',
            $fieldName,
            implode('", "', array_keys($bodyData))
        ));

        return new Crawler($fieldData);
    }

}

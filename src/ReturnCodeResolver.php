<?php
declare(strict_types=1);

namespace Pronnect\GpWebPay;

use DOMDocument;
use DOMXPath;
use RuntimeException;

class ReturnCodeResolver
{
    private ?DOMXPath $xpath = null;

    public function resolvePrimary(int $prCode): string
    {
        return $this->query('/codes/primaryReturnCodes/code[@value="%d"]', $prCode);
    }

    public function resolveSecondary(int $srCode): string
    {
        return $this->query('/codes/secondaryReturnCode/code[@value="%d"]', $srCode);
    }

    private function query(string $xpathPattern, int $code): string
    {
        $nodes = $this->xpath()->query(sprintf($xpathPattern, $code));

        if ($nodes === false || $nodes->length === 0) {
            return sprintf('Unknown code %d', $code);
        }

        return trim($nodes->item(0)->textContent);
    }

    private function xpath(): DOMXPath
    {
        if ($this->xpath !== null) {
            return $this->xpath;
        }

        $path = dirname(__DIR__) . '/resources/xml/codes.xml';
        $xml  = is_readable($path) ? file_get_contents($path) : false;

        if ($xml === false) {
            throw new RuntimeException(sprintf('GP WebPay codes.xml not readable at %s', $path));
        }

        $dom = new DOMDocument();
        if (!$dom->loadXML($xml)) {
            throw new RuntimeException('GP WebPay codes.xml failed to parse');
        }

        return $this->xpath = new DOMXPath($dom);
    }
}

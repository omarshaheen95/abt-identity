<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PdfZipService
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 36000]);
    }

    public function generate(array $formParams, string $fileName): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // 1) Request PDF generation from the external service
        try {
            $res = $this->client->request('POST', 'https://pdfservice.arabic-uae.com/getpdf.php', [
                'form_params' => $formParams,
            ]);
        } catch (ConnectException $e) {
            Log::channel('telegram')->error("[PDF] Connection to pdfservice failed: " . $e->getMessage());
            throw new RuntimeException('connection_failed');
        } catch (RequestException $e) {
            $response   = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 'N/A';
            Log::channel('telegram')->error("[PDF] pdfservice rejected the request. Status: {$statusCode} - " . $e->getMessage());
            throw new RuntimeException('request_failed');
        }

        // 2) Decode and validate the JSON response
        $responseBody = (string) $res->getBody();
        $result       = json_decode($responseBody);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($result->url)) {
            Log::channel('telegram')->error("[PDF] Invalid response from pdfservice: " . $responseBody);
            throw new RuntimeException('invalid_response');
        }

        // 3) Open a streaming connection to the ZIP file URL
        try {
            $zipResponse = $this->client->request('GET', $result->url, [
                'stream'  => true,
                'timeout' => 36000,
            ]);
        } catch (\Exception $e) {
            Log::channel('telegram')->error("[PDF] Failed opening ZIP stream: " . $e->getMessage());
            throw new RuntimeException('zip_stream_failed');
        }

        $body          = $zipResponse->getBody();
        $contentLength = $zipResponse->getHeaderLine('Content-Length');

        // 4) Build response headers
        $headers = [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $fileName .'_'.now()->timestamp.'.zip"',
            'X-Accel-Buffering'   => 'no',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Pragma'              => 'no-cache',
        ];

        if ($contentLength !== '') {
            $headers['Content-Length'] = $contentLength;
        }

        // 5) Stream the ZIP directly to the client
        return response()->stream(function () use ($body) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            while (!$body->eof()) {
                echo $body->read(8192);
                flush();
            }
            $body->close();
        }, 200, $headers);
    }
}

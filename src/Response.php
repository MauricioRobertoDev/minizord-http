<?php

namespace Minizord\Http;

use InvalidArgumentException;
use Minizord\Http\Contract\ResponseInterface;

class Response implements ResponseInterface
{
    use MessageTrait;

    private const HTTP_PHRASES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',

        422 => 'Misdirected Request',
        422 => 'Unprocessable Content',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /**
     * Status code.
     *
     * @var int
     */
    private $statusCode;

    /**
     * Motivo http.
     *
     * @var string
     */
    private $reasonPhrase;

    /**
     * Representação de uma resposta de saída do lado do servidor.
     *
     * @param int         $status
     * @param array       $headers
     * @param string      $body
     * @param string      $version
     * @param string|null $reason
     */
    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', string $reason = null)
    {
        if ($body) {
            $this->body = new Stream($body);
        }

        $this->statusCode = $status;
        $this->setHeaders($headers);

        if (!$reason && isset(self::HTTP_PHRASES[$this->statusCode])) {
            $reason = self::HTTP_PHRASES[$status];
        }

        $this->validateProtocolVersion($version);
        $this->reasonPhrase = $reason ?? '';
        $this->protocol     = $version;
    }

    /**
     * Retorna o status code.
     *
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * Retorna uma nova instância com o status code passado.
     *
     * @param  int    $code
     * @param  string $reasonPhrase
     * @return self
     */
    public function withStatus($code, $reasonPhrase = '') : self
    {
        if (!is_int($code) && !is_string($code)) {
            throw new InvalidArgumentException('O status code precisa ser um número inteiro');
        }

        $code = (int) $code;

        if ($code < 100 || $code > 599) {
            throw new InvalidArgumentException('O status code precisa estar entre 100 e 599. O status passado foi: ' . $code);
        }

        $clone             = clone $this;
        $clone->statusCode = $code;

        if (!$reasonPhrase && isset(self::HTTP_PHRASES[$clone->statusCode])) {
            $reasonPhrase = self::HTTP_PHRASES[$clone->statusCode];
        }

        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    /**
     * Retorna a reason phrase, motivo ex. OK, Not Found, Bad Request...
     *
     * @return string
     */
    public function getReasonPhrase() : string
    {
        return $this->reasonPhrase;
    }
}

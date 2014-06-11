<?php
namespace Phpforce\RestClient;

use Phpforce\SoapClient\ClientInterface;
use Guzzle\Http\Client as GuzzleClient;
use Phpforce\SoapClient\Result\LoginResult;

/**
 * A client for the Salesforce REST API
 */
class Client
{
    /**
     * SOAP client
     *
     * @var ClientInterface
     */
    protected $soapClient;

    /**
     * Constructor
     *
     * @param ClientInterface $soapClient
     */
    public function __construct(ClientInterface $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    /**
     * Perform call to Salesforce REST API
     *
     * @param string $method
     * @param array $arguments
     *
     * @return array The JSON response as an array
     */
    public function call($method, $arguments = null)
    {
        $request = $this->getClient()->get($method);
        $request->setHeader('Authorization', 'Bearer ' . $this->getSessionId());
        $request->getQuery()->merge($arguments);

        $response = $request->send();

        return $response->json();
    }

    /**
     * Perform post to Salesforce REST API
     *
     * @param string $uri
     * @param array  $arguments
     *
     * @return array The JSON response as an array
     */
    public function post($uri, array $arguments = array())
    {
        $request = $this->getClient()->post($uri);
        $request->setHeader('Authorization', 'Bearer ' . $this->getSessionId());
        $request->getParams()->merge($arguments);

        $response = $request->send();

        return $response->json();
    }

    /**
     * Get Guzzle client
     *
     * @return \Guzzle\Http\Client
     */
    protected function getClient()
    {
        return new GuzzleClient(
            sprintf(
                'https://%s.salesforce.com/services/apexrest',
                $this->getLoginResult()->getServerInstance()
            )
        );
    }

    /**
     * Get login result from SOAP client
     *
     * @return LoginResult
     */
    protected function getLoginResult()
    {
        return $this->soapClient->getLoginResult();
    }

    /**
     * Get current session id through Salesforce SOAP API
     *
     * @return string
     */
    protected function getSessionId()
    {
        return $this->getLoginResult()->getSessionId();
    }
}

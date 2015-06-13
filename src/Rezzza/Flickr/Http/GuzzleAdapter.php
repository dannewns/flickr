<?php

namespace Rezzza\Flickr\Http;

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;

/**
 * GuzzleAdapter
 *
 * @uses AdapterInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class GuzzleAdapter implements AdapterInterface
{
    
    private $client;
    
    public function __construct()
    {
        if (!class_exists('\Guzzle\Http\Client')) {
            throw new \LogicException('Please, install guzzle/http before using this adapter.');
        }
        
        $this->client  = new Client('', array('redirect.disable' => true));
    }
    
    /**
     * {@inheritdoc}
     */
    public function post($url, array $data = array(), array $headers = array())
    {
        $request = $this->client->post($url, $headers, $data);
        // flickr does not supports this header and return a 417 http code during upload
        $request->removeHeader('Expect');

        $response = $request->send();

        if ($response->isSuccessful()) {

            switch($response->getContentType()){

                case "application/json":
                    return $response->json();
                    break;

                case "text/xml; charset=utf-8":
                    return $response->xml();
                    break;

            }

        }

    }

    /**
     * @param array $requests
     * An array of Requests
     * Each Request is an array with keys: url, data and headers
     *
     * @return \SimpleXMLElement[]
     */
    public function multiPost(array $requests)
    {
        $multi_request = $this->client->getCurlMulti();
        foreach ($requests as &$request) {
            $request = $this->client->post($request['url'], $request['headers'], $request['data']);
            $multi_request->add($request);
        }
        unset($request);

        $multi_request->send();

        $responses = array();
        /** @var RequestInterface[] $requests */
        foreach ($requests as $request) {
            switch($request->getResponse()->getContentType()){

                case "application/json":
                    $responses[] = $request->getResponse()->json();
                    break;

                case "text/xml; charset=utf-8":
                    $responses[] = $request->getResponse()->xml();
                    break;

            }
        }

        return $responses;
    }
    
    /**
     * @return $client
     */
    public function getClient()
    {
        return $this->client;
    }

}

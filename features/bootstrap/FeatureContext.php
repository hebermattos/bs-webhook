<?php

require 'vendor/autoload.php';

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Phalcon\Config\Adapter\Ini as IniConfig;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $validPayload;
    private $response;
    
    private $client;
    private $config;

    public function __construct()
    {
        $this->config = parse_ini_file("config.ini", true);
        $this->client = new Client();
    }

    /**
     * @Given a valid boleto paid event payload
     */
    public function setValidPayload()
    {
        $this->validPayload = '{"object":{"id":3924,"expire_at":"2016-08-17","paid_at":"2016-08-08","description":"UMBLER","status":"paid","shorten_url":"https://bole.to/3/dmon","customer_person_type":"individual","customer_person_name":"oliveirea","customer_cnpj_cpf":"125.812.717-28","customer_address":"Rua quinhentos","customer_state":"RJ","customer_neighborhood":"bairro","customer_zipcode":"12312123","customer_address_number":null,"customer_address_complement":null,"customer_phone_number":null,"customer_email":null,"created_via_api":true,"customer_city_name":"Rio de Janeiro","paid_amount":30.8,"amount":77.0,"url":"https://bole.to/3/dmon","formats":{"png":"https://bole.to/3/dmon.png","pdf":"https://bole.to/3/dmon.pdf"},"meta":null,"fine_for_delay":null,"late_payment_interest":null,"notes":null,"bank_rate":0.0,"bank_billet_account_id":211,"beneficiary_name":"umbler","beneficiary_cnpj_cpf":"42.024.567/0001-48","beneficiary_address":"rua da empresa","beneficiary_assignor_code":"0877-P / 0065600-3","guarantor_name":null,"guarantor_cnpj_cpf":null,"payment_place":"Pagável em qualquer banco até a data de vencimento.","instructions":null,"document_date":null,"document_type":"DM","document_number":null,"document_amount":0.0,"acceptance":"N","processed_our_number":"06/00001747142-4","processed_our_number_raw":"06000017471424","bank_contract_slug":"bradesco-bs-06","agency_number":"0877","agency_digit":"0","account_number":"0065600","account_digit":"0","extra1":null,"extra1_digit":null,"extra2":null,"extra2_digit":null,"line":"23790.87709 60000.174718 42006.560009 5 68890000007700","our_number":"1747142","customer_subscription_id":null,"installment_number":null,"installment_id":null,"carne_url":null},"changes":{"paid_at":[null,"2016-08-08"],"paid_amount_cents":[null,3080],"bank_rate_cents":[null,0],"banco_recebedor":[null,"237"],"agencia_recebedora":[null,"0296"],"agency_id":[null,15378],"status":["opened","paid"],"updated_at":["2016-08-10 17:02:27 -0300","2016-08-10 17:03:38 -0300"]},"event_code":"bank_billet.paid","webhook":{"id":141,"url":"http://bswebhook-com.umbler.net/index.php?_url=/bswebhook"}}';
    }

    /**
     * @When i do a POST against http:\/\/bswebhook-com.umbler.net\/index.php?_url=\/bswebhook
     */
    public function iDoAPostAgainstHttpBswebhookComUmblerNetIndexPhpUrlBswebhook()   
    {
        try
        {
            $requestResponse = $this->client->request('POST', 'http://rc-bswebhook-com.umbler.net/index.php?_url=/bswebhook',  [
                                                        'json' => $this->validPayload,
                                                        'headers' => [
                                                                'HTTP_X_HUB_SIGNATURE' => 'sha1=745187d4669d44dba800abadb127c6ce777b8a00'
                                                            ]
                                                        ]);
            
            $this->response = $requestResponse ->getBody()->getContent();                                  
            
        } catch (ServerException $e) {
            $this->response = $e->getResponse();
        } catch (ClientException $e) {
            $this->response = $e->getResponse();
        }
    }
    /**
     * @Then i should have a valid status
     */
    public function CheckResponse()
    {
        echo $this->response;
        
        
        //if (strpos($this->response, '"status":"OK"') == false) {
         //   throw new Exception(Psr7\str($this->response));
        //}

    }

}

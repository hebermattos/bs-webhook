Feature: Boleto account
  
  Scenario: Receive Boleto Paid Event from Boleto Simples with invalid token
    Given a valid boleto paid event payload
    And with a invalid token
    When i do a POST against http://bswebhook-com.umbler.net/index.php?_url=/bswebhook
    Then i should have a not authorized status
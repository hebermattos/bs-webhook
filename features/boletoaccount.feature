Feature: Boleto account
  
  Scenario: Receive POST
    Given a valid boleto paid event payload
    When i do a POST against http://bswebhook-com.umbler.net/index.php?_url=/bswebhook
    Then i should have a valid status



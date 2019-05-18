

[![Build Status](https://travis-ci.org/hurnell/postcode-api-bundle.svg?branch=master)](https://travis-ci.org/hurnell/postcode-api-bundle) [![Coverage Status](https://coveralls.io/repos/github/hurnell/postcode-api-bundle/badge.svg)](https://coveralls.io/github/hurnell/postcode-api-bundle) [![License](https://poser.pugx.org/hurnell/postcode-api-bundle/license)](https://packagist.org/packages/hurnell/postcode-api-bundle) [![Latest Stable Version](https://poser.pugx.org/hurnell/postcode-api-bundle/v/stable)](https://packagist.org/packages/hurnell/postcode-api-bundle)

# postcode-api-bundle

A Symfony 4 bundle to access Dutch postcode API at [Postcode API (postcodeapi.nu)](https://www.postcodeapi.nu/). Creates a PostcodeModel object based on postcode, house number & number extra combination.

## Characteristics/Requirements
Search based on postcode, house number AND house number extra. Note that some combinations of postcode and house number require a house number extra and without this extra value the address does NOT EXIST:
* ('2011XA', 20, '') is not a valid combination. For this combination of postcode and house number, extra must be 'A', 'RD' or 'ZW'.
* For the values ('2011XA', 20, '') the bundle will return an InvalidNumberExtraException with the following message: "House number extra must be (A, RD, ZW) for this combination of postcode and house number."

## Installation
1. Download via composer.
2. Enable bundle by adding class reference to config/bundles.php (if composer did not do that for you).
3. Create yaml configuration config/packages/hurnell_postcode_api.yaml with reference to your api_key.

### 1 - Download via composer
```bash
composer require hurnell/postcode-api-bundle:*
```
### 2 - Enable bundle
```php
# config/bundles.php

Hurnell\PostcodeApiBundle\HurnellPostcodeApiBundle::class => ['all' => true],
```
### 3 - Configure with API key
```yaml
# config/packages/hurnell_postcode_api.yaml

hurnell_postcode_api:
    api_key: 'your_api_key'
```

## Usage

Autowiring is enabled by default so in a controller action (or constructor of other classes)
```php
<?php

use Hurnell\PostcodeApiBundle\Service\PostcodeApiClient;
// use Exception classes

class MyController extends AbstractController {
    
    public function getPostcodeAction(PostcodeApiClient $client){
        $form = $this->createForm(PostcodeFormType::class);
        
        try {
            $postcodeModel = $client
                ->makeRequest(
                    '2011XC',
                     20,
                    'RD'
                )
                ->populatePostcodeModel();
            $postcodeModel->getStreet();       // Doelstraat
            $postcodeModel->getCity();         // Haarlem
            // $postcodeModel-> get etc etc
            // json response
            // return $this->json($postcodeModel->toJson());
        } catch (InvalidApiResponseException|InvalidPostcodeException $e) {
            // handle exception
        } catch (InvalidHouseNumberException $e) {
            // handle exception
        } catch (InvalidNumberExtraException $e) {
            // handle exception
            $form->get('extra')->addError(new FormError($e->getMessage()));
        }
    }
}
```




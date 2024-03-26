
# Inventory Bundle

I recorded a video showing the application running, you can watch it [here](https://www.youtube.com/watch?v=FNidmKk1OOs).
I go through the application, run the inventory command, run the tests, update and create stocks, etc.

## Installation

#### Optional

>  Symfony Flex - If you want to use Symfony Flex to auto create required config files for you, please add the `plinio-cardoso/symfony-recipes` to your composer json file, if not you can add it manually, see step 2.

```
"extra": {  
    "symfony": {
        "endpoint": ["https://api.github.com/repos/plinio-cardoso/symfony-recipes/contents/index.json", "flex://defaults"],
        "allow-contrib": true,
        "require": "7.0.*"
    }  
}
```
#### Step 1 - Download the Inventory Bundle using composer

```bash  
$ composer require plinio-cardoso/inventory-bundle "1.0"
```

#### Step 2 - Add the config files manually if you are not using Symfony Flex

> config/routes/inventory.yaml
```
inventory:  
    resource: '@InventoryBundle/config/routing.yml'
```

> config/packages/inventory.yaml
```
inventory:  
    out_of_stock_notification:  
        from: 'system@gmail.com'  
        to: 'admin@gmail.com'  
        subject: 'Product out of stock alert!!!'
```

## Usage

Website: http://localhost:8080/
Mail Catcher: http://localhost:1080/

Note that you need to run the `messenger:consumer` command to consume events for Stock updates to receive the out-of-stock email.

## Unit/Application Tests

#### Command to run only the inventory bundle tests
```
$ php bin/phpunit vendor/plinio-cardoso/inventory-bundle
```

## Notes

**Unit/Application Tests:** In this module I created only unit tests for the most important classes (services) to show my knowledge of this, however, in a real-world scenario I would create all unit tests for all classes and application tests for all controllers/view to achieve 100% of coverage.

**Docker:** The docker folder in this module has an example of the current docker setup that I used to run this module in a Symfony application to show my knowledge of docker.

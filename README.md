## Getting started

### Creds
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```

## Running the application
```
docker exec -it app bash
composer install
php artisan key:generate
php artisan migrate
```

## Product table design

| column | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `id` | `primary:uuid` | product id |
| `name` | `string` | name |
| `price` | `bigInt` | price |
| `currency` | `unsignedInteger` | currency |
| `timestamps` | `timestamps` | created_at, updated_at |


## API Reference

### Create a product
Operation: How money object are stored in the table

```http
  POST http://localhost:8080/api/products
```
Sample payload
```
{
    "name": "ISK_0",
    "price": 100,
    "currency": 66
}
```

| Body param | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. name of product |
| `price` | `numeric` | **Required**. Must be according to currency precision |
| `currency` | `int` | **Required**. From Currency enum |


### 
### Convert product currency
Operation: Converting money currency to another

```http
  POST http://localhost:8080/api/products/{product_id}/convert
```
Sample payload
```
{
    "currency": 45
}
```

| URL param | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `product_id` | `uuid` | **Required**. product id |

| Body param | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `currency` | `int` | **Required**. From Currency enum |


### 
### Add product to cart
Operation: addition by adding product money objects

```http
  POST http://localhost:8080/api/cart/add
```
Sample payload
```
{
    "product_ids": [
        "bb27fda1-7ebb-4478-b833-b579b2d02ac0",
        "7cfd4860-023b-45b2-a0af-0ccd0557f6d3"
    ]
}
```
| Body param | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `product_ids` | `array['uuids']` | **Required**. array of product uuids |


### 
### Cart flat discount
Operation: subtraction of total money objects in the cart by flat discount(as money object)

```http
  POST http://localhost:8080/api/cart/apply-flat-discount
```
Sample payload
```
{
    "product_ids": [
        "3360718d-3a29-4574-8f1a-85381718e863",
    ],
    "discount": 10.0
}
```
| Body param | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `product_ids` | `array['uuids']` | **Required**. array of product uuids |
| `discount` | `numeric` | **Required**. cart discount |

### 
### Cart product quantity
Operation: multiplication of quantity(as money object) to the product price

```http
  POST http://localhost:8080/api/cart/add-with-quantity
```
Sample payload
```
{
    "product_id": "bb27fda1-7ebb-4478-b833-b579b2d02ac0",
    "quantity": 3.34 //temporarily at float for experiments
}
```
| Body param | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `product_id` | `uuid` | **Required**. product uuid |
| `quantity` | `numeric` | **Required**. product quantity |


### 
### Payment installment
Operation: division of terms(as money object) to the cart total.

```http
  POST http://localhost:8080/api/cart/installments
```
Sample payload
```
{
    "product_ids": ["bb27fda1-7ebb-4478-b833-b579b2d02ac0"],
    "installments": 3
}

```
| Body param | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `product_ids` | `array['uuids]` | **Required**. array product uuid |
| `installments` | `integer` | **Required**. term of installment |


### 
### Cart percent discount
Operation: take discount in percent off the money object

```http
  POST http://localhost:8080/api/cart/apply-percent-discount
```
Sample payload
```
{
    "product_ids": [
        "3360718d-3a29-4574-8f1a-85381718e863"
    ],
    "discount_percentage": 20.1
}
```
| Body param | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `product_ids` | `array['uuids]` | **Required**. array product uuid |
| `discount_percentage` | `numeric` | **Required**. percentage discount 1-100 |


### 
### Bonus features
Operation: bonus operations from interactions of money object(total, lowest, highest, average)

```http
  POST http://localhost:8080/api/money/statistics
```
Sample payload
```
{
    "product_ids": [
        "45e14612-4e3b-4c7c-a7b4-4a9491035af7",
        "3c671492-d580-4086-b2fa-42c92b5bff40",
        "c811c125-3d84-4a0e-89b3-e31add211c07"
    ]
}
```
| Body param | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `product_ids` | `array['uuids]` | **Required**. array product uuid |

### POSTMAN COLLECTION
Download postman collection in root startselect.postman_collection.json

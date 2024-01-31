# User Interface Preview
Few image of this web app User Interface
| Add JSON Page | Edit JSON Page |
|---|---|
| ![Add Image](https://raw.githubusercontent.com/baguswijaksono/json-storage/main/imagedocs/add.png) | ![Edit Image](https://raw.githubusercontent.com/baguswijaksono/json-storage/main/imagedocs/edit.png) |

| Get JSON Page | Home Page |
|---|---|
| ![Get JSON Image](https://raw.githubusercontent.com/baguswijaksono/json-storage/main/imagedocs/getjson.png) | ![Home Image](https://raw.githubusercontent.com/baguswijaksono/json-storage/main/imagedocs/home.png) |

| Login Page | Register Page |
|---|---|
| ![Login Image](https://raw.githubusercontent.com/baguswijaksono/json-storage/main/imagedocs/login.png) | ![Register Image](https://raw.githubusercontent.com/baguswijaksono/json-storage/main/imagedocs/register.png) |


# Fetching Data from an API

## Get JSON

| Parameter    | Description                                                                                                                                                                                   |
| ------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `id` (int) |  Example: `GET /?page=json&id=4` |


## PHP Example

The PHP example demonstrates fetching data from an API using `file_get_contents` and handling the response.

```php
<?php

$url = 'URL'; // Replace with your API endpoint
$response = file_get_contents($url);
if ($response === false) {
    die('Error fetching data');
}

$data = json_decode($response, true);

if ($data === null) {
    die('Error decoding JSON');
}

?>

```
## JavaScript Example
The JavaScript example uses fetch to retrieve data from an API and handles the response asynchronously.

```js
const apiUrl = 'URL'; // Replace with your API endpoint

fetch(apiUrl)
  .then(response => {
    if (!response.ok) {
      throw new Error(`Network response was not ok: ${response.status}`);
    return response.json();
  })
  .then(data => {
    console.log('Fetched data:', data);
  })
  .catch(error => {
    console.error('There was a problem with the fetch operation:', error);
  });
```

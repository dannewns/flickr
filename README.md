Flickr Api Wrapper for PHP5.3
=============================

#updates

This package now also has support for JSON, by simply passing in `'format' => 'json', 'nojsoncallback' => 1` into your
array the package will then return the result as json.


```php
$metadata = new Rezzza\Flickr\Metadata('api key', 'secret');
$metadata->setOauthAccess('access token', 'access token secret');

$factory  = new Rezzza\Flickr\ApiFactory($metadata, new Rezzza\Flickr\Http\GuzzleAdapter());

$xml = $factory->call('flickr.test.login');
$xml = $factory->call('flickr.photos.getInfo', array(
    'photo_id' => 1337,
));

$factory->upload('path/to/photo.png', 'my title');
```

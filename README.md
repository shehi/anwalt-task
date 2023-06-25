# Task

## Assumptions & Considerations

* **Trusted Files**: We assume all files we serve are indeed image files. Code doesn't have checks for that.
* **No RESTful API**: Since we don't care about RESTful API for this task, it has only one GET endpoint - the default `/`. Otherwise, it is possible to bring in `symfony/http-kernel`, create proper `ControllerResolver` and manage `Request -> ... -> Response` flow. 
* **Strategy Pattern Omitted**: Since this small implementation doesn't feature any Configuration boilerplate, I didn't implement Strategy pattern. To change the image library from ImageMagick to GD, `public/index.php` file will need updating. Passing `GdTransformer` instance instead of `ImagickTransformer` will do the trick.
* **Content-Disposition**: This header is set to `inline`, as a result the original filename won't have any effect. If this is made `attachment`, then browsers will force file download with original filename.
* **No HTTP Caching**: To keep things simple, no `ETag`, `Cache-Control` and similar caching headers were utilized in server side.
* **No Crop Origin**: To keep it simple, for `c_crop` transformation the origin point is assumed to be `x = 1000, y = 1000`.
* **Aspect Ratio** is ignored during `c_rescale`. As such, `Imagick::resizeImage()`'s `bestFit` parameter is set to `false`. 
* **Always two sizing qualifiers** will be passed - both width and height -, never one. For a potential `c_thumb` transformation, such capability can be introduced (e.g. as seen in Cloudinary).
* This implementation (although not necessary) separates Domain, Application and Infrastructure concerns. This can be seen in folder structure of `./src` folder.
  * _Domain_ contains services concerned with domain-logic, in our case, `ImageService`
  * _Infrastructure_ contains implementations of specific external solutions, in this case Gd and Imagick libs.
  * _Application_ usually contains ports and adapters responsible of connecting Domain to Infra, plus pure application concerns.
* **Tests** only cover `ImageService`. Due to time constraints, and their low value in terms of being tested, I didn't include `GdTransformer` and `ImagickTransformer` in tests.

## Setup

* If it is desired for Docker user (inside the `php` container) to have the same UID/GID as the Host-machine user (in order to avoid permission issues):
  ```
  export MYUID=$(id -u);
  export MYGID=$(id -g);
  ```
  **Skip this if Host UID and GID both are `1000`**.
* `docker-compose build`
* `docker-compose up -d` - `80:80` port attachment on `localhost`. **Make sure Host's port-80 is not occupied!**
* `docker-compose exec php bash -l` to enter the container terminal:
  * `XDEBUG_MODE=off composer install` to install dependencies.
  * `XDEBUG_MODE=coverage ./vendor/bin/phpunit tests` for tests with coverage
  * `XDEBUG_MODE=off ./vendor/bin/phpunit --no-coverage tests` for tests with no coverage

## Modifier Usage

* 2 Transformations: `c_rescale` & `c_crop`.
* Both require width (`w_123` format) and height (`h_123` format).
* As mentioned above, in _Assumptions_, `c_crop` assumes crop origin point to be `x = 1000, y = 1000` (for the sake of simplicity). Thus, this coordinate isn't configurable at the moment.
* URL format: `<host>/<filename>/<transformation>,<width>,<height>`.
* Example URLs to test:
  * http://localhost/e9b2sczowtx41.avif/c_rescale,w_400,h_400
  * http://localhost/e9b2sczowtx41.avif/c_crop,w_400,h_400
  * http://localhost/e9b2sczowtx41.jpg/c_rescale,w_400,h_400
  * http://localhost/e9b2sczowtx41.jpg/c_crop,w_400,h_400
  * http://localhost/e9b2sczowtx41.png/c_rescale,w_400,h_400
  * http://localhost/e9b2sczowtx41.png/c_crop,w_400,h_400
  * http://localhost/e9b2sczowtx41.webp/c_rescale,w_400,h_400
  * http://localhost/e9b2sczowtx41.webp/c_crop,w_400,h_400

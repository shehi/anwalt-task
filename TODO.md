# Introduction

Thank you for coming that far with our selection process and that you are willing to take part in it.
Keep in mind that there is no "right way" to do it. This assignment is designed to gauge your skills and give us an idea of how you approach tasks relevant to this position.

Good luck!

# Task description

Setup a small image service which can deliver images using a GET request and which are stored on the server.
It is possible to use different modifiers to change what will be returned.
Two modifiers must be implemented:
1. crop-modifier (will cut the image and will take height and width as parameters)
2. resize-modifier (resizes the images based on given height and width as parameters)

Further modifiers should be possible to integrate easily in code.
After you access an image you will be redirected to a beautified url (without modification parameters).

The services outputs images in the same file format (e.g. jpg) as they have been read.

Prepare a simple HTML-page which includes a resized and a cropped image.

# Example of image service usage

You want to retrieve image "dog.webp" in the size of 200px height and 200px width.
The original image on the server has the following dimensions: 1000px height, 1000px width

You trigger retrieving the image by using an url like:
http://your-image-service/dog.webp/<some-modification-parameters\>

You will get redirected to:
http://your-image-service/<generated-image-name\>.webp

# Guidelines

- Stay with simple GET-Request and parameters. It's not about a RESTful API.
- Docker as container management tool
- Implementation has to be based on vanilla PHP. Frameworks are not allowed - except for testing. Of course you can use libraries e.g. a router component. You can also use image manipulation libs as imagick or gd. However, the central image manipulation code should come from you. So how you structure modifiers and so on.
- Use object oriented programming
- Automated testing must be implemented
- Documentation exists how to use your service.

# Project delivery

- Project must have clear instructions on how to set up, install dependencies and run.
- Git should be used as a VCS. Please send us the link to the repository or a zip-file.
- Feel free to ask questions regarding the requirements.
- Be careful not to violate any image rights.

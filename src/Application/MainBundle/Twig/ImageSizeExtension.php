<?php
namespace Application\MainBundle\Twig;

class ImageSizeExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('image_size', array($this, 'imageSizeFilter')),
        );
    }

    public function imageSizeFilter($file, $option = null)
    {
        $size = @getimagesize(__DIR__ . '/../../../../web' . $file);

        if (!$size[0]) {
            return '';
        }

        if ($option == 'x') {
            // return WIDTHxHEIGHT
            return $size[0] . 'x' . $size[1];

        } else if ($option == 'width') {
            return $size[0];

        } else if ($option == 'height') {
            return $size[1];
        }

        // return html attributes
        return 'width="' . $size[0] . '" height="' . $size[1] . '"';
    }

    public function getName()
    {
        return 'image_size_extension';
    }
}
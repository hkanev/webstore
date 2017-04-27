<?php
/**
 * Created by PhpStorm.
 * User: Hristian
 * Date: 4/27/2017
 * Time: 7:18 PM
 */

namespace AdministrationBundle\Services;


use AdministrationBundle\Entity\Orders;
use AdministrationBundle\Entity\Product;

class UserProductManager
{
    private $imageUploader;

    /**
     * UserProductManager constructor.
     * @param $imageUploader
     */
    public function __construct(FileUploader $imageUploader)
    {
        $this->imageUploader = $imageUploader;
    }


    public function createUserProduct(Orders $order, $image, $oldImage, $category, $user)
    {
        $product = $order->getProduct();

        $newProduct = new Product();

        if($image == null){
            $imageName = $oldImage;
        } else {
            $imageName = $this->imageUploader->upload($image);
        }

        $newProduct->setImage($imageName);
        $newProduct->setQuantity($product->getQuantity());
        $newProduct->setCategory($category);
        $newProduct->setCreatedOn(new \DateTime());
        $newProduct->setDescription($product->getDescription());
        $newProduct->setName($product->getName());
        $newProduct->setPrice($product->getPrice());
        $newProduct->setSelelr($user);

        return $newProduct;
    }
}
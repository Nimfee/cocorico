<?php

/*
 * This file is part of the Cocorico package.
 *
 * (c) Cocolabs SAS <contact@cocolabs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cocorico\CoreBundle\Entity;

use Cocorico\CoreBundle\Model\BaseListingImage;
use Doctrine\ORM\Mapping as ORM;

/**
 * ListingCategoryImage
 *
 * @ORM\Entity
 *
 * @ORM\Table(name="listing_category_image", indexes={
 *    @ORM\Index(name="position_li_ca_idx", columns={"position"})
 *  })
 *
 */
class ListingCategoryImage extends BaseListingImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="ListingCategory", inversedBy="image")
     */
    private $listingCategory;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set listingCategory
     *
     * @param  \Cocorico\CoreBundle\Entity\ListingCategory $listingCategory
     * @return ListingCategoryImage
     */
    public function setListingCategory(ListingCategory $listingCategory = null)
    {
        $this->listingCategory = $listingCategory;

        return $this;
    }

    /**
     * Get $listingCategory
     *
     * @return \Cocorico\CoreBundle\Entity\ListingCategory
     */
    public function getListingCategory()
    {
        return $this->listingCategory;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
        }
    }

    public function __construct()
    {
        $this->position = 1;
    }
}

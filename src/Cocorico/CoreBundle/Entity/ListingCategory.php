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

use Cocorico\CoreBundle\Model\BaseListingCategory;
use Cocorico\CoreBundle\Model\BaseListingImage;
use Cocorico\CoreBundle\Model\ListingCategoryListingCategoryFieldInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ListingCategory
 *
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Tree(type="nested")
 *
 * @ORM\Entity(repositoryClass="Cocorico\CoreBundle\Repository\ListingCategoryRepository")
 *
 * @ORM\Table(name="listing_category")
 *
 */
class ListingCategory extends BaseListingCategory
{
    use ORMBehaviors\Translatable\Translatable;

    const SERVER_PATH_TO_IMAGE_FOLDER = '/server/path/to/images';

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="ListingCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="ListingCategory", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     *
     * @ORM\OneToMany(targetEntity="Cocorico\CoreBundle\Entity\ListingListingCategory", mappedBy="category", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $listingListingCategories;

    /**
     *
     * @ORM\OneToMany(targetEntity="Cocorico\CoreBundle\Model\ListingCategoryListingCategoryFieldInterface", mappedBy="category", cascade={"persist", "remove"})
     */
    private $fields;

    /**
     * For Asserts @see \Cocorico\CoreBundle\Validator\Constraints\ListingValidator
     *
     * @ORM\OneToOne(targetEntity="ListingCategoryImage", mappedBy="listingCategory", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "asc"})
     */
    private $image;

    public function __construct()
    {
        $this->listingListingCategories = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }

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
     * Set parent
     *
     * @param  \Cocorico\CoreBundle\Entity\ListingCategory $parent
     * @return ListingCategory
     */
    public function setParent(ListingCategory $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Cocorico\CoreBundle\Entity\ListingCategory
     */
    public function getParent()
    {
        return $this->parent;
    }


    /**
     * Add children
     *
     * @param  \Cocorico\CoreBundle\Entity\ListingCategory $children
     * @return ListingCategory
     */
    public function addChild(ListingCategory $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Cocorico\CoreBundle\Entity\ListingCategory $children
     */
    public function removeChild(ListingCategory $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add field
     *
     * @param  ListingCategoryListingCategoryFieldInterface $field
     * @return ListingCategory
     */
    public function addField($field)
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * Remove listings
     *
     * @param  ListingCategoryListingCategoryFieldInterface $field
     */
    public function removeField($field)
    {
        $this->fields->removeElement($field);
    }

    /**
     * Get fields
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|ListingCategoryListingCategoryFieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }


    /**
     * Add category
     *
     * @param  \Cocorico\CoreBundle\Entity\ListingListingCategory $listingListingCategory
     * @return ListingCategory
     */
    public function addListingListingCategory(ListingListingCategory $listingListingCategory)
    {
        if (!$this->listingListingCategories->contains($listingListingCategory)) {
            $this->listingListingCategories[] = $listingListingCategory;
        }

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Cocorico\CoreBundle\Entity\ListingListingCategory $listingListingCategory
     */
    public function removeListingListingCategory(ListingListingCategory $listingListingCategory)
    {
        $this->listingListingCategories->removeElement($listingListingCategory);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListingListingCategories()
    {
        return $this->listingListingCategories;
    }

    /**
     * @param ArrayCollection $listingListingCategories
     * @return ArrayCollection
     */
    public function setListingListingCategories(ArrayCollection $listingListingCategories)
    {
        $this->listingListingCategories = $listingListingCategories;
    }

    public function getName()
    {
        return $this->translate()->getName();
    }

    public function getDescription()
    {
        return $this->translate()->getDescription();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set image
     *
     * @param  \Cocorico\CoreBundle\Entity\ListingCategoryImage $image
     * @return ListingCategory
     */
    public function setImage(ListingCategoryImage $image)
    {
        $image->setListingCategory($this);
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return ListingCategoryImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist($image)
    {
        $this->manageFileUpload($image);
    }

    /**
     * @ORM\PrePersist
     */
    public function preUpdate($image)
    {
        $this->manageFileUpload($image);
    }

    public function manageFileUpload()
    {
        if (null === $this->getFile()) {
            return;
        }
        $this->getFile()->move(
            BaseListingImage::IMAGE_FOLDER,
            $this->getFile()->getClientOriginalName()
        );

        $listingCategoryImage = new ListingCategoryImage();
        $listingCategoryImage->setName($this->getFile()->getClientOriginalName());
        $this->setImage($listingCategoryImage);
        $this->setFile(null);
    }
}

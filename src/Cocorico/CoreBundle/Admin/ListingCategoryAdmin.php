<?php

/*
 * This file is part of the Cocorico package.
 *
 * (c) Cocolabs SAS <contact@cocolabs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cocorico\CoreBundle\Admin;

use Cocorico\CoreBundle\Entity\ListingCategory;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ListingCategoryAdmin extends Admin
{
    protected $translationDomain = 'SonataAdminBundle';
    protected $baseRoutePattern = 'listing-category';
    protected $locales;
    protected $bundles;

    // setup the default sort column and order
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'root, lft'
    );

    /**
     * @param string $name
     *
     * @return null|string|void
     */
    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'CocoricoSonataAdminBundle::list_outer_rows_mosaic.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    public function setLocales($locales)
    {
        $this->locales = $locales;
    }

    public function setBundlesEnabled($bundles)
    {
        $this->bundles = $bundles;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var ListingCategory $subject */
//        $subject = $this->getSubject();

        //Translations fields
        $titles = $descriptions = array();
        foreach ($this->locales as $i => $locale) {
            $titles[$locale] = array(
                'label' => 'Name',
                'required' => true
            );
            $descriptions[$locale] = array(
                'label' => 'Description',
                'required' => true
            );
        }

        $formMapper
            ->with('admin.listing_category.title')
            ->add(
                'translations',
                'a2lix_translations',
                array(
                    'locales' => $this->locales,
                    'required_locales' => $this->locales,
                    'fields' => array(
                        'name' => array(
                            'field_type' => 'text',
                            'locale_options' => $titles,
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'locale_options' => $descriptions,
                        ),
                        'slug' => array(
                            'field_type' => 'hidden'
                        )
                    ),
                    /** @Ignore */
                    'label' => 'Descriptions'
                )
            )
            ->add(
                'parent',
                null,
                array(
                    'label' => 'admin.listing_category.parent.label'
                )
            );

        if (array_key_exists("CocoricoListingCategoryFieldBundle", $this->bundles)) {
            $formMapper
                ->add(
                    'fields',
                    null,
                    array(
                        'label' => 'admin.listing_category.fields.label',
                        'disabled' => true,
                        'property' => 'field'
                    )
                );
        }
        $formMapper->add(
            'file',
            FileType::class,
            array(
                'required' => false,
                'label' => 'admin.listing_category.images.label'
            )
        );

        $formMapper
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'translations.name',
                null,
                array('label' => 'admin.listing_category.name.label')
            )
            ->add(
                'parent',
                null,
                array('label' => 'admin.listing_category.parent.label')
            )
            ->add(
                'translations.description',
                null,
                array('label' => 'admin.listing_category.description.label')
            );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.listing_category.name.label',
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.listing_category.description.label',
                    'template' => 'CocoricoSonataAdminBundle::list_description_field.html.twig',
                    'data_trans' => 'SonataAdminBundle'
                )
            )
            ->add(
                'image',
                null,
                array(
                    'label' => 'admin.listing_category.image.label',
                    'template' => 'CocoricoSonataAdminBundle::list_image_field.html.twig',
                )
            )
            ->addIdentifier(
                'parent',
                null,
                array('label' => 'admin.listing_category.parent.label')
            );

        if (array_key_exists("CocoricoListingCategoryFieldBundle", $this->bundles)) {
            $listMapper
                ->add(
                    'fields',
                    null,
                    array(
                        'label' => 'admin.listing_category.fields.label',
                        'associated_property' => 'field'
                    )
                );
        }

        $listMapper->add(
            '_action',
            'actions',
            array(
                'actions' => array(
                    //'show' => array(),
                    'edit' => array(),
                )
            )
        );
    }

    public function getExportFields()
    {
        return array(
            'Id' => 'id',
            'name' => 'name',
            'image' => 'image',
            'description' => 'description',
            'category' => 'parent'
        );
    }

    public function getDataSourceIterator()
    {
        $datagrid = $this->getDatagrid();
        $datagrid->buildPager();

        $dataSourceIt = $this->getModelManager()->getDataSourceIterator($datagrid, $this->getExportFields());
        $dataSourceIt->setDateTimeFormat('d M Y');

        return $dataSourceIt;
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions["delete"]);

        return $actions;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        //$collection->remove('create');
        //$collection->remove('delete');
    }
}

<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Communication\Form;

use ArrayObject;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\ProductOption\Communication\Form\Constraint\UniqueGroupName;
use Spryker\Zed\ProductOption\ProductOptionConfig;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @method \Spryker\Zed\ProductOption\Business\ProductOptionFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductOption\Communication\ProductOptionCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductOption\ProductOptionConfig getConfig()
 * @method \Spryker\Zed\ProductOption\Persistence\ProductOptionRepositoryInterface getRepository()
 */
class ProductOptionGroupForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_TAX_SET_FIELD = 'fkTaxSet';

    /**
     * @var string
     */
    public const FIELD_VALUES = 'productOptionValues';

    /**
     * @var string
     */
    public const FIELD_ID_PRODUCT_OPTION_GROUP = 'idProductOptionGroup';

    /**
     * @var string
     */
    public const FIELD_NAME = 'name';

    /**
     * @var string
     */
    public const FIELD_VALUE_TRANSLATIONS = 'productOptionValueTranslations';

    /**
     * @var string
     */
    public const FIELD_GROUP_NAME_TRANSLATIONS = 'groupNameTranslations';

    /**
     * @var string
     */
    public const OPTION_TAX_SETS = 'optionTaxSets';

    /**
     * @var string
     */
    public const OPTION_LOCALE = 'locale';

    /**
     * @var string
     */
    public const PRODUCTS_TO_BE_ASSIGNED = 'products_to_be_assigned';

    /**
     * @var string
     */
    public const PRODUCTS_TO_BE_DE_ASSIGNED = 'products_to_be_de_assigned';

    /**
     * @var string
     */
    public const PRODUCT_OPTION_VALUES_TO_BE_REMOVED = 'product_option_values_to_be_removed';

    /**
     * @var string
     */
    public const ALPHA_NUMERIC_PATTERN = '/^[a-z0-9\.\_]+$/';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addNameField($builder)
            ->addValuesFields($builder, $options[static::OPTION_LOCALE])
            ->addValueTranslationFields($builder)
            ->addGroupNameTranslationFields($builder)
            ->addTaxSetField($builder, $options)
            ->addIdProductOptionGroup($builder)
            ->addProductsToBeAssignedField($builder)
            ->addProductsToBeDeAssignedField($builder)
            ->addProductOptionValuesToBeRemoved($builder);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(static::OPTION_TAX_SETS);

        $resolver->setDefaults([
            static::OPTION_LOCALE => null,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_NAME, TextType::class, [
            'label' => 'Group name translation key',
            'required' => true,
            'attr' => [
                'placeholder' => ProductOptionConfig::PRODUCT_OPTION_GROUP_NAME_TRANSLATION_PREFIX . '(your key)',
            ],
            'constraints' => [
                new NotBlank(),
                new UniqueGroupName([
                    UniqueGroupName::OPTION_PRODUCT_OPTION_QUERY_CONTAINER => $this->getQueryContainer(),
                ]),
                new Regex([
                    'pattern' => static::ALPHA_NUMERIC_PATTERN,
                    'message' => 'Invalid key provided. Valid values "a-z", "0-9", ".", "_".',
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param string $currentLocale
     *
     * @return $this
     */
    protected function addValuesFields(FormBuilderInterface $builder, string $currentLocale)
    {
        $builder->add(static::FIELD_VALUES, CollectionType::class, [
            'entry_type' => ProductOptionValueForm::class,
            'entry_options' => [
                static::OPTION_LOCALE => $currentLocale,
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'constraints' => [
                new Callback([
                    'callback' => function (ArrayObject $values, ExecutionContextInterface $context) {
                        if (count($values) === 0) {
                            $context->buildViolation('No option values added.')
                                ->addViolation();
                        }
                    },
                ]),
            ],
        ]);

        $builder->get(static::FIELD_VALUES)
            ->addModelTransformer($this->getFactory()->createArrayToArrayObjectTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addValueTranslationFields(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_VALUE_TRANSLATIONS, CollectionType::class, [
            'entry_type' => ProductOptionTranslationForm::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
        ]);

        $builder->get(static::FIELD_VALUE_TRANSLATIONS)
            ->addModelTransformer($this->getFactory()->createArrayToArrayObjectTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addGroupNameTranslationFields(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_GROUP_NAME_TRANSLATIONS, CollectionType::class, [
            'entry_type' => ProductOptionTranslationForm::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
        ]);

        $builder->get(static::FIELD_GROUP_NAME_TRANSLATIONS)
            ->addModelTransformer($this->getFactory()->createArrayToArrayObjectTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addTaxSetField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add(
            static::FIELD_TAX_SET_FIELD,
            ChoiceType::class,
            [
                'label' => 'Tax set',
                'choices' => array_flip($options[static::OPTION_TAX_SETS]),
            ],
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdProductOptionGroup(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_PRODUCT_OPTION_GROUP, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addProductsToBeAssignedField(FormBuilderInterface $builder)
    {
        $builder
            ->add(static::PRODUCTS_TO_BE_ASSIGNED, HiddenType::class, [
                'attr' => [
                    'id' => static::PRODUCTS_TO_BE_ASSIGNED,
                ],
            ]);

        $builder->get(static::PRODUCTS_TO_BE_ASSIGNED)
            ->addModelTransformer($this->getFactory()->createStringToArrayTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addProductsToBeDeAssignedField(FormBuilderInterface $builder)
    {
        $builder
            ->add(static::PRODUCTS_TO_BE_DE_ASSIGNED, HiddenType::class, [
                'attr' => [
                    'id' => static::PRODUCTS_TO_BE_DE_ASSIGNED,
                ],
            ]);

        $builder->get(static::PRODUCTS_TO_BE_DE_ASSIGNED)
            ->addModelTransformer($this->getFactory()->createStringToArrayTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addProductOptionValuesToBeRemoved(FormBuilderInterface $builder)
    {
        $builder
            ->add(static::PRODUCT_OPTION_VALUES_TO_BE_REMOVED, HiddenType::class, [
                'attr' => [
                    'id' => static::PRODUCT_OPTION_VALUES_TO_BE_REMOVED,
                ],
            ]);

        $builder->get(static::PRODUCT_OPTION_VALUES_TO_BE_REMOVED)
            ->addModelTransformer($this->getFactory()->createStringToArrayTransformer());

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'product_option_general';
    }

    /**
     * @deprecated Use {@link getBlockPrefix()} instead.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}

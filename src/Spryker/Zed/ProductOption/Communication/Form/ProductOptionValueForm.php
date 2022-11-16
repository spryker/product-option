<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Communication\Form;

use Generated\Shared\Transfer\ProductOptionValueTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\ProductOption\Communication\Form\Constraint\UniqueOptionValueSku;
use Spryker\Zed\ProductOption\Communication\Form\Constraint\UniqueValue;
use Spryker\Zed\ProductOption\ProductOptionConfig;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @method \Spryker\Zed\ProductOption\Business\ProductOptionFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductOption\Communication\ProductOptionCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductOption\ProductOptionConfig getConfig()
 * @method \Spryker\Zed\ProductOption\Persistence\ProductOptionRepositoryInterface getRepository()
 */
class ProductOptionValueForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_VALUE = 'value';

    /**
     * @var string
     */
    public const FIELD_SKU = 'sku';

    /**
     * @var string
     */
    public const FIELD_PRICES = 'prices';

    /**
     * @var string
     */
    public const FIELD_ID_PRODUCT_OPTION_VALUE = 'idProductOptionValue';

    /**
     * @var string
     */
    public const FIELD_OPTION_HASH = 'optionHash';

    /**
     * @var string
     */
    public const OPTION_AMOUNT_PER_STORE = 'amount_per_store';

    /**
     * @var string
     */
    public const ALPHA_NUMERIC_PATTERN = '/^[a-z0-9\.\_]+$/';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addNameField($builder)
            ->addSkuField($builder)
            ->addPricesField($builder, $options[ProductOptionGroupForm::OPTION_LOCALE])
            ->addIdProductOptionValue($builder)
            ->addFormHash($builder);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => ProductOptionValueTransfer::class,
            'constraints' => [
                new UniqueValue([
                    UniqueValue::OPTION_PRODUCT_OPTION_QUERY_CONTAINER => $this->getQueryContainer(),
                ]),
                new UniqueOptionValueSku([
                    UniqueOptionValueSku::OPTION_PRODUCT_OPTION_QUERY_CONTAINER => $this->getQueryContainer(),
                ]),
            ],
            ProductOptionGroupForm::OPTION_LOCALE => null,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_VALUE, TextType::class, [
            'label' => 'Option name translation key',
            'required' => true,
            'attr' => [
                'placeholder' => ProductOptionConfig::PRODUCT_OPTION_TRANSLATION_PREFIX . '(your key)',
            ],
            'constraints' => [
                new NotBlank(),
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
     *
     * @return $this
     */
    protected function addSkuField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_SKU, TextType::class, [
            'label' => 'Sku',
            'required' => true,
            'constraints' => [
                new NotBlank(),
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
    protected function addPricesField(FormBuilderInterface $builder, string $currentLocale)
    {
        $builder->add(
            static::FIELD_PRICES,
            $this->getFactory()->getMoneyCollectionFormTypePlugin()->getType(),
            [
                static::OPTION_AMOUNT_PER_STORE => true,
            ],
        );

        $this->expandPricesFieldWithLocale($builder, $currentLocale);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdProductOptionValue(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_PRODUCT_OPTION_VALUE, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addFormHash(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_OPTION_HASH, HiddenType::class);

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'product_option';
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

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param string $currentLocale
     *
     * @return void
     */
    protected function expandPricesFieldWithLocale(FormBuilderInterface $builder, string $currentLocale): void
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($currentLocale) {
            $pricesField = $event->getForm()->get(static::FIELD_PRICES);
            foreach ($pricesField->all() as $childIndex => $childField) {
                $childFieldConfig = $childField->getConfig();
                if (!$childFieldConfig->hasOption(ProductOptionGroupForm::OPTION_LOCALE)) {
                    continue;
                }

                $childFieldOptions = $childFieldConfig->getOptions();
                $childFieldOptions[ProductOptionGroupForm::OPTION_LOCALE] = $currentLocale;

                $pricesField->remove($childIndex);
                $pricesField->add($childIndex, get_class($childFieldConfig->getType()->getInnerType()), $childFieldOptions);
            }
        });
    }
}

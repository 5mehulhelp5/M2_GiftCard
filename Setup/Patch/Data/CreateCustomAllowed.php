<?php

namespace Market\GiftCard\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Market\GiftCard\Model\Attributes;
use Market\GiftCard\Model\Type\GiftCard;

class CreateCustomAllowed implements DataPatchInterface, PatchRevertableInterface
{

    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @{inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            Attributes::IS_CUSTOM_Allowed,
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Is Custom Amount Allowed',
                'input' => 'select',
                'class' => '',
                'source' => Boolean::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => GiftCard::TYPE_CODE,
                'group' => 'General',
            ]
        );

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @{inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @{inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    public function revert()
    {
        $this->moduleDataSetup->startSetup();

        /** @var EavSetup $eav */
        $eav = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $this->moduleDataSetup->endSetup();
    }
}

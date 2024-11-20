<?php

namespace Perspective\Game\Setup\Patch\Data;

use Magento\Customer\Model\Group;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddCustomerGroup implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var Group
     */
    private Group $groupModel;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Group $groupModel
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Group $groupModel
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->groupModel = $groupModel;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function apply(): void
    {
        $existingGroup = $this->groupModel->load('tic_tac', 'customer_group_code');
        if ($existingGroup->getId()) {
            return;
        }

        $this->groupModel->setCode('tic_tac')
            ->setTaxClassId(3)
            ->save();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function revert(): void
    {
        $existingGroup = $this->groupModel->load('tic_tac', 'customer_group_code');
        if ($existingGroup->getId()) {
            $existingGroup->delete();
        }
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}

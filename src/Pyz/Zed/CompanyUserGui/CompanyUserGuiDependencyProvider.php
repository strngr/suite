<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\CompanyUserGui;

use Spryker\Zed\CompanyBusinessUnitGui\Communication\Plugin\BusinessUnitFieldCustomerCompanyAttachFormExpanderPlugin;
use Spryker\Zed\CompanyBusinessUnitGui\Communication\Plugin\CompanyBusinessUnit\CompanyBusinessUnitCompanyUserTableConfigExpanderPlugin;
use Spryker\Zed\CompanyBusinessUnitGui\Communication\Plugin\CompanyBusinessUnit\CompanyBusinessUnitCompanyUserTablePrepareDataExpanderPlugin;
use Spryker\Zed\CompanyBusinessUnitGui\Communication\Plugin\CompanyUser\CompanyBusinessUnitFormExpanderPlugin;
use Spryker\Zed\CompanyRoleGui\Communication\Plugin\CompanyRole\CompanyRoleCompanyUserTableConfigExpanderPlugin;
use Spryker\Zed\CompanyRoleGui\Communication\Plugin\CompanyRole\CompanyRoleCompanyUserTablePrepareDataExpanderPlugin;
use Spryker\Zed\CompanyRoleGui\Communication\Plugin\CompanyRoleFieldCustomerCompanyAttachFormExpanderPlugin;
use Spryker\Zed\CompanyRoleGui\Communication\Plugin\CompanyUser\CompanyRoleFromExpanderPlugin;
use Spryker\Zed\CompanyUserGui\CompanyUserGuiDependencyProvider as SprykerCompanyUserGuiDependencyProvider;

class CompanyUserGuiDependencyProvider extends SprykerCompanyUserGuiDependencyProvider
{
    /**
     * @return \Spryker\Zed\CompanyUserGuiExtension\Dependency\Plugin\CompanyUserTableConfigExpanderPluginInterface[]
     */
    protected function getCompanyUserTableConfigExpanderPlugins(): array
    {
        return [
            new CompanyRoleCompanyUserTableConfigExpanderPlugin(),
            new CompanyBusinessUnitCompanyUserTableConfigExpanderPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\CompanyUserGuiExtension\Dependency\Plugin\CompanyUserTablePrepareDataExpanderPluginInterface[]
     */
    protected function getCompanyUserTablePrepareDataExpanderPlugins(): array
    {
        return [
            new CompanyRoleCompanyUserTablePrepareDataExpanderPlugin(),
            new CompanyBusinessUnitCompanyUserTablePrepareDataExpanderPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\CompanyUserGuiExtension\Dependency\Plugin\CompanyUserFormExpanderPluginInterface[]
     */
    protected function getCompanyUserFormExpanderPlugins(): array
    {
        return [
            new CompanyBusinessUnitFormExpanderPlugin(),
            new CompanyRoleFromExpanderPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\CompanyUserGuiExtension\Dependency\Plugin\CustomerCompanyAttachFormExpanderPluginInterface[]
     */
    protected function getCustomerCompanyAttachFormExpanderPlugins(): array
    {
        return [
            new BusinessUnitFieldCustomerCompanyAttachFormExpanderPlugin(),
            new CompanyRoleFieldCustomerCompanyAttachFormExpanderPlugin(),
        ];
    }
}

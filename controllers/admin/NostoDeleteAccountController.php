<?php
/**
 * 2013-2020 Nosto Solutions Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@nosto.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Nosto Solutions Ltd <contact@nosto.com>
 * @copyright 2013-2020 Nosto Solutions Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once 'NostoBaseController.php';
/** @noinspection PhpUnused */
class NostoDeleteAccountController extends NostoBaseController
{
    /**
     * @inheritdoc
     * @noinspection PhpUnused
     */
    public function execute()
    {
        try {
            $accountName = NostoHelperConfig::getAccountName();
            NostoHelperConfig::clearCache();
            NostoHelperAccount::delete();
            NostoHelperFlash::add(
                'success',
                Context::getContext()->getTranslator()->trans(
                    sprintf(
                        'Shop %s and language %s was successfully disconnected from the Nosto account %s',
                        NostoHelperContext::getShop()->name,
                        NostoHelperContext::getLanguage()->name,
                        $accountName
                    )
                )
            );
        } catch (Exception $e) {
            /** @noinspection PhpDeprecationInspection */
            NostoHelperFlash::add(
                'error',
                $this->l('Account could not be removed. Please see logs for details.')
            );
            NostoHelperLogger::error($e, 'Deleting Nosto account failed');
        }

        return true;
    }
}

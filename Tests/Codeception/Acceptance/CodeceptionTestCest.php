<?php


namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Tests\tests\Acceptance;

use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Tests\tests\Support\AcceptanceTester;

class CodeceptionTestCest
{

    // tests
    public function widgetContainsCompaniesInRightOrder(AcceptanceTester $I)
    {
        $I->amOnUrl('https://mautic-bundletest.ddev.site/');
        $I->fillField('#username', 'admin');
        $I->fillField('#password', 'mautic');
        #$I->wait(1);
        $I->click('login');
        #$I->wait(1);
        $I->see('Dashboard', 'h3');


    }
}

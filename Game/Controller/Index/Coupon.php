<?php

namespace Perspective\Game\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\SalesRule\Model\Rule;
class Coupon extends Action
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var Rule
     */
    protected Rule $rule;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Rule $rule
     */
    public function __construct(
        Context     $context,
        JsonFactory $resultJsonFactory,
        Rule        $rule
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->rule = $rule;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute(): mixed
    {
        $result = $this->resultJsonFactory->create();
        try {
            $code = $this->generateCouponCode();
            return $result->setData(['code' => $code]);
        } catch (\Exception $e) {
            return $result->setData(['error' => $e->getMessage()]);
        }
    }

    /**
     * @return string
     */
    protected function generateCouponCode()
    {
        $couponCode = substr(md5(uniqid(mt_rand(), true)), 0, 8);

        $coupon['discount_type'] ='by_percent';
        $coupon['flag_is_free_shipping'] = 'no';
        $coupon['redemptions'] = 1;
        $this->rule->setName('custom_coupon' . $couponCode)
            ->setDescription(' 10% Off Discount coupon.')
            ->setFromDate(date('Y-m-d'))
            ->setToDate('')
            ->setUsesPerCustomer(1)
            ->setCustomerGroupIds(4)
            ->setIsActive(1)
            ->setSimpleAction('by_percent')
            ->setDiscountAmount(10)
            ->setDiscountQty(1)
            ->setApplyToShipping($coupon['flag_is_free_shipping'])
            ->setTimesUsed($coupon['redemptions'])
            ->setWebsiteIds(1)
            ->setCouponType(2)
            ->setCouponCode($couponCode)
            ->setUsesPerCoupon(1);
        $this->rule->save();

        return $couponCode;
    }
}

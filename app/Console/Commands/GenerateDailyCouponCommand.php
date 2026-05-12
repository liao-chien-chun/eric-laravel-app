<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateDailyCouponCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupon:generate-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每天產生一張已啟用的優惠券';

    public function __construct(
        private CouponService $couponService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('開始產生每日優惠券...');

        // 隨機產生優惠券資料
        $discountType = rand(1, 2); // 1:固定金額 2:百分比

        $data = [
            'code' => 'DAILY' . now()->format('Ymd') . Str::upper(Str::random(4)),
            'name' => '每日限量優惠券 ' . now()->format('Y/m/d'),
            'description' => '系統自動產生的每日限量優惠券',
            'discount_type' => $discountType,
            'discount_value' => $discountType === Coupon::DISCOUNT_TYPE_FIXED ? rand(10, 100) : rand(5, 30), // 整數
            'min_order_amount' => rand(0, 5) * 100, // 整數
            'total_quantity' => rand(10, 100), // 10-100 張
            'per_user_limit' => 1,
            'start_at' => null, // 無時間限制
            'end_at' => null,   // 無時間限制
            'status' => Coupon::STATUS_ACTIVE, // 直接啟用
        ];

        $coupon = $this->couponService->createCoupon($data);

        $this->info("優惠券產生成功！");
        $this->table(
            ['欄位', '值'],
            [
                ['ID', $coupon->id],
                ['代碼', $coupon->code],
                ['名稱', $coupon->name],
                ['折扣類型', $coupon->discount_type === 1 ? '固定金額' : '百分比'],
                ['折扣值', $coupon->discount_value],
                ['最低訂單金額', $coupon->min_order_amount],
                ['數量', $coupon->total_quantity],
                ['狀態', '已啟用'],
            ]
        );

        return Command::SUCCESS;
    }
}

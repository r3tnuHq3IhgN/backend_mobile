<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Chair;
use App\Models\TicketOrder;
use App\Models\FoodCombo;
use App\Models\Payment;

const CHAIR_PRICES = [50000, 10000, 75000];

class ApiVnPay extends Controller
{
    public function createTransaction(Request $request)
    {
        $user_id = auth('api')->user() ? auth('api')->user()->id : null;

        if (!$user_id) {
            return $this->responseMessage('Please log in', 400);
        }
        $chair_ids = $request['chair_id'];
        $food_combo_ids = $request['food_combo_id'];
        $film_detail_id = $request['film_detail_id'];
        $validated = $request->validate([
            'chair_id' => 'required',
            'food_combo_id' => 'required',
            'film_detail_id' => 'required|numeric',
        ]);
        $ticket_orders = DB::table('ticket_orders')->where('film_detail_id', $film_detail_id)->get();
        $ticket_order_ids = [];
        forEach($ticket_orders as $ticket_order) {
            $ticket_order_ids[] = $ticket_order->id;
        }
        if(DB::table('chair_orders')->whereIn('chair_id', $chair_ids)->whereIn('ticket_order_id', $ticket_order_ids)->count() > 0) {
            return $this->responseMessage('Có ghế đã được đặt, vui lòng chọn lại', 400);
        };
        $chairs = Chair::whereIn('id', $chair_ids)->get(['id', 'type']);
        $chair_total_price = 0;
        $food_combos_price = 0;
        $order = TicketOrder::create([
            'user_id' => $user_id,
            'film_detail_id' => $film_detail_id,
            'status' => false,
        ]);
        forEach($chairs as $chair){
            $chair_total_price += CHAIR_PRICES[$chair->type];
            DB::table('chair_orders')->insert(['ticket_order_id' => $order->id, 'chair_id' => $chair->id]);
        }
        $food_combos = FoodCombo::whereIn('id', $food_combo_ids) ? FoodCombo::whereIn('id', $food_combo_ids)->get(['id','price']) : 0;
        forEach($food_combos as $food_combo){
            $food_combos_price += $food_combo->price;
            DB::table('food_combo_orders')->insert(['ticket_order_id' => $order->id, 'food_combo_id' => $food_combo->id]);
        }
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://139.162.56.4:88/api/vnpay-return";
        $vnp_TmnCode = "U6IWK5LE"; // Mã website tại VNPAY
        $vnp_HashSecret = "ZRQYENHOGHZHRWUKZQMLPQHYDWMDWSVP"; //Chuỗi bí mật

        $vnp_TxnRef = $order->id; // Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = 'Thanh toan'; // Thông tin mô tả nội dung thanh toán (Tiếng Việt, không dấu). Ví dụ: **Nap tien cho thue bao 0123456789. So tien 100,000 VND**
        $vnp_OrderType = 19001; // Mã danh mục hàng hóa. Mỗi hàng hóa sẽ thuộc một nhóm danh mục do VNPAY quy định. Xem thêm bảng Danh mục hàng hóa
        $vnp_Amount = ($chair_total_price + $food_combos_price) * 100; // Số tiền thanh toán.
        $vnp_Locale = 'vn'; // Ngôn ngữ giao diện hiển thị. Hiện tại hỗ trợ Tiếng Việt (vn), Tiếng Anh (en)
        $vnp_BankCode = $request['bank_code']; // VNPAYQR, VNBANK, INTCART
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; // Địa chỉ IP của khách hàng thực hiện giao dịch. Ví dụ: 13.160.92.202
        //Add Params of 2.0.1 Version
        $vnp_ExpireDate = $request['txtexpire'];
        //Billing
        $vnp_Bill_Mobile = $request['txt_billing_mobile'];
        $vnp_Bill_Email = $request['txt_billing_email'];
        $fullName = trim($request['txt_billing_fullname']);
        if (isset($fullName) && trim($fullName) != '') {
            $name = explode(' ', $fullName);
            $vnp_Bill_FirstName = array_shift($name);
            $vnp_Bill_LastName = array_pop($name);
        }
        $vnp_Bill_Address = $request['txt_inv_addr1'];
        $vnp_Bill_City = $request['txt_bill_city'];
        $vnp_Bill_Country = $request['txt_bill_country'];
        $vnp_Bill_State = $request['txt_bill_state'];
        // Invoice
        $vnp_Inv_Phone = $request['txt_inv_mobile'];
        $vnp_Inv_Email = $request['txt_inv_email'];
        $vnp_Inv_Customer = $request['txt_inv_customer'];
        $vnp_Inv_Address = $request['txt_inv_addr1'];
        $vnp_Inv_Company = $request['txt_inv_company'];
        $vnp_Inv_Taxcode = $request['txt_inv_taxcode'];
        $vnp_Inv_Type = $request['cbo_inv_type'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00', 'message' => 'success', 'data' => $vnp_Url
        );
        if (isset($request['redirect'])) {
            header('Location: ' . $vnp_Url);
            die();
        } else {
            echo json_encode($returnData);
        }
    }

    public function return(Request $request) {
        if($request->vnp_ResponseCode == "00") {
            $ticket_order = TicketOrder::where('id', $request->vnp_TxnRef)->first();
            $ticket_order->status = true;
            $ticket_order->save();
            $payment = Payment::create([
                'amount' => $_GET['vnp_Amount']/100,
                'bank_code' => $_GET['vnp_BankCode'],
                'bank_tran_no' => $_GET['vnp_BankTranNo'],
                'card_type' => $_GET['vnp_CardType'],
                'order_info' => $_GET['vnp_OrderInfo'],
                'pay_date' => $_GET['vnp_PayDate'],
                'response_code' => $_GET['vnp_ResponseCode'],
                'transaction_no' => $_GET['vnp_TransactionNo'],
                'transaction_status' => $_GET['vnp_TransactionStatus'],
                'txn_ref' => $_GET['vnp_TxnRef'],
            ]);
            return redirect()->away('mobileproject://paymentSuccess?' . http_build_query($request));
        }
        DB::table('food_combo_orders')->where('ticket_order_id', $request->vnp_TxnRef)->delete();
        DB::table('chair_orders')->where('ticket_order_id', $request->vnp_TxnRef)->delete();
        return redirect()->away('mobileproject://paymentFail');
    }

    public function getListBill(Request $request) {
        $user_id = auth('api')->user() ? auth('api')->user()->id : null;

        if (!$user_id) {
            return $this->responseMessage('Please log in', 400);
        }

        $ticket_orders = DB::table('ticket_orders')->where('user_id', $user_id)->where('status', 1)->get(['id', 'film_detail_id']);
        forEach($ticket_orders as $order) {
            $film_detail = DB::table('film_details')->where('id', $order->film_detail_id)->first();
            $film = DB::table('films')->where('id', $film_detail->film_id)->first();
            $room = DB::table('rooms')->where('id', $film_detail->room_id)->first();
            $chair_orders = DB::table('chair_orders')->where('ticket_order_id', $order->id)->get();
            $food_combo_orders = DB::table('food_combo_orders')->where('ticket_order_id', $order->id)->get();
            $chair_names = [];
            $food_combos = [];
            $order->film_name = $film->name;
            $order->room_name = $room->name;
            $order->film_type = $film_detail->type;
            forEach($chair_orders as $chair_order) {
                $chair_names[] = DB::table('chairs')->where('id', $chair_order->chair_id)->first()->name;
            }
            forEach($food_combo_orders as $food_combo_order) {
                $combo = DB::table('food_combos')->where('id', $food_combo_order->food_combo_id)->first();
                $food_combos[] = [
                    "name" => $combo->name,
                    "image" => $combo->image,
                    "price" => $combo->price,
                ];
            }
            $order->chair_names = $chair_names;
            $order->food_combos = $food_combos;
        }

        return $this->responseData($ticket_orders, 200);
    }
}

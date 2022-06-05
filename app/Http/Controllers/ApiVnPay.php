<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Chair;
use App\Models\TicketOrder;

class ApiVnPay extends Controller
{
    public function createTransaction(Request $request)
    {
        $user_id = auth('api')->user() ? auth('api')->user()->id : null;

        if (!$user_id) {
            return $this->responseMessage('Please log in', 400);
        }
        $chair_id = $request['chair_id'];
        $food_combo_id = $request['food_combo_id'];
        $film_detail_id = $request['film_detail_id'];

        $validated = $request->validate([
            'chair_id' => 'required|numeric',
            'food_combo_id' => 'required|numeric',
            'film_detail_id' => 'required|numeric',
        ]);

        $chair = Chair::find($chair_id);

        switch ($chair->type) {
            case 0:
                $amount = 50000;
                break;
            case 1:
                $amount = 100000;
                break;
            default:
                $amount = 75000;
                break;
        }
        
        $order = TicketOrder::create([
            'user_id' => $user_id,
            'chair_id' => $chair_id,
            'food_combo_id' => $food_combo_id,
            'film_detail_id' => $film_detail_id,
            'status' => false,
        ]);

        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/payment";
        $vnp_TmnCode = "U6IWK5LE"; // Mã website tại VNPAY 
        $vnp_HashSecret = "ZRQYENHOGHZHRWUKZQMLPQHYDWMDWSVP"; //Chuỗi bí mật

        $vnp_TxnRef = $request['order_id']; // Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = 'Thanh toan'; // Thông tin mô tả nội dung thanh toán (Tiếng Việt, không dấu). Ví dụ: **Nap tien cho thue bao 0123456789. So tien 100,000 VND**
        $vnp_OrderType = 19001; // Mã danh mục hàng hóa. Mỗi hàng hóa sẽ thuộc một nhóm danh mục do VNPAY quy định. Xem thêm bảng Danh mục hàng hóa
        $vnp_Amount = $amount * 100; // Số tiền thanh toán.
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
    // public function getListBill(){
    //     $data = DB::table('payments')->join('orders', 'orders.order_id', '=', 'payments.txn_ref')
    //     ->where('orders.user_id', '=', Auth::user()->id)->get();
    //     return $this->responseData($data, 200);
    // }
    public function return(Request $request) {
        $url = session('url_prev','/');
        if($request->vnp_ResponseCode == "00") {
            $this->apSer->thanhtoanonline(session('cost_id'));
            $order = Order::find($request->order_id);
            $order->update(['status' => true]);
            return redirect($url)->with('success' ,'Đã thanh toán phí dịch vụ');
        }
        session()->forget('url_prev');
        return redirect($url)->with('errors' ,'Lỗi trong quá trình thanh toán phí dịch vụ');
    }
}

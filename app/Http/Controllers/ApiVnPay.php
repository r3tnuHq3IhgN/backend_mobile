<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class ApiVnPay extends Controller
{
    //
    public function createTransaction(Request $request)
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/payment";
        $vnp_TmnCode = "U6IWK5LE"; // Mã website tại VNPAY 
        $vnp_HashSecret = "ZRQYENHOGHZHRWUKZQMLPQHYDWMDWSVP"; //Chuỗi bí mật

        $vnp_TxnRef = $request['order_id']; // Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = $request['order_desc']; // Thông tin mô tả nội dung thanh toán (Tiếng Việt, không dấu). Ví dụ: **Nap tien cho thue bao 0123456789. So tien 100,000 VND**
        $vnp_OrderType = $request['order_type']; // Mã danh mục hàng hóa. Mỗi hàng hóa sẽ thuộc một nhóm danh mục do VNPAY quy định. Xem thêm bảng Danh mục hàng hóa
        $vnp_Amount = $request['amount'] * 100; // Số tiền thanh toán.
        $vnp_Locale = $request['language']; // Ngôn ngữ giao diện hiển thị. Hiện tại hỗ trợ Tiếng Việt (vn), Tiếng Anh (en)
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
            //http://shop-hunter.herokuapp.com/?vnp_Amount=5000000&vnp_BankCode=NCB&vnp_BankTranNo=VNP13747649&vnp_CardType=ATM&vnp_OrderInfo=Thanh+toan&vnp_PayDate=20220516162654&vnp_ResponseCode=00&vnp_TmnCode=U6IWK5LE&vnp_TransactionNo=13747649&vnp_TransactionStatus=00&vnp_TxnRef=06&vnp_SecureHash=b6a86ee3dd736d25695e857763a56860497da8c5b2b25c2ffdf716f67814d3b8d9cc8d581483fe98e715b0f8faf3fe9fa64983c351a557b18810b51a705763d4
        }
    }

    
    public function getData(Request $request)
    {


        /* Payment Notify
     * IPN URL: Ghi nhận kết quả thanh toán từ VNPAY
     * Các bước thực hiện:
     * Kiểm tra checksum 
     * Tìm giao dịch trong database
     * Kiểm tra số tiền giữa hai hệ thống
     * Kiểm tra tình trạng của giao dịch trước khi cập nhật
     * Cập nhật kết quả vào Database
     * Trả kết quả ghi nhận lại cho VNPAY
     */

    date_default_timezone_set('Asia/Ho_Chi_Minh');

    $vnp_TmnCode = "U6IWK5LE"; //Website ID in VNPAY System
    $vnp_HashSecret = "ZRQYENHOGHZHRWUKZQMLPQHYDWMDWSVP"; //Secret key
    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl = "http://127.0.0.1:8000/payment";
    $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
    //Config input format
    //Expire
    $startTime = date("YmdHis");
    $expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));


        $inputData = array();
        $returnData = array();

        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
        $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
        $vnp_Amount = $inputData['vnp_Amount'] / 100; // Số tiền thanh toán VNPAY phản hồi

        $Status = 0; // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo URL thanh toán.
        $orderId = $inputData['vnp_TxnRef'];

        try {
            //Check Orderid    
            //Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash) {
                //Lấy thông tin đơn hàng lưu trong Database và kiểm tra trạng thái của đơn hàng, mã đơn hàng là: $orderId            
                //Việc kiểm tra trạng thái của đơn hàng giúp hệ thống không xử lý trùng lặp, xử lý nhiều lần một giao dịch
                //Giả sử: $order = mysqli_fetch_assoc($result);   

                $order = NULL;
                if ($order != NULL) {
                    if ($order["Amount"] == $vnp_Amount) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền kiểm tra là đúng. //$order["Amount"] == $vnp_Amount
                    {
                        if ($order["Status"] != NULL && $order["Status"] == 0) {
                            if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
                                $Status = 1; // Trạng thái thanh toán thành công
                            } else {
                                $Status = 2; // Trạng thái thanh toán thất bại / lỗi
                            }
                            //Cài đặt Code cập nhật kết quả thanh toán, tình trạng đơn hàng vào DB
                            //
                            //
                            //
                            //Trả kết quả về cho VNPAY: Website/APP TMĐT ghi nhận yêu cầu thành công                
                            $returnData['RspCode'] = '00';
                            $returnData['Message'] = 'Confirm Success';
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                        }
                    } else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }
        //Trả lại VNPAY theo định dạng JSON
        echo json_encode($returnData);
    }
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>Giao dịch của bạn đang được xử lý !</h2>
    @php
        // var_dump($_GET['vnp_Amount']/100);
        // var_dump($_GET['vnp_BankCode']);
        // var_dump($_GET['vnp_BankTranNo']);
        // var_dump($_GET['vnp_CardType']);
        // var_dump($_GET['vnp_OrderInfo']);
        // var_dump($_GET['vnp_PayDate']);
        // var_dump($_GET['vnp_ResponseCode']);
        // var_dump($_GET['vnp_TransactionNo']);
        // var_dump($_GET['vnp_TransactionStatus']);
        // var_dump($_GET['vnp_TxnRef']);
        $data = DB::table('payments')->where('txn_ref', $_GET['vnp_TxnRef'])->first();
        if($data == null ){
            DB::table('payments')->insert([
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
        }
        //vnp_Amount=5000000
        //vnp_BankCode=NCB&
        //vnp_BankTranNo=VNP13747798&
        //vnp_CardType=ATM&
        //vnp_OrderInfo=Thanh+toan&
        //vnp_PayDate=20220516181916&
        //vnp_ResponseCode=00&
        //vnp_TmnCode=U6IWK5LE&
        //vnp_TransactionNo=13747798&
        //vnp_TransactionStatus=00&
        //vnp_TxnRef=08&
        //vnp_SecureHash=ec6ceb64125d0a1f670433bd8d93a7f1a2da3b2d862f882207bbfbbc56cd6a7b934dab46e8d6ec6773c59ff791edd5d359196860f700c7f5b100cda39c10476f
    @endphp
</body>
</html>
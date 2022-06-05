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
    @endphp
</body>
</html>
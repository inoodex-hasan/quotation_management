<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status Updated</title>
</head>
<body>
    <div class="">
        <div id=":n4" class="ii gt" jslog="20277; u014N:xr6bB; 1:WyIjdGhyZWFkLWY6MTgxNTEzNDEyNDM5ODQzNTc2NyJd; 4:WyIjbXNnLWY6MTgxNTEzNDEyNDM5ODQzNTc2NyIsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLG51bGwsbnVsbCxudWxsLDBd">
            <div id=":n3" class="a3s aiL msg-6277900744185192596">
                <div style="margin:0;padding:0;background-color:#ffffff" bgcolor="#FFFFFF">
                    <div style="background-color:#ffffff" bgcolor="#FFFFFF">
                    <div style="margin:0px auto;max-width:600px">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;width:100%" width="100%">
                        <tbody>
                            <tr>
                            <td style="border-collapse:collapse;direction:ltr;font-size:0px;padding:20px 0;text-align:center" align="center">
                                <div class="m_-6277900744185192596mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%" align="left" width="100%">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="border-collapse:collapse;vertical-align:top">
                                    <tbody>
                                    <tr>
                                        <td align="left" style="border-collapse:collapse;font-size:0px;padding:10px 25px;word-break:break-word">
                                        <table border="0" cellpadding="0" cellspacing="0px" role="presentation" style="border-collapse:collapse;border-spacing:0px">
                                            <tbody>
                                            <tr>
                                                <td style="border-collapse:collapse;width:135px" width="135">
                                                    <img height="auto" src="{{asset('frontend/images/bedouin logo.png')}}" width="100%" style="line-height:100%;border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px" border="0" class="CToWUd" data-bit="iit">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                </div>
                            </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    <div style="margin:0px auto;max-width:600px">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;width:100%" width="100%">
                        <tbody>
                            <tr>
                            <td style="border-collapse:collapse;direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0px;padding-top:0px;text-align:center" align="center">
                                <div class="m_-6277900744185192596mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%" align="left" width="100%">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="border-collapse:collapse;vertical-align:top">
                                    <tbody>
                                    <tr>
                                        <td align="left" style="border-collapse:collapse;font-size:0px;padding:10px 25px;word-break:break-word">
                                        <div style="font-family:Ubuntu,Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;text-align:left;color:#000000" align="left"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="border-collapse:collapse;font-size:0px;padding:10px 25px;word-break:break-word">
                                        <div style="font-family:Ubuntu,Helvetica,Arial,sans-serif;font-size:14px;font-weight:bold;line-height:22px;text-align:left;color:#f23534" align="left"> অর্ডার #{{$order->order_number}}, {{ $order->created_at->format('F j, Y') }} </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="border-collapse:collapse;font-size:0px;padding:10px 25px;word-break:break-word">
                                            <div style="font-family:Ubuntu,Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;text-align:left;color:#000000" align="left">
                                                <p style="display:block;margin:13px 0">আপনার পেমেন্ট সম্পন্ন হয়েছে।</p>
                                           </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="border-collapse:collapse;font-size:0px;padding:10px 25px;word-break:break-word">
                                        <table width="100%" border="0" style="border-collapse:collapse;color:#737373;font-family:Ubuntu,Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;table-layout:auto;width:100%;border:none">
                                            <tbody>
                                            <tr style="border-bottom:1px solid #ecedee;text-align:left;padding:15px 0">
                                                <th style="padding:0 0px 10px 0;font-size:14px">পণ্য</th>
                                                <th style="padding:0 5px 10px 0;width:70px;text-align:right;font-size:14px" width="70" align="right">পরিমান</th>
                                                <th style="padding:0 0px 10px 0;width:130px;text-align:right;font-size:14px" width="130" align="right">মূল্য</th>
                                            </tr>
                                            @foreach($items as $item)
                                                <tr style="border-bottom:solid 1px #ebebeb">
                                                    <td style="border-collapse:collapse;padding:10px 0px 10px 0;font-size:14px"> {{$item->product_name}} </td>
                                                    <td style="border-collapse:collapse;padding:10px 5px 10px 0;text-align:right;font-size:14px" align="right"> {{$item->quantity}} </td>
                                                    <td style="border-collapse:collapse;padding:10px 0px 10px 0;text-align:right;font-size:14px" align="right">
                                                        <span>
                                                        {{$item->sale_price}}&nbsp; <span>৳</span>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr style="background-color:#fafafa" bgcolor="#fafafa">
                                                <th colspan="2" style="padding:10px 15px 10px 0;text-align:right;border-top-width:0px;color:#858585;vertical-align:middle;font-size:14px;padding-top:8px;padding-bottom:8px" align="right">মোট মূল্য</th>
                                                <td style="border-collapse:collapse;padding:0 0 0 15px;text-align:left;border-top-width:0px;color:#858585;vertical-align:middle;font-size:14px;padding-top:8px;padding-bottom:8px" align="left">
                                                <span>{{$order->total_amount}}&nbsp; <span>৳</span>
                                                </span>
                                                </td>
                                            </tr>
                                            <tr style="background-color:#fafafa" bgcolor="#fafafa">
                                                <th colspan="2" style="padding:10px 15px 10px 0;text-align:right;border-top-width:0px;color:#858585;vertical-align:middle;font-size:14px;padding-top:8px;padding-bottom:8px" align="right">শিপিং</th>
                                                <td style="border-collapse:collapse;padding:0 0 0 15px;text-align:left;border-top-width:0px;color:#858585;vertical-align:middle;font-size:14px;padding-top:8px;padding-bottom:8px" align="left">
                                                <span>{{$order->delivery_charge}}&nbsp; <span>৳</span>
                                                </span>&nbsp; <small>via {{ getArrayData(lib_deliveryCharge(), $order->delivery_method)}}</small>
                                                </td>
                                            </tr>
                                            <tr style="background-color:#fafafa" bgcolor="#fafafa">
                                                <th colspan="2" style="padding:10px 15px 10px 0;text-align:right;border-top-width:0px;color:#858585;vertical-align:middle;font-size:14px;padding-top:8px;padding-bottom:8px" align="right">পেমেন্ট মেথড</th>
                                                <td style="border-collapse:collapse;padding:0 0 0 15px;text-align:left;border-top-width:0px;color:#858585;vertical-align:middle;font-size:14px;padding-top:8px;padding-bottom:8px" align="left">{{isset(getPaymentMethods()[$order->payment_method]['name']) ? getPaymentMethods()[$order->payment_method]['name'] : ''}}</td>
                                            </tr>
                                            <tr style="background-color:#fafafa" bgcolor="#fafafa">
                                                <th colspan="2" style="padding:10px 15px 10px 0;text-align:right;border-top-width:0px;color:#858585;vertical-align:middle;font-size:14px;padding-top:8px;padding-bottom:8px" align="right">সর্বমোট</th>
                                                <td style="border-collapse:collapse;padding:0 0 0 15px;text-align:left;border-top-width:0px;color:#858585;vertical-align:middle;font-size:14px;padding-top:8px;padding-bottom:8px" align="left">
                                                <span>{{$order->paid_amount}}&nbsp; <span>৳</span>
                                                </span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                </div>
                            </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    <div style="margin:0px auto;max-width:600px">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;width:100%" width="100%">
                        <tbody>
                            <tr>
                            <td style="border-collapse:collapse;direction:ltr;font-size:0px;padding:20px 0;text-align:center" align="center">
                                <div class="m_-6277900744185192596mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%" align="left" width="100%">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="border-collapse:collapse;vertical-align:top">
                                    <tbody>
                                    <tr>
                                        <td align="left" style="border-collapse:collapse;font-size:0px;padding:10px 25px;word-break:break-word">
                                        <table cellpadding="0" cellspacing="0" width="100%" border="0" style="border-collapse:collapse;color:#000000;font-family:Ubuntu,Helvetica,Arial,sans-serif;font-size:13px;line-height:22px;table-layout:auto;width:100%;border:none">
                                            <tbody>
                                            <tr>
                                                <td style="border-collapse:collapse;padding-right:15px;color:#858585">
                                                <p style="display:block;margin:13px 0;font-size:15px;font-weight:bold">বিলিং ঠিকানা</p>
                                                <p style="display:block;margin:13px 0;font-size:14px"> {{$billing->name}} <br>{{$billing->address_details}} <br>{{ getArrayData($lib_areas, $billing->area)}} , {{getArrayData($lib_districts, $billing->state)}}<br>
                                                    <a href="tel:01915797999" target="_blank">{{$billing->phone}}</a>
                                                    <br>
                                                    <a href="mailto:{{$billing->email}}" target="_blank">{{$billing->email}}</a>
                                                </p>
                                                </td>
                                                <td valign="top" style="border-collapse:collapse;padding-left:15px;color:#858585">
                                                <p style="display:block;margin:13px 0;font-size:15px;font-weight:bold">শিপিং ঠিকানা</p>
                                                <p style="display:block;margin:13px 0;font-size:14px"> {{$shipping->name}} <br>{{$shipping->address_details}} <br>{{ getArrayData($lib_areas, $shipping->area)}} , {{getArrayData($lib_districts, $shipping->state)}} 
                                                    <a href="tel:{{$shipping->phone}}" target="_blank">{{$shipping->phone}}</a>
                                                    <br>
                                                    <a href="mailto:{{$shipping->email}}" target="_blank">{{$shipping->email}}</a>
                                                </p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                </div>
                            </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    <div style="margin:0px auto;max-width:600px">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;width:100%" width="100%">
                        <tbody>
                            <tr>
                            <td style="border-collapse:collapse;direction:ltr;font-size:0px;padding:20px 0;text-align:center" align="center">
                                <div class="m_-6277900744185192596mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%" align="left" width="100%">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="border-collapse:collapse;vertical-align:top">
                                    <tbody>
                                    <tr>
                                        <td align="left" style="border-collapse:collapse;font-size:0px;padding:10px 25px;padding-bottom:0;word-break:break-word">
                                        <div style="font-family:Ubuntu,Helvetica,Arial,sans-serif;font-size:14px;line-height:22px;text-align:left;color:#000000" align="left">বেদুইন টিম</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="border-collapse:collapse;font-size:0px;padding:10px 25px;padding-top:0px;padding-left:0;word-break:break-word">
                                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;line-height:100%">
                                            <tbody>
                                            <tr>
                                                <td align="center" bgcolor="#ffffff" role="presentation" valign="middle" style="border-collapse:collapse;border:none;border-radius:3px;background:#ffffff">
                                                <a href="{{route('index')}}" target="_blank" >{{route('index')}}</a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                </div>
                            </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    <div style="margin:0px auto;max-width:600px">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;width:100%" width="100%">
                        <tbody>
                            <tr>
                            <td style="border-collapse:collapse;direction:ltr;font-size:0px;padding:20px 0;text-align:center" align="center">
                                <div class="m_-6277900744185192596mj-column-per-100" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%" align="left" width="100%">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="border-collapse:collapse;vertical-align:top">
                                    <tbody>
                                    <tr>
                                        <td align="center" style="border-collapse:collapse;font-size:0px;padding:10px 25px;padding-top:0px;padding-left:0;word-break:break-word">
                                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;line-height:100%">
                                            <tbody>
                                            <tr>
                                                <td align="center" bgcolor="#ffffff" role="presentation" valign="middle" style="border-collapse:collapse;border:none;border-radius:3px;background:#ffffff">
                                                <a style="display:inline-block;background:#ffffff;color:#cccccc;font-family:Ubuntu,Helvetica,Arial,sans-serif;font-size:12px;font-weight:normal;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;border-radius:3px" bgcolor="#ffffff"> Unsubscribe </a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                </div>
                            </td>
                            </tr>
                        </tbody>
                        </table>
                        <div class="yj6qo"></div>
                        <div class="adL"></div>
                    </div>
                    <div class="adL"></div>
                    </div>
                    <div class="adL"></div>
                </div>
                <div class="adL"></div>
            </div>
        </div>
        <div class="WhmR8e" data-hash="0"></div>
    </div>
</body>
</html>
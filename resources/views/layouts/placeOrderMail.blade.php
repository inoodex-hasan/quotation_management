<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Quick Phone Fix N More</title>
   
  </head>
  <body style="color: black !important;">

    <div style="page-break-after: always;">
      <div class="fk-print t-pt-30 t-pb-30">
        <div style="display:flex; justify-content:center;">
          <div id="preview" style="padding: 2px;" content="width=device-width, initial-scale=1.0">
            <div class="row">
              <div class="col-12 text-center">
                <h4 style=" text-align:center; margin:0;">Quick Phone Fix N More</h4>
                <p style="text-align:center; margin:0;" >7157 Ogontaz Ave, Philadelphia PA 19138</p>
                <p style="text-align:center; margin:0;">Hotline: +445 309 7609</p>
                <div class="col-12 mt-1 mb-1" style="border-bottom: 1px dashed"></div>
                <div class="col-12 mt-1 mb-1" style="border-bottom: 1px dashed"></div>
                
                <table class="table mb-0 text table-borderless">
                  <thead>
                    <tr style="border-bottom: 1px dashed ">
                      <th scope="col" class="fk-print-text fk-print-text--bold xxsm-text text-capitalize" style="font-size: 14px;text-align:left;">Customer Info:</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr style="">
                      <td class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;">
                        <span class="d-block">
                          <span class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;">Name: {{$service->name}} </span>
                        </span>
                      </td>
                    </tr>
                    <tr style=" ">
                      <td class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;">
                        <span class="d-block">
                          <span class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;">Phone: {{$service->phone}} </span>
                        </span>
                      </td>
                    </tr>
                    <tr style="">
                      <td class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;">
                        <span class="d-block">
                          <span class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;">Email: {{$service->email}} </span>
                        </span>
                      </td>
                    </tr>
                    <tr style="">
                      <td class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;">
                        <span class="d-block">
                          <span class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;">Address: {{$service->address}} </span>
                        </span>
                      </td>
                    </tr>
                   
                  </tbody>
                </table>
                <table style="width:100%; border-collapse: collapse; ">
                  <tbody>
                    <tr style="border-bottom: 1px dashed ">
                      <th scope="col" class="fk-print-text fk-print-text--bold xxsm-text text-capitalize" style="font-size: 14px; text-align:left;" colspan="4">Service Info:</th>
                    </tr>
                    @if($service->details != "")
                      <tr>
                        <td class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;" colspan="4">
                            Details: <br>
                            {{$service->details}}
                        </td>
                      </tr>
                    @endif
                    <tr>
                        <td class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;" colspan="4">
                            Repaired By: {{getArrayData($serviceMans,$service->repaired_by)}}
                        </td>
                      </tr>
                    <tr>
                      <th class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">Product Name</th>
                      <th class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">EMEI Number</th>
                      <th class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">Service Warranty</th>
                      <th class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">Price</th>
                      <th class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">Paid</th>
                      <th class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">Due</th>
                    </tr>
                    <tr>
                      <td  class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">{{$service->product_name}}</td>
                      <td  class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">{{$service->product_number}}</td>
                      <td  class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">{{$service->warranty_duration}} Days</td>
                      <td  class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">${{$service->bill}}</td>
                      <td  class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">${{$service->paid_amount}}</td>
                      <td  class="fk-print-text xxsm-text text-capitalize" style="font-size: 14px;border: 1px solid #000;">${{$service->due_amount}}</td>
                    </tr>
                  </tbody>
                </table>
                <!-- <hr> -->
                <!-- <hr> -->
                <!-- <p class="mb-0 xsm-text fk-print-text text-capitalize text-center">bill prepared by: Ariya Stark</p> -->
                <!-- <span>---------------------------------------------------------------------------------------------------</span> -->
                <div class="col-12 mt-2 mb-1" style="border-bottom: 1px dashed"></div>
                <span scope="col" class="fk-print-text fk-print-text--bold xsm-text text-capitalize" style="font-size: 14px;" id="datePlaceholder">Date: {{date("m-d-Y g:i A")}}</span>
                <br>
                
                <table class="table mt-1 mb-1 text-center table-borderless">
                  <thead>
                    <tr></tr>
                  </thead>
                </table>
                <div class="col-12 mt-0" style="border-bottom: 1px dashed"></div>
                <p class="mb-0 sm-text fk-print-text--bold text-center text-capitalize" style="font-size: 12px;">Thank You. Please come again</p>
                <p class="mb-0 sm-text fk-print-text--bold text-center text-capitalize" style="font-size: 12px;">Note: Warranty For {{$service->warranty_duration}} Days. Warranty Does Not Cover Broken Or Water Damage. No Refund, Exchange Only.</p>
                <div class="col-12 mt-0" style="border-bottom: 1px dashed"></div>
                <div class="col-12 mt-0" style="border-bottom: 1px dashed"></div>
                <div class="no-cut">&nbsp;</div>
                <div class="no-cut">&nbsp;</div>
                <div class="no-cut">&nbsp;</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </body>
</html>
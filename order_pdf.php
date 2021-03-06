<?php
include('vendor/autoload.php');
require('connection.inc.php');
require('function.inc.php');

//If Admin not login then check user login or not login.If user not login then die this page & not generate pdf.
if(!isset($_SESSION['ADMIN_LOGIN'])){
    if(!isset($_SESSION['USER_ID'])){
        die();
    }
}

## If Admin or User login then execute all this code & generate pdf file ##

//hold order id
$order_id = get_safe_value($con,$_GET['id']);

//fetch coupon details from database
$coupon_details=mysqli_fetch_assoc(mysqli_query($con,"SELECT coupon_value FROM order_tbl WHERE id='$order_id'"));
//hold coupon value
$coupon_value=$coupon_details['coupon_value'];


if(isset($_SESSION['ADMIN_LOGIN'])){
    //if Admin login then execute this query
    $order_res=mysqli_fetch_assoc(mysqli_query($con,"SELECT address,city,post_code,payment_status,payment_method,added_on FROM order_tbl WHERE id='$order_id'"));
}else{
    $uid = $_SESSION['USER_ID'];
    //if user login then execute this query
    $order_res=mysqli_fetch_assoc(mysqli_query($con,"SELECT address,city,post_code,payment_status,payment_method,added_on FROM order_tbl WHERE user_id='$uid' AND id='$order_id'"));
}


$css=file_get_contents('css/bootstrap.min.css');
$css.=file_get_contents('style.css');


$html='
<!doctype html>
<html class="no-js" lang="en">

<head>
</head>

<body>
    <div class="wishlist-table table-responsive">
        <table>
            <caption style="caption-side: left; margin-bottom:20px;">
            <span style="color:green; font-size:18px; font-weight:bold;">E-Shop Online Ltd.</span><br/>
            <span style="color:blue; font-size:14px; font-weight:normal;"><strong>Address:</strong> Lalbagh Rd, Dhaka 1211</span><br/>
            <span style="color:blue; font-size:14px; font-weight:normal;"><strong>Phone:</strong> 01883-791806</span><br/><br/>
            <hr/>
            <span style="color:black; font-size:18px; font-weight:normal;"><strong>ORDER DETAILS:</strong></span><br/>
            <span style="color:black; font-size:14px; font-weight:normal;"><strong>Order Address: </strong>'.$order_res['address'].', '.$order_res['city'].'-'.$order_res['post_code'].'</span><br/>
            <span style="color:black; font-size:14px; font-weight:normal;"><strong>Order Date: </strong>'.$order_res['added_on'].'</span><br/>
            <span style="color:black; font-size:14px; font-weight:normal;"><strong>Payement Method: </strong>'.$order_res['payment_method'].'</span><br/>
            <span style="color:black; font-size:14px; font-weight:normal;"><strong>Payement Status: </strong>'.$order_res['payment_status'].'</span>
            </caption>
            <thead>
                <tr>
                    <th class="product-name">Product Name</th>
                    <th class="product-thumbnail"><span class="nobr">Product Image</span></th>
                    <th class="product-name"><span class="nobr"> Qty </span></th>
                    <th class="product-price"><span class="nobr">Price</span></th>
                    <th class="product-price"><span class="nobr">Total Price</span></th>
                </tr>
            </thead>
            <tbody>';

            if(isset($_SESSION['ADMIN_LOGIN'])){
               //if Admin login then execute this query
               $res = mysqli_query($con, "SELECT distinct(order_details.id),order_details.*,product.name,product.image FROM order_details,product,order_tbl WHERE order_details.order_id='$order_id' AND order_details.product_id=product.id");  
            }else{
                $uid = $_SESSION['USER_ID'];
               //if user login then execute this query
               $res = mysqli_query($con, "SELECT distinct(order_details.id),order_details.*,product.name,product.image FROM order_details,product,order_tbl WHERE order_details.order_id='$order_id' AND order_tbl.user_id='$uid' AND order_details.product_id=product.id"); 
            }
            
            $total_price=0;

            if(mysqli_num_rows($res)==0){
                die();
            }
            while($row=mysqli_fetch_assoc($res)){
                $total_price=$total_price+($row['qty']*$row['price']);

            $html.='<tr>
                    <td class="product-name">'.$row['name'].'</td>
                    <td class="product-thumbnail"><img src="'.PRODUCT_IMAGE_SITE_PATH.$row['image'].'"></td>
                    <td class="product-name">'.$row['qty'].'</td>
                    <td class="product-price">Tk.'.$row['price'].'</td>
                    <td class="product-price">Tk.'.$row['qty']*$row['price'].'</td>
                </tr>';
            }
           
           $delivery=25;
           $grand_price=$total_price+$delivery;
           $final_price=$grand_price-$coupon_value;
           $html.='<tr>
                     <td colspan="3"></td>
                     <td class="product-price">Total </td>
                     <td class="product-price">Tk.'.$total_price.'</td>
                   </tr>
                   <tr>
                     <td colspan="3"></td>
                     <td class="product-price">Delivery Charge </td>
                     <td class="product-price">Tk.'.$delivery.'</td>
                   </tr>
                   <tr>
                     <td colspan="3"></td>
                     <td class="product-price">Grand Total </td>
                     <td class="product-price">Tk.'.$grand_price.'</td>
                  </tr>';
            
           if($coupon_value!=''){
               $html.='<tr>
                        <td colspan="3"></td>
                        <td class="product-price">Coupon Discount Value </td>
                        <td class="product-price">Tk.'.$coupon_value.'</td>
                      </tr>  
                      <tr>
                        <td colspan="3"></td>
                        <td class="product-price">Final Grand Total </td>
                        <td class="product-price">Tk.'.$final_price.'</td>
                      </tr>';
                }

           $html.='</tbody>
                    </table>
                </div>
            </body>

            </html>
            ';

$mpdf=new \Mpdf\Mpdf();
$mpdf->WriteHTML($css,1);
$mpdf->WriteHTML($html,2);
$file=time().'.pdf';
$mpdf->Output($file,'D');

?>

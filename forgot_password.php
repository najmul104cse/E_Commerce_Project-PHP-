<?php
    //include header page
    require('top.inc.php');

    if(isset($_SESSION['USER_LOGIN']) && $_SESSION['USER_LOGIN']=='yes'){
?>
        <script>
        window.location.href='my_order.php';
        </script>
<?php
    }
?>

    <!-- Start Bradcaump area -->
        <div class="ht__bradcaump__area" style="background: rgba(0, 0, 0, 0) url(images/bg/3.jpg) no-repeat scroll center center / cover ;">
            <div class="ht__bradcaump__wrap">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="bradcaump__inner">
                                <nav class="bradcaump-inner">
                                  <a class="breadcrumb-item" href="index.php">Home</a>
                                  <span class="brd-separetor"><i class="zmdi zmdi-chevron-right"></i></span>
                                  <span class="breadcrumb-item active">Forgot Password</span>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Bradcaump area -->
        <!-- Start Contact Area -->
        <section class="htc__contact__area ptb--100 bg__white">
            <div class="container">
                <div class="row">
					<div class="col-md-6">
						<div class="contact-form-wrap mt--60">
							<div class="col-xs-12">
								<div class="contact-title">
									<h2 class="title__line--6">Forgot Password</h2>
								</div>
							</div>
							<div class="col-xs-12">
								<form id="login-form" method="post">
									<div class="single-contact-form">
										<div class="contact-box name">
											<input type="text" name="email" id="email" placeholder="Your Email*" style="width:100%">
										</div>
										<span class="field_error" id="email_error"></span>
										<span class="field_correct" id="email_msg"></span>
									</div>
									
									<div class="contact-btn">
										<button type="button" class="fv-btn" onclick="forgot_password()" id="btn_submit">Submit</button>
									</div>
								</form>
								<div class="form-output login_msg">
									<p class="form-messege field_error"></p>
								</div>
							</div>
						</div> 
                
				    </div>
                </div>
                  
            </div>
            
        </section>
        <!-- End Contact Area -->
        
        <input type="hidden" id="is_email_verified">
        
        <script>
            
            function forgot_password(){
                $('#email_msg').html('');
                $('#email_error').html('');
                var email = $('#email').val();
                if(email==''){
                    $('#email_error').html('Please enter your email !');
                }else{
                    $('#btn_submit').html('Please Wait...');
                    $('#btn_submit').attr('disabled',true);
                    $.ajax({
                        url:'forgot_password_submit.php',
                        type:'post',
                        data:'email='+email,
                        success:function(result){
                            $('#email').val('');
                            $('#btn_submit').html('Submit');
                            $('#btn_submit').attr('disabled',false);
                            if($.trim(result)=='yes'){
                                $('#email_msg').html('Please check your email id for password !');
                            }
                            if($.trim(result)=='not_present'){
                                $('#email_error').html('This Email id is not registered !');
                            }
                        }
                    });
                }
            }
            
           
        </script>
        
        
	     <!-- Main js file that contents all jQuery plugins activation. 
         <script type="text/javascript" src="js/main.js"></script> -->
	
<?php
   //include footer page
   require('foot.inc.php');
?>                
        
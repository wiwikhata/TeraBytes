// ---------------------------------------------------------------------------------
// jquery.js
// jquery functions
// ---------------------------------------------------------------------------------
$(document).ready(function()
{
  // Animate header
  $("#animate p").delay(1000).animate({"opacity": "1"}, 700);
  
  // Reset shipping checkbox to unchecked when viewing cart
  $("#checkout").click(function()
  {
    if($('#chkShipping').prop('checked', true))
    {
        localStorage.input = 'false';
    }
  });
  			
  // Keep the checkbox checked on refresh (if checked)
  $(function()
  {
      var test = localStorage.input === 'true'? true: false;
      $('#chkShipping').prop('checked', test);      
  });

  $('#chkShipping').on('change', function() 
  {
      localStorage.input = $(this).is(':checked');
      console.log($(this).is(':checked'));
  });

  // Add (or don't add) extra shipping charges
  $('#chkShipping').change(function()
  {    
      if($(this).prop("checked") == true)
      {
          document.getElementById("update_total").click();
          $("#chkShipping").prop("checked", true);

      }
      if($(this).prop("checked") == false)
      {
          document.getElementById("update_total").click();
          $("#chkShipping").prop("checked", false);
      }         
  });

  // Delete product from cart upon checkbox being checked
  $('.checks').change(function()
  {
    if($(this).prop("checked") == true)
    {
      document.getElementById("update_cart").click();
      $(".checks").prop("checked", true);
    }
    if($(this).prop("checked") == false)
    {
        document.getElementById("update_cart").click();
        $(".checks" ).prop("checked", false);
    }
  });

  // Update cart upon quantity change
  $('.cart-quantity').on('keyup change', function()
  {
    if($(this).val() != "")
    {
      document.getElementById("update_cart").click();
    } 
  });

  // Alternative shipping addresses
  $('#alt').click(function()
  {
    if($(this).prop("checked") == true)
    {
      $('#chkbox span').html('*Uncheck to use primary address');
      $('#alt-shipping').show();       
    }
    else
    {
      $('#chkbox span').html('*Check to send to alternate address'); 
      $('#alt-shipping').hide(); 
    }    
  });

  // Payment button click
  $('#payment button').click(function()
  {
    if($('#alt').prop('checked'))
    {
      $('#altchk').prop('checked', true);
      $("#submit3alt").click();
    }
    else
    {
      $("#submit3").click();  
    }               
  });
});  